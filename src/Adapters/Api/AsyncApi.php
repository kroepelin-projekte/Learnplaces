<?php

namespace fluxlabs\learnplaces\Adapters\Api;

use fluxlabs\learnplaces\Core\Ports;
use fluxlabs\learnplaces\Adapters\Config\OutboundsAdapter;
use fluxlabs\learnplaces\Core\Domain;

class AsyncApi
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
        $this->service = Ports\Service::new($outboundsAdapter, $this);
    }

    public function createApiBaseUrl() : void
    {
        $this->publish(
            StatusEnum::$STATUS_OK, 'baseUrlCreated', [
                'baseUrl' => $this->outbounds->getApiBaseUrl()
            ]
        );
    }

    public function projectIdValueList($name, $idOrParentId = null) : void
    {
        $event = 'IdValueListProjected';
        switch ($name) {
            case 'courses':
                $this->service->projectCourses($this->publishIdValueListProjection($name.'/'.$event));
                break;
            case 'learnplaces':
                $this->service->projectLearnplaces($this->publishIdValueListProjection($name.'/'.$event), $idOrParentId);
                break;
        }
    }


    public function projectObject(string $name, $idOrParentId = null)
    {
        $event = 'objectProjected';
        switch ($name) {
            case 'defaultLocation':
                $this->service->projectDefaultLocation($this->publishObjectProjection($name.'/'.$event));
                break;
            case 'learnplaceLocation':
                $this->service->projectLearnplaceLocation($this->publishObjectProjection($name.'/'.$event), $idOrParentId);
                break;
            case 'courseLocation':
                $this->service->projectCourseLocation($this->publishObjectProjection($name.'/'.$event), $idOrParentId);
                break;
            case 'course':
                $this->service->projectCourse($this->publishObjectProjection($name.'/'.$event), $idOrParentId);
                break;
            case 'currentUser':
                $this->service->projectCurrentUser($this->publishObjectProjection($name.'/'.$event));
                break;
        }
    }

    public function projectObjectList($name, $idOrParentId = null)
    {
        $event = 'objectListProjected';
        switch ($name) {
            case 'learnplaceContents':
                $this->service->projectLearnplaceContents($this->publishObjectListProjection($name.'/'.$event), $idOrParentId);
                break;
        }
    }


    private function publishIdValueListProjection(string $messageId)
    {
        /**
         * @param Domain\Models\IliasObject[]
         */
        return function (array $objectList) use ($messageId) {

            $idValueList = IdValueList::fromObjetList($objectList);

            $this->publish(
                StatusEnum::$STATUS_OK,
                $messageId,
                $idValueList
            );
        };
    }

    private function publishObjectListProjection(string $messageId)
    {
        return function (array $objectList) use ($messageId) {

            $objectItemList = ObjectItemList::fromObjetList($objectList);

            $this->publish(
                StatusEnum::$STATUS_OK,
                $messageId,
                $objectItemList
            );
        };
    }

    private function publishObjectProjection(string $messageId)
    {
        return function (object $payload) use ($messageId) {
            $this->publish(
                StatusEnum::$STATUS_OK,
                $messageId,
                $payload
            );
        };
    }

    private function publish($status, $address, $payload)
    {
        header("Content-Type:application/json");
        header("HTTP/2.0 " . $status);
        header("statusText: OK");
        header("address: " . $address);
        $response = $payload;

        $json_response = json_encode($response, JSON_UNESCAPED_SLASHES);
        echo $json_response;
        exit;
    }



}