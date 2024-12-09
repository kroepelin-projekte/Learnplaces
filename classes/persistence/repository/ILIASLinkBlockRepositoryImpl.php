<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\repository;

use arException;
use KPG\Learnplaces\persistence\dto\ILIASLinkBlock;
use KPG\Learnplaces\persistence\dto\Learnplace;
use KPG\Learnplaces\persistence\entity\Block;
use KPG\Learnplaces\persistence\entity\Visibility;
use KPG\Learnplaces\persistence\repository\exception\EntityNotFoundException;

use function is_null;

/**
 * Class ILIASLinkBlock
 *
 * @package KPG\Learnplaces\persistence\repository
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class ILIASLinkBlockRepositoryImpl implements ILIASLinkBlockRepository
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
    public function store(ILIASLinkBlock $linkBlock): ILIASLinkBlock
    {
        $storedBlock = ($linkBlock->getId() > 0) ? $this->update($linkBlock) : $this->create($linkBlock);
        $this->storeBlockConstraint($storedBlock);
        return $storedBlock;
    }

    private function create(ILIASLinkBlock $linkBlock): ILIASLinkBlock
    {
        /**
         * @var Block $block
         */
        $block = $this->mapToBlockEntity($linkBlock);
        $block->create();
        $linkBlockEntity = $this->mapToEntity($linkBlock);
        $linkBlockEntity->setFkBlockId($block->getPkId());
        $linkBlockEntity->create();
        return $this->mapToDTO($block, $linkBlockEntity);
    }

    private function update(ILIASLinkBlock $linkBlock): ILIASLinkBlock
    {
        $blockEntity = $this->mapToBlockEntity($linkBlock);
        $blockEntity->update();
        $linkBlockEntity = $this->mapToEntity($linkBlock);
        $linkBlockEntity->update();
        return $this->mapToDTO($blockEntity, $linkBlockEntity);
    }

    /**
     * @inheritdoc
     */
    public function findByBlockId(int $id): ILIASLinkBlock
    {
        try {
            $block = Block::findOrFail($id);
            $linkBlock = \KPG\Learnplaces\persistence\entity\ILIASLinkBlock::where(['fk_block_id' => $id])->first();
            if(is_null($linkBlock)) {
                throw new EntityNotFoundException("PictureUploadBlock with id \"$id\" was not found");
            }

            return $this->mapToDTO($block, $linkBlock);
        } catch (arException $ex) {
            throw new EntityNotFoundException("PictureUploadBlock with id \"$id\" was not found", $ex);
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id)
    {
        try {
            $uploadBlock = \KPG\Learnplaces\persistence\entity\ILIASLinkBlock::where(['fk_block_id' => $id])->first();
            if(!is_null($uploadBlock)) {
                $uploadBlock->delete();
            }

            Block::findOrFail($id)->delete();
        } catch (arException $ex) {
            throw new EntityNotFoundException("ILIAS link block with id \"$id\" not found", $ex);
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
        $blocks = Block::innerjoinAR(new \KPG\Learnplaces\persistence\entity\ILIASLinkBlock(), 'pk_id', 'fk_block_id')
            ->where(['fk_learnplace_id' => $learnplace->getId()])->get();

        $mappedBlocks = [];

        //fetch all specific link blocks and map them DTOs
        foreach ($blocks as $block) {
            $linkBlockEntity = \KPG\Learnplaces\persistence\entity\ILIASLinkBlock::where(['fk_block_id' => $block->getPkId()])->first();
            $mappedBlocks[] = $this->mapToDTO($block, $linkBlockEntity);
        }

        return $mappedBlocks;
    }

    private function mapToDTO(Block $block, \KPG\Learnplaces\persistence\entity\ILIASLinkBlock $linkBlockEntity): ILIASLinkBlock
    {

        $linkBlock = new ILIASLinkBlock();
        /**
         * @var Visibility $visibility
         */
        $visibility = Visibility::findOrFail($block->getFkVisibility());

        $linkBlock
            ->setRefId($linkBlockEntity->getRefId())
            ->setId($block->getPkId())
            ->setSequence($block->getSequence())
            ->setVisibility($visibility->getName());

        return $linkBlock;

    }

    private function mapToEntity(ILIASLinkBlock $linkBlock): \KPG\Learnplaces\persistence\entity\ILIASLinkBlock
    {

        /**
         * @var \KPG\Learnplaces\persistence\entity\ILIASLinkBlock $activeRecord
         */
        $activeRecord = \KPG\Learnplaces\persistence\entity\ILIASLinkBlock::where(['fk_block_id' => $linkBlock->getId()])->first();

        if(is_null($activeRecord)) {
            $activeRecord = new \KPG\Learnplaces\persistence\entity\ILIASLinkBlock();
            $activeRecord->setFkBlockId($linkBlock->getId());
        }

        $activeRecord->setRefId($linkBlock->getRefId());
        return $activeRecord;
    }

}
