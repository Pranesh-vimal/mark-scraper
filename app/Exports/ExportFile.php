<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportFile implements FromArray, WithHeadings, ShouldAutoSize, WithMapping
{

    use Exportable;

    protected $marks;
    protected $heading;

    public function  __construct($marks, $heading)
    {
        $this->marks = $marks;
        $this->heading = $heading;
    }

    public function headings(): array
    {
        $columns = [];

        foreach ($this->heading as $fields) {
            $columns[] = ucfirst(str_replace('_', ' ', $fields));
        }

        return $columns;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        return $this->marks;
    }

    public function map($marks): array
    {
        $pattern = [];
        foreach ($this->heading as $key => $heading) {
            array_push($pattern, isset($marks[$heading]) ?  $marks[$heading] : '-');
        }

        return [$pattern];
    }
}
