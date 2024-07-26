<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\gui\block\RichTextBlock;

use ILIAS\UI\Implementation\Component\Input\Field\Section;
use ilLearnplacesPlugin;
use ilTextAreaInputGUI;
use SRAG\Learnplaces\gui\block\AbstractBlockEditFormView;
use SRAG\Learnplaces\service\publicapi\model\BlockModel;
use SRAG\Learnplaces\service\publicapi\model\RichTextBlockModel;
use xsrlRichTextBlockGUI;

/**
 * Class RichTextBlockEditFormView
 *
 * @package SRAG\Learnplaces\gui\block\RichTextBlock
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class RichTextBlockEditFormView extends AbstractBlockEditFormView
{
    public const POST_CONTENT = 'post_content';
    public const TYPE = 'richtext';
    /**
     * @var RichTextBlockModel $block
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
        // todo $ui = PluginContainer::resolve('ui'); ?
        global $DIC;
        $ui = $DIC->ui();
        $input = $ui->factory()->input();
        $field = $input->field();

        $textarea = $field->textarea($this->plugin->txt('rich_text_block_content'))
            ->withAdditionalOnLoadCode(fn ($id) => "document.getElementById('$id')?.setAttribute('id', 'textarea');")
            ->withRequired(true);

        return $input->field()->section([
            self::POST_CONTENT => $textarea,
        ], $this->plugin->txt('block_specific_settings'));

        /*        $textArea = new ilTextareaInputGUI($this->plugin->txt('rich_text_block_content'), self::POST_CONTENT);
                $textArea->setRequired(true);
                $textArea->setUseRte(true);
                $textArea->setRteTags([
                    'p',
                    'br',
                    'strong',
                    'b',
                    'i',
                    'u',
                    's',
                    'strike',
                    'em',
                    'span',
                ]);

                $textArea->disableButtons([
                    'charmap',
                    'undo',
                    'redo',
                    'justifyleft',
                    'justifycenter',
                    'justifyright',
                    'justifyfull',
                    'anchor',
                    'fullscreen',
                    'cut',
                    'copy',
                    'paste',
                    'pastetext',
                    'formatselect',
                    'bullist',
                    'hr',
                    'sub',
                    'sup',
                    'numlist',
                    'cite',
                    'removeformat',
                    'indent',
                    'outdent',
                    'link',
                    'unlink',
                    'code',
                    'pasteword',
                ]);
                $this->addItem($textArea);*/
    }

    /**
     * @inheritDoc
     */
    protected function createValueArrayForSpecificFormParts(): array
    {
        return [
            self::POST_CONTENT => $this->block->getContent(),
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getFormActionUrl(): string
    {
        return $this->ctrl->getFormActionByClass(xsrlRichTextBlockGUI::class, $this->getFormCmd());
    }

    /**
     * @inheritDoc
     */
    protected function getObject(): void
    {
        $inputs = $this->getForm()->getInputs();
        $bsp = $inputs[self::BLOCK_SPECIFIC_PARTS];
        $bsp_inputs = $bsp->getInputs();
        $post_content = $bsp_inputs[self::POST_CONTENT];
        $value = $post_content->getValue();

        $this->block->setContent($value ?? '');
    }
}
