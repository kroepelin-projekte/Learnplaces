<?php

namespace SRAG\Learnplaces\Setup;

use ILIAS\Setup;
use ILIAS\Setup\Objective;
use ILIAS\Setup\Metrics;
use ilDatabaseUpdateStepsExecutedObjective;
use ilDatabaseUpdateStepsMetricsCollectedObjective;
use ILIAS\Refinery\Factory as Refinery;
use ILIAS\Setup\Agent\NullAgent;
use SRAG\Learnplaces\Setup\Migrations\LearnplacesResourceStorageMigrationPictures;
use SRAG\Learnplaces\Setup\Migrations\LearnplacesResourceStorageMigrationVideos;

class LearnplacesSetupAgent extends NullAgent
{
    public function getMigrations(): array
    {
        return [
            "ResourceStorageMigrationPictures" => new LearnplacesResourceStorageMigrationPictures(),
            "ResourceStorageMigrationVideos" => new LearnplacesResourceStorageMigrationVideos(),
        ];
    }

/*    public function getUpdateObjective(Setup\Config $config = null): Setup\Objective
    {
        return new ilDatabaseUpdateStepsExecutedObjective(new UpdateSteps());
    }

    public function getStatusObjective(Metrics\Storage $storage): Setup\Objective
    {
        return new ilDatabaseUpdateStepsMetricsCollectedObjective($storage, new UpdateSteps());
    }*/
}