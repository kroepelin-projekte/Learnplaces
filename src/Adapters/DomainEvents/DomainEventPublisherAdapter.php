<?php
namespace fluxlabs\learnplaces\Adapters\DomainEvents;
use fluxlabs\learnplaces\Core\Ports;
use \fluxlabs\learnplaces\Core\Domain;
use fluxlabs\learnplaces\Adapters\Api\AsyncApi;

class DomainEventPublisherAdapter implements Ports\DomainEventPublisher {

    private AsyncApi $asyncApi;

    function coursesCreated(array $courses)
    {
        $this->asyncApi->onCoursesCreated($courses);
    }
}