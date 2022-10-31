<?php

namespace fluxlabs\learnplaces\Adapters\Storage;

use PDO;
use PDOException;

class LearnplaceRepository
{
    private DatabaseConfig $databaseConfig;

    public static function new(DatabaseConfig $databaseConfig) : LearnplaceRepository
    {
        return new self($databaseConfig);
    }

    private function __constructor(DatabaseConfig $databaseConfig)
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

        $sql = 'SELECT xsrl_ref.ref_id FROM ilias.object_data as xsrl_obj inner join object_reference as xsrl_ref on xsrl_ref.obj_id = xsrl_obj.obj_id  where xsrl_obj.type like "xsrl"';
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

}