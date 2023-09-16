<?php
class QlbhStockInvoice extends Module
{
    public static $item = array();
    public static $quyen_quan_ly_kho;
    public static $quyen_xuat_kho;
    public static $quyen_indon;
    public static $date_init_value;
    public static $group_id;
    public static $account_id;
    public static $user_id;
    function __construct($row)
    {
        Module::Module($row);
        require_once('packages/vissale/lib/php/vissale.php');

        self::$group_id = Session::get('group_id');
        self::$account_id = Session::get('user_id');
        self::$user_id = get_user_id();
        if(System::is_local()){
            self::$date_init_value = 'NULL';//'0000-00-00 00:00:00';
        }else{
            self::$date_init_value = '0000-00-00 00:00:00';
        }
        self::$quyen_quan_ly_kho = check_user_privilege('ADMIN_KHO');
        self::$quyen_xuat_kho = check_user_privilege('XUATKHO');
        self::$quyen_indon = check_user_privilege('IN_DON');
        if(Url::get('page') == 'qlbh_xuat_kho' and User::is_login()){
            $_REQUEST['type'] = 'EXPORT';
        }
        if(!Url::get('type')){
            Url::redirect_current(array('type'=>'IMPORT'));
        }
        require_once('packages/vissale/lib/php/qlbh.php');
        require_once('db.php');
        if(Url::get('from_order_ids')){
            if($invoice_id = QlbhStockInvoiceDB::xuat_kho(Url::get('from_order_ids'),Url::get('warehouse_id'))){
                Url::redirect_current(array('cmd'=>'view','id'=>$invoice_id,'type'=>'EXPORT'));
            }
        }
        switch (Url::get('cmd')){
            case 'get_suggest_product':
                $this->get_suggest_product();
                break;
            case 'view':
                if((check_user_privilege('XUATKHO') or QlbhStockInvoice::$quyen_quan_ly_kho) and Url::iget('id') and QlbhStockInvoice::$item = DB::select('qlbh_stock_invoice','id ='.Url::iget('id').' and group_id='.Session::get('group_id'))){
                    require_once 'forms/view.php';
                    $this->add_form(new ViewQlbhStockInvoiceForm());
                }else{
                    Url::access_denied();
                }
                break;
            case 'add':
                if(QlbhStockInvoice::$quyen_quan_ly_kho){
                    require_once 'forms/edit.php';
                    $this->add_form(new EditQlbhStockInvoiceForm());
                }else{
                    Url::access_denied();
                }
                break;
            case 'edit':
                if(QlbhStockInvoice::$quyen_quan_ly_kho and Url::get('id') and QlbhStockInvoice::$item = DB::select('qlbh_stock_invoice','id ='.Url::iget('id'))){
                    require_once 'forms/edit.php';
                    $this->add_form(new EditQlbhStockInvoiceForm());
                }else{
                    Url::access_denied();
                }
                break;
            
            case 'preview':
                require_once 'forms/preview.php';
                $this->add_form(new PreViewQlbhStockInvoiceForm());
                break;

            case 'pdf':
                require_once 'forms/pdf.php';
                $this->add_form(new PDFQlbhStockInvoiceForm());
                break;

            case 'delete':
                if(QlbhStockInvoice::$quyen_quan_ly_kho){
                    if(Url::get('id') and DB::exists('SELECT id FROM qlbh_stock_invoice WHERE id = '.Url::iget('id').' and group_id='.Session::get('group_id').'')){
                        DB::delete('qlbh_stock_invoice_detail','invoice_id= '.Url::iget('id'));
                        DB::delete('qlbh_stock_invoice','id= '.Url::iget('id'));
                        //System::log('');
                        Url::js_redirect(true,'Xoá thành công');
                    }
                    if(Url::get('item_check_box')){
                        $arr = Url::get('item_check_box');
                        for($i=0;$i<sizeof($arr);$i++){
                            DB::delete('qlbh_stock_invoice','id = '.DB::escape($arr[$i]));
                            DB::delete('qlbh_stock_invoice_detail','invoice_id= '.DB::escape($arr[$i]));
                        }
                        Url::redirect_current();
                    }else{
                        Url::redirect_current();
                    }
                }else{
                    //
                    echo 'Không có quyền truy cập!';
                    exit();
                }
                break;
            case 'export_excel_import':
                    $this->export_excel_import();
                    break;
            default:
                if(Session::get('group_id')){
                    require_once 'forms/list.php';
                    $this->add_form(new ListQlbhStockInvoiceForm());
                }else{
                    Url::access_denied();
                }
                break;
        }
    }
    function get_suggest_product(){
        $name = DB::escape(Url::get('term'));
        if(strlen($name)>1){
            $sql = '
			  SELECT products.id,products.code,products.name,products.name as label FROM products WHERE (products.name LIKE "%'.$name.'%" or products.code LIKE "%'.$name.'%") and products.group_id='.Session::get('group_id').' AND (products.del = 0 OR products.del IS NULL)
		    ';
            if($items = DB::fetch_all($sql)){
                echo json_encode($items);
            }else{
                echo json_encode([]);
            }
        }else{
            echo json_encode([]);
        }
    }

