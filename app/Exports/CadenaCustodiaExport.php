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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
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
        $spreadsheet = IOFactory::load(storage_path('app/public/Book3.xlsx'));
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
        $contador_insitu = 0;
        $contador_muestras = 0;
        $inicio_parametros_laboratorio = '';
        $inicio_parametros_in_situ = '';
        #Recorro toda la plantilla y busco la cantidad de laboratorio e insitu
        $flag_laboratorio = false;
        for ($row = 1; $row <= $highestRow; $row++) {
            $col = 'A';
            while ($this->column_to_number($col) <= $this->column_to_number($highestColumn)) {
                $cellValue = $worksheet->getCell($col . $row)->getValue();
                if(substr($cellValue, -strlen("_LABORATORIO]")) == "_LABORATORIO]") {
                    $contador_laboratorio++;
                    $flag_laboratorio = true;
                    $texto = str_replace("[","",$cellValue);
                    $texto = str_replace("]","",$texto);
                    $texto_nombre_laboratorio = $texto;
                    if($inicio_parametros_laboratorio == '') {
                        $inicio_parametros_laboratorio = $col;
                    }
                } else {
                    if($flag_laboratorio == true) {
                        break;
                        $row = $highestRow + 1;
                    }
                }                
                $col = $this->sumarLetra($col);
            }
        }
        #insitu
        $flag_insitu = false;
        for ($row = 1; $row <= $highestRow; $row++) {
            $col = 'A';
            while ($this->column_to_number($col) <= $this->column_to_number($highestColumn)) {
                $cellValue = $worksheet->getCell($col . $row)->getValue();
                if(substr($cellValue, -strlen("_INSITU]")) == "_INSITU]") {
                    $contador_insitu++;
                    $flag_insitu = true;
                    $texto = str_replace("[","",$cellValue);
                    $texto = str_replace("]","",$texto);
                    $texto_nombre_insitu = $texto;
                    if($inicio_parametros_in_situ == '') {
                        $inicio_parametros_in_situ = $col;
                    }                    
                } else {
                    if($flag_insitu == true) {
                        break;
                        $row = $highestRow + 1;
                    }
                }                
                $col = $this->sumarLetra($col);
            }
        }
        #Numero Muestras
        $flag_insitu = false;
        for ($row = 1; $row <= $highestRow; $row++) {
            $col = 'A';
            while ($this->column_to_number($col) <= $this->column_to_number($highestColumn)) {
                $cellValue = $worksheet->getCell($col . $row)->getValue();
                if(preg_match('/\[BUCLE\]\[.*?\]/', $cellValue)) {
                    $contador_muestras++;
                    $flag_muestras = true;
                    break;
                }
                $col = $this->sumarLetra($col);
            }
        }
        //Averiguo la cantidad de hojas que necesito
        $cantidad_hojas_laboratorio = intdiv(count($parametros_laboratorio[$texto_nombre_laboratorio]),$contador_laboratorio);
        $cantidad_hojas_insitu = intdiv(count($parametros_in_situ[$texto_nombre_insitu]),$contador_insitu);
        $num_hojas_parametros = max($cantidad_hojas_laboratorio, $cantidad_hojas_insitu);
        $cantidad_hojas_muestras = intdiv(count($info),$contador_muestras);
        $num_hojas_totales = $num_hojas_parametros + $cantidad_hojas_muestras;
        //dd($num_hojas_totales);
        for ($i=1; $i <= $num_hojas_totales; $i++) {
            // Agrega la hoja copiada al archivo de Excel
            $clonedWorksheet = clone $spreadsheet->getActiveSheet();
            //obtengo nombre de la hojas actual
            $activeSheetName = $worksheet->getTitle();
            //creo las nuevas hojas
            $clonedWorksheet->setTitle($activeSheetName."(".$i.")");
            $spreadsheet->addSheet($clonedWorksheet);
        }

        #Laboratorio
        $patron = '/^\[.*\]$/';
        $n = 0;
        while(count($parametros_laboratorio[$texto_nombre_laboratorio]) > $n*$contador_laboratorio) {
            $parametros = array_slice($parametros_laboratorio[$texto_nombre_laboratorio],($n)*$contador_laboratorio,$contador_laboratorio);
            $sheet = $spreadsheet->getSheet($n);
            $tag = false;
            $contador2 = 0;
            $tag_inicio = false;
            for ($row = 1; $row <= $highestRow; $row++) {
                $col = 'A';
                $contador=0;
                while ($this->column_to_number($col) <= $this->column_to_number($highestColumn)) {
                    $cellValue = $sheet->getCell($col . $row)->getValue();
                    if (preg_match('/\[BUCLE\]\[.*?\]/', $cellValue)) {
                        if(count($info) > $contador2 + 1) {
                            if($tag_inicio) {
                                $contador2++;
                            }
                            $tag_inicio = true;
                        } else {
                            $tag = true;
                        }
                    }
                    if (preg_match('/\[BUCLE\]\[.*?\]/', $cellValue) && $tag) {
                        break;
                    }
                    $texto = str_replace("[","",$cellValue);
                    $texto = str_replace("]","",$texto);
                    if(substr($cellValue, -strlen("_LABORATORIO]")) == "_LABORATORIO]") {
                        if($texto_nombre_laboratorio == $texto) {
                            if (array_key_exists($contador,$parametros)) {
                                $sheet->setCellValue($col . $row, $parametros[$contador]);
                                $contador++;
                            }
                        } elseif((substr($cellValue, 0, strlen("[EXISTE_")) == "[EXISTE_") && (substr($cellValue, -strlen("_LABORATORIO]")) == "_LABORATORIO]")) {
                            $i=$contador2;                        
                            foreach($info[$i]['PARAMETROS_LABORATORIO'] as $valor) {
                                //dd($valor);
                                if($valor->{strtolower($texto_nombre_laboratorio)} != null) {
                                    if($this->column_to_number($col)-$this->column_to_number($inicio_parametros_laboratorio) < count($parametros)) {
                                        if($valor->{strtolower($texto_nombre_laboratorio)} == $parametros[$this->column_to_number($col)-$this->column_to_number($inicio_parametros_laboratorio)]) {
                                            $sheet->setCellValue($col . $row, 'X');
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $col = $this->sumarLetra($col);
                }
            }
            $n++;
        }

        #In Situ
        $n = 0;
        while(count($parametros_in_situ[$texto_nombre_insitu]) > $n*$contador_insitu) {
            $parametros = array_slice($parametros_in_situ[$texto_nombre_insitu],($n)*$contador_insitu,$contador_insitu);
            $sheet = $spreadsheet->getSheet($n);
            $tag = false;
            $contador2 = 0;
            $tag_inicio = false;
            for ($row = 1; $row <= $highestRow; $row++) {
                $col = 'A';
                $contador=0;
                while ($this->column_to_number($col) <= $this->column_to_number($highestColumn)) {
                    $cellValue = $sheet->getCell($col . $row)->getValue();
                    if (preg_match('/\[BUCLE\]\[.*?\]/', $cellValue)) {
                        if(count($info) > $contador2 + 1) {
                            if($tag_inicio) {
                                $contador2++;
                            }
                            $tag_inicio = true;
                        } else {
                            $tag = true;
                        }
                    }
                    if (preg_match('/\[BUCLE\]\[.*?\]/', $cellValue) && $tag) {
                        break;
                    }
                    
                    $texto = str_replace("[","",$cellValue);
                    $texto = str_replace("]","",$texto);
                    if(substr($cellValue, -strlen("_INSITU]")) == "_INSITU]") {
                        if($texto_nombre_insitu == $texto) {
                            if (array_key_exists($contador,$parametros)) {
                                $sheet->setCellValue($col . $row, $parametros[$contador]);
                                $contador++;
                            }
                        } elseif((preg_match($patron, $cellValue)) && (substr($cellValue, -strlen("_INSITU]")) == "_INSITU]")) {
                            $i=$contador2;                        
                            foreach($info[$i]['PARAMETROS_INSITU'] as $valor) {
                                if($valor->{strtolower($texto_nombre_insitu)} != null) {
                                    if($this->column_to_number($col)-$this->column_to_number($inicio_parametros_in_situ) < count($parametros)) {
                                        if($valor->{strtolower($texto_nombre_insitu)} == $parametros[$this->column_to_number($col)-$this->column_to_number($inicio_parametros_in_situ)]) {
                                            $sheet->setCellValue($col . $row, $valor->{strtolower($texto)});
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $col = $this->sumarLetra($col);
                }
            }
            $n++;
        }

        #Recorro y reemplazo
        for ($i=0; $i <= $num_hojas_totales; $i++) {
            $sheet = $spreadsheet->getSheet($i);
            $contador = 0;
            $tag = false;
            $tag_inicio = false;
            for ($row = 1; $row <= $highestRow; $row++) {
                $col = 'A';
                while ($this->column_to_number($col) <= $this->column_to_number($highestColumn)) {
                    $cellValue = $sheet->getCell($col . $row)->getValue();
                    $patron = '/^\[.*\]$/';
                    if (preg_match('/\[BUCLE\]\[.*?\]/', $cellValue)) {
                        if(count($info) > $contador + 1) {
                            if($tag_inicio) {
                                $contador++;
                            }
                            $tag_inicio = true;
                        } else {
                            $tag = true;
                        }
                    }
                    if (preg_match('/\[BUCLE\]\[.*?\]/', $cellValue) && $tag) {
                        break;
                    }

                    if((preg_match($patron, $cellValue)) && (substr($cellValue, -strlen("_LABORATORIO]")) != "_LABORATORIO]") && (substr($cellValue, -strlen("_INSITU]")) != "_INSITU]")) {
                        $texto = str_replace("[BUCLE]","",$cellValue);
                        $texto = str_replace("[","",$texto);
                        $texto = str_replace("]","",$texto);
                        //dd($contador);
                        if (array_key_exists($texto, $info[$contador])) {
                            $sheet->setCellValue($col . $row, $info[$contador][$texto]);
                        }
                    }

                    if((preg_match($patron, $cellValue)) && (substr($cellValue, -strlen("_INSITU]")) == "_INSITU]")) {
                        //dd($cellValue);
                        $texto = str_replace("[","",$cellValue);
                        $texto = str_replace("]","",$texto);
                        if (array_key_exists($texto, $info[$contador])) {
                            //dd($info[$contador]['PARAMETROS_INSITU'][0]);
                            //dd($texto);
                            $sheet->setCellValue($col . $row, $info[$contador]['PARAMETROS_INSITU'][$texto]);
                        }
                    }
                    $col = $this->sumarLetra($col);

                    
                }
            }

            //Busco y dejo en blanco los campos [] no rellenados        
            for ($row = 1; $row <= $highestRow; $row++) {
                $col = 'A';
                while ($this->column_to_number($col) <= $this->column_to_number($highestColumn)) {
                    $cellValue = $sheet->getCell($col . $row)->getValue();
                    $patron = '/^\[.*\]$/';
                    if(preg_match($patron, $cellValue)) {                    
                        $sheet->setCellValue($col . $row, "");
                    }
                    
                    $col = $this->sumarLetra($col);
                }
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
