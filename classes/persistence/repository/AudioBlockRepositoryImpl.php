<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\repository;

use arException;
use KPG\Learnplaces\persistence\dto\AudioBlock;
use KPG\Learnplaces\persistence\dto\Learnplace;
use KPG\Learnplaces\persistence\entity\Block;
use KPG\Learnplaces\persistence\entity\Visibility;
use KPG\Learnplaces\persistence\repository\exception\EntityNotFoundException;

use function is_null;

/**
 * Class AudioBlockRepositoryImpl
 *
 * @package KPG\Learnplaces\persistence\repository
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class AudioBlockRepositoryImpl implements AudioBlockRepository
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
    public function store(AudioBlock $audioBlock): AudioBlock
    {
        $storedBlock = ($audioBlock->getId() > 0) ? $this->update($audioBlock) : $this->create($audioBlock);
        $this->storeBlockConstraint($storedBlock);
        return $storedBlock;
    }

    private function create(AudioBlock $audioBlock): AudioBlock
    {
        /**
         * @var Block $block
         */
        $block = $this->mapToBlockEntity($audioBlock);
        $block->create();
        $audioBlockEntity = $this->mapToEntity($audioBlock);
        $audioBlockEntity->setFkBlockId($block->getPkId());
        $audioBlockEntity->create();
        return $this->mapToDTO($block, $audioBlockEntity);
    }

    private function update(AudioBlock $audioBlock): AudioBlock
    {
        $blockEntity = $this->mapToBlockEntity($audioBlock);
        $blockEntity->update();
        $audioBlockEntity = $this->mapToEntity($audioBlock);
        $audioBlockEntity->update();
        return $this->mapToDTO($blockEntity, $audioBlockEntity);
    }

    /**
     * @inheritdoc
     */
    public function findByBlockId(int $id): AudioBlock
    {
        try {
            $block = Block::findOrFail($id);
            $audioBlock = \KPG\Learnplaces\persistence\entity\AudioBlock::where(['fk_block_id' => $id])->first();
            if(is_null($audioBlock)) {
                throw new EntityNotFoundException("Audio block with id \"$id\" was not found");
            }
            return $this->mapToDTO($block, $audioBlock);
        } catch (arException $ex) {
            throw new EntityNotFoundException("Audio block with id \"$id\" was not found", $ex);
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id)
    {
        try {
            $uploadBlock = \KPG\Learnplaces\persistence\entity\AudioBlock::where(['fk_block_id' => $id])->first();
            if(!is_null($uploadBlock)) {
                $uploadBlock->delete();
            }

            Block::findOrFail($id)->delete();
        } catch (arException $ex) {
            throw new EntityNotFoundException("Audio block with id \"$id\" not found", $ex);
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
        $blocks = Block::innerjoinAR(new \KPG\Learnplaces\persistence\entity\AudioBlock(), 'pk_id', 'fk_block_id')
            ->where(['fk_learnplace_id' => $learnplace->getId()])->get();

        $mappedBlocks = [];

        //fetch all specific blocks and map them to DTOs
        foreach ($blocks as $block) {
            $audioBlockEntity = \KPG\Learnplaces\persistence\entity\AudioBlock::where(['fk_block_id' => $block->getPkId()])->first();
            $mappedBlocks[] = $this->mapToDTO($block, $audioBlockEntity);
        }

        return $mappedBlocks;
    }

    private function mapToDTO(Block $block, \KPG\Learnplaces\persistence\entity\AudioBlock $audioBlockEntity): AudioBlock
    {

        $audioBlock = new AudioBlock();
        /**
         * @var Visibility $visibility
         */
        $visibility = Visibility::findOrFail($block->getFkVisibility());

        $audioBlock
            ->setPath($audioBlockEntity->getPath())
            ->setId($block->getPkId())
            ->setSequence($block->getSequence())
            ->setConstraint($this->learnplaceConstraintRepository->findByBlockId($block->getPkId()))
            ->setVisibility($visibility->getName());

        return $audioBlock;

    }

    private function mapToEntity(AudioBlock $audioBlock): \KPG\Learnplaces\persistence\entity\AudioBlock
    {

        /**
         * @var \KPG\Learnplaces\persistence\entity\AudioBlock $activeRecord
         */
        $activeRecord = \KPG\Learnplaces\persistence\entity\AudioBlock::where(['fk_block_id' => $audioBlock->getId()])->first();

        if(is_null($activeRecord)) {
            $activeRecord = new \KPG\Learnplaces\persistence\entity\AudioBlock();
            $activeRecord->setFkBlockId($audioBlock->getId());
        }

        $activeRecord->setPath($audioBlock->getPath());
        return $activeRecord;
    }
}
