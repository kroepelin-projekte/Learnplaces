<?php

namespace fluxlabs\learnplaces\Adapters\Config;


abstract class AbstractHasAccessToCourse {
    abstract public function hasAccessToCourse(int $ref_id): bool;
}