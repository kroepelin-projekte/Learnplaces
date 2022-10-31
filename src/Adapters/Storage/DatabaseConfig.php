<?php

namespace fluxlabs\learnplaces\Adapters\Storage;

class DatabaseConfig
{
    public string $iliasDatabaseHost;
    public string $iliasDatabaseName;
    public string $iliasDatabaseUser;
    public string $iliasDatabasePassword;

    public static function new(
        string $iliasDatabaseHost,
        string $iliasDatabaseName,
        string $iliasDatabaseUser,
        string $iliasDatabasePassword
    ) {
        return new self($iliasDatabaseHost, $iliasDatabaseName, $iliasDatabaseUser, $iliasDatabasePassword);
    }

    private function __construct(
        string $iliasDatabaseHost,
        string $iliasDatabaseName,
        string $iliasDatabaseUser,
        string $iliasDatabasePassword
    ) {
        /** php8 constructor(public string $iliasDatabaseHost, public string $iliasDatabase, public string $iliasDatabaseUser, public string $iliasDatabasePassword)  */

        $this->iliasDatabaseHost = $iliasDatabaseHost;
        $this->iliasDatabaseName = $iliasDatabaseName;
        $this->iliasDatabaseUser = $iliasDatabaseUser;
        $this->iliasDatabasePassword = $iliasDatabasePassword;
    }
}