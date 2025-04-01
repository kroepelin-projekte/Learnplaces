<?php

declare(strict_types=1);

namespace KPG\Learnplaces\api\Database;

class OAuthEntityInstall
{
    public static function install(): void
    {
        global $DIC;
        $ilDB = $DIC->database();

        $fields = array(
            'id' => array(
                'notnull' => '1',
                'type' => 'integer',
                'length' => '8',
            ),
            'state' => array(
                'notnull' => '1',
                'type' => 'text',
                'length' => '2000',
            ),
            'code_challenge' => array(
                'notnull' => '1',
                'type' => 'text',
                'length' => '2000',
            ),
            'redirect_uri' => array(
                'notnull' => '1',
                'type' => 'text',
                'length' => '2000',
            ),
            'code' => array(
                'notnull' => '0',
                'type' => 'text',
                'length' => '2000',
            ),
            'expire' => array(
                'notnull' => '1',
                'type' => 'integer',
                'length' => '8',
            ),
        );

        if (!$ilDB->tableExists(OAuthEntity::TABLE_NAME)) {
            $ilDB->createTable(OAuthEntity::TABLE_NAME, $fields);

            if (!$ilDB->sequenceExists(OAuthEntity::TABLE_NAME)) {
                $ilDB->createSequence(OAuthEntity::TABLE_NAME);
            }
        }
    }
}
