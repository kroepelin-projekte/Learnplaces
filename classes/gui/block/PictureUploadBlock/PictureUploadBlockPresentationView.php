<?php

declare(strict_types=1);

namespace KPG\Learnplaces\gui\block\PictureUploadBlock;

use ilCtrl;
use ilLearnplacesPlugin;
use ilSplitButtonException;
use ilTemplate;
use LogicException;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\gui\block\Renderable;
use KPG\Learnplaces\gui\block\util\ReadOnlyViewAware;
use KPG\Learnplaces\gui\helper\CommonControllerAction;
use KPG\Learnplaces\service\publicapi\model\PictureUploadBlockModel;
use xsrlPictureUploadBlockGUI;

use function is_null;
use ILIAS\UI\Factory;

/**
 * Class PictureUploadBlockPresentationView
 *
 * @package KPG\Learnplaces\gui\block
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class PictureUploadBlockPresentationView implements Renderable
{
    use ReadOnlyViewAware;

    public const SEQUENCE_ID_PREFIX = 'block_';

    private ilLearnplacesPlugin $plugin;
    private ilTemplate $template;
    private ilCtrl $controlFlow;
    private PictureUploadBlockModel $model;

    /**
     * PictureUploadBlockPresentationView constructor.
     *
     * @param ilLearnplacesPlugin $plugin
     * @param ilCtrl              $controlFlow
     */
    public function __construct(ilLearnplacesPlugin $plugin, ilCtrl $controlFlow)
    {
        $this->plugin = $plugin;
        $this->controlFlow = $controlFlow;
        $this->template = \ilLearnplacesPlugin::getInstance()->getTemplate('default/block/tpl.picture_upload.html');
        $this->initView();
    }

    /**
     * @return void
     */
    private function initView(): void
    {
        $this->template->setVariable('CONTENT', $this->plugin->txt('picture_upload_block_content'));
    }

    /**
     * @param PictureUploadBlockModel $model
     * @return void
     */
    public function setModel(PictureUploadBlockModel $model): void
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     * @throws ilSplitButtonException
     */
    public function getHtml(): string
    {
        if (is_null($this->model)) {
            throw new LogicException('The picture upload block view requires a model to render its content.');
        }

        return $this->wrapWithBlockTemplate($this->template)->get();
    }

    /**
     * Wraps the given template with the tpl.block.html template.
     *
     * @param ilTemplate $blockTemplate The block template which should be wrapped.
     * @return ilTemplate               The wrapped template.
     *
     * @throws ilSplitButtonException   Thrown if something went wrong with the split button.
     */
    private function wrapWithBlockTemplate(ilTemplate $blockTemplate): ilTemplate
    {
        $outerTemplate = \ilLearnplacesPlugin::getInstance()->getTemplate('default/tpl.block.html');

        /** @var Factory $factory */
        $factory = PluginContainer::resolve('factory');
        $renderer = PluginContainer::resolve('renderer');

        $editAction = $this->controlFlow->getLinkTargetByClass(xsrlPictureUploadBlockGUI::class, CommonControllerAction::CMD_EDIT) . '&' . xsrlPictureUploadBlockGUI::BLOCK_ID_QUERY_KEY . '=' . $this->model->getId();
        $editButton = $factory->button()->standard($this->plugin->txt('common_edit'), $editAction);

        $actionMenu = $renderer->render($factory->dropdown()->standard([
            $editButton,
        ])->withLabel('Actions'));

        //fill outer template
        if (!$this->isReadonly()) {
            $outerTemplate->setVariable('ACTION_BUTTON', $actionMenu);
        }
        $outerTemplate->setVariable('CONTENT', $blockTemplate->get());
        $outerTemplate->setVariable('SEQUENCE', $this->model->getSequence());
        return $outerTemplate;
    }
}
