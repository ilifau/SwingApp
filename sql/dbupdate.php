<#1>
<?php
    /**
     * Copyright (c) 2017 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
     * GPLv3, see docs/LICENSE
     */

    /**
     * SwingApp plugin: database update script
     *
     * @author Fred Neumann <fred.neumann@fau.de>
     */
?>
<#2>
<?php
if (!$ilDB->tableExists('swingapp_config'))
{
    $fields = array(
        'param_name' => array(
            'type' => 'text',
            'length' => 255,
            'notnull' => true,
        ),
        'param_value' => array(
            'type' => 'text',
            'length' => 255,
            'notnull' => false,
            'default' => null
        )
    );
    $ilDB->createTable("swingapp_config", $fields);
    $ilDB->addPrimaryKey("swingapp_config", array("param_name"));
}
?>
<#3>
<?php
if (!$ilDB->tableExists('swingapp_data'))
{
    $fields = array(
        'obj_id' => array(
            'type' => 'integer',
            'length' => 4,
            'notnull' => true,
        ),
        'param_name' => array(
            'type' => 'text',
            'length' => 255,
            'notnull' => true,
        ),
        'param_value' => array(
            'type' => 'text',
            'length' => 255,
            'notnull' => false,
            'default' => null
        )
    );
    $ilDB->createTable("swingapp_settings", $fields);
    $ilDB->addPrimaryKey("swingapp_settings", array("obj_id", "param_name"));
}
?>
