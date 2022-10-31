<?php

namespace fluxlabs\learnplaces\Adapters\Config;

use fluxlabs\learnplaces\Core\Ports;
use fluxlabs\learnplaces\Core\Domain\Models\Courses;
use fluxlabs\learnplaces\Adapters\Storage;


class OutboundsAdapter implements Ports\Outbounds
{
    private string $apiBaseUrl;
    /**
     * @var Storage\DatabaseConfig
     */
    private $databaseConfig;
    private AbstractGroupReadableLearnplacesByCourses $coursesOfReadableLearnplaces;

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
            $config->getRefIdsFilteredByReadPermission
        );
    }

    private function __construct(
        string $apiBaseUrl,
        Storage\DatabaseConfig $databaseConfig,
        AbstractGroupReadableLearnplacesByCourses $refIdsFilteredByReadPermission
    ) {
        $this->apiBaseUrl = $apiBaseUrl;
        $this->databaseConfig = $databaseConfig;
        $this->coursesOfReadableLearnplaces = $refIdsFilteredByReadPermission;
    }

    public function getAllLearnplaceRefIds() : array
    {
        return Storage\LearnplaceRepository::new($this->databaseConfig)->getAllLearnplaceRefIds();
    }

    /**
     * @param $learnplaceRefIds
     * @return Courses[]
     */
    public function getLearnplaceCourses($learnplaceRefIds) : array
    {
        return Storage\CourseRepository::new($this->databaseConfig)->getLearnplaceCoursesSubset($learnplaceRefIds);
    }

    public function groupReadableLearnplacesByCourses(array $ref_ids) : array
    {
        return $this->coursesOfReadableLearnplaces->groupReadableLearnplacesByCourses($ref_ids);
    }

    public function getApiBaseUrl() : string
    {
        return $this->apiBaseUrl;
    }
}