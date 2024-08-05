<?php

declare(strict_types=1);

use ILIAS\DI\UIServices;
use ILIAS\Filesystem\Stream\Stream;
use ILIAS\Refinery\Factory as Refinery;
use ILIAS\ResourceStorage\Identification\ResourceIdentification;
use Psr\Http\Message\ServerRequestInterface;
use SRAG\Learnplaces\container\PluginContainer;
use SRAG\Learnplaces\gui\block\AccordionBlock\AccordionBlockPresentationView;
use SRAG\Learnplaces\gui\block\BlockAddFormGUI;
use SRAG\Learnplaces\gui\block\BlockType;
use SRAG\Learnplaces\gui\block\IliasLinkBlock\IliasLinkBlockPresentationView;
use SRAG\Learnplaces\gui\block\PictureBlock\PictureBlockPresentationView;
use SRAG\Learnplaces\gui\block\RenderableBlockViewFactory;
use SRAG\Learnplaces\gui\block\RenderableBlockViewFactoryImpl;
use SRAG\Learnplaces\gui\block\RichTextBlock\RichTextBlockEditFormView;
use SRAG\Learnplaces\gui\block\util\AccordionAware;
use SRAG\Learnplaces\gui\block\util\ReferenceIdAware;
use SRAG\Learnplaces\gui\block\VideoBlock\VideoBlockPresentationView;
use SRAG\Learnplaces\gui\component\PlusView;
use SRAG\Learnplaces\gui\ContentPresentationView;
use SRAG\Learnplaces\gui\helper\CommonControllerAction;
use SRAG\Learnplaces\service\publicapi\block\AccordionBlockService;
use SRAG\Learnplaces\service\publicapi\block\LearnplaceService;
use SRAG\Learnplaces\service\publicapi\model\AccordionBlockModel;
use SRAG\Learnplaces\service\publicapi\model\BlockModel;
use SRAG\Learnplaces\service\publicapi\model\ILIASLinkBlockModel;
use SRAG\Learnplaces\service\publicapi\model\MapBlockModel;
use SRAG\Learnplaces\service\publicapi\model\PictureBlockModel;
use SRAG\Learnplaces\service\publicapi\model\RichTextBlockModel;
use SRAG\Learnplaces\service\publicapi\model\VideoBlockModel;
use SRAG\Learnplaces\service\security\AccessGuard;
use SRAG\Learnplaces\service\visibility\LearnplaceServiceDecoratorFactory;

/**
 *
 *
 * Wie https://git.studer-raimann.ch/ILIAS/Core/blob/feature/5-4/bibliographic-improvements/Modules/Bibliographic/classes/FieldFilter/class.ilBiblFieldFilterGUI.php
 *
 *
 * Class xsrlContentGUI
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 *
 */
final class xsrlContentGUI
{
    use ReferenceIdAware;
    use AccordionAware;

    public const TAB_ID = 'content';
    /**
     * Command to store the sequence numbers
     */
    public const CMD_SEQUENCE = 'sequence';
    private const CMD_SEQUENCE_FORM = 'sequenceForm';
    private const CMD_SEQUENCE_VIEW = 'sequenceView';
    /**
     * The anchor start which is used to
     * jump to the edited block after leaving the edit / creation view.
     */
    public const ANCHOR_TEMPLATE = 'sequence-';

