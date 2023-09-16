<?php

class ExcelHelper
{   
    const MAX_ALLOW_ROW = 4000;
    const CHUNK_SIZE = 1000;

    /**
     * { function_description }
     *
     * @param      int   $numRow  The number row
     */
    private static function throwMaxAllowRowError()
    {
        die('<h4 style="margin:auto;float:left;color:#f00;padding:5px;border:1px solid #F00;border-radius: 5px;">Bạn chỉ được tải lên file excel có tối đa ' . self::MAX_ALLOW_ROW . ' dòng, vui lòng kiểm tra lại.</h4>');
    }
        
    /**
     * { function_description }
     *
     * @param      string  $filePath   The file path
     * @param      int     $numColumn  The number column
     * @param      int     $numRow     The number row
     *
     * @return     array   ( description_of_the_return_value )
     */
    public static function parse(string $filePath, int $fromCol = 1, $toCol = 36)
    {   
        $pos = 1;

        do{
            $reader = PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);

            $reader->setReadFilter(new class($pos) implements PhpOffice\PhpSpreadsheet\Reader\IReadFilter{
                public $startRow;
                public $endRow;

                public function __construct($pos)
                {
                    $this->startRow = $pos;
                    $this->endRow = $this->startRow + ExcelHelper::CHUNK_SIZE;
                }

                public function readCell($column, $row, $worksheetName = '')
                {
                    return $row >= $this->startRow && $row < $this->endRow;
                }
            });

            $sheet = $reader->load($filePath)->getActiveSheet();

            $rows = [];
            for ($rowIdx = $reader->getReadFilter()->startRow; $rowIdx < $reader->getReadFilter()->endRow; $rowIdx++) { 
                $cols = [];
                $isNotEmpty = 0; // Để kiểm tra xem dòng có rỗng không
                for ($colIdx = $fromCol; $colIdx <= $toCol; $colIdx++) { 
                    $cols[$colIdx] = $sheet->getCellByColumnAndRow($colIdx, $rowIdx)->getValue();
                    $isNotEmpty += empty($cols[$colIdx]) ? 0 : 1;
                }

                if($isNotEmpty) $rows[] = $cols;
            }

            $pos += ExcelHelper::CHUNK_SIZE;

            if($pos >= ExcelHelper::MAX_ALLOW_ROW + 2){
                return self::throwMaxAllowRowError();
            }

            $sheet->__destruct();
            unset($sheet);
            unset($reader);

            yield $rows;

        }while($rows && count($rows) <= ExcelHelper::MAX_ALLOW_ROW + 2);
    }
}