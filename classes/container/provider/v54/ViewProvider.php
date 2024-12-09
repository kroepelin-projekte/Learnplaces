<?php

declare(strict_types=1);

namespace KPG\Learnplaces\container\provider\v54;

use ilCtrl;
use ilLearnplacesPlugin;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use KPG\Learnplaces\gui\block\AccordionBlock\AccordionBlockPresentationView;
use KPG\Learnplaces\gui\block\BlockAddFormGUI;
use KPG\Learnplaces\gui\block\IliasLinkBlock\IliasLinkBlockPresentationView;
use KPG\Learnplaces\gui\block\MapBlock\MapBlockPresentationView;
use KPG\Learnplaces\gui\block\PictureBlock\PictureBlockPresentationView;
use KPG\Learnplaces\gui\block\PictureUploadBlock\PictureUploadBlockPresentationView;
use KPG\Learnplaces\gui\block\RenderableBlockViewFactory;
use KPG\Learnplaces\gui\block\RichTextBlock\RichTextBlockPresentationView;
use KPG\Learnplaces\gui\block\VideoBlock\VideoBlockPresentationView;
use KPG\Learnplaces\gui\ContentPresentationView;

/**
 * Class ViewProvider
 *
 * Provides factories for the view components
 * because they have some dependencies to ilCtrl and the ilLanguage usw.
 *
 * @package KPG\Learnplaces\container\provider
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class ViewProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $pimple)
    {
        $pimple[PictureUploadBlockPresentationView::class] = $pimple->factory(function ($c) {
            return new PictureUploadBlockPresentationView(
                $c[ilLearnplacesPlugin::class],
                $c[ilCtrl::class]
            );
        });

        $pimple[PictureBlockPresentationView::class] = $pimple->factory(function ($c) {
            return new PictureBlockPresentationView(
                $c[ilLearnplacesPlugin::class],
                $c[ilCtrl::class]
            );
        });

        $pimple[RichTextBlockPresentationView::class] = $pimple->factory(function ($c) {
            return new RichTextBlockPresentationView(
                $c[ilLearnplacesPlugin::class],
                $c[ilCtrl::class]
            );
        });

        $pimple[IliasLinkBlockPresentationView::class] = $pimple->factory(function ($c) {
            return new IliasLinkBlockPresentationView(
                $c[ilLearnplacesPlugin::class],
                $c[ilCtrl::class]
            );
        });

        $pimple[MapBlockPresentationView::class] = $pimple->factory(function ($c) {
            return new MapBlockPresentationView(
                $c[ilLearnplacesPlugin::class],
                $c[ilCtrl::class]
            );
        });

        $pimple[VideoBlockPresentationView::class] = $pimple->factory(function ($c) {
            return new VideoBlockPresentationView(
                $c[ilLearnplacesPlugin::class],
                $c[ilCtrl::class]
            );
        });

        $pimple[AccordionBlockPresentationView::class] = $pimple->factory(function ($c) {
            return new AccordionBlockPresentationView(
                $c[ilLearnplacesPlugin::class],
                $c[ilCtrl::class],
                $c[ContentPresentationView::class]
            );
        });

        $pimple[ContentPresentationView::class] = $pimple->factory(function ($c) {
            return new ContentPresentationView(
                $c[ilCtrl::class],
                $c[ilLearnplacesPlugin::class],
                $c[RenderableBlockViewFactory::class]
            );
        });

        $pimple[BlockAddFormGUI::class] = $pimple->factory(function ($c) {
            return new BlockAddFormGUI(
                $c[ilLearnplacesPlugin::class],
                $c[ilCtrl::class]
            );
        });
    }
}
