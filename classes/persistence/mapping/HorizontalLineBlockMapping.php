<?php

declare(strict_types=1);

namespace KPG\Lernplaces\persistence\mapping;

use KPG\Learnplaces\persistence\dto\HorizontalLineBlock;
use KPG\Learnplaces\service\publicapi\model\HorizontalLineBlockModel;

/**
 * Trait HorizontalLineBlockDtoMappingAware
 *
 * Adds functionality to map a horizontal line block model to a horizontal line block dto.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait HorizontalLineBlockDtoMappingAware
{
    public function toDto(): HorizontalLineBlock
    {
        /**
         * @var HorizontalLineBlockDtoMappingAware|HorizontalLineBlockModel $this
         */
        $dto = new HorizontalLineBlock();
        $this->fillBaseBlock($dto);

        return $dto;
    }
}

/**
 * Trait HorizontalLineBlockModelMappingAware
 *
 * Adds functionality to map a horizontal line block dto to a horizontal line block model.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait HorizontalLineBlockModelMappingAware
{
    public function toModel(): HorizontalLineBlockModel
    {
        /**
         * @var HorizontalLineBlockModelMappingAware|HorizontalLineBlock $this
         */
        $model = new HorizontalLineBlockModel();
        $this->fillBaseBlock($model);

        return $model;
    }
}
