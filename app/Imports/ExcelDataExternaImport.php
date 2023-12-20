<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExcelDataExternaImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    /**
    * @param Collection $collection
    */
    private $fila = 1;
    private $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function collection(Collection $rows)
    {
        $insertData = [];
        foreach ($rows as $row) {
            $fechaMuestreo = Carbon::createFromFormat('d/m/Y', $row['fecha_muestreo'])->format('Y-m-d');
            $insertData[] = [
                'id_user' => $this->userId,
                'fila' => $this->fila,
                'id_muestra' => $row['id_muestra'],
                'fecha_muestreo' => $fechaMuestreo,
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
        DB::table('data_externa_temporals')->insert($insertData);
        
    }

    public function chunkSize(): int
    {
        return 5000; // El tamaño de cada trozo (chunk)
    }
}
