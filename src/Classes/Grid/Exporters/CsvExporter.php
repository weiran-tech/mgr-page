<?php

namespace Weiran\MgrPage\Classes\Grid\Exporters;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Weiran\MgrPage\Classes\Grid\Column;

class CsvExporter extends AbstractExporter
{
    /**
     * @inheritDoc
     */
    public function export()
    {
        $filename = $this->getTable() . '.csv';

        $headers = [
            'Content-Encoding'    => 'UTF-8',
            'Content-Type'        => 'text/csv;charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        response()->stream(function () {
            $handle = fopen('php://output', 'wb');

            $titles = [];

            $this->chunk(function ($records) use ($handle, &$titles) {
                if (empty($titles)) {
                    $titles = $this->getHeaderRow();

                    // Add CSV headers
                    fputcsv($handle, $titles);
                }

                $records = $this->getFormattedRecords($records);
                foreach ($records as $record) {
                    fputcsv($handle, $record);
                }
            });

            // Close the output stream
            fclose($handle);
        }, 200, $headers)->send();

        exit;
    }

    /**
     * 获取 Header, 从记录中获取 Header
     * @return array
     */
    private function getHeaderRow(): array
    {
        $titles = collect();
        collect($this->grid->visibleColumns())->each(function (Column $column) use ($titles) {
            if ($column->name === Column::NAME_ACTION) {
                return;
            }
            $name = $column->name;
            /** @var Column $column */
            $column = $this->grid->visibleColumns()->first(function (Column $column) use ($name) {
                return $column->name === $name;
            });

            if ($column) {
                $titles->push($column->label);
            } else {
                $titles->push(Str::ucfirst($name));
            }
        });
        return $titles->toArray();
    }

    /**
     * @param Collection $records
     *
     * @return array
     */
    public function getHeaderRowFromRecords(Collection $records): array
    {
        $titles = collect(Arr::dot($records->first()->toArray()))->keys()->map(
            function ($key) {
                $key = str_replace('.', ' ', $key);

                return Str::ucfirst($key);
            }
        );

        return $titles->toArray();
    }

    /**
     * @param Collection $data
     * @return array
     */
    private function getFormattedRecords(Collection $data): array
    {
        return $data->map(function ($row) {
            $newRow = collect();
            $this->grid->visibleColumns()->each(function (Column $column) use ($row, $newRow) {
                if ($column->name === Column::NAME_ACTION) {
                    return;
                }
                $newRow->push(data_get($row->toArray(), $column->name));
            });
            return $newRow->toArray();
        })->toArray();
    }

    /**
     * @param Model $record
     *
     * @return array
     */
    public function getFormattedRecord(Model $record)
    {
        return Arr::dot($record->getAttributes());
    }
}
