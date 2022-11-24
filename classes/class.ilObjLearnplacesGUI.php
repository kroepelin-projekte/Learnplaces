<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../vendor/autoload.php';

use SRAG\Learnplaces\container\PluginContainer;
use SRAG\Learnplaces\gui\helper\CommonControllerAction;
use SRAG\Learnplaces\service\publicapi\block\LearnplaceService;
use SRAG\Learnplaces\service\publicapi\block\MapBlockService;
use SRAG\Learnplaces\service\publicapi\model\ILIASLinkBlockModel;
use SRAG\Learnplaces\service\publicapi\model\MapBlockModel;
use SRAG\Learnplaces\service\security\AccessGuard;
use SRAG\Learnplaces\service\visibility\LearnplaceServiceDecoratorFactory;
use fluxlabs\learnplaces\Adapters\Config\AbstractGroupReadableLearnplacesByCourses;

/**
 * Class ilObjLearnplacesGUI
 * @author            Nicolas Schäfli <ns@studer-raimann.ch>
 * @ilCtrl_isCalledBy ilObjLearnplacesGUI: ilRepositoryGUI, ilObjPluginDispatchGUI
 * @ilCtrl_isCalledBy ilObjLearnplacesGUI: ilAdministrationGUI
 * @ilCtrl_Calls      ilObjLearnplacesGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI
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
 */
final class ilObjLearnplacesGUI extends ilObjectPluginGUI
{

    const DEFAULT_CMD = CommonControllerAction::CMD_INDEX;

    const TAB_ID_PERMISSION = 'id_permissions';
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
    /**
     * @var ilObjUser $currentUser
     */
    private $currentUser;

    /**
     * ilObjLearnplacesGUI constructor.
     * @param int|null $a_ref_id
     * @param int      $a_id_type
     * @param int      $a_parent_node_id
     * @see ilObjectPluginGUI for possible id types.
     */
    public function __construct($a_ref_id = 0, int $a_id_type = self::REPOSITORY_NODE_ID, int $a_parent_node_id = 0)
    {
        parent::__construct($a_ref_id, $a_id_type, $a_parent_node_id);
        $this->mapBlockService = PluginContainer::resolve(MapBlockService::class);
        $this->objectId = intval(ilObject::_lookupObjectId($this->ref_id));
        $this->learnplaceTabs = PluginContainer::resolve('ilTabs');
        $this->accessGuard = PluginContainer::resolve(AccessGuard::class);
        $this->currentUser = PluginContainer::resolve('ilUser');
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return ilLearnplacesPlugin::PLUGIN_ID;
    }

