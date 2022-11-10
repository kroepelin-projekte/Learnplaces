<?php

namespace fluxlabs\learnplaces\Adapters\Api;

use fluxlabs\learnplaces\Core\Domain;

class CourseIdValueList
{
    public array $items = [];

    public static function fromCourses(array $courses) : CourseIdValueList
    {
        $obj = new self();
        foreach ($courses as $course) {
            $obj->appendItem($course);
        }
        return $obj;
    }

    private function __construct()
    {

    }

    public function appendItem(Domain\Models\Course $course)
    {
        $idValueObject = new  class {
            public static function fromCourse(Domain\Models\Course $course) : IdValue
            {
                return IdValue::new(
                    $course->ref_id,
                    $course->title
                );
            }
        };

        $this->items[$course->ref_id] = $idValueObject::fromCourse($course);
    }

}