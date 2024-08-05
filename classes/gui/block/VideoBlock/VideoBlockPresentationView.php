<?php

declare(strict_types=1);

namespace SRAG\Learnplaces\gui\block\VideoBlock;

use ilButtonToSplitButtonMenuItemAdapter;
use ilCtrl;
use ILIAS\ResourceStorage\Identification\ResourceIdentification;
use ilLearnplacesPlugin;
use ilLinkButton;
use ilSplitButtonException;
use ilSplitButtonGUI;
use ilTemplate;
use ilTextInputGUI;
use LogicException;
use SRAG\Learnplaces\container\PluginContainer;
use SRAG\Learnplaces\gui\block\Renderable;
use SRAG\Learnplaces\gui\block\util\ReadOnlyViewAware;
use SRAG\Learnplaces\gui\helper\CommonControllerAction;
use SRAG\Learnplaces\service\publicapi\model\VideoBlockModel;
use SRAG\Learnplaces\util\DeleteItemModal;
use xsrlVideoBlockGUI;

/**
 * Class VideoBlockPresentationView
 *
 * @package SRAG\Learnplaces\gui\block\VideoBlock
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class VideoBlockPresentationView implements Renderable
{
    use ReadOnlyViewAware;
    use DeleteItemModal;

    public const SEQUENCE_ID_PREFIX = 'block_';
    public const TYPE = 'video';

    /**
     * @var ilLearnplacesPlugin $plugin
     */
    private $plugin;
    /**
     * @var ilTemplate $template
     */
    private $template;
    /**
     * @var ilCtrl $controlFlow
     */
    private $controlFlow;
    /**
     * @var VideoBlockModel $model
     */
    private $model;

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
        $this->template = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/templates/default/block/tpl.video.html', true, true);
    }

    /**
     * @return void
     */
    private function initView(): void
    {
        $factory = PluginContainer::resolve('factory');
        $renderer = PluginContainer::resolve('renderer');
        $resourceStorage = PluginContainer::resolve('resourceStorage');

        $resourceId = $this->model->getResourceId();
        $resource = new ResourceIdentification($resourceId);
        if ($resourceStorage->manage()->find($resourceId)) {
            $src = $resourceStorage->consume()
                ->src($resource)
                ->getSrc();

            // todo not working in ilias 9

            $videoHTML = $renderer->render(
                $factory->player()->video($src)
            );

/*            $videoHTML = '<video width="320" height="240" controls>
                          <source src="$src" type="video/mp4">
                          Your browser does not support the video tag.
                        </video>';*/

            $this->template->setVariable('CONTENT', $videoHTML);
        }
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
     */
    public function getHtml(): string
    {
        if(is_null($this->model)) {
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
        $outerTemplate = new ilTemplate('./Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/templates/default/tpl.block.html', true, true);

        //setup button
        $factory = PluginContainer::resolve('factory');
        $renderer = PluginContainer::resolve('renderer');

        $editAction = $this->controlFlow->getLinkTargetByClass(xsrlVideoBlockGUI::class, CommonControllerAction::CMD_EDIT) . '&' . xsrlVideoBlockGUI::BLOCK_ID_QUERY_KEY . '=' . $this->model->getId();
        $editButton = $factory->button()->standard($this->plugin->txt('common_edit'), $editAction);
        $htmlEditButton = $renderer->render($editButton);

        $deleteButton = $this->deleteItemButtonWithModal(
            $this->model->getId() . '-' . self::TYPE,
            'Video',
            $this->plugin->txt('confirm_delete_header'),
            $this->plugin->txt('common_delete')
        );

        //setup sequence number
        $input = new ilTextInputGUI('', self::SEQUENCE_ID_PREFIX . $this->model->getId());
        $input->setRequired(true);
        $input->setValidationRegexp('/^\d+$/');
        $input->setValue($this->model->getSequence());
        $input->setRequired(true);

        //fill outer template
        if(!$this->isReadonly()) {
            $outerTemplate->setVariable('ACTION_BUTTON', $htmlEditButton . $deleteButton);
            $outerTemplate->setVariable('SEQUENCE_INPUT', $input->render());
        }
        $outerTemplate->setVariable('CONTENT', $blockTemplate->get());
        $outerTemplate->setVariable('SEQUENCE', $this->model->getSequence());
        return $outerTemplate;
    }
}