    private static $blockTypeViewMapping = [
        //BlockType::PICTURE_UPLOAD   => xsrlPictureUploadBlockGUI::class,
        BlockType::PICTURE          => xsrlPictureBlockGUI::class,
        BlockType::RICH_TEXT        => xsrlRichTextBlockGUI::class,
        BlockType::ILIAS_LINK       => xsrlIliasLinkBlockGUI::class,
        BlockType::MAP              => xsrlMapBlockGUI::class,
        BlockType::VIDEO            => xsrlVideoBlockGUI::class,
        BlockType::ACCORDION        => xsrlAccordionBlockGUI::class,
    ];

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
     * @var RenderableBlockViewFactory $renderableFactory
     */
    private $renderableFactory;
    /**
     * @var LearnplaceService $learnplaceService
     */
    private $learnplaceService;
    /**
     * @var AccordionBlockService $accordionService
     */
    private $accordionService;
    /**
     * @var LearnplaceServiceDecoratorFactory $learnplaceServiceDecorationFactory
     */
    private $learnplaceServiceDecorationFactory;
    /**
     * @var BlockAddFormGUI $blockAddGUI
     */
    private $blockAddGUI;
    /**
     * @var ServerRequestInterface $request
     */
    private $request;
    /**
     * @var AccessGuard $accessGuard
     */
    private $accessGuard;

    private UIServices $ui;
    private ILIAS\HTTP\Services $http;

    private $refinery;

