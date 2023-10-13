<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EddExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $sql;
    protected $headings;

    public function __construct(string $sql, array $headings)
    {
        $this->sql = $sql;
        $this->headings = $headings;
    }

    public function collection()
    {
        $results = DB::select($this->sql);
        $resultsArray = json_decode(json_encode($results), true);

        // Filtrar las cabeceras y mantener el orden según $headings
        return collect($resultsArray)->map(function($row) {
            return collect($this->headings)->map(function($heading) use ($row) {
                return $row[$heading] ?? null; // Devuelve el valor o null si no existe
            })->toArray();
        });
    }

    public function headings(): array
    {
        return $this->headings;
    }
}
