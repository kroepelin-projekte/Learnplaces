<?php

declare(strict_types=1);

namespace KPG\Learnplaces\gui\block\PictureBlock;

use ILIAS\FileUpload\MimeType;
use ILIAS\UI\Component\Input\Field\Section;
use ilLearnplacesUploadHandlerGUI;
use KPG\Learnplaces\gui\block\AbstractBlockEditFormView;
use KPG\Learnplaces\service\publicapi\model\BlockModel;
use KPG\Learnplaces\service\publicapi\model\PictureBlockModel;
use xsrlPictureBlockGUI;

use function version_compare;
use const ILIAS_VERSION_NUMERIC;

/**
 * Class PictureBlockEditFormView
 *
 * @package KPG\Learnplaces\gui\block\PictureBlock
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class PictureBlockEditFormView extends AbstractBlockEditFormView
{
    public const POST_TITLE = 'post_title';
    public const POST_DESCRIPTION = 'post_description';
    public const POST_IMAGE = 'post_image';

    protected BlockModel $block;

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
        $title = $this->field->text($this->plugin->txt('picture_block_enter_title'))
            ->withValue($this->block->getTitle())
            ->withMaxLength(256);
        $description = $this->field->textarea($this->plugin->txt('picture_block_enter_description'))
            ->withValue($this->block->getDescription())
            ->withMaxLimit(2000);

        $fileUpload = $this->field->file(new ilLearnplacesUploadHandlerGUI(), $this->plugin->txt('picture_block_select_picture'))
            ->withAcceptedMimeTypes([MimeType::IMAGE__JPEG, MimeType::IMAGE__PNG])
            ->withRequired(true);

        if ($picture = $this->block->getPicture()) {
            $resourceId = $picture->getResourceId();
            if ($resource = $this->resourceStorage->manage()->find($resourceId)) {
                $src = $this->resourceStorage->consume()->src($resource)->getSrc();

                $fileUpload = $fileUpload
                    ->withValue([$resourceId])
                    ->withByLine(
                        "<img src='$src' als='Preview Image' style='width: 100%; max-width: 800px; padding: 10px 10px 10px 0;' />"
                    );
            }
        }

        return $this->field->section([
            self::POST_TITLE => $title,
            self::POST_DESCRIPTION => $description,
            self::POST_IMAGE => $fileUpload,
        ], $this->plugin->txt('block_specific_settings'));
    }

    /**
     * @inheritDoc
     * @throws \ilCtrlException
     */
    protected function getFormActionUrl(): string
    {
        return $this->ctrl->getFormActionByClass(xsrlPictureBlockGUI::class, $this->getFormCmd());
    }

    /**
     * @inheritDoc
     */
    protected function getObject(): void
    {
        $this->block->setTitle($this->getFormData()[self::POST_TITLE]);
        $this->block->setDescription($this->getFormData()[self::POST_DESCRIPTION]);
    }
}
