<?php

declare(strict_types=1);

use ILIAS\UI\Implementation\Component\Input\Field\Section;
use SRAG\Learnplaces\container\PluginContainer;
use SRAG\Learnplaces\gui\block\AbstractBlockEditFormView;
use SRAG\Learnplaces\service\publicapi\model\ILIASLinkBlockModel;
use SRAG\Learnplaces\service\publicapi\model\BlockModel;
use SRAG\Learnplaces\util\LinkInput;

/**
 * Class IliasLinkBlockEditFormView
 *
 * @package SRAG\Learnplaces\gui\block\IliasLinkBlock
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 * @ilCtrl_Calls xsrlIliasLinkBlockEditFormViewGUI: ilFormPropertyDispatchGUI
 */
final class xsrlIliasLinkBlockEditFormViewGUI extends AbstractBlockEditFormView
{
    public const POST_REFID = 'post_refid';
    /**
     * @var ILIASLinkBlockModel $block
     */
    protected BlockModel $block;

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
        $linkInput = new LinkInput();
        $link = $linkInput->getLinkButton($this->plugin->txt('ilias_link_block_select_target'), $this->block->getRefId());

        return $this->field->section([
            self::POST_REFID => $link,
        ], $this->plugin->txt('block_specific_settings'));
    }

    /**
     * @inheritDoc
     */
    protected function getFormActionUrl(): string
    {
        return $this->ctrl->getFormActionByClass(xsrlIliasLinkBlockGUI::class, $this->getFormCmd());
    }

    /**
     * @inheritDoc
     */
    protected function getObject(): void
    {
        $ref_id = $this->getFormData()[self::POST_REFID];
        $this->block->setRefId(intval($ref_id));
    }
}
