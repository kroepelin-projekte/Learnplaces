<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\Setup;

use ilDatabaseUpdateSteps;
use ilDBInterface;

class UpdateSteps implements ilDatabaseUpdateSteps
{
    protected ilDBInterface $db;

    public function prepare(ilDBInterface $db): void
    {
        $this->db = $db;
    }
}
