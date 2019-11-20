<?php
// Copyright (c) 2018 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

require_once('Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SwingApp/classes/class.ilSwingAppParam.php');

/**
 * SwingApp plugin data class
 *
 * @author Fred Neumann <fred.neumann@ili.fau.de>
 *
 */
class ilSwingAppData
{

    /** @var int obj_id */
    protected $obj_id;
	/**
	 * @var ilOERinFormParam[]	$params		parameters: 	name => ilSwingApparam
	 */
	protected $params = array();

    /**
     * @var ilOERinFormPlugin
     */
	protected $plugin;


	/**
	 * Constructor.
	 * @param ilPlugin
     * @param int obj_id;
	 */
	public function __construct($a_plugin_object, $a_obj_id)
	{
		$this->plugin = $a_plugin_object;
		$this->obj_id = $a_obj_id;

        /** @var ilSwingAppParam[] $params */
        $params = array();

        $params[] = ilSwingAppParam::_create(
            'data_base',
            $this->plugin->txt('data_base'),
            $this->plugin->txt('data_base_info'),
            ilSwingAppParam::TYPE_HEAD,
            null
        );

        foreach ($params as $param)
        {
            $this->params[$param->name] = $param;
        }
        $this->read();
	}

    /**
     * Get all parameters as an array
     * @return array name => value
     */
    public function getAllValues()
    {
        $result = array();
        foreach ($this->params as $name => $param)
        {
            $result[$name] = $param->value;
        }
        return $result;
    }

    /**
     * Get the value of a named parameter
     * @param $name
     * @return  mixed
     */
	public function get($name)
    {
        if (!isset($this->params[$name]))
        {
            return null;
        }
        else
        {
            return $this->params[$name]->value;
        }
    }

    /**
     * Set the value of the named parameter
     * @param string $name
     * @param mixed $value
     *
     */
    public function set($name, $value = null)
    {
       $param = $this->params[$name];

       if (isset($param))
       {
           $param->setValue($value);
       }
    }


    /**
     * Read the configuration from the database
     */
	public function read()
    {
        global $DIC;
        $ilDB = $DIC->database();

        $query = "SELECT * FROM swingapp_data WHERE obj_id = ". $ilDB->quote($this->obj_id, 'integer');
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
            $ilDB->replace('swingapp_data',
                array(
                    'obj_id' =>  array('integer', $this->obj_id),
                    'param_name' => array('text', $param->name)
                ),
                array('param_value' => array('text', (string) $param->value))
            );
        }
    }
}