<?php

declare(strict_types=1);

namespace KPG\Learnplaces\container\provider\v54;

use ilLearnplacesPlugin;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Http\Message\ServerRequestInterface;
use KPG\Learnplaces\gui\block\BlockAddFormGUI;
use KPG\Learnplaces\gui\block\RenderableBlockViewFactory;
use KPG\Learnplaces\gui\block\RenderableBlockViewFactoryImpl;
use KPG\Learnplaces\service\media\PictureService;
use KPG\Learnplaces\service\media\VideoService;
use KPG\Learnplaces\service\publicapi\block\AccordionBlockService;
use KPG\Learnplaces\service\publicapi\block\ConfigurationService;
use KPG\Learnplaces\service\publicapi\block\ILIASLinkBlockService;
use KPG\Learnplaces\service\publicapi\block\LearnplaceService;
use KPG\Learnplaces\service\publicapi\block\LocationService;
use KPG\Learnplaces\service\publicapi\block\MapBlockService;
use KPG\Learnplaces\service\publicapi\block\PictureBlockService;
use KPG\Learnplaces\service\publicapi\block\PictureUploadBlockService;
use KPG\Learnplaces\service\publicapi\block\RichTextBlockService;
use KPG\Learnplaces\service\publicapi\block\VideoBlockService;
use KPG\Learnplaces\service\security\AccessGuard;
use KPG\Learnplaces\service\visibility\LearnplaceServiceDecoratorFactory;
use xsrlAccordionBlockGUI;
use xsrlContentGUI;
use xsrlIliasLinkBlockGUI;
use xsrlMapBlockGUI;
use xsrlPictureBlockGUI;
use xsrlPictureUploadBlockGUI;
use xsrlRichTextBlockGUI;
use xsrlSettingGUI;
use xsrlVideoBlockGUI;

/**
 * Class GUIProvider
 *
 * @package KPG\Learnplaces\container\provider
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class GUIProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $pimple)
    {
        $pimple[RenderableBlockViewFactory::class] = function ($c) {return new RenderableBlockViewFactoryImpl(); };

        $pimple[xsrlContentGUI::class] = function ($c) {
            return new xsrlContentGUI(
                $c['ilTabs'],
                $c['tpl'],
                $c['ilCtrl'],
                $c[ilLearnplacesPlugin::class],
                $c[RenderableBlockViewFactory::class],
                $c[LearnplaceService::class],
                $c[AccordionBlockService::class],
                $c[LearnplaceServiceDecoratorFactory::class],
                $c[BlockAddFormGUI::class],
                $c[ServerRequestInterface::class],
                $c[AccessGuard::class]
            );
        };

        $pimple[xsrlPictureUploadBlockGUI::class] = function ($c) {
            return new xsrlPictureUploadBlockGUI(
                $c['ilTabs'],
                $c['tpl'],
                $c['ilCtrl'],
                $c[ilLearnplacesPlugin::class],
                $c[PictureUploadBlockService::class],
                $c[LearnplaceService::class],
                $c[ConfigurationService::class],
                $c[AccordionBlockService::class],
                $c[ServerRequestInterface::class],
                $c[AccessGuard::class]
            );
        };

        $pimple[xsrlPictureBlockGUI::class] = function ($c) {
            return new xsrlPictureBlockGUI(
                $c['ilTabs'],
                $c['tpl'],
                $c['ilCtrl'],
                $c[ilLearnplacesPlugin::class],
                $c[PictureService::class],
                $c[PictureBlockService::class],
                $c[LearnplaceService::class],
                $c[ConfigurationService::class],
                $c[AccordionBlockService::class],
                $c[ServerRequestInterface::class],
                $c[AccessGuard::class]
            );
        };

        $pimple[xsrlRichTextBlockGUI::class] = function ($c) {
            return new xsrlRichTextBlockGUI(
                $c['ilTabs'],
                $c['tpl'],
                $c['ilCtrl'],
                $c[ilLearnplacesPlugin::class],
                $c[RichTextBlockService::class],
                $c[LearnplaceService::class],
                $c[ConfigurationService::class],
                $c[AccordionBlockService::class],
                $c[ServerRequestInterface::class],
                $c[AccessGuard::class]
            );
        };

        $pimple[xsrlIliasLinkBlockGUI::class] = function ($c) {
            return new xsrlIliasLinkBlockGUI(
                $c['ilTabs'],
                $c['tpl'],
                $c['ilCtrl'],
                $c[ilLearnplacesPlugin::class],
                $c[ILIASLinkBlockService::class],
                $c[LearnplaceService::class],
                $c[ConfigurationService::class],
                $c[AccordionBlockService::class],
                $c[ServerRequestInterface::class],
                $c[AccessGuard::class]
            );
        };

        $pimple[xsrlMapBlockGUI::class] = function ($c) {
            return new xsrlMapBlockGUI(
                $c['ilTabs'],
                $c['tpl'],
                $c['ilCtrl'],
                $c[ilLearnplacesPlugin::class],
                $c[MapBlockService::class],
                $c[LearnplaceService::class],
                $c[ConfigurationService::class],
                $c[ServerRequestInterface::class],
                $c[AccessGuard::class]
            );
        };

        $pimple[xsrlVideoBlockGUI::class] = function ($c) {
            return new xsrlVideoBlockGUI(
                $c['ilTabs'],
                $c['tpl'],
                $c['ilCtrl'],
                $c[ilLearnplacesPlugin::class],
                $c[VideoBlockService::class],
                $c[VideoService::class],
                $c[LearnplaceService::class],
                $c[ConfigurationService::class],
                $c[AccordionBlockService::class],
                $c[ServerRequestInterface::class],
                $c[AccessGuard::class]
            );
        };

        $pimple[xsrlAccordionBlockGUI::class] = function ($c) {
            return new xsrlAccordionBlockGUI(
                $c['ilTabs'],
                $c['tpl'],
                $c['ilCtrl'],
                $c[ilLearnplacesPlugin::class],
                $c[AccordionBlockService::class],
                $c[LearnplaceService::class],
                $c[ConfigurationService::class],
                $c[ServerRequestInterface::class],
                $c[AccessGuard::class]
            );
        };

        $pimple[xsrlSettingGUI::class] = function ($c) {
            return new xsrlSettingGUI(
                $c['ilTabs'],
                $c['tpl'],
                $c['ilCtrl'],
                $c[ilLearnplacesPlugin::class],
                $c[ConfigurationService::class],
                $c[LocationService::class],
                $c[LearnplaceService::class],
                $c[ServerRequestInterface::class],
                $c[AccessGuard::class]
            );
        };
    }
}
