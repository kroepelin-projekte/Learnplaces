<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\repository;

use arException;
use InvalidArgumentException;
use KPG\Learnplaces\persistence\dto\AccordionBlock;
use KPG\Learnplaces\persistence\dto\Learnplace;
use KPG\Learnplaces\persistence\entity\AccordionBlockMember;
use KPG\Learnplaces\persistence\entity\Block;
use KPG\Learnplaces\persistence\entity\Visibility;
use KPG\Learnplaces\persistence\repository\exception\EntityNotFoundException;
use KPG\Learnplaces\persistence\repository\util\BlockAccumulator;

class AccordionBlockRepositoryImpl implements AccordionBlockRepository
{
    use BlockMappingAware;
    use BlockConstraintAware;

    /**
     * @var LearnplaceConstraintRepository $learnplaceConstraintRepository
     */
    private $learnplaceConstraintRepository;
    /**
     * @var BlockAccumulator $blockAccumulator
     */
    private $blockAccumulator;


    /**
     * AccordionBlockRepositoryImpl constructor.
     *
     * @param LearnplaceConstraintRepository $learnplaceConstraintRepository
     */
    public function __construct(LearnplaceConstraintRepository $learnplaceConstraintRepository)
    {
        $this->learnplaceConstraintRepository = $learnplaceConstraintRepository;
    }


    /**
     * PostConstruct to break the circular dependency with the block accumulator
     * Will be automatically called by de DIC.
     *
     * @param BlockAccumulator $blockAccumulator
     */
    public function postConstruct(BlockAccumulator $blockAccumulator)
    {
        $this->blockAccumulator = $blockAccumulator;
    }


    /**
     * @inheritdoc
     */
    public function store(AccordionBlock $accordionBlock): AccordionBlock
    {
        $storedBlock = ($accordionBlock->getId() > 0) ? $this->update($accordionBlock) : $this->create($accordionBlock);
        $this->storeBlockConstraint($storedBlock);
        return $storedBlock;
    }

    private function create(AccordionBlock $accordionBlock): AccordionBlock
    {
        /**
         * @var Block $block
         */
        $block = $this->mapToBlockEntity($accordionBlock);
        $block->create();
        $accordionBlockEntity = $this->mapToEntity($accordionBlock);
        $accordionBlockEntity->setFkBlockId($block->getPkId());
        $accordionBlockEntity->create();
        $this->storeBlockRelationsAndSequence($accordionBlockEntity->getPkId(), $accordionBlock->getBlocks());
        $dto = $this->mapToDTO($block, $accordionBlockEntity);
        return $dto;
    }

    private function update(AccordionBlock $accordionBlock): AccordionBlock
    {
        $blockEntity = $this->mapToBlockEntity($accordionBlock);
        $blockEntity->update();
        $accordionBlockEntity = $this->mapToEntity($accordionBlock);
        $accordionBlockEntity->update();
        $this->storeBlockRelationsAndSequence($accordionBlockEntity->getPkId(), $accordionBlock->getBlocks());
        $dto = $this->mapToDTO($blockEntity, $accordionBlockEntity);
        return $dto;
    }

