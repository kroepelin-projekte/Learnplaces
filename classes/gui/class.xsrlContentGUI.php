<?php

declare(strict_types=1);

use ILIAS\DI\UIServices;
use ILIAS\Refinery\Factory as Refinery;
use Psr\Http\Message\ServerRequestInterface;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\gui\block\AccordionBlock\AccordionBlockPresentationView;
use KPG\Learnplaces\gui\block\BlockAddFormGUI;
use KPG\Learnplaces\gui\block\BlockType;
use KPG\Learnplaces\gui\block\IliasLinkBlock\IliasLinkBlockPresentationView;
use KPG\Learnplaces\gui\block\PictureBlock\PictureBlockPresentationView;
use KPG\Learnplaces\gui\block\RenderableBlockViewFactory;
use KPG\Learnplaces\gui\block\RenderableBlockViewFactoryImpl;
use KPG\Learnplaces\gui\block\RichTextBlock\RichTextBlockEditFormView;
use KPG\Learnplaces\gui\block\util\AccordionAware;
use KPG\Learnplaces\gui\block\util\ReferenceIdAware;
use KPG\Learnplaces\gui\block\VideoBlock\VideoBlockPresentationView;
use KPG\Learnplaces\gui\ContentPresentationView;
use KPG\Learnplaces\gui\helper\CommonControllerAction;
use KPG\Learnplaces\service\publicapi\block\AccordionBlockService;
use KPG\Learnplaces\service\publicapi\block\LearnplaceService;
use KPG\Learnplaces\service\publicapi\model\AccordionBlockModel;
use KPG\Learnplaces\service\publicapi\model\BlockModel;
use KPG\Learnplaces\service\publicapi\model\MapBlockModel;
use KPG\Learnplaces\service\security\AccessGuard;
use KPG\Learnplaces\service\visibility\LearnplaceServiceDecoratorFactory;
use Repository\RepositoryObject\Learnplaces\classes\api\Config\Settings;
use KPG\Learnplaces\gui\component\PlusView;
use ILIAS\UI\Component\Input\Container\Form\Standard;
use ILIAS\UI\Factory;

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
    public const CMD_SEQUENCE_VIEW = 'sequenceView';
    /**
     * The anchor start which is used to
     * jump to the edited block after leaving the edit / creation view.
     */
    public const ANCHOR_TEMPLATE = 'sequence-';

    private static array $blockTypeViewMapping = [
        //BlockType::PICTURE_UPLOAD   => xsrlPictureUploadBlockGUI::class,
        BlockType::PICTURE => xsrlPictureBlockGUI::class,
        BlockType::RICH_TEXT => xsrlRichTextBlockGUI::class,
        BlockType::ILIAS_LINK => xsrlIliasLinkBlockGUI::class,
        BlockType::MAP => xsrlMapBlockGUI::class,
        BlockType::VIDEO => xsrlVideoBlockGUI::class,
        BlockType::ACCORDION => xsrlAccordionBlockGUI::class,
    ];

    private ilTabsGUI $tabs;
    private ilGlobalPageTemplate $template;
    private ilCtrl $controlFlow;
    private ilLearnplacesPlugin $plugin;
    private RenderableBlockViewFactory $renderableFactory;
    private LearnplaceService $learnplaceService;
    private AccordionBlockService $accordionService;
    private LearnplaceServiceDecoratorFactory $learnplaceServiceDecorationFactory;
    private BlockAddFormGUI $blockAddGUI;
    private AccessGuard $accessGuard;
    private UIServices $ui;
    private ILIAS\HTTP\Services $http;
    private Refinery $refinery;

    /**
     * xsrlContentGUI constructor.
     *
     * @param ilTabsGUI                         $tabs
     * @param ilGlobalPageTemplate              $template
     * @param UIServices                        $ui
     * @param ilCtrl                            $controlFlow
     * @param ILIAS\HTTP\Services               $http
     * @param Refinery                          $refinery
     * @param ilLearnplacesPlugin               $plugin
     * @param RenderableBlockViewFactory        $renderableFactory
     * @param LearnplaceService                 $learnplaceService
     * @param AccordionBlockService             $accordionService
     * @param LearnplaceServiceDecoratorFactory $learnplaceServiceDecorationFactory
     * @param BlockAddFormGUI                   $blockAddGUI
     * @param ServerRequestInterface            $request
     * @param AccessGuard                       $accessGuard
     */
    public function __construct(
        ilTabsGUI $tabs,
        ilGlobalPageTemplate $template,
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
                        if (method_exists($this->template, 'show')) {
                            $this->template->show();
                        }
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
                        if (method_exists($this->template, 'show')) {
                            $this->template->show();
                        }
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
     * @throws ilTemplateException|ilCtrlException
     */
    private function index(): void
    {
        $writePermission = $this->accessGuard->hasWritePermission();
        $template = $this->plugin->getTemplate('default/tpl.block_list.html');
        //decorate the learnplace only if the user has no write rights
        $learnplaceService = ($writePermission) ? $this->learnplaceService : $this->learnplaceServiceDecorationFactory->decorate($this->learnplaceService);

        $learnplace = $learnplaceService->findByObjectId(ilObject::_lookupObjectId($this->getCurrentRefId()));
        /**
         * @var ContentPresentationView $view
         */
        $view = PluginContainer::resolve(ContentPresentationView::class);
        $view->setBlocks($learnplace->getBlocks());
        $view->setReadonly(!$writePermission);

        $template->setVariable('CONTENT', $view->getHTML());

        $this->template->addCss('Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/templates/style.css');
        $this->template->addJavaScript('Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/templates/script.js');

        $button = '';
        if (!empty(Settings::getClientURL())) {
            $url = rtrim(Settings::getClientURL(), '/') . '/lernort/' . $learnplace->getId();
            $button = "<a class='btn btn-default' href='$url' target='_blank'>{$this->plugin->txt('app_link')}</a>";
        }

        $this->template->setContent(
            $button
            . $template->get()
        );
    }

    /**
     * @return void
     * @throws ilCtrlException
     */
    private function add(): void
    {
        $learnplace = $this->learnplaceService->findByObjectId(ilObject::_lookupObjectId($this->getCurrentRefId()));
        foreach ($learnplace->getBlocks() as $block) {
            if ($block instanceof MapBlockModel) {
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

        if ($form->getError()) {
            $this->template->setOnScreenMessage('failure', $this->plugin->txt('message_create_failure'), true);
            $this->controlFlow->redirect($this, CommonControllerAction::CMD_INDEX);
        }

        $input = intval($form_data[BlockAddFormGUI::POST_VISIBILITY_SECTION][BlockAddFormGUI::POST_BLOCK_TYPES]);
        $controller = xsrlContentGUI::$blockTypeViewMapping[$input];
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
            if ($block instanceof AccordionBlockModel) {
                $blockIterator->append(new ArrayIterator($block->getBlocks()));
            }
        }

        $blockIterator->append(new ArrayIterator($learnplace->getBlocks()));

        $form = $this->sequenceForm()->withRequest($this->http->request());
        $formData = $form->getData();
        if ($form->getError()) {
            $this->template->setOnScreenMessage('failure', $this->plugin->txt('content_sequence_changed_error'));
            $this->sequenceView();
            return;
        }
        $post = current($formData);

        uksort($post, function ($key1, $key2) {
            $num1 = intval(str_replace('block_', '', $key1));
            $num2 = intval(str_replace('block_', '', $key2));
            return $num2 <=> $num1;
        });


        //yield ['block_12' => '5']
        $iterator = new RegexIterator(new ArrayIterator($post), '/^(?:block\_\d+)$/', RegexIterator::MATCH, RegexIterator::USE_KEY);

        //yield [12 => 5]
        $mappedBlockGenerator = function (Iterator $iterator) {
            foreach ($iterator as $key => $entry) {
                $id = intval(str_replace('block_', '', $key));
                yield $id => intval($entry);
            }
        };

        $mappedBlocks = $mappedBlockGenerator($iterator);

        //set the new sequence numbers
        foreach ($mappedBlocks as $id => $sequence) {
            foreach ($blockIterator as $block) {
                if ($block->getId() === $id) {
                    $block->setSequence($sequence);

                    //sort accordion blocks
                    if ($block instanceof AccordionBlockModel) {
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
        usort($blocks, function (BlockModel $a, BlockModel $b) {
            return $a->getSequence() >= $b->getSequence() ? 1 : -1;
        });
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
     * @throws ilCtrlException
     */
    private function sequenceView(): void
    {
        $this->tabs->activateSubTab('sequence');
        $renderer = PluginContainer::resolve('renderer');

        $this->template->addCss('Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/templates/style.css');

        $this->template->setContent(
            $renderer->render($this->sequenceForm())
        );
    }

    /**
     * @return Standard
     * @throws ilCtrlException
     */
    private function sequenceForm(): Standard
    {
        /** @var Factory $factory */
        $factory = PluginContainer::resolve('factory');
        $field = $factory->input()->field();

        $renderableBlockViewFactory = new RenderableBlockViewFactoryImpl();
        $writePermission = $this->accessGuard->hasWritePermission();
        $learnplaceService = ($writePermission) ? $this->learnplaceService : $this->learnplaceServiceDecorationFactory->decorate($this->learnplaceService);
        $learnplace = $learnplaceService->findByObjectId(ilObject::_lookupObjectId($this->getCurrentRefId()));
        $blocks = $learnplace->getBlocks();

        $fields = [];
        foreach ($blocks as $block) {
            if ($block instanceof MapBlockModel) {
                continue;
            }
            $view = $renderableBlockViewFactory->getInstance($block);

            $blocksOfAccordion = [];
            if ($block instanceof AccordionBlockModel) {
                $block->setExpand(false);
                $blocksOfAccordion = $block->getBlocks();
                $block->setBlocks([]);
            }

            $inputField = $field->numeric('Position', $view->getHtml())
                ->withValue($block->getSequence())
                ->withRequired(true)
                ->withAdditionalOnLoadCode(function ($id) {
                    return <<<JS
                    (function() {
                        const el = document.getElementById('$id');
                        el.querySelector('.c-input__help-byline').style.pointerEvents = 'none';
                    })();
                    JS;
                });

            if ($block instanceof AccordionBlockModel) {
                $inputField = $inputField->withAdditionalOnLoadCode(function ($id) {
                    return <<<JS
                    (function() {
                        const el = document.getElementById('$id');
                        el.querySelector('.accordion-arrow').classList.add('arrow-fixed-position');
                        el.querySelector('.c-input__help-byline').style.pointerEvents = 'none';
                    })();
                    JS;
                });
            }

            $fields['block_' . $block->getId()] = $inputField;

            if ($block instanceof AccordionBlockModel) {
                foreach ($blocksOfAccordion as $accordionBlock) {
                    $view = $renderableBlockViewFactory->getInstance($accordionBlock);

                    $fields['block_' . $accordionBlock->getId()] = $field->numeric('Position', $view->getHtml())
                        ->withValue($accordionBlock->getSequence())
                        ->withAdditionalOnLoadCode(function ($id) {
                            return <<<JS
                            (function()  {
                                const input = document.getElementById('$id');
                                input.style.width = '60%';
                                input.style.marginLeft = 'auto';
                                input.querySelector('.c-input__help-byline').style.pointerEvents = 'none';
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
}
