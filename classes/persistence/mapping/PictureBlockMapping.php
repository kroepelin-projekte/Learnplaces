<?php

declare(strict_types=1);

namespace KPG\Lernplaces\persistence\mapping;

use KPG\Learnplaces\persistence\dto\PictureBlock;
use KPG\Learnplaces\service\publicapi\model\PictureBlockModel;

/**
 * Trait PictureBlockDtoMappingAware
 *
 * Adds functionality to map a picture block model to a picture block dto.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait PictureBlockDtoMappingAware
{
    public function toDto(): PictureBlock
    {
        /**
         * @var PictureBlockDtoMappingAware|PictureBlockModel $this
         */
        $dto = new PictureBlock();
        $dto->setPicture(is_null($this->getPicture()) ? null : $this->getPicture()->toDto())
            ->setTitle($this->getTitle())
            ->setDescription($this->getDescription());
        $this->fillBaseBlock($dto);

        return $dto;
    }
}

/**
 * Trait PictureBlockModelMappingAware
 *
 * Adds functionality to map a picture block dto to a picture block model.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait PictureBlockModelMappingAware
{
    public function toModel(): PictureBlockModel
    {
        /**
         * @var PictureBlockModelMappingAware|PictureBlock $this
         */
        $model = new PictureBlockModel();
        $model
            ->setPicture(is_null($this->getPicture()) ? null : $this->getPicture()->toModel())
            ->setTitle($this->getTitle())
            ->setDescription($this->getDescription());
        $this->fillBaseBlock($model);

        return $model;
    }
}
