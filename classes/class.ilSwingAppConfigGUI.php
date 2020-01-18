<?php
// Copyright (c) 2019 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

require_once(__DIR__ . '/class.ilSwingAppConfig.php');

/**
 * Test archive creator configuration user interface class
 *
 * @author Fred Neumann <fred.neumann@fau.de>
 * @author Jesus Copado <jesus.copado@fau.de>
 */
class ilSwingAppConfigGUI extends ilPluginConfigGUI
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

	/** @var  ilSwingAppConfig $config */
	protected $config;

	/**
	 * Constructor.
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
	}


    /**
     * Handles all commands, default is "configure"
     * @param $cmd
     */
	public function performCommand($cmd)
	{
		$this->plugin = $this->getPluginObject();
		$this->config = $this->plugin->getConfig();

		switch ($cmd)
		{
			case "saveConfiguration":
				$this->saveConfiguration();
				break;

			case "configure":
			default:
				$this->editConfiguration();
				break;
		}
	}

	/**
	 * Edit the configuration
	 */
	protected function editConfiguration()
	{
		$form = $this->initConfigForm();
		$this->tpl->setContent($form->getHTML());
	}

	/**
	 * Save the edited configuration
	 */
	protected function saveConfiguration()
	{
		$form = $this->initConfigForm();
		if (!$form->checkInput())
		{
			$form->setValuesByPost();
			$this->tpl->setContent($form->getHTML());
			return;
		}

		// needed again because initConfigForm will set the saved values to the form
        $form->setValuesByPost();

        $this->config->setValuesFromForm($form);
        $this->config->write();

		ilUtil::sendSuccess($this->plugin->txt('settings_saved'), true);
		$this->ctrl->redirect($this, 'editConfiguration');
	}

	/**
	 * Fill the configuration form
	 * @return ilPropertyFormGUI
	 */
	protected function initConfigForm()
	{
		$form = new ilPropertyFormGUI();
		$form->setFormAction($this->ctrl->getFormAction($this, 'editConfiguration'));

		$this->config->addFormItems($form);

		$form->addCommandButton('saveConfiguration', $this->lng->txt('save'));

		return $form;
	}
}