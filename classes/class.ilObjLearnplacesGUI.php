<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\gui\helper\CommonControllerAction;
use KPG\Learnplaces\service\publicapi\block\LearnplaceService;
use KPG\Learnplaces\service\publicapi\block\MapBlockService;
use KPG\Learnplaces\service\publicapi\model\ILIASLinkBlockModel;
use KPG\Learnplaces\service\publicapi\model\MapBlockModel;
use KPG\Learnplaces\service\security\AccessGuard;
use KPG\Learnplaces\service\visibility\LearnplaceServiceDecoratorFactory;
use KPG\Learnplaces\persistence\entity\VideoBlock;

/**
 * Class ilObjLearnplacesGUI
 *
 * @author            Nicolas Schäfli <ns@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilObjLearnplacesGUI: ilRepositoryGUI, ilObjPluginDispatchGUI
 * @ilCtrl_isCalledBy ilObjLearnplacesGUI: ilAdministrationGUI
 * @ilCtrl_Calls      ilObjLearnplacesGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI, ilLearningProgressGUI
 * @ilCtrl_Calls      ilObjLearnplacesGUI: ilCommonActionDispatcherGUI
 * @ilCtrl_Calls      ilObjLearnplacesGUI: xsrlPictureUploadBlockGUI
 * @ilCtrl_Calls      ilObjLearnplacesGUI: xsrlPictureBlockGUI
 * @ilCtrl_Calls      ilObjLearnplacesGUI: xsrlContentGUI
 * @ilCtrl_Calls      ilObjLearnplacesGUI: xsrlRichTextBlockGUI
 * @ilCtrl_Calls      ilObjLearnplacesGUI: xsrlIliasLinkBlockGUI
 * @ilCtrl_Calls      ilObjLearnplacesGUI: xsrlMapBlockGUI
 * @ilCtrl_Calls      ilObjLearnplacesGUI: xsrlVideoBlockGUI
 * @ilCtrl_Calls      ilObjLearnplacesGUI: xsrlAccordionBlockGUI
 * @ilCtrl_Calls      ilObjLearnplacesGUI: xsrlSettingGUI
 * @ilCtrl_Calls      ilObjLearnplacesGUI: xsrlVisitorsGUI
 */
final class ilObjLearnplacesGUI extends ilObject2GUI
{
    public const DEFAULT_CMD = CommonControllerAction::CMD_INDEX;
    public const TAB_ID_PERMISSION = 'id_permissions';
    public const LP_SESSION_ID = 'xsrl_lp_session_state';

    /**
     * @var MapBlockService $mapBlockService
     */
    private $mapBlockService;
    /**
     * @var int $objectId
     */
    private $objectId;
    /**
     * @var ilTabsGUI $learnplaceTabs
     */
    private $learnplaceTabs;
    /**
     * @var AccessGuard $accessGuard
     */
    private $accessGuard;

    private ilLearnplacesPlugin $plugin;

    /**
     * ilObjLearnplacesGUI constructor.
     *
     * @param int|null  $a_ref_id
     * @param int       $a_id_type
     * @param int       $a_parent_node_id
     *
     * @see ilObjectPluginGUI for possible id types.
     */
    public function __construct($a_ref_id = 0, int $a_id_type = self::REPOSITORY_NODE_ID, int $a_parent_node_id = 0)
    {
        parent::__construct($a_ref_id, $a_id_type, $a_parent_node_id);
        $this->mapBlockService = PluginContainer::resolve(MapBlockService::class);
        $this->objectId = ilObject::_lookupObjectId($this->ref_id);
        $this->learnplaceTabs = PluginContainer::resolve('ilTabs');
        $this->accessGuard = PluginContainer::resolve(AccessGuard::class);
        $this->plugin = ilLearnplacesPlugin::getInstance();
    }

    public function txt(string $var): string
    {
        return $this->plugin->txt($var);
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return ilLearnplacesPlugin::PLUGIN_ID;
    }

