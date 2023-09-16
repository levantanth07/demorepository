<?php
class AdminProducts extends Module{
    public static $group_id;
    public static $account_id;
    function export_excel(){
        $group_id = self::$group_id;
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
        require_once 'packages/core/includes/utils/PHPExcel/Classes/PHPExcel.php';
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("QLBH")
            ->setLastModifiedBy("QLBH")
            ->setTitle("Email List")
            ->setSubject("Email List")
            ->setDescription("Email List")
            ->setKeywords("office PHPExcel php")
            ->setCategory("Test result file");
        // set value for header
        $i=1;
        $objWorkSheet = $objPHPExcel->setActiveSheetIndex(0);
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
            // Add some data
            $objWorkSheet = $objPHPExcel->setActiveSheetIndex();
            $j = 1;
            foreach($colomns as $v){
                $objWorkSheet->setCellValue(($letter_arr[$j].$i), $value[$j]);
                $j++;
            }
            $i++;
        }
        // Rename worksheet
        //echo date('H:i:s') , " Rename worksheet" , EOL;
        //$objPHPExcel->getActiveSheet()->setTitle('Simple');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Save Excel 2007 file
        //  echo date('H:i:s') , " Write to Excel2007 format" , EOL;
        //$callStartTime = microtime(true);
        //echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;

        $subfix = 'product_'.$group_id.'_'.self::$account_id;
        $file = ''.$subfix.'_'.'emails.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file.'"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_clean();
        $objWriter->save('php://output');
        return;
    }
    function __construct($row){
        self::$group_id = Session::get('group_id');
        self::$account_id = Session::get('user_id');
        Module::Module($row);
        
        require_once ROOT_PATH . 'packages/vissale/modules/AdminProductsBuild/ProductHelper.php';
        require_once('packages/vissale/lib/php/vissale.php');
        require_once('packages/vissale/modules/AdminProducts/db.php');

        $master_group_id = Session::get('master_group_id');
        $account_type = Session::get('account_type');

        if($account_type==TONG_CONG_TY && check_user_privilege('ADMIN_KETOAN')){
            $canView = true;
        } elseif ($master_group_id) {
            $canView = false;
        } else {
            //auto
            if(check_user_privilege('ADMIN_KHO')){
                $canView = true;
            }else{
                $canView = false;
            }
        }
        if ( User::is_login()  ){
            $do = Url::get('do');
            if(Url::get('do')=='export_excel'){
                $this->export_excel();
                die;
            }
            if ($do == 'check_duplicated' && $canView) {
                require_once 'forms/ajax.php';
                $this->add_form(new EditAdminProductsAjax());
            } elseif ( $canView  ) {
                require_once 'forms/edit.php';
                $this->add_form(new EditAdminProductsForm());
            } else {
                Url::js_redirect('/', 'Bạn không có quyền truy cập tính năng này.');
            }
        } else {
            URL::access_denied();
        }
    }
}
?>