<?php

namespace fluxlabs\learnplaces\Core\Domain;

use fluxlabs\learnplaces\Core\Ports;
use fluxlabs\learnplaces\Core\Domain\Models;

class PwaAggregate
{

    private Ports\DomainEventPublisher $domainEventPublisher;
    private array $courses;

    public static function new(
        Ports\DomainEventPublisher $domainEventPublisher
    )
    {
        return new self($domainEventPublisher);
    }

    private function __construclst(Ports\DomainEventPublisher $domainEventPublisher)
    {
        $this->domainEventPublisher = $domainEventPublisher;
    }


    public function createCourses(
        array $courses
    ) {

    }

    private function applyCourseMenuDataCreated(
        array $courses
    ) {
        $this->courses = $courses;

        $this->domainEventPublisher->coursesCreated(
            $courses
        );
    }

}