    /**
     * Main Triage to following GUI-Classes
     */
    public function executeCommand(): void
    {
        $nextClass = $this->ctrl->getNextClass();

        /**
         * @var ilGlobalPageTemplate | ilTemplate $template
         */
        $template = PluginContainer::resolve('tpl');
        $template->setTitle(ilObject::_lookupTitle($this->objectId));
        $template->setDescription(ilObject::_lookupDescription($this->objectId));
        $template->setTitleIcon(/*ilObject::_getIcon($this->objectId)*/'Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/templates/images/icon_xsrl.svg');
        if (!$this->getCreationMode()) {
            $this->setLocator();
        }
        $properties = [];

        if (!ilObjLearnplacesAccess::checkOnline(intval($this->obj_id))) {
            $properties[] = [
                'property' => $this->txt('common_status'),
                'value' => $this->txt('common_offline'),
            ];
        }
        $template->setAlertProperties($properties);

        switch ($nextClass) {
            case strtolower(ilLearningProgressGUI::class):
                $user_id = $this->user->getId();
                if (!$this->access->checkAccess('write', "", $this->object->getRefId())) {
                    throw new Exception('Permission denied');
                }
                $this->renderTabs();
                $this->tabs_gui->setTabActive("learning_progress");
                $new_gui = new ilLearningProgressGUI(
                    ilLearningProgressBaseGUI::LP_CONTEXT_REPOSITORY,
                    $this->object->getRefId(),
                    $user_id
                );
                $this->ctrl->forwardCommand($new_gui);
                $this->tpl->printToStdout();
                break;

            case strtolower(ilInfoScreenGUI::class):
                if (!$this->access->checkAccess('visible', "", $this->object->getRefId())) {
                    throw new Exception('Permission denied');
                }
                $this->renderTabs();
                $info = new ilInfoScreenGUI($this);
                //$info->enablePrivateNotes();
                $this->ctrl->forwardCommand($info);
                $this->tpl->printToStdout();
                break;
            case "":
                if ($this->ctrl->getCmd() === 'streamVideo') {
                    $this->streamVideo();
                    break;
                }
            case strtolower(ilObjLearnplacesGUI::class):
                parent::executeCommand();
                break;
            case strtolower(xsrlContentGUI::class):
                $this->renderTabs();
                $this->setSubtabs();
                $this->learnplaceTabs->activateSubTab(xsrlContentGUI::TAB_ID);
                $this->ctrl->forwardCommand(PluginContainer::resolve(xsrlContentGUI::class));
                break;
            case strtolower(xsrlPictureUploadBlockGUI::class):
                $this->renderTabs();
                $this->setSubtabs();
                $this->learnplaceTabs->activateSubTab(xsrlContentGUI::TAB_ID);
                $this->ctrl->forwardCommand(PluginContainer::resolve(xsrlPictureUploadBlockGUI::class));
                break;
            case strtolower(xsrlPictureBlockGUI::class):
                $this->renderTabs();
                $this->setSubtabs();
                $this->learnplaceTabs->activateSubTab(xsrlContentGUI::TAB_ID);
                $this->ctrl->forwardCommand(PluginContainer::resolve(xsrlPictureBlockGUI::class));
                break;
            case strtolower(xsrlRichTextBlockGUI::class):
                $this->renderTabs();
                $this->setSubtabs();
                $this->learnplaceTabs->activateSubTab(xsrlContentGUI::TAB_ID);
                $this->ctrl->forwardCommand(PluginContainer::resolve(xsrlRichTextBlockGUI::class));
                break;
            case strtolower(xsrlIliasLinkBlockGUI::class):
                $this->renderTabs();
                $this->setSubtabs();
                $this->learnplaceTabs->activateSubTab(xsrlContentGUI::TAB_ID);
                $this->ctrl->forwardCommand(PluginContainer::resolve(xsrlIliasLinkBlockGUI::class));
                break;
            case strtolower(xsrlIliasLinkBlockEditFormViewGUI::class):
                //required for the ilLinkInputGUI ...
                $this->ctrl->forwardCommand(new xsrlIliasLinkBlockEditFormViewGUI(new ILIASLinkBlockModel()));
                break;
            case strtolower(xsrlMapBlockGUI::class):
                $this->renderTabs();
                $this->setSubtabs();
                $this->learnplaceTabs->activateTab(xsrlContentGUI::TAB_ID);
                $this->learnplaceTabs->activateSubTab(xsrlMapBlockGUI::TAB_ID);
                $this->ctrl->forwardCommand(PluginContainer::resolve(xsrlMapBlockGUI::class));
                break;
            case strtolower(xsrlVideoBlockGUI::class):
                $this->renderTabs();
                $this->setSubtabs();
                $this->learnplaceTabs->activateSubTab(xsrlContentGUI::TAB_ID);
                $this->ctrl->forwardCommand(PluginContainer::resolve(xsrlVideoBlockGUI::class));
                break;
            case strtolower(xsrlAccordionBlockGUI::class):
                $this->renderTabs();
                $this->setSubtabs();
                $this->learnplaceTabs->activateSubTab(xsrlContentGUI::TAB_ID);
                $this->ctrl->forwardCommand(PluginContainer::resolve(xsrlAccordionBlockGUI::class));
                break;
            case strtolower(xsrlSettingGUI::class):
                $this->renderTabs();
                $this->ctrl->forwardCommand(PluginContainer::resolve(xsrlSettingGUI::class));
                break;
            case strtolower(ilPermissionGUI::class):
                $this->renderTabs();
                $this->ctrl->forwardCommand(new ilPermissionGUI($this));
                $this->learnplaceTabs->activateTab(self::TAB_ID_PERMISSION);
                if ($template instanceof ilGlobalPageTemplate) {
                    $template->printToStdout();
                } else {
                    //$template->getStandardTemplate();
                    //$template->show();
                }
            case strtolower(xsrlVisitorsGUI::class):
                $this->renderTabs();
                $this->learnplaceTabs->activateTab(xsrlVisitorsGUI::TAB_ID);
                $this->ctrl->forwardCommand(PluginContainer::resolve(xsrlVisitorsGUI::class));
                break;
            default:
                $this->ctrl->redirectByClass(static::class, $this->getStandardCmd());
                break;
        }
    }

