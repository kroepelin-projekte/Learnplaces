<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use KPG\Learnplaces\util\QrCode;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;

class VerifyQRCode
{
    /**
     * @throws ResponseSendingException
     */
    public function endpoint(array $params, array $request_body): void
    {
        switch ((new QrCode())->validateToken(htmlspecialchars($params['token']))) {
            case 0:
                Response::send(400, "QR_CODE_INVALID", ["valid" => false]);
                break;
            case 1:
                Response::send(201, null, ["valid" => true]);
                break;
            default:
                Response::send(400, "QR_CODE_USER_WARS_HERE", ["valid" => true]);
                break;
        }
    }
}