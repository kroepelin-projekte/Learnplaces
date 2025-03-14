<?php

namespace KPG\Learnplaces\api\Database\Tables;

class CookieSecrets
{
    public const TABLE_NAME = 'kpg_xsrl_coo_sec';

    public static function install(): void
    {
        global $ilDB;
        if (!$ilDB->tableExists(self::TABLE_NAME)) {
            $fields = [
                "id" => [
                    "type" => "integer",
                    "length" => 8,
                    'notnull' => true,
                ],
                "user_id" => [
                    "type" => "integer",
                    "length" => 8,
                    'notnull' => true,
                ],
                "secret" => [
                    "type" => "clob",
                    "notnull" => true,
                ],
                "created_at" => [
                    "type" => "timestamp",
                    "notnull" => true,
                ],
                "updated_at" => [
                    "type" => "timestamp",
                    "notnull" => true,
                ]
            ];

            $ilDB->createTable(self::TABLE_NAME, $fields);
            $ilDB->createSequence(self::TABLE_NAME);
            $ilDB->addPrimaryKey(self::TABLE_NAME, ['id']);
        }
    }

    public static function uninstall(): void
    {
        global $ilDB;
        if (!$ilDB->tableExists(self::TABLE_NAME)) {
            $ilDB->dropTable(self::TABLE_NAME);
        }
    }

    public static function updateOrInsertSecret(string $secret, int $user_id): void
    {
        global $ilDB;
        $time = (new \DateTime())->format('Y-m-d H:i:s');
        $sql = "SELECT id FROM " . self::TABLE_NAME . " WHERE user_id =" . $ilDB->quote(
                $user_id);
        $result = $ilDB->fetchAssoc($ilDB->query($sql));
        if ($result !== null) {
            $ilDB->update(self::TABLE_NAME, ["updated_at" => ["timestamp", $time], "secret" => ["clob", $secret]],
                ["user_id" => ["int", $user_id]]
            );
        } else {
            $next_id = $ilDB->nextId(self::TABLE_NAME);
            $ilDB->insert(self::TABLE_NAME, [
                "id" => ["integer", $next_id],
                "user_id" => ["integer", $user_id],
                "secret" => ["clob", $secret],
                "created_at" => ["timestamp", $time],
                "updated_at" => ["timestamp", $time]
            ]);
        }
    }

    public static function getAll(): array
    {
        global $ilDB;
        $sql = "SELECT * FROM " . self::TABLE_NAME;
        $result = $ilDB->query($sql);
        if($result === null) {
            return [];
        }
        $records = [];
        while ($record = $ilDB->fetchAssoc($result)) {
            $records[] = $record;
        }

        return $records;


    }

    public static function deleteSecretByID(int $id): void
    {
        global $ilDB;
        $sql = "DELETE FROM " . self::TABLE_NAME . " WHERE id =" . $ilDB->quote(
                $id);
        $ilDB->query($sql);
    }
    public static function deleteSecretByUserID(int $user_id): void
    {
        global $ilDB;
        $sql = "DELETE FROM " . self::TABLE_NAME . " WHERE user_id =" . $ilDB->quote(
                $user_id);
        $ilDB->query($sql);
    }
}