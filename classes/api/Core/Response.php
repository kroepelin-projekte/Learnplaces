<?php

namespace RepositoryObject\Learnplaces\classes\api\Core;

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
        http_response_code($status_code);
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, must-revalidate');
        echo json_encode(['status_code' => $status_code, 'error_code' => $error_code, 'data' => $data]);
        exit;
    }
}