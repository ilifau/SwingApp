<?php
// Copyright (c) 2017 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

require_once('Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/OERinForm/classes/class.ilOERinFormBaseGUI.php');

/**
 * GUI for SwingApp publishing functions
 *
 * @author Fred Neumann <fred.neumann@fau.de>
 * @version $Id$
 *
 * @ilCtrl_IsCalledBy ilSwingAppPublishGUI: ilUIPluginRouterGUI
 */
class ilSwingAppPublishGUI extends ilSwingAppBaseGUI
{

	/** @var  int parent object ref_id */
	protected $parent_ref_id;

	/** @var  string parent object type */
	protected $parent_type;

	/** @var  string parent gui class */
	protected $parent_gui_class;

	/** @var  ilObject $parent_obj */
	protected $parent_obj;

	/** @var  ilOERinFormPublishMD $md_obj */
	protected $md_obj;

	/**
	 * constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->ctrl->saveParameter($this, 'ref_id');

		$this->parent_ref_id = $_GET['ref_id'];
		$this->parent_type = ilObject::_lookupType($this->parent_ref_id, true);
		$this->parent_obj = ilObjectFactory::getInstanceByRefId($this->parent_ref_id);
		$this->parent_gui_class = ilObjectFactory::getClassByType($this->parent_type).'GUI';
    }


	/**
	* Handles all commands
	*/
	public function executeCommand()
	{
		$fallback_url = "goto.php?target=".$this->parent_type.'_'.$this->parent_ref_id;

		if (!$this->access->checkAccess('write','', $_GET['ref_id']))
		{
            ilUtil::sendFailure($this->lng->txt("permission_denied"), true);
            $this->ctrl->redirectToURL( $fallback_url);
		}

		$this->ctrl->saveParameter($this, 'ref_id');
		$cmd = $this->ctrl->getCmd('showHelp');

		$next_class = $this->ctrl->getNextClass($this);

		switch ($next_class)
		{
			default:
				switch ($cmd)
				{
					case "publish":
						$this->$cmd();
						break;

					default:
						ilUtil::sendFailure($this->lng->txt("permission_denied"), true);
                        $this->ctrl->redirectToURL($fallback_url);
						break;
				}
		}


	}

	/**
	 * Get the plugin object
	 * @return ilOERinFormPlugin|null
	 */
	public function getPlugin()
	{
		return $this->plugin;
	}

    /**
	 * Prepare the test header, tabs etc.
	 */
	protected function prepareOutput()
	{
		global $DIC;

		/** @var ilLocatorGUI $ilLocator */
		$ilLocator = $DIC['ilLocator'];

		$ilLocator->addRepositoryItems($this->parent_obj->getRefId());
		$ilLocator->addItem($this->parent_obj->getTitle(), ilLink::_getLink($this->parent_ref_id, $this->parent_type));

		$this->tpl->getStandardTemplate();
		$this->tpl->setLocator();
		$this->tpl->setTitle($this->parent_obj->getPresentationTitle());
		$this->tpl->setDescription($this->parent_obj->getLongDescription());
		$this->tpl->setTitleIcon(ilObject::_getIcon('', 'big', $this->parent_type), $this->lng->txt('obj_'.$this->parent_type));
	}


	/**
	 * Reject the publishing
	 */
	public function publish()
	{
		ilUtil::sendSuccess($this->plugin->txt('published'), true);
		$this->returnToParent();
	}


    /**
     * Return to the parent GUI
     */
    protected function returnToParent()
    {
        $this->ctrl->redirectToURL($_GET['return']);
    }

}
