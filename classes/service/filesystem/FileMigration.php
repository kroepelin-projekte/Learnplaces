<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\service\filesystem;

use ilLearnplacesStakeholder;
use ilLoggerFactory;

class FileMigration
{
    /**
     * Moves pictures to the resource storage and updates the picture entity with the resource ID and clears the original path.
     *
     * @return void
     */
    public static function movePicturesToResourceStorage(): void
    {
        global $DIC;

        $logger = ilLoggerFactory::getLogger('Learnplaces.FileMigration');
        $logger->info("\n\n\n");
        $logger->info('Start moving pictures to resource storage');

        $pictures = \SRAG\Learnplaces\persistence\entity\Picture::get();
        $logger->info('Found ' . count($pictures) . ' pictures');

        if (! $DIC->offsetExists('filesystem')) {
            \ilInitialisation::bootstrapFilesystems();
            $logger->info('Filesystems initialized');
        }

        foreach ($pictures as $picture) {
            $logger->info("_____________________\n");
            $logger->info('Move picture ' . $picture->getId());

            $path = $picture->getOriginalPath();
            $logger->info('Path: ' . $path);

            if ($DIC->filesystem()->web()->has($path)) {
                $logger->info('File exists');

                $stream = $DIC->filesystem()->web()->readStream($path);
                $logger->info('Stream created');

                $resourceId = $DIC->resourceStorage()->manage()
                    ->stream($stream, new ilLearnplacesStakeholder(), '')
                    ->serialize();
                $logger->info('Resource ID: ' . $resourceId);

                $picture->setResourceId($resourceId);
                $logger->info('Resource ID set in database');

                // $picture->setOriginalPath('');
                // $logger->info('Original path cleared');

                // $picture->setPreviewPath('');
                // $logger->info('Preview path cleared');

                $picture->update();
                $logger->info('Picture table updated');

                // $DIC->filesystem()->web()->delete($path);
            } else {
                $logger->info('File does not exist');
            }
        };
    }

    /**
     * Moves videos to the resource storage and updates the video block entity with the resource ID and clears the path.
     *
     * @return void
     */
    public static function moveVideosToResourceStorage(): void
    {
        global $DIC;

        $videoBlocks = \SRAG\Learnplaces\persistence\entity\VideoBlock::get();
        foreach ($videoBlocks as $videoBlock) {
            $path = $videoBlock->getPath();
            if ($DIC->filesystem()->web()->has($path)) {
                $stream = $DIC->filesystem()->web()->readStream($path);
                $resourceId = $DIC->resourceStorage()->manage()
                    ->stream($stream, new ilLearnplacesStakeholder(), '')
                    ->serialize();
                $videoBlock->setResourceId($resourceId);
                $videoBlock->setPath('');
                $videoBlock->update();
                // $DIC->filesystem()->web()->delete($path);
            }
        };
    }
}
