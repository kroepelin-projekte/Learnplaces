<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use KPG\Learnplaces\util\QrCode;

class VerifyQRCode
{
    public function endpoint(array $params, array $request_body)
    {
        $obj_qr_code = new QrCode();
        $result = $obj_qr_code->validateToken(htmlspecialchars($params['token']));
        if ($result === 0) {
            Response::send(400, "QR_CODE_INVALID", ["valid" => false]);
        } elseif ($result === 1) {
            Response::send(201, null, ["valid" => true]);
        } else {
            Response::send(400, "QR_CODE_USER_WARS_HERE", ["valid" => true]);
        }
    }
}