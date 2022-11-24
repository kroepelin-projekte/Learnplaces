<?php

namespace fluxlabs\learnplaces\Core\Ports;

use fluxlabs\learnplaces\Core\Domain;
use stdClass;

class Service
{
    private Outbounds $outbounds;

    public static function new(
        Outbounds $outbounds
    ) {
        return new self($outbounds);
    }

    private function __construct(Outbounds $outbounds)
    {
        $this->outbounds = $outbounds;
    }

    public function createApiBaseUrl() : void
    {
        $allLearnplaceRefIds = $this->outbounds->getAllLearnplaceRefIds();
        $courseRefIds = $this->outbounds->groupReadableLearnplaceRefIdsByCourseRefIds($allLearnplaceRefIds);

        $this->outbounds->getCourses($courseRefIds);
    }

    public function projectDefaultLocation(callable $projectTo)
    {
        $object = new stdClass();
        $object->{'location'} = $this->outbounds->getDefaultLocation();
        $projectTo($object);
    }

    public function projectLearnplaceLocation(callable $projectTo, $id)
    {
        $object = new stdClass();
        $object->{'location'} = $this->outbounds->getLearnplaceLocation($id);
        $projectTo($object);
    }

    public function projectCourseLocation(callable $projectTo, $id)
    {
        $object = new stdClass();
        $object->{'location'} = $this->outbounds->getCourseLocation($id);
        $projectTo($object);
    }



    public function projectCourse(callable $projectTo, $id) : void
    {
        if ($this->outbounds->hasAccessToCourse($id) === false) {
            return;
        }
        $courses = $this->outbounds->getCourses([$id]);

        $object = new stdClass();
        $object->{'course'} = $courses[0];
        $projectTo($object);
    }

    public function projectCourses(callable $projectTo) : void
    {
        $allLearnplaceRefIds = $this->outbounds->getAllLearnplaceRefIds();
        $groupedLearnplaceRefIds = $this->outbounds->groupReadableLearnplaceRefIdsByCourseRefIds($allLearnplaceRefIds);

        $projectTo($this->outbounds->getCourses(array_keys($groupedLearnplaceRefIds)));
    }

    public function projectCurrentUser(callable $projectTo) : void
    {
        $projectTo($this->outbounds->getCurrentUser());
    }

    public function projectLearnplaces(callable $projectTo, int $courseId) : void
    {
        $allLearnplaceRefIds = $this->outbounds->getAllLearnplaceRefIds();
        $groupedLearnplaceRefIds = $this->outbounds->groupReadableLearnplaceRefIdsByCourseRefIds($allLearnplaceRefIds);

        $relevantLearnplaceRefIds = $groupedLearnplaceRefIds[$courseId];

        $projectTo($this->outbounds->getLearnplaces($relevantLearnplaceRefIds));
    }

}