<?php

namespace fluxlabs\learnplaces\Core\Ports;

use fluxlabs\learnplaces\Core\Domain;

class Service
{
    private Outbounds $outbounds;
    private DomainEventPublisher $domainEventPublisher;

    public static function new(
        Outbounds $outbounds,
        DomainEventPublisher $domainEventPublisher
    )
    {
        return new self($outbounds, $domainEventPublisher);
    }

    private function __construct( Outbounds $outbounds, DomainEventPublisher $domainEventPublisher)
    {
        $this->outbounds = $outbounds;
        $this->domainEventPublisher = $domainEventPublisher;
    }


    public function createApiBaseUrl() : void
    {
        $allLearnplaceRefIds = $this->outbounds->getAllLearnplaceRefIds();
        $courseRefIds = $this->outbounds->groupReadableLearnplaceRefIdsByCourseRefIds($allLearnplaceRefIds);

        $this->outbounds->getCourses($courseRefIds);
    }

    /**
     * @return  Domain\Models\Course[]
     */
    public function createCourses() : void
    {
        $allLearnplaceRefIds = $this->outbounds->getAllLearnplaceRefIds();
        $groupedLearnplaceRefIds = $this->outbounds->groupReadableLearnplaceRefIdsByCourseRefIds($allLearnplaceRefIds);

        $courses = $this->outbounds->getCourses(array_keys($groupedLearnplaceRefIds));
        $this->domainEventPublisher->coursesCreated($courses);
    }

    /**
     * @return  Domain\Models\Course
     */
    public function createCourse($id) : void {
        if($this->outbounds->hasAccessToCourse($id) === false) {
            return;
        }
        $courses = $this->outbounds->getCourses([$id]);
        $this->domainEventPublisher->courseCreated($courses[0]);
    }

    /**
     * @return  Domain\Models\Course[]
     */
    public function createLearnplaces(int $courseId) : void
    {
        $allLearnplaceRefIds = $this->outbounds->getAllLearnplaceRefIds();
        $groupedLearnplaceRefIds = $this->outbounds->groupReadableLearnplaceRefIdsByCourseRefIds($allLearnplaceRefIds);

        $relevantLearnplaceRefIds = $groupedLearnplaceRefIds[$courseId];

        $learnplaces = $this->outbounds->getLearnplaces($relevantLearnplaceRefIds);
        $this->domainEventPublisher->learnplacesCreated($learnplaces);
    }

}