    /**
     * Main Triage to following GUI-Classes
     */
    public function executeCommand()
    {
        //todo replace with psr4 loader
        require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/src/Core/Ports/Outbounds.php';

        require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/src/Adapters/Api/AsyncApi.php';
        require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/src/Adapters/Api/IdValue.php';
        require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/src/Adapters/Api/IdValueList.php';
        require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/src/Adapters/Api/StatusEnum.php';

        require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/src/Adapters/Storage/DatabaseConfig.php';
        require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/src/Adapters/Storage/CourseRepository.php';
        require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/src/Adapters/Storage/LearnplaceRepository.php';
        require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/src/Adapters/Storage/LocationRepository.php';

        require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/src/Core/Domain/Models/IliasObject.php';
        require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/src/Core/Domain/Models/Course.php';
        require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/src/Core/Domain/Models/User.php';
        require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/src/Core/Domain/Models/Learnplace.php';
        require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/src/Core/Domain/Models/Location.php';

        require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/src/Core/Ports/Service.php';

        require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/src/Adapters/Config/Config.php';
        require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/src/Adapters/Config/AbstractGroupReadableLearnplacesByCourses.php';
        require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/src/Adapters/Config/AbstractHasAccessToCourse.php';
        require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/src/Adapters/Config/AbstractCurrentUser.php';
        require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/src/Adapters/Config/OutboundsAdapter.php';

        if ($_GET['ref_id'] == 1) {


            global $DIC;
            $ilDB = $DIC->database();
            $usrId = $this->currentUser->getId();
            $currentUser = $this->currentUser;

            $outboundsAdapter = fluxlabs\learnplaces\Adapters\Config\OutboundsAdapter::new(
                fluxlabs\learnplaces\Adapters\Config\Config::new(
                    ILIAS_HTTP_PATH . "/" . $this->ctrl->getLinkTarget($this) . "&cmd=api-request&api=",
                    $ilDB->getHost(),
                    $ilDB->getDbName(),
                    $ilDB->getUserName(),
                    $ilDB->getPassword(),
                    new class($usrId) extends AbstractGroupReadableLearnplacesByCourses {

                        private int $usrId;

                        public function __construct(int $usrId)
                        {
                            $this->usrId = $usrId;
                        }

                        public function groupReadableLearnplacesByCourses(array $ref_ids) : array
                        {
                            return ilObjLearnplacesAccess::fnGroupReadableLearnplacesByCourses($this->usrId)($ref_ids);
                        }
                    },
                    new class($usrId) extends \fluxlabs\learnplaces\Adapters\Config\AbstractHasAccessToCourse {
                        private int $usrId;

                        public function __construct(int $usrId)
                        {
                            $this->usrId = $usrId;
                        }

                        public function hasAccessToCourse(int $ref_id) : bool
                        {
                            return ilObjLearnplacesAccess::fnHasAccessToCourse($this->usrId)($ref_id);
                        }
                    },
                    new class($currentUser) extends \fluxlabs\learnplaces\Adapters\Config\AbstractCurrentUser {
                        private $currentUser;

                        public function __construct($currentUser)
                        {
                            $this->currentUser = $currentUser;
                        }

                        public function getCurrentUser() : \fluxlabs\learnplaces\Core\Domain\Models\User
                        {
                            return \fluxlabs\learnplaces\Core\Domain\Models\User::new(
                                $this->currentUser->getId(),
                                $this->currentUser->getFirstname(),
                                $this->currentUser->getLastName(),
                                $this->currentUser->getEmail(),
                            );
                        }
                    },
                )
            );

            //http://127.3.3.3/ilias.php?ref_id=1&cmdClass=ilobjlearnplacesgui&cmdNode=p6:o6&baseClass=ilobjplugindispatchgui&cmd=api-request&api=
            if ($this->ctrl->getCmd() === "api-request") {

                $apiRequest = $_GET['api'];

                $newAsyncApi = function () use ($outboundsAdapter) : fluxlabs\learnplaces\Adapters\Api\AsyncApi {
                    return fluxlabs\learnplaces\Adapters\Api\AsyncApi::new(
                        $outboundsAdapter
                    );
                };
                $getContext = function ($apiRequest) : string {
                    $exploded = explode('/', $apiRequest);
                    return $exploded[0];
                };
                $getIdType = function ($apiRequest, string $type) {
                    $exploded = explode('/' . $type . '/', $apiRequest);
                    if (count($exploded) == 2) {
                        if (stristr('/', $exploded[1])) {
                            return explode('/', $exploded[1])[0];
                        }
                        return $exploded[1];
                    }
                    return null;
                };

                $getId = function ($apiRequest) use ($getIdType) {
                    $id = $getIdType($apiRequest, 'refId');
                    if ($id) {
                        return $id;
                    }

                    return $getIdType($apiRequest, 'parentRefId');
                };

                $handleProjection = function (string $apiRequest) use (
                    $getContext,
                    $getId,
                    $newAsyncApi
                ) : void {
                    switch (true) {
                        case stristr($apiRequest, 'projectIdValueList'):
                            $newAsyncApi()->projectIdValueList($getContext($apiRequest), $getId($apiRequest));
                            break;
                        case stristr($apiRequest, 'projectObject'):
                            $newAsyncApi()->projectObject($getContext($apiRequest), $getId($apiRequest));
                            break;
                    }
                };
                $handleProjection($apiRequest);
                exit;
            }

            //http://127.3.3.3/goto.php?target=xsrl_1&client_id=default
            fluxlabs\learnplaces\Adapters\Api\AsyncApi::new(
                $outboundsAdapter
            )->createApiBaseUrl();
            exit;
        }

        $nextClass = $this->ctrl->getNextClass();

        /**
         * @var ilGlobalPageTemplate | ilTemplate $template
         */
        $template = PluginContainer::resolve('tpl');
        $template->setTitle(ilObject::_lookupTitle($this->objectId));
        $template->setDescription(ilObject::_lookupDescription($this->objectId));
        $template->setTitleIcon(ilObject::_getIcon($this->objectId));
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
            case "":
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
                $this->learnplaceTabs->activateTab(self::TAB_ID_PERMISSION);
                $this->ctrl->forwardCommand(new ilPermissionGUI($this));
                if ($template instanceof ilGlobalPageTemplate) {
                    $template->printToStdout();
                } else {
                    $template->getStandardTemplate();
                    $template->show();
                }
                break;
            default:
                $this->ctrl->redirectByClass(static::class, $this->getStandardCmd());
                break;
        }
    }

    protected function performCommand(string $command)
    {
        if ($this->accessGuard->hasReadPermission()) {
            switch ($command) {
                case CommonControllerAction::CMD_INDEX:
                    $this->index();
                    return;
            }
        }

        $this->ctrl->redirectByClass(ilRepositoryGUI::class, $this->getStandardCmd());
    }

    /**
     *
     */
    protected function setSubtabs()
    {
        if ($this->accessGuard->hasWritePermission()) {
            $this->learnplaceTabs->addSubTab(xsrlContentGUI::TAB_ID, $this->lng->txt(xsrlContentGUI::TAB_ID),
                $this->ctrl->getLinkTarget($this));

            if ($this->accessGuard->hasWritePermission() && !$this->hasMap()) {
                $this->learnplaceTabs->addSubTab(xsrlMapBlockGUI::TAB_ID, $this->plugin->txt('tabs_map'),
                    $this->ctrl->getLinkTargetByClass(xsrlMapBlockGUI::class, CommonControllerAction::CMD_ADD));
            } else {
                if ($this->hasMap()) {
                    $this->learnplaceTabs->addSubTab(xsrlMapBlockGUI::TAB_ID, $this->plugin->txt('tabs_map'),
                        $this->ctrl->getLinkTargetByClass(xsrlMapBlockGUI::class, self::DEFAULT_CMD));
                }
            }

//            $this->learnplaceTabs->activateSubTab(self::SUBTAB_CONTENT);
        }
    }

    /**
     * This command will be executed after a new repository object was created.
     * @return string
     */
    public function getAfterCreationCmd()
    {
        return self::DEFAULT_CMD;
    }

    /**
     * This command will be executed if no command was supplied.
     * @return string
     */
    public function getStandardCmd()
    {
        return self::DEFAULT_CMD;
    }

    /**
     * @inheritdoc
     */
    protected function supportsCloning()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function supportsExport()
    {
        return false;
    }

    public function index()
    {
        $this->ctrl->redirectByClass(xsrlContentGUI::class, self::DEFAULT_CMD);
    }

    private function renderTabs()
    {
        $this->learnplaceTabs->addTab(xsrlContentGUI::TAB_ID, $this->plugin->txt('tabs_content'),
            $this->ctrl->getLinkTargetByClass(xsrlContentGUI::class, self::DEFAULT_CMD));
        if ($this->accessGuard->hasWritePermission()) {
            $this->learnplaceTabs->addTab(xsrlSettingGUI::TAB_ID, $this->plugin->txt('tabs_settings'),
                $this->ctrl->getLinkTargetByClass(xsrlSettingGUI::class, CommonControllerAction::CMD_EDIT));
        }
        parent::setTabs();

        //add an empty tab to prevent ilias from hiding the entire tab bar if only one tab exists.
        $this->learnplaceTabs->addTab('', '', '#');
    }

    private function hasMap() : bool
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
}