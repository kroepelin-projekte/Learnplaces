<?php

declare(strict_types=1);

namespace KPG\Learnplaces\gui\block\MapBlock;

use ilCtrl;
use ILIAS\HTTP\Services;
use ilLearnplacesPlugin;
use ilMapUtil;
use ilTemplate;
use LogicException;
use KPG\Learnplaces\container\PluginContainer;
use KPG\Learnplaces\gui\block\util\ReadOnlyViewAware;
use KPG\Learnplaces\gui\helper\CommonControllerAction;
use KPG\Learnplaces\service\publicapi\model\ConfigurationModel;
use KPG\Learnplaces\service\publicapi\model\LocationModel;
use KPG\Learnplaces\service\publicapi\model\MapBlockModel;
use KPG\Learnplaces\util\DeleteItemModal;
use xsrlMapBlockGUI;
use ILIAS\Refinery;
use KPG\Learnplaces\service\publicapi\model\BlockModel;

/**
 * Class MapBlockPresentationView
 *
 * @package KPG\Learnplaces\gui\block\MapBlock
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class MapBlockPresentationView
{
    use ReadOnlyViewAware;
    use DeleteItemModal;

    public const TYPE = 'map';

    private ilLearnplacesPlugin $plugin;
    private ilTemplate $template;
    private ilCtrl $controlFlow;
    private BlockModel $model;
    private LocationModel $location;
    private ConfigurationModel $configuration;
    private Services $http;
    private Refinery $refinery;

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
        $this->http = PluginContainer::resolve('http');
        $this->refinery = PluginContainer::resolve('refinery');
        $this->template = \ilLearnplacesPlugin::getInstance()->getTemplate('default/tpl.map_tab.html');
    }

    /**
     * @return void
     * @throws \ilCtrlException
     */
    private function initView(): void
    {
        //setup button
        global $DIC;
        $factory = $DIC->ui()->factory();

        $editAction = $this->controlFlow->getLinkTargetByClass(xsrlMapBlockGUI::class, CommonControllerAction::CMD_EDIT) . '&' . xsrlMapBlockGUI::BLOCK_ID_QUERY_KEY . '=' . $this->model->getId();
        $editButton = $factory->button()->standard($this->plugin->txt('common_edit'), $editAction);

        if (version_compare(ILIAS_VERSION_NUMERIC, '9.0', '>=')) {
            $affected_item = $factory->modal()->interruptiveItem()
                ->standard('deleteMap', 'Map');
        } else {
            $affected_item = $factory->modal()->interruptiveItem('deleteMap', 'Map');
        }
        $modal = $factory->modal()->interruptive(
            $this->plugin->txt('common_delete'),
            $this->plugin->txt('confirm_delete_header'),
            $this->controlFlow->getLinkTargetByClass(xsrlMapBlockGUI::class, CommonControllerAction::CMD_DELETE) . '&block=' . $this->model->getId()
        )
            ->withAffectedItems([$affected_item]);
        $deleteButton = $factory->button()->standard($this->plugin->txt('common_delete'), '')
            ->withOnClick($modal->getShowSignal());

        $toolbar = $DIC->toolbar();
        $toolbar->addComponent($editButton);
        $toolbar->addComponent($deleteButton);
        $toolbar->addComponent($modal);

        $map = ilMapUtil::getMapGUI();
        $map->setMapId($map_id = "map_" . hash('sha256', uniqid('map', true)))
            ->setLatitude((string) $this->location->getLatitude())
            ->setLongitude((string) $this->location->getLongitude())
            ->setZoom($this->configuration->getMapZoomLevel())
            ->setEnableTypeControl(true)
            ->setEnableLargeMapControl(true)
            ->setEnableUpdateListener(false)
            ->setEnableCentralMarker(true)
            ->setWidth('100%')
            ->setHeight('500px');

        $this->template->setVariable('CONTENT', $map->getHtml());
    }

    /**
     * @param MapBlockModel $model
     * @param LocationModel $location
     * @param ConfigurationModel $configuration
     * @return void
     */
    public function setModels(MapBlockModel $model, LocationModel $location, ConfigurationModel $configuration): void
    {
        $this->model = $model;
        $this->location = $location;
        $this->configuration = $configuration;
    }

    /**
     * @inheritDoc
     */
    public function getHtml(): string
    {
        if (is_null($this->model)) {
            throw new LogicException('The picture block view requires a model to render its content.');
        }

        $this->initView();
        return $this->template->get();
    }
}
