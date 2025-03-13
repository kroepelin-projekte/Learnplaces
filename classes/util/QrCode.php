<?php

namespace KPG\Learnplaces\util;

use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\persistence\repository\LearnplaceRepository;
use KPG\Learnplaces\persistence\entity\VisitJournal;

class QrCode
{
    private const QR_CODE_SECRET = "faa7482c9135aa1628e19d7145386ee49f452e7ad3f417d9818748dc2a3b0b89";

    public function createToken(): string
    {
        $refinery = PluginContainer::resolve('refinery');
        $query = PluginContainer::resolve('query');

        if (!$query->has('ref_id')) {
            throw new \Exception('Learnplaces - getToken(): ref_id is missing');
        }

        $obj_id = \ilObject::_lookupObjectId($query->retrieve('ref_id', $refinery->kindlyTo()->int()));

        return hash('sha256', $obj_id . "-" . self::QR_CODE_SECRET);
    }

    public function validateToken(string $token): int
    {
        [$obj_id, $secret] = explode('-', $token);
        if (!is_numeric($obj_id) or $secret !== self::QR_CODE_SECRET) {
            return 0;
        }
        $learn_places_repo = PluginContainer::resolve(LearnplaceRepository::class);
        try {
            $obj_learn_place = $learn_places_repo->findByObjectId($obj_id);
        } catch (\Exception $e) {
            return 0;
        }
        if (!$obj_learn_place) {
            return 0;
        }

        global $DIC;
        $user_id = $DIC->user()->getId();
        $learn_place_id = $obj_learn_place->getId();

        foreach (VisitJournal::get() as $ar) {
            if ($ar->getUserId() == $user_id && $ar->getFkLearnplaceId() == $learn_place_id) {
                return 2;
            }
        }

        $ar_visit = new VisitJournal();
        $ar_visit->setUserId($user_id)->setFkLearnplaceId($learn_place_id)->setTime(time())->create();
        return 1;
    }

}