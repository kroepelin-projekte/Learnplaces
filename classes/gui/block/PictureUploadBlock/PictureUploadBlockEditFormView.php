<?php

declare(strict_types=1);

namespace KPG\Learnplaces\gui\block\PictureUploadBlock;

use KPG\Learnplaces\gui\block\AbstractBlockEditFormView;
use xsrlPictureUploadBlockGUI;

/**
 * Class PictureUploadBlockEditFormView
 *
 * @package KPG\Learnplaces\gui\block
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class PictureUploadBlockEditFormView extends AbstractBlockEditFormView
{
    /**
     * @inheritDoc
     */
    protected function hasBlockSpecificParts(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    protected function getFormActionUrl(): string
    {
        return $this->ctrl->getFormActionByClass(xsrlPictureUploadBlockGUI::class);
    }

    /**
     * @inheritDoc
     */
    protected function initBlockSpecificForm(): void
    {
    }

    /**
     * @inheritDoc
     */
    protected function getObject(): void
    {
    }
}
