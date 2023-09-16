<?php

require_once ROOT_PATH . 'packages/core/includes/common/BiggameAPI.php';

class ListImportMaBuuDienForm extends Form{
    public static $available_total = 0;
    protected $map;
    function __construct(){
        Form::Form('ListImportMaBuuDienForm');
    }
    function on_submit(){
        if(Url::get('upload')){
            if($_FILES['excel_file']['name'] == ''){
                Url::js_redirect(true,'Bạn vui lòng tải file cần import');
            }
        }
        if(isset($_FILES['excel_file']) and $excel_file = $_FILES['excel_file'] and $tmp_name=$excel_file['tmp_name']){
            $exel_items = read_excel($tmp_name);
            $_SESSION['exel_items'] = $exel_items;
            Session::delete('mbd_import_excel_fail_rows');
            Session::delete('mbd_import_excel_exist_code');
            Session::delete('mbd_import_excel_fail_cells');
            Session::delete('mbd_import_excel_fail_mdb_data');
            Session::delete('mbd_import_excel_success_rows');
            Session::delete('mbd_import_excel_success_link');
            Session::set('mbd_import_excel_fail_header', $exel_items[1]);
            $mbd_import_excel_fail_header = $exel_items[1];
            $mbd_import_excel_fail_header[9] = 'NOTE';
            Session::set('mbd_import_excel_fail_status_header', $mbd_import_excel_fail_header);
            Url::js_redirect(true,'Bạn đã tải file lên, vui lòng chọn bước tiếp theo!');
        }else{
            if(isset($_SESSION['exel_items']) and $arr=$_SESSION['exel_items']){
                $cond_ = 'orders.group_id='.Session::get('group_id').'';
                if(Session::get('master_group_id')){
                    $cond_ = ' (orders.group_id='.Session::get('group_id').' or orders.master_group_id='.Session::get('master_group_id').')';
                }
                $data = $this->processData($arr, $cond_);
                $ids = $data['ids'];
                $keyIds = $data['keyIds'];
                $idsNhapKho = $data['idsNhapKho'];
                $idsFail = $data['idsFail'];
                $idsFailPosttal =  $data['idsFailPosttal'];
                $idsConfirmed = $data['idsConfirmed'];
                $idsDelivered = $data['idsDelivered'];
                $idsNotImportMBD = array_keys($data['idsNotImportMBD']);
                $idsFailStatus = $data['idsFailStatus'];
                $idsNotImportOrderId = array_keys($data['idsNotImportOrderId']);

                if(count($idsFailPosttal) > 0){
                    // Session::set('mbd_import_excel_fail_header', $arr[0] );
                    Session::set('mbd_import_excel_fail_rows',array_intersect_key($arr,array_flip($idsFail)));
                }

                if(count($ids) > 0){
                    $strIds = implode(',',$ids);
                    $row = $this->getBaseUrl().$strIds;

                    Session::set('mbd_import_excel_success_rows',$ids);
                    Session::set('mbd_import_excel_success_link',$row);
                }

                if(count($data['idsNotImportMBD']) > 0){
                    $dataFailSMBD = [];
                    foreach ($arr as $key=> $value) {
                        if($data['idsNotImportMBD'][$key]){
                            $dataFailSMBD[$key] = $value;
                            $dataFailSMBD[$key][9] = $data['idsNotImportMBD'][$key][9];
                        }
                    }
                    Session::set('mbd_import_excel_fail_mdb_data',$dataFailSMBD);
                }
                if(isset($_SESSION['exel_items']) and empty($_SESSION['exel_items'])){
                    unset($_SESSION['exel_items']);
                }
                // CHUYEN_HOAN or TRA_VE_KHO
                $create_import_invoice_when_return = get_group_options('create_import_invoice_when_return');
                if($create_import_invoice_when_return == true and $idsNhapKho){
                    $strIdsNhaKho = implode(',', $idsNhapKho);
                    ImportMaBuuDienDB::nhap_kho($strIdsNhaKho);
                }
                $admin_orders = 'admin_orders';
                $messages = [];
                if($ids){
                    $messages[] = 'Bạn đã import '.sizeof($ids).' dòng thành công : '.implode(', ',$keyIds).'.';
                }
                if($idsFail){
                    $messages[] = 'Import không thành công '.sizeof($idsFail).' dòng : '.implode(', ',$idsFail).'.';
                }
                if($idsConfirmed){
                    $messages[] = 'Các dòng chưa trải qua xác nhận chốt đơn: '.implode(', ',$idsConfirmed).'.';
                }
                if($idsDelivered){
                    $messages[] = 'Import không thành công '. sizeof($idsDelivered) .' dòng. Các dòng lỗi chưa trải qua trạng thái Chuyển hàng: '.implode(', ',$idsDelivered).'.';
                }
                if($idsNotImportMBD){
                    $messages[] = 'Các dòng lỗi khi import chuyển trạng thái: '.implode(', ',$idsNotImportMBD).'.';
                }
                if($idsNotImportOrderId){
                    $messages[] = 'Các dòng chưa import mã đơn hàng : '.implode(', ',$idsNotImportOrderId).'.';
                }
                if($ids){
                    if(count($idsFail) > 0 || count($idsNotImportMBD) > 0 || count($idsNotImportOrderId) > 0){
                        Url::js_redirect(true,implode('\r\n', $messages));
                    }else{
                        Session::delete('mbd_import_excel_success_rows');
                        Url::js_redirect($admin_orders,implode('\r\n', $messages),array('ids'=> implode(',', $ids)));
                    }
                } else {
                    Url::js_redirect(true,implode('\r\n', $messages));
                }
            }
        }
    }

