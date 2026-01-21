<?php

namespace KPG\Learnplaces\gui;

use ILIAS\UI\Component\Table as I;
use ILIAS\Data\Range;
use ILIAS\Data\Order;
use ILIAS\UI\Component\Table\DataRowBuilder;
use Generator;
use ilLearnplacesPlugin;
use ILIAS\Data\Factory;
use ILIAS\UI\Implementation\Component\Table\Data;
use ILIAS\DI\Container;

class VisitorsTable implements I\DataRetrieval
{
    protected \ILIAS\UI\Factory $ui_factory;
    protected Factory $df;
    private array $table_data;
    private Container $DIC;
    private ilLearnplacesPlugin $plugin;

    public function __construct(ilLearnplacesPlugin $plugin, $table_data)
    {
        global $DIC;
        $this->ui_factory = $DIC['ui.factory'];
        $this->df = new Factory();
        global $DIC;
        $this->DIC = $DIC;
        $this->plugin = $plugin;
        $this->table_data = $table_data;
    }

    /**
     * @param array $table_data
     * @return void
     */
    public function setTableData(array $table_data): void
    {
        $this->table_data = $table_data;
    }

    /**
     * @param DataRowBuilder $row_builder
     * @param array          $visible_column_ids
     * @param Range          $range
     * @param Order          $order
     * @param array|null     $filter_data
     * @param array|null     $additional_parameters
     * @return Generator
     */
    public function getRows(
        DataRowBuilder $row_builder,
        array $visible_column_ids,
        Range $range,
        Order $order,
        ?array $filter_data,
        ?array $additional_parameters
    ): Generator {
        $row_id = 0;
        $record = [];
        $record_data = $this->table_data;

        if ($range) {
            $record_data = array_slice($record_data, $range->getStart(), $range->getLength());
        }

        if ($order) {
            list($order_field, $order_direction) = $order->join([], fn($ret, $key, $value) => [$key, $value]);
            usort($record_data, fn($a, $b) => $a[$order_field] <=> $b[$order_field]);
            if ($order_direction === 'DESC') {
                $record_data = array_reverse($record_data);
            }
        }
        foreach ($record_data as $row) {
            $record['full_name'] = $row['full_name'];
            $record['login'] = $row['login'];
            $record['visited_at'] = $row['visited_at'];
            yield $row_builder->buildDataRow($row_id, $record);
            $row_id++;
        }
    }

    /**
     * @return array
     */
    protected function getColumsForRepresentation(): array
    {
        $f = $this->ui_factory;
        $columns = [
            'full_name' => $f->table()->column()->text($this->plugin->txt('lang_visitor_table_visitor')),
            'login' => $f->table()->column()->text($this->plugin->txt('lang_visitor_table_login')),
            'visited_at' => $f->table()->column()->text($this->plugin->txt('lang_visitor_table_vistited_at')),
        ];
        return $columns;
    }

    /**
     * @param array|null $filter_data
     * @param array|null $additional_parameters
     * @return int|null
     */
    public function getTotalRowCount(?array $filter_data, ?array $additional_parameters): ?int
    {
        return count($this->table_data);
    }

    /**
     * @return Data
     */
    public function getTableForRepresentation(): Data
    {
        return $this->ui_factory->table()->data(
            '',
            $this->getColumsForRepresentation(),
            $this
        );
    }
}
