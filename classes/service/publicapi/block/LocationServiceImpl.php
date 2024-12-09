<?php

declare(strict_types=1);

namespace KPG\Learnplaces\service\publicapi\block;

use InvalidArgumentException;
use KPG\Learnplaces\persistence\repository\exception\EntityNotFoundException;
use KPG\Learnplaces\persistence\repository\LocationRepository;
use KPG\Learnplaces\service\publicapi\model\LocationModel;

/**
 * Class LocationServiceImpl
 *
 * @package KPG\Learnplaces\service\publicapi\block
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class LocationServiceImpl implements LocationService
{
    /**
     * @var LocationRepository $locationRepository
     */
    private $locationRepository;


    /**
     * LocationServiceImpl constructor.
     *
     * @param LocationRepository $locationRepository
     */
    public function __construct(LocationRepository $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }


    /**
     * @inheritDoc
     */
    public function store(LocationModel $locationModel): LocationModel
    {
        $dto = $this->locationRepository->store($locationModel->toDto());
        return $dto->toModel();
    }


    /**
     * @inheritDoc
     */
    public function delete(int $id)
    {
        try {
            $this->locationRepository->delete($id);
        } catch (EntityNotFoundException $ex) {
            throw new InvalidArgumentException('The location with the given id could not be deleted, because the it was not found.', 0, $ex);
        }
    }


    /**
     * @inheritDoc
     */
    public function find(int $id): LocationModel
    {
        try {
            $dto = $this->locationRepository->find($id);
            return $dto->toModel();
        } catch (EntityNotFoundException $ex) {
            throw new InvalidArgumentException('The location with the given id does not exist.', 0, $ex);
        }
    }
}
