<?php

declare(strict_types=1);

namespace KPG\Learnplaces\service\publicapi\block;

use InvalidArgumentException;
use KPG\Learnplaces\persistence\repository\exception\EntityNotFoundException;
use KPG\Learnplaces\persistence\repository\RichTextBlockRepository;
use KPG\Learnplaces\service\publicapi\model\RichTextBlockModel;

/**
 * Class RichTextBlockServiceImpl
 *
 * @package KPG\Learnplaces\service\publicapi\block
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class RichTextBlockServiceImpl implements RichTextBlockService
{
    /**
     * @var RichTextBlockRepository $richTextBlockRepository
     */
    private $richTextBlockRepository;


    /**
     * RichTextBlockServiceImpl constructor.
     *
     * @param RichTextBlockRepository $richTextBlockRepository
     */
    public function __construct(RichTextBlockRepository $richTextBlockRepository)
    {
        $this->richTextBlockRepository = $richTextBlockRepository;
    }


    /**
     * @inheritDoc
     */
    public function store(RichTextBlockModel $richTextBlockModel): RichTextBlockModel
    {
        $dto = $this->richTextBlockRepository->store($richTextBlockModel->toDto());
        return $dto->toModel();
    }


    /**
     * @inheritDoc
     */
    public function delete(int $id)
    {
        try {
            $this->richTextBlockRepository->delete($id);
        } catch (EntityNotFoundException $ex) {
            throw new InvalidArgumentException('The rich text block with the given id could not be deleted, because the block was not found.', 0, $ex);
        }
    }


    /**
     * @inheritDoc
     */
    public function find(int $id): RichTextBlockModel
    {
        try {
            $dto = $this->richTextBlockRepository->findByBlockId($id);
            return $dto->toModel();
        } catch (EntityNotFoundException $ex) {
            throw new InvalidArgumentException('The rich text block with the given id does not exist.', 0, $ex);
        }
    }
}
