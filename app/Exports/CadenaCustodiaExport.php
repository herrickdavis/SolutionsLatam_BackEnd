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
        \Log::info("dasdsr aqui"."\n");
        $spreadsheet = IOFactory::load(storage_path('app/public/Book3.xlsx'));
        \Log::info("Pasr aqui"."\n");
        // Obtiene la primera hoja en el archivo (ajusta esto si necesitas trabajar con múltiples hojas)
        $worksheet = $spreadsheet->getActiveSheet();

        $highestRow = $worksheet->getHighestDataRow();
        $highestColumn = $worksheet->getHighestDataColumn();
        
        $info = $this->info;
        $parametros_laboratorio = $this->parametros_laboratorio;
        $parametros_in_situ = $this->parametros_in_situ;
        // Itera sobre las celdas y realiza las modificaciones
        //\Log::info($highestColumn."\n");
        $contador_laboratorio = 0;
        $contador_in_situ = 0;
        $contador_muestras = 0;
        $inicio_parametros_laboratorio = 0;
        $inicio_parametros_in_situ = 0;
        //Recorro en busca de metodos de laboratorio y relleno
        //dd($info[1]);
        //\Log::info("POr aqui"."\n");
        //\Log::info($highestRow."\n");
        //\Log::info($this->column_to_number($highestColumn)."\n");
        for ($row = 1; $row <= $highestRow; $row++) {
            $col = 'A';
            while ($this->column_to_number($col) <= $this->column_to_number($highestColumn)) {
                $cellValue = $worksheet->getCell($col . $row)->getValue();

                if ($cellValue  == "[METODO_LABORATORIO]" & count($parametros_laboratorio) > $contador_laboratorio) {
                    if($inicio_parametros_laboratorio == 0){
                        $inicio_parametros_laboratorio = $this->column_to_number($col);
                    }
                    //\Log::info($parametros_laboratorio[$contador_laboratorio]->parametro."\n");
                    $worksheet->setCellValue($col . $row, $parametros_laboratorio[$contador_laboratorio]);
                    $contador_laboratorio++;
                }
                $col = $this->sumarLetra($col);
            }
        }
        //\Log::info("ahora aqui"."\n");
        $contador_laboratorio = 0;
        //Recorro en busca de parametros in situ y relleno
        for ($row = 1; $row <= $highestRow; $row++) {
            $col = 'A';
            while ($this->column_to_number($col) <= $this->column_to_number($highestColumn)) {
                $cellValue = $worksheet->getCell($col . $row)->getValue();

                if ($cellValue  == "[PARAMETROS_IN_SITU]" & count($parametros_in_situ) > $contador_in_situ) {
                    if($inicio_parametros_in_situ == 0){
                        $inicio_parametros_in_situ = $this->column_to_number($col);
                    }
                    //\Log::info($parametros_laboratorio[$contador_laboratorio]->parametro."\n");
                    $worksheet->setCellValue($col . $row, $parametros_in_situ[$contador_in_situ]);
                    $contador_in_situ++;
                }
                $col = $this->sumarLetra($col);
            }
        }
        $contador_veces = 0;
        for ($row = 1; $row <= $highestRow; $row++) {
            $col = 'A';
            if($worksheet->getCell("A". $row)->getValue() == "[ESTACION]") {
                $contador_veces++;
                if($contador_veces == 1 || $contador_veces == 0) {
                    $contador_muestras = 0;    
                } else {
                    $contador_muestras = $contador_veces - 1;
                }
            }
            $contador_laboratorio = 0;
            while ($this->column_to_number($col) <= $this->column_to_number($highestColumn)) {
                $cellValue = $worksheet->getCell($col . $row)->getValue();
                
                //Busco y reeemplazo
                $texto = str_replace("[","",$cellValue);
                $texto = str_replace("]","",$texto);
                //\Log::info($texto."\n");
                if(count($info) > $contador_muestras) {
                    if (array_key_exists($texto, $info[$contador_muestras])) {
                        $patron = '/^\[.*\]$/';
                        if(preg_match($patron,$cellValue)) {
                            $worksheet->setCellValue($col . $row, $info[$contador_muestras][$texto]);
                        }
                    } elseif ($cellValue == "[EXISTE_METODO]" & count($parametros_laboratorio) > $contador_laboratorio) {
                        //dd($parametros_laboratorio[$contador_laboratorio]);
                        //\Log::info($contador_muestras."Estacin \n");
                        \Log::info($contador_muestras."\n");
                        if(count($info[$contador_muestras]["METODOS_LABORATORIO"]) > 0) {
                            if($info[$contador_muestras]["METODOS_LABORATORIO"][0] == $parametros_laboratorio[$contador_laboratorio]) {
                                $worksheet->setCellValue($col . $row, "X");
                                unset($info[$contador_muestras]["METODOS_LABORATORIO"][0]);
                                $info[$contador_muestras]["METODOS_LABORATORIO"] = array_values($info[$contador_muestras]["METODOS_LABORATORIO"]);
                            } else {
                                $worksheet->setCellValue($col . $row, "");
                            }
                        } else {
                            $worksheet->setCellValue($col . $row, "");
                        }
                        $contador_laboratorio++;
                    } else {
                        //aca si tiene el formato [texto] lo reemplazo por vacio
                        $patron = '/^\[.*\]$/';
                        if (preg_match($patron,$cellValue)) {
                            $worksheet->setCellValue($col . $row, "");
                        }
                    }
                } else {
                    //aca si tiene el formato [texto] lo reemplazo por vacio
                    $patron = '/^\[.*\]$/';
                    if (preg_match($patron,$cellValue)) {
                        $worksheet->setCellValue($col . $row, "");
                    }
                }
                $col = $this->sumarLetra($col);
                //\Log::info($col." : ".$texto."\n");
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

    private function column_to_number($column) {
        $column = strtoupper($column);
        $count = strlen($column);
        $number = 0;
        
        for ($i = 0; $i < $count; $i++) {
            $number *= 26;
            $number += ord($column[$i]) - ord('A') + 1;
        }
        
        return $number;
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
