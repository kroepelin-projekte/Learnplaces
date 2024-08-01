<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\gui\block;

use ilCtrl;
use ilFormSectionHeaderGUI;
use ILIAS\UI\Component\Input\Container\Form\Standard;
use ILIAS\UI\Factory;
use ILIAS\UI\Renderer;
use ilLearnplacesPlugin;
use ilPropertyFormGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use SRAG\Learnplaces\container\PluginContainer;
use SRAG\Learnplaces\gui\component\PlusView;
use SRAG\Learnplaces\gui\helper\CommonControllerAction;
use xsrlContentGUI;

/**
 * Class BlockAddFormGUI
 *
 * Provides the view with the block type selection.
 *
 * @package SRAG\Learnplaces\gui\block
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class BlockAddFormGUI
{
    public const POST_BLOCK_TYPES = 'post_block_types';
    public const POST_SEQUENCE = 'post_sequence';
    public const POST_VISIBILITY_SECTION = 'post_visibility_section';

    private ilLearnplacesPlugin $plugin;
    private ilCtrl $controlFlow;
    private bool $mapEnabled = true;
    private $accordionEnabled = true;
    private \ILIAS\UI\Component\Input\Container\Form\Standard $form;
    /** @var Factory $factory */
    protected object $factory;
    /** @var object|\ILIAS\UI\Component\Input\Field\Factory $field */
    protected object $field;
    /** @var Renderer $factory */
    private $renderer;

    /**
     * @param ilLearnplacesPlugin $plugin
     * @param ilCtrl $controlFlow
     */
    public function __construct(ilLearnplacesPlugin $plugin, ilCtrl $controlFlow)
    {
        $this->plugin = $plugin;
        $this->controlFlow = $controlFlow;
        $this->field = PluginContainer::resolve('field');
        $this->factory = PluginContainer::resolve('factory');
        $this->renderer = PluginContainer::resolve('renderer');
    }

    /**
     * @return void
     * @throws \ilCtrlException
     */
    public function initForm(): void
    {
        $this->controlFlow->saveParameterByClass(xsrlContentGUI::class, PlusView::POSITION_QUERY_PARAM);
        $this->controlFlow->saveParameterByClass(xsrlContentGUI::class, PlusView::ACCORDION_QUERY_PARAM);

        //create visibility
        $radioGroup = $this->field->radio($this->plugin->txt('block_type_title'), '')
            ->withOption((string) BlockType::PICTURE, $this->plugin->txt('block_picture'));

        if ($this->accordionEnabled) {
            $radioGroup = $radioGroup
                ->withOption((string) BlockType::ACCORDION, $this->plugin->txt('block_accordion'));
        }

        $radioGroup = $radioGroup
            ->withOption((string) BlockType::ILIAS_LINK, $this->plugin->txt('block_ilias_link'))
            ->withOption((string) BlockType::RICH_TEXT, $this->plugin->txt('block_rich_text'))
            ->withOption((string) BlockType::VIDEO, $this->plugin->txt('block_video'))
            ->withRequired(true);

        $visibilitySectionHeader = $this->field->section([
            self::POST_BLOCK_TYPES => $radioGroup
        ], $this->plugin->txt('block_add_header'));

        $this->form = $this->factory->input()->container()->form()->standard(
            $this->controlFlow->getFormActionByClass(xsrlContentGUI::class, CommonControllerAction::CMD_CREATE),
            [
                self::POST_VISIBILITY_SECTION => $visibilitySectionHeader
            ]
        );
    }

    /**
     * @param bool $mapEnabled
     * @return $this
     */
    public function setMapEnabled(bool $mapEnabled): BlockAddFormGUI
    {
        $this->mapEnabled = $mapEnabled;

        return $this;
    }

    /**
     * @param bool $accordionEnabled
     * @return $this
     */
    public function setAccordionEnabled(bool $accordionEnabled): BlockAddFormGUI
    {
        $this->accordionEnabled = $accordionEnabled;

        return $this;
    }

    /**
     * @return string
     * @throws \ilCtrlException
     */
    public function getHTML(): string
    {
        $this->initForm();
        return $this->renderer->render($this->form);
    }

    /**
     * @return Standard
     */
    public function getForm(): Standard
    {
        return $this->form;
    }
}
