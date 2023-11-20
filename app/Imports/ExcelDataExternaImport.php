<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

class ExcelDataExternaImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    /**
    * @param Collection $collection
    */
    private $fila = 1;
    public function collection(Collection $rows)
    {
        $insertData = [];
        foreach ($rows as $row) {
            $insertData[] = [
                'fila' => $this->fila,
                'id_muestra' => $row['id_muestra'],
                'fecha_muestreo' => $row['fecha_muestreo'],
                'matriz' => $row['matriz'],
                'tipo_muestra' => $row['tipo_muestra'],
                'proyecto' => $row['proyecto'],
                'estacion' => $row['estacion'],
                'empresa_contratante' => $row['empresa_contratante'],
                'empresa_solicitante' => $row['empresa_solicitante'],
                'parametro' => $row['parametro'],
                'valor' => $row['valor'],
                'unidad' => $row['unidad']
            ];
            $this->fila++;
        }
        Log::info($insertData);
        DB::table('data_externa_temporals')->insert($insertData);
        
    }

    public function chunkSize(): int
    {
        return 5000; // El tamaño de cada trozo (chunk)
    }
}
