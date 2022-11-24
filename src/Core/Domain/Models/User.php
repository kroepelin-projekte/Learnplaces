<?php

namespace fluxlabs\learnplaces\Core\Domain\Models;

use fluxlabs\learnplaces\Adapters\Api\IdValue;

class User extends IliasObject
{
    public string $id;
    public string $title;
    public string $email;
    public string $objectType = "usr";

    public static function new(
        int $usrId,
        string $firstname,
        string $lastName,
        string $email
    ) : self {
        return new self($usrId, $firstname . " " . $lastName, $email);
    }

    private function __construct(
        int $usrId,
        string $title,
        string $email
    ) {
        $this->id = "usrId/" . $usrId;
        $this->title = $title;
        $this->email = $email;
    }
}