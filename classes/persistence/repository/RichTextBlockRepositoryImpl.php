<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\repository;

use arException;
use KPG\Learnplaces\persistence\dto\Learnplace;
use KPG\Learnplaces\persistence\dto\RichTextBlock;
use KPG\Learnplaces\persistence\entity\Block;
use KPG\Learnplaces\persistence\entity\Visibility;
use KPG\Learnplaces\persistence\repository\exception\EntityNotFoundException;

use function is_null;

/**
 * Class RichTextBlockRepositoryImpl
 *
 * @package KPG\Learnplaces\persistence\repository
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class RichTextBlockRepositoryImpl implements RichTextBlockRepository
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
    public function store(RichTextBlock $richTextBlock): RichTextBlock
    {
        $storedBlock = ($richTextBlock->getId() > 0) ? $this->update($richTextBlock) : $this->create($richTextBlock);
        $this->storeBlockConstraint($storedBlock);
        return $storedBlock;
    }

    private function create(RichTextBlock $richTextBlock): RichTextBlock
    {
        /**
         * @var Block $block
         */
        $block = $this->mapToBlockEntity($richTextBlock);
        $block->create();
        $richTextBlockEntity = $this->mapToEntity($richTextBlock);
        $richTextBlockEntity->setFkBlockId($block->getPkId());
        $richTextBlockEntity->create();
        return $this->mapToDTO($block, $richTextBlockEntity);
    }

    private function update(RichTextBlock $richTextBlock): RichTextBlock
    {
        $blockEntity = $this->mapToBlockEntity($richTextBlock);
        $blockEntity->update();
        $richTextBlockEntity = $this->mapToEntity($richTextBlock);
        $richTextBlockEntity->update();
        return $this->mapToDTO($blockEntity, $richTextBlockEntity);
    }

    /**
     * @inheritdoc
     */
    public function findByBlockId(int $id): RichTextBlock
    {
        try {
            $block = Block::findOrFail($id);
            $richTextBlock = \KPG\Learnplaces\persistence\entity\RichTextBlock::where(['fk_block_id' => $id])->first();
            if(is_null($richTextBlock)) {
                throw new EntityNotFoundException("Rich text block with id \"$id\" was not found");
            }
            return $this->mapToDTO($block, $richTextBlock);
        } catch (arException $ex) {
            throw new EntityNotFoundException("Rich text block with id \"$id\" was not found", $ex);
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id)
    {
        try {
            $richTextBlock = \KPG\Learnplaces\persistence\entity\RichTextBlock::where(['fk_block_id' => $id])->first();
            if(!is_null($richTextBlock)) {
                $richTextBlock->delete();
            }

            Block::findOrFail($id)->delete();
        } catch (arException $ex) {
            throw new EntityNotFoundException("Rich text block with id \"$id\" not found", $ex);
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
        $blocks = Block::innerjoinAR(new \KPG\Learnplaces\persistence\entity\RichTextBlock(), 'pk_id', 'fk_block_id')
            ->where(['fk_learnplace_id' => $learnplace->getId()])->get();

        $mappedBlocks = [];

        //fetch all specific blocks and map them to DTOs
        foreach ($blocks as $block) {
            $richTextBlock = \KPG\Learnplaces\persistence\entity\RichTextBlock::where(['fk_block_id' => $block->getPkId()])->first();
            $mappedBlocks[] = $this->mapToDTO($block, $richTextBlock);
        }

        return $mappedBlocks;
    }

    private function mapToDTO(Block $block, \KPG\Learnplaces\persistence\entity\RichTextBlock $richTextBlockEntity): RichTextBlock
    {

        $richTextBlock = new RichTextBlock();
        /**
         * @var Visibility $visibility
         */
        $visibility = Visibility::findOrFail($block->getFkVisibility());

        $richTextBlock
            ->setContent($richTextBlockEntity->getContent())
            ->setId($block->getPkId())
            ->setSequence($block->getSequence())
            ->setVisibility($visibility->getName());

        return $richTextBlock;

    }

    private function mapToEntity(RichTextBlock $richTextBlock): \KPG\Learnplaces\persistence\entity\RichTextBlock
    {

        /**
         * @var \KPG\Learnplaces\persistence\entity\RichTextBlock $activeRecord
         */
        $activeRecord = \KPG\Learnplaces\persistence\entity\RichTextBlock::where(['fk_block_id' => $richTextBlock->getId()])->first();

        if(is_null($activeRecord)) {
            $activeRecord = new \KPG\Learnplaces\persistence\entity\RichTextBlock();
            $activeRecord->setFkBlockId($richTextBlock->getId());
        }

        $activeRecord
            ->setContent($richTextBlock->getContent());

        return $activeRecord;
    }
}
