<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use SRAG\Learnplaces\gui\block\RichTextBlock\RichTextBlockEditFormView;
use SRAG\Learnplaces\gui\block\util\AccordionAware;
use SRAG\Learnplaces\gui\block\util\BlockIdReferenceValidationAware;
use SRAG\Learnplaces\gui\block\util\InsertPositionAware;
use SRAG\Learnplaces\gui\block\util\ReferenceIdAware;
use SRAG\Learnplaces\gui\component\PlusView;
use SRAG\Learnplaces\gui\exception\ValidationException;
use SRAG\Learnplaces\gui\helper\CommonControllerAction;
use SRAG\Learnplaces\service\publicapi\block\AccordionBlockService;
use SRAG\Learnplaces\service\publicapi\block\ConfigurationService;
use SRAG\Learnplaces\service\publicapi\block\LearnplaceService;
use SRAG\Learnplaces\service\publicapi\block\RichTextBlockService;
use SRAG\Learnplaces\service\publicapi\model\RichTextBlockModel;
use SRAG\Learnplaces\service\security\AccessGuard;

/**
 * Class xsrlRichTextBlock
 *
 * @package SRAG\Learnplaces\gui\block\RichTextBlock
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class xsrlRichTextBlockGUI
{
    use InsertPositionAware;
    use AccordionAware;
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
     * @var RichTextBlockService $richTextBlockService
     */
    private $richTextBlockService;
    /**
     * @var LearnplaceService $learnplaceService
     */
    private $learnplaceService;
    /**
     * @var ConfigurationService $configService
     */
    private $configService;
    /**
     * @var AccordionBlockService $accordionService
     */
    private $accordionService;
    /**
     * @var ServerRequestInterface $request
     */
    private $request;
    /**
     * @var AccessGuard $blockAccessGuard
     */
    private $blockAccessGuard;

    /**
     * xsrlRichTextBlockGUI constructor.
     *
     * @param ilTabsGUI $tabs
     * @param ilGlobalPageTemplate | ilTemplate $template
     * @param ilCtrl $controlFlow
     * @param ilLearnplacesPlugin $plugin
     * @param RichTextBlockService $richTextBlockService
     * @param LearnplaceService $learnplaceService
     * @param ConfigurationService $configService
     * @param AccordionBlockService $accordionService
     * @param ServerRequestInterface $request
     * @param AccessGuard $blockAccessGuard
     */
    public function __construct(ilTabsGUI $tabs, $template, ilCtrl $controlFlow, ilLearnplacesPlugin $plugin, RichTextBlockService $richTextBlockService, LearnplaceService $learnplaceService, ConfigurationService $configService, AccordionBlockService $accordionService, ServerRequestInterface $request, AccessGuard $blockAccessGuard)
    {
        $this->tabs = $tabs;
        $this->template = $template;
        $this->controlFlow = $controlFlow;
        $this->plugin = $plugin;
        $this->richTextBlockService = $richTextBlockService;
        $this->learnplaceService = $learnplaceService;
        $this->configService = $configService;
        $this->accordionService = $accordionService;
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
        $this->controlFlow->saveParameter($this, PlusView::ACCORDION_QUERY_PARAM);

        $config = $this->configService->findByObjectId(ilObject::_lookupObjectId($this->getCurrentRefId()));
        $block = new RichTextBlockModel();

        $block->setVisibility($config->getDefaultVisibility());
        $form = new RichTextBlockEditFormView($block);

        $this->template->setContent($form->getHTML());

        $this->addRichTextEdiorJs('');
    }

    /**
     * @return void
     * @throws ilCtrlException
     */
    private function create(): void
    {
        $form = new RichTextBlockEditFormView(new RichTextBlockModel());
        try {
            $queries = $this->request->getQueryParams();

            //store block
            /**
             * @var RichTextBlockModel $block
             */
            $block = $form->getBlockModel();
            $block->setId(0); //mitigate block id injection
            $accordionId = $this->getCurrentAccordionId($queries);
            if ($accordionId > 0) {
                $this->redirectInvalidRequests($accordionId);
            }

            $block = $this->richTextBlockService->store($block);

            $anchor = xsrlContentGUI::ANCHOR_TEMPLATE;
            if ($accordionId > 0) {
                $accordion = $this->accordionService->find($accordionId);
                $blocks = $accordion->getBlocks();
                array_splice($blocks, $this->getInsertPosition($queries), 0, [$block]);
                $accordion->setBlocks($blocks);
                $this->accordionService->store($accordion);
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
        $block = $this->richTextBlockService->find($this->getBlockId());
        $form = new RichTextBlockEditFormView($block);
        $this->template->setContent($form->getHTML());

        $this->addRichTextEdiorJs($block->getContent());
    }

    /**
     * @return void
     * @throws ilCtrlException
     */
    private function update(): void
    {

        $form = new RichTextBlockEditFormView(new RichTextBlockModel());
        try {
            //store block
            /**
             * @var RichTextBlockModel $block
             */
            $block = $form->getBlockModel();
            $this->redirectInvalidRequests($block->getId());
            $oldBlock = $this->richTextBlockService->find($block->getId());
            $block->setSequence($oldBlock->getSequence());
            $this->richTextBlockService->store($block);

            $anchor = xsrlContentGUI::ANCHOR_TEMPLATE . $block->getSequence();
            $this->template->setOnScreenMessage('success', $this->plugin->txt('message_changes_save_success'), true);
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
        $this->richTextBlockService->delete($blockId);
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

    /**
     * @param string $content
     * @return void
     */
    private function addRichTextEdiorJs(string $content): void
    {
        $tinymce_library_src = "./Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/vendor/tinymce/tinymce/tinymce.min.js";
        $this->template->addJavaScript($tinymce_library_src);

        $content = preg_replace('/(\r\n|\n|\r)/', '', $content);

        $skin_css_path = ilUtil::getStyleSheetLocation();

        $this->template->addOnLoadCode(
            <<<JS
            tinymce.init({
                selector: '#textarea',
                inline: false,
                menubar: false,
                branding: false,
                statusbar: false,
                paste_block_drop: false,
                paste_data_images: false,
                paste_as_text: true,
                paste_word_valid_elements: 'p,br,strong,b,i,u,s,strike,em,span',
                content_css: '$skin_css_path',
                height : "40vh",
                content_style: "html body#tinymce {overflow-y: scroll; background: transparent !important; padding: 10px;} body#tinymce::selection {background: transparent !important;} .tox .tox-edit-area .tox-edit-area__iframe {background: transparent !important;} html {overflow-y: scroll !important;} tox tox-tinymce {height: 100%; min-height: 500vh;}",
                plugins: 'lists autolink link image paste',
                toolbar: 'undo redo | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify',
                setup: function (editor) {
                    editor.on("init", function () {
                          this.setContent(`$content`);
                        }
                    );
                }
            });
            JS
        );
    }
}
