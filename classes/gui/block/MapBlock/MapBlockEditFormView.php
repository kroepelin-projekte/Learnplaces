<?php

declare(strict_types=1);

namespace KPG\Learnplaces\gui\block\PictureUploadBlock;

use KPG\Learnplaces\gui\block\AbstractBlockEditFormView;
use xsrlMapBlockGUI;

/**
 * Class MapBlockEditFormView
 *
 * @package KPG\Learnplaces\gui\block\PictureUploadBlock
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class MapBlockEditFormView extends AbstractBlockEditFormView
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
        return $this->ctrl->getFormActionByClass(xsrlMapBlockGUI::class, $this->getFormCmd());
    }

    /**
     * @inheritDoc
     */
    protected function initBlockSpecificForm()
    {
    }

    /**
     * @inheritDoc
     */
    protected function getObject()
    {
    }
}