    function draw(){
        $this->map = array();
        $this->map['items'] = array();
        $this->map['total'] = 0;
        if(isset($_SESSION['exel_items'])){
            $items = $_SESSION['exel_items'];
            $this->map['total'] = (sizeof($_SESSION['exel_items']) - 1 > 0)?sizeof($_SESSION['exel_items']) - 1:0;
            foreach($items as $key=>$val){
                $total_price = 'Tổng trên đơn';
                if($key>1){
                    $items[$key][6] = System::calculate_number($val[6]);
                    $items[$key][7] = System::calculate_number($val[7]);
                    $items[$key][8] = System::calculate_number($val[8]);
                    $total_price = 0;
                    $cond_ = 'group_id='.Session::get('group_id').'';
                    if(Session::get('master_group_id')){
                        $cond_ = ' (group_id='.Session::get('group_id').' or master_group_id='.Session::get('master_group_id').')';
                    }
                    if($order_id = intval($val[3]) and $order=DB::fetch('select id,total_price from orders where id='.$order_id.' and '.$cond_)){
                        $total_price = $order['total_price'];
                    }else{
                        $items[$key][3] = $val[3].'  <span style="font-size:11px;color:#F00;">(Không tồn tại)</span>';
                    }
                }
                $items[$key]['total_price'] = $total_price;
            }
            $this->map['items'] = $items;
        }
        $layout = 'list';
        $this->map['available_total'] = ListImportMaBuuDienForm::$available_total;
        $this->parse_layout($layout,$this->map);
    }
    function checkSubmitForm()
    {
        if(Url::get('import_thu_tien') || Url::get('import_thanh_cong') || Url::get('import_chuyen_hoan') || Url::get('import_tra_hang_ve_kho')){
            return "STATUS";
        } else {
            return "CODE";
        }
    }



