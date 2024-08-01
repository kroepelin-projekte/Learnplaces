<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\service\media;

use ILIAS\ResourceStorage\Identification\ResourceIdentification;
use ilLearnplacesStakeholder;
use Intervention\Image\ImageManager;
use InvalidArgumentException;
use League\Flysystem\FilesystemInterface;
use LogicException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use SRAG\Learnplaces\container\PluginContainer;
use SRAG\Learnplaces\persistence\repository\exception\EntityNotFoundException;
use SRAG\Learnplaces\persistence\repository\PictureRepository;
use SRAG\Learnplaces\service\filesystem\PathHelper;
use SRAG\Learnplaces\service\media\exception\FileUploadException;
use SRAG\Learnplaces\service\media\wrapper\FileTypeDetector;
use SRAG\Learnplaces\service\publicapi\model\PictureModel;
use wapmorgan\FileTypeDetector\Detector;

use function array_pop;

/**
 * Class PictureServiceImpl
 *
 * @package SRAG\Learnplaces\service\media
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
        Detector::JPEG,
        Detector::PNG
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
