<?php

namespace fluxlabs\learnplaces\Core\Ports;

use fluxlabs\learnplaces\Core\Domain\Models\Course;
use fluxlabs\learnplaces\Core\Domain\Models\Learnplace;
use fluxlabs\learnplaces\Core\Domain\Models\Location;
use fluxlabs\learnplaces\Core\Domain\Models\User;

interface Outbounds
{
    public function getApiBaseUrl() : string;

    public function getAllLearnplaceRefIds() : array;

    public function getCurrentUser() : User;

    /**
     * @param array $groupedLearnplaceRefIds
     * @return Course[]
     */
    public function getCourses($groupedLearnplaceRefIds) : array;

    public function getDefaultLocation() : Location;

    public function getLearnplaceLocation($id) : Location;
    public function getCourseLocation($id) : Location;

    /**
     * @param array $learnPlaceRefIds
     * @return Learnplace[]
     */
    public function getLearnplaces($learnPlaceRefIds) : array;

    public function groupReadableLearnplaceRefIdsByCourseRefIds(array $ref_ids) : array;

    public function hasAccessToCourse(int $ref_id) : bool;
}