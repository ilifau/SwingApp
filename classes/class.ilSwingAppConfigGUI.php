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
            case "confirmUpdateApps":
            case "updateApps":
				$this->$cmd();
				break;

			case "configure":
			default:
            $this->modifyConfigToolbar();
            $this->editConfiguration();
				break;
		}
	}

	/**
	 * Edit the configuration
	 */
	protected function editConfiguration()
	{
        if (!$this->plugin->isBuildPossible()
        ) {
            ilUtil::sendInfo($this->plugin->txt("build_not_possible"), false);
        }

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

    /**
     * Modify the export tab toolbar
     */
    public function modifyConfigToolbar()
    {
        if ($this->plugin->isBuildPossible()) {
            $button = ilLinkButton::getInstance();
            $button->setCaption($this->plugin->txt('update_apps'), false);
            $button->setUrl($this->ctrl->getLinkTarget($this, 'confirmUpdateApps'));
            $this->toolbar->addButtonInstance($button);
        }

    }

    /**
     * Confirm the update of apps
     */
    protected function confirmUpdateApps() {

        if ($this->plugin->isBuildRunning()) {
            ilUtil::sendFailure($this->plugin->getBuildRunningMessage(), true);
            $this->ctrl->redirect($this, 'editConfiguration');
        }

        $this->plugin->includeClass('class.ilSwingAppSettings.php');

        $objects = ilSwingAppSettings::getPublishableObjects();

        $gui = new ilConfirmationGUI;
        $gui->setFormAction($this->ctrl->getFormAction($this));
        foreach ($objects as $obj_id => $title) {
            $gui->addItem('obj_ids[]', $obj_id, $title);
        }
        $gui->setHeaderText($this->plugin->txt('confirm_update_apps'));
        $gui->setConfirm($this->plugin->txt('update_apps'), 'updateApps');
        $gui->setCancel($this->lng->txt('cancel'), 'editConfiguration');

        $this->tpl->setContent($gui->getHTML());
    }

    /**
     *
     */
    protected function UpdateApps() {

        if ($this->plugin->isBuildRunning()) {
            ilUtil::sendFailure($this->plugin->getBuildRunningMessage(), true);
            $this->ctrl->redirect($this, 'editConfiguration');
        }

        $this->plugin->setBuildRunning(true);

        $log = [];
        $built = [];
        $failed = [];

        $obj_ids = $_POST['obj_ids'];
        foreach ($obj_ids as $obj_id) {

            $object = new ilObjDataCollection($obj_id, false);
            $log[] = 'Publishing '. $object->getTitle();

            $this->plugin->includeClass('class.ilSwingAppPublish.php');
            $publisher = new ilSwingAppPublish($object);
            $publisher->buildContent();
            $success = $publisher->publishApp();
            if ($success) {
                $built[] = $object->getTitle();
            }
            else {
                $failed[] = $object->getTitle();
            }
            $log = array_merge($log, $publisher->getBuildLog());
        }

        $this->plugin->setBuildRunning(false);

        $info = '<pre class="small" style="height: 200px; overflow:scroll;">'.implode('<br />', $log).'</pre>';

        $built = empty($built) ? [$this->plugin->txt('none')] : $built;
        $failed = empty($failed) ? [$this->plugin->txt('none')] : $failed;

        ilUtil::sendInfo(sprintf($this->plugin->txt("apps_updated"),
                            implode(', ', $built), implode(', ', $failed)). $info, true);

        $this->ctrl->redirect($this, 'editConfiguration');
    }
}