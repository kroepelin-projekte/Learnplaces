<?php

namespace fluxlabs\learnplaces\Adapters\Config;


abstract class AbstractHasAccessToCourse {
    abstract public function hasAccessToCourse(int $id): bool;
}