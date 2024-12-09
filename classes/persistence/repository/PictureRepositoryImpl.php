<?php

declare(strict_types=1);

namespace KPG\Learnplaces\persistence\repository;

use arException;
use ilDatabaseException;
use KPG\Learnplaces\persistence\dto\Picture;
use KPG\Learnplaces\persistence\repository\exception\EntityNotFoundException;

/**
 * Class PictureRepositoryImpl
 *
 * @package KPG\Learnplaces\persistence\repository
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
class PictureRepositoryImpl implements PictureRepository
{
    /**
     * @inheritdoc
     */
    public function store(Picture $picture): Picture
    {
        $activeRecord = $this->mapToEntity($picture);
        $activeRecord->store();
        return $this->mapToDTO($activeRecord);
    }

    /**
     * @inheritdoc
     */
    public function find(int $id): Picture
    {
        try {
            $pictureEntity = \KPG\Learnplaces\persistence\entity\Picture::findOrFail($id);
            return $this->mapToDTO($pictureEntity);
        } catch (arException $ex) {
            throw new EntityNotFoundException("Picture with id \"$id\" not found.", $ex);
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id)
    {
        try {
            $pictureEntity = \KPG\Learnplaces\persistence\entity\Picture::findOrFail($id);
            $pictureEntity->delete();
        } catch (arException $ex) {
            throw new EntityNotFoundException("Picture with id \"$id\" not found.", $ex);
        } catch(ilDatabaseException $ex) {
            throw new ilDatabaseException("Unable to delete picture with id \"$id\"");
        }
    }

    private function mapToDTO(\KPG\Learnplaces\persistence\entity\Picture $pictureEntity): Picture
    {

        $picture = new Picture();
        $picture
            ->setId($pictureEntity->getPkId())
            ->setResourceId($pictureEntity->getResourceId());

        return $picture;
    }

    private function mapToEntity(Picture $picture): \KPG\Learnplaces\persistence\entity\Picture
    {

        /**
         * @var \KPG\Learnplaces\persistence\entity\Picture $activeRecord
         */
        $activeRecord = new \KPG\Learnplaces\persistence\entity\Picture($picture->getId());

        $activeRecord
            ->setPkId($picture->getId())
            ->setResourceId($picture->getResourceId());

        return $activeRecord;
    }
}
