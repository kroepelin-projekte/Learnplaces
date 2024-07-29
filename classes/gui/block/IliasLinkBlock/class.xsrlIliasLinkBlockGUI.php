<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use SRAG\Learnplaces\gui\block\util\AccordionAware;
use SRAG\Learnplaces\gui\block\util\BlockIdReferenceValidationAware;
use SRAG\Learnplaces\gui\block\util\InsertPositionAware;
use SRAG\Learnplaces\gui\block\util\ReferenceIdAware;
use SRAG\Learnplaces\gui\component\PlusView;
use SRAG\Learnplaces\gui\exception\ValidationException;
use SRAG\Learnplaces\gui\helper\CommonControllerAction;
use SRAG\Learnplaces\service\publicapi\block\AccordionBlockService;
use SRAG\Learnplaces\service\publicapi\block\ConfigurationService;
use SRAG\Learnplaces\service\publicapi\block\ILIASLinkBlockService;
use SRAG\Learnplaces\service\publicapi\block\LearnplaceService;
use SRAG\Learnplaces\service\publicapi\model\ILIASLinkBlockModel;
use SRAG\Learnplaces\service\security\AccessGuard;

/**
 * Class xsrlIliasLinkBlock
 *
 * @package SRAG\Learnplaces\gui\block\IliasLinkBlock
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 *
 * @ilCtrl_Calls           xsrlIliasLinkBlockGUI: xsrlIliasLinkBlockEditFormViewGUI
 * @ilCtrl_isCalledBy      xsrlIliasLinkBlockGUI: ilInternalLinkGUI
 */
final class xsrlIliasLinkBlockGUI
{
    use InsertPositionAware;
    use AccordionAware;
    use BlockIdReferenceValidationAware;
    use ReferenceIdAware;

    public const TAB_ID = 'content';
    public const BLOCK_ID_QUERY_KEY = 'block';
    public const ANCHOR_TEMPLATE = 'sequence-';

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
     * @var ILIASLinkBlockService $iliasLinkService
     */
    private $iliasLinkService;
    /**
     * @var LearnplaceService $learnplaceService
     */
    private $learnplaceService;
    /**
     * @var ConfigurationService $configService
     */
    private $configService;
    /**
     * @var AccordionBlockService $accprdionService
     */
    private $accprdionService;
    /**
     * @var ServerRequestInterface $request
     */
    private $request;
    /**
     * @var AccessGuard $blockAccessGuard
     */
    private $blockAccessGuard;


    /**
     * xsrlIliasLinkBlockGUI constructor.
     *
     * @param ilTabsGUI $tabs
     * @param ilGlobalPageTemplate | ilTemplate $template
     * @param ilCtrl $controlFlow
     * @param ilLearnplacesPlugin $plugin
     * @param ILIASLinkBlockService $iliasLinkService
     * @param LearnplaceService $learnplaceService
     * @param ConfigurationService $configService
     * @param AccordionBlockService $accprdionService
     * @param ServerRequestInterface $request
     * @param AccessGuard $blockAccessGuard
     */
    public function __construct(ilTabsGUI $tabs, $template, ilCtrl $controlFlow, ilLearnplacesPlugin $plugin, ILIASLinkBlockService $iliasLinkService, LearnplaceService $learnplaceService, ConfigurationService $configService, AccordionBlockService $accprdionService, ServerRequestInterface $request, AccessGuard $blockAccessGuard)
    {
        $this->tabs = $tabs;
        $this->template = $template;
        $this->controlFlow = $controlFlow;
        $this->plugin = $plugin;
        $this->iliasLinkService = $iliasLinkService;
        $this->learnplaceService = $learnplaceService;
        $this->configService = $configService;
        $this->accprdionService = $accprdionService;
        $this->request = $request;
        $this->blockAccessGuard = $blockAccessGuard;
    }


