<?php
namespace fluxlabs\learnplaces\Core\Ports;

use fluxlabs\learnplaces\Core\Domain\Models\Course;

interface DomainEventPublisher {
    function coursesCreated(array $courses);
    function courseCreated(Course $course);
    function learnplacesCreated(array $learnplaces);
}