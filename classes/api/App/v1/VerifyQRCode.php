<?php

namespace KPG\Learnplaces\api\App\v1;

use RepositoryObject\Learnplaces\classes\api\Core\Response;
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
        global $DIC;
        $client_token = htmlspecialchars($params['token']);
        $db = $DIC->database();

        $set = $db->queryF(
            "SELECT pk_id, qr_code_token
            FROM xsrl_configuration
            WHERE xsrl_configuration.qr_code_token = %s",
            ['text'],
            [$client_token]
        );
        if ($rec = $db->fetchAssoc($set)) {
            $result = $db->query(
                "SELECT * FROM xsrl_visit_journal WHERE fk_learnplace_id = " . $rec['pk_id']
                . " AND user_id = " . $DIC->user()->getId()
            );

            if ($result->rowCount() != 0) {
                Response::send(200, "QR_CODE_USER_WAS_HERE", ["found" => true, "first_time_found" => false, "id" => $rec['pk_id']]);
            }

            $ar_visit = new VisitJournal();
            $ar_visit->setUserId($DIC->user()->getId())->setFkLearnplaceId($rec['pk_id'])->setTime(time())->create();
            Response::send(200, null, ["found" => true, "first_time_found" => true, "id" => $rec['pk_id']]);
        }

        Response::send(200, "QR_CODE_NOT_FOUND", ["found" => false]);
    }
}