    public function executeCommand(): bool
    {
        $next_class = $this->controlFlow->getNextClass();
        $cmd = $this->controlFlow->getCmd(CommonControllerAction::CMD_INDEX);
        $this->tabs->activateTab(self::TAB_ID);

        if ($next_class === strtolower(xsrlIliasLinkBlockEditFormViewGUI::class)) {
            $this->controlFlow->forwardCommand(new xsrlIliasLinkBlockEditFormViewGUI(new ILIASLinkBlockModel()));
        }

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

    private function add(): void
    {
        $this->controlFlow->saveParameter($this, PlusView::POSITION_QUERY_PARAM);
        $this->controlFlow->saveParameter($this, PlusView::ACCORDION_QUERY_PARAM);

        $config = $this->configService->findByObjectId(ilObject::_lookupObjectId($this->getCurrentRefId()));
        $block = new ILIASLinkBlockModel();

        $block->setVisibility($config->getDefaultVisibility());
        $form = new xsrlIliasLinkBlockEditFormViewGUI($block);

        // todo expandable tree
        global $DIC;
        $refinery = $DIC->refinery();
        $request_wrapper = $DIC->http()->wrapper()->query();
        if ($request_wrapper->has('async_ref') && $request_wrapper->retrieve('async_ref', $refinery->kindlyTo()->bool())) {
            $ref = $request_wrapper->retrieve('async_ref', $refinery->kindlyTo()->int());
            $this->tree($ref);
            exit();
        }

        $this->template->setContent($form->getHTML() . $this->tree());
    }

    // todo expandable tree
    function tree($ref = null)
    {
        global $DIC;
        $ilTree = $DIC['tree'];

        if (is_null($ref)) {
            $do_async = false;
            $ref = 1;
            $data = array(
                $ilTree->getNodeData(1)
            );
        } else {
            $do_async = true;
            $data = $ilTree->getChilds($ref);
            if (count($data) === 0) {
                return;
            }
        }

        $recursion = new class () implements \ILIAS\UI\Component\Tree\TreeRecursion {
            public function getChildren($record, $environment = null): array
            {
                return [];
            }

            public function build(
                \ILIAS\UI\Component\Tree\Node\Factory $factory,
                                                      $record,
                                                      $environment = null
            ): \ILIAS\UI\Component\Tree\Node\Node {
                $ref_id = $record['ref_id'];
                $label = $record['title']
                    . ' (' . $record['type'] . ', ' . $ref_id . ')';

                $icon = $environment['icon_factory']->standard($record["type"], '');
                $url = $this->getAsyncURL($environment, $ref_id);

                $node = $factory->simple($label, $icon)
                    ->withAsyncURL($url);

                //find these under ILIAS->Administration in the example tree
                if ((int) $ref_id > 9 && (int) $ref_id < 20) {
                    $label = $environment['modal']->getShowSignal()->getId();
                    $node = $factory->simple($label)
                        ->withAsyncURL($url)
                        ->withOnClick($environment['modal']->getShowSignal());
                }

                return $node;
            }

            protected function getAsyncURL($environment, string $ref_id): string
            {
                $url = $environment['url'];
                $base = substr($url, 0, strpos($url, '?') + 1);
                $query = parse_url($url, PHP_URL_QUERY);
                if ($query) {
                    parse_str($query, $params);
                } else {
                    $params = [];
                }
                $params['async_ref'] = $ref_id;
                $url = $base . http_build_query($params);
                return $url;
            }
        };

        $f = $DIC->ui()->factory();
        $renderer = $DIC->ui()->renderer();
        $image = $f->image()->responsive("src/UI/examples/Image/mountains.jpg", "Image source: https://stocksnap.io, Creative Commons CC0 license");
        $page = $f->modal()->lightboxImagePage($image, 'Mountains');
        $modal = $f->modal()->lightbox($page);

        $environment = [
            'url' => $DIC->http()->request()->getRequestTarget(),
            'modal' => $modal,
            'icon_factory' => $f->symbol()->icon()
        ];

        $tree = $f->tree()->expandable("Label", $recursion)
            ->withEnvironment($environment)
            ->withData($data);

        if (!$do_async) {
            return $renderer->render([$modal, $tree]);
        } else {
            echo $renderer->renderAsync([$modal, $tree->withIsSubTree(true)]);
        }
    }


    private function create(): void
    {
        $form = new xsrlIliasLinkBlockEditFormViewGUI(new ILIASLinkBlockModel());
        try {
            $queries = $this->request->getQueryParams();

            //store block
            /**
             * @var ILIASLinkBlockModel $block
             */
            $block = $form->getBlockModel();
            $block->setId(0); //mitigate block id injection
            $accordionId = $this->getCurrentAccordionId($queries);
            if ($accordionId > 0) {
                $this->redirectInvalidRequests($accordionId);
            }

            $block = $this->iliasLinkService->store($block);


            $anchor = xsrlContentGUI::ANCHOR_TEMPLATE;
            if ($accordionId > 0) {
                $accordion = $this->accprdionService->find($accordionId);
                $blocks = $accordion->getBlocks();
                array_splice($blocks, $this->getInsertPosition($queries), 0, [$block]);
                $accordion->setBlocks($blocks);
                $this->accprdionService->store($accordion);
                $anchor .= $accordion->getSequence();
            } else {
                //fetch learnplace
                $learnplace = $this->learnplaceService->findByObjectId(ilObject::_lookupObjectId($this->getCurrentRefId()));

                //store relation learnplace <-> block
                $blocks = $learnplace->getBlocks();
                array_splice($blocks, $this->getInsertPosition($queries), 0, [$block]);
                $learnplace->setBlocks($blocks);
                $this->learnplaceService->store($learnplace);
                $anchor .= $block->getSequence();
            }

            $this->template->setOnScreenMessage('success', $this->plugin->txt('message_changes_save_success'), true);
            $this->controlFlow->redirectByClass(xsrlContentGUI::class, CommonControllerAction::CMD_INDEX, $anchor);
        } catch (ValidationException $ex) {
            $form->setValuesByPost();
            $this->template->setContent($form->getHTML());
        }
    }

    private function edit(): void
    {
        $blockId = $this->getBlockId();
        $block = $this->iliasLinkService->find($blockId);
        $form = new xsrlIliasLinkBlockEditFormViewGUI($block);
        $this->template->setContent($form->getHTML());
    }

    private function update(): void
    {
        $form = new xsrlIliasLinkBlockEditFormViewGUI(new ILIASLinkBlockModel());
        try {
            //store block
            /**
             * @var ILIASLinkBlockModel $block
             */
            $block = $form->getBlockModel();
            $this->redirectInvalidRequests($block->getId());
            $linkBlock = $this->iliasLinkService->find($block->getId());
            $linkBlock->setRefId($block->getRefId());
            $linkBlock->setVisibility($block->getVisibility());
            $this->iliasLinkService->store($linkBlock);

            $anchor = xsrlContentGUI::ANCHOR_TEMPLATE . $block->getSequence();
            $this->template->setOnScreenMessage('success', $this->plugin->txt('message_changes_save_success'), true);
            $this->controlFlow->redirectByClass(xsrlContentGUI::class, CommonControllerAction::CMD_INDEX, $anchor);
        } catch (ValidationException $ex) {
            $form->setValuesByPost();
            $this->template->setContent($form->getHTML());
        }
    }

    private function delete(): void
    {
        $queries = $this->request->getQueryParams();
        $blockId = intval($queries[self::BLOCK_ID_QUERY_KEY]);
        $this->redirectInvalidRequests($blockId);

        $this->iliasLinkService->delete($blockId);
        $this->regenerateSequence();
        $this->template->setOnScreenMessage('success', $this->plugin->txt('message_delete_success'), true);
        $this->controlFlow->redirectByClass(xsrlContentGUI::class, CommonControllerAction::CMD_INDEX);
    }

    private function confirm(): void
    {
        $queries = $this->request->getQueryParams();
        $confirm = new ilConfirmationGUI();
        $confirm->setHeaderText($this->plugin->txt('confirm_delete_header'));
        $confirm->setFormAction(
            $this->controlFlow->getFormAction($this) .
            '&' .
            self::BLOCK_ID_QUERY_KEY .
            '=' .
            $queries[self::BLOCK_ID_QUERY_KEY]
        );
        $confirm->setConfirm($this->plugin->txt('common_delete'), CommonControllerAction::CMD_DELETE);
        $confirm->setCancel($this->plugin->txt('common_cancel'), CommonControllerAction::CMD_CANCEL);
        $this->template->setContent($confirm->getHTML());
    }

    private function cancel(): void
    {
        $this->controlFlow->redirectByClass(xsrlContentGUI::class, CommonControllerAction::CMD_INDEX);
    }

    private function getBlockId(): int
    {
        $queries = $this->request->getQueryParams();
        return intval($queries[self::BLOCK_ID_QUERY_KEY]);
    }

    private function regenerateSequence(): void
    {
        $learnplace = $this->learnplaceService->findByObjectId(ilObject::_lookupObjectId($this->getCurrentRefId()));
        $this->learnplaceService->store($learnplace);
    }
}
