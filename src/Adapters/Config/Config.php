<?php

namespace fluxlabs\learnplaces\Adapters\Config;

class Config
{
    public string $apiBaseUrl;
    public string $iliasDatabaseHost;
    public string $iliasDatabaseName;
    public string $iliasDatabaseUser;
    public string $iliasDatabasePassword;
    public AbstractGroupReadableLearnplacesByCourses $getRefIdsFilteredByReadPermission;

    public static function new(
        string $apiBaseUrl,
        string $iliasDatabaseHost,
        string $iliasDatabaseName,
        string $iliasDatabaseUser,
        string $iliasDatabasePassword,
        AbstractGroupReadableLearnplacesByCourses $getRefIdsFilteredByReadPermission)
    : self
    {
        return new self(
            $apiBaseUrl,
            $iliasDatabaseHost,
            $iliasDatabaseName,
            $iliasDatabaseUser,
            $iliasDatabasePassword,
            $getRefIdsFilteredByReadPermission
        );
    }

    private function __construct(
        string $apiBaseUrl,
        string $iliasDatabaseHost,
        string $iliasDatabaseName,
        string $iliasDatabaseUser,
        string $iliasDatabasePassword,
        AbstractGroupReadableLearnplacesByCourses $getRefIdsFilteredByReadPermission
    ) {
        $this->apiBaseUrl = $apiBaseUrl;
        $this->iliasDatabaseHost = $iliasDatabaseHost;
        $this->iliasDatabaseName = $iliasDatabaseName;
        $this->iliasDatabaseUser = $iliasDatabaseUser;
        $this->iliasDatabasePassword = $iliasDatabasePassword;
        $this->getRefIdsFilteredByReadPermission = $getRefIdsFilteredByReadPermission;
    }
}