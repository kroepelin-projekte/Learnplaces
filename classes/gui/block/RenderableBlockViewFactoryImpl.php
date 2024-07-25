<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\gui\block;

use InvalidArgumentException;
use SRAG\Learnplaces\container\PluginContainer;
use SRAG\Learnplaces\gui\block\AccordionBlock\AccordionBlockPresentationView;
use SRAG\Learnplaces\gui\block\IliasLinkBlock\IliasLinkBlockPresentationView;
use SRAG\Learnplaces\gui\block\PictureBlock\PictureBlockPresentationView;
use SRAG\Learnplaces\gui\block\PictureUploadBlock\PictureUploadBlockPresentationView;
use SRAG\Learnplaces\gui\block\RichTextBlock\RichTextBlockPresentationView;
use SRAG\Learnplaces\gui\block\VideoBlock\VideoBlockPresentationView;
use SRAG\Learnplaces\service\publicapi\model\AccordionBlockModel;
use SRAG\Learnplaces\service\publicapi\model\BlockModel;
use SRAG\Learnplaces\service\publicapi\model\ILIASLinkBlockModel;
use SRAG\Learnplaces\service\publicapi\model\PictureBlockModel;
use SRAG\Learnplaces\service\publicapi\model\PictureUploadBlockModel;
use SRAG\Learnplaces\service\publicapi\model\RichTextBlockModel;
use SRAG\Learnplaces\service\publicapi\model\VideoBlockModel;

use function get_class;

/**
 * Class RenderableBlockViewFactoryImpl
 *
 * @package SRAG\Learnplaces\gui\block
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class RenderableBlockViewFactoryImpl implements RenderableBlockViewFactory
{
    /**
     * Generates a renderable view for the given block model.
     *
     * @param BlockModel $blockModel    Which should be wrapped by a renderable view.
     *
     * @return Renderable   A renderable view for the given model.
     * @throws InvalidArgumentException
     *                      Thrown if the block model has no corresponding view.
     */
    public function getInstance(BlockModel $blockModel): Renderable
    {
        $modelClass = get_class($blockModel);
        switch ($modelClass) {
            case PictureUploadBlockModel::class:
                return $this->getPictureUploadPresentationView($blockModel);
            case PictureBlockModel::class:
                return $this->getPicturePresentationView($blockModel);
            case RichTextBlockModel::class:
                return $this->getRichTextView($blockModel);
            case ILIASLinkBlockModel::class:
                return $this->getIliasLinkView($blockModel);
            case VideoBlockModel::class:
                return $this->getVideoView($blockModel);
            case AccordionBlockModel::class:
                return $this->getAccordionView($blockModel);
            default:
                throw new InvalidArgumentException('Model has no corresponding view.');
        }
    }

    private function getPictureUploadPresentationView(PictureUploadBlockModel $model): PictureUploadBlockPresentationView
    {
        /**
         * @var PictureUploadBlockPresentationView $view
         */
        $view = PluginContainer::resolve(PictureUploadBlockPresentationView::class);
        $view->setModel($model);
        return $view;
    }

    private function getPicturePresentationView(PictureBlockModel $model): PictureBlockPresentationView
    {
        /**
         * @var PictureBlockPresentationView $view
         */
        $view = PluginContainer::resolve(PictureBlockPresentationView::class);
        $view->setModel($model);
        return $view;
    }

    private function getRichTextView(RichTextBlockModel $model): RichTextBlockPresentationView
    {

        /**
         * @var RichTextBlockPresentationView $view
         */
        $view = PluginContainer::resolve(RichTextBlockPresentationView::class);
        $view->setModel($model);
        return $view;
    }

    private function getIliasLinkView(ILIASLinkBlockModel $model): IliasLinkBlockPresentationView
    {

        /**
         * @var IliasLinkBlockPresentationView $view
         */
        $view = PluginContainer::resolve(IliasLinkBlockPresentationView::class);
        $view->setModel($model);
        return $view;
    }

    private function getVideoView(VideoBlockModel $model): VideoBlockPresentationView
    {

        /**
         * @var VideoBlockPresentationView $view
         */
        $view = PluginContainer::resolve(VideoBlockPresentationView::class);
        $view->setModel($model);
        return $view;
    }

    private function getAccordionView(AccordionBlockModel $model): AccordionBlockPresentationView
    {

        /**
         * @var AccordionBlockPresentationView $view
         */
        $view = PluginContainer::resolve(AccordionBlockPresentationView::class);
        $view->setModel($model);
        return $view;
    }
}
