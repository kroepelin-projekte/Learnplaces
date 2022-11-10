<?php
namespace fluxlabs\learnplaces\Core\Ports;
use fluxlabs\learnplaces\Core\Domain\Models\Course;

interface Outbounds {
    public function getApiBaseUrl(): string;

    public function getAllLearnplaceRefIds() : array;
    /**
     * @param array $groupedLearnplaceRefIds
     * @return Course[]
     */
    public function getCourses($groupedLearnplaceRefIds) : array;

    public function groupReadableLearnplaceRefIdsByCourseRefIds(array $ref_ids) : array;
}