<?php

declare(strict_types=1);

namespace KPG\Learnplaces\container\provider\v54;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use KPG\Learnplaces\persistence\dto\LearnplaceConstraint;
use KPG\Learnplaces\persistence\repository\AccordionBlockRepository;
use KPG\Learnplaces\persistence\repository\AccordionBlockRepositoryImpl;
use KPG\Learnplaces\persistence\repository\AnswerRepository;
use KPG\Learnplaces\persistence\repository\AnswerRepositoryImpl;
use KPG\Learnplaces\persistence\repository\AudioBlockRepository;
use KPG\Learnplaces\persistence\repository\AudioBlockRepositoryImpl;
use KPG\Learnplaces\persistence\repository\CommentBlockRepository;
use KPG\Learnplaces\persistence\repository\CommentBlockRepositoryImpl;
use KPG\Learnplaces\persistence\repository\CommentRepository;
use KPG\Learnplaces\persistence\repository\CommentRepositoryImpl;
use KPG\Learnplaces\persistence\repository\ConfigurationRepository;
use KPG\Learnplaces\persistence\repository\ConfigurationRepositoryImpl;
use KPG\Learnplaces\persistence\repository\ExternalStreamBlockRepository;
use KPG\Learnplaces\persistence\repository\ExternalStreamBlockRepositoryImpl;
use KPG\Learnplaces\persistence\repository\FeedbackBlockRepository;
use KPG\Learnplaces\persistence\repository\FeedbackBlockRepositoryImpl;
use KPG\Learnplaces\persistence\repository\FeedbackRepository;
use KPG\Learnplaces\persistence\repository\FeedbackRepositoryImpl;
use KPG\Learnplaces\persistence\repository\HorizontalLineBlockRepository;
use KPG\Learnplaces\persistence\repository\HorizontalLineBlockRepositoryImpl;
use KPG\Learnplaces\persistence\repository\ILIASLinkBlockRepository;
use KPG\Learnplaces\persistence\repository\ILIASLinkBlockRepositoryImpl;
use KPG\Learnplaces\persistence\repository\LearnplaceConstraintRepository;
use KPG\Learnplaces\persistence\repository\LearnplaceRepository;
use KPG\Learnplaces\persistence\repository\LearnplaceRepositoryImpl;
use KPG\Learnplaces\persistence\repository\LocationRepository;
use KPG\Learnplaces\persistence\repository\LocationRepositoryImpl;
use KPG\Learnplaces\persistence\repository\MapBlockRepository;
use KPG\Learnplaces\persistence\repository\MapBlockRepositoryImpl;
use KPG\Learnplaces\persistence\repository\PictureBlockRepository;
use KPG\Learnplaces\persistence\repository\PictureBlockRepositoryImpl;
use KPG\Learnplaces\persistence\repository\PictureRepository;
use KPG\Learnplaces\persistence\repository\PictureRepositoryImpl;
use KPG\Learnplaces\persistence\repository\PictureUploadBlockRepository;
use KPG\Learnplaces\persistence\repository\PictureUploadBlockRepositoryImpl;
use KPG\Learnplaces\persistence\repository\RichTextBlockRepository;
use KPG\Learnplaces\persistence\repository\RichTextBlockRepositoryImpl;
use KPG\Learnplaces\persistence\repository\util\BlockAccumulator;
use KPG\Learnplaces\persistence\repository\util\BlockAccumulatorImpl;
use KPG\Learnplaces\persistence\repository\VideoBlockRepository;
use KPG\Learnplaces\persistence\repository\VideoBlockRepositoryImpl;
use KPG\Learnplaces\persistence\repository\VisitJournalRepository;
use KPG\Learnplaces\persistence\repository\VisitJournalRepositoryImpl;

