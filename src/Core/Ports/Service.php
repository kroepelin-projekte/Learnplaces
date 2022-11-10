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

}