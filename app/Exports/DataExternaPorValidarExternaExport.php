<?php

namespace App\Exports;

use App\Models\DataExternaTemporal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataExternaPorValidarExternaExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $dataWithIndicators;

    public function __construct()
    {
        $this->dataWithIndicators = DataExternaTemporal::select(
                                                            "id_muestra",
                                                            "fecha_muestreo",
                                                            "id_matriz",
                                                            "matriz",
                                                            "id_tipo_muestra",
                                                            "tipo_muestra",
                                                            "id_proyecto",
                                                            "proyecto",
                                                            "id_estacion",
                                                            "estacion",
                                                            "id_empresa_contratante",
                                                            "empresa_contratante",
                                                            "id_empresa_solicitante",
                                                            "empresa_solicitante",
                                                            "id_parametro",
                                                            "parametro",
                                                            "valor",
                                                            "unidad"
                                                            )
                                                            ->where(function($query) {
                                                                $query->whereNull('id_muestra')
                                                                    ->orWhereNull('id_matriz')
                                                                    ->orWhereNull('id_tipo_muestra')
                                                                    ->orWhereNull('id_proyecto')
                                                                    ->orWhereNull('id_estacion')
                                                                    ->orWhereNull('id_empresa_contratante')
                                                                    ->orWhereNull('id_empresa_solicitante')
                                                                    ->orWhereNull('id_parametro');
                                                            })->get();
    }

    public function collection()
    {
        return $this->dataWithIndicators->map(function ($item) {
            $clonedItem = clone $item;
            unset($clonedItem->id_matriz);
            unset($clonedItem->id_tipo_muestra);
            unset($clonedItem->id_proyecto);
            unset($clonedItem->id_estacion);
            unset($clonedItem->id_empresa_contratante);
            unset($clonedItem->id_empresa_solicitante);
            unset($clonedItem->id_parametro);
            return $clonedItem;
        });
    }

    public function headings(): array
    {
        return [
            'ID MUESTRA',
            'FECHA MUESTREO',
            'MATRIZ',
            'TIPO MUESTRA',
            'PROYECTO',
            'ESTACION',
            'EMPRESA CONTRATANTE',
            'EMPRESA SOLICITANTE',
            'PARAMETRO',
            'VALOR',
            'UNIDAD'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [];
        foreach ($this->dataWithIndicators as $rowKey => $row) {
            if (is_null($row->id_matriz)) {
                $styles['C' . ($rowKey + 2)] = ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFF0000']]];
            }
            if (is_null($row->id_tipo_muestra)) {
                $styles['D' . ($rowKey + 2)] = ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFF0000']]];
            }
            if (is_null($row->id_proyecto)) {
                $styles['E' . ($rowKey + 2)] = ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFF0000']]];
            }
            if (is_null($row->id_estacion)) {
                $styles['F' . ($rowKey + 2)] = ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFF0000']]];
            }
            if (is_null($row->id_empresa_contratante)) {
                $styles['G' . ($rowKey + 2)] = ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFF0000']]];
            }
            if (is_null($row->id_empresa_solicitante)) {
                $styles['H' . ($rowKey + 2)] = ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFF0000']]];
            }
            if (is_null($row->id_parametro)) {
                $styles['I' . ($rowKey + 2)] = ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFF0000']]];
            }
        }

        return $styles;
    }
}
