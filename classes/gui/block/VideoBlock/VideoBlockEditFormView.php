<?php

declare(strict_types=1);

namespace KPG\Learnplaces\gui\block\VideoBlock;

use ilFileInputGUI;
use ILIAS\FileUpload\MimeType;
use ILIAS\UI\Component\Input\Field\Section;
use ilLearnplacesUploadHandlerGUI;
use KPG\Learnplaces\gui\block\AbstractBlockEditFormView;
use xsrlVideoBlockGUI;

/**
 * Class VideoBlockEditFormView
 *
 * @package KPG\Learnplaces\gui\block\VideoBlock
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class VideoBlockEditFormView extends AbstractBlockEditFormView
{
    public const POST_VIDEO = 'post_video';

    /**
     * @inheritDoc
     */
    protected function hasBlockSpecificParts(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function initBlockSpecificForm(): Section
    {
        $fileUpload = $this->field->file(new ilLearnplacesUploadHandlerGUI(), $this->plugin->txt('video_block_select_video'))
            ->withAcceptedMimeTypes([MimeType::VIDEO__MP4])
            ->withRequired($this->block->getId() <= 0);

        return $this->field->section([
            self::POST_VIDEO => $fileUpload,
        ], $this->plugin->txt('block_specific_settings'));
    }

    /**
     * @inheritDoc
     */
    protected function getFormActionUrl(): string
    {
        return $this->ctrl->getFormActionByClass(xsrlVideoBlockGUI::class, $this->getFormCmd());
    }

    /**
     * @inheritDoc
     */
    protected function getObject()
    {

    }
}
