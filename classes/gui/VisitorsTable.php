<?php

namespace KPG\Learnplaces\gui;

use ILIAS\UI\Implementation\Component\Table as T;
use ILIAS\UI\Component\Table as I;
use ILIAS\Data\Range;
use ILIAS\Data\Order;
use ILIAS\UI\URLBuilder;
use Psr\Http\Message\ServerRequestInterface;
use ILIAS\UI\Component\Table\DataRowBuilder;
use Generator;
use ilLearnplacesPlugin;

class VisitorsTable implements I\DataRetrieval
{

    protected \ILIAS\UI\Factory $ui_factory;
    protected \ILIAS\Data\Factory $df;
    private array $table_data;
    private \ILIAS\DI\Container $DIC;
    private ilLearnplacesPlugin $plugin;


    public function __construct( ilLearnplacesPlugin $plugin, $table_data)
    {
        global $DIC;
        $this->ui_factory = $DIC['ui.factory'];
        $this->df = new \ILIAS\Data\Factory();
        global $DIC;
        $this->DIC = $DIC;
        $this->plugin = $plugin;
        $this->table_data = $table_data;
    }

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
            $record['name_with_link'] = $row['object_name_with_link'];
            $record['date'] = $row['number_of_ratings'];
            yield $row_builder->buildDataRow($row_id, $record);
            $row_id++;
        }

    }

    protected function getColumsForRepresentation(): array
    {
        $f = $this->ui_factory;
        $columns = [
            'name_with_link' => $f->table()->column()->text("Besucher"),
            'date' => $f->table()->column()->text("Datum und Uhrzeit"),
        ];
        return $columns;
    }



    public function getTotalRowCount(?array $filter_data, ?array $additional_parameters): ?int
    {
        return count($this->table_data);
    }
    public function getTableForRepresentation(): \ILIAS\UI\Implementation\Component\Table\Data
    {
        return $this->ui_factory->table()->data(
            '',
            $this->getColumsForRepresentation(),
            $this
        );
    }


}