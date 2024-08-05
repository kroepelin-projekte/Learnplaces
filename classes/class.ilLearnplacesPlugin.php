<?php

use SRAG\Learnplaces\container\PluginContainer;

require_once __DIR__ . '/bootstrap.php';

/**
 * Class ilLearnplacesPlugin
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class ilLearnplacesPlugin extends ilRepositoryObjectPlugin
{
    public const PLUGIN_NAME = "Learnplaces";
    public const PLUGIN_ID = "xsrl";

    private static ?ilLearnplacesPlugin $instance = null;

    /**
     * ilLearnplacesPlugin constructor.
     */
    public function __construct()
    {
        $database = PluginContainer::resolve('database');
        $componentRepository = PluginContainer::resolve('componentRepository');
        parent::__construct($database, $componentRepository, 'xsrl');

        self::$instance = $this;
    }

    /**
     * @return ilLearnplacesPlugin
     */
    public static function getInstance(): ilLearnplacesPlugin
    {
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @return string
     */
    public function getPluginName(): string
    {
        return self::PLUGIN_NAME;
    }

    /**
     * @return bool
     */
    public function allowCopy(): bool
    {
        return true;
    }

    /**
     * @return void
     */
    protected function uninstallCustom(): void
    {
        $this->deleteFiles();
        $this->dropDatabase();
    }

    /**
     * @return void
     */
    private function dropDatabase(): void
    {
        $database = PluginContainer::resolve('ilDB');
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\AccordionBlock::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\AccordionBlockMember::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\Answer::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\AudioBlock::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\Block::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\Comment::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\CommentBlock::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\Configuration::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\ExternalStreamBlock::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\FeedbackBlock::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\Feedback::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\HorizontalLineBlock::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\ILIASLinkBlock::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\Learnplace::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\LearnplaceConstraint::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\Location::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\MapBlock::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\Picture::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\PictureBlock::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\PictureGalleryEntry::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\PictureUploadBlock::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\RichTextBlock::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\VideoBlock::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\Visibility::returnDbTableName(), false);
        $database->dropTable(\SRAG\Learnplaces\persistence\entity\VisitJournal::returnDbTableName(), false);
    }

    /**
     * @return void
     */
    private function deleteFiles(): void
    {
        $resourceStorage = PluginContainer::resolve('resourceStorage');

        $pictures = \SRAG\Learnplaces\persistence\entity\Picture::get();
        foreach ($pictures as $picture) {
            $resourceId = $picture->getResourceId();
            $identification = new \ILIAS\ResourceStorage\Identification\ResourceIdentification($resourceId);
            $resourceStorage->manage()->remove($identification, new ilLearnplacesStakeholder());
        }

        $videoBlocks = \SRAG\Learnplaces\persistence\entity\VideoBlock::get();
        foreach ($videoBlocks as $videoBlock) {
            $resourceId = $videoBlock->getResourceId();
            $identification = new \ILIAS\ResourceStorage\Identification\ResourceIdentification($resourceId);
            $resourceStorage->manage()->remove($identification, new ilLearnplacesStakeholder());
        }
    }
}
