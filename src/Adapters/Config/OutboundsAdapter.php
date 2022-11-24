<?php

namespace fluxlabs\learnplaces\Adapters\Config;

use fluxlabs\learnplaces\Core\Ports;
use fluxlabs\learnplaces\Core\Domain\Models\Course;
use fluxlabs\learnplaces\Adapters\Storage;
use fluxlabs\learnplaces\Core\Domain\Models\Learnplace;
use fluxlabs\learnplaces\Core\Domain\Models\Location;
use fluxlabs\learnplaces\Core\Domain\Models\User;

class OutboundsAdapter implements Ports\Outbounds
{
    private string $apiBaseUrl;
    /**
     * @var Storage\DatabaseConfig
     */
    private $databaseConfig;
    private AbstractGroupReadableLearnplacesByCourses $coursesOfReadableLearnplaces;
    private AbstractHasAccessToObject $checkAccess;
    private AbstractCurrentUser $currentUser;

    public static function new(
        Config $config
    ) {
        return new self(
            $config->apiBaseUrl,
            Storage\DatabaseConfig::new(
                $config->iliasDatabaseHost,
                $config->iliasDatabaseName,
                $config->iliasDatabaseUser,
                $config->iliasDatabasePassword
            ),
            $config->getRefIdsFilteredByReadPermission,
            $config->hasAccessToCourse,
            $config->currentUser
        );
    }

    private function __construct(
        string $apiBaseUrl,
        Storage\DatabaseConfig $databaseConfig,
        AbstractGroupReadableLearnplacesByCourses $refIdsFilteredByReadPermission,
        AbstractHasAccessToObject $hasAccessToCourse,
        AbstractCurrentUser  $currentUser
    ) {
        $this->apiBaseUrl = $apiBaseUrl;
        $this->databaseConfig = $databaseConfig;
        $this->coursesOfReadableLearnplaces = $refIdsFilteredByReadPermission;
        $this->checkAccess = $hasAccessToCourse;
        $this->currentUser = $currentUser;
    }

    public function getAllLearnplaceRefIds() : array
    {
        return Storage\LearnplaceRepository::new($this->databaseConfig)->getAllLearnplaceRefIds();
    }

    /**
     * @param $courseRefIds
     * @return Course[]
     */
    public function getCourses($courseRefIds) : array
    {
        return Storage\CourseRepository::new($this->databaseConfig)->getCourses($courseRefIds);
    }

    /**
     * @param array $learnPlaceRefIds
     * @return Learnplace[]
     */
    public function getLearnplaces($learnPlaceRefIds) : array
    {
        return Storage\LearnplaceRepository::new($this->databaseConfig)->getLearnplaces($learnPlaceRefIds);
    }


    public function groupReadableLearnplaceRefIdsByCourseRefIds(array $ref_ids) : array
    {
        return $this->coursesOfReadableLearnplaces->groupReadableLearnplacesByCourses($ref_ids);
    }

    public function hasAccessToCourse(int $refId) : bool
    {
        return $this->checkAccess->hasAccess($refId);
    }

    public function getApiBaseUrl() : string
    {
        return $this->apiBaseUrl;
    }

    public function getDefaultLocation() : Location
    {
        return Storage\LocationRepository::new($this->databaseConfig)->getDefaultLocation();
    }

    public function getLearnplaceLocation(int $id) : Location
    {
        return Storage\LocationRepository::new($this->databaseConfig)->getLearnplaceLocation($id);
    }

    public function getCourseLocation(int $id) : Location
    {
        return Storage\LocationRepository::new($this->databaseConfig)->getCourseLocation($id);

    }

    public function getCurrentUser() : User
    {
        return $this->currentUser->getCurrentUser();
    }

    public function getLearnplaceContents(int $id) : array
    {
        return Storage\LearnplaceRepository::new($this->databaseConfig)->getLearnplaceContents($id);
    }

    public function hasAccessToLearnplace(int $refId) : bool
    {
        return $this->checkAccess->hasAccess($refId);
    }
}