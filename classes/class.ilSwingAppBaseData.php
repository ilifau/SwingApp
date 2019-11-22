<?php
// Copyright (c) 2019 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

require_once('Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SwingApp/classes/class.ilSwingAppParam.php');

/**
 * Base class for data (config and settings)
 *
 * @author Fred Neumann <fred.neumann@ili.fau.de>
 *
 */
abstract class ilSwingAppBaseData
{
	/**
	 * @var ilSwingAppParam[]	$params		name => ilSwingAppParam
	 */
	protected $params = [];

    /**
     * @var ilSwingAppPlugin
     */
	protected $plugin;


	/**
	 * Constructor.
	 * @param ilSwingAppPlugin
     * @param int obj_id;
	 */
	public function __construct()
	{
		$this->plugin = ilPlugin::getPluginObject(IL_COMP_SERVICE, 'UIComponent', 'uihk', 'SwingApp');
		$this->initParams();
        $this->read();
	}

    /**
     * Initialize the list of params
     * @return mixed
     */
    abstract protected function initParams();

    /**
     * Read the param values from the database
     */
    abstract public function read();

    /**
     * Write the param values to the database
     */
    abstract public function write();


    /**
     * Add a param to the list of params
     * @param ilSwingAppParam $param
     */
    protected function addParam($param)
    {
        $this->params[$param->name] = $param;
    }

    /**
     * Get the params
     * @return ilSwingAppParam[]
     */
    protected function getParams()
    {
        return array_values($this->params);
    }


    /**
     * Add the form elements to a property form
     * @param ilPropertyFormGUI $form
     */
    public function addFormItems($form)
    {
        foreach ($this->params as $name => $param) {
            $form->addItem($param->getFormItem('ilSwingAppData_'.$name));
        }
    }

    /**
     * Set the values from a propertyForm
     * @param ilPropertyFormGUI $form
     */
    public function setValuesFromForm($form)
    {
        foreach ($this->params as $name => $param) {
            /** @var ilFormPropertyGUI $item */
            $item = $form->getItemByPostVar('ilSwingAppData_'.$name);
            $param->setFromItem($item);
        }
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
}