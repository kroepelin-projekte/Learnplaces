<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use SRAG\Learnplaces\gui\block\AccordionBlock\AccordionBlockEditFormView;
use SRAG\Learnplaces\gui\block\util\BlockIdReferenceValidationAware;
use SRAG\Learnplaces\gui\block\util\InsertPositionAware;
use SRAG\Learnplaces\gui\block\util\ReferenceIdAware;
use SRAG\Learnplaces\gui\component\PlusView;
use SRAG\Learnplaces\gui\exception\ValidationException;
use SRAG\Learnplaces\gui\helper\CommonControllerAction;
use SRAG\Learnplaces\service\publicapi\block\AccordionBlockService;
use SRAG\Learnplaces\service\publicapi\block\ConfigurationService;
use SRAG\Learnplaces\service\publicapi\block\LearnplaceService;
use SRAG\Learnplaces\service\publicapi\model\AccordionBlockModel;
use SRAG\Learnplaces\service\security\AccessGuard;

/**
 * Class xsrlAccordionBlock
 *
 * @package SRAG\Learnplaces\gui\block\AccordionBlock
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class xsrlAccordionBlockGUI
{
    use InsertPositionAware;
    use BlockIdReferenceValidationAware;
    use ReferenceIdAware;

    public const TAB_ID = 'content';
    public const BLOCK_ID_QUERY_KEY = 'block';

    /**
     * @var ilTabsGUI $tabs
     */
    private $tabs;
    /**
     * @var ilGlobalPageTemplate | ilTemplate $template
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
     * @var AccordionBlockService $accordionService
     */
    private $accordionService;
    /**
     * @var LearnplaceService $learnplaceService
     */
    private $learnplaceService;
    /**
     * @var ConfigurationService $configService
     */
    private $configService;
    /**
     * @var ServerRequestInterface $request
     */
    private $request;
    /**
     * @var AccessGuard $blockAccessGuard
     */
    private $blockAccessGuard;

    /**
     * xsrlAccordionBlockGUI constructor.
     *
     * @param ilTabsGUI $tabs
     * @param ilGlobalPageTemplate | ilTemplate $template
     * @param ilCtrl $controlFlow
     * @param ilLearnplacesPlugin $plugin
     * @param AccordionBlockService $accordionService
     * @param LearnplaceService $learnplaceService
     * @param ConfigurationService $configService
     * @param ServerRequestInterface $request
     * @param AccessGuard $blockAccessGuard
     */
    public function __construct(ilTabsGUI $tabs, $template, ilCtrl $controlFlow, ilLearnplacesPlugin $plugin, AccordionBlockService $accordionService, LearnplaceService $learnplaceService, ConfigurationService $configService, ServerRequestInterface $request, AccessGuard $blockAccessGuard)
    {
        $this->tabs = $tabs;
        $this->template = $template;
        $this->controlFlow = $controlFlow;
        $this->plugin = $plugin;
        $this->accordionService = $accordionService;
        $this->learnplaceService = $learnplaceService;
        $this->configService = $configService;
        $this->request = $request;
        $this->blockAccessGuard = $blockAccessGuard;
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
            case CommonControllerAction::CMD_ADD:
            case CommonControllerAction::CMD_CANCEL:
            case CommonControllerAction::CMD_CONFIRM:
            case CommonControllerAction::CMD_CREATE:
            case CommonControllerAction::CMD_DELETE:
            case CommonControllerAction::CMD_EDIT:
            case CommonControllerAction::CMD_UPDATE:
                if ($this->blockAccessGuard->hasWritePermission()) {
                    $this->{$cmd}();
                    if ($this->template instanceof ilGlobalPageTemplate) {
                        $this->template->printToStdout();
                    } else {
                        $this->template->show();
                    }
                    return true;
                }
                break;
        }

        $this->template->setOnScreenMessage('failure', $this->plugin->txt('common_access_denied'), true);
        $this->controlFlow->redirectByClass(ilRepositoryGUI::class);

        return false;
    }

    /**
     * @return void
     * @throws ilCtrlException
     */
    private function add(): void
    {
        $this->controlFlow->saveParameter($this, PlusView::POSITION_QUERY_PARAM);

        $config = $this->configService->findByObjectId(ilObject::_lookupObjectId($this->getCurrentRefId()));
        $block = new AccordionBlockModel();

        $block->setVisibility($config->getDefaultVisibility());
        $form = new AccordionBlockEditFormView($block);
        $this->template->setContent($form->getHTML());
    }

    /**
     * @return void
     * @throws ilCtrlException
     */
    private function create(): void
    {
        $form = new AccordionBlockEditFormView(new AccordionBlockModel());
        try {
            $queries = $this->request->getQueryParams();

            //store block
            /**
             * @var AccordionBlockModel $block
             */
            $block = $form->getBlockModel();
            $block->setId(0); //mitigate injection of block id
            $block = $this->accordionService->store($block);

            //fetch learnplace
            $learnplace = $this->learnplaceService->findByObjectId(ilObject::_lookupObjectId($this->getCurrentRefId()));

            //store relation learnplace <-> block
            $blocks = $learnplace->getBlocks();
            array_splice($blocks, $this->getInsertPosition($queries), 0, [$block]);
            $learnplace->setBlocks($blocks);
            $this->learnplaceService->store($learnplace);

            $this->template->setOnScreenMessage('success', $this->plugin->txt('message_changes_save_success'), true);
            $anchor = xsrlContentGUI::ANCHOR_TEMPLATE . $block->getSequence();
            $this->controlFlow->redirectByClass(xsrlContentGUI::class, CommonControllerAction::CMD_INDEX, $anchor);
        } catch (ValidationException $ex) {
            $form->setValuesByPost();
            $this->template->setContent($form->getHTML());
        } catch (LogicException $ex) {
            $form->setValuesByPost();
            $this->template->setContent($form->getHTML());
        }
    }

    /**
     * @return void
     */
    private function edit(): void
    {
        $block = $this->accordionService->find($this->getBlockId());
        $form = new AccordionBlockEditFormView($block);
        $this->template->setContent($form->getHTML());
    }

    /**
     * @return void
     * @throws ilCtrlException
     */
    private function update(): void
    {
        $form = new AccordionBlockEditFormView(new AccordionBlockModel());
        try {
            //store block
            /**
             * @var AccordionBlockModel $block
             */
            $block = $form->getBlockModel();
            $this->redirectInvalidRequests($block->getId());
            $accordion = $this->accordionService->find($block->getId());
            $accordion->setTitle($block->getTitle());
            $accordion->setExpand($block->isExpand());
            $accordion->setVisibility($block->getVisibility());
            $this->accordionService->store($accordion);

            $this->template->setOnScreenMessage('success', $this->plugin->txt('message_changes_save_success'), true);
            $anchor = xsrlContentGUI::ANCHOR_TEMPLATE . $block->getSequence();
            $this->controlFlow->redirectByClass(xsrlContentGUI::class, CommonControllerAction::CMD_INDEX, $anchor);
        } catch (ValidationException $ex) {
            $form->setValuesByPost();
            $this->template->setContent($form->getHTML());
        }
    }

    /**
     * @return void
     * @throws ilCtrlException
     */
    private function delete(): void
    {
        $queries = $this->request->getQueryParams();
        $blockId = intval($queries[self::BLOCK_ID_QUERY_KEY]);
        $this->redirectInvalidRequests($blockId);
        $this->accordionService->delete($blockId);
        $this->regenerateSequence();
        $this->template->setOnScreenMessage('success', $this->plugin->txt('message_delete_success'), true);
        $this->controlFlow->redirectByClass(xsrlContentGUI::class, CommonControllerAction::CMD_INDEX);
    }

    /**
     * @return void
     * @throws ilCtrlException
     */
    private function cancel(): void
    {
        $this->controlFlow->redirectByClass(xsrlContentGUI::class, CommonControllerAction::CMD_INDEX);
    }

    /**
     * @return int
     */
    private function getBlockId(): int
    {
        $queries = $this->request->getQueryParams();
        return intval($queries[self::BLOCK_ID_QUERY_KEY]);
    }

    /**
     * @return void
     */
    private function regenerateSequence(): void
    {
        $learnplace = $this->learnplaceService->findByObjectId(ilObject::_lookupObjectId($this->getCurrentRefId()));
        $this->learnplaceService->store($learnplace);
    }
}