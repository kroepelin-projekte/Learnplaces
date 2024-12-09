<?php

declare(strict_types=1);

namespace KPG\Learnplaces\container\provider\v54;

use Intervention\Image\ImageManager;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Http\Message\ServerRequestInterface;
use KPG\Learnplaces\persistence\repository\PictureRepository;
use KPG\Learnplaces\service\media\PictureService;
use KPG\Learnplaces\service\media\PictureServiceImpl;
use KPG\Learnplaces\service\media\VideoService;
use KPG\Learnplaces\service\media\VideoServiceImpl;
use KPG\Learnplaces\service\media\wrapper\FileTypeDetector;
use KPG\Learnplaces\service\media\wrapper\WabmorganFileTypeDetector;

use const ILIAS_ABSOLUTE_PATH;

/**
 * Class MediaServiceProvider
 *
 * Contains the wiring of the entire KPG\Learnplaces\service\media namespace.
 *
 * @package KPG\Learnplaces\container\provider
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class MediaServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $pimple)
    {
        $pimple[FileTypeDetector::class]    = function ($c) {return new WabmorganFileTypeDetector(); };
        $pimple[ImageManager::class]        = function ($c) {return new ImageManager(); };
        $pimple[PictureService::class]      = function ($c) {
            return new PictureServiceImpl(
                $c[ServerRequestInterface::class],
                $c[PictureRepository::class],
                $c[FileTypeDetector::class],
            );
        };
        $pimple[VideoService::class]        = function ($c) {
            return new VideoServiceImpl(
                $c[ServerRequestInterface::class],
                $c[FileTypeDetector::class],
            );
        };
    }
}
