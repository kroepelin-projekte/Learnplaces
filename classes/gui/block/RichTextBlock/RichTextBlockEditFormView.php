<?php

declare(strict_types=1);

namespace KPG\Learnplaces\gui\block\RichTextBlock;

use HTMLPurifier;
use HTMLPurifier_Config;
use ILIAS\UI\Implementation\Component\Input\Field\Section;
use ilLearnplacesPlugin;
use ilTextAreaInputGUI;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\gui\block\AbstractBlockEditFormView;
use KPG\Learnplaces\service\publicapi\model\BlockModel;
use KPG\Learnplaces\service\publicapi\model\RichTextBlockModel;
use xsrlRichTextBlockGUI;

/**
 * Class RichTextBlockEditFormView
 *
 * @package KPG\Learnplaces\gui\block\RichTextBlock
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
        $textarea = $this->field->hidden(/*$this->plugin->txt('rich_text_block_content')*/)
            #->withValue($this->block->getContent())
            ->withAdditionalOnLoadCode(fn ($id) => "document.getElementById('$id')?.setAttribute('id', 'textarea');")
            ->withRequired(true);

        return $this->field->section([
            self::POST_CONTENT => $textarea,
        ], $this->plugin->txt('block_specific_settings'));
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
        $http = PluginContainer::resolve('http');
        $form = $this->getForm()->withRequest($http->request());
        $inputs = $form->getInputs();
        $blockSpecificParts = $inputs[self::BLOCK_SPECIFIC_PARTS];
        $blockSpecificPartsInputs = $blockSpecificParts->getInputs();
        $contentInput = $blockSpecificPartsInputs[self::POST_CONTENT];
        $content = $contentInput->getValue();

        $content = $this->sanitizeHTML($content);

        $content = str_replace('`', "'", $content);

        $this->block->setContent($content ?? '');
    }

    /**
     * @param string $content
     * @return string
     */
    protected function sanitizeHTML(string $content): string
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.DefinitionImpl', null);
        $config->set('HTML.AllowedElements', 'p,br,strong,b,i,u,s,strike,em,span,ul,ol,li');
        $config->set('HTML.AllowedAttributes', ['style']);
        $purifier = new HTMLPurifier($config);

        return $purifier->purify($content);
    }
}
