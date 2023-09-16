<?php

class AdminProductsBuild extends Module
{
    public static $group_id;
    public static $account_id;
    function __construct($row)
    {
        require_once ROOT_PATH . 'packages/vissale/lib/php/vissale.php';
        require_once ROOT_PATH . 'packages/core/includes/common/Systems.php';
        require_once ROOT_PATH . 'packages/vissale/modules/AdminProductsBuild/ProductHelper.php';

        AdminProductsBuild::$group_id = Session::get('group_id');
        AdminProductsBuild::$account_id = Session::get('user_id');
        Module::Module($row);
        require_once 'db.php';
        switch (Url::get('do')) {
            case 'export_excel':
                $this->export_excel();
                break;
            case 'update-from-master':
            case 'update-all':
            case 'edit':
            case 'add':
                require_once "forms/edit.php";
                $this->add_form(new EditAdminProductsBuildForm());
                break;

            case 'search_master_product':
                return $this->searchMasterProduct();

            case 'change_product_code':
                return $this->changeProductCode();

            case 'import_excel_master_product':
                require_once "forms/import_excel_master_product.php";
                return $this->add_form(new ImportExcelMasterProduct());

            case 'export_excel_master_product':
                require_once "forms/export_excel_master_product.php";
                return $this->add_form(new ExportExcelMasterProduct());

            case 'list':
                require_once "forms/list.php";
                $this->add_form(new ListAdminProductsBuildForm());
                break;

            case 'master_product':
                require_once "forms/master_product.php";
                $this->add_form(new MasterProductForm());
                break;

            default:
                require_once "forms/list.php";
                $this->add_form(new ListAdminProductsBuildForm());
                break;
        }
    }

    /**
     * { function_description }
     */
    private function searchMasterProduct()
    {
        [$masterProducts, $count] = (new ProductHelper())->findMasterProducts([
            'code' => URL::get('code'),
            'name' => URL::get('name'),
            'system' => URL::iget('system'),
            'created_by' => URL::iget('created_by'),
            'updated_by' => URL::iget('updated_by'),
            'created_at' => URL::get('created_by'),
            'updated_at' => URL::get('created_by'),
            'bundle' => 0,
            'label' => 0,
            'unit' => 0,
            'status' => 1,
            'limit' => URL::iget('limit'),
            'p' => URL::iget('p'),
            'operator' => 'OR'
        ]);
        send_json(['master_products' => $masterProducts, 'count' => $count]);
    }

    /**
     * { function_description }
     */
    private function changeProductCode()
    {
        try{
            $helper = new ProductHelper();
            $groupID = Session::get('group_id');

            if(!$code = URL::get('code')){
                send_json(['status' => 'error', 'message' => 'Mã sản phẩm không hợp lệ !']);
            }

            if(!$product = $helper->findProduct($groupID, $code)){
                send_json(['status' => 'error', 'message' => 'Không tồn tại mã sản phẩm !']);
            }

            if($product['standardized']){
                send_json(['status' => 'error', 'message' => 'Sản phẩm đã chuẩn hóa không được phép sửa !']);
            }

            $update = ['code' => $code . '_' . mt_rand(100000, 999999) . '_old'];
            if(!$helper->updateProduct($product['id'],  $update)){
                send_json(['status' => 'error', 'message' => 'Đổi code thất bại !']);
            }

            send_json(['status' => 'success', 'message' => 'Đổi code thành công !']);
        }catch(Throwable $e){
            send_json(['status' => 'error', 'message' => System::is_local() ? $e->getMessage() : 'Lỗi hệ thống !']);
        }
    }

    function export_excel(){
        $group_id = AdminProductsBuild::$group_id;
        $colomns = array();
        $value_colomns = array();
        /////////////////////
        $c_temp = get_product_columns();

        $temp_letter_arr = array(
            1=>'A',
            2=>'B',
            3=>'C',
            4=>'D',
            5=>'E',
            6=>'F',
            7=>'G',
            8=>'H',
            9=>'I',
            10=>'J',
            11=>'K',
            12=>'L',
            13=>'M',
            14=>'N',
            15=>'O',
            16=>'P',
            17=>'Q',
            18=>'R',
            19=>'S',
            20=>'T',
            21=>'U',
            22=>'V',
            23=>'W',
            24=>'X',
            25=>'Y',
            26=>'Z',
            27=>'AA',
            28=>'AB',
            29=>'AC',
            30=>'AD',
            31=>'AE',
            32=>'AF',
            33=>'AG',
            34=>'AH',
            35=>'AI',
            36=>'AJ',
        );
        /////////////////////
        $i=1;
        $letter_arr = array();
        foreach($c_temp as $key=>$value){
            $colomns[] = $value['name'];
            $value_colomns[] = $value['id'];
            $letter_arr[$i] = $temp_letter_arr[$i];
            $i++;
        }
        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()->setCreator("QLBH")
            ->setLastModifiedBy("QLBH")
            ->setTitle("Email List")
            ->setSubject("Email List")
            ->setDescription("Email List")
            ->setKeywords("office PHPExcel php")
            ->setCategory("Test result file");
        // set value for header
        $i=1;
        $objWorkSheet = $spreadsheet->getActiveSheet();
        foreach($colomns as $value){
            $objWorkSheet->setCellValue(''.$letter_arr[$i].'1', $value);
            $objWorkSheet->getColumnDimension($letter_arr[$i])->setWidth(10);
            $i++;
        }
        $i=2;
        $cond = ' products.group_id='.$group_id;
        require_once ('packages/vissale/modules/AdminOrders/db.php');
        $items = AdminOrdersDB::get_products($cond);
        $ex_items = [];
        foreach($items as $key=>$value){
            $j = 1;
            foreach($value_colomns as $k=>$v){
                $val = str_replace(array('<br>',"="),array(",",""),$value[$v]);
                if($v=='mobile'){
                    $val = ' '.$val;
                }
                $ex_items[$key][$j] = $val;
                $j++;
            }
        }
        foreach($ex_items as $key=>$value){
            $j = 1;
            foreach($colomns as $v){
                $objWorkSheet->setCellValue(($letter_arr[$j].$i), $value[$j]);
                $j++;
            }
            $i++;
        }
        $subfix = 'product_'.$group_id.'_'.self::$account_id;
        $file = ''.$subfix.'_'.'emails.xlsx';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file.'"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        ob_clean();
        $objWriter->save('php://output');
        exit;
    }

    public static function validate_product_weight($weight)
    {
        $message = '';

        if (!$weight) {
            $message = 'Bắt buộc! Trọng lượng phải là giá trị dương';
        } else {
            $weight = str_replace(',', '', $weight);
            if (intval($weight) > 99000000) $message = 'Trọng lượng không quá 99.000.000 gam';
        }

        return $message;
    }
}
