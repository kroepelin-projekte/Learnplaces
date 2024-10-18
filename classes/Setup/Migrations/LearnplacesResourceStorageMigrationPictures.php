<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\Setup\Migrations;

error_reporting(E_ALL);
ini_set('display_errors', '1');

use _PHPStan_01e5828ef\Nette\Neon\Exception;
use ILIAS\DI\Container;
use ILIAS\Filesystem\Stream\Stream;
use ILIAS\Setup\Environment;
use ILIAS\Setup\Migration;
use ILIAS\Setup\Objective;
use ilLearnplacesStakeholder;
use ilResourceStorageMigrationHelper;
use InitResourceStorage;

class LearnplacesResourceStorageMigrationPictures implements Migration
{
    private ilResourceStorageMigrationHelper $helper;

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return "Learnplaces Pictures ResourceStorage Migration";
    }

    /**
     * Tell the default amount of steps to be executed for one run of the migration.
     * Return Migration::INFINITE if all units should be migrated at once.
     */
    public function getDefaultAmountOfStepsPerRun(): int
    {
        return 5000;
    }

    /**
     * Objectives the migration depends on.
     *
     * @throw UnachievableException if the objective is not achievable
     * @return Objective[]
     */
    public function getPreconditions(Environment $environment): array
    {
        return ilResourceStorageMigrationHelper::getPreconditions();
    }

    /**
     * Prepare the migration by means of some environment.
     *
     * This is not supposed to modify the environment, but will be run to prime the
     * migration object to run `step` and `getRemainingAmountOfSteps` afterwards.
     */
    public function prepare(Environment $environment) : void
    {
        $this->helper = new ilResourceStorageMigrationHelper(
            new ilLearnplacesStakeholder(),
            $environment
        );
    }

    /**
     *  Run one step of the migration.
     */
    public function step(Environment $environment): void
    {
        $pictures = \SRAG\Learnplaces\persistence\entity\Picture::get();

        $db = $this->helper->getDatabase();
        $rec = $db->query("SELECT pk_id, original_path FROM xsrl_picture WHERE resource_id IS NULL LIMIT 1");
        $res = $db->fetchObject($rec);

        $original_path = ltrim($res->original_path, './');
        $absolute_path = CLIENT_WEB_DIR . '/' . $original_path;

        $plugin_id_and_owner = explode('/', $original_path)[0];
        $owner = (int) preg_replace('/\D/', '', $plugin_id_and_owner);

        if (!is_file($absolute_path)) {
            throw new \Exception("Could not find file: " . $absolute_path);
        }

        $identification = $this->helper->movePathToStorage($absolute_path, $owner);

        if (is_null($identification)) {
            throw new \Exception("Could not move file to storage");
        }

        $resource_id = $identification->serialize();

        $db->manipulateF(
            "UPDATE xsrl_picture SET resource_id = %s WHERE pk_id = %s",
            ['text', 'integer'],
            [$resource_id, $res->pk_id]
        );
    }

    /**
     * Count up how many "things" need to be migrated. This helps the admin to
     * decide how big he can create the steps and also how long a migration takes
     */
    public function getRemainingAmountOfSteps(): int
    {
        $db = $this->helper->getDatabase();
        $rec = $db->query("SELECT COUNT(*) AS amount FROM xsrl_picture WHERE resource_id IS NULL");
        $res = $this->helper->getDatabase()->fetchObject($rec);
        return (int) $res->amount;
    }
}