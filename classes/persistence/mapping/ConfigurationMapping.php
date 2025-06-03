<?php

declare(strict_types=1);

namespace KPG\Lernplaces\persistence\mapping;

use KPG\Learnplaces\persistence\dto\Configuration;
use KPG\Learnplaces\service\publicapi\model\ConfigurationModel;

/**
 * Trait ConfigurationDtoMappingAware
 *
 * Adds functionality to map a configuration block model to a configuration block dto.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait ConfigurationDtoMappingAware
{
    public function toDto(): Configuration
    {
        /**
         * @var ConfigurationDtoMappingAware|ConfigurationModel $this
         */
        $dto = new Configuration();
        $dto->setId($this->getId())
            ->setOnline($this->isOnline())
            ->setDefaultVisibility($this->getDefaultVisibility())
            ->setMapZoomLevel($this->getMapZoomLevel())
            ->setTags($this->getTags())
            ->setQrCodeToken($this->getQrCodeToken());

        return $dto;
    }
}

/**
 * Trait ConfigurationModelMappingAware
 *
 * Adds functionality to map a configuration block dto to a configuration block model.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait ConfigurationModelMappingAware
{
    public function toModel(): ConfigurationModel
    {
        /**
         * @var ConfigurationModelMappingAware|Configuration $this
         */
        $model = new ConfigurationModel();
        $model->setId($this->getId())
            ->setOnline($this->isOnline())
            ->setDefaultVisibility($this->getDefaultVisibility())
            ->setMapZoomLevel($this->getMapZoomLevel())
            ->setTags($this->getTags())
            ->setQrCodeToken($this->getQrCodeToken());

        return $model;
    }
}
