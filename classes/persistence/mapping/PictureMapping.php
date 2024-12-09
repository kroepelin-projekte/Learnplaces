<?php

declare(strict_types=1);

namespace KPG\Lernplaces\persistence\mapping;

use KPG\Learnplaces\persistence\dto\Picture;
use KPG\Learnplaces\service\publicapi\model\PictureModel;
use KPG\Learnplaces\service\filesystem\PathHelper;

/**
 * Trait PictureDtoMappingAware
 *
 * Adds the functionality to map a picture model to a picture dto.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait PictureDtoMappingAware
{
    public function toDto(): Picture
    {
        /**
         * @var PictureModel|PictureDtoMappingAware $this
         */
        $dto = new Picture();
        $dto->setResourceId($this->getResourceId())
            ->setId($this->getId());

        return $dto;
    }
}

/**
 * Trait PictureModelMappingAware
 *
 * Adds the functionality to map a picture dto to a picture model.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait PictureModelMappingAware
{
    public function toModel(): PictureModel
    {
        /**
         * @var Picture|PictureDtoMappingAware $this
         */
        $model = new PictureModel();
        $model->setResourceId($this->getResourceId())
            ->setId($this->getId());

        return $model;
    }
}
