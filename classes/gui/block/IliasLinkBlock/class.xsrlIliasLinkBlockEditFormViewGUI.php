<?php

declare(strict_types=1);

use ILIAS\UI\Implementation\Component\Input\Field\Section;
use SRAG\Learnplaces\gui\block\AbstractBlockEditFormView;
use SRAG\Learnplaces\service\publicapi\model\ILIASLinkBlockModel;

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

        $link = $field->link($this->plugin->txt('ilias_link_block_select_target'))
            ->withRequired(true);

        return $input->field()->section([
            self::POST_REFID => $link,
        ], $this->plugin->txt('block_specific_settings'));


        /*        $link = new ilLinkInputGUI($this->plugin->txt('ilias_link_block_select_target'), self::POST_REFID);
                $link->setInternalLinkFilterTypes(['RepositoryItem']);
                $link->setRequired(true);
                $link->setAllowedLinkTypes(ilLinkInputGUI::INT);

                $this->addItem($link);*/
    }

    /**
     * @inheritDoc
     */
    protected function createValueArrayForSpecificFormParts(): array
    {
        $values = [
            self::POST_REFID => $this->denormalizeRefId($this->block->getRefId()),
        ];

        return $values;
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
        //raw value looks like "xsrl|74"
        $rawValue = $this->getFormData()[self::POST_REFID];
        $delimiter = '|';
        $values = explode($delimiter, $rawValue);
        $lastElement = end($values);
        $this->block->setRefId(intval($lastElement));
    }

    /**
     * Denormalize the ref id to a notation for the ilLinkInputGUI which looks like 'xsrl|74'
     *
     * @param int $id   The ref id which should be transformed.
     *
     * @return string   The transformed ref id.
     */
    private function denormalizeRefId(int $id): string
    {
        $isReference = true;
        $type = ilObject::_lookupType($id, $isReference);
        $notation = "$type|$id";
        return $notation;
    }
}
