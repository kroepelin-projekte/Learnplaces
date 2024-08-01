<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\gui\block;

use ilCtrl;
use ilFormSectionHeaderGUI;
use ilHiddenInputGUI;
use ILIAS\HTTP\Services;
use ILIAS\UI\Factory;
use ILIAS\UI\Renderer;
use ilLearnplacesPlugin;
use ilLinkButton;
use ilPropertyFormGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use SRAG\Learnplaces\container\PluginContainer;
use SRAG\Learnplaces\gui\block\RichTextBlock\RichTextBlockEditFormView;
use SRAG\Learnplaces\gui\exception\ValidationException;
use SRAG\Learnplaces\gui\helper\CommonControllerAction;
use SRAG\Learnplaces\service\publicapi\model\BlockModel;
use SRAG\Learnplaces\util\Visibility;

use function array_merge_clobber;
use function in_array;
use function intval;

/**
 * Class xsrlAbstractBlockFormGUI
 *
 * @package SRAG\Learnplaces\gui\block
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
abstract class AbstractBlockEditFormView
{
    public const POST_VISIBILITY = "post_visibility";
    public const POST_ID = 'post_id';
    public const VISIBILITY_SECTION = 'visibilitySection';
    public const BLOCK_SPECIFIC_PARTS = 'blockSpecificParts';

    private static $validVisibilities = [
        Visibility::ALWAYS,
        Visibility::NEVER,
        Visibility::ONLY_AT_PLACE,
        Visibility::AFTER_VISIT_PLACE,
    ];

    protected BlockModel $block;
    protected ilLearnplacesPlugin $plugin;
    /** @var ilCtrl $ctrl */
    protected object $ctrl;
    /** @var Services $http */
    protected object $http;
    protected \ILIAS\UI\Component\Input\Container\Form\Standard $form;
    /** @var mixed|null */
    protected $form_data;
    /** @var object \ILIAS\ResourceStorage\Services  */
    protected object $resourceStorage;
    /** @var Factory $factory */
    protected object $factory;
    /** @var Renderer $renderer */
    protected object $renderer;
    /** @var object|\ILIAS\UI\Component\Input\Field\Factory $field */
    protected object $field;

    /**
     * AbstractBlockFormView constructor.
     *
     * @param BlockModel $setting
     */
    public function __construct(BlockModel $setting)
    {
        $this->block = $setting;
        $this->ctrl = PluginContainer::resolve('ilCtrl');
        $this->http = $http = PluginContainer::resolve('http');
        $this->plugin = ilLearnplacesPlugin::getInstance();
        $this->resourceStorage = PluginContainer::resolve('resourceStorage');
        $this->factory = PluginContainer::resolve('factory');
        $this->renderer = PluginContainer::resolve('renderer');
        $this->field = $this->factory->input()->field();

        $this->initForm();
    }

    /**
     * @return void
     */
    private function initForm(): void
    {
        $post_id = $this->field->hidden()
            ->withValue($this->block->getId())
            ->withRequired(true);

        //create visibility
        $radioGroup = $this->field->radio($this->plugin->txt('visibility_title'))
            ->withOption(Visibility::ALWAYS, $this->plugin->txt('visibility_always'))
            ->withOption(Visibility::AFTER_VISIT_PLACE, $this->plugin->txt('visibility_after_visit_place'))
            ->withOption(Visibility::ONLY_AT_PLACE, $this->plugin->txt('visibility_only_at_place'))
            ->withOption(Visibility::NEVER, $this->plugin->txt('visibility_never'))
            ->withValue($this->block->getVisibility())
            ->withRequired(true);

        $visibilitySectionHeader = $this->field->section([
            self::POST_ID => $post_id,
            self::POST_VISIBILITY => $radioGroup
        ], $this->plugin->txt('common_visibility'));

        $formParts[self::POST_ID] = $post_id;
        $formParts[self::VISIBILITY_SECTION] = $visibilitySectionHeader;

        if($this->hasBlockSpecificParts()) {
            //create block specific settings header
            $blockSpecificParts = $this->initBlockSpecificForm();
            $formParts[self::BLOCK_SPECIFIC_PARTS] = $blockSpecificParts;
        }

        $this->form = $this->factory->input()->container()->form()->standard($this->getFormActionUrl(), $formParts);
    }

    /**
     * If this method returns true a block specific config section is rendered.
     *
     * @return bool
     */
    abstract protected function hasBlockSpecificParts(): bool;

    /**
     * Init block specific gui settings with $this->addItem().
     * This method is only called if the hasBlockSpecificParts returned true.
     *
     * @return void
     */
    abstract protected function initBlockSpecificForm();

    /**
     * Defines the form action url.
     *
     * @return string   The form action url.
     */
    abstract protected function getFormActionUrl(): string;

    /**
     * Fill the data of the block gui into the specific block object.
     *
     * @return void
     */
    abstract protected function getObject();

    /**
     * @return BlockModel
     */
    public function getBlockModel(): BlockModel
    {
        $form = $this->form;
        $form = $form->withRequest($this->http->request());
        $this->form_data = $form->getData();

        if($form->getError()) {
            throw new ValidationException('Received block content is not valid and got rejected.');
        }

        $visibility = $this->form_data[self::VISIBILITY_SECTION][self::POST_VISIBILITY];
        if(!in_array($visibility, self::$validVisibilities)) {
            throw new ValidationException('Invalid visibility received!');
        }

        $this->getObject();
        $this->block->setVisibility($visibility);
        $this->block->setId(intval($this->form_data[self::POST_ID]));
        return $this->block;
    }

    /**
     * @return string
     */
    public function getHTML(): string
    {
        return $this->renderer->render($this->form);
    }

    /**
     * @return \ILIAS\UI\Component\Input\Container\Form\Standard
     */
    public function getForm(): \ILIAS\UI\Component\Input\Container\Form\Standard
    {
        return $this->form;
    }

    /**
     * @return array
     */
    public function getFormData(): array
    {
        return $this->form_data[self::BLOCK_SPECIFIC_PARTS];
    }

    /**
     * @return void
     */
    public function setValuesByPost(): void
    {
        $this->form = $this->form->withRequest($this->http->request());
    }

    /**
     * @return string
     */
    protected function getFormCmd(): string
    {
        $cmd = CommonControllerAction::CMD_CREATE;
        if($this->block->getId() > 0) {
            $cmd = CommonControllerAction::CMD_UPDATE;
        }
        return $cmd;
    }
}
