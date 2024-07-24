<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\gui\block\AccordionBlock;

use ilCheckboxInputGUI;
use ILIAS\UI\Implementation\Component\Input\Field\Section;
use ilTextInputGUI;
use SRAG\Learnplaces\gui\block\AbstractBlockEditFormView;
use SRAG\Learnplaces\gui\helper\CommonControllerAction;
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
    protected \SRAG\Learnplaces\service\publicapi\model\BlockModel $block;

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
        // todo $ui = PluginContainer::resolve('ui'); ?
        global $DIC;
        $ui = $DIC->ui();
        $input = $ui->factory()->input();
        $field = $input->field();

        $title = $field->text($this->plugin->txt('accordion_block_title'))->withMaxLength(256)->withRequired(true);
        $expand = $field->checkbox($this->plugin->txt('accordion_block_expand'))->withValue(true);

        return $input->field()->section([
            self::POST_TITLE => $title,
            self::POST_EXPAND => $expand,
        ], $this->plugin->txt('block_specific_settings'));

        /*        $title = new ilTextInputGUI($this->plugin->txt('accordion_block_title'), self::POST_TITLE);
                $title->setMaxLength(256);
                $title->setRequired(true);

                $expand = new ilCheckboxInputGUI($this->plugin->txt('accordion_block_expand'), self::POST_EXPAND);
                $expand->setChecked(true);

                $this->addItem($title);
                $this->addItem($expand);*/
    }

    /**
     * @inheritDoc
     */
    protected function createValueArrayForSpecificFormParts(): array
    {
        return [
            self::POST_TITLE => $this->block->getTitle(),
            self::POST_EXPAND => $this->block->isExpand(),
        ];
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
    protected function getObject()
    {
        $this->block->setTitle($this->getFormData()[self::POST_TITLE]);
        $this->block->setExpand(boolval($this->getFormData()[self::POST_EXPAND]));
    }
}
