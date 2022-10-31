<?php

namespace fluxlabs\learnplaces\Adapters\Api;

use fluxlabs\learnplaces\Core\Domain;

class CourseMenuData
{
    private array $data = [];

    public static function fromCourses(array $courses) : CourseMenuData
    {
        $obj = new self();
        foreach ($courses as $course) {
            $obj->appendCourseMenuItem($course);
        }
        return $obj;
    }

    private function __construct()
    {

    }

    public function appendCourseMenuItem(Domain\Models\Course $course)
    {
        $menuItem = new class {
            public int $refId;
            public string $title;

            public static function fromCourse(Domain\Models\Course $course) : object
            {
                return new self($course->ref_id, $course->title);
            }

            public function __construct(
                int $refId,
                string $title
            ) {
                $this->refId = $refId;
                $this->title = $title;
            }
        };

        $this->data[] = $menuItem::fromCourse($course);
    }

}