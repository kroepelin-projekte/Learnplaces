<?php

namespace KPG\Learnplaces\Setup;

use ILIAS\Setup\Agent\NullAgent;
use KPG\Learnplaces\Setup\Migrations\LearnplacesResourceStorageMigrationPictures;
use KPG\Learnplaces\Setup\Migrations\LearnplacesResourceStorageMigrationVideos;

class LearnplacesSetupAgent extends NullAgent
{
    public function getMigrations(): array
    {
        return [
            "ResourceStorageMigrationPictures" => new LearnplacesResourceStorageMigrationPictures(),
            "ResourceStorageMigrationVideos" => new LearnplacesResourceStorageMigrationVideos(),
        ];
    }
}