    public function performCommand(string $cmd): void
    {
        if ($this->accessGuard->hasReadPermission()) {
            switch ($cmd) {
                case CommonControllerAction::CMD_INDEX:
                    $this->index();
                    return;
            }
        }

        $this->ctrl->redirectByClass(ilRepositoryGUI::class, $this->getStandardCmd());
    }

    public function infoScreen(): void
    {
        $this->ctrl->redirectByClass(
            "ilinfoscreengui",
            "showSummary"
        );
    }

    /**
     * @return void
     * @throws ilCtrlException
     */
    protected function setSubtabs(): void
    {
        if ($this->accessGuard->hasWritePermission()) {
            $this->learnplaceTabs->addSubTab(xsrlContentGUI::TAB_ID, $this->lng->txt(xsrlContentGUI::TAB_ID), $this->ctrl->getLinkTargetByClass([ilObjPluginDispatchGUI::class, ilObjLearnplacesGUI::class, xsrlContentGUI::class], self::DEFAULT_CMD));
            $this->learnplaceTabs->addSubTab('sequence', $this->plugin->txt('content_change_sequence'), $this->ctrl->getLinkTargetByClass([ilObjPluginDispatchGUI::class, ilObjLearnplacesGUI::class, xsrlContentGUI::class], xsrlContentGUI::CMD_SEQUENCE_VIEW));

            if ($this->accessGuard->hasWritePermission() && !$this->hasMap()) {
                $this->learnplaceTabs->addSubTab(xsrlMapBlockGUI::TAB_ID, $this->plugin->txt('tabs_map'), $this->ctrl->getLinkTargetByClass([ilObjPluginDispatchGUI::class, ilObjLearnplacesGUI::class, xsrlMapBlockGUI::class], CommonControllerAction::CMD_ADD));
            } elseif ($this->hasMap()) {
                $this->learnplaceTabs->addSubTab(xsrlMapBlockGUI::TAB_ID, $this->plugin->txt('tabs_map'), $this->ctrl->getLinkTargetByClass([ilObjPluginDispatchGUI::class, ilObjLearnplacesGUI::class, xsrlMapBlockGUI::class], self::DEFAULT_CMD));
            }
        }
    }

    /**
     * This command will be executed after a new repository object was created.
     *
     * @return string
     */
    public function getAfterCreationCmd(): string
    {
        return self::DEFAULT_CMD;
    }

    /**
     * This command will be executed if no command was supplied.
     *
     * @return string
     */
    public function getStandardCmd(): string
    {
        return self::DEFAULT_CMD;
    }

