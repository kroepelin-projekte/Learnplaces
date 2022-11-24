<?php

namespace fluxlabs\learnplaces\Adapters\Config;


abstract class AbstractHasAccessToObject {
    abstract public function hasAccess(int $id): bool;
}