<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\repository;

use arException;
use KPG\Learnplaces\persistence\dto\Learnplace;
use KPG\Learnplaces\persistence\dto\MapBlock;
use KPG\Learnplaces\persistence\entity\Block;
use KPG\Learnplaces\persistence\entity\Visibility;
use KPG\Learnplaces\persistence\repository\exception\EntityNotFoundException;

/**
 * Class MapBlockRepositoryImpl
 *
 * @package KPG\Learnplaces\persistence\repository
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class MapBlockRepositoryImpl implements MapBlockRepository
{
    use BlockMappingAware;
    use BlockConstraintAware;

    /**
     * @var LearnplaceConstraintRepository $learnplaceConstraintRepository
     */
    private $learnplaceConstraintRepository;

    /**
     * Constructor.
     *
     * @param LearnplaceConstraintRepository $learnplaceConstraintRepository
     */
    public function __construct(LearnplaceConstraintRepository $learnplaceConstraintRepository)
    {
        $this->learnplaceConstraintRepository = $learnplaceConstraintRepository;
    }

    /**
     * @inheritdoc
     */
    public function store(MapBlock $mapBlock): MapBlock
    {
        $storedBlock = ($mapBlock->getId() > 0) ? $this->update($mapBlock) : $this->create($mapBlock);
        $this->storeBlockConstraint($storedBlock);
        return $storedBlock;
    }

    private function create(MapBlock $mapBlock): MapBlock
    {
        /**
         * @var Block $block
         */
        $block = $this->mapToBlockEntity($mapBlock);
        $block->create();
        $mapBlockEntity = $this->mapToEntity($mapBlock);
        $mapBlockEntity->setFkBlockId($block->getPkId());
        $mapBlockEntity->create();
        return $this->mapToDTO($block);
    }

    private function update(MapBlock $mapBlock): MapBlock
    {
        $blockEntity = $this->mapToBlockEntity($mapBlock);
        $blockEntity->update();
        $mapBlockEntity = $this->mapToEntity($mapBlock);
        $mapBlockEntity->update();
        return $this->mapToDTO($blockEntity);
    }

    /**
     * @inheritdoc
     */
    public function findByBlockId(int $id): MapBlock
    {
        try {
            //check if the id belongs to a map block
            $mapBlock = \KPG\Learnplaces\persistence\entity\MapBlock::where(['fk_block_id' => $id])->first();
            if(is_null($mapBlock)) {
                throw new EntityNotFoundException("Map block with id \"$id\" was not found");
            }

            $block = Block::findOrFail($id);
            return $this->mapToDTO($block);
        } catch (arException $ex) {
            throw new EntityNotFoundException("Map block with id \"$id\" was not found", $ex);
        }
    }


    /**
     * @inheritdoc
     */
    public function findByObjectId(int $objectId): MapBlock
    {
        try {
            //check if the id belongs to a map block
            /**
             * @var \KPG\Learnplaces\persistence\entity\MapBlock $mapBlock
             */
            $mapBlock = \KPG\Learnplaces\persistence\entity\MapBlock::innerjoinAR(new Block(), 'fk_block_id', 'pk_id', ['pk_id', 'fk_learnplace_id'])
                ->innerjoinAR(new \KPG\Learnplaces\persistence\entity\Learnplace(), 'fk_learnplace_id', 'pk_id', ['pk_id', 'fk_object_id'], '=', true)
                ->where(['fk_object_id' => $objectId])->first();
            if(is_null($mapBlock)) {
                throw new EntityNotFoundException("No map block belongs to the object id \"$objectId\".");
            }

            $block = Block::findOrFail($mapBlock->getFkBlockId());
            return $this->mapToDTO($block);
        } catch (arException $ex) {
            throw new EntityNotFoundException("No map block belongs to the object id \"$objectId\".", $ex);
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id)
    {
        try {
            $uploadBlock = \KPG\Learnplaces\persistence\entity\MapBlock::where(['fk_block_id' => $id])->first();
            if(!is_null($uploadBlock)) {
                $uploadBlock->delete();
            }

            Block::findOrFail($id)->delete();
        } catch (arException $ex) {
            throw new EntityNotFoundException("Map block with id \"$id\" not found", $ex);
        }
    }


    /**
     * @inheritdoc
     */
    public function findByLearnplace(Learnplace $learnplace): array
    {
        /**
         * @var Block[] $blocks
         */
        $blocks = Block::innerjoinAR(new \KPG\Learnplaces\persistence\entity\MapBlock(), 'pk_id', 'fk_block_id', ['fk_block_id'])
            ->where(['fk_learnplace_id' => $learnplace->getId()])->get();

        $mappedBlocks = [];

        //fetch all specific blocks and map them to DTOs
        foreach ($blocks as $block) {
            $mappedBlocks[] = $this->mapToDTO($block);
        }

        return $mappedBlocks;
    }

    private function mapToDTO(Block $block): MapBlock
    {

        $mapBlock = new MapBlock();
        /**
         * @var Visibility $visibility
         */
        $visibility = Visibility::findOrFail($block->getFkVisibility());

        $mapBlock
            ->setId($block->getPkId())
            ->setSequence($block->getSequence())
            ->setVisibility($visibility->getName());

        return $mapBlock;

    }

    private function mapToEntity(MapBlock $mapBlock): \KPG\Learnplaces\persistence\entity\MapBlock
    {

        /**
         * @var \KPG\Learnplaces\persistence\entity\MapBlock $activeRecord
         */
        $activeRecord = \KPG\Learnplaces\persistence\entity\MapBlock::where(['fk_block_id' => $mapBlock->getId()])->first();

        if(is_null($activeRecord)) {
            $activeRecord = new \KPG\Learnplaces\persistence\entity\MapBlock();
            $activeRecord->setFkBlockId($mapBlock->getId());
        }

        return $activeRecord;
    }

}
