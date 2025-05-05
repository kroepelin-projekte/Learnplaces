<?php

namespace KPG\Learnplaces\util;

use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\persistence\repository\LearnplaceRepository;
use KPG\Learnplaces\persistence\entity\VisitJournal;

class QrCode
{
    private const QR_CODE_SECRET = "faa7482c9135aa1628e19d7145386ee49f452e7ad3f417d9818748dc2a3b0b89";

    public function createToken(int $learn_place_id): string
    {
        return hash('sha256', $learn_place_id . "-" . self::QR_CODE_SECRET);
    }

    public function validateToken(string $backend_token, string $client_token): bool
    {
        return hash_equals($backend_token, $client_token);
    }

}