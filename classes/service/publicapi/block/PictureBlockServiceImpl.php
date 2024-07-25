<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\service\publicapi\block;

use InvalidArgumentException;
use SRAG\Learnplaces\persistence\repository\exception\EntityNotFoundException;
use SRAG\Learnplaces\persistence\repository\PictureBlockRepository;
use SRAG\Learnplaces\service\media\PictureService;
use SRAG\Learnplaces\service\publicapi\model\PictureBlockModel;

use function is_null;

/**
 * Class PictureBlockServiceImpl
 *
 * @package SRAG\Learnplaces\service\publicapi\block
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class PictureBlockServiceImpl implements PictureBlockService
{
    /**
     * @var PictureBlockRepository $pictureBlockRepository
     */
    private $pictureBlockRepository;
    /**
     * @var PictureService $pictureService
     */
    private $pictureService;


    /**
     * PictureBlockServiceImpl constructor.
     *
     * @param PictureBlockRepository $pictureBlockRepository
     * @param PictureService         $pictureService
     */
    public function __construct(PictureBlockRepository $pictureBlockRepository, PictureService $pictureService)
    {
        $this->pictureBlockRepository = $pictureBlockRepository;
        $this->pictureService = $pictureService;
    }


    /**
     * @inheritDoc
     */
    public function store(PictureBlockModel $blockModel): PictureBlockModel
    {
        $dto = $this->pictureBlockRepository->store($blockModel->toDto());
        return $dto->toModel();
    }


    /**
     * @inheritDoc
     */
    public function delete(int $id)
    {
        try {
            $block = $this->pictureBlockRepository->findByBlockId($id);
            $this->pictureBlockRepository->delete($id);
            if(!is_null($block->getPicture())) {
                $this->pictureService->delete($block->getPicture()->getId());
            }

        } catch (EntityNotFoundException $ex) {
            throw new InvalidArgumentException('The picture block with the given id could not be deleted, because the block was not found.', 0, $ex);
        }
    }


    /**
     * @inheritDoc
     */
    public function find(int $id): PictureBlockModel
    {
        try {
            $dto = $this->pictureBlockRepository->findByBlockId($id);
            return $dto->toModel();
        } catch (EntityNotFoundException $ex) {
            throw new InvalidArgumentException('The picture block with the given id does not exist.', 0, $ex);
        }
    }
}