    private function export_excel_import(){
        $cond = 'qlbh_stock_invoice.group_id= '.Session::get('group_id').'
            '.((!User::can_admin() and Session::is_set('warehouse_id'))?' AND qlbh_stock_invoice_detail.warehouse_id = \''.DB::escape(Session::get('warehouse_id')).'\'':'').'
            '.(Url::get('type')?' AND qlbh_stock_invoice.type = \''.DB::escape(Url::sget('type')).'\'':'').'
            '.(Url::get('user_id') && Url::get('type') == 'EXPORT'?' AND qlbh_stock_invoice.user_id = \''.DB::escape(Url::sget('user_id')).'\'':'').'
            '.(Url::get('note')?' AND UPPER(qlbh_stock_invoice.note) LIKE \'%'.DB::escape(strtoupper(Url::sget('note'))).'%\'':'').'
            '.(Url::get('receiver_name')?' AND qlbh_stock_invoice.receiver_name LIKE \'%'.DB::escape(Url::sget('receiver_name')).'%\'':'').'
            '.(Url::get('create_date_from')?' AND qlbh_stock_invoice.create_date >= \''.DB::escape(Date_Time::to_sql_date(Url::sget('create_date_from'))).'\'':'').'
            '.(Url::get('create_date_to')?' AND qlbh_stock_invoice.create_date <= \''.DB::escape(Date_Time::to_sql_date(Url::sget('create_date_to'))).'\'':'').'
            '.(Url::get('warehouse_id')?' AND qlbh_stock_invoice_detail.warehouse_id = '.DB::escape(Url::iget('warehouse_id')).'':'').'
            '.(Url::get('supplier_id')?' AND qlbh_stock_invoice.supplier_id = '.DB::escape(Url::iget('supplier_id')).'':'').'
            ';
            $billNumber = trim(Url::get('bill_number'));
            $subBillNumber = substr($billNumber,0,2);
            if (strtoupper($subBillNumber) == 'PX' || strtoupper($subBillNumber) == 'PN') {
                $billNumber = substr($billNumber,2);
            }
            $andBillNumber = '';
            if (strlen($billNumber) == 0) {
                $andBillNumber = '';
            } else {
                $andBillNumber = ' AND qlbh_stock_invoice.bill_number LIKE  "'.DB::escape($billNumber).'%"';
            }
            $cond .= $andBillNumber;
            $sql = '
                SELECT
                    qlbh_stock_invoice.*
                FROM
                    qlbh_stock_invoice
                    LEFT JOIN qlbh_stock_invoice_detail ON qlbh_stock_invoice_detail.invoice_id = qlbh_stock_invoice.id
                WHERE
                    '.$cond.'
                GROUP BY bill_number
                ORDER BY
                    qlbh_stock_invoice.create_date DESC,qlbh_stock_invoice.id DESC
        ';
        $items = DB::fetch_all($sql);
        $i = 1;
        $suppliers = DB::select_all('qlbh_supplier','group_id='.DB::escape(Session::get('group_id')).'');
        $customers = DB::select_all('qlbh_warehouse','group_id=0 or group_id='.DB::escape(Session::get('group_id')).'','structure_id');
        $shops = QlbhStockInvoiceDB::get_shop(Session::get('user_id'));
        foreach($items as $key=>$value){
            $items[$key]['bill_number'] = ($value['type']=='IMPORT'?'PN':'PX').$value['bill_number'];
            $index = $i;
            $items[$key]['i'] = $index;
            if ($value['type'] == 'IMPORT' && !empty($value['order_id'])) {
                $items[$key]['note'] = 'Từ ' . sizeof(explode(',',$value['order_id'])) . ' đơn hàng đã trả hàng về kho (Mã: ' . str_replace(',', ', ', $value['order_id']) . ')';
            }
            if ($value['type'] == 'EXPORT' && !empty($value['order_id']) && !empty($value['bill_number'])) {
                $items[$key]['note'] = 'Từ ' . sizeof(explode(',',$value['order_id'])) . ' đơn hàng (Mã: ' . str_replace(',', ', ', $value['order_id']) . ')';
            }

            $items[$key]['create_date'] = Date_Time::to_common_date($value['create_date']);
            if(isset($suppliers[$value['supplier_id']])){
                $items[$key]['supplier_name'] = $suppliers[$value['supplier_id']]['name'];
            }else{
                $items[$key]['supplier_name'] = '';
            }
            if(isset($shops[$value['shop_id']])){
                $items[$key]['shop_name'] = $shops[$value['shop_id']]['name'];
            }else{
                $items[$key]['shop_name'] = '';
            }
            if(isset($customers[$value['warehouse_id']])){
                $items[$key]['warehouse_name'] = $customers[$value['warehouse_id']]['name'];
            }else{
                $items[$key]['warehouse_name'] = '';
            }
            $i++;

        }
        $array = [];
        if(Url::get('type') == 'EXPORT'){ 
            $array[] = [
                'STT'=>'STT',
                'NGAY_TAO'=>'Ngày tạo',
                'SO_PHIEU'=>'Số phiếu',
                'NGUOI_XUAT'=>'Người xuất',
                'NGUOI_NHAN'=>'Người nhận',
                'NOTE'=>'Diễn giải',
                'TONG_TIEN'=>'Tổng tiền',
            ];
            foreach ($items as $key => $value) {
                $array[] = [
                    'STT'=>$value['i'],
                    'NGAY_TAO'=>$value['create_date'],
                    'SO_PHIEU'=>$value['bill_number'],
                    'NGUOI_XUAT'=>$value['deliver_name'],
                    'NGUOI_NHAN'=>$value['receiver_name'],
                    'NOTE'=>$value['note'],
                    'TONG_TIEN'=>$value['total_amount'],
                ];
            }
        } else {
            $array[] = [
                'STT'=>'STT',
                'NGAY_TAO'=>'Ngày tạo',
                'SO_PHIEU'=>'Số phiếu',
                'NGUOI_XUAT'=>'Người xuất',
                'NGUOI_NHAN'=>'Người nhận',
                'NOTE'=>'Diễn giải',
                'NCC'=>'Nhà cung cấp',
                // 'TONG_TIEN'=>'Tổng tiền',
            ];
            foreach ($items as $key => $value) {
            $array[] = [
                'STT'=>$value['i'],
                'NGAY_TAO'=>$value['create_date'],
                'SO_PHIEU'=>$value['bill_number'],
                'NGUOI_XUAT'=>$value['deliver_name'],
                'NGUOI_NHAN'=>$value['receiver_name'],
                'NOTE'=>$value['note'],
                'NCC'=>$value['supplier_name'],
                // 'TONG_TIEN'=>$value['total_amount'],
            ];
        }
        }
        
        
        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator("BIG")
            ->setLastModifiedBy("BIG")
            ->setTitle("Xuất excel danh sách phiếu kho")
            ->setSubject("Xuất excel danh sách phiếu kho");
        $sheet = $spreadsheet->getActiveSheet()->fromArray($array);
        $objWriter = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        if (ob_get_contents()) {
            ob_end_clean();
        };
        header('Content-Type: application/vnd.ms-excel');
        if(Url::get('type') == 'EXPORT'){
            header('Content-Disposition: attachment;filename="xuat-excel-danh-sach-phieu-xuat-kho' . $_SESSION['user_id'] . '.xlsx"');
        } else {
            header('Content-Disposition: attachment;filename="xuat-excel-danh-sach-phieu-nhap-kho' . $_SESSION['user_id'] . '.xlsx"');
        }
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
?>
