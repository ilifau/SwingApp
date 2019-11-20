<?php
// Copyright (c) 2019 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

require_once('Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SwingApp/classes/class.ilSwingAppBaseData.php');

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
        $this->params = [
            'base_config' => ilSwingAppParam::_create(
                'base_config',
                $this->plugin->txt('base_config'),
                $this->plugin->txt('base_config_info'),
                ilSwingAppParam::TYPE_HEAD,
                null
            )
        ];
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

        foreach ($this->params as $param)
        {
            $ilDB->replace('swingapp_config',
                array('param_name' => array('text', $param->name)),
                array('param_value' => array('text', (string) $param->value))
            );
        }
    }
}