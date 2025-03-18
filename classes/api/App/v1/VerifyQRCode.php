<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
use KPG\Learnplaces\util\QrCode;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\persistence\repository\LearnplaceRepository;
use ilObject;
use KPG\Learnplaces\persistence\entity\VisitJournal;

class VerifyQRCode
{
    /**
     * @throws ResponseSendingException
     */
    public function endpoint(array $params, array $request_body): void
    {
        $logger = \ilLoggerFactory::getLogger('api___');

        $obj_learn_place = PluginContainer::resolve(LearnplaceRepository::class)->find((int) $params['id']);
        $client_token = htmlspecialchars($params['token']);
        $qr_code_util = new QrCode();
        $backend_token = $qr_code_util->createToken($obj_learn_place->getId());
        $logger->info("Client Token: " . $client_token . " Backend Token: " . $backend_token);
        if (!$qr_code_util->validateToken($client_token, $backend_token)) {
            Response::send(400, "QR_CODE_INVALID", ["valid" => false]);
        }
        global $DIC;
        $user_id = $DIC->user()->getId();
        $logger->info("User ID: " . $user_id);
        $learn_place_id = $obj_learn_place->getId();
        foreach (VisitJournal::get() as $ar) {
            if ($ar->getUserId() == $user_id && $ar->getFkLearnplaceId() == $learn_place_id) {
                $logger->info("User war hier");
                Response::send(400, "QR_CODE_USER_WARS_HERE", ["valid" => true]);
            }
        }
        $logger->info("Neuer Eintrag in die Tabelle");
        $ar_visit = new VisitJournal();
        $ar_visit->setUserId($user_id)->setFkLearnplaceId($learn_place_id)->setTime(time())->create();
        Response::send(200, null, ["valid" => true]);
    }
}