<?php

declare(strict_types=1);

namespace KPG\Learnplaces\gui\block\VideoBlock;

use ilCtrl;
use ilLearnplacesPlugin;
use ilSplitButtonException;
use ilTemplate;
use LogicException;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\gui\block\Renderable;
use KPG\Learnplaces\gui\block\util\ReadOnlyViewAware;
use KPG\Learnplaces\gui\helper\CommonControllerAction;
use KPG\Learnplaces\service\publicapi\model\VideoBlockModel;
use KPG\Learnplaces\util\DeleteItemModal;
use xsrlVideoBlockGUI;

/**
 * Class VideoBlockPresentationView
 *
 * @package KPG\Learnplaces\gui\block\VideoBlock
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class VideoBlockPresentationView implements Renderable
{
    use ReadOnlyViewAware;
    use DeleteItemModal;

    public const SEQUENCE_ID_PREFIX = 'block_';
    public const TYPE = 'video';

    private ilLearnplacesPlugin $plugin;
    private ilTemplate $template;
    private ilCtrl $controlFlow;
    private VideoBlockModel $model;

    /**
     * Video constructor.
     *
     * @param ilLearnplacesPlugin $plugin
     * @param ilCtrl              $controlFlow
     */
    public function __construct(ilLearnplacesPlugin $plugin, ilCtrl $controlFlow)
    {
        $this->plugin = $plugin;
        $this->controlFlow = $controlFlow;
        $this->template = \ilLearnplacesPlugin::getInstance()->getTemplate('default/block/tpl.video.html');
    }

    /**
     * @return void
     */
    private function initView(): void
    {
        $ctrl = PluginContainer::resolve('ctrl');

        $block_id = $this->model->getId();

        $ctrl->setParameterByClass(\ilObjLearnplacesGUI::class, 'block_id', $block_id);
        $src = $ctrl->getLinkTargetByClass([\ilObjPluginDispatchGUI::class, \ilObjLearnplacesGUI::class], 'streamVideo');
        $ctrl->clearParametersByClass(\ilObjLearnplacesGUI::class);

        // Kitchensink-Component Video is not responsive
        $videoHTML = "<video style='width: 100%;' controls><source src=\"$src\" type=\"video/mp4\">Your browser does not support the video tag.</video>";

        $this->template->setVariable('CONTENT', $videoHTML);
    }

    /**
     * @param VideoBlockModel $model
     * @return void
     */
    public function setModel(VideoBlockModel $model): void
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     * @throws ilSplitButtonException|\ilTemplateException
     */
    public function getHtml(): string
    {
        if (is_null($this->model)) {
            throw new LogicException('The video block view requires a model to render its content.');
        }

        $this->initView();
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

        //setup button
        $factory = PluginContainer::resolve('factory');
        $renderer = PluginContainer::resolve('renderer');
        $lng = PluginContainer::resolve('lng');

        $editAction = $this->controlFlow->getLinkTargetByClass(xsrlVideoBlockGUI::class, CommonControllerAction::CMD_EDIT) . '&' . xsrlVideoBlockGUI::BLOCK_ID_QUERY_KEY . '=' . $this->model->getId();
        $editButton = $factory->button()->shy($this->plugin->txt('common_edit'), $editAction);

        $deleteButton = $this->deleteItemButtonWithModal(
            $this->model->getId() . '-' . self::TYPE,
            'Video',
            $this->plugin->txt('confirm_delete_header'),
            $this->plugin->txt('common_delete')
        );

        $actionMenu = $renderer->render($factory->dropdown()->standard([
            $editButton,
            $deleteButton['button']
        ])->withLabel($lng->txt('actions')));

        //fill outer template
        if (!$this->isReadonly()) {
            $outerTemplate->setVariable('ACTION_BUTTON', $actionMenu . $deleteButton['modal']);
        }
        $outerTemplate->setVariable('CONTENT', $blockTemplate->get());
        return $outerTemplate;
    }
}
