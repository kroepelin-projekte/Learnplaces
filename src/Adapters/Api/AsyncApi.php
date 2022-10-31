<?php

namespace fluxlabs\learnplaces\Adapters\Api;

use fluxlabs\learnplaces\Core\Ports;
use fluxlabs\learnplaces\Adapters\Config\OutboundsAdapter;
use fluxlabs\learnplaces\Core\Domain\Models\Course;

class AsyncApi
{

    private Ports\Outbounds $outbounds;
    private Ports\Service $service;

    public static function new(
        OutboundsAdapter $outboundsAdapter
    ) {
        return new self($outboundsAdapter, Ports\Service::new($outboundsAdapter));
    }

    private function __construct(OutboundsAdapter $outboundsAdapter, Ports\Service $service)
    {
        $this->outbounds = $outboundsAdapter;
        $this->service = $service;
    }

    public function createApiBaseUrl() : void
    {
        $this->response(
            StatusEnum::$STATUS_OK, 'baseUrlResponse', [
                'baseUrl' => $this->outbounds->getApiBaseUrl()
            ]
        );
    }

    public function createCourseMenuData() : void
    {
        $this->service->createCourses();
    }

    public function onCoursesCreated(
        array $courses
    ) : void {
        $menuData = CourseMenuData::fromCourses($courses);
        $this->publish(
            StatusEnum::$STATUS_OK,
            'CourseMenuDataCreated',
            $menuData
        );
    }

    private function publish($status, $messageName, $payload)
    {
        header("Content-Type:application/json");
        header("HTTP/2.0 " . $status);
        $response['status'] = $status;
        $response['status_message'] = $messageName;
        $response['data'] = $payload;

        $json_response = json_encode($response, JSON_UNESCAPED_SLASHES);
        echo $json_response;
        exit;
    }

}