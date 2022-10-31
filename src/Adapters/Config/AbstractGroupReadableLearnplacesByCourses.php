<?php

namespace fluxlabs\learnplaces\Adapters\Config;


abstract class AbstractGroupReadableLearnplacesByCourses {
    abstract public function groupReadableLearnplacesByCourses(array $ref_ids): array;
}