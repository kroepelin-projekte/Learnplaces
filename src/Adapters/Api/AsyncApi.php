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
            StatusEnum::$STATUS_OK, 'baseUrlResponse', [
                'baseUrl' => $this->outbounds->getApiBaseUrl()
            ]
        );
    }

    public function projectCourseMenuData() : void
    {
        $this->service->createCourses();
    }


    function coursesCreated(array $courses)
    {
        $menuData = CourseMenuData::fromCourses($courses);
        $this->publish(
            StatusEnum::$STATUS_OK,
            'CourseMenuDataProjected',
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