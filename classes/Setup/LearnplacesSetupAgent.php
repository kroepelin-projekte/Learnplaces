<?php

namespace classes\Setup;

use classes\Setup\Migrations\LearnplacesResourceStorageMigration;
use ILIAS\Setup\Agent\NullAgent;

class LearnplacesSetupAgent extends NullAgent
{
    public function getMigrations() : array
    {
        return [
            "ResourceStorageMigration" => new LearnplacesResourceStorageMigration(),
        ];
    }
}