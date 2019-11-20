<?php
// Copyright (c) 2019 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE


/**
 * Base class for GUIs of the SwingApp plugin
 */
abstract class ilSwingAppBaseGUI
{
	/** @var  ilAccessHandler $access */
	protected $access;

	/** @var ilCtrl $ctrl */
	protected $ctrl;

	/** @var  ilLanguage $lng */
	protected $lng;

	/** @var ilTabsGUI */
	protected $tabs;

	/** @var  ilToolbarGUI $toolbar */
	protected $toolbar;

	/** @var ilTemplate $tpl */
	protected $tpl;

	/** @var ilSwingAppPlugin $plugin */
	protected $plugin;


	/**
	 * ilSwingAppBaseGUI constructor
	 */
	public function __construct()
	{
		global $DIC;

		$this->access = $DIC->access();
		$this->ctrl = $DIC->ctrl();
		$this->lng = $DIC->language();
		$this->tabs = $DIC->tabs();
		$this->toolbar = $DIC->toolbar();
		$this->tpl = $DIC['tpl'];

		$this->plugin = ilPlugin::getPluginObject(IL_COMP_SERVICE, 'UIComponent', 'uihk', 'SwingApp');
	}


	/**
	 * Get the link target for a command using the ui plugin router
	 * @param string $a_cmd
     * @param string $a_anchor
     * @param bool $a_async
     * @return string
	 */
	protected function getLinkTarget($a_cmd = '', $a_anchor = '', $a_async = false)
	{
	    $this->ctrl->setParameter($this,'ref_id', (int) $_GET['ref_id']);
		return $this->ctrl->getLinkTargetByClass(array('ilUIPluginRouterGUI', get_class($this)), $a_cmd, $a_anchor, $a_async);
	}
}