<#1>
<?php

use SRAG\Learnplaces\persistence\entity\Visibility;
use SRAG\Learnplaces\service\filesystem\PathHelper;

require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/vendor/autoload.php');

\SRAG\Learnplaces\persistence\entity\AccordionBlock::installDB();
\SRAG\Learnplaces\persistence\entity\AccordionBlockMember::installDB();
\SRAG\Learnplaces\persistence\entity\Answer::installDB();
\SRAG\Learnplaces\persistence\entity\AudioBlock::installDB();
\SRAG\Learnplaces\persistence\entity\Block::installDB();
\SRAG\Learnplaces\persistence\entity\Comment::installDB();
\SRAG\Learnplaces\persistence\entity\CommentBlock::installDB();
\SRAG\Learnplaces\persistence\entity\Configuration::installDB();
\SRAG\Learnplaces\persistence\entity\ExternalStreamBlock::installDB();
\SRAG\Learnplaces\persistence\entity\FeedbackBlock::installDB();
\SRAG\Learnplaces\persistence\entity\Feedback::installDB();
\SRAG\Learnplaces\persistence\entity\HorizontalLineBlock::installDB();
\SRAG\Learnplaces\persistence\entity\ILIASLinkBlock::installDB();
\SRAG\Learnplaces\persistence\entity\Learnplace::installDB();
\SRAG\Learnplaces\persistence\entity\LearnplaceConstraint::installDB();
\SRAG\Learnplaces\persistence\entity\Location::installDB();
\SRAG\Learnplaces\persistence\entity\MapBlock::installDB();
\SRAG\Learnplaces\persistence\entity\Picture::installDB();
\SRAG\Learnplaces\persistence\entity\PictureBlock::installDB();
\SRAG\Learnplaces\persistence\entity\PictureGalleryEntry::installDB();
\SRAG\Learnplaces\persistence\entity\PictureUploadBlock::installDB();
\SRAG\Learnplaces\persistence\entity\RichTextBlock::installDB();
\SRAG\Learnplaces\persistence\entity\VideoBlock::installDB();
\SRAG\Learnplaces\persistence\entity\Visibility::installDB();
\SRAG\Learnplaces\persistence\entity\VisitJournal::installDB();

$visibilityAlways = new Visibility();
$visibilityNever = new Visibility();
$visibilityOnlyAtPlace = new Visibility();
$visibilityAfterVisitPlace = new Visibility();
$visibilityAfterVisitOtherPlace = new Visibility();

$visibilityAlways->setName(\SRAG\Learnplaces\util\Visibility::ALWAYS);
$visibilityAlways->create();

$visibilityNever->setName(\SRAG\Learnplaces\util\Visibility::NEVER);
$visibilityNever->create();

$visibilityOnlyAtPlace->setName(\SRAG\Learnplaces\util\Visibility::ONLY_AT_PLACE);
$visibilityOnlyAtPlace->create();

$visibilityAfterVisitPlace->setName(\SRAG\Learnplaces\util\Visibility::AFTER_VISIT_PLACE);
$visibilityAfterVisitPlace->create();

$visibilityAfterVisitOtherPlace->setName(\SRAG\Learnplaces\util\Visibility::AFTER_VISIT_OTHER_PLACE);
$visibilityAfterVisitOtherPlace->create();
?>
<#2>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/vendor/autoload.php');
\SRAG\Learnplaces\persistence\entity\Configuration::updateDB(); //map_zoom_level field added
?>
<#3>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/vendor/autoload.php');

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
 * @var \SRAG\Learnplaces\persistence\entity\Picture[] $pictures
 */
$pictures = \SRAG\Learnplaces\persistence\entity\Picture::get();
foreach ($pictures as $picture) {
    $originalPath = lowercaseFileExtension($picture->getOriginalPath());

    $originalInternalPath = \SRAG\Learnplaces\service\filesystem\PathHelper::generatePluginInternalPathFrom(
        $originalPath
    );

    $previewPath = lowercaseFileExtension($picture->getPreviewPath());
    $previewInternalPath = \SRAG\Learnplaces\service\filesystem\PathHelper::generatePluginInternalPathFrom(
        $previewPath
    );

    $picture->setOriginalPath($originalInternalPath);
    $picture->setPreviewPath($previewInternalPath);
    $picture->store();
}

/**
 * @var \SRAG\Learnplaces\persistence\entity\VideoBlock[] $videos
 */
$videos = \SRAG\Learnplaces\persistence\entity\VideoBlock::get();
foreach ($videos as $video) {
    $path = lowercaseFileExtension($video->getPath());

    $internalPath = \SRAG\Learnplaces\service\filesystem\PathHelper::generatePluginInternalPathFrom(
        $path
    );

    $coverPath = lowercaseFileExtension($video->getCoverPath());
    $coverInternalPath = \SRAG\Learnplaces\service\filesystem\PathHelper::generatePluginInternalPathFrom(
        $coverPath
    );

    $video->setPath($internalPath);
    $video->setCoverPath($coverInternalPath);
    $video->store();
}
?>
<#4>
<?php
\SRAG\Learnplaces\persistence\entity\Picture::updateDB4();
\SRAG\Learnplaces\persistence\entity\VideoBlock::updateDB4();
?>