    /**
     * xsrlContentGUI constructor.
     *
     * @param ilTabsGUI $tabs
     * @param ilGlobalPageTemplate | ilTemplate $template
     * @param UIServices $ui
     * @param ilCtrl $controlFlow
     * @param ILIAS\HTTP\Services $http
     * @param Refinery $refinery
     * @param ilLearnplacesPlugin $plugin
     * @param RenderableBlockViewFactory $renderableFactory
     * @param LearnplaceService $learnplaceService
     * @param AccordionBlockService $accordionService
     * @param LearnplaceServiceDecoratorFactory $learnplaceServiceDecorationFactory
     * @param BlockAddFormGUI $blockAddGUI
     * @param ServerRequestInterface $request
     * @param AccessGuard $accessGuard
     */
    public function __construct(
        ilTabsGUI $tabs,
        $template,
        UIServices $ui,
        ilCtrl $controlFlow,
        ILIAS\HTTP\Services $http,
        Refinery $refinery,
        ilLearnplacesPlugin $plugin,
        RenderableBlockViewFactory $renderableFactory,
        LearnplaceService $learnplaceService,
        AccordionBlockService $accordionService,
        LearnplaceServiceDecoratorFactory $learnplaceServiceDecorationFactory,
        BlockAddFormGUI $blockAddGUI,
        ServerRequestInterface $request,
        AccessGuard $accessGuard
    ) {
        $this->tabs = $tabs;
        $this->template = $template;
        $this->ui = $ui;
        $this->controlFlow = $controlFlow;
        $this->http = $http;
        $this->refinery = $refinery;
        $this->plugin = $plugin;
        $this->renderableFactory = $renderableFactory;
        $this->learnplaceService = $learnplaceService;
        $this->accordionService = $accordionService;
        $this->learnplaceServiceDecorationFactory = $learnplaceServiceDecorationFactory;
        $this->blockAddGUI = $blockAddGUI;
        $this->request = $request;
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
                if ($this->accessGuard->hasReadPermission()) {
                    $this->index();
                    if (version_compare(ILIAS_VERSION_NUMERIC, "6.0", "<")) {
                        $this->template->show();
                    } else {
                        $this->template->printToStdout();
                    }
                    return true;
                }
                break;
            case CommonControllerAction::CMD_ADD:
            case CommonControllerAction::CMD_CANCEL:
            case CommonControllerAction::CMD_CONFIRM:
            case CommonControllerAction::CMD_CREATE:
            case CommonControllerAction::CMD_DELETE:
            case CommonControllerAction::CMD_EDIT:
            case CommonControllerAction::CMD_UPDATE:
            case self::CMD_SEQUENCE:
            case self::CMD_SEQUENCE_VIEW:
            case self::CMD_SEQUENCE_FORM:
                if ($this->accessGuard->hasWritePermission()) {
                    $this->{$cmd}();
                    if (version_compare(ILIAS_VERSION_NUMERIC, "6.0", "<")) {
                        $this->template->show();
                    } else {
                        $this->template->printToStdout();
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
     * actions
     *
     * @return void
     * @throws ilCtrlException
     * @throws ilTemplateException
     */
    private function index(): void
    {
        $factory = PluginContainer::resolve('factory');
        $toolbar = new ilToolbarGUI();

        $saveSequenceButton = $factory->button()->standard(
            $this->plugin->txt('content_change_sequence'),
            $this->controlFlow->getLinkTargetByClass(self::class, self::CMD_SEQUENCE_VIEW),
        );
        $toolbar->addComponent($saveSequenceButton);

        $writePermission = $this->accessGuard->hasWritePermission();
        $template = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/templates/default/tpl.block_list.html', true, true);

        //decorate the learnplace only if the user has no write rights
        $learnplaceService = ($writePermission) ? $this->learnplaceService : $this->learnplaceServiceDecorationFactory->decorate($this->learnplaceService);

        $learnplace = $learnplaceService->findByObjectId(ilObject::_lookupObjectId($this->getCurrentRefId()));
        /**
         * @var ContentPresentationView $view
         */
        $view = PluginContainer::resolve(ContentPresentationView::class);
        $view->setBlocks($learnplace->getBlocks());
        $view->setReadonly(!$writePermission);

        if($writePermission) {
            $template->setVariable('FORM_ACTION', $this->controlFlow->getFormAction($this, self::CMD_SEQUENCE));
            $template->setVariable('TOOLBAR', $toolbar->getHTML());
        }

        $template->setVariable('CONTENT', $view->getHTML());

        $this->template->addCss(ilLearnplacesPlugin::getInstance()->getStyleSheetLocation('style.css'));
        $this->template->setContent($template->get());
    }

    /**
     * @return void
     * @throws ilCtrlException
     */
    private function add(): void
    {
        $learnplace = $this->learnplaceService->findByObjectId(ilObject::_lookupObjectId($this->getCurrentRefId()));
        foreach ($learnplace->getBlocks() as $block) {
            if($block instanceof MapBlockModel) {
                $this->blockAddGUI->setMapEnabled(false);
                break;
            }
        }

        $this->blockAddGUI->setAccordionEnabled($this->getCurrentAccordionId($this->request->getQueryParams()) === 0);

        $this->template->setContent($this->blockAddGUI->getHTML());
    }

    /**
     * @return void
     * @throws ilCtrlException
     */
    private function create(): void
    {
        $blockAdd = $this->blockAddGUI;
        $blockAdd->initForm();
        $form = $blockAdd->getForm();
        $form = $form->withRequest($this->http->request());
        $form_data = $form->getData();

        if($form->getError()) {
            $this->template->setOnScreenMessage('failure', $this->plugin->txt('message_create_failure'), true);
            $this->controlFlow->redirect($this, CommonControllerAction::CMD_INDEX);
        }

        $input = intval($form_data[BlockAddFormGUI::POST_VISIBILITY_SECTION][BlockAddFormGUI::POST_BLOCK_TYPES]);
        $controller = static::$blockTypeViewMapping[$input];
        $this->controlFlow->saveParameterByClass($controller, PlusView::POSITION_QUERY_PARAM);
        $this->controlFlow->saveParameterByClass($controller, PlusView::ACCORDION_QUERY_PARAM);

        //dispatch to controller which knows how to handle that block
        $this->controlFlow->redirectByClass($controller, CommonControllerAction::CMD_ADD);
    }

    /**
     * @return void
     * @throws ilCtrlException
     */
    private function cancel(): void
    {
        $this->controlFlow->redirect($this, CommonControllerAction::CMD_INDEX);
    }

    /**
     * @return void
     * @throws ilCtrlException
     */
    private function sequence(): void
    {
        $learnplace = $this->learnplaceService->findByObjectId(ilObject::_lookupObjectId($this->getCurrentRefId()));
        $blockIterator = new AppendIterator();

        foreach ($learnplace->getBlocks() as $block) {
            if($block instanceof AccordionBlockModel) {
                $blockIterator->append(new ArrayIterator($block->getBlocks()));
            }
        }

        $blockIterator->append(new ArrayIterator($learnplace->getBlocks()));

        #$post = $this->request->getParsedBody();
        $form = $this->sequenceForm()->withRequest($this->http->request());
        $formData = $form->getData();
        if ($form->getError()) {
            $this->template->setOnScreenMessage('failure', $this->plugin->txt('content_sequence_changed_error'));
            $this->sequenceView();
            return;
        }
        $post = current($formData);
        krsort($post);

        //yield ['block_12' => '5']
        $iterator = new RegexIterator(new ArrayIterator($post), '/^(?:block\_\d+)$/', RegexIterator::MATCH, RegexIterator::USE_KEY);

        //yield [12 => 5]
        $mappedBlockGenerator = function (Iterator $iterator) {
            foreach ($iterator as $key => $entry) {
                $id = intval(str_replace('block_', '', $key));
                yield $id => intval($entry);
            }
            return;
        };

        $mappedBlocks = $mappedBlockGenerator($iterator);

        //set the new sequence numbers
        foreach ($mappedBlocks as $id => $sequence) {
            foreach ($blockIterator as $block) {
                if($block->getId() === $id) {
                    $block->setSequence($sequence);

                    //sort accordion blocks
                    if($block instanceof AccordionBlockModel) {
                        $block->setBlocks($this->sortBlocksBySequence($block->getBlocks()));
                    }

                    break;
                }
            }
        }

        $blocks = $learnplace->getBlocks();
        $learnplace->setBlocks($this->sortBlocksBySequence($blocks));

        //store new sequence
        $this->learnplaceService->store($learnplace);

        $this->template->setOnScreenMessage('success', $this->plugin->txt('content_sequence_changed_successfully'), true);
        $this->controlFlow->redirect($this, CommonControllerAction::CMD_INDEX);
    }

    /**
     * @param array $blocks
     * @return array
     */
    private function sortBlocksBySequence(array $blocks): array
    {
        usort($blocks, function (BlockModel $a, BlockModel $b) { return $a->getSequence() >= $b->getSequence() ? 1 : -1;});
        return $blocks;
    }

    /**
     * Workaround because form action in modal is not working in ILIAS 8.
     * The modal should redirect to the block gui.
     *
     * @return void
     * @throws ilCtrlException
     */
    private function delete(): void
    {
        global $DIC;
        $superglobal = $DIC->http()->wrapper();
        if ($superglobal->post()->has('interruptive_items')) {
            $interruptive_items = $superglobal->post()->retrieve('interruptive_items', $this->refinery->kindlyTo()->listOf($DIC->refinery()->kindlyTo()->string()));
            [$itemId, $blockType] = explode('-', $interruptive_items[0]);

            switch ($blockType) {
                case AccordionBlockPresentationView::TYPE:
                    $this->controlFlow->setParameterByClass(xsrlAccordionBlockGUI::class, 'block', $itemId);
                    $this->controlFlow->redirectByClass(xsrlAccordionBlockGUI::class, CommonControllerAction::CMD_DELETE);
                    break;
                case PictureBlockPresentationView::TYPE:
                    $this->controlFlow->setParameterByClass(xsrlPictureBlockGUI::class, 'block', $itemId);
                    $this->controlFlow->redirectByClass(xsrlPictureBlockGUI::class, CommonControllerAction::CMD_DELETE);
                    break;
                case IliasLinkBlockPresentationView::TYPE:
                    $this->controlFlow->setParameterByClass(xsrlIliasLinkBlockGUI::class, 'block', $itemId);
                    $this->controlFlow->redirectByClass(xsrlIliasLinkBlockGUI::class, CommonControllerAction::CMD_DELETE);
                    break;
                case RichTextBlockEditFormView::TYPE:
                    $this->controlFlow->setParameterByClass(xsrlRichTextBlockGUI::class, 'block', $itemId);
                    $this->controlFlow->redirectByClass(xsrlRichTextBlockGUI::class, CommonControllerAction::CMD_DELETE);
                    break;
                case VideoBlockPresentationView::TYPE:
                    $this->controlFlow->setParameterByClass(xsrlVideoBlockGUI::class, 'block', $itemId);
                    $this->controlFlow->redirectByClass(xsrlVideoBlockGUI::class, CommonControllerAction::CMD_DELETE);
                    break;
            }
        }
    }

    /**
     * @return void
     */
    private function sequenceView(): void
    {
        $renderer = PluginContainer::resolve('renderer');

        $this->template->addCss(ilLearnplacesPlugin::getInstance()->getStyleSheetLocation('style.css'));

        $this->template->setContent(
            $renderer->render($this->sequenceForm())
        );
    }

    /**
     * @return \ILIAS\UI\Component\Input\Container\Form\Standard
     * @throws ilCtrlException
     */
    private function sequenceForm(): \ILIAS\UI\Component\Input\Container\Form\Standard
    {
        /** @var \ILIAS\UI\Factory $factory */
        $factory = PluginContainer::resolve('factory');
        $field = $factory->input()->field();

        $renderableBlockViewFactory = new RenderableBlockViewFactoryImpl();
        $writePermission = $this->accessGuard->hasWritePermission();
        $learnplaceService = ($writePermission) ? $this->learnplaceService : $this->learnplaceServiceDecorationFactory->decorate($this->learnplaceService);
        $learnplace = $learnplaceService->findByObjectId(ilObject::_lookupObjectId($this->getCurrentRefId()));
        $blocks = $learnplace->getBlocks();

        $fields = [];
        foreach ($blocks as $block) {
            $view = $renderableBlockViewFactory->getInstance($block);

            $label = $this->getLabel($block);

            if ($block instanceof AccordionBlockModel) {
                $block->setExpand(false);
                $blocksOfAccordion = $block->getBlocks();
                $block->setBlocks([]);
            }

            $inputField = $field->numeric($label, $view->getHtml())
                ->withValue($block->getSequence())
                ->withRequired(true);

            if ($block instanceof AccordionBlockModel) {
                $inputField = $inputField->withOnLoadCode(function ($id) {
                    return <<<JS
                    (function() {
                        const el = document.getElementById('$id');
                        el.parentElement.querySelector('.help-block').style.pointerEvents = 'none';
                        el.parentElement.querySelector('#accordion-arrow').style.transform = 'rotate(90deg)';
                    })();
                    JS;
                });
            }

            $fields['block_' . $block->getId()] = $inputField;

            if ($block instanceof AccordionBlockModel) {
                foreach ($blocksOfAccordion as $accordionBlock) {
                    $view = $renderableBlockViewFactory->getInstance($accordionBlock);

                    //   language var
                    $fields['block_' . $accordionBlock->getId()] = $field->numeric($this->getLabel($accordionBlock), $view->getHtml())
                        ->withValue($accordionBlock->getSequence())
                        ->withOnLoadCode(function ($id) {
                            return <<<JS
                            (function()  {
                                const input = document.getElementById('$id');
                                const el = input.parentElement;
                                el.style.width = '60%';
                                el.style.float = 'right';
                            })();
                            JS;
                        })
                        ->withRequired(true);
                }
            }
        }

        $section = $field->section($fields, $this->plugin->txt('content_change_sequence'));

        $action = $this->controlFlow->getFormAction($this, self::CMD_SEQUENCE);

        return $factory->input()->container()->form()->standard($action, [$section]);
    }

    private function getLabel($block): string
    {
        $label = '';
        switch ($block) {
            case $block instanceof AccordionBlockModel:
                $label = 'Accordion';
                break;
            case $block instanceof IliasLinkBlockModel:
                $label = 'Link';
                break;
            case $block instanceof RichTextBlockModel:
                $label = 'Text';
                break;
            case $block instanceof VideoBlockModel:
                $label = 'Video';
                break;
            case $block instanceof PictureBlockModel:
                $label = 'Picture';
                break;
        }

        // todo  lang
        return $label . ' position';
    }
}
