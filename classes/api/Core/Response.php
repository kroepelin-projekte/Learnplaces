<?php

namespace RepositoryObject\Learnplaces\classes\api\Core;

use JetBrains\PhpStorm\NoReturn;
use ILIAS\HTTP\Response\ResponseHeader;
use ILIAS\Filesystem\Stream\Streams;

class Response
{

    /**
     * @description return a Server Error to the Client
     * @return void
     */
    public static function serverError(): void
    {
        self::send(500, 'SERVER_ERROR');
    }

    /**
     * @description Return to the client
     * @param int   $status_code
     * @param $error_code
     * @param array $data
     * @param $msg
     * @return void
     */
    public static function send(int $status_code = 201, string $error_code = null, array $data = []): void
    {
        $response_body['status_code'] = $status_code;
        $response_body['error_code'] = $error_code;
        $response_body['data'] = $data;

        global $DIC;
        $response = $DIC->http()->response()
            ->withHeader(ResponseHeader::CONTENT_TYPE, 'application/json')
            ->withStatus($status_code)
            ->withBody(Streams::ofString(json_encode($response_body)));

        $DIC->http()->saveResponse($response);
        $DIC->http()->sendResponse();
        $DIC->http()->close();
    }
}