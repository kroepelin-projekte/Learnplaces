<?php

declare(strict_types=1);

use KPG\Learnplaces\service\publicapi\block\LearnplaceService;
use KPG\Learnplaces\service\security\AccessGuard;
use ILIAS\DI\UIServices;
use KPG\Learnplaces\gui\helper\CommonControllerAction;
use KPG\Learnplaces\gui\VisitorsTable;
use KPG\Learnplaces\container\PluginContainer;

class VisitorsGUI
{

    public const TAB_ID = 'participant';
    /**
     * Command to store the sequence numbers
     */

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
        $table = new VisitorsTable($this->plugin,[]);

        global $DIC;
        $refinery = PluginContainer::resolve('refinery');
        $query = PluginContainer::resolve('query');

        if (!$query->has('ref_id')) {
            throw new \Exception('Learnplaces - getToken(): ref_id is missing');
        }

        $obj_id = \ilObject::_lookupObjectId($query->retrieve('ref_id', $refinery->kindlyTo()->int()));

        $visitors = [];
        foreach ( $this->learnplaceService->findByObjectId($obj_id)->getVisitJournals() as $visitJournal) {
            $visitors[] = [
              'full_name' => ilObjUser::_lookupFullname( $visitJournal->getUserId()),
              "login" => ilObjUser::_lookupLogin($visitJournal->getUserId()),
              'visited_at' => $visitJournal->getTime()->format('d.m.Y H:i'),
            ];
        }
        $table->setTableData($visitors);

        $final_table = $table->getTableForRepresentation();
        $this->template->setContent($this->ui->renderer()->render($final_table->withRequest($DIC->http()->request())));
    }
}