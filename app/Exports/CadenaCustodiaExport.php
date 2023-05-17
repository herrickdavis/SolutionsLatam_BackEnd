<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Files\LocalTemporaryFile;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Files\TemporaryFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CadenaCustodiaExport
{    
    public $info = array();
    public $parametros_laboratorio = array();
    public $parametros_in_situ = array();

    public function setData($info,$parametros_laboratorio, $parametros_in_situ) {
        $this->info = $info;
        $this->parametros_laboratorio = $parametros_laboratorio;
        $this->parametros_in_situ = $parametros_in_situ;
    }

    public function export(): StreamedResponse
    {
        // Carga el archivo de Excel existente
        $spreadsheet = IOFactory::load(storage_path('app/public/Book1.xlsx'));

        // Obtiene la primera hoja en el archivo (ajusta esto si necesitas trabajar con múltiples hojas)
        $worksheet = $spreadsheet->getActiveSheet();

        $highestRow = $worksheet->getHighestDataRow();
        $highestColumn = $worksheet->getHighestDataColumn();
        
        $info = array_change_key_case((array) $this->info,  CASE_UPPER);

        // Itera sobre las celdas y realiza las modificaciones
        for ($row = 1; $row <= $highestRow; $row++) {
            $col = 'A';
            while ($col <= $highestColumn) {
                $cellValue = $worksheet->getCell($col . $row)->getValue();

                //Busco y reeemplazo
                $texto = str_replace("[","",$cellValue);
                $texto = str_replace("]","",$texto);

                if (array_key_exists($texto, $info)) {
                    $worksheet->setCellValue($col . $row, $info[$texto]);
                } elseif ($cellValue  == "[]") {

                } else {
                    //aca si tiene el formato [texto] lo reemplazo por vacio
                    $patron = '/^\[.*\]$/';
                    if (preg_match($patron,$cellValue)) {
                        $worksheet->setCellValue($col . $row, "");
                    }
                }
                $col = $this->sumarLetra($col);
            }
        }

        // Prepara la respuesta de transmisión
        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        });

        // Establece las cabeceras de la respuesta
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="archivo_modificado.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    private function sumarLetra($letra) {
        $nuevaLetra = '';
        $longitud = strlen($letra);
        $carry = 1;
    
        for ($i = $longitud - 1; $i >= 0; $i--) {
            $ascii = ord($letra[$i]) + $carry;
            if ($ascii > ord('Z')) {
                $carry = 1;
                $ascii = ord('A');
            } else {
                $carry = 0;
            }
            $nuevaLetra = chr($ascii) . $nuevaLetra;
        }
    
        if ($carry) {
            $nuevaLetra = 'A' . $nuevaLetra;
        }
    
        return $nuevaLetra;
    }

    
    
    /*public function registerEvents(): array
    {
        //$this->a = $this->data[0];
        return [
            BeforeExport::class => function(BeforeExport $event){
                $templateFile = new LocalTemporaryFile(storage_path('app/public/Book1.xlsx'));
                $sheet = $event->writer->reopen($templateFile,Excel::XLSX);
                //$sheet = $event->writer->getSheetByIndex(0);
                $this->WriteDatosCOC($sheet);
                //$event->writer->getSheetByIndex(0)->export($event->getConcernable());
                //return $event->getWriter()->getSheetByIndex(0);
            },
            /*AfterSheet::class => function(AfterSheet $event){
                $event->sheet->getStartRow(1);
            }
        ];
    }

    public function WriteDatosCOC($sheet) {
        $sheet->setCellValue('C7', $this->datos['cliente']);
        $sheet->setCellValue('C8', $this->datos['contacto']);
        $sheet->setCellValue('C9', $this->datos['correo']);
        $sheet->setCellValue('C10', $this->datos['procedencia']);
        $sheet->setCellValue('C11', $this->datos['proyecto']);
        $sheet->setCellValue('AT9', $this->datos['numero_proceso']);
        $sheet->setCellValue('AT10', $this->datos['numero_os']);
    }*/
}
