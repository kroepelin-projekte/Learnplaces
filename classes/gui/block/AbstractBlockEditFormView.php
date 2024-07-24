<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\gui\block;

use ilCtrl;
use ilFormSectionHeaderGUI;
use ilHiddenInputGUI;
use ILIAS\HTTP\Services;
use ilLearnplacesPlugin;
use ilLinkButton;
use ilPropertyFormGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use SRAG\Learnplaces\container\PluginContainer;
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
    /** @var $ctrl ilCtrl */
    protected $ctrl;
    /** @var Services $http */
    private object $http;
    protected \ILIAS\UI\Component\Input\Container\Form\Standard $form;
    /**
     * @var mixed|null
     */
    private $form_data;

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
        $this->initForm();
    }

    private function initForm(): void
    {
        // todo entfernen
        global $DIC;
        $this->ui = $DIC->ui();
        $input = $DIC->ui()->factory()->input();
        $field = $input->field();

        $post_id = $field->hidden()
            ->withValue($this->block->getId())
            ->withRequired(true);

        //create visibility
        $radioGroup = $field->radio($this->plugin->txt('visibility_title'))
            ->withOption(Visibility::ALWAYS, $this->plugin->txt('visibility_always'))
            ->withOption(Visibility::AFTER_VISIT_PLACE, $this->plugin->txt('visibility_after_visit_place'))
            ->withOption(Visibility::ONLY_AT_PLACE, $this->plugin->txt('visibility_only_at_place'))
            ->withOption(Visibility::NEVER, $this->plugin->txt('visibility_never'))
            ->withValue($this->block->getVisibility())
            ->withRequired(true);

        $visibilitySectionHeader = $input->field()->section([
            self::POST_ID => $post_id,
            self::POST_VISIBILITY => $radioGroup
        ], $this->plugin->txt('common_visibility'));

        /*		$this->setFormAction($this->getFormActionUrl());
                $this->setPreventDoubleSubmission(true);
                $this->setShowTopButtons(false);

                $id = new ilHiddenInputGUI(self::POST_ID);
                $id->setRequired(true);
                $this->addItem($id);

                //create visibility
                $visibilitySectionHeader = new ilFormSectionHeaderGUI();
                $visibilitySectionHeader->setTitle($this->plugin->txt('common_visibility'));
                $this->addItem($visibilitySectionHeader);

                $radioGroup = new ilRadioGroupInputGUI($this->plugin->txt('visibility_title'), self::POST_VISIBILITY);
                $radioGroup->addOption(new ilRadioOption($this->plugin->txt('visibility_always'), Visibility::ALWAYS));
                $radioGroup->addOption(new ilRadioOption($this->plugin->txt('visibility_after_visit_place'), Visibility::AFTER_VISIT_PLACE));
                $radioGroup->addOption(new ilRadioOption($this->plugin->txt('visibility_only_at_place'), Visibility::ONLY_AT_PLACE));
                $radioGroup->addOption(new ilRadioOption($this->plugin->txt('visibility_never'), Visibility::NEVER));
                $radioGroup->setRequired(true);
                $this->addItem($radioGroup);*/

        $formParts[self::POST_ID] = $post_id;
        $formParts[self::VISIBILITY_SECTION] = $visibilitySectionHeader;

        if($this->hasBlockSpecificParts()) {
            //create block specific settings header
            /*			$visibilitySectionHeader = new ilFormSectionHeaderGUI();
                        $visibilitySectionHeader->setTitle($this->plugin->txt('block_specific_settings'));
                        $this->addItem($visibilitySectionHeader);*/
            $blockSpecificParts = $this->initBlockSpecificForm();
            $formParts[self::BLOCK_SPECIFIC_PARTS] = $blockSpecificParts;
        }

        $this->form = $input->container()->form()->standard($this->getFormActionUrl(), $formParts);


        #$this->initButtons();
    }

    /*	private function initButtons() {
            if($this->block->getId() > 0) {
                $this->addCommandButton(CommonControllerAction::CMD_UPDATE, $this->plugin->txt('common_save'));
                $this->addCommandButton(CommonControllerAction::CMD_CANCEL, $this->plugin->txt('common_cancel'));
                return;
            }

            $this->addCommandButton(CommonControllerAction::CMD_CREATE, $this->plugin->txt('common_create'));
            $this->addCommandButton(CommonControllerAction::CMD_CANCEL, $this->plugin->txt('common_cancel'));
        }*/


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
     * Creates an array for the block specific form parts.
     *
     * Example:
     * [
     *      'POST_TEXT_INPUT' => 'Some text for this field',
     * ]
     *
     * @return array
     */
    abstract protected function createValueArrayForSpecificFormParts(): array;

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
     * Fills the form with the data of the block model.
     *
     * @return void
     */
    public function fillForm()
    {
        /*		$values = [
                    self::POST_ID => $this->block->getId(),
                    self::POST_VISIBILITY => $this->block->getVisibility(),
                ];

                $allValues = array_merge($values, $this->createValueArrayForSpecificFormParts());

                $this->setValuesByArray($allValues);*/
    }

    /**
     * @return string
     */
    public function getHTML(): string
    {
        return $this->ui->renderer()->render($this->form);
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
