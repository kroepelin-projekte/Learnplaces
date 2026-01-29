<?php

use KPG\Learnplaces\container\PluginContainer;
use Repository\RepositoryObject\Learnplaces\classes\api\Config\Settings;
use KPG\Learnplaces\persistence\entity\AccordionBlock;
use KPG\Learnplaces\persistence\entity\AccordionBlockMember;
use KPG\Learnplaces\persistence\entity\Answer;
use KPG\Learnplaces\persistence\entity\AudioBlock;
use KPG\Learnplaces\persistence\entity\Block;
use KPG\Learnplaces\persistence\entity\Comment;
use KPG\Learnplaces\persistence\entity\CommentBlock;
use KPG\Learnplaces\persistence\entity\Configuration;
use KPG\Learnplaces\persistence\entity\ExternalStreamBlock;
use KPG\Learnplaces\persistence\entity\FeedbackBlock;
use KPG\Learnplaces\persistence\entity\Feedback;
use KPG\Learnplaces\persistence\entity\HorizontalLineBlock;
use KPG\Learnplaces\persistence\entity\ILIASLinkBlock;
use KPG\Learnplaces\persistence\entity\Learnplace;
use KPG\Learnplaces\persistence\entity\LearnplaceConstraint;
use KPG\Learnplaces\persistence\entity\Location;
use KPG\Learnplaces\persistence\entity\MapBlock;
use KPG\Learnplaces\persistence\entity\Picture;
use KPG\Learnplaces\persistence\entity\PictureBlock;
use KPG\Learnplaces\persistence\entity\PictureGalleryEntry;
use KPG\Learnplaces\persistence\entity\PictureUploadBlock;
use KPG\Learnplaces\persistence\entity\RichTextBlock;
use KPG\Learnplaces\persistence\entity\VideoBlock;
use KPG\Learnplaces\persistence\entity\Visibility;
use KPG\Learnplaces\persistence\entity\VisitJournal;
use ILIAS\ResourceStorage\Identification\ResourceIdentification;

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../vendor/autoload.php';

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
     * @return ilLearnplacesPlugin
     */
    public static function getInstance(): ilLearnplacesPlugin
    {
        if (is_null(self::$instance)) {
            $database = PluginContainer::resolve('database');
            $componentRepository = PluginContainer::resolve('componentRepository');
            self::$instance = new self($database, $componentRepository, 'xsrl');
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
        Settings::uninstall();
    }

    /**
     * @return void
     */
    private function dropDatabase(): void
    {
        $database = PluginContainer::resolve('database');
        $database->dropTable(AccordionBlock::returnDbTableName(), false);
        $database->dropTable(AccordionBlockMember::returnDbTableName(), false);
        $database->dropTable(Answer::returnDbTableName(), false);
        $database->dropTable(AudioBlock::returnDbTableName(), false);
        $database->dropTable(Block::returnDbTableName(), false);
        $database->dropTable(Comment::returnDbTableName(), false);
        $database->dropTable(CommentBlock::returnDbTableName(), false);
        $database->dropTable(Configuration::returnDbTableName(), false);
        $database->dropTable(ExternalStreamBlock::returnDbTableName(), false);
        $database->dropTable(FeedbackBlock::returnDbTableName(), false);
        $database->dropTable(Feedback::returnDbTableName(), false);
        $database->dropTable(HorizontalLineBlock::returnDbTableName(), false);
        $database->dropTable(ILIASLinkBlock::returnDbTableName(), false);
        $database->dropTable(Learnplace::returnDbTableName(), false);
        $database->dropTable(LearnplaceConstraint::returnDbTableName(), false);
        $database->dropTable(Location::returnDbTableName(), false);
        $database->dropTable(MapBlock::returnDbTableName(), false);
        $database->dropTable(Picture::returnDbTableName(), false);
        $database->dropTable(PictureBlock::returnDbTableName(), false);
        $database->dropTable(PictureGalleryEntry::returnDbTableName(), false);
        $database->dropTable(PictureUploadBlock::returnDbTableName(), false);
        $database->dropTable(RichTextBlock::returnDbTableName(), false);
        $database->dropTable(VideoBlock::returnDbTableName(), false);
        $database->dropTable(Visibility::returnDbTableName(), false);
        $database->dropTable(VisitJournal::returnDbTableName(), false);
    }

    /**
     * @return void
     */
    private function deleteFiles(): void
    {
        $resourceStorage = PluginContainer::resolve('resourceStorage');

        $pictures = Picture::get();
        foreach ($pictures as $picture) {
            $resourceId = $picture->getResourceId();
            $identification = new ResourceIdentification($resourceId);
            $resourceStorage->manage()->remove($identification, new ilLearnplacesStakeholder());
        }

        $videoBlocks = VideoBlock::get();
        foreach ($videoBlocks as $videoBlock) {
            $resourceId = $videoBlock->getResourceId();
            $identification = new ResourceIdentification($resourceId);
            $resourceStorage->manage()->remove($identification, new ilLearnplacesStakeholder());
        }
    }

    public static function _getIcon(string $a_type): string
    {
        return 'Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/templates/images/icon_xsrl.svg';
    }
}
