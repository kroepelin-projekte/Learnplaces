<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\service\filesystem;

use ilLearnplacesStakeholder;

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

        $pictures = \SRAG\Learnplaces\persistence\entity\Picture::get();
        foreach ($pictures as $picture) {
            $path = $picture->getOriginalPath();
            if ($DIC->filesystem()->web()->has($path)) {
                $stream = $DIC->filesystem()->web()->readStream($path);
                $resourceId = $DIC->resourceStorage()->manage()
                    ->stream($stream, new ilLearnplacesStakeholder(), '')
                    ->serialize();
                $picture->setResourceId($resourceId);
                $picture->setOriginalPath('');
                $picture->setPreviewPath('');
                $picture->update();
                $DIC->filesystem()->web()->delete($path);
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
                $DIC->filesystem()->web()->delete($path);
            }
        };
    }
}
