<?php

namespace fluxlabs\learnplaces\Adapters\Storage;

use PDO;
use PDOException;
use fluxlabs\learnplaces\Core\Domain\Models\Learnplace;
use fluxlabs\learnplaces\Core\Domain\Models\TextContent;
use fluxlabs\learnplaces\Core\Domain\Models\DetailsContent;
use fluxlabs\learnplaces\Core\Domain\Models\ContentList;

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
     * @return PDO
     */
    private function connection() : PDO
    {
        try {
            $connection = new PDO("mysql:host=" . $this->databaseConfig->iliasDatabaseHost . ";dbname=" . $this->databaseConfig->iliasDatabaseName,
                $this->databaseConfig->iliasDatabaseUser, $this->databaseConfig->iliasDatabasePassword);
            // set the PDO error mode to exception
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        return $connection;
    }

    /**
     * @return int[]
     */
    function getAllLearnplaceRefIds() : array
    {
        $sql = "SELECT xsrl_ref.ref_id FROM ilias.object_data as xsrl_obj inner join object_reference as xsrl_ref on xsrl_ref.obj_id = xsrl_obj.obj_id  where xsrl_obj.type like 'xsrl'";
        $result = $this->connection()->query($sql);

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
        $refIdFilter = implode(",", $learnplacesRefIds);

        $sql = "SELECT * FROM ilias.object_data as crs_obj "
            . " inner join object_reference as crs_ref on crs_ref.obj_id = crs_obj.obj_id and crs_ref.ref_id IN (" . $refIdFilter . ") "
            . " where crs_obj.type like 'xsrl' ";

        $result = $this->connection()->query($sql);

        $data = [];

        if ($result->rowCount() > 0) {
            // output data of each row
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $data[] = Learnplace::new(
                    $row['ref_id'],
                    $row['obj_id'],
                    $row['title'],
                    (string) $row['description'],
                );
            }
        }

        return $data;
    }

    /**
     * @param int $refId
     * @return TextContent[]
     */
    //todo
    public function getLearnplaceContents(int $refId) : object
    {
        $sql = "select block_content.pk_id, fk_block_id, content, content_type,  xsrl_visibility.Name as visibility from xsrl_block "
            . " inner join xsrl_learnplace on xsrl_learnplace.pk_id = xsrl_block.fk_learnplace_id "
            . " inner join object_data on object_data.obj_id = xsrl_learnplace.fk_object_id "
            . " inner join object_reference on object_data.obj_id = object_reference.obj_id and object_reference.deleted is null "
            . " inner join ( "
            . "  Select pk_id, title as content, fk_block_id, 'details' as content_type from xsrl_accordion_block "
            . " UNION "
            . " Select pk_id, content, fk_block_id, 'text' as content_type from xsrl_rich_text_block where NOT EXISTS  ( "
            . "  SELECT fk_block_id from xsrl_accordion_block_m where xsrl_accordion_block_m.fk_block_id = xsrl_rich_text_block.fk_block_id "
            . " ) "
            . " ) as block_content on block_content.fk_block_id = xsrl_block.pk_id "
            . " inner join xsrl_visibility on xsrl_visibility.pk_id = xsrl_block.fk_visibility "
            . " where object_reference.ref_id = " . $refId . " "
            . " Order by sequence";

        $result = $this->connection()->query($sql);

        $data = [];


        $getAccordionElements = function($pk_id) {
            $sql = "select xsrl_rich_text_block.pk_id, xsrl_rich_text_block.fk_block_id, content, 'text' as content_type,  xsrl_visibility.Name as visibility from xsrl_block "
                . " inner join xsrl_learnplace on xsrl_learnplace.pk_id = xsrl_block.fk_learnplace_id "
                . " inner join object_data on object_data.obj_id = xsrl_learnplace.fk_object_id "
                . " inner join object_reference on object_data.obj_id = object_reference.obj_id and object_reference.deleted is null "
                . " inner join xsrl_accordion_block_m on xsrl_accordion_block_m.fk_accordion_block = xsrl_block.pk_id "
                . " LEFT join xsrl_rich_text_block on xsrl_rich_text_block.fk_block_id = xsrl_accordion_block_m.fk_block_id "
                . " inner join xsrl_visibility on xsrl_visibility.pk_id = xsrl_block.fk_visibility "
                . " where xsrl_block.pk_id = " . $pk_id . " "
                . " Order by sequence";
            return $this->connection()->query($sql);
        };


        $data = [];
        if ($result->rowCount() > 0) {
            // output data of each row
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

                $elementId = "xsrlBlockId/" . $row['pk_id'];
                switch ($row['content_type']) {
                    case 'details':
                        $subdata = [];
                        $subelements = $getAccordionElements($row['pk_id']);
                        while ($subrow = $subelements->fetch(PDO::FETCH_ASSOC)) {
                            switch ($subrow['content_type']) {
                                case 'text':
                                    $subdata[] = TextContent::new(
                                        $elementId,
                                        $subrow['pk_id'],
                                        $subrow['content'],
                                        $subrow['visibility']
                                    );
                                    break;

                            }
                        }
                        $data[] = DetailsContent::new(
                            null,
                            $elementId,
                            $row['content'],
                            $row['visibility'],
                            $subdata
                        );
                        break;
                    case 'text':
                        $data[] = TextContent::new(
                            null,
                            $elementId,
                            $row['content'],
                            $row['visibility'],
                        );;
                        break;
                }
            }

            $contentList = ContentList::new(
                null,
                'content/refId/'.$refId,
                $data,
                'ALWAYS' //todo
            );

        }

        return $contentList;
    }

}