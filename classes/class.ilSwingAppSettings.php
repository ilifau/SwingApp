<?php
// Copyright (c) 2018 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

require_once('Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SwingApp/classes/class.ilSwingAppBaseData.php');

/**
 * SwingApp plugin settings class
 *
 * @author Fred Neumann <fred.neumann@ili.fau.de>
 *
 */
class ilSwingAppSettings extends ilSwingAppBaseData
{

    /** @var int obj_id */
    protected $obj_id;


	/**
	 * Constructor.
     * @param int obj_id;
	 */
	public function __construct($a_obj_id)
	{
        $this->obj_id = $a_obj_id;
        parent::__construct();
	}

    /**
     * Initialize the list of Params
     */
    protected function initParams()
    {
        $this->addParam(ilSwingAppParam::_create(
            'data_import',
            $this->plugin->txt('data_import'),
            '',
            ilSwingAppParam::TYPE_HEAD,
            ''
        ));

        $this->addParam( ilSwingAppParam::_create(
            'media_import_dir',
            $this->plugin->txt('media_import_dir'),
            $this->plugin->txt('media_import_dir_info'),
            ilSwingAppParam::TYPE_TEXT,
            ''
        ));
    }

    /**
     * Read the configuration from the database
     */
	public function read()
    {
        global $DIC;
        $ilDB = $DIC->database();

        $query = "SELECT * FROM swingapp_settings WHERE obj_id = ". $ilDB->quote($this->obj_id, 'integer');
        $res = $ilDB->query($query);
        while($row = $ilDB->fetchAssoc($res))
        {
            $this->set($row['param_name'], $row['param_value']);
        }
    }

    /**
     * Write the configuration to the database
     */
    public function write()
    {
        global $DIC;
        $ilDB = $DIC->database();

        foreach ($this->getParams() as $param)
        {
            $ilDB->replace('swingapp_settings',
                array(
                    'obj_id' =>  array('integer', $this->obj_id),
                    'param_name' => array('text', $param->name)
                ),
                array('param_value' => array('text', (string) $param->value))
            );
        }
    }
}