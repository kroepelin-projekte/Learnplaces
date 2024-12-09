<?php

declare(strict_types=1);

namespace KPG\Lernplaces\persistence\mapping;

use KPG\Learnplaces\persistence\dto\AudioBlock;
use KPG\Learnplaces\service\publicapi\model\AudioBlockModel;

/**
 * Trait AudioBlockDtoMappingAware
 *
 * Adds functionality to map an audio block model to an audio block dto.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait AudioBlockDtoMappingAware
{
    public function toDto(): AudioBlock
    {
        /**
         * @var AudioBlockDtoMappingAware|AudioBlockModel $this
         */
        $dto = new AudioBlock();
        $dto->setPath($this->getPath());
        $this->fillBaseBlock($dto);

        return $dto;
    }
}

/**
 * Trait AudioBlockModelMappingAware
 *
 * Adds functionality to map an audio block dto to an audio block model.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait AudioBlockModelMappingAware
{
    public function toModel(): AudioBlockModel
    {
        /**
         * @var AudioBlockModelMappingAware|AudioBlock $this
         */
        $model = new AudioBlockModel();
        $model->setPath($this->getPath());
        $this->fillBaseBlock($model);

        return $model;
    }
}
