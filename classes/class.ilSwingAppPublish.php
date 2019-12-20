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

        $dictionary = [];
        $dictionary['modules'] = $this->exportModules();
        $dictionary['units'] = $this->exportUnits();
        $dictionary['words'] = $this->exportWords();
        file_put_contents($this->directory.'/data/dictionary.json', json_encode($dictionary, JSON_PRETTY_PRINT));

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
            $texts[$key] = $this->applyMarkup($value);
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
            $media[$key] = $files['standard'];
            $media[$key.'Start'] = $files['preview'];
        }

        return $media;
    }

    /**
     * Get the table content of TrainingModules
     * @return array
     */
    protected function exportModules()
    {
        $tableId = ilDclTable::_getTableIdByTitle('TrainingModules', $this->object->getId());
        $table = $this->object->getTableById($tableId);
        $list = $table->getPartialRecords('Name', "asc", null, 0, []);

        $modules = [];
        /** @var ilDclBaseRecordModel $record */
        foreach ($list['records'] as $record) {
            $id = $record->getId();
            $module = [
                'id' => $id,
                'name' => '',
                'description' => '',
                'videoName' => '',
                'videoDesc' => '',
                'videoNameStart' => '',
                'videoDescStart' => ''
            ];

            /** @var ilDclBaseFieldModel $field */
            foreach ($table->getFields() as $field) {
                $recField = $record->getRecordField($field->getId());
                switch ($field->getTitle()) {
                    case 'Name':
                        $module['name'] = $this->trimPrefix($recField->getExportValue());
                        break;
                    case 'Description':
                        $module['description'] = $recField->getExportValue();
                        break;
                    case 'Video':
                        $files = $this->exportMob($recField->getValue(), 'unit' . $id . '_name');
                        $module['videoName'] = $files['standard'];
                        $module['videoNameStart'] = $files['preview'];
                        break;
                    case 'VideoDescription':
                        $files = $this->exportMob($recField->getValue(), 'unit' . $id . '_desc');
                        $module['videoDesc'] = $files['standard'];
                        $module['videoDescStart'] = $files['preview'];
                        break;
                }
            }
            $modules[] = $module;
        }
        return $modules;
    }

    /**
     * Get the table content of TrainingUnits
     * @return array
     */
    protected function exportUnits()
    {
        $tableId = ilDclTable::_getTableIdByTitle('TrainingUnits', $this->object->getId());
        $table = $this->object->getTableById($tableId);
        $list = $table->getPartialRecords('Name', "asc", null, 0, []);

        $units = [];
        /** @var ilDclBaseRecordModel $record */
        foreach ($list['records'] as $record) {
            $id = $record->getId();
            $unit = [
                'id' => $id,
                'name' => '',
                'description' => '',
                'module' => '',
                'videoName' => '',
                'videoDesc' => '',
                'videoNameStart' => '',
                'videoDescStart' => ''
            ];

            /** @var ilDclBaseFieldModel $field */
            foreach ($table->getFields() as $field) {
                $recField = $record->getRecordField($field->getId());
                switch ($field->getTitle()) {
                    case 'Name':
                        $unit['name'] = $this->trimPrefix($recField->getExportValue());
                        break;
                    case 'Description':
                        $unit['description'] = $recField->getExportValue();
                        break;
                    case 'TrainingModule':
                        $unit['module'] = $recField->getValue();
                        break;
                    case 'Video':
                        $files = $this->exportMob($recField->getValue(), 'module' . $id . '_name');
                        $unit['videoName'] = $files['standard'];
                        $unit['videoNameStart'] = $files['preview'];
                        break;
                    case 'VideoDescription':
                        $files = $this->exportMob($recField->getValue(), 'module' . $id . '_desc');
                        $unit['videoDesc'] = $files['standard'];
                        $unit['videoDescStart'] = $files['preview'];
                        break;
                }
            }
            $units[] = $unit;
        }
        return $units;
    }


    /**
     * Get the table content of TrainingUnits
     * @return array
     */
    protected function exportWords()
    {
        $tableId = ilDclTable::_getTableIdByTitle('Words', $this->object->getId());
        $table = $this->object->getTableById($tableId);
        $list = $table->getPartialRecords('Number', "asc", null, 0, []);

        $words = [];
        /** @var ilDclBaseRecordModel $record */
        foreach ($list['records'] as $record) {
            $id = $record->getId();
            $word = [
                'id' => $id,
                'number' => '',
                'name' => '',
                'description' => '',
                'synonyms' => '',
                'units' => [],
                'videoName' => '',
                'videoDesc' => '',
                'videoNameStart' => '',
                'videoDescStart' => '',
                'img1' => '',
                'img2' => '',
                'img1Source' => '',
                'img2Source' => '',
                'relatedWords' => []
            ];

            /** @var ilDclBaseFieldModel $field */
            foreach ($table->getFields() as $field) {
                $recField = $record->getRecordField($field->getId());
                switch ($field->getTitle()) {
                    case 'Name':
                        $word['name'] = $recField->getExportValue();
                        break;
                    case 'Number':
                        $word['name'] = $recField->getExportValue();
                        break;
                    case 'Description':
                        $word['description'] = $recField->getExportValue();
                        break;
                    case 'Synonyms':
                        $word['synonyms'] = $recField->getExportValue();
                        break;
                    case 'TrainingUnits':
                        $word['units'] = (array) $recField->getValue();
                        break;
                    case 'Video':
                        $files = $this->exportMob($recField->getValue(), 'word' . $id . '_name');
                        $word   ['videoName'] = $files['standard'];
                        $word['videoNameStart'] = $files['preview'];
                        break;
                    case 'VideoDescription':
                        $files = $this->exportMob($recField->getValue(), 'word' . $id . '_desc');
                        $word['videoDesc'] = $files['standard'];
                        $word['videoDescStart'] = $files['preview'];
                        break;
                    case 'Img1':
                        $files = $this->exportMob($recField->getValue(), 'word' . $id . '_img1');
                        $word['img1'] = $files['standard'];
                        break;
                    case 'Img2':
                        $files = $this->exportMob($recField->getValue(), 'word' . $id . '_img2');
                        $word['img2'] = $files['standard'];
                        break;
                    case 'Img1-Source':
                        $word['img1Source'] = $recField->getExportValue();
                        break;
                    case 'Img2-Source':
                        $word['img2Source'] = $recField->getExportValue();
                        break;
                    case 'RelatedWords':
                        $word['relatedWords'] = (array) $recField->getValue();
                        break;
                }
            }
            $words[] = $word;
        }
        return $words;
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
                if (is_file($sourcefile)) {
                    $files['standard'] = $path;
                    copy($sourcefile, $this->directory . '/' . $path);
                }

                if ($mob->getVideoPreviewPic()) {
                    $previewfile = $mob->getVideoPreviewPic();
                    $pathinfo = pathinfo($previewfile);
                    $extension = $pathinfo['extension'];

                    $path = $subdir . '/' . $filename . '_start.'. strtolower($extension);
                    if (is_file($previewfile)) {
                        $files['preview'] = $path;
                        copy($previewfile, $this->directory . '/' . $path);
                    }
                }
            }
        }

        return $files;
    }

    /**
     * Cut the numeric prefix from a string
     * @param $text
     * @return string|string[]|null
     */
    protected function trimPrefix($text) {
        return preg_replace('/^[0-9\. ]+/', '', $text);
    }

    /**
     * Apply little markup to a text
     * @param $text
     * @return string|string[]|null
     */
    protected function applyMarkup($text) {
        $text = nl2br(trim($text));
        return preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
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