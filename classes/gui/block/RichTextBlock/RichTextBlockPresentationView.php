<?php

declare(strict_types=1);

namespace KPG\Learnplaces\gui\block\RichTextBlock;

use ilCtrl;
use ilLearnplacesPlugin;
use ilSplitButtonException;
use ilTemplate;
use LogicException;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\gui\block\Renderable;
use KPG\Learnplaces\gui\block\util\ReadOnlyViewAware;
use KPG\Learnplaces\gui\helper\CommonControllerAction;
use KPG\Learnplaces\service\publicapi\model\RichTextBlockModel;
use KPG\Learnplaces\util\DeleteItemModal;
use xsrlRichTextBlockGUI;
use ilTemplateException;
use ILIAS\UI\Factory;

/**
 * Class RichTextBlockPresentationView
 *
 * @package KPG\Learnplaces\gui\block\RichTextBlock
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class RichTextBlockPresentationView implements Renderable
{
    use ReadOnlyViewAware;
    use DeleteItemModal;

    public const SEQUENCE_ID_PREFIX = 'block_';
    public const TYPE = 'richtext';

    private ilLearnplacesPlugin $plugin;
    private ilTemplate $template;
    private ilCtrl $controlFlow;
    private RichTextBlockModel $model;

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
        $this->template = \ilLearnplacesPlugin::getInstance()->getTemplate('default/block/tpl.rich_text.html');
    }

    /**
     * @return void
     */
    private function initView(): void
    {
        $this->template->setVariable('CONTENT', $this->model->getContent());
    }

    /**
     * @param RichTextBlockModel $model
     * @return void
     */
    public function setModel(RichTextBlockModel $model): void
    {
        $this->model = $model;
        $this->initView();
    }

    /**
     * @inheritDoc
     * @throws ilSplitButtonException|ilTemplateException
     */
    public function getHtml(): string
    {
        if (is_null($this->model)) {
            throw new LogicException('The rich text block view requires a model to render its content.');
        }

        return $this->wrapWithBlockTemplate($this->template)->get();
    }

    /**
     * Wraps the given template with the tpl.block.html template.
     *
     * @param ilTemplate $template The block template which should be wrapped.
     *
     * @return ilTemplate               The wrapped template.
     *
     * @throws ilSplitButtonException   Thrown if something went wrong with the split button.
     * @throws \ilCtrlException
     */
    private function wrapWithBlockTemplate(ilTemplate $template): ilTemplate
    {
        $outerTemplate = \ilLearnplacesPlugin::getInstance()->getTemplate('default/tpl.block.html');

        /** @var Factory $factory */
        $factory = PluginContainer::resolve('factory');
        $renderer = PluginContainer::resolve('renderer');
        $lng = PluginContainer::resolve('lng');

        //setup button
        $editAction = $this->controlFlow->getLinkTargetByClass(xsrlRichTextBlockGUI::class, CommonControllerAction::CMD_EDIT) . '&' . xsrlRichTextBlockGUI::BLOCK_ID_QUERY_KEY . '=' . $this->model->getId();
        $editButton = $factory->button()->shy($this->plugin->txt('common_edit'), $editAction);

        $deleteButton = $this->deleteItemButtonWithModal(
            $this->model->getId() . '-' . self::TYPE,
            'Text',
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
        $outerTemplate->setVariable('CONTENT', $template->get());
        return $outerTemplate;
    }
}
