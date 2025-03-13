<?php

namespace RepositoryObject\Learnplaces\classes\api\Core;

use JetBrains\PhpStorm\NoReturn;
use ILIAS\HTTP\Response\ResponseHeader;
use ILIAS\Filesystem\Stream\Streams;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;

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
     * @param       $error_code
     * @param array $data
     * @param       $msg
     * @return void
     * @throws ResponseSendingException
     */
    public static function send(int $status_code = 201, string $error_code = null, array $data = []): void
    {
        global $DIC;
        $response = $DIC->http()->response()
            ->withHeader(ResponseHeader::CONTENT_TYPE, 'application/json')
            ->withStatus($status_code)
            ->withBody(Streams::ofString(json_encode( [$status_code, $error_code, $data])));

        $DIC->http()->saveResponse($response);
        $DIC->http()->sendResponse();
        $DIC->http()->close();
    }
}