<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\gui\block\PictureBlock;

use ilFileInputGUI;
use ILIAS\FileUpload\MimeType;
use ILIAS\UI\Component\Input\Field\Section;
use ilLearnplacesUploadHandlerGUI;
use ilTextAreaInputGUI;
use ilTextInputGUI;
use SRAG\Learnplaces\container\PluginContainer;
use SRAG\Learnplaces\gui\block\AbstractBlockEditFormView;
use SRAG\Learnplaces\gui\helper\CommonControllerAction;
use SRAG\Learnplaces\service\publicapi\model\PictureBlockModel;
use SRAG\Learnplaces\service\publicapi\model\PictureUploadBlockModel;
use xsrlPictureBlockGUI;

use function version_compare;

use const ILIAS_VERSION_NUMERIC;

/**
 * Class PictureBlockEditFormView
 *
 * @package SRAG\Learnplaces\gui\block\PictureBlock
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class PictureBlockEditFormView extends AbstractBlockEditFormView
{
    public const POST_TITLE = 'post_title';
    public const POST_DESCRIPTION = 'post_description';
    public const POST_IMAGE = 'post_image';

    protected \SRAG\Learnplaces\service\publicapi\model\BlockModel $block;

    /**
     * PictureBlockEditFormView constructor.
     *
     * @param PictureBlockModel $model
     */
    public function __construct(PictureBlockModel $model)
    {
        parent::__construct($model);
    }

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

        $title = $field->text($this->plugin->txt('picture_block_enter_title'))->withMaxLength(256);
        $description = $field->textarea($this->plugin->txt('picture_block_enter_description'))->withMaxLimit(2000);

        $fileUpload = $field->file(new ilLearnplacesUploadHandlerGUI(), $this->plugin->txt('picture_block_select_picture'))
            ->withAcceptedMimeTypes([MimeType::IMAGE__JPEG, MimeType::IMAGE__PNG])
            ->withRequired($this->block->getId() <= 0);

        return $input->field()->section([
            self::POST_TITLE => $title,
            self::POST_DESCRIPTION => $description,
            self::POST_IMAGE => $fileUpload,
        ], $this->plugin->txt('block_specific_settings'));

        /*		$title = new ilTextInputGUI($this->plugin->txt('picture_block_enter_title'), self::POST_TITLE);
                $title->setMaxLength(256);
                $description = new ilTextAreaInputGUI($this->plugin->txt('picture_block_enter_description'), self::POST_DESCRIPTION);
                if(version_compare(ILIAS_VERSION_NUMERIC, '5.3') >= 0)
                    $description->setMaxNumOfChars(2000);

                $fileUpload = new ilFileInputGUI($this->plugin->txt('picture_block_select_picture'), self::POST_IMAGE);
                $fileUpload->setSuffixes(['jpg', 'png']);
                $fileUpload->setRequired($this->block->getId() <= 0);

                $this->addItem($title);
                $this->addItem($description);
                $this->addItem($fileUpload);*/
    }

    /**
     * @inheritDoc
     */
    protected function createValueArrayForSpecificFormParts(): array
    {
        $values = [
            self::POST_TITLE => $this->block->getTitle(),
            self::POST_DESCRIPTION => $this->block->getDescription(),
        ];

        return $values;
    }

    /**
     * @inheritDoc
     */
    protected function getFormActionUrl(): string
    {
        return $this->ctrl->getFormActionByClass(xsrlPictureBlockGUI::class, $this->getFormCmd());
    }

    /**
     * @inheritDoc
     */
    protected function getObject()
    {
        $this->block->setTitle($this->getFormData()[self::POST_TITLE]);
        $this->block->setDescription($this->getFormData()[self::POST_DESCRIPTION]);
    }
}
