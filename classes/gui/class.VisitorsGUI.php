<?php

declare(strict_types=1);

use KPG\Learnplaces\gui\block\RenderableBlockViewFactory;
use KPG\Learnplaces\service\publicapi\block\LearnplaceService;
use KPG\Learnplaces\service\publicapi\block\AccordionBlockService;
use KPG\Learnplaces\service\visibility\LearnplaceServiceDecoratorFactory;
use KPG\Learnplaces\gui\block\BlockAddFormGUI;
use Psr\Http\Message\ServerRequestInterface;
use KPG\Learnplaces\service\security\AccessGuard;
use ILIAS\Refinery;
use ILIAS\DI\UIServices;
use KPG\Learnplaces\gui\helper\CommonControllerAction;
use KPG\Learnplaces\gui\VisitorsTable;

class VisitorsGUI
{

    public const TAB_ID = 'participant';
    /**
     * Command to store the sequence numbers
     */
    public const CMD_SEQUENCE = 'cmd_participant';
    private const CMD_SEQUENCE_FORM = 'participantForm';
    public const CMD_SEQUENCE_VIEW = 'participantView';

    /**
     * @var ilTabsGUI $tabs
     */
    private $tabs;
    /**
     * @var ilGlobalPageTemplate $template
     */
    private $template;
    /**
     * @var ilCtrl $controlFlow
     */
    private $controlFlow;
    /**
     * @var ilLearnplacesPlugin $plugin
     */
    private $plugin;

    /**
     * @var AccessGuard $accessGuard
     */
    private $accessGuard;

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
     * @throws ilTemplateException
     */
    private function index(): void
    {
        $table = new VisitorsTable($this->plugin,[]);
        $final_table = $table->getTableForRepresentation();
        global $DIC;

        $this->template->setContent($this->ui->renderer()->render($final_table->withRequest($DIC->http()->request())));
    }
}