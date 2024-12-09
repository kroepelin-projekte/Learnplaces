<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\repository;

use arException;
use ilDatabaseException;
use KPG\Learnplaces\persistence\dto\Learnplace;
use KPG\Learnplaces\persistence\dto\Location;
use KPG\Learnplaces\persistence\repository\exception\EntityNotFoundException;

use function is_null;

/**
 * Class LocationRepositoryImpl
 *
 * @package KPG\Lernplaces\persistence\repository
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class LocationRepositoryImpl implements LocationRepository
{
    /**
     * @inheritdoc
     */
    public function store(Location $location): Location
    {
        $activeRecord = $this->mapToEntity($location);
        $activeRecord->store();
        return $this->mapToDTO($activeRecord);
    }

    /**
     * @inheritdoc
     */
    public function find(int $id): Location
    {
        try {
            $locationId = \KPG\Learnplaces\persistence\entity\Location::findOrFail($id);
            return $this->mapToDTO($locationId);
        } catch (arException $ex) {
            throw new EntityNotFoundException("Location not found with id \"$id\"", $ex);
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id)
    {
        try {
            $location = \KPG\Learnplaces\persistence\entity\Location::findOrFail($id);
            $location->delete();
        } catch (arException $ex) {
            throw new EntityNotFoundException("Location not found with id \"$id\"", $ex);
        } catch (ilDatabaseException $ex) {
            throw new ilDatabaseException("Unable to delete location with id \"$id\"");
        }
    }


    /**
     * @inheritdoc
     */
    public function findByLearnplace(Learnplace $learnplace): Location
    {

        $id = $learnplace->getId();

        $location = \KPG\Learnplaces\persistence\entity\Location::where(['fk_learnplace_id' => $id])->first();
        if(is_null($location)) {
            throw new EntityNotFoundException("Location not found with id \"$id\"");
        }

        return $this->mapToDTO($location);
    }


    /**
     * Maps the active record to the dto.
     *
     * @param \KPG\Learnplaces\persistence\entity\Location $locationEntity The active record which should be mapped to the dto.
     *
     * @return Location The newly mapped location dto.
     */
    private function mapToDTO(\KPG\Learnplaces\persistence\entity\Location $locationEntity): Location
    {
        $location = new Location();
        $location
            ->setId($locationEntity->getPkId())
            ->setElevation($locationEntity->getElevation())
            ->setLatitude($locationEntity->getLatitude())
            ->setLongitude($locationEntity->getLongitude())
            ->setRadius($locationEntity->getRadius());

        return $location;
    }


    /**
     * Maps the dto to the active record representation.
     *
     * @param Location $location    The location object which should be mapped to the active record.
     *
     * @return \KPG\Learnplaces\persistence\entity\Location The newly mapped active record.
     */
    private function mapToEntity(Location $location): \KPG\Learnplaces\persistence\entity\Location
    {

        $activeRecord = new \KPG\Learnplaces\persistence\entity\Location($location->getId());
        $activeRecord
            ->setRadius($location->getRadius())
            ->setLongitude($location->getLongitude())
            ->setLatitude($location->getLatitude())
            ->setElevation($location->getElevation());

        return $activeRecord;
    }
}
