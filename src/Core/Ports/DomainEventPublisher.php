<?php
namespace fluxlabs\learnplaces\Core\Ports;

interface DomainEventPublisher {
    function coursesCreated(array $courses);
}