    /**
     * @inheritdoc
     */
    protected function supportsCloning(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function supportsExport(): bool
    {
        return false;
    }

    /**
     * @return void
     * @throws ilCtrlException
     */
    public function index(): void
    {
        $this->ctrl->redirectByClass(xsrlContentGUI::class, self::DEFAULT_CMD);
    }

    public function addInfoTab(): void
    {
        $ilAccess = $this->access;
        $ilTabs = $this->learnplaceTabs;

        // info screen
        if ($ilAccess->checkAccess('visible', "", $this->object->getRefId())) {
            $ilTabs->addTarget(
                "info_short",
                $this->ctrl->getLinkTargetByClass(
                    "ilinfoscreengui",
                    "showSummary"
                ),
                "showSummary"
            );
        }
    }

    /**
     * @return void
     * @throws ilCtrlException
     */
    private function renderTabs(): void
    {
        $this->learnplaceTabs->addTab(xsrlContentGUI::TAB_ID, $this->plugin->txt('tabs_content'), $this->ctrl->getLinkTargetByClass([ilObjPluginDispatchGUI::class, ilObjLearnplacesGUI::class, xsrlContentGUI::class], self::DEFAULT_CMD));
        if ($this->accessGuard->hasWritePermission()) {
            $this->learnplaceTabs->addTab(xsrlSettingGUI::TAB_ID, $this->plugin->txt('tabs_settings'), $this->ctrl->getLinkTargetByClass([ilObjPluginDispatchGUI::class, ilObjLearnplacesGUI::class, xsrlSettingGUI::class], CommonControllerAction::CMD_EDIT));
            $this->learnplaceTabs->addTab(xsrlVisitorsGUI::TAB_ID, $this->plugin->txt('tabs_visitor'), $this->ctrl->getLinkTargetByClass([ilObjPluginDispatchGUI::class, ilObjLearnplacesGUI::class, xsrlVisitorsGUI::class], CommonControllerAction::CMD_INDEX));

            if (ilLearningProgressAccess::checkAccess($this->object->getRefId())) {
                $this->tabs_gui->addTarget(
                    'learning_progress',
                    $this->ctrl->getLinkTargetByClass([ilObjPluginDispatchGUI::class, self::class, ilLearningProgressGUI::class], ''),
                    '',
                    array('illplistofobjectsgui', 'illplistofsettingsgui', 'illearningprogressgui', 'illplistofprogressgui')
                );
            }
        }

        $this->addInfoTab();

        parent::setTabs();

        //add an empty tab to prevent ilias from hiding the entire tab bar if only one tab exists.
        $this->learnplaceTabs->addTab('', '', '#');
    }

    /**
     * @return bool
     */
    private function hasMap(): bool
    {
        if (is_null($this->ref_id)) {
            return false;
        }

        try {
            $map = $this->mapBlockService->findByObjectId(ilObject::_lookupObjectId($this->ref_id));
            return $this->accessGuard->isValidBlockReference($map->getId());
        } catch (InvalidArgumentException $ex) {
            return false;
        }
    }

    /**
     * @param array $a_target
     * @return void
     * @throws ilCtrlException
     */
    public static function _goto(array $a_target): void
    {
        global $DIC;

        if (!isset($a_target[0])) {
            return;
        }

        try {
            [$cmd, $state] = explode('_', $a_target[0]);
        } catch (Exception $ex) {
            $DIC->ctrl()->setParameterByClass(xsrlContentGUI::class, 'ref_id', $a_target[0]);
            $DIC->ctrl()->redirectByClass([ilObjPluginDispatchGUI::class, ilObjLearnplacesGUI::class, xsrlContentGUI::class], 'index');
            $DIC->ctrl()->clearParametersByClass(xsrlContentGUI::class);
            return;
        }

        $logger = ilLoggerFactory::getLogger('Learnplaces');
        $logger->info('goto: redirect from ilObjLearnplacesGUI to xsrlAuthGUI');

        $DIC->ctrl()->setParameterByClass(xsrlAuthGUI::class, 'state', $state);
        $DIC->ctrl()->redirectByClass([ilUIPluginRouterGUI::class, xsrlAuthGUI::class], xsrlAuthGUI::CMD_AUTH);
        $DIC->ctrl()->clearParametersByClass(xsrlAuthGUI::class);
    }

    /**
     * @return void
     * @throws arException
     */
    private function streamVideo(): void
    {
        global $DIC;
        $query = $DIC->http()->wrapper()->query();
        $int = $DIC->refinery()->kindlyTo()->int();
        if (!$query->has('block_id')) {
            return;
        }
        $block_id = $query->retrieve('block_id', $int);
        $rid = VideoBlock::where(['fk_block_id' => $block_id])->first()->getResourceId();

        if ($identification = $DIC->resourceStorage()->manage()->find($rid)) {
            $file_path = $DIC->resourceStorage()->consume()->stream($identification)->getStream()->getMetaData('uri');
            $stream = new \KPG\Learnplaces\util\VideoStream($file_path);
            $stream->start();
        }
    }
}
