<?php

namespace fluxlabs\learnplaces\Adapters\Storage;

use PDO;
use PDOException;
use fluxlabs\learnplaces\Core\Domain\Models\Learnplace;

class LearnplaceRepository
{
    private DatabaseConfig $databaseConfig;

    public static function new(DatabaseConfig $databaseConfig) : LearnplaceRepository
    {
        return new self($databaseConfig);
    }

    private function __construct(DatabaseConfig $databaseConfig)
    {
        $this->databaseConfig = $databaseConfig;
    }

    /**
     * @return int[]
     */
    function getAllLearnplaceRefIds(): array
    {
        try {
            $conn = new PDO("mysql:host=" . $this->databaseConfig->iliasDatabaseHost . ";dbname=" . $this->databaseConfig->iliasDatabaseName,
                $this->databaseConfig->iliasDatabaseUser, $this->databaseConfig->iliasDatabasePassword);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        $sql = "SELECT xsrl_ref.ref_id FROM ilias.object_data as xsrl_obj inner join object_reference as xsrl_ref on xsrl_ref.obj_id = xsrl_obj.obj_id  where xsrl_obj.type like 'xsrl'";
        $result = $conn->query($sql);

        $data = [];
        if ($result->rowCount() > 0) {
            // output data of each row
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row['ref_id'];
            }
        }

        return $data;
    }

    /**
     * @param int[] $learnplacesRefIds
     * @return Learnplace[]
     */
    public function getLearnplaces(array $learnplacesRefIds) : array
    {
        try {
            $conn = new PDO("mysql:host=" . $this->databaseConfig->iliasDatabaseHost . ";dbname=" . $this->databaseConfig->iliasDatabaseName,
                $this->databaseConfig->iliasDatabaseUser, $this->databaseConfig->iliasDatabasePassword);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        $refIdFilter = implode(",", $learnplacesRefIds);


        $sql = "SELECT * FROM ilias.object_data as crs_obj "
            ." inner join object_reference as crs_ref on crs_ref.obj_id = crs_obj.obj_id and crs_ref.ref_id IN (" . $refIdFilter . ") "
            ." where crs_obj.type like 'xsrl' ";

        $result = $conn->query($sql);

        $data = [];

        if ($result->rowCount() > 0) {
            // output data of each row
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $data[] = Learnplace::new(
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