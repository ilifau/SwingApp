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
        $button->setCaption($this->plugin->txt('create_app'), false);
        $button->setUrl($this->getLinkTarget('createApp'));
        $this->toolbar->addButtonInstance($button);

        $button = ilLinkButton::getInstance();
        $button->setCaption($this->plugin->txt('start_app'), false);
        $button->setTarget('_blank');
        $button->setUrl('https://creator.ionic.io/share/2c6cd2fda95e');
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
            case "createApp":
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

    public function createApp()
    {
        $a_id = $this->parentObj->getId();
        $a_type = 'dcl';
        $v = explode(".", ILIAS_VERSION_NUMERIC);
        $a_target_release = $v[0].".".$v[1].".0";
        $ts = time();

        ilExport::_createExportDirectory($a_id, "xml", $a_type);
        $export_dir = ilExport::_getExportDirectory($a_id, "xml", $a_type);

        $sub_dir = $ts.'__'.IL_INST_ID.'__xml_'.$this->parentObj->getId();
        $new_file = $ts.'__'.IL_INST_ID.'__app_'.$this->parentObj->getId().'.zip';

        $export_run_dir = $export_dir."/".$sub_dir;
        ilUtil::makeDirParents($export_run_dir);

        ilUtil::rCopy($this->plugin->getDirectory() . '/apps/demoApp/www', $export_run_dir);

        $exp = new ilExportFileInfo($a_id);
        $exp->setVersion($a_target_release);
        $exp->setCreationDate(new ilDateTime($ts,IL_CAL_UNIX));
        $exp->setExportType('app');
        $exp->setFilename($new_file);
        $exp->create();

        ilUtil::zip($export_run_dir, $export_dir."/".$new_file);
        ilUtil::delDir($export_run_dir);

        ilUtil::sendSuccess($this->plugin->txt("app_created"), true);
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
