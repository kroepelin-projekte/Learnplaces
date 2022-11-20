<?php

namespace fluxlabs\learnplaces\Adapters\Api;

use fluxlabs\learnplaces\Core\Ports;
use fluxlabs\learnplaces\Adapters\Config\OutboundsAdapter;
use fluxlabs\learnplaces\Core\Domain\Models\Course;

class AsyncApi implements Ports\DomainEventPublisher
{

    private Ports\Outbounds $outbounds;
    private Ports\Service $service;

    public static function new(
        OutboundsAdapter $outboundsAdapter
    ) {
        return new self($outboundsAdapter);
    }

    private function __construct(OutboundsAdapter $outboundsAdapter)
    {
        $this->outbounds = $outboundsAdapter;
        $this->service =  Ports\Service::new($outboundsAdapter, $this);
    }

    public function createApiBaseUrl() : void
    {
        $this->publish(
            StatusEnum::$STATUS_OK, 'baseUrlCreated', [
                'baseUrl' => $this->outbounds->getApiBaseUrl()
            ]
        );
    }

    public function projectIdValueList($name, $parentId = null) : void
    {
        switch($name) {
            case 'courses':
                $this->service->createCourses();
                break;
            case 'learnplaces':
                $this->service->createLearnplaces($parentId);
                break;
        }
    }

    public function projectMetadata($name, $id) : void
    {
        switch($name) {
            case 'course':
                $this->service->createCourse($id);
                break;
        }
    }


    function coursesCreated(array $courses)
    {
        $courseIdValueList = CourseIdValueList::fromCourses($courses);
        $this->publish(
            StatusEnum::$STATUS_OK,
            'courses/idValueListProjected',
            $courseIdValueList
        );
    }

    function courseCreated(Course $course)
    {
        $this->publish(
            StatusEnum::$STATUS_OK,
            'course/courseMetadataProjected',
            $course
        );
    }


    private function publish($status, $address, $payload)
    {
        header("Content-Type:application/json");
        header("HTTP/2.0 " . $status);
        header("statusText: OK");
        header("address: ".$address);
        $response['data'] = $payload;

        $json_response = json_encode($response, JSON_UNESCAPED_SLASHES);
        echo $json_response;
        exit;
    }

    function learnplacesCreated(array $learnplaces)
    {
        $idValueList = LearnplaceIdValueList::fromLearnplaces($learnplaces);
        $this->publish(
            StatusEnum::$STATUS_OK,
            'learnplaces/idValueListProjected',
            $idValueList
        );
    }
}