<?php
// Copyright (c) 2019 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

require_once(__DIR__ . '/class.ilSwingAppBaseData.php');

/**
 * SwingApp plugin config class
 *
 * @author Fred Neumann <fred.neumann@ili.fau.de>
 *
 */
class ilSwingAppConfig extends ilSwingAppBaseData
{
    /**
     * Initialize the list of Params
     */
    protected function initParams()
    {
        $this->addParam( ilSwingAppParam::_create(
            'base_config',
            $this->plugin->txt('base_config'),
            '',
            ilSwingAppParam::TYPE_HEAD,
            null
        ));

        $this->addParam( ilSwingAppParam::_create(
            'build_command',
            $this->plugin->txt('build_command'),
            $this->plugin->txt('build_command_info'),
            ilSwingAppParam::TYPE_TEXT,
            ''
        ));

        $this->addParam( ilSwingAppParam::_create(
            'build_base_dir',
            $this->plugin->txt('build_base_dir'),
            $this->plugin->txt('build_base_dir_info'),
            ilSwingAppParam::TYPE_TEXT,
            ''
        ));

        $this->addParam( ilSwingAppParam::_create(
            'build_content_dir',
            $this->plugin->txt('build_content_dir'),
            $this->plugin->txt('build_content_dir_info'),
            ilSwingAppParam::TYPE_TEXT,
            ''
        ));

        $this->addParam( ilSwingAppParam::_create(
            'build_result_dir',
            $this->plugin->txt('build_result_dir'),
            $this->plugin->txt('build_result_dir_info'),
            ilSwingAppParam::TYPE_TEXT,
            ''
        ));


        $this->addParam( ilSwingAppParam::_create(
            'build_running_since',
            $this->plugin->txt('build_running_since'),
            $this->plugin->txt('build_running_since_info'),
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

        $query = "SELECT * FROM swingapp_config";
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
            $ilDB->replace('swingapp_config',
                array('param_name' => array('text', $param->name)),
                array('param_value' => array('text', (string) $param->value))
            );
        }
    }
}