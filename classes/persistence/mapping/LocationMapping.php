<?php

declare(strict_types=1);

namespace KPG\Lernplaces\persistence\mapping;

use KPG\Learnplaces\persistence\dto\Location;
use KPG\Learnplaces\service\publicapi\model\LocationModel;

/**
 * Trait LocationDtoMappingAware
 *
 * Adds functionality to map a location block model to a location block dto.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait LocationDtoMappingAware
{
    public function toDto(): Location
    {
        /**
         * @var LocationDtoMappingAware|LocationModel $this
         */
        $dto = new Location();
        $dto->setId($this->getId())
            ->setElevation($this->getElevation())
            ->setLatitude($this->getLatitude())
            ->setLongitude($this->getLongitude())
            ->setRadius($this->getRadius());

        return $dto;
    }
}

/**
 * Trait LocationModelMappingAware
 *
 * Adds functionality to map a location block dto to a location block model.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait LocationModelMappingAware
{
    public function toModel(): LocationModel
    {
        /**
         * @var LocationModelMappingAware|Location $this
         */
        $model = new LocationModel();
        $model
            ->setId($this->getId())
            ->setElevation($this->getElevation())
            ->setLatitude($this->getLatitude())
            ->setLongitude($this->getLongitude())
            ->setRadius($this->getRadius());

        return $model;
    }
}
