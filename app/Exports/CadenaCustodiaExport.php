<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class CadenaCustodiaExport
{    
    public $info = array();
    public $parametros_laboratorio = array();
    public $parametros_in_situ = array();
    public $ruta_documento = '';

    public function setData($info,$parametros_laboratorio, $parametros_in_situ, $ruta_documento) {
        $this->info = $info;
        $this->parametros_laboratorio = $parametros_laboratorio;
        $this->parametros_in_situ = $parametros_in_situ;
        $this->ruta_documento = $ruta_documento;
    }

    public function export(): StreamedResponse
    {
        // Carga el archivo de Excel existente        
        $spreadsheet = IOFactory::load(storage_path('app/'.$this->ruta_documento));
        // Obtiene la primera hoja en el archivo (ajusta esto si necesitas trabajar con múltiples hojas)
        $worksheet = $spreadsheet->getActiveSheet();
        $cellStyle = $worksheet->getStyle('A8');
        $font = $cellStyle->getFont();
        $fill = $cellStyle->getFill();
        $border = $cellStyle->getBorders();
        $alignment = $cellStyle->getAlignment();

        $styleDetails = sprintf(
            "Font: %s, Size: %s, Fill Type: %s, Fill Color: %s, Horizontal Alignment: %s",
            $font->getName(),
            $font->getSize(),
            $fill->getFillType(),
            $fill->getStartColor()->getARGB(),
            $fill->getStartColor()->getRGB(),
            $alignment->getHorizontal()
        );

        $highestRow = $worksheet->getHighestDataRow();
        $highestColumn = $worksheet->getHighestDataColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        $info = $this->info;
        $parametros_laboratorio = $this->parametros_laboratorio;
        $parametros_in_situ = $this->parametros_in_situ;
        // Itera sobre las celdas y realiza las modificaciones
        $contador_laboratorio = 0;
        $contador_insitu = 0;
        $contador_muestras = 0;
        $inicio_parametros_laboratorio = '';
        $inicio_parametros_in_situ = '';
        #Recorro toda la plantilla y busco la cantidad de laboratorio e insitu
        $flag_laboratorio = false;
        $texto_nombre_laboratorio = '';
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
        $texto_nombre_insitu = '';
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
        $cantidad_hojas_laboratorio = intdiv(count($parametros_laboratorio[$texto_nombre_laboratorio]) > 0 ? count($parametros_laboratorio[$texto_nombre_laboratorio]) - 1:count($parametros_laboratorio[$texto_nombre_laboratorio]),$contador_laboratorio);
        if(isset($parametros_in_situ[$texto_nombre_insitu])) {
            $cantidad_hojas_insitu = intdiv(count($parametros_in_situ[$texto_nombre_insitu]) > 0 ? count($parametros_in_situ[$texto_nombre_insitu]) - 1:count($parametros_in_situ[$texto_nombre_insitu]),$contador_insitu);
        } else {
            $cantidad_hojas_insitu = 0;
            $parametros_in_situ[$texto_nombre_insitu] = [];
        }
        $num_hojas_parametros = max($cantidad_hojas_laboratorio, $cantidad_hojas_insitu);
        $cantidad_hojas_muestras = intdiv(count($info) > 0 ? count($info) - 1:count($info),$contador_muestras);
        $num_hojas_totales = ($num_hojas_parametros + 1)*($cantidad_hojas_muestras + 1);
        //dd($cantidad_hojas_insitu);
        //dd($cantidad_hojas_muestras);
        //dd($num_hojas_totales);
        for ($i=1; $i < $num_hojas_totales; $i++) {
            // Agrega la hoja copiada al archivo de Excel
            $clonedWorksheet = clone $spreadsheet->getActiveSheet();
            //obtengo nombre de la hojas actual
            $activeSheetName = $worksheet->getTitle();
            //creo las nuevas hojas
            $clonedWorksheet->setTitle($activeSheetName."(".$i.")");
            $spreadsheet->addSheet($clonedWorksheet);
        }
        /*for ($i = 1; $i < $num_hojas_totales; $i++) {
            // Crea una nueva hoja en blanco
            $newWorksheet = new Worksheet($spreadsheet, $worksheet->getTitle() . " (" . $i . ")");
            $spreadsheet->addSheet($newWorksheet, $i);
            $spreadsheet->setActiveSheetIndex($i);
        
            // Copia el contenido y los estilos de cada celda
            for ($row = 1; $row <= $highestRow; ++$row) {
                for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                    $cellCoordinate = Coordinate::stringFromColumnIndex($col) . $row;
                    $cellValue = $worksheet->getCell($cellCoordinate)->getValue();
                    $cellStyle = $worksheet->getStyle($cellCoordinate);
        
                    // Establece el valor y el estilo en la nueva hoja
                    $newWorksheet->setCellValue($cellCoordinate, $cellValue);
                    $newWorksheet->duplicateStyle($cellStyle, $cellCoordinate);
                }
            }
        }*/

        #Laboratorio
        $patron = '/^\[.*\]$/';
        $n = 0;
        //dd($contador_laboratorio);
        $hoja = 0;
        while(count($parametros_laboratorio[$texto_nombre_laboratorio]) > $n*$contador_laboratorio) {
            $parametros = array_slice($parametros_laboratorio[$texto_nombre_laboratorio],($n)*$contador_laboratorio,$contador_laboratorio);
            $tag = false;
            $contador2 = 0;
            $tag_inicio = false;
            for($m=0;$m<$cantidad_hojas_muestras + 1; $m++) {
                $sheet = $spreadsheet->getSheet($hoja);
                for ($row = 1; $row <= $highestRow; $row++) {
                    $col = 'A';
                    $contador=0;
                    while ($this->column_to_number($col) <= $this->column_to_number($highestColumn)) {
                        $cellValue = $sheet->getCell($col . $row)->getValue();
                        if (preg_match('/\[BUCLE\]\[.*?\]/', $cellValue)) {
                            if(count($info) >= $contador2 + 1) {
                                if($tag_inicio) {
                                    $contador2++;                                
                                }
                                $tag_inicio = true;
                            } 
                            if(count($info) <= $contador2) {
                                $tag = true;
                            }
                        }
                        if ((preg_match('/\[BUCLE\]\[.*?\]/', $cellValue)) && $tag) {
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
                $hoja++;
            }
            $n++;
        }

        #In Situ
        $n = 0;
        $hoja = 0;
        while(count($parametros_in_situ[$texto_nombre_insitu]) > $n*$contador_insitu) {
            $parametros = array_slice($parametros_in_situ[$texto_nombre_insitu],($n)*$contador_insitu,$contador_insitu);
            $tag = false;
            $contador2 = 0;
            $tag_inicio = false;
            for($m=0;$m<$cantidad_hojas_muestras + 1; $m++) {
                $sheet = $spreadsheet->getSheet($hoja);
                for ($row = 1; $row <= $highestRow; $row++) {
                    $col = 'A';
                    $contador=0;
                    while ($this->column_to_number($col) <= $this->column_to_number($highestColumn)) {
                        $cellValue = $sheet->getCell($col . $row)->getValue();
                        if (preg_match('/\[BUCLE\]\[.*?\]/', $cellValue)) {
                            if(count($info) >= $contador2 + 1) {
                                if($tag_inicio) {
                                    $contador2++;                                
                                }
                                $tag_inicio = true;
                            } 
                            if(count($info) <= $contador2) {
                                $tag = true;
                            }
                        }
                        if ((preg_match('/\[BUCLE\]\[.*?\]/', $cellValue)) && $tag) {
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
                                                if(($valor->{strtolower($texto)} != "None") && ($valor->{strtolower($texto)} != Null)) {
                                                    $sheet->setCellValue($col . $row, $valor->{strtolower($texto)});
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $col = $this->sumarLetra($col);
                    }
                }
                $hoja++;
            }
            $n++;
        }
        #Recorro y reemplazo
        for ($m=0; $m < $cantidad_hojas_muestras + 1; $m++) {
            $hoja = $m;
            $array_muestras = array_slice($info, ($m)*$contador_muestras);
            for ($i=0; $i < $num_hojas_parametros + 1; $i++) {
                $contador = 0;
                $tag_inicio = false;
                $tag = false;
                $sheet = $spreadsheet->getSheet($hoja);
                $hoja = $hoja + $cantidad_hojas_muestras + 1;
                for ($row = 1; $row <= $highestRow; $row++) {
                    $col = 'A';
                    while ($this->column_to_number($col) <= $this->column_to_number($highestColumn)) {
                        $cellValue = $sheet->getCell($col . $row)->getValue();
                        $patron = "/\[([^\]]*)\]/";
                        if (preg_match('/\[BUCLE\]\[.*?\]/', $cellValue)) {
                            if(count($array_muestras) >= $contador + 1) {
                                if($tag_inicio) {
                                    $contador++;
                                }
                                $tag_inicio = true;
                            }
                            if(count($array_muestras) <= $contador) {
                                $tag = true;
                            }
                        }
                        if ($tag) {
                            $contador = 0;
                            if(preg_match('/\[END\]$/', $cellValue)) {
                                $tag = false;
                            }
                            break;
                        }

                        if((preg_match($patron, $cellValue)) && (substr($cellValue, -strlen("_LABORATORIO]")) != "_LABORATORIO]") && (substr($cellValue, -strlen("_INSITU]")) != "_INSITU]")) {
                            $cellValue = str_replace("[BUCLE]","",$cellValue);
                            preg_match_all("/\[([^\]]*)\]/", $cellValue, $matches);
                            $newCellValue = $cellValue;
                            if (!empty($matches[1])) {
                                foreach ($matches[1] as $texto) {
                                    // Verifica si el texto entre corchetes existe como clave en el array $info
                                    if (array_key_exists($texto, $array_muestras[$contador])) {
                                        if (($array_muestras[$contador][$texto] != "None") && ($array_muestras[$contador][$texto] != null)) {
                                            // Reemplaza cada coincidencia del texto entre corchetes con su valor correspondiente en $info
                                            $pattern = '/' . preg_quote("[$texto]", '/') . '/';
                                            $replacement = $array_muestras[$contador][$texto];
                                            $newCellValue = preg_replace($pattern, $replacement, $newCellValue);
                                        }
                                    }
                                }
                            }
                        
                            // Establece el nuevo valor de la celda con todos los reemplazos realizados
                            $sheet->setCellValue($col . $row, $newCellValue);
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
}
