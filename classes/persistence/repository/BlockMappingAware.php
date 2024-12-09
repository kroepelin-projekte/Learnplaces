<?php

namespace KPG\Learnplaces\persistence\repository;

use KPG\Learnplaces\persistence\dto\Block;
use KPG\Learnplaces\persistence\entity\Visibility;

/**
 * Trait BlockMappingAware
 *
 * The Block mapping aware trait provides a convenience method to map the dto blocks to the entity blocks.
 *
 * @package KPG\Learnplaces\persistence\repository
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait BlockMappingAware
{
    /**
     * Maps a dto block to an entity block.
     *
     * @param Block             $block            The dto block which should be mapped to the entity block.
     *
     * @return \KPG\Learnplaces\persistence\entity\Block   The mapped entity block.
     */
    private function mapToBlockEntity(Block $block): \KPG\Learnplaces\persistence\entity\Block
    {
        $blockEntity = new \KPG\Learnplaces\persistence\entity\Block($block->getId());


        $visibility = Visibility::where(['name' => $block->getVisibility()])->first();
        $blockEntity
            ->setPkId($block->getId())
            ->setSequence($block->getSequence())
            ->setFkVisibility($visibility->getPkId());

        return $blockEntity;
    }
}
