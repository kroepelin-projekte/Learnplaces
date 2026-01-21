<?php

declare(strict_types=1);

use ILIAS\DI\UIServices;
use Psr\Http\Message\ServerRequestInterface;
use KPG\Learnplaces\gui\block\PictureBlock\PictureBlockEditFormView;
use KPG\Learnplaces\gui\block\util\AccordionAware;
use KPG\Learnplaces\gui\block\util\BlockIdReferenceValidationAware;
use KPG\Learnplaces\gui\block\util\InsertPositionAware;
use KPG\Learnplaces\gui\block\util\ReferenceIdAware;
use KPG\Learnplaces\gui\component\PlusView;
use KPG\Learnplaces\gui\exception\ValidationException;
use KPG\Learnplaces\gui\helper\CommonControllerAction;
use KPG\Learnplaces\service\media\exception\FileUploadException;
use KPG\Learnplaces\service\media\PictureService;
use KPG\Learnplaces\service\publicapi\block\AccordionBlockService;
use KPG\Learnplaces\service\publicapi\block\ConfigurationService;
use KPG\Learnplaces\service\publicapi\block\LearnplaceService;
use KPG\Learnplaces\service\publicapi\block\PictureBlockService;
use KPG\Learnplaces\service\publicapi\model\PictureBlockModel;
use KPG\Learnplaces\service\security\AccessGuard;
use ILIAS\HTTP\Services;

/**
 * Class xsrlPictureBlockGUI
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class xsrlPictureBlockGUI
{
    use InsertPositionAware;
    use AccordionAware;
    use BlockIdReferenceValidationAware;
    use ReferenceIdAware;

    public const TAB_ID = 'content';
    public const BLOCK_ID_QUERY_KEY = 'block';

    private ilTabsGUI $tabs;
    private ilGlobalPageTemplate $template;
    private ilCtrl $controlFlow;
    private ilLearnplacesPlugin $plugin;
    private PictureService $pictureService;
    private PictureBlockService $pictureBlockService;
    private LearnplaceService $learnplaceService;
    private ConfigurationService $configService;
    private AccordionBlockService $accordionService;
    private AccessGuard $blockAccessGuard;
    private UIServices $ui;
    private Services $http;

    /**
     * xsrlPictureBlockGUI constructor.
     *
     * @param ilTabsGUI                         $tabs
     * @param ilGlobalPageTemplate | ilTemplate $template
     * @param ilCtrl                            $controlFlow
     * @param ilLearnplacesPlugin               $plugin
     * @param PictureService                    $pictureService
     * @param PictureBlockService               $pictureBlockService
     * @param LearnplaceService                 $learnplaceService
     * @param ConfigurationService              $configService
     * @param AccordionBlockService             $accordionService
     * @param ServerRequestInterface            $request
     * @param AccessGuard                       $blockAccessGuard
     */
    public function __construct(ilTabsGUI $tabs, $template, ilCtrl $controlFlow, ilLearnplacesPlugin $plugin, PictureService $pictureService, PictureBlockService $pictureBlockService, LearnplaceService $learnplaceService, ConfigurationService $configService, AccordionBlockService $accordionService, ServerRequestInterface $request, AccessGuard $blockAccessGuard)
    {
        $this->tabs = $tabs;
        $this->template = $template;
        $this->controlFlow = $controlFlow;
        $this->plugin = $plugin;
        $this->pictureService = $pictureService;
        $this->pictureBlockService = $pictureBlockService;
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
        $block = new PictureBlockModel();

        $block->setVisibility($config->getDefaultVisibility());
        $form = new PictureBlockEditFormView($block);
        $this->template->setContent($form->getHTML());
    }

    /**
     * @return void
     * @throws ilCtrlException
     */
    private function create(): void
    {
        $form = new PictureBlockEditFormView(new PictureBlockModel());
        try {
            $queries = $this->request->getQueryParams();

            //store block
            /**
             * @var PictureBlockModel $block
             */
            $block = $form->getBlockModel();
            $block->setId(0); //mitigate block id injection
            $accordionId = $this->getCurrentAccordionId($queries);
            if ($accordionId > 0) {
                $this->redirectInvalidRequests($accordionId);
            }

            $resourceId = current($form->getFormData()[PictureBlockEditFormView::POST_IMAGE]);
            $picture = $this->pictureService->storeUpload(ilObject::_lookupObjectId($this->getCurrentRefId()), $resourceId);
            $block->setPicture($picture);
            $block = $this->pictureBlockService->store($block);

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
        } catch (ValidationException) {
            $form->setValuesByPost();
            $this->template->setContent($form->getHTML());
        } catch (LogicException) {
            $form->setValuesByPost();
            $this->template->setContent($form->getHTML());
        } catch (FileUploadException) {
            $form->setValuesByPost();
            $this->template->setOnScreenMessage('failure', $this->plugin->txt('picture_block_upload_error'), true);
            $this->template->setContent($form->getHTML());
        }
    }

    /**
     * @return void
     */
    private function edit(): void
    {
        $blockId = $this->getBlockId();
        $block = $this->pictureBlockService->find($blockId);
        $form = new PictureBlockEditFormView($block);
        $this->template->setContent($form->getHTML());
    }

    /**
     * @return void
     * @throws ilCtrlException
     */
    private function update(): void
    {
        $tempBlock = new PictureBlockModel();
        $tempBlock->setId(PHP_INT_MAX);
        $form = new PictureBlockEditFormView($tempBlock);

        try {
            /**
             * @var PictureBlockModel $block
             */
            $block = $form->getBlockModel();
            $this->redirectInvalidRequests($block->getId());

            $oldPictureBlock = $this->pictureBlockService->find($block->getId());
            $oldPicture = $oldPictureBlock->getPicture();
            $block->setPicture($oldPicture);

            $resourceId = current($form->getFormData()[PictureBlockEditFormView::POST_IMAGE]);
            if ($resourceId) {
                //store new picture
                $picture = $this->pictureService->storeUpload(ilObject::_lookupObjectId($this->getCurrentRefId()), $resourceId);
                $block->setPicture($picture);

                //delete old picture
                $this->pictureService->delete($oldPicture->getId());
            }

            $block->setSequence($oldPictureBlock->getSequence());
            $this->pictureBlockService->store($block);

            $anchor = xsrlContentGUI::ANCHOR_TEMPLATE . $block->getSequence();
            $this->template->setOnScreenMessage('success', $this->plugin->txt('message_changes_save_success'), true);
            $this->controlFlow->redirectByClass(xsrlContentGUI::class, CommonControllerAction::CMD_INDEX, $anchor);
        } catch (ValidationException) {
            $form->setValuesByPost();
            $this->template->setContent($form->getHTML());
        } catch (LogicException) {
            $form->setValuesByPost();
            $this->template->setContent($form->getHTML());
        } catch (FileUploadException) {
            $form->setValuesByPost();
            $this->template->setOnScreenMessage('failure', $this->plugin->txt('picture_block_upload_error'), true);
            $this->template->setContent($form->getHTML());
        }
    }

    /**
     * @return void
     * @throws ilCtrlException
     */
    private function delete(): void
    {
        $blockId = $this->getBlockId();
        $this->redirectInvalidRequests($blockId);
        $this->pictureBlockService->delete($blockId);
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
