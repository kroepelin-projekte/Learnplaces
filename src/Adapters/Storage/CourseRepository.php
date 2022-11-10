<?php

namespace fluxlabs\learnplaces\Adapters\Storage;

use PDOException;
use PDO;
use fluxlabs\learnplaces\Core\Domain\Models\Course;

class CourseRepository
{

    private DatabaseConfig $databaseConfig;

    public static function new(DatabaseConfig $databaseConfig) : CourseRepository
    {
        return new self($databaseConfig);
    }

    private function __construct(DatabaseConfig $databaseConfig)
    {
        $this->databaseConfig = $databaseConfig;
    }

    /**
     * @param int[] $courseRefIds
     * @return Course[]
     */
    public function getCourses(array $courseRefIds) : array
    {
        try {
            $conn = new PDO("mysql:host=" . $this->databaseConfig->iliasDatabaseHost . ";dbname=" . $this->databaseConfig->iliasDatabaseName,
                $this->databaseConfig->iliasDatabaseUser, $this->databaseConfig->iliasDatabasePassword);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        $courseRefIdFilter = implode(",", $courseRefIds);

        $sql = "SELECT * FROM ilias.object_data as crs_obj "
            ." inner join object_reference as crs_ref on crs_ref.obj_id = crs_obj.obj_id  "
            ." where crs_obj.type like 'crs' and crs_ref.ref_id in ('" . $courseRefIdFilter . "')";
        $result = $conn->query($sql);

        $data = [];

        if ($result->rowCount() > 0) {
            // output data of each row
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $data[] = Course::new(
                    $row['ref_id'],
                    $row['obj_id'],
                    $row['title'],
                    $row['description'],
                );
            }
        }

        return $data;
    }

}