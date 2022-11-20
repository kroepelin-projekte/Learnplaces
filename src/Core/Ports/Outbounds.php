<?php
namespace fluxlabs\learnplaces\Core\Ports;
use fluxlabs\learnplaces\Core\Domain\Models\Course;
use fluxlabs\learnplaces\Core\Domain\Models\Learnplace;

interface Outbounds {
    public function getApiBaseUrl(): string;

    public function getAllLearnplaceRefIds() : array;
    /**
     * @param array $groupedLearnplaceRefIds
     * @return Course[]
     */
    public function getCourses($groupedLearnplaceRefIds) : array;

    /**
     * @param array $learnPlaceRefIds
     * @return Learnplace[]
     */
    public function getLearnplaces($learnPlaceRefIds) : array;

    public function groupReadableLearnplaceRefIdsByCourseRefIds(array $ref_ids) : array;

    public function hasAccessToCourse(int $ref_id) : bool;
}