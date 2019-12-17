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

    /** @var ilObjDataCollection $object */
    protected $object;

    /** @var ilSwingAppPlugin */
	protected $plugin;

    /** @var string */
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
        ilUtil::makeDirParents($this->directory. '/pictures');
        ilUtil::makeDirParents($this->directory. '/videos');

        $texts = json_encode($this->exportGeneralTexts(), JSON_PRETTY_PRINT);
        file_put_contents($this->directory.'/data/texts.json', $texts);

        $media = json_encode($this->exportGeneralMedia(), JSON_PRETTY_PRINT);
        file_put_contents($this->directory.'/data/media.json', $media);



        $this->packContent();
    }

    /**
     * Get the table content of GeneralTexts
     * @return array
     */
    protected function exportGeneralTexts()
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

        return $texts;
    }

    /**
     * Get the table content of GeneralMedia
     * @return array
     */
    protected function exportGeneralMedia()
    {
        $tableId = ilDclTable::_getTableIdByTitle('GeneralMedia', $this->object->getId());
        $table = $this->object->getTableById($tableId);

        $keyField = $table->getFieldByTitle('Identifier');
        $valueField = $table->getFieldByTitle('Medium');

        $list = $table->getPartialRecords('Identifier', "asc", null, 0, []);

        $media = [];
        /** @var ilDclBaseRecordModel $record */
        foreach ($list['records'] as $record) {
            $id = $record->getId();
            $key = $record->getRecordField($keyField->getId())->getExportValue();

            $mob_Id = $record->getRecordField($valueField->getId())->getValue();
            $files = $this->exportMob($mob_Id, 'medium'.$id);
            $media[$key] = $files;
        }

        return $media;
    }

    /**
     * Export media object content and return the file name
     * @param int $mob_id
     * @param string target filename (without extension)
     * @return array
     */
    protected function exportMob($mob_id, $filename)
    {
        $files = [
            'standard' => '',
            'preview' => ''
        ];

        if ($mob = new ilObjMediaObject($mob_id)) {
            $mobdir = ilObjMediaObject::_getDirectory($mob->getId());


            if ($med = $mob->getMediaItem('Standard')) {
                if (in_array($med->getSuffix(), array('jpg', 'jpeg', 'png', 'gif'))) {
                    $subdir = "pictures";
                }
                else {
                    $subdir = "videos";
                }

                $sourcefile = $mobdir . "/" . $med->getLocation();
                $pathinfo = pathinfo($sourcefile);
                $extension = $pathinfo['extension'];

                $path = $subdir . '/' . $filename . '.'. strtolower($extension);
                $files['standard'] = $path;
                copy($mobdir . "/" . $med->getLocation(), $this->directory . '/' . $path);


                if ($mob->getVideoPreviewPic()) {
                    $previewfile = $mob->getVideoPreviewPic();
                    $pathinfo = pathinfo($previewfile);
                    $extension = $pathinfo['extension'];

                    $path = $subdir . '/' . $filename . '_preview.'. strtolower($extension);
                    $files['preview'] = $path;
                    copy($mob->getVideoPreviewPic(), $this->directory . '/' . $path);
                }
            }
        }

        return $files;
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