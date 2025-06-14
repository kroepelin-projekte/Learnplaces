<?php

declare(strict_types=1);

use KPG\Learnplaces\service\publicapi\block\LearnplaceService;
use KPG\Learnplaces\service\security\AccessGuard;
use ILIAS\DI\UIServices;
use KPG\Learnplaces\gui\helper\CommonControllerAction;
use KPG\Learnplaces\gui\VisitorsTable;
use KPG\Learnplaces\container\PluginContainer;

class xsrlVisitorsGUI
{
    public const TAB_ID = 'participant';

    /**
     * @var ilTabsGUI $tabs
     */
    private $tabs;
    /**
     * @var ilGlobalPageTemplate $template
     */
    private ilGlobalPageTemplate $template;
    /**
     * @var ilCtrl $controlFlow
     */
    private ilCtrl $controlFlow;
    /**
     * @var ilLearnplacesPlugin $plugin
     */
    private ilLearnplacesPlugin $plugin;

    /**
     * @var AccessGuard $accessGuard
     */
    private AccessGuard $accessGuard;

    private UIServices $ui;
    private $learnplaceService;

    public function __construct(
        ilTabsGUI $tabs,
        $template,
        UIServices $ui,
        ilLearnplacesPlugin $plugin,
        LearnplaceService $learnplaceService,
        AccessGuard $accessGuard,
        ilCtrl $controlFlow
    ) {
        $this->controlFlow = $controlFlow;
        $this->tabs = $tabs;
        $this->template = $template;
        $this->ui = $ui;
        $this->plugin = $plugin;
        $this->accessGuard = $accessGuard;
        $this->learnplaceService = $learnplaceService;
    }

    /**
     * @return bool
     * @throws ilCtrlException
     * @throws ilTemplateException
     */
    public function executeCommand(): bool
    {
        $cmd = $this->controlFlow->getCmd(CommonControllerAction::CMD_INDEX);
        $this->tabs->activateTab(self::TAB_ID);

        switch ($cmd) {
            case CommonControllerAction::CMD_INDEX:
                if ($this->accessGuard->hasWritePermission()) {
                    $this->index();
                    $this->template->printToStdout();
                }
                break;
            case CommonControllerAction::CMD_DELETE:
                if ($this->accessGuard->hasWritePermission()) {
                    $this->delete();
                }
                break;
        }
        return false;
    }

    /**
     * actions
     *
     * @return void
     * @throws ilCtrlException
     * @throws ilTemplateException|Exception
     */
    private function index(): void
    {
        $table = new VisitorsTable($this->plugin, []);

        global $DIC;
        $refinery = PluginContainer::resolve('refinery');
        $query = PluginContainer::resolve('query');

        if (!$query->has('ref_id')) {
            throw new \Exception('Learnplaces - getToken(): ref_id is missing');
        }

        $obj_id = \ilObject::_lookupObjectId($query->retrieve('ref_id', $refinery->kindlyTo()->int()));

        $visitors = [];
        foreach ($this->learnplaceService->findByObjectId($obj_id)->getVisitJournals() as $visitJournal) {
            $visitors[] = [
                'full_name' => ilObjUser::_lookupFullname($visitJournal->getUserId()),
                "login" => ilObjUser::_lookupLogin($visitJournal->getUserId()),
                'visited_at' => $visitJournal->getTime()->format('d.m.Y H:i'),
            ];
        }
        $table->setTableData($visitors);

        $final_table = $table->getTableForRepresentation();

        $delete_modal = $this->ui->factory()->modal()->interruptive(
            $this->plugin->txt('lang_delete_all_visitors'),
            "Möchten Sie alle Besucher löschen? Nach der Löschung können die Besucher nicht mehr wiederhergestellt werden.",
            $DIC->ctrl()->getFormActionByClass(
                \xsrlVisitorsGUI::class,
                CommonControllerAction::CMD_DELETE
            ),
        )->withActionButtonLabel($this->plugin->txt('lang_delete_visitors'))
                                 ->withAffectedItems([
                                     $this->ui->factory()->modal()->interruptiveItem()->standard(
                                         "",
                                         ''
                                     ),
                                 ]);

        $action_delete = $DIC->ctrl()->getLinkTargetByClass(
            xsrlVisitorsGUI::class,
            CommonControllerAction::CMD_DELETE
        );
        $delete_button = $this->ui->factory()->button()->standard($this->plugin->txt('lang_delete_all_visitors'), $action_delete)->withOnClick(
            $delete_modal->getShowSignal()
        );

        $this->template->setContent(
            $this->ui->renderer()->render(
                [$delete_modal, $delete_button, $final_table->withRequest($DIC->http()->request())]
            )
        );
    }

    public function delete(): void
    {
        global $DIC;
        $database = $DIC->database();
        $refinery = PluginContainer::resolve('refinery');
        $query = PluginContainer::resolve('query');

        if (!$query->has('ref_id')) {
            throw new \Exception('Learnplaces - getToken(): ref_id is missing');
        }

        $learnplace_object = $this->learnplaceService->findByObjectId(ilObject::_lookupObjectId($query->retrieve('ref_id', $refinery->kindlyTo()->int())));

        $database->manipulate("DELETE FROM xsrl_visit_journal WHERE fk_learnplace_id = " . $database->quote($learnplace_object->getId(), 'integer'));

        $DIC->ui()->mainTemplate()->setOnScreenMessage('success', 'Besucher wurden gelöscht', true);
        $DIC->ctrl()->redirectByClass(xsrlVisitorsGUI::class, CommonControllerAction::CMD_INDEX);
    }
}