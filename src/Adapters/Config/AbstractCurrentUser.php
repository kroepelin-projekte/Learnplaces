<?php

namespace fluxlabs\learnplaces\Adapters\Config;

use fluxlabs\learnplaces\Core\Domain\Models\User;

abstract class AbstractCurrentUser {
    abstract public function getCurrentUser(): User;
}