    function processData($arr,$cond_)
    {
        $arrCode = [];
        $arrStatusCode = [];

        $list_post_code = array_map(function ($item) {
            return DB::escape($item[2]);
        },$arr);
        unset($list_post_code[0]);
        $sql = "SELECT 
            id, postal_code
        FROM 
            orders 
        WHERE postal_code in ('".implode("','",array_unique($list_post_code))."')
        AND $cond_";
        $check_postal = DB::fetch_all($sql);

        if(count($check_postal) > 0){
            $exists_postal_code = array_values(array_map(function ($item) { return $item['postal_code']; }, $check_postal));
            Session::set('mbd_import_excel_exist_code', $exists_postal_code);
        }else{
            Session::delete('mbd_import_excel_exist_code');
        }
        foreach ($arr as $k => $val) {
            if($k > 1){
                $random = 1234567890;
                $shipping_price = ($val[8]!='')?trim($val[8]):0;
                $total_price  = ($val[7]!=='')?trim($val[7]):"";//truong de so sanh gia
                $total_price = System::calculate_number($total_price);
                $code = ($val[3]!='')?trim($val[3]):"";
                $postal_code = ($val[2]!='')?trim($val[2]):"";
                $price_ = DB::escape($val[7]);
                $shipping_ = DB::escape($val[8]);
                $postal_code = DB::escape($postal_code);
                $shipping_price = DB::escape($shipping_price);
                $price_ = $price_ === "0"? 0: DataFilter::removeXSSinHtml($price_);
                $shipping_ = DataFilter::removeXSSinHtml($shipping_);
                $shipping_price = DataFilter::removeXSSinHtml($shipping_price);
                $code = DB::escape($code);
                $code = DataFilter::removeXSSinHtml($code);
                $total_price = $total_price == '0'? 0: DataFilter::removeXSSinHtml($total_price);
                $postal_code = $postal_code ? DataFilter::removeXSSinHtml($postal_code) :'';
                $arrCode[$k] = [
                    'key'=>$k,
                    'code'=>$code,
                    'total_price'=>$total_price !== "" ? intval($total_price) : "",
                    'shipping_price'=>$shipping_price != "" ? intval($shipping_price) : 0,
                    'postal_code'=>$postal_code,
                    'price_'=>$price_,
                    'shipping_'=>intval($shipping_)
                ];
                $arrStatusCode[$k] = [
                    'key'=>$k,
                    'code'=>$code,
                    'code_check'=>$val[3],
                    'total_price'=>$total_price !== "" ? intval($total_price) : "",
                    'shipping_price'=>$shipping_price != "" ? intval($shipping_price) : 0,
                    'postal_code'=>$postal_code,
                    'price_'=>$price_,
                    'shipping_'=>intval($shipping_)
                ];
            }
        }
        $checkSubmitForm = $this->checkSubmitForm();
        $data = [];
        if($checkSubmitForm == "STATUS"){
            $data = $this->changeStatus($arrStatusCode, $cond_);
        } else {
            $data = $this->importPostalCode($arrCode, $cond_);
        }
        return $data;
    }

    function getBaseUrl() 
    {
        $currentPath = $_SERVER['PHP_SELF']; 
        $pathInfo = pathinfo($currentPath); 
        $hostName = $_SERVER['HTTP_HOST']; 
        $protocol = System::getProtocol();
        return $protocol.'://'.$hostName.$pathInfo['dirname']."index062019.php?page=admin_orders&ids=";
    }

