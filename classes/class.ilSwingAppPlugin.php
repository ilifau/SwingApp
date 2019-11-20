<?php
// Copyright (c) 2019 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

include_once("./Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php");
 
/**
 * Basic plugin file
 *
 * @author Fred Neumann <fred.neumann@fau.de>
 * @version $Id$
 *
 */
class ilSwingAppPlugin extends ilUserInterfaceHookPlugin
{
    /** @var ilSwingAppConfig */
    protected $config;


	public function getPluginName()
	{
		return "SwingApp";
	}


    /**
     * Get the data set for an object
     * @param $obj_id
     * @return ilSwingAppData
     */
	public function getData($obj_id)
    {
        $this->includeClass('class.ilSwingAppData.php');
        return new ilSwingAppData($this, $obj_id);
    }


    /**
     * Get the plugin configuration
     * @return ilOERinFormConfig
     */
    public function getConfig()
    {
        if (!isset($this->config))
        {
            $this->includeClass('class.ilSwingAppConfig.php');
            $this->config = new ilOERinFormConfig($this);
        }
        return $this->config;
    }


    /**
     * Check if the object type is allowed
     * @param string $type
     * @return bool
     */
    public function isAllowedType($type)
    {
        return in_array($type, array('dcl'));
    }


    /**
	 * Get a user preference
	 * @param string	$name
	 * @param mixed		$default
	 * @return mixed
	 */
	public function getUserPreference($name, $default = false)
	{
		global $ilUser;
		$value = $ilUser->getPref($this->getId().'_'.$name);
		if ($value !== false)
		{
			return $value;
		}
		else
		{
			return $default;
		}
	}


	/**
	 * Set a user preference
	 * @param string	$name
	 * @param mixed		$value
	 */
	public function setUserPreference($name, $value)
	{
		global $ilUser;
		$ilUser->writePref($this->getId().'_'.$name, $value);
	}
}
