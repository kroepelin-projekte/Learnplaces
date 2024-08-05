<?php

declare(strict_types=1);

use ILIAS\ResourceStorage\Stakeholder\AbstractResourceStakeholder;

class ilLearnplacesStakeholder extends AbstractResourceStakeholder
{
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return 'learning_places';
    }

    /**
     * @return int
     */
    public function getOwnerOfNewResources(): int
    {
        return SYSTEM_USER_ID;
    }
}