    function importPostalCode($arrCode, $cond_)
    {
        require_once('packages/vissale/modules/AdminOrders/db.php');
        $add_deliver_order = get_group_options('add_deliver_order');
        $ids = [];
        $keyIds = [];
        $idsFail = [];
        $idsNhapKho = [];
        $idsConfirmed = [];
        $idsNotImportMBD = [];
        $orderId = [];
        foreach($arrCode as $ac){
            if( $ac['code'] != "" && intval($ac['code']))
            $orderId[] = intval($ac['code']);
        }
        $arrCountIds = array_count_values($orderId);
        $strCode = implode(',',$orderId);
        $orderCode = [];

        // Biggame
        $ordersChanged = [];

        $errorCells = [];

        $sql = "SELECT 
                    orders.id,
                    orders.status_id,
                    orders.total_price,
                    orders.group_id,
                    orders.postal_code,
                    orders.shipping_price,
                    orders.confirmed,
                    orders.user_confirmed,
                    statuses.level as status_level,
                    statuses.no_revenue as status_no_revenue,
                    orders_extra.accounting_confirmed
                FROM 
                    orders 
                LEFT JOIN orders_extra ON orders_extra.order_id = orders.id
                LEFT JOIN statuses ON orders.status_id = statuses.id 
                WHERE 
                    orders.id IN ($strCode) 
                AND $cond_";

        $orders = DB::fetch_all($sql);
        $rowIndex = 1;
        foreach($orders as $key => $order){
            $orderCode[] = intval($order['id']);
        }

        $keyFail = [];
        $keyNotFails = [];

        foreach ($arrCode as $key => $ac) {
            $isFail = false;
            if(!in_array($ac['code'], $orderCode)
                || in_array($arrCode[$key]['postal_code'], Session::get('mbd_import_excel_exist_code'))
                || $arrCode[$key]['total_price'] === ""
                || $arrCode[$key]['postal_code'] === ""
            ) {
                $keyFail[] = $ac['key'];
                $isFail = true;
                $idsFail[] = $ac['key'];
            }


            foreach ($orders as $key => $order) {
                $order_id = $order['id'];

                if(intval($order_id) === intval($ac['code']) && (intval($ac['total_price']) !== intval($order['total_price']))) {
                    $keyFail[] = $ac['key'];
                    $idsFail[] = $ac['key'];
                    $isFail = true;
                }
            }

            if(!$isFail) $keyNotFails[intval($ac['code'])] = $ac['key'];
        }
        if(count($keyNotFails)){
            foreach($orders as $order){
                if(isset($keyNotFails[$order['id']])){
                    $totalPrice = $order['total_price'];
                    if (empty($order['total_price'])) {
                        $totalPrice = 0;
                    }
                    $key = $keyNotFails[$order['id']];

                    $array = [
                        'shipping_price'=>$arrCode[$key]['shipping_price'] === ""? 0 : $arrCode[$key]['shipping_price'],
                        'postal_code'=>$arrCode[$key]['postal_code'],
                    ];
                    if($add_deliver_order == 1){
                        $array['total_price'] = $array['shipping_price'] + $arrCode[$key]['total_price'];
                    }

                    DB::update('orders',$array,'id="'.$order['id'].'"');
                    $ids[] = intval($order['id']);
                    $keyIds[] = $arrCode[$key]['key'];
                    // unset($_SESSION['exel_items'][$arrCode[$key]['key']]);
                    $data = '';
                    $data .= $this->getDataLog($arrCode, $order, $key);
                    if($data){
                        AdminOrdersDB::update_revision($order['id'],false,false,$data);
                    }
                }
            }
        }


        $fail = [];
        if($keyFail){
            $fail = array_unique(array_merge($keyFail, $idsFail));
        } else {
            $fail = $idsFail;
        }
        if(count($fail)> 0){
            $index = 1;
            foreach ($fail as $key => $id_fail) {
                $value= $arrCode[$id_fail];
                if($value['code'] == "" ||!in_array($value['code'],$orderCode)) $errorCells[$index][] = 3;
                // if($arrCountIds[$arrCode[$id_fail]['code']] >= 2)$errorCells[$index][] = 3;
                if(in_array($value['postal_code'], Session::get('mbd_import_excel_exist_code'))) $errorCells[$index][] = 2;
                if($value['total_price'] === "") $errorCells[$index][] = 7;
                foreach($orders as $order){
                    $order_id = intval($order['id']);
                    if($order_id == intval($value['code']) && (intval($value['total_price']) !== intval($order['total_price']))) $errorCells[$index][] = 7;
                }
                if($value['postal_code'] == "") $errorCells[$index][] = 2;
                if(intval($value['shipping_price'])  < 0) $errorCells[$index][] = 8;
                $index++;
            }
        }

        Session::set('mbd_import_excel_fail_cells',$errorCells );
        $datas['ids'] = $ids;
        $datas['keyIds'] = $keyIds;
        $datas['idsNhapKho'] = $idsNhapKho;
        $datas['idsFail'] =  $fail;
        $datas['idsFailPosttal'] =  $fail;
        $datas['idsConfirmed'] = $idsConfirmed;
        $datas['idsNotImportMBD'] = $idsNotImportMBD;

        // gửi thông tin sang biggame
        if($ordersChanged = $this->filterCarts($ordersChanged)){
            BiggameAPI::instance()->sendListCarts($ordersChanged);
        }

        return $datas;
    }