/**
 * Class RepositoryProvider
 *
 * Contains the wiring of the entire KPG\Lernplaces\persistence\repository namespace.
 *
 * @package KPG\Learnplaces\container\provider
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class RepositoryProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $pimple)
    {
        $pimple[VisitJournalRepository::class]  = function ($c) {return new VisitJournalRepositoryImpl(); };
        $pimple[ConfigurationRepository::class] = function ($c) {return new ConfigurationRepositoryImpl(); };
        $pimple[LocationRepository::class]      = function ($c) {return new LocationRepositoryImpl(); };

        $pimple[LearnplaceConstraintRepository::class] = function ($c) {
            return new class () implements LearnplaceConstraintRepository {
                /**
                 * @inheritDoc
                 */
                public function store(LearnplaceConstraint $constraint): LearnplaceConstraint
                {
                    throw new \Exception('Not implemented yet!');
                }


                /**
                 * @inheritDoc
                 */
                public function findByBlockId(int $id): LearnplaceConstraint
                {
                    throw new \Exception('Not implemented yet!');
                }


                /**
                 * @inheritDoc
                 */
                public function delete(int $id)
                {
                    throw new \Exception('Not implemented yet!');
                }
            };
        };

        $pimple[VideoBlockRepository::class]            = function ($c) {return new VideoBlockRepositoryImpl($c[LearnplaceConstraintRepository::class]); };
        $pimple[RichTextBlockRepository::class]         = function ($c) {return new RichTextBlockRepositoryImpl($c[LearnplaceConstraintRepository::class]); };
        $pimple[PictureUploadBlockRepository::class]    = function ($c) {return new PictureUploadBlockRepositoryImpl($c[LearnplaceConstraintRepository::class]); };
        $pimple[PictureRepository::class]               = function ($c) {return new PictureRepositoryImpl(); };
        $pimple[PictureBlockRepository::class]          = function ($c) {return new PictureBlockRepositoryImpl($c[LearnplaceConstraintRepository::class], $c[PictureRepository::class]); };
        $pimple[MapBlockRepository::class]              = function ($c) {return new MapBlockRepositoryImpl($c[LearnplaceConstraintRepository::class]); };
        $pimple[ILIASLinkBlockRepository::class]        = function ($c) {return new ILIASLinkBlockRepositoryImpl($c[LearnplaceConstraintRepository::class]); };
        $pimple[HorizontalLineBlockRepository::class]   = function ($c) {return new HorizontalLineBlockRepositoryImpl($c[LearnplaceConstraintRepository::class]); };
        $pimple[FeedbackRepository::class]              = function ($c) {return new FeedbackRepositoryImpl(); };
        $pimple[FeedbackBlockRepository::class]         = function ($c) {return new FeedbackBlockRepositoryImpl($c[LearnplaceConstraintRepository::class]); };
        $pimple[ExternalStreamBlockRepository::class]   = function ($c) {return new ExternalStreamBlockRepositoryImpl($c[LearnplaceConstraintRepository::class]); };
        $pimple[AnswerRepository::class]                = function ($c) {return new AnswerRepositoryImpl($c[PictureRepository::class]); };
        $pimple[CommentRepository::class]               = function ($c) {return new CommentRepositoryImpl($c[PictureRepository::class], $c[AnswerRepository::class]); };
        $pimple[CommentBlockRepository::class]          = function ($c) {return new CommentBlockRepositoryImpl($c[LearnplaceConstraintRepository::class], $c[CommentRepository::class]); };
        $pimple[AudioBlockRepository::class]            = function ($c) {return new AudioBlockRepositoryImpl($c[LearnplaceConstraintRepository::class]); };
        $pimple[BlockAccumulator::class]                = function ($c) {

            /**
             * @var AccordionBlockRepositoryImpl $accordion
             */
            $accordion = $c[AccordionBlockRepository::class];
            $accumulator = new BlockAccumulatorImpl(
                $c[PictureUploadBlockRepository::class],
                $c[ILIASLinkBlockRepository::class],
                $c[AudioBlockRepository::class],
                $c[HorizontalLineBlockRepository::class],
                $c[MapBlockRepository::class],
                $c[CommentBlockRepository::class],
                $c[VideoBlockRepository::class],
                $c[RichTextBlockRepository::class],
                $c[PictureBlockRepository::class],
                $c[ExternalStreamBlockRepository::class],
                $c[FeedbackBlockRepository::class],
                $accordion
            );

            $accordion->postConstruct($accumulator);
            return $accumulator;
        };

        $pimple[AccordionBlockRepository::class]        = function ($c) {return new AccordionBlockRepositoryImpl($c[LearnplaceConstraintRepository::class]);};
        $pimple[LearnplaceRepository::class]            = function ($c) {
            return new LearnplaceRepositoryImpl(
                $c[LocationRepository::class],
                $c[VisitJournalRepository::class],
                $c[ConfigurationRepository::class],
                $c[FeedbackRepository::class],
                $c[PictureRepository::class],
                $c[BlockAccumulator::class]
            );

        };
    }
}
