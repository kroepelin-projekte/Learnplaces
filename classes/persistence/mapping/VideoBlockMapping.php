<?php

declare(strict_types=1);

namespace KPG\Lernplaces\persistence\mapping;

use KPG\Learnplaces\persistence\dto\VideoBlock;
use KPG\Learnplaces\service\publicapi\model\VideoBlockModel;
use KPG\Learnplaces\service\filesystem\PathHelper;

/**
 * Trait VideoBlockDtoMappingAware
 *
 * Adds functionality to map a video block model to a video block dto.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait VideoBlockDtoMappingAware
{
    public function toDto(): VideoBlock
    {
        /**
         * @var VideoBlockDtoMappingAware|VideoBlockModel $this
         */
        $dto = new VideoBlock();
        $dto->setResourceId($this->getResourceId());

        $this->fillBaseBlock($dto);

        return $dto;
    }
}

/**
 * Trait VideoBlockModelMappingAware
 *
 * Adds functionality to map a video block dto to a video block model.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait VideoBlockModelMappingAware
{
    public function toModel(): VideoBlockModel
    {
        /**
         * @var VideoBlockModelMappingAware|VideoBlock $this
         */
        $model = new VideoBlockModel();
        $model->setResourceId($this->getResourceId());
        $this->fillBaseBlock($model);

        return $model;
    }
}