    function changeStatus($arrStatusCode, $cond_)
    {
        require_once('packages/vissale/modules/AdminOrders/db.php');
        $add_deliver_order = get_group_options('add_deliver_order');
        $ids = [];
        $keyIds = [];
        $idsFail = [];
        $idsFailStatus = [];
        $idsNhapKho = [];
        $idsConfirmed = [];
        $idsDelivered = [];
        $idsNotImportMBD = [];
        $idsNotImportOrderId = [];
        $statusId = [];
        $postalCode = [];
        $postalCodeCheck = [];
        $orderPostalRequest = [];
        $ordersChanged = [];
        foreach ($arrStatusCode as $key=> $value) {
            $postalCode[] = $value['postal_code'];
            if($value['postal_code'] != ''){
                $postalCodeCheck[$value['postal_code']] = $value;
                $orderPostalRequest[] = $value['postal_code'];
            }
        }
        $strPostalCode = "('" . implode("','", $postalCode) . "')";
        $sql = "SELECT 
                    orders.id, orders.status_id, orders.total_price,
                    orders.group_id, orders.postal_code, orders.shipping_price,
                    orders.confirmed, orders.user_confirmed, orders.user_delivered,
                    orders_extra.update_successed_user, orders_extra.update_successed_time,
                    orders_extra.update_paid_user, orders_extra.update_paid_time,
                    orders_extra.update_returned_user, orders_extra.update_returned_time,
                    orders_extra.update_returned_to_warehouse_user_id, orders_extra.update_returned_to_warehouse_time
                FROM 
                    orders 
                LEFT JOIN orders_extra ON orders.id = orders_extra.order_id
                WHERE 
                    orders.postal_code IN $strPostalCode 
                AND $cond_";
        $orders = DB::fetch_all($sql);
        $orderPostal = [];
        foreach ($orders as $k => $v) {
            $statusId[] = $v['status_id'];
            $orderPostal[] = $v['postal_code'];        
        }
        $diff = array_diff($orderPostalRequest, $orderPostal);
        
        foreach ($arrStatusCode as $key => $value) {
            if(in_array($value['postal_code'], $diff)){
                $idsNotImportMBD[$key][9] = 'Đơn chưa import mã bưu điện';
                $idsFail[] = $key;
            }else if($value['postal_code'] == ''){
                $idsNotImportMBD[$key][9] = 'Đơn chưa import mã bưu điện';
                $idsFail[] = $key;
            }
        }
        $strStatusId = implode(',', array_unique($statusId));
        $statuses = DB::fetch_all("SELECT id,name FROM statuses WHERE id IN ($strStatusId)");
        $userId = get_user_id();
        foreach ($orders as $key => $order) {
            $data = '';
            $flag = false;
            $array = [];
            $rowExtra = [];
            if(!empty($order['user_confirmed']) && !empty($order['user_delivered'])){
                if(Url::get('import_thu_tien') and $order['status_id']!=DA_THU_TIEN and $order['status_id'] == THANH_CONG and !empty($order['postal_code'])){
                    $array += array(
                        'status_id'=>DA_THU_TIEN
                    );
                    if($order['status_id'] != DA_THU_TIEN && isset($statuses[$order['status_id']])){
                        $data .= 'Import mã bưu điện: Chuyển trạng thái đơn hàng từ <b>'.$statuses[$order['status_id']]['name'].'</b> sang <b>Đã thu tiền</b><br>';
                    }
                    $flag = true;
                    if (empty($order['update_paid_user']) || empty($order['update_paid_time'])) {
                        $rowExtra = [
                            'update_paid_user' => $userId,
                            'update_paid_time' => date('Y-m-d H:i:s')
                        ];
                    }
                } else if (Url::get('import_thanh_cong') and $order['status_id']!=THANH_CONG and $order['status_id']!=CHUYEN_HOAN and $order['status_id']!=TRA_VE_KHO and !empty($order['postal_code'])) {
                    $array += array(
                        'status_id'=>THANH_CONG
                    );
                    if($order['status_id'] != THANH_CONG && isset($statuses[$order['status_id']])){
                        $data .= 'Import mã bưu điện: Chuyển trạng thái đơn hàng từ <b>'.$statuses[$order['status_id']]['name'].'</b> sang <b>Thành công</b><br>';
                    }
                    $flag = true;
                    if (empty($order['update_successed_user']) || empty($order['update_successed_time'])) {
                        $rowExtra = [
                            'update_successed_user' => $userId,
                            'update_successed_time' => date('Y-m-d H:i:s')
                        ];
                    }
                } else if(Url::get('import_chuyen_hoan') and $order['status_id']!=THANH_CONG and $order['status_id']!=CHUYEN_HOAN and $order['status_id']!=DA_THU_TIEN and !empty($order['postal_code'])){
                    $array += array(
                        'status_id'=>CHUYEN_HOAN
                    );
                    if($order['status_id'] != CHUYEN_HOAN && isset($statuses[$order['status_id']])){
                        $data .= 'Import mã bưu điện: Chuyển trạng thái đơn hàng từ <b>'.$statuses[$order['status_id']]['name'].'</b> sang <b>Chuyển hoàn</b><br>';
                    }
                    $flag = true;
                    if (empty($order['update_returned_user']) || empty($order['update_returned_time'])) {
                        $rowExtra = [
                            'update_returned_user' => $userId,
                            'update_returned_time' => date('Y-m-d H:i:s')
                        ];
                    }
                } else if(Url::get('import_tra_hang_ve_kho') and $order['status_id']!=THANH_CONG and $order['status_id']!=TRA_VE_KHO and $order['status_id']!=DA_THU_TIEN and !empty($order['postal_code'])){
                    $array += array(
                        'status_id'=>TRA_VE_KHO
                    );
                    if($order['status_id'] != TRA_VE_KHO && isset($statuses[$order['status_id']])){
                        $data .= 'Import mã bưu điện: Chuyển trạng thái đơn hàng từ <b>'.$statuses[$order['status_id']]['name'].'</b> sang <b>Đã trả hàng về kho</b><br>';
                    }
                    $idsNhapKho[] = $order['id'];
                    $flag = true;
                    if (empty($order['update_returned_to_warehouse_user_id']) || empty($order['update_returned_to_warehouse_time'])) {
                        $rowExtra = [
                            'update_returned_to_warehouse_user_id' => $userId,
                            'update_returned_to_warehouse_time' => date('Y-m-d H:i:s')
                        ];
                    }
                } else {
                    $idsFail[] = $postalCodeCheck[$order['postal_code']]['key'];
                }
                if( $order['status_id'] == THANH_CONG && ( Url::get('import_thanh_cong') ||  Url::get('import_chuyen_hoan') || Url::get('import_tra_hang_ve_kho') ) ){
                    $idsNotImportMBD[$postalCodeCheck[$order['postal_code']]['key']][9] = 'Đơn hàng đang ở trạng thái Thành công';
                } else if($order['status_id'] == DA_THU_TIEN && ( Url::get('import_thu_tien') ||  Url::get('import_chuyen_hoan') || Url::get('import_tra_hang_ve_kho') )){
                    $idsNotImportMBD[$postalCodeCheck[$order['postal_code']]['key']][9] = 'Đơn hàng đang ở trạng thái Đã thu tiền';
                } else if($order['status_id'] == CHUYEN_HOAN && ( Url::get('import_thu_tien') ||  Url::get('import_chuyen_hoan') || Url::get('import_thanh_cong') ) ){
                    $idsNotImportMBD[$postalCodeCheck[$order['postal_code']]['key']][9] = 'Đơn hàng đang ở trạng thái Chuyển hoàn';
                } else if($order['status_id'] == TRA_VE_KHO && ( Url::get('import_thu_tien') ||  Url::get('import_tra_hang_ve_kho') || Url::get('import_thanh_cong') ) ){
                    $idsNotImportMBD[$postalCodeCheck[$order['postal_code']]['key']][9] = 'Đơn hàng đang ở trạng thái Đã trả hàng';
                }
                if($flag == true){
                    $array['total_price'] = $postalCodeCheck[$order['postal_code']]['price_'] !='' ? $postalCodeCheck[$order['postal_code']]['price_'] : $order['total_price'];
                    $array['total_price'] = str_replace(',', '',$array['total_price']);
                    if($postalCodeCheck[$order['postal_code']]['shipping_'] != ''){
                        $ship_price = str_replace(',', '',$postalCodeCheck[$order['postal_code']]['shipping_']);
                        $array['shipping_price'] = intval($ship_price);
                    }
                    if($add_deliver_order == 1 && $postalCodeCheck[$order['postal_code']]['shipping_'] != ''){
                        $ship_price = str_replace(',', '',$postalCodeCheck[$order['postal_code']]['shipping_']);
                        $total_price = str_replace(',', '',$array['total_price']);
                        $array['total_price'] = intval($total_price) + intval($ship_price);
                        $postalCodeCheck[$order['postal_code']]['price_'] = $array['total_price'];
                    }
                    
                    $array['modified'] = date("Y-m-d H:i:s");
                    // Lưu lại thay đổi để gửi qua biggame
                    $dataBiggame = $order;
                    $dataBiggame['total_price'] = $array['total_price'];
                    $dataBiggame['status_id'] = $array['status_id'];
                    $ordersChanged[] =  $dataBiggame;
                    DB::update('orders',$array,'id="'.$order['id'].'"');
                    DB::update('orders_extra',$rowExtra,'order_id="'.$order['id'].'"');
                    $ids[] = $order['id'];
                    $keyIds[] = $postalCodeCheck[$order['postal_code']]['key'];
                    $data .= $this->getDataLog($postalCodeCheck, $order, $order['postal_code']);
                    if($data){
                        AdminOrdersDB::update_revision($order['id'],false,false,$data);
                    }
                    unset($_SESSION['exel_items'][$postalCodeCheck[$order['postal_code']]['key']]);
                }
                
            } else {
                
                $idsFail[] = $postalCodeCheck[$order['postal_code']]['key'];
                if(empty($order['user_confirmed'])){
                    $idsConfirmed[] = $postalCodeCheck[$order['postal_code']]['key'];
                    $idsNotImportMBD[$postalCodeCheck[$order['postal_code']]['key']][9] = 'Đơn hàng chưa trải qua trạng thái Xác nhận chốt đơn';
                } else if(empty($order['user_delivered'])){
                    $idsNotImportMBD[$postalCodeCheck[$order['postal_code']]['key']][9] = 'Đơn hàng chưa trải qua trạng thái Chuyển hàng';
                    $idsDelivered[] = $postalCodeCheck[$order['postal_code']]['key'];
                }
                
            }
        }
        Session::delete('mbd_import_excel_fail_rows');
        Session::delete('mbd_import_excel_fail_cells');
        $datas['ids'] = $ids;
        $datas['keyIds'] = $keyIds;
        $datas['idsNhapKho'] = $idsNhapKho;
        $datas['idsFail'] = array_filter($idsFail);
        $datas['idsFailStatus'] = $idsFailStatus;
        $datas['idsConfirmed'] = array_filter($idsConfirmed);
        $datas['idsDelivered'] = array_filter($idsDelivered);
        $datas['idsNotImportMBD'] = array_filter($idsNotImportMBD);
        $datas['idsNotImportOrderId'] = $idsNotImportOrderId;
        // gửi thông tin sang biggame
        if($ordersChanged = $this->filterCarts($ordersChanged)){
            BiggameAPI::instance()->sendListCarts($ordersChanged);
        }
        return $datas;
    }
    function getDataLog($data, $order, $key)
    {
        $log = '';
        $log .= $data[$key]['shipping_']!= '' && !empty($order['shipping_price']) && ($data[$key]['shipping_price'] != $order['shipping_price']) ? "Import mã bưu điện: Thay đổi phí vận chuyển từ ".$order['shipping_price'] ."=> ". $data[$key]['shipping_price'] ."<br>" :'';
        $log .= !empty($order['postal_code']) && ($data[$key]['postal_code'] != $order['postal_code']) ? "Import mã bưu điện: Thay đổi mã bưu điện từ ".$order['postal_code'] ."=> ". $data[$key]['postal_code'] ."<br>" :'';
        $log .= $data[$key]['price_'] !== ''  && ($data[$key]['price_'] != $order['total_price']) ? "Tổng tiền thay đổi từ ".$order['total_price'] ."=> ". $data[$key]['total_price'] ."<br>" :'';
        $log .= empty($order['shipping_price']) && $data[$key]['shipping_'] != '' ? "Import mã bưu điện: Thêm mới phí vận chuyển ".$data[$key]['shipping_price']."" ."<br>" :'';
        $log .= empty($order['postal_code']) && $data[$key]['postal_code'] != '' ? "Import mã bưu điện: Thêm mới mã bưu điện ".$data[$key]['postal_code']."" ."<br>" :'';
        return $log;
    }

