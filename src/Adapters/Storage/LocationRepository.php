<?php

namespace fluxlabs\learnplaces\Adapters\Storage;

use PDO;
use PDOException;
use fluxlabs\learnplaces\Core\Domain;

class LocationRepository
{
    private DatabaseConfig $databaseConfig;

    public static function new(DatabaseConfig $databaseConfig) : LocationRepository
    {
        return new self($databaseConfig);
    }

    private function __construct(DatabaseConfig $databaseConfig)
    {
        $this->databaseConfig = $databaseConfig;
    }

    public function getDefaultLocation(): Domain\Models\Location {
        try {
            $conn = new PDO("mysql:host=" . $this->databaseConfig->iliasDatabaseHost . ";dbname=" . $this->databaseConfig->iliasDatabaseName,
                $this->databaseConfig->iliasDatabaseUser, $this->databaseConfig->iliasDatabasePassword);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }


        $sql = "SELECT value, keyword FROM settings where module = 'maps' and keyword in ('std_latitude','std_longitude','std_zoom')";
        $result = $conn->query($sql);

        $data = [];
        if ($result->rowCount() > 0) {
            // output data of each row
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $data[$row['keyword']] = $row['value'];
            }
        }

        return  Domain\Models\Location::new(
            'ilias', 'Default Location', 'location', $data['std_latitude'], $data['std_longitude'], 0, $data['std_zoom']
        );
    }

    public function getCourseLocation(int $id): Domain\Models\Location {
        try {
            $conn = new PDO("mysql:host=" . $this->databaseConfig->iliasDatabaseHost . ";dbname=" . $this->databaseConfig->iliasDatabaseName,
                $this->databaseConfig->iliasDatabaseUser, $this->databaseConfig->iliasDatabasePassword);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }


        $sql = "SELECT crs_settings.latitude, crs_settings.longitude, crs_settings.location_zoom, object_data.title, object_data.type FROM crs_settings
                inner join object_reference on object_reference.obj_id = crs_settings.obj_id
                inner join object_data on object_data.obj_id = object_reference.obj_id
                where object_reference.ref_id = $id";

        $result = $conn->query($sql);

        if ($result->rowCount() > 0) {
            // output data of each row
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $data = Domain\Models\Location::new(
                    $id, $row['title'], $row['type'], $row['latitude'], $row['longitude'], 0, $row['location_zoom']
                );
            }
        }

        return $data;
    }


    public function getLearnplaceLocation(int $id): Domain\Models\Location {
        try {
            $conn = new PDO("mysql:host=" . $this->databaseConfig->iliasDatabaseHost . ";dbname=" . $this->databaseConfig->iliasDatabaseName,
                $this->databaseConfig->iliasDatabaseUser, $this->databaseConfig->iliasDatabasePassword);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }


        $sql = "SELECT xsrl_location.latitude, xsrl_location.longitude, xsrl_location.radius, xsrl_configuration.map_zoom_level, object_data.title, object_data.type FROM xsrl_location
                inner join xsrl_learnplace on xsrl_learnplace.pk_id = xsrl_location.fk_learnplace_id 
                inner join xsrl_configuration on xsrl_configuration.pk_id = xsrl_learnplace.pk_id
                inner join object_reference on object_reference.obj_id = xsrl_learnplace.fk_object_id
                inner join object_data on object_data.obj_id = object_reference.obj_id
                where object_reference.ref_id = $id";

        $result = $conn->query($sql);

        if ($result->rowCount() > 0) {
            // output data of each row
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $data = Domain\Models\Location::new(
                    $id, $row['title'], $row['type'], $row['latitude'], $row['longitude'], $row['radius'], $row['map_zoom_level']
                );
            }
        }

        return $data;
    }


}