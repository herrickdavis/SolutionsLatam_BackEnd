<?php

namespace App\Exports;

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

class CadenaCustodiaExport implements WithEvents
{    
    public $datos = array();
    public $muestra = array();

    public function setData($datos,$muestras) {
        $this->datos = $datos;
        $this->muestras = $muestras;
    }
    
    public function registerEvents(): array
    {
        //$this->a = $this->data[0];
        return [
            BeforeExport::class => function(BeforeExport $event){
                $templateFile = new LocalTemporaryFile(storage_path('app/public/TemplateCDC.xlsx'));
                $sheet = $event->writer->reopen($templateFile,Excel::XLSX);
                //$sheet = $event->writer->getSheetByIndex(0);
                $this->WriteDatosCOC($sheet);
                //$event->writer->getSheetByIndex(0)->export($event->getConcernable());
                //return $event->getWriter()->getSheetByIndex(0);
            },
            /*AfterSheet::class => function(AfterSheet $event){
                $event->sheet->getStartRow(1);
            }*/
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
    }
}
