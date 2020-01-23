<?php
// Copyright (c) 2019 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

require_once(__DIR__ . '/class.ilSwingAppBaseGUI.php');

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
    /** @var  ilSwingAppConfig $config */
    protected $config;

    /** @var  ilSwingAppSettings  $settings*/
    protected $settings;

    /** @var ilObjDataCollection $parentObj */
    protected $parentObj;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->parentObj = new ilObjDataCollection($_GET['ref_id'], true);

        $this->config = $this->plugin->getConfig();
        $this->settings = $this->plugin->getSettings($this->parentObj->getId());
    }


    /**
     * Modify the export tab toolbar
     */
    public function modifyExportToolbar()
    {

        if (empty($this->toolbar->getItems()))
        {
            // e.g delete confirmation is shown
            return;
        }


        $button = ilLinkButton::getInstance();
        $button->setCaption($this->plugin->txt('export_content'), false);
        $button->setUrl($this->getLinkTarget('exportContent'));
        $this->toolbar->addButtonInstance($button);

        $this->toolbar->addSeparator();

        if (!empty($this->settings->get('publish_url'))) {
            $button = ilLinkButton::getInstance();
            $button->setCaption($this->plugin->txt('open_app'), false);
            $button->setUrl($this->settings->get('publish_url'));
            $button->setTarget('_blank');
            $this->toolbar->addButtonInstance($button);
        }

        if ($this->isPublishPossible()) {
            $button = ilLinkButton::getInstance();
            $button->setCaption($this->plugin->txt('update_app'), false);
            $button->setUrl($this->getLinkTarget('confirmUpdateApp'));
            $this->toolbar->addButtonInstance($button);
        }

        if ($this->plugin->hasAdminAccess()) {
            $this->toolbar->addSeparator();

            $button = ilLinkButton::getInstance();
            $button->setCaption($this->plugin->txt('app_settings'), false);
            $button->setUrl($this->getLinkTarget('editSettings'));
            $this->toolbar->addButtonInstance($button);
        }

    }


    /**
     * Handles all commands, default is "show"
     */
    public function executeCommand()
    {
        if (!$this->access->checkAccess('write','',$this->parentObj->getRefId())) {
            ilUtil::sendFailure($this->lng->txt("permission_denied"), true);
            $this->returnToObject();
        }
        $this->ctrl->saveParameter($this, 'ref_id');

        $cmd = $this->ctrl->getCmd('editSettings');
        switch ($cmd)
        {
            case "editSettings":
                $this->checkAdminAccess();
                $this->prepareOutput();
                $this->$cmd();
                break;
            case "saveSettings":
            case "cancelSettings":
                $this->checkAdminAccess();
                $this->$cmd();
                break;
            case "exportContent":
            case "returnToExport":
                $this->$cmd();
                break;

            case "confirmUpdateApp":
                $this->checkPublishPossible();
                $this->prepareOutput();
                $this->$cmd();
                break;

            case "cancelUpdateApp":
            case "updateApp":
                $this->checkPublishPossible();
                $this->$cmd();
                break;

            default:
                ilUtil::sendFailure($this->lng->txt("permission_denied"), true);
                $this->returnToObject();
                break;
        }
    }


    /**
     * Prepare the header, tabs etc.
     */
    protected function prepareOutput()
    {
        global $DIC;

        /** @var ilLocatorGUI $ilLocator */
        $ilLocator = $DIC['ilLocator'];

        $this->ctrl->setParameterByClass('ilObjDataCollectionGUI', 'ref_id',  $this->parentObj->getRefId());
        $ilLocator->addRepositoryItems($this->parentObj->getRefId());
        $ilLocator->addItem($this->parentObj->getTitle(),$this->ctrl->getLinkTargetByClass(['ilRepositoryGUI', 'ilObjDataCollectionGUI']));

        $this->tpl->getStandardTemplate();
        $this->tpl->setLocator();
        $this->tpl->setTitle($this->parentObj->getPresentationTitle());
        $this->tpl->setDescription($this->parentObj->getLongDescription());
        $this->tpl->setTitleIcon(ilObject::_getIcon('', 'big', 'dcl'), $this->lng->txt('obj_dcl'));

        return true;
    }

    /**
     * Init the settings form
     */
    protected function initSettingsForm()
    {
        require_once('Services/Form/classes/class.ilPropertyFormGUI.php');
        $form = new ilPropertyFormGUI();
        $form->setFormAction($this->ctrl->getFormAction($this, 'editSettings'));

        $this->settings->addFormItems($form);

        $form->addCommandButton('saveSettings', $this->lng->txt('save'));
        $form->addCommandButton('cancelSettings', $this->lng->txt('cancel'));

        return $form;
    }


    /**
     * Edit the archive settings
     */
    protected function editSettings()
    {
        if (!$this->plugin->isBuildPossible()
        ) {
            ilUtil::sendInfo($this->plugin->txt("build_not_possible"), false);
        }
        elseif (!$this->isPublishPossible()
        ) {
            ilUtil::sendInfo($this->plugin->txt("publish_not_possible"), false);
        }

        $form = $this->initSettingsForm();
        $this->tpl->setContent($form->getHTML());
        $this->tpl->show();
    }


    /**
     * Save the archive settings
     */
    protected function saveSettings()
    {
        $form = $this->initSettingsForm();
        if (!$form->checkInput())
        {
            $form->setValuesByPost();
            $this->prepareOutput();
            $this->tpl->setContent($form->getHTML());
            $this->tpl->show();
            return;
        }

        // needed again because initConfigForm will set the saved values to the form
        $form->setValuesByPost();

        $this->settings->setValuesFromForm($form);
        $this->settings->write();


        $message = $this->plugin->txt('settings_saved');
        if (!$this->plugin->isBuildPossible()
        ) {
            $message .= '<br />' . $this->plugin->txt("build_not_possible");
        }
        elseif (!$this->isPublishPossible()
        ) {
            $message .= '<br />' . $this->plugin->txt("publish_not_possible");
        }

        ilUtil::sendSuccess($message, true);


        $this->returnToExport();
    }


    /**
     * Cancel the archive settings
     */
    protected function cancelSettings()
    {
        $this->returnToExport();
    }

    /**
     * Export the content
     * @throws ilDateTimeException
     */
    protected function exportContent()
    {
        $this->plugin->includeClass('class.ilSwingAppPublish.php');
        $publisher = new ilSwingAppPublish($this->parentObj);
        $publisher->buildContent();
        $publisher->packContent();

        ilUtil::sendSuccess($this->plugin->txt("content_exported"), true);
        $this->returnToExport();
    }

    /**
     * Confirm to update the app
     */
    protected function confirmUpdateApp()
    {
        if ($this->plugin->isBuildRunning()) {
            ilUtil::sendFailure($this->plugin->getBuildRunningMessage(), true);
            $this->returnToExport();
        }

        $url = $this->settings->get('publish_url');
        $link = '<a target="_blank" href="'.$url.'" >'.$url.'</a>';

        $gui = new ilConfirmationGUI();
        $gui->setFormAction($this->ctrl->getFormAction($this));
        $gui->setHeaderText(sprintf($this->plugin->txt('confirm_update_app'), $link));
        $gui->setConfirm($this->plugin->txt('update_app'), 'updateApp');
        $gui->setCancel($this->lng->txt('cancel'), 'returnToExport');

        $this->tpl->setContent($gui->getHTML());
        $this->tpl->show();
    }

    /**
     * Export the content
     * @throws ilDateTimeException
     */
    protected function updateApp()
    {
        $this->plugin->includeClass('class.ilSwingAppPublish.php');
        $publisher = new ilSwingAppPublish($this->parentObj);

        if ($this->plugin->isBuildRunning()) {
            ilUtil::sendFailure($this->plugin->getBuildRunningMessage(), true);
            $this->returnToExport();
        }
        $this->plugin->setBuildRunning(true);
        $publisher->buildContent();
        $success = $publisher->publishApp();
        $info = '<pre class="small" style="height: 200px; overflow:scroll;">'.implode('<br />', $publisher->getBuildLog()).'</pre>';
        $this->plugin->setBuildRunning(false);

        if ($success) {
            ilUtil::sendSuccess($this->plugin->txt("app_updated") . $info, true);
        }
        else {
            ilUtil::sendFailure($this->plugin->txt("app_update_failed") . $info, true);

        }
        $this->returnToExport();
    }

    /**
     * Cancel the app update
     */
    protected function cancelUpdateApp()
    {
        $this->returnToExport();
    }


    /**
     * Check if the user has admin access and retorn with an error if not
     */
    protected function checkAdminAccess()
    {
        if (!$this->plugin->hasAdminAccess()) {
            ilUtil::sendFailure($this->lng->txt("permission_denied"), true);
            $this->returnToObject();
        }
    }

    /**
     * Check if the user has admin access and retorn with an error if not
     */
    protected function checkPublishPossible()
    {
        if (!$this->isPublishPossible()
        ) {
            ilUtil::sendFailure($this->plugin->txt("build_not_possible"), true);
            $this->returnToObject();
        }
    }

    /**
     * Check if the user has admin access and retorn with an error if not
     */
    protected function isPublishPossible()
    {
        return ($this->plugin->isBuildPossible() &&
            is_dir($this->settings->get('publish_dir')) &&
            is_writeable($this->settings->get('publish_dir'))
        );
    }

    /**
     * Return to the export screen of the parent object
     */
    protected function returnToExport()
    {
        $this->ctrl->setParameterByClass('ilDclExportGUI', 'ref_id', $this->parentObj->getRefId());
        $this->ctrl->redirectByClass(array('ilRepositoryGUI','ilObjDataCollectionGUI', 'ilDclExportGUI'));
    }

    /**
     * Return to the user view of the parent object
     */
    protected function returnToObject()
    {
        $this->ctrl->redirectToURL("goto.php?target=dcl_".$this->parentObj->getRefId());
    }
}
