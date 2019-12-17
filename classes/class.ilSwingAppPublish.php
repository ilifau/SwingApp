<?php
// Copyright (c) 2019 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

/**
 * Base class for data (config and settings)
 *
 * @author Fred Neumann <fred.neumann@ili.fau.de>
 *
 */
class ilSwingAppPublish
{
	/**
	 * @var ilSwingAppParam[]	$params		name => ilSwingAppParam
	 */
    /** @var ilObjDataCollection $object */
    protected $object;

    /**
     * @var ilSwingAppPlugin
     */
	protected $plugin;

    /**
     * @var
     */
	protected $directory;


	/**
	 * Constructor.
	 * @param ilObjDataCollection $object
	 */
	public function __construct($object)
	{
		$this->plugin = ilPlugin::getPluginObject(IL_COMP_SERVICE, 'UIComponent', 'uihk', 'SwingApp');
		$this->object = $object;

		$this->directory = ilUtil::getDataDir()."/dcl_data"."/dcl_".$this->object->getId()."/content";
	}

    /**
     * Build the content
     * @throws ilDateTimeException
     */
	public function buildContent()
    {
        ilUtil::makeDirParents($this->directory);
        ilUtil::delDir($this->directory, true);

        ilUtil::makeDirParents($this->directory. '/data');
        $texts = $this->getGeneralTexts();
        file_put_contents($this->directory.'/data/texts.json', $texts);

        $this->packContent();
    }


    protected function getGeneralTexts()
    {
        $tableId = ilDclTable::_getTableIdByTitle('GeneralTexts', $this->object->getId());
        $table = $this->object->getTableById($tableId);

        $keyField = $table->getFieldByTitle('Identifier');
        $valueField = $table->getFieldByTitle('DisplayText');

        $list = $table->getPartialRecords('Identifier', "asc", null, 0, []);

        $texts = [];
        /** @var ilDclBaseRecordModel $record */
        foreach ($list['records'] as $record) {
            $key = $record->getRecordField($keyField->getId())->getExportValue();
            $value = $record->getRecordField($valueField->getId())->getExportValue();
            $texts[$key] = $value;
        }

        return json_encode($texts, JSON_PRETTY_PRINT);
    }

    /**
     * Create a zip file with the content ald list it on the export page
     * @throws ilDateTimeException
     */
    protected function packContent()
    {
        $ts = time();
        $v = explode(".", ILIAS_VERSION_NUMERIC);
        $version = $v[0].".".$v[1].".0";

        ilExport::_createExportDirectory($this->object->getId(), "xml", $this->object->getType());
        $export_dir = ilExport::_getExportDirectory($this->object->getId(), "xml", $this->object->getType());

        $new_file = $ts.'__'.IL_INST_ID.'__app_'.$this->object->getId().'.zip';

        ilUtil::zip($this->directory, $export_dir."/".$new_file);

        $exp = new ilExportFileInfo($this->object->getId());
        $exp->setVersion($version);
        $exp->setCreationDate(new ilDateTime($ts,IL_CAL_UNIX));
        $exp->setExportType('app');
        $exp->setFilename($new_file);
        $exp->create();
    }


}