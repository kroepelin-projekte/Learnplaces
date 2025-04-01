<?php
namespace KPG\Learnplaces\api\Authenticator\Handler;
class PKCEUtilHandler {

    public function generateCode() {
        return bin2hex(random_bytes(32));
    }
}