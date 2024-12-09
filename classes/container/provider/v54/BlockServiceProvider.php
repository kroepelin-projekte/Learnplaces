<?php

declare(strict_types=1);

namespace KPG\Learnplaces\container\provider\v54;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use KPG\Learnplaces\persistence\repository\AccordionBlockRepository;
use KPG\Learnplaces\persistence\repository\AnswerRepository;
use KPG\Learnplaces\persistence\repository\AnswerRepositoryImpl;
use KPG\Learnplaces\persistence\repository\ConfigurationRepository;
use KPG\Learnplaces\persistence\repository\ExternalStreamBlockRepository;
use KPG\Learnplaces\persistence\repository\ILIASLinkBlockRepository;
use KPG\Learnplaces\persistence\repository\LearnplaceRepository;
use KPG\Learnplaces\persistence\repository\LocationRepository;
use KPG\Learnplaces\persistence\repository\MapBlockRepository;
use KPG\Learnplaces\persistence\repository\PictureBlockRepository;
use KPG\Learnplaces\persistence\repository\PictureUploadBlockRepository;
use KPG\Learnplaces\persistence\repository\RichTextBlockRepository;
use KPG\Learnplaces\persistence\repository\VideoBlockRepository;
use KPG\Learnplaces\persistence\repository\VisitJournalRepository;
use KPG\Learnplaces\service\media\PictureService;
use KPG\Learnplaces\service\media\VideoService;
use KPG\Learnplaces\service\publicapi\block\AccordionBlockService;
use KPG\Learnplaces\service\publicapi\block\AccordionBlockServiceImpl;
use KPG\Learnplaces\service\publicapi\block\AnswerService;
use KPG\Learnplaces\service\publicapi\block\CommentService;
use KPG\Learnplaces\service\publicapi\block\CommentServiceImpl;
use KPG\Learnplaces\service\publicapi\block\ConfigurationService;
use KPG\Learnplaces\service\publicapi\block\ConfigurationServiceImpl;
use KPG\Learnplaces\service\publicapi\block\ExternalStreamBlockService;
use KPG\Learnplaces\service\publicapi\block\ExternalStreamBlockServiceImpl;
use KPG\Learnplaces\service\publicapi\block\ILIASLinkBlockService;
use KPG\Learnplaces\service\publicapi\block\ILIASLinkBlockServiceImpl;
use KPG\Learnplaces\service\publicapi\block\LearnplaceService;
use KPG\Learnplaces\service\publicapi\block\LearnplaceServiceImpl;
use KPG\Learnplaces\service\publicapi\block\LocationService;
use KPG\Learnplaces\service\publicapi\block\LocationServiceImpl;
use KPG\Learnplaces\service\publicapi\block\MapBlockService;
use KPG\Learnplaces\service\publicapi\block\MapBlockServiceImpl;
use KPG\Learnplaces\service\publicapi\block\PictureBlockService;
use KPG\Learnplaces\service\publicapi\block\PictureBlockServiceImpl;
use KPG\Learnplaces\service\publicapi\block\PictureUploadBlockService;
use KPG\Learnplaces\service\publicapi\block\PictureUploadBlockServiceImpl;
use KPG\Learnplaces\service\publicapi\block\RichTextBlockService;
use KPG\Learnplaces\service\publicapi\block\RichTextBlockServiceImpl;
use KPG\Learnplaces\service\publicapi\block\util\BlockOperationDispatcher;
use KPG\Learnplaces\service\publicapi\block\util\DefaultBlockOperationDispatcher;
use KPG\Learnplaces\service\publicapi\block\VideoBlockService;
use KPG\Learnplaces\service\publicapi\block\VideoBlockServiceImpl;
use KPG\Learnplaces\service\publicapi\block\VisitJournalService;
use KPG\Learnplaces\service\publicapi\block\VisitJournalServiceImpl;

/**
 * Class BlockServiceProvider
 *
 * Contains the wiring of the entire KPG\Learnplaces\service\publicapi\block namespace.
 *
 * @package KPG\Learnplaces\container\provider
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class BlockServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $pimple)
    {
        $pimple[VisitJournalService::class]             = function ($c) {return new VisitJournalServiceImpl($c[VisitJournalRepository::class]); };
        $pimple[VideoBlockService::class]               = function ($c) {return new VideoBlockServiceImpl($c[VideoBlockRepository::class], $c[VideoService::class]); };
        $pimple[RichTextBlockService::class]            = function ($c) {return new RichTextBlockServiceImpl($c[RichTextBlockRepository::class]); };
        $pimple[PictureUploadBlockService::class]       = function ($c) {return new PictureUploadBlockServiceImpl($c[PictureUploadBlockRepository::class]); };
        $pimple[PictureBlockService::class]             = function ($c) {return new PictureBlockServiceImpl($c[PictureBlockRepository::class], $c[PictureService::class]); };
        $pimple[MapBlockService::class]                 = function ($c) {return new MapBlockServiceImpl($c[MapBlockRepository::class]); };
        $pimple[LocationService::class]                 = function ($c) {return new LocationServiceImpl($c[LocationRepository::class]); };
        $pimple[ILIASLinkBlockService::class]           = function ($c) {return new ILIASLinkBlockServiceImpl($c[ILIASLinkBlockRepository::class]); };
        $pimple[ExternalStreamBlockService::class]      = function ($c) {return new ExternalStreamBlockServiceImpl($c[ExternalStreamBlockRepository::class]); };
        $pimple[ConfigurationService::class]            = function ($c) {return new ConfigurationServiceImpl($c[ConfigurationRepository::class]); };
        $pimple[CommentService::class]                  = function ($c) {return new CommentServiceImpl($c[ConfigurationRepository::class]); };
        $pimple[AnswerService::class]                   = function ($c) {return new AnswerRepositoryImpl($c[AnswerRepository::class]); };
        $pimple[AccordionBlockService::class]           = function ($c) {return new AccordionBlockServiceImpl($c[AccordionBlockRepository::class]); };
        $pimple[BlockOperationDispatcher::class]        = function ($c) {
            $dispatcher =  new DefaultBlockOperationDispatcher(
                $c[AccordionBlockService::class],
                $c[ILIASLinkBlockService::class],
                $c[PictureBlockService::class],
                $c[PictureUploadBlockService::class],
                $c[MapBlockService::class],
                $c[RichTextBlockService::class],
                $c[VideoBlockService::class]
            );
            $accordion = $c[AccordionBlockService::class];
            $accordion->postConstruct($dispatcher);
            return $dispatcher;
        };
        $pimple[LearnplaceService::class]               = function ($c) {
            return new LearnplaceServiceImpl(
                $c[ConfigurationService::class],
                $c[LocationService::class],
                $c[VisitJournalService::class],
                $c[LearnplaceRepository::class],
                $c[BlockOperationDispatcher::class],
                $c[PictureService::class]
            );
        };
    }
}
