<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\repository;

use arException;
use KPG\Learnplaces\persistence\dto\Learnplace;
use KPG\Learnplaces\persistence\dto\VideoBlock;
use KPG\Learnplaces\persistence\entity\Block;
use KPG\Learnplaces\persistence\entity\Visibility;
use KPG\Learnplaces\persistence\repository\exception\EntityNotFoundException;

use function is_null;

/**
 * Class VideoBlockRepositoryImpl
 *
 * @package KPG\Learnplaces\persistence\repository
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class VideoBlockRepositoryImpl implements VideoBlockRepository
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
    public function store(VideoBlock $videoBlock): VideoBlock
    {
        $storedBlock = ($videoBlock->getId() > 0) ? $this->update($videoBlock) : $this->create($videoBlock);
        $this->storeBlockConstraint($storedBlock);
        return $storedBlock;
    }

    private function create(VideoBlock $videoBlock): VideoBlock
    {
        /**
         * @var Block $block
         */
        $block = $this->mapToBlockEntity($videoBlock);
        $block->create();
        $videoBlockEntity = $this->mapToEntity($videoBlock);
        $videoBlockEntity->setFkBlockId($block->getPkId());
        $videoBlockEntity->create();
        return $this->mapToDTO($block, $videoBlockEntity);
    }

    private function update(VideoBlock $videoBlock): VideoBlock
    {
        $blockEntity = $this->mapToBlockEntity($videoBlock);
        $blockEntity->update();
        $videoBlockEntity = $this->mapToEntity($videoBlock);
        $videoBlockEntity->update();
        return $this->mapToDTO($blockEntity, $videoBlockEntity);
    }

    /**
     * @inheritdoc
     */
    public function findByBlockId(int $id): VideoBlock
    {
        try {
            $videoBlock = \KPG\Learnplaces\persistence\entity\VideoBlock::where(['fk_block_id' => $id])->first();
            if(is_null($videoBlock)) {
                throw new EntityNotFoundException("Video block with id \"$id\" was not found");
            }
            $block = Block::findOrFail($id);
            return $this->mapToDTO($block, $videoBlock);
        } catch (arException $ex) {
            throw new EntityNotFoundException("Video block with id \"$id\" was not found", $ex);
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id)
    {
        try {
            $uploadBlock = \KPG\Learnplaces\persistence\entity\VideoBlock::where(['fk_block_id' => $id])->first();
            if(!is_null($uploadBlock)) {
                $uploadBlock->delete();
            }

            Block::findOrFail($id)->delete();
        } catch (arException $ex) {
            throw new EntityNotFoundException("Video block with id \"$id\" not found", $ex);
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
        $blocks = Block::innerjoinAR(new \KPG\Learnplaces\persistence\entity\VideoBlock(), 'pk_id', 'fk_block_id')
            ->where(['fk_learnplace_id' => $learnplace->getId()])->get();

        $mappedBlocks = [];

        //fetch all specific blocks and map them to DTOs
        foreach ($blocks as $block) {
            $videoBlockEntity = \KPG\Learnplaces\persistence\entity\VideoBlock::where(['fk_block_id' => $block->getPkId()])->first();
            $mappedBlocks[] = $this->mapToDTO($block, $videoBlockEntity);
        }

        return $mappedBlocks;
    }

    private function mapToDTO(Block $block, \KPG\Learnplaces\persistence\entity\VideoBlock $videoBlockEntity): VideoBlock
    {

        $videoBlock = new VideoBlock();
        /**
         * @var Visibility $visibility
         */
        $visibility = Visibility::findOrFail($block->getFkVisibility());

        $videoBlock
            ->setResourceId($videoBlockEntity->getResourceId())
            ->setId($block->getPkId())
            ->setSequence($block->getSequence())
            ->setVisibility($visibility->getName());

        return $videoBlock;

    }

    private function mapToEntity(VideoBlock $videoBlock): \KPG\Learnplaces\persistence\entity\VideoBlock
    {

        /**
         * @var \KPG\Learnplaces\persistence\entity\VideoBlock $activeRecord
         */
        $activeRecord = \KPG\Learnplaces\persistence\entity\VideoBlock::where(['fk_block_id' => $videoBlock->getId()])->first();

        if(is_null($activeRecord)) {
            $activeRecord = new \KPG\Learnplaces\persistence\entity\VideoBlock();
            $activeRecord->setFkBlockId($videoBlock->getId());
        }

        $activeRecord
            ->setResourceId($videoBlock->getResourceId());

        return $activeRecord;
    }
}
