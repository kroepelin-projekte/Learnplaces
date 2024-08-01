<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\gui\block\AccordionBlock;

use ilCheckboxInputGUI;
use ILIAS\UI\Implementation\Component\Input\Field\Section;
use ilTextInputGUI;
use SRAG\Learnplaces\container\PluginContainer;
use SRAG\Learnplaces\gui\block\AbstractBlockEditFormView;
use SRAG\Learnplaces\gui\helper\CommonControllerAction;
use SRAG\Learnplaces\service\publicapi\model\BlockModel;
use SRAG\Learnplaces\service\publicapi\model\AccordionBlockModel;
use xsrlAccordionBlockGUI;

use function boolval;

/**
 * Class AccordionBlockEditFormView
 *
 * @package SRAG\Learnplaces\gui\block\AccordionBlock
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class AccordionBlockEditFormView extends AbstractBlockEditFormView
{
    public const POST_TITLE = 'post_title';
    public const POST_EXPAND = 'post_expand';
    /**
     * @var AccordionBlockModel $block
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
        $title = $this->field->text($this->plugin->txt('accordion_block_title'))
            ->withValue($this->block->getTitle())
            ->withMaxLength(256)
            ->withRequired(true);
        $expand = $this->field->checkbox($this->plugin->txt('accordion_block_expand'))
            ->withValue($this->block->isExpand());

        return $this->field->section([
            self::POST_TITLE => $title,
            self::POST_EXPAND => $expand,
        ], $this->plugin->txt('block_specific_settings'));
    }

    /**
     * @inheritDoc
     */
    protected function getFormActionUrl(): string
    {
        return $this->ctrl->getFormActionByClass(xsrlAccordionBlockGUI::class, $this->getFormCmd());
    }

    /**
     * @inheritDoc
     */
    protected function getObject(): void
    {
        $this->block->setTitle($this->getFormData()[self::POST_TITLE]);
        $this->block->setExpand(boolval($this->getFormData()[self::POST_EXPAND]));
    }
}
