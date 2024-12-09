<?php

declare(strict_types=1);

namespace KPG\Lernplaces\persistence\mapping;

use KPG\Learnplaces\persistence\dto\PictureUploadBlock;
use KPG\Learnplaces\service\publicapi\model\PictureUploadBlockModel;

/**
 * Trait PictureUploadBlockDtoMappingAware
 *
 * Adds functionality to map a picture upload block model to a picture upload block dto.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait PictureUploadBlockDtoMappingAware
{
    public function toDto(): PictureUploadBlock
    {
        /**
         * @var PictureUploadBlockDtoMappingAware|PictureUploadBlockModel $this
         */
        $dto = new PictureUploadBlock();
        $this->fillBaseBlock($dto);

        return $dto;
    }
}

/**
 * Trait PictureUploadBlockMappingAware
 *
 * Adds functionality to map a picture upload block dto to a picture upload block model.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait PictureUploadBlockMappingAware
{
    public function toModel(): PictureUploadBlockModel
    {
        /**
         * @var PictureUploadBlockMappingAware|PictureUploadBlock $this
         */
        $model = new PictureUploadBlockModel();
        $this->fillBaseBlock($model);

        return $model;
    }
}
