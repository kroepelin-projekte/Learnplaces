<?php

use KPG\Learnplaces\container\PluginContainer;

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
        \Repository\RepositoryObject\Learnplaces\classes\api\Config\Settings::uninstall();
    }

    /**
     * @return void
     */
    private function dropDatabase(): void
    {
        $database = PluginContainer::resolve('database');
        $database->dropTable(\KPG\Learnplaces\persistence\entity\AccordionBlock::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\AccordionBlockMember::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\Answer::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\AudioBlock::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\Block::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\Comment::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\CommentBlock::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\Configuration::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\ExternalStreamBlock::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\FeedbackBlock::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\Feedback::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\HorizontalLineBlock::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\ILIASLinkBlock::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\Learnplace::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\LearnplaceConstraint::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\Location::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\MapBlock::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\Picture::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\PictureBlock::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\PictureGalleryEntry::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\PictureUploadBlock::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\RichTextBlock::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\VideoBlock::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\Visibility::returnDbTableName(), false);
        $database->dropTable(\KPG\Learnplaces\persistence\entity\VisitJournal::returnDbTableName(), false);
    }

    /**
     * @return void
     */
    private function deleteFiles(): void
    {
        $resourceStorage = PluginContainer::resolve('resourceStorage');

        $pictures = \KPG\Learnplaces\persistence\entity\Picture::get();
        foreach ($pictures as $picture) {
            $resourceId = $picture->getResourceId();
            $identification = new \ILIAS\ResourceStorage\Identification\ResourceIdentification($resourceId);
            $resourceStorage->manage()->remove($identification, new ilLearnplacesStakeholder());
        }

        $videoBlocks = \KPG\Learnplaces\persistence\entity\VideoBlock::get();
        foreach ($videoBlocks as $videoBlock) {
            $resourceId = $videoBlock->getResourceId();
            $identification = new \ILIAS\ResourceStorage\Identification\ResourceIdentification($resourceId);
            $resourceStorage->manage()->remove($identification, new ilLearnplacesStakeholder());
        }
    }
/*
    public function beforeActivation(): bool
    {
        $base_url = \Repository\RepositoryObject\Learnplaces\classes\api\Config\Settings::getBaseURL();
        if ($base_url === '') {
            global $DIC;
            $DIC->ui()->maintemplate()->setOnScreenMessage('failure', $this->txt("lang_before_Activation"));
            return false;
        } else {
            return true;
        }
    }
*/
    public static function _getIcon(string $a_type): string
    {
        return 'Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/templates/images/icon_xsrl.svg';
    }
}