    /**
     * @inheritdoc
     */
    public function findByBlockId(int $id): AccordionBlock
    {
        try {
            $block = Block::findOrFail($id);
            $accordionBlock = \KPG\Learnplaces\persistence\entity\AccordionBlock::where(['fk_block_id' => $id])->first();
            return $this->mapToDTO($block, $accordionBlock);
        } catch (arException $ex) {
            throw new EntityNotFoundException("Accordion block with id \"$id\" was not found", $ex);
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id)
    {
        try {
            /**
             * @var \KPG\Learnplaces\persistence\entity\AccordionBlock $accordionBlock
             */
            $accordionBlock = \KPG\Learnplaces\persistence\entity\AccordionBlock::where(['fk_block_id' => $id])->first();
            if(is_null($accordionBlock)) {
                throw new EntityNotFoundException("Accordion block with id \"$id\" not found");
            }

            $accordionBlock->delete();

            /**
             * @var AccordionBlockMember[] $relations
             */
            $relations = AccordionBlockMember::where(['fk_accordion_block' => $accordionBlock->getPkId()])->get();
            foreach ($relations as $relation) {
                $relation->delete();
            }

            Block::findOrFail($id)->delete();
        } catch (arException $ex) {
            throw new EntityNotFoundException("Accordion block with id \"$id\" not found", $ex);
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
        $blocks = Block::innerjoinAR(new \KPG\Learnplaces\persistence\entity\AccordionBlock(), 'pk_id', 'fk_block_id')
            ->where(['fk_learnplace_id' => $learnplace->getId()])->get();

        $mappedBlocks = [];

        //fetch all specific blocks and map them to DTOs
        foreach ($blocks as $block) {
            $accordionBlock = \KPG\Learnplaces\persistence\entity\AccordionBlock::where(['fk_block_id' => $block->getPkId()])->first();
            $mappedBlocks[] = $this->mapToDTO($block, $accordionBlock);
        }

        return $mappedBlocks;
    }

    private function mapToDTO(Block $block, \KPG\Learnplaces\persistence\entity\AccordionBlock $accordionBlockEntity): AccordionBlock
    {

        $accordionBlock = new AccordionBlock();
        /**
         * @var Visibility $visibility
         */
        $visibility = Visibility::findOrFail($block->getFkVisibility());

        $accordionBlock
            ->setTitle($accordionBlockEntity->getTitle())
            ->setExpand($accordionBlockEntity->getExpand() === 1)
            ->setId($block->getPkId())
            ->setSequence($block->getSequence())
            ->setVisibility($visibility->getName());

        /**
         * @var AccordionBlockMember[] $members
         */
        $members = AccordionBlockMember::where(['fk_accordion_block' => $accordionBlockEntity->getPkId()])->get();
        $blocks = [];
        foreach ($members as $member) {
            try {
                $blocks[] = $this->blockAccumulator->fetchSpecificBlocksById($member->getFkBlockId());
            } catch (EntityNotFoundException $exception) {
                //delete broken block relation
                $member->delete();
            }
        }

        $accordionBlock->setBlocks($this->fixArrayKeys($blocks));

        return $accordionBlock;

    }

    private function mapToEntity(AccordionBlock $accordionBlock): \KPG\Learnplaces\persistence\entity\AccordionBlock
    {

        /**
         * @var \KPG\Learnplaces\persistence\entity\AccordionBlock $activeRecord
         */
        $activeRecord = \KPG\Learnplaces\persistence\entity\AccordionBlock::where(['fk_block_id' => $accordionBlock->getId()])->first();

        if(is_null($activeRecord)) {
            $activeRecord = new \KPG\Learnplaces\persistence\entity\AccordionBlock();
            $activeRecord->setFkBlockId($accordionBlock->getId());
        }

        $activeRecord->setExpand($accordionBlock->isExpand() ? 1 : 0);
        $activeRecord->setTitle($accordionBlock->getTitle());

        return $activeRecord;
    }

    private function fixArrayKeys(array $array): array
    {
        $fixed = [];
        foreach ($array as $item) {
            $fixed[] = $item;
        }
        return $fixed;
    }

    private function storeBlockRelationsAndSequence(int $accordionId, array $blocks)
    {
        try {
/*            $learnplace = \KPG\Learnplaces\persistence\entity\Learnplace::innerjoinAR(new Block(), 'pk_id', 'fk_learnplace_id', ['fk_learnplace_id'])
                ->innerjoinAR(new \KPG\Learnplaces\persistence\entity\AccordionBlock(), Block::returnDbTableName() . '.pk_id', 'fk_block_id', ['fk_block_id'], '=', true) // xsrl_block.pk_id
                ->where([\KPG\Learnplaces\persistence\entity\AccordionBlock::returnDbTableName() . '.pk_id' => $accordionId])->first();*/

            global $DIC;

            $quotedAccordionId = $DIC->database()->quote($accordionId);

            $query = $DIC->database()->query("
                SELECT xsrl_learnplace.pk_id as id
                FROM xsrl_learnplace 
                INNER JOIN xsrl_block AS b 
                    ON xsrl_learnplace.pk_id = b.fk_learnplace_id 
                INNER JOIN xsrl_accordion_block AS ab 
                    ON b.pk_id = ab.fk_block_id 
                WHERE ab.pk_id = $quotedAccordionId
            ");

            $learnplace_id = null;
            if ($result = $DIC->database()->fetchAssoc($query)) {
                $learnplace_id = $result['id'];
            }

            /**
             * @var \KPG\Learnplaces\persistence\dto\Block $block
             */
            foreach($blocks as $block) {
                /**
                 * @var Block $blockEntity
                 */
                $blockEntity = Block::findOrFail($block->getId());

                // Only update the learnplace relation if we have one, for example while cloning we don't know the learnplace relation until the end

                if ($learnplace_id !== null) {
                    $blockEntity->setFkLearnplaceId($learnplace_id);
                }

/*                if ($learnplace !== null) {
                    $blockEntity->setFkLearnplaceId($learnplace->getPkId());
                }*/
                $blockEntity->setSequence($block->getSequence());
                $blockEntity->update();

                $entry = AccordionBlockMember::where(['fk_block_id' => $block->getId()])->first() ?? new AccordionBlockMember();
                $entry
                    ->setFkAccordionBlock($accordionId)
                    ->setFkBlockId($block->getId())
                    ->store();
            }
        } catch (arException $ex) {
            throw new InvalidArgumentException('Could not store relation to learnplace for non persistent block.', 0, $ex);
        }
    }
}
