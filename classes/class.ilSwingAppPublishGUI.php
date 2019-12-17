<?php
// Copyright (c) 2019 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

require_once('Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/SwingApp/classes/class.ilSwingAppBaseGUI.php');

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
        $this->toolbar->addSeparator();

        if ($this->plugin->hasAdminAccess()) {
            $button = ilLinkButton::getInstance();
            $button->setCaption($this->plugin->txt('app_settings'), false);
            $button->setUrl($this->getLinkTarget('editSettings'));
            $this->toolbar->addButtonInstance($button);
        }

        $button = ilLinkButton::getInstance();
        $button->setCaption($this->plugin->txt('export_content'), false);
        $button->setUrl($this->getLinkTarget('exportContent'));
        $this->toolbar->addButtonInstance($button);
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

        $form->setValuesByPost();
        $this->settings->setValuesFromForm($form);
        $this->settings->write();

        ilUtil::sendSuccess($this->lng->txt('settings_saved'), true);
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
    public function exportContent()
    {
        $this->plugin->includeClass('class.ilSwingAppPublish.php');
        $publisher = new ilSwingAppPublish($this->parentObj);
        $publisher->buildContent();

        ilUtil::sendSuccess($this->plugin->txt("content_exported"), true);
        $this->returnToExport();
    }

    /**
     * Check if the user has admin access and retorn with an error if not
     */
    public function checkAdminAccess()
    {
        if (!$this->plugin->hasAdminAccess()) {
            ilUtil::sendFailure($this->lng->txt("permission_denied"), true);
            $this->returnToObject();
        }
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
    public function returnToObject()
    {
        $this->ctrl->redirectToURL("goto.php?target=tst_".$this->parentObj->getRefId());
    }
}
