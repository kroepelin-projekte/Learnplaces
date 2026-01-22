<?php

declare(strict_types=1);

namespace KPG\Learnplaces\service\media;

use ILIAS\ResourceStorage\Identification\ResourceIdentification;
use ilLearnplacesStakeholder;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\persistence\repository\exception\EntityNotFoundException;
use KPG\Learnplaces\persistence\repository\PictureRepository;
use KPG\Learnplaces\service\media\wrapper\FileTypeDetector;
use KPG\Learnplaces\service\publicapi\model\PictureModel;
use ILIAS\FileUpload\MimeType;

/**
 * Class PictureServiceImpl
 *
 * @package KPG\Learnplaces\service\media
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class PictureServiceImpl implements PictureService
{
    /**
     * The picture service will only accept uploads with the whitelisted extensions.
     *
     * @var string[] $allowedPictureTypes
     */
    private static $allowedPictureTypes = [
        MimeType::IMAGE__JPEG,
        MimeType::IMAGE__PNG
    ];

    /**
     * @var PictureRepository $pictureRepository
     */
    private $pictureRepository;

    /**
     * PictureServiceImpl constructor.
     *
     * @param ServerRequestInterface $request
     * @param PictureRepository      $pictureRepository
     * @param FileTypeDetector       $fileTypeDetector
     */
    public function __construct(ServerRequestInterface $request, PictureRepository $pictureRepository, FileTypeDetector $fileTypeDetector)
    {
        $this->pictureRepository = $pictureRepository;
    }

    /**
     * @inheritDoc
     */
    public function storeUpload(int $objectId, string $resourceId): PictureModel
    {
        $picture = new PictureModel();
        $picture->setResourceId($resourceId);

        $dto = $this->pictureRepository->store($picture->toDto());

        return $dto->toModel();
    }

    /**
     * @inheritDoc
     * @return void
     * @throws \ilDatabaseException
     */
    public function delete(int $pictureId): void
    {
        try {
            $picture = $this->pictureRepository->find($pictureId);
            $this->pictureRepository->delete($pictureId);

            $this->deleteFile($picture->getResourceId());
        } catch (EntityNotFoundException $ex) {
            throw new InvalidArgumentException("Unable to delete picture with id \"$pictureId\".", 0, $ex);
        }
    }

    /**
     * @param string $resourceId
     * @return void
     */
    private function deleteFile(string $resourceId): void
    {
        $resourceStorage = PluginContainer::resolve('resourceStorage');

        $resource = new ResourceIdentification($resourceId);
        if ($resourceStorage->manage()->find($resourceId)) {
            $resourceStorage->manage()->remove($resource, new ilLearnplacesStakeholder());
        }
    }
}
