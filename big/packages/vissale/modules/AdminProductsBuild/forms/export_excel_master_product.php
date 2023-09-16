<?php

class ExportExcelMasterProduct
{
    private $mp;
    private $spreadsheet;
    private $products;
    private $row = 1;

    const DEFAULT_WIDTH = 15;

    function __construct()
    {   
        require_once ROOT_PATH . 'packages/core/includes/common/ExcelHelper.php';
        $this->mp = new ProductHelper();

        $this->handle();
    }

    /**
     * { function_description }
     */
    private function handle()
    {   
        $this->validate();

        $this->export();
    }

    /**
     * { function_description }
     */
    private function validate()
    {
        if (!$this->mp::hasPrivilegeOnMasterProduct()) {
            Url::js_redirect(true, 'Bạn không có quyền truy cập tính năng này.');
        }
    }

    private function mb_ucfirst($string, $encoding)
    {
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, null, $encoding);
        return mb_strtoupper($firstChar, $encoding) . $then;
    }

    /**
     * { function_description }
     */
    private function export()
    {
        if(!$this->getProducts()){
            return;
        }

        $this->initSpreadSheet();
        
        $this->fillHeader();

        $this->fillBody();


        $fileName = sprintf('product_%s_%s', Session::get('user_data', 'id'), date('d_m_Y'));
        $this->attachClient($fileName, 'Xlsx');
    }

    /**
     * Gets the products.
     *
     * @return     <type>  The products.
     */
    private function getProducts()
    {
        return $this->products = $this->mp->allWithRelation();
    }

    /**
     * { function_description }
     */
    private function fillHeader()
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $columns = array_values($this->getColumns());

        foreach ($columns as $numColumn => $value) {
            $numColumn++;
            $sheet->setCellValueExplicitByColumnAndRow($numColumn, $this->row, $value['name'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING, false)
                ->getColumnDimensionByColumn($numColumn)
                ->setWidth($value['width']);

            $style = $sheet->getStyleByColumnAndRow($numColumn, $this->row);
            
            $style->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB(PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE);

            $style->getFont()
                ->getColor()
                ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        }

        $this->row++;
    }

    /**
     * { function_description }
     */
    private function fillBody()
    {   
        $sheet = $this->spreadsheet->getActiveSheet();
        $columns = array_keys($this->getColumns());

        foreach ($this->products as $product) {
            foreach ($columns as $numColumn => $slug) {
                $sheet->setCellValueExplicitByColumnAndRow($numColumn+1, $this->row, $product[$slug], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING, false);
            }
            
            $this->row++;
        }
    }

    /**
     * Gets the columns.
     *
     * @return     array  The columns.
     */
    private function getColumns()
    {
        return [
            'id' => [
                'name' => 'ID',
                'width' => 10
            ],
            'code' => [
                'name' => 'Code',
                'width' => self::DEFAULT_WIDTH
            ],
            'name' => [
                'name' => 'Tên',
                'width' => self::DEFAULT_WIDTH
            ],
            'full_name' => [
                'name' => 'Tên đầy đủ',
                'width' => self::DEFAULT_WIDTH
            ],
            'master_unit_name' => [
                'name' => 'Đơn vị',
                'width' => self::DEFAULT_WIDTH
            ],
            'master_bundle_name' => [
                'name' => 'Phân Loại',
                'width' => self::DEFAULT_WIDTH
            ],
            'label_name' => [
                'name' => 'Nhãn',
                'width' => self::DEFAULT_WIDTH
            ],
            'cost_price' => [
                'name' => 'Giá vốn',
                'width' => self::DEFAULT_WIDTH
            ],
            'weight' => [
                'name' => 'Trọng lượng',
                'width' => self::DEFAULT_WIDTH
            ],
            'status_name' => [
                'name' => 'Trạng thái',
                'width' => self::DEFAULT_WIDTH
            ],
            'note' => [
                'name' => 'Ghi chú',
                'width' => self::DEFAULT_WIDTH
            ],
            'factory_product_code' => [
                'name' => 'Mã nhà máy',
                'width' => self::DEFAULT_WIDTH
            ],
            'created_username' => [
                'name' => 'Người tạo',
                'width' => self::DEFAULT_WIDTH
            ],
            'created_at' => [
                'name' => 'Thời gian tạo',
                'width' => self::DEFAULT_WIDTH
            ],
            'updated_username' => [
                'name' => 'Người cập nhật',
                'width' => self::DEFAULT_WIDTH
            ],
            'all_fields_updated_at' => [
                'name' => 'Thời gian cập nhật',
                'width' => self::DEFAULT_WIDTH
            ],
        ];
    }

    /**
     * Initializes the spread sheet.
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function initSpreadSheet()
    {
        $this->spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $this->spreadsheet->getProperties()
            ->setCreator("QLBH")
            ->setLastModifiedBy("QLBH")
            ->setTitle("Sản phẩm hệ thống")
            ->setSubject("Sản phẩm hệ thống");

        return $this->spreadsheet;
    }

    /**
     * Attaches the client.
     *
     * @param      string  $fileName  The file name
     */
    private function attachClient(string $fileName, string $format = 'Xlsx')
    {   
        if($format === 'Xlsx'){
            $objWriter = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($this->spreadsheet);
        }else{
            $objWriter = new PhpOffice\PhpSpreadsheet\Writer\Xls($this->spreadsheet);
        }

        ob_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $fileName . '.' . $format . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter->save('php://output');

        exit;
    }
}
