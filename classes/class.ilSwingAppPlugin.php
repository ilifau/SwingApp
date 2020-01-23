<?php
// Copyright (c) 2019 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

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

    /** @var array ilSwingAppSettings[]  (indexed by obj_id) */
    protected $settings = [];


    /**
     * Get the name of the plugin
     * @return string
     */
	public function getPluginName()
	{
		return "SwingApp";
	}


    /**
     * Get the data set for an object
     * @param $obj_id
     * @return ilSwingAppSettings
     */
	public function getSettings($obj_id)
    {
        if (!isset($this->settings[$obj_id])) {
           $this->includeClass('class.ilSwingAppSettings.php');
           $this->settings[$obj_id] = new ilSwingAppSettings($obj_id);
        }
        return $this->settings[$obj_id];
    }


    /**
     * Get the plugin configuration
     * @return ilSwingAppConfig
     */
    public function getConfig()
    {
        if (!isset($this->config)) {
            $this->includeClass('class.ilSwingAppConfig.php');
            $this->config = new ilSwingAppConfig();
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
     * Check if a build is possible
     * @return bool
     */
    public function isBuildPossible()
    {
        $this->getConfig();

        $cmd = $this->config->get('build_command');
        $baseDir = $this->config->get('build_base_dir');
        $contentDir = $this->config->get('build_content_dir');
        $resultDir = $this->config->get('build_result_dir');

        if (empty($cmd)) {
            return false;
        }
        foreach ([$baseDir, $contentDir, $resultDir] as $dir)

        if (!is_dir($dir) && ! is_writable($dir)) {
            return false;
        }

        return true;
    }

    /**
     * Check if a build process is running
     * @return bool
     */
    public function isBuildRunning()
    {
        $this->getConfig();
        $running_since = $this->config->get('build_running_since');
        return !empty($running_since);
    }

    /**
     * Gert the message for a running build process
     * @return string
     */
    public function getBuildRunningMessage()
    {
        $this->getConfig();
        $running_since = $this->config->get('build_running_since');
        return sprintf($this->txt('build_running_message'), $running_since);
    }

    /**
     * Set the running status for the build process
     * @param bool $running
     * @throws ilDateTimeException
     */
    public function setBuildRunning($running = true)
    {
        $this->getConfig();

        if ($running) {
            $now = new ilDateTime(time(), IL_CAL_UNIX);
            $this->config->set('build_running_since', $now->get(IL_CAL_DATETIME));
        }
        else {
            $this->config->set('build_running_since', '');
        }
        $this->config->write();
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


    /**
     * Check if the user has administrative access
     * @return bool
     */
    public function hasAdminAccess()
    {
       global $DIC;
        return $DIC->rbac()->system()->checkAccess("visible", SYSTEM_FOLDER_ID);
    }
}
