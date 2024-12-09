<?php

declare(strict_types=1);

namespace KPG\Learnplaces\gui\block;

use InvalidArgumentException;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\gui\block\AccordionBlock\AccordionBlockPresentationView;
use KPG\Learnplaces\gui\block\IliasLinkBlock\IliasLinkBlockPresentationView;
use KPG\Learnplaces\gui\block\PictureBlock\PictureBlockPresentationView;
use KPG\Learnplaces\gui\block\PictureUploadBlock\PictureUploadBlockPresentationView;
use KPG\Learnplaces\gui\block\RichTextBlock\RichTextBlockPresentationView;
use KPG\Learnplaces\gui\block\VideoBlock\VideoBlockPresentationView;
use KPG\Learnplaces\service\publicapi\model\AccordionBlockModel;
use KPG\Learnplaces\service\publicapi\model\BlockModel;
use KPG\Learnplaces\service\publicapi\model\ILIASLinkBlockModel;
use KPG\Learnplaces\service\publicapi\model\PictureBlockModel;
use KPG\Learnplaces\service\publicapi\model\PictureUploadBlockModel;
use KPG\Learnplaces\service\publicapi\model\RichTextBlockModel;
use KPG\Learnplaces\service\publicapi\model\VideoBlockModel;

use function get_class;

/**
 * Class RenderableBlockViewFactoryImpl
 *
 * @package KPG\Learnplaces\gui\block
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

    /**
     * @param PictureUploadBlockModel $model
     * @return PictureUploadBlockPresentationView
     */
    private function getPictureUploadPresentationView(PictureUploadBlockModel $model): PictureUploadBlockPresentationView
    {
        /**
         * @var PictureUploadBlockPresentationView $view
         */
        $view = PluginContainer::resolve(PictureUploadBlockPresentationView::class);
        $view->setModel($model);
        return $view;
    }

    /**
     * @param PictureBlockModel $model
     * @return PictureBlockPresentationView
     */
    private function getPicturePresentationView(PictureBlockModel $model): PictureBlockPresentationView
    {
        /**
         * @var PictureBlockPresentationView $view
         */
        $view = PluginContainer::resolve(PictureBlockPresentationView::class);
        $view->setModel($model);
        return $view;
    }

    /**
     * @param RichTextBlockModel $model
     * @return RichTextBlockPresentationView
     */
    private function getRichTextView(RichTextBlockModel $model): RichTextBlockPresentationView
    {
        /**
         * @var RichTextBlockPresentationView $view
         */
        $view = PluginContainer::resolve(RichTextBlockPresentationView::class);
        $view->setModel($model);
        return $view;
    }

    /**
     * @param ILIASLinkBlockModel $model
     * @return IliasLinkBlockPresentationView
     */
    private function getIliasLinkView(ILIASLinkBlockModel $model): IliasLinkBlockPresentationView
    {
        /**
         * @var IliasLinkBlockPresentationView $view
         */
        $view = PluginContainer::resolve(IliasLinkBlockPresentationView::class);
        $view->setModel($model);
        return $view;
    }

    /**
     * @param VideoBlockModel $model
     * @return VideoBlockPresentationView
     */
    private function getVideoView(VideoBlockModel $model): VideoBlockPresentationView
    {
        /**
         * @var VideoBlockPresentationView $view
         */
        $view = PluginContainer::resolve(VideoBlockPresentationView::class);
        $view->setModel($model);
        return $view;
    }

    /**
     * @param AccordionBlockModel $model
     * @return AccordionBlockPresentationView
     */
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
