<#1>
<?php

use KPG\Learnplaces\persistence\entity\Visibility;
use KPG\Learnplaces\service\filesystem\PathHelper;


require_once('./Customizing/plugins/Repository/RepositoryObject/Learnplaces/vendor/autoload.php');

\KPG\Learnplaces\persistence\entity\AccordionBlock::installDB();
\KPG\Learnplaces\persistence\entity\AccordionBlockMember::installDB();
\KPG\Learnplaces\persistence\entity\Answer::installDB();
\KPG\Learnplaces\persistence\entity\AudioBlock::installDB();
\KPG\Learnplaces\persistence\entity\Block::installDB();
\KPG\Learnplaces\persistence\entity\Comment::installDB();
\KPG\Learnplaces\persistence\entity\CommentBlock::installDB();
\KPG\Learnplaces\persistence\entity\Configuration::installDB();
\KPG\Learnplaces\persistence\entity\ExternalStreamBlock::installDB();
\KPG\Learnplaces\persistence\entity\FeedbackBlock::installDB();
\KPG\Learnplaces\persistence\entity\Feedback::installDB();
\KPG\Learnplaces\persistence\entity\HorizontalLineBlock::installDB();
\KPG\Learnplaces\persistence\entity\ILIASLinkBlock::installDB();
\KPG\Learnplaces\persistence\entity\Learnplace::installDB();
\KPG\Learnplaces\persistence\entity\LearnplaceConstraint::installDB();
\KPG\Learnplaces\persistence\entity\Location::installDB();
\KPG\Learnplaces\persistence\entity\MapBlock::installDB();
\KPG\Learnplaces\persistence\entity\Picture::installDB();
\KPG\Learnplaces\persistence\entity\PictureBlock::installDB();
\KPG\Learnplaces\persistence\entity\PictureGalleryEntry::installDB();
\KPG\Learnplaces\persistence\entity\PictureUploadBlock::installDB();
\KPG\Learnplaces\persistence\entity\RichTextBlock::installDB();
\KPG\Learnplaces\persistence\entity\VideoBlock::installDB();
\KPG\Learnplaces\persistence\entity\Visibility::installDB();
\KPG\Learnplaces\persistence\entity\VisitJournal::installDB();

$visibilityAlways = new Visibility();
$visibilityNever = new Visibility();
$visibilityOnlyAtPlace = new Visibility();
$visibilityAfterVisitPlace = new Visibility();
$visibilityAfterVisitOtherPlace = new Visibility();

$visibilityAlways->setName(\KPG\Learnplaces\util\Visibility::ALWAYS);
$visibilityAlways->create();

$visibilityNever->setName(\KPG\Learnplaces\util\Visibility::NEVER);
$visibilityNever->create();

$visibilityOnlyAtPlace->setName(\KPG\Learnplaces\util\Visibility::ONLY_AT_PLACE);
$visibilityOnlyAtPlace->create();

$visibilityAfterVisitPlace->setName(\KPG\Learnplaces\util\Visibility::AFTER_VISIT_PLACE);
$visibilityAfterVisitPlace->create();

$visibilityAfterVisitOtherPlace->setName(\KPG\Learnplaces\util\Visibility::AFTER_VISIT_OTHER_PLACE);
$visibilityAfterVisitOtherPlace->create();
?>
<#2>
<?php
require_once('./Customizing/plugins/Repository/RepositoryObject/Learnplaces/vendor/autoload.php');
\KPG\Learnplaces\persistence\entity\Configuration::updateDB(); //map_zoom_level field added
?>
<#3>
<?php
require_once('./Customizing/plugins/Repository/RepositoryObject/Learnplaces/vendor/autoload.php');

function lowercaseFileExtension($filename)
{
    if ($filename === null || strlen($filename) === 0) {
        return $filename;
    }
    $info = pathinfo($filename);
    $filenameWithLcExtension =  $info['dirname'] . '/' . $info['filename'] . '.' . strtolower($info['extension']);
    if (file_exists($filename) && !file_exists($filenameWithLcExtension)) {
        rename($filename, $filenameWithLcExtension);
    }

    return $filenameWithLcExtension;
}

/**
 * @var \KPG\Learnplaces\persistence\entity\Picture[] $pictures
 */
$pictures = \KPG\Learnplaces\persistence\entity\Picture::get();
foreach ($pictures as $picture) {
    $originalPath = lowercaseFileExtension($picture->getOriginalPath());

    $originalInternalPath = \KPG\Learnplaces\service\filesystem\PathHelper::generatePluginInternalPathFrom(
        $originalPath
    );

    $previewPath = lowercaseFileExtension($picture->getPreviewPath());
    $previewInternalPath = \KPG\Learnplaces\service\filesystem\PathHelper::generatePluginInternalPathFrom(
        $previewPath
    );

    $picture->setOriginalPath($originalInternalPath);
    $picture->setPreviewPath($previewInternalPath);
    $picture->store();
}

/**
 * @var \KPG\Learnplaces\persistence\entity\VideoBlock[] $videos
 */
$videos = \KPG\Learnplaces\persistence\entity\VideoBlock::get();
foreach ($videos as $video) {
    $path = lowercaseFileExtension($video->getPath());

    $internalPath = \KPG\Learnplaces\service\filesystem\PathHelper::generatePluginInternalPathFrom(
        $path
    );

    $coverPath = lowercaseFileExtension($video->getCoverPath());
    $coverInternalPath = \KPG\Learnplaces\service\filesystem\PathHelper::generatePluginInternalPathFrom(
        $coverPath
    );

    $video->setPath($internalPath);
    $video->setCoverPath($coverInternalPath);
    $video->store();
}
?>
<#4>
<?php
\KPG\Learnplaces\persistence\entity\Picture::updateDB4();
\KPG\Learnplaces\persistence\entity\VideoBlock::updateDB4();
?>
<#5>
<?php
KPG\Learnplaces\api\Database\OAuthEntityInstall::install();
?>
<#6>
<?php
KPG\Learnplaces\api\Database\OAuthEntityInstall::update_2();
?>
<#7>
<?php
global $ilDB;
if (!$ilDB->tableColumnExists('xsrl_configuration', 'tags')) {
    $ilDB->addTableColumn('xsrl_configuration', 'tags', array(
        'type' => 'text',
        'notnull' => false,
        'length' => 1000,
        'default' => null
    ));
}
?>
