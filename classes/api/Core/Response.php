<?php

namespace RepositoryObject\Learnplaces\classes\api\Core;

use ILIAS\HTTP\Response\ResponseHeader;
use ILIAS\Filesystem\Stream\Streams;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;

class Response
{

    /**
     * @description return a Server Error to the Client
     * @return void
     * @throws ResponseSendingException
     */
    public static function serverError(): void
    {
        self::send(500, 'SERVER_ERROR');
    }

    /**
     * @description Return to the client
     * @param int         $status_code
     * @param string|null $error_code
     * @param array       $data
     * @return void
     * @throws ResponseSendingException
     */
    public static function send(int $status_code = 201, string $error_code = null, array $data = []): void
    {
        global $DIC;

        $response_body['status_code'] = $status_code;
        $response_body['error_code'] = $error_code;
        $response_body['data'] = $data;

        $response = $DIC->http()->response()
            ->withHeader(ResponseHeader::CONTENT_TYPE, 'application/json')
            ->withStatus($status_code)
            ->withBody(Streams::ofString(json_encode($response_body)));

        $DIC->http()->saveResponse($response);
        $DIC->http()->sendResponse();
        $DIC->http()->close();
    }
}