    /**
     * { function_description }
     *
     * @param      array   $rows   The rows
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function filterCarts(array $rows)
    {
        return from($rows)

            // Lấy groupName và tên người xác nhận
            ->pipe(function($rows){
                $groupID = $rows[0]['group_id'] ?? 0;
                $userIDs = from($rows)->map(function($row){ return $row['user_confirmed']; })->unique();

                $sql = 'SELECT `name` FROM `groups` WHERE `id` = ' . $groupID;
                $groupName = DB::fetch($sql, 'name');

                $sql = 'SELECT `id`, `name` FROM `users` WHERE `id` IN (' . $userIDs->join(',') . ')';
                $users = DB::fetch_all_column($sql, 'name', 'id');

                return [$groupName, $users, $rows];
            })

            // Xây dựng danh sách
            ->pipe(function($results){
                [$groupName, $users, $rows] = $results;

                return array_map(function($row) use($users, $groupName) {
                    $statuses = DB::fetch('select id,no_revenue,level from statuses where id='.(int)$row['status_id']);
                    return [
                        "id"                    => $row['id'],
                        "confirmed"             => $row['confirmed'],
                        "user_confirmed"        => $row['user_confirmed'],
                        "user_confirmed_name"   => $users[$row['user_confirmed']] ?? '',
                        "accounting_confirmed"  => $row['accounting_confirmed'],
                        "total_price"           => $row['total_price'],
                        "group_id"              => $row['group_id'],
                        "group_name"            => $groupName,
                        "status_id"             => $row['status_id'],
                        "no_revenue"            => $statuses['no_revenue']??1,
                        "level"                 => $statuses['level']??0
                    ];
                }, $rows);
            })
            ->toArray();
    }
}
