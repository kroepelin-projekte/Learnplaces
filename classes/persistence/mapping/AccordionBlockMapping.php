<?php

declare(strict_types=1);

namespace KPG\Lernplaces\persistence\mapping;

use KPG\Learnplaces\persistence\dto\AccordionBlock;
use KPG\Learnplaces\persistence\dto\Block;
use KPG\Learnplaces\service\publicapi\model\AccordionBlockModel;
use KPG\Learnplaces\service\publicapi\model\BlockModel;

/**
 * Trait AccordionBlockDtoMappingAware
 *
 * Defines the mapping of the accordion block model to the accordion block dto.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 *
 */
trait AccordionBlockDtoMappingAware
{
    public function toDto(): AccordionBlock
    {
        /**
         * @var AccordionBlockModel|AccordionBlockDtoMappingAware $this
         */

        $blockDtos = array_map(
            function (BlockModel $blockModel) {return $blockModel->toDto();},
            $this->getBlocks()
        );

        $dto = new AccordionBlock();
        $dto
            ->setTitle($this->getTitle())
            ->setExpand($this->isExpand())
            ->setBlocks($blockDtos);
        $this->fillBaseBlock($dto);

        return $dto;
    }

}

/**
 * Trait AccordionBlockModelMappingAware
 *
 * Defines the mapping of the accordion block dto to the accordion block model.
 *
 * @package KPG\Lernplaces\persistence\mapping
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait AccordionBlockModelMappingAware
{
    public function toModel(): AccordionBlockModel
    {
        /**
         * @var AccordionBlock|AccordionBlockModelMappingAware $this
         */

        $blockModels = array_map(
            function (Block $block) {return $block->toModel();},
            $this->getBlocks()
        );

        $model = new AccordionBlockModel();
        $model
            ->setTitle($this->getTitle())
            ->setExpand($this->isExpand())
            ->setBlocks($blockModels);
        $this->fillBaseBlock($model);

        return $model;
    }
}
