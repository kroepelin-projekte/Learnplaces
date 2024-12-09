<?php

declare(strict_types=1);

namespace KPG\Lernplaces\persistence\mapping;

use KPG\Learnplaces\persistence\dto\RichTextBlock;
use KPG\Learnplaces\service\publicapi\model\RichTextBlockModel;

/**
 * Trait RichTextBlockDtoMappingAware
 *
 * Adds functionality to map a rich text block model to a rich text block dto.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait RichTextBlockDtoMappingAware
{
    public function toDto(): RichTextBlock
    {
        /**
         * @var RichTextBlockDtoMappingAware|RichTextBlockModel $this
         */
        $dto = new RichTextBlock();
        $dto->setContent($this->getContent());
        $this->fillBaseBlock($dto);

        return $dto;
    }
}

/**
 * Trait RichTextBlockModelMappingAware
 *
 * Adds functionality to map a rich text block dto to a rich text block model.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait RichTextBlockModelMappingAware
{
    public function toModel(): RichTextBlockModel
    {
        /**
         * @var RichTextBlockModelMappingAware|RichTextBlock $this
         */
        $model = new RichTextBlockModel();
        $model->setContent($this->getContent());
        $this->fillBaseBlock($model);

        return $model;
    }
}
