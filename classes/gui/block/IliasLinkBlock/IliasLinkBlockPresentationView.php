<?php

declare(strict_types=1);

namespace KPG\Learnplaces\gui\block\IliasLinkBlock;

use ilCtrl;
use ilLearnplacesPlugin;
use ilObject;
use ilSplitButtonException;
use ilTemplate;
use LogicException;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\gui\block\Renderable;
use KPG\Learnplaces\gui\block\util\ReadOnlyViewAware;
use KPG\Learnplaces\gui\helper\CommonControllerAction;
use KPG\Learnplaces\service\publicapi\model\ILIASLinkBlockModel;
use KPG\Learnplaces\util\DeleteItemModal;
use xsrlIliasLinkBlockGUI;
use ILIAS\Data\ReferenceId;
use ILIAS\StaticURL\Services;
use ilTemplateException;

/**
 * Class IliasLinkBlockPresentationView
 *
 * @package KPG\Learnplaces\gui\block\IliasLinkBlock
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class IliasLinkBlockPresentationView implements Renderable
{
    use ReadOnlyViewAware;
    use DeleteItemModal;

    public const SEQUENCE_ID_PREFIX = 'block_';
    public const TYPE = 'link';

    private ilLearnplacesPlugin $plugin;
    private ilTemplate $template;
    private ilCtrl $controlFlow;
    private ILIASLinkBlockModel $model;

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
        $this->template = \ilLearnplacesPlugin::getInstance()->getTemplate('default/block/tpl.ilias_link.html');
    }

    /**
     * @return void
     */
    private function initView(): void
    {
        $objectId = ilObject::_lookupObjectId($this->model->getRefId());
        $title = ilObject::_lookupTitle($objectId);

        $factory = PluginContainer::resolve('factory');
        $renderer = PluginContainer::resolve('renderer');

        /** @var Services $static_url */
        $static_url = PluginContainer::resolve('url');

        $url = $static_url->builder()->build(
            ilObject::_lookupType(ilObject::_lookupObjectId($this->model->getRefId())),
            new ReferenceId($this->model->getRefId()),
        )->__toString();

        $iliasLink = $renderer->render(
            $factory->link()->standard($title, $url)
        );

        $this->template->setVariable('CONTENT', $iliasLink);
        $this->template->setVariable('DESCRIPTION', ilObject::_lookupDescription($objectId));
    }

    /**
     * @param ILIASLinkBlockModel $model
     * @return void
     */
    public function setModel(ILIASLinkBlockModel $model): void
    {
        $this->model = $model;
        $this->initView();
    }

    /**
     * @inheritDoc
     * @throws ilSplitButtonException|ilTemplateException|\ilCtrlException
     */
    public function getHtml(): string
    {
        if (is_null($this->model)) {
            throw new LogicException('The picture block view requires a model to render its content.');
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
     * @throws \ilCtrlException
     */
    private function wrapWithBlockTemplate(ilTemplate $blockTemplate): ilTemplate
    {
        $outerTemplate = \ilLearnplacesPlugin::getInstance()->getTemplate('default/tpl.block.html');

        $factory = PluginContainer::resolve('factory');
        $renderer = PluginContainer::resolve('renderer');
        $lng = PluginContainer::resolve('lng');

        //setup button
        $editAction = $this->controlFlow->getLinkTargetByClass(xsrlIliasLinkBlockGUI::class, CommonControllerAction::CMD_EDIT) . '&' . xsrlIliasLinkBlockGUI::BLOCK_ID_QUERY_KEY . '=' . $this->model->getId();
        $editButton = $factory->button()->shy($this->plugin->txt('common_edit'), $editAction);

        $deleteButton = $this->deleteItemButtonWithModal(
            $this->model->getId() . '-' . self::TYPE,
            'Link',
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
