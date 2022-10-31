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
        $courseRefIds = $this->outbounds->groupReadableLearnplacesByCourses($allLearnplaceRefIds);

        $this->outbounds->getLearnplaceCourses($courseRefIds);
    }

    /**
     * @return  Domain\Models\Course[]
     */
    public function createCourses() : void
    {
        $allLearnplaceRefIds = $this->outbounds->getAllLearnplaceRefIds();
        $groupedLearnplaces = $this->outbounds->groupReadableLearnplacesByCourses($allLearnplaceRefIds);

        $courses = $this->outbounds->getLearnplaceCourses($groupedLearnplaces);

        $pwaAggregate = Domain\PwaAggregate::new($this->domainEventPublisher)->createCourses(

        );
    }

}