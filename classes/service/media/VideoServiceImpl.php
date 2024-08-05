<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\service\media;

use ILIAS\ResourceStorage\Identification\ResourceIdentification;
use ilLearnplacesStakeholder;
use LogicException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use SRAG\Learnplaces\container\PluginContainer;
use SRAG\Learnplaces\service\filesystem\PathHelper;
use SRAG\Learnplaces\service\media\exception\FileUploadException;
use SRAG\Learnplaces\service\media\wrapper\FileTypeDetector;
use SRAG\Learnplaces\service\publicapi\model\VideoModel;
use wapmorgan\FileTypeDetector\Detector;

use function array_pop;

/**
 * Class VideoServiceImpl
 *
 * @package SRAG\Learnplaces\service\media
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class VideoServiceImpl implements VideoService
{
    /**
     * The video service will only accept uploads with the whitelisted extensions.
     *
     * @var string[] $allowedVideoTypes
     */
    private static $allowedVideoTypes = [
        Detector::MP4
    ];

    /**
     * @var FileTypeDetector $fileTypeDetector
     */
    private $fileTypeDetector;

    /**
     * VideoServiceImpl constructor.
     *
     * @param ServerRequestInterface $request
     * @param FileTypeDetector       $fileTypeDetector
     */
    public function __construct(ServerRequestInterface $request, FileTypeDetector $fileTypeDetector)
    {
        $this->fileTypeDetector = $fileTypeDetector;
    }

    /**
     * @inheritDoc
     */
    public function storeUpload(int $objectId, string $resourceId): VideoModel
    {
        $videoModel = new VideoModel();
        $videoModel->setResourceId($resourceId);

        return $videoModel;
    }

    /**
     * @inheritdoc
     */
    public function delete(VideoModel $video): void
    {
        $this->deleteFile($video->getResourceId());
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

    /**
     * @param string $pathToVideo
     * @return void
     */
    private function validateVideoContent(string $pathToVideo): void
    {
        try {
            /*
             * Supported headers:
             * offset 4: ftyp = 0x66747970
             *
             * Possible sup types:
             * offset 8: isom = 0x69736F6D
             * offset 8: 3gp5 = 0x33677035
             * offset 8: MSNV = 0x4D534E56
             * offset 8: M4A  = 0x4D344120
             *
             * documentation: https://github.com/wapmorgan/FileTypeDetector
             */
            $typeInfo = $this->fileTypeDetector->detectByContent($pathToVideo);

            if(in_array($typeInfo[1], self::$allowedVideoTypes) === false) {
                $this->deleteFile($pathToVideo);
                throw new FileUploadException('Video with invalid content uploaded.');
            }
        } catch (RuntimeException $ex) {
            $this->deleteFile($pathToVideo);
            throw new FileUploadException('Video with unknown header uploaded.');
        }
    }
}
