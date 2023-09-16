<?php
use Jenssegers\Agent\Agent;
use GuzzleHttp\Client;
require_once ROOT_PATH.'packages/core/includes/common/BiggameAPI.php';

class AdminOrdersDB
{
    const DUPLIDATE_TYPE_PHONE = 0; // chỉ trùng lặp sdt
    const DUPLIDATE_TYPE_PHONE_BUNDLE = 1; // trùng số dt và phân laoi sp

    const FILTER_DUPLIATE_NONE = 0;
    const FILTER_DUPLIATE_ONLY = 1;
    const FILTER_DUPLIATE_NOT = 2;

    const VIETTEL = [
        "086","096","097","098","0169","0168",
        "0167","0166","0165","0164","0163","0162","039","038",
        "037","036","035","034","033","032"
    ];
    const VINAPHONE = [
        "091","094","0128","0123","0124","0125","0127",
        "0129","088","083","084","085","081","082"
    ];
    const MOBIFONE = [
        "0120","0121","0122","0126","0128","089","090","093",
        "070","079","077","076","078","089","090","093"
    ];
    const VIETNAMOBILE = ["092","0182","0186","0188","052","056","058"];
    const GMOBILE = ["099","0199","059"];
    const ITELECOM = ["087"];
    const CARRIERS = [
        'VIETTEL' => self::VIETTEL,
        'VINAPHONE' => self::VINAPHONE,
        'MOBIFONE' => self::MOBIFONE,
        'VIETNAMOBILE' => self::VIETNAMOBILE,
        'GMOBILE' => self::GMOBILE,
        'ITELECOM' => self::ITELECOM,
    ];

    public static  $passphrase = '95c97403-cddb-4649-bbe1-a1c95d2237ec';

    public static  $cipher = 'AES-128-ECB';

    // public static function encode(array $value)
    // {
    //     return openssl_encrypt(base64_encode(json_encode($value)), self::$cipher, self::$passphrase);
    // }
    public static function encode(array $value): string
    {
        $value = (string)openssl_encrypt((string)json_encode($value), self::$cipher, self::$passphrase);

        return base64_encode($value);
    }
    public static function decode(string $key)
    {
        return openssl_decrypt($key, self::$cipher, self::$passphrase);
    }
    public static function isSettingMedidoc()
    {
        $key = 'form_medidoc';
        $groupID = Session::get('group_id');
        $row = DB::fetch('select id,value from group_options where `key`= "' . $key . '" and group_id = '. $groupID);
        if(empty(json_decode($row['value']))){
            return false;
        }
        return true;
    }
    static function get_account_group_new(){
        $cond = '';
        $group_id = AdminOrders::$group_id;
        if(AdminOrders::$is_owner || AdminOrders::$admin_group || AdminOrders::$quyen_bung_don || AdminOrders::$quyen_cskh || AdminOrders::$quyen_van_don || AdminOrders::$quyen_chia_don || AdminOrders::$quyen_admin_marketing){
            $cond = '';
        } else if(AdminOrders::$is_account_group_department && get_account_group_ids()){
            $cond.= ' AND account.account_group_id IN ('.get_account_group_ids().')';
        } else if($account_group_id=DB::fetch('select account_group_id from account where account.id="'.Session::get('user_id').'"','account_group_id')){
            $cond .= ' AND account_group.id = '.$account_group_id.'';
        }
        $sql = '
            SELECT 
                account_group.id,account_group.name 
            FROM 
                account_group 
            JOIN 
                `account` on `account`.account_group_id=account_group.id
            WHERE 
                account.group_id = ' . $group_id . $cond .' 
            ORDER BY
            account_group.name
        ';
        $groups = DB::fetch_all($sql);
        return $groups;
    }

    static function sentDataToApiTuha($orderId,$oldStatus=false,$newStatus=false){
        // $url = TUHA_BIGGAME_ENDPOINT;
        $data = AdminOrdersDB::processDataToApiTuha($orderId,$oldStatus,$newStatus);
        BiggameAPI::instance()->sendListCarts($data);

        /*
        if(!empty($data)){
            if(System::is_local()){
              logApiBigGame($data);
            }
            $client = new Client();
            try {
                $response = $client->post(
                    $url,
                        array(
                            'form_params' => array(
                                'orders' => $data
                            ),
                            'allow_redirects' => false,
                            'timeout'         => 10
                        )
                );

            } catch (Exception $e) {

            }
        }
        */
    }
    static function processDataToApiTuha($orderId,$oldStatus=false,$newStatus=false){
        $sql = "SELECT 
                    orders.id, 
                    orders.confirmed, 
                    orders.user_confirmed, 
                    orders.total_price, 
                    orders.group_id, 
                    orders.status_id, 
                    orders.total_price,
                    statuses.level as status_level,
                    statuses.no_revenue as status_no_revenue,
                    groups.name as group_name,
                    users.name as user_confirmed_name,
                    orders_extra.accounting_confirmed,
                    orders_extra.accounting_user_confirmed 
                FROM 
                    orders 
                LEFT JOIN 
                    groups ON orders.group_id = groups.id
                LEFT JOIN
                    users ON orders.user_confirmed = users.id
                LEFT JOIN 
                    orders_extra ON orders_extra.order_id = orders.id
                LEFT JOIN 
                    statuses ON orders.status_id = statuses.id
                WHERE
                    orders.id = $orderId;
                ";
        $result = DB::fetch($sql);
        $totalPrice = $result['total_price'];
        $accounting_confirmed = $result['accounting_confirmed'];
        $confirmed = $result['confirmed'];
        $user_confirmed = $result['user_confirmed'];
        $user_confirmed_name = $result['user_confirmed_name'];
        $data[] = [
            'id' => $result['id'],
            'confirmed' => $confirmed??'',
            'user_confirmed' => $user_confirmed??'',
            'total_price' => $totalPrice,
            'group_id' => $result['group_id']??'',
            'group_name' => $result['group_name']??'',
            'user_confirmed_name' => $user_confirmed_name??'',
            'accounting_confirmed' => $accounting_confirmed??'',
            'status_id' => $result['status_id'],
            'no_revenue' => $result['status_no_revenue']??1,
            'level' => $result['status_level']??0
        ];
        if(!empty($user_confirmed)){
            if($oldStatus == false && $newStatus == false){
                return $data;
            } else if($newStatus != false && $oldStatus != false){
                return $data;
            } else {
                return [];
            }
        }
        return [];

    }
    static function permissionSendEmailPrint()
    {
        $user_id = get_user_id();
        $access = ['IN_DON'];
        $strAccess = "('" . implode("','", $access) . "')";
        $sql = "SELECT roles_to_privilege.id FROM roles_to_privilege INNER JOIN users_roles ON roles_to_privilege.role_id = users_roles.role_id 
                    WHERE users_roles.user_id = $user_id 
                    AND roles_to_privilege.privilege_code 
                    IN $strAccess";
        $check = DB::fetch_all($sql);
        if (is_group_owner() || sizeof($check) > 0) {
            return true;
        }
        return false;
    }
    static function logConnect($id) {
        $agent = new Agent();
        $browser = $agent->browser();
        $platform = $agent->platform();

        if ($agent->isMobile()) {
            $device = 'Mobile';
        } else {
            $device = 'PC';
        }
        $userAgent = DataFilter::removeXSSinHtml($_SERVER['HTTP_USER_AGENT']);
        $ip = self::getIp();

        $log = '</b>Trình duyệt: "'.$browser.'", OS: "'.$platform.'", Device: "'.$device.'", IP: "'.$ip.'", User Agent: "'.$userAgent.'"';
        AdminOrdersDB::update_revision($id,false,false,$log);
    }
    static function getIp() {
        return $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    }
    static function update_name_customer($customerPhone, $name){
        $record['name'] = DB::escape(DataFilter::removeXSSinHtml($name));
        $customerPhone =  DB::escape($customerPhone);
        $group_id = AdminOrders::$group_id;
        $sql = "SELECT id,name,mobile FROM crm_customer WHERE mobile = '".($customerPhone)."' AND group_id = $group_id ORDER BY id DESC LIMIT 0,1";
        $query = DB::fetch($sql);
        if($query){
            DB::update('crm_customer',$record,'id = '.$query['id']);
            return $query['id'];
        } else {
            return 0;
        }

    }
    static function getListCustomerGroup(){
        $group_id = Session::get('group_id');
        $sql = '
            SELECT
                id, `crm_customer_group`.`name`
            FROM
                `crm_customer_group`
            WHERE
                structure_id <> ' . ID_ROOT . ' 
                and 
                (
                    (group_id = ' . $group_id . ' ' . (Session::get('master_group_id') ? ' or group_id=' . Session::get('master_group_id') : '') . ')
                    OR group_id = 1
                )
                ';
        return DB::fetch_all($sql);
    }
    static function log_order_extra($id) {
        $id = DB::escape($id);
        $customerGroup = AdminOrdersDB::getListCustomerGroup();
        $insurance_value = Url::post('insurance_value')?System::calculate_number(Url::post('insurance_value')):'0'; // khai gia
        $customer_group = Url::post('customer_group')?Url::post('customer_group'):'0'; // khai gia
        $deliver_date = Url::get('deliver_date') != '' ? date('Y-m-d', strtotime(str_replace('/','-',Url::get('deliver_date')))) : '';
        $saved_date = Url::get('saved_date') != '' ? date('Y-m-d', strtotime(str_replace('/','-',Url::get('saved_date')))) : '';

        $sql = "SELECT orders_extra.id,orders_extra.saved_date,orders_extra.deliver_date,orders_extra.insurance_value,orders_extra.customer_group,crm_customer_group.name  FROM orders_extra LEFT JOIN crm_customer_group ON crm_customer_group.id=orders_extra.customer_group  WHERE order_id = $id";
        $get_Extra = DB::fetch($sql);
        $log_data= '';
        if ($get_Extra && $insurance_value != $get_Extra['insurance_value']) {
            $log_data .= '</b>Thay đổi khai giá từ: "'.$get_Extra['insurance_value'].'" thành "'.$insurance_value.'"'."<br>";
        } else {
            if ($insurance_value != 0 && $insurance_value != '' && empty($get_Extra['insurance_value']))
                $log_data .= '</b>Thêm khai giá: "'.$insurance_value.'"'."<br>";
        }
        if ($get_Extra && $saved_date != $get_Extra['saved_date']) {
            $log_data .= '</b>Thay đổi ngày lưu đơn từ: "'.$get_Extra['saved_date'].'" thành "'.$saved_date.'"'."<br>";
        } else {
            if ($saved_date && empty($get_Extra['saved_date']))
                $log_data .= '</b>Thêm ngày lưu đơn: "'.$saved_date.'"'."<br>";
        }
        if ($get_Extra && $deliver_date != $get_Extra['deliver_date']) {
            $log_data .= '</b>Thay đổi ngày hẹn giao hàng từ: "'.$get_Extra['deliver_date'].'" thành "'.$deliver_date.'"'."<br>";
        } else {
            if ($deliver_date && empty($get_Extra['deliver_date']))
                $log_data .= '</b>Thêm ngày hẹn giao hàng: "'.$deliver_date.'"'."<br>";
        }
        if ($get_Extra && !empty($get_Extra['customer_group']) && $customer_group != $get_Extra['customer_group']) {
            $log_data .= '</b>Nhóm khách hàng thay đổi từ: "'.$customerGroup[$get_Extra['customer_group']]['name'].'" thành "'.$customerGroup[$customer_group]['name'].'"'."<br>";
        } else {
            if ($customer_group != 0 && $customer_group != '' && empty($get_Extra['customer_group']))
                $log_data .= '</b>Thêm nhóm khách hàng: "'.$customerGroup[$customer_group]['name'].'"'."<br>";
        }

        if ($log_data) {
            AdminOrdersDB::update_revision($id,false,false,$log_data);
        }
    }
    public static function getCustomer($mobile_number, $group_id){
        $sqlFormat = 'SELECT * FROM `crm_customer` WHERE `mobile` = "%s" AND `group_id` = %d';
        $sql = sprintf($sqlFormat, DB::escape($mobile_number), $group_id);

        return DB::fetch($sql);
    }
    static function onlySale()
    {
        $code = 'GANDON';
        $user_id = get_user_id();
        $sql = "SELECT roles_to_privilege.id,roles_to_privilege.privilege_code FROM roles_to_privilege INNER JOIN users_roles ON roles_to_privilege.role_id = users_roles.role_id 
                    WHERE users_roles.user_id = $user_id 
                   ";
        $check = DB::fetch_all($sql);
        $access = [];
        if (!empty($check)) {
            foreach ($check as $value) {
                $access[] = $value['privilege_code'];
            }
        }
        if (AdminOrders::$admin_group || is_group_owner()) {
            return true;
        }
        if (in_array($code,$access) && sizeof($access) == 1) {
            return false;
        }
    }

    /**
     * TRUE nếu có thể sửa số điện thoại, ngược lại FALSE
     *
     * Mong muốn:
     * Quyền sale khi kết hợp 1 số quyền liệt kê dưới đây sẽ không được phép sửa sô điện thoại
     *
     * + Quyền sale
     * + Quyền bung đơn
     * + Popup sửa đơn nhanh
     * + Bung đơn
     * + Bung đơn nhóm
     * + Bung đơn (Không bung thành công và chuyển hàng)
     * + Xuất excel
     * + In đơn
     * + Quyền báo cáo: Xem nhóm báo cáo Sale, Xem báo cáo chung, Xem nhóm báo cáo Marketing; Xem doanh thu MKT, Xem doanh thu sale, Xem nhóm báo cáo trực page, tình trạng xử lý và chia đơn;
     *
     * Các quyền được phép sửa sdt:
     * - Khi dùng các quyền khác với quyền dc liệt kê phía trên
     * - Khi dùng các quyền khác không được liệt kê phía trên
     * https://pm.tuha.vn/issues/9261
     */
    public static function canEditPhoneNumber()
    {
        if(AdminOrders::$admin_group || AdminOrders::$is_owner){
            return true;
        }

        // Danh sách các quyền khi kết hợp với sale sẽ không được phép sửa sdt
        $roleCodes = [
            'GANDON', 'BUNGDON', 'BUNGDON_NHOM', 'BUNGDON2', 'SUA_DON_NHANH', 'XUAT_EXCEL', 'IN_DON', 'NHOM_BC_CHUNG',
            'NHOM_BC_MKT', 'NHOM_BC_TRUC_PAGE_TINH_TRANG_DON', 'NHOM_BC_SALE', 'NHOM_BC_MKT',
            'BC_DOANH_THU_MKT', 'BC_DOANH_THU_NV', 'BC_BXH_VINH_DANH'
        ];

        $roles = getUserRoles();

        // trả lại true nếu user không có quyền Sale hoặc có quyền khác danh sách quyền bên trên
        return !in_array('GANDON', $roles) || !!array_diff($roles, $roleCodes);
    }

    static function get_rating_info($order_id){
        $cond = 'order_rating.order_id = '.$order_id;
        if(!AdminOrders::$admin_group){
            $cond .= ' AND order_rating.assigned_user_id='.AdminOrders::$user_id;
        }
        $sql = '
            SELECT
                order_rating.*
            FROM
                order_rating
            WHERE
                '.$cond.'
        ';

        if($item = DB::fetch($sql)){
            $item['can_edit'] = true;
            $rating_point = '';
            for($i=1;$i<=$item['rating_point'];$i++){
                $rating_point .= '<i class="fa fa-star"></i>';
            }
            $item['rating_point'] = $rating_point;
            if($item['rating_template_ids']){
                $sql = '
                    SELECT
                        rating_template.id,
                        rating_template.content
                    FROM
                        rating_template
                    WHERE
                        rating_template.group_id = '.AdminOrders::$group_id.'
                        AND rating_template.id IN ('.$item['rating_template_ids'].')
                ';
                $item['rating_template_ids'] = DB::fetch_all($sql);
            }
            if(!AdminOrders::$admin_group and $item['assigned_user_id']!=AdminOrders::$user_id){
                $item['can_edit'] = false;
            }
        }
        else{
            $item = [];
            $item['order_id'] = '';
            $item['rating_point'] = '';
            if(!AdminOrders::$admin_group){
                $item['can_edit'] = false;
            }else{
                $item['can_edit'] = true;
            }
        }
        return $item;
    }
    static function get_rating_template($point){
        $cond = DB::escape($cond);
        $cond = 'rating_template.group_id='.AdminOrders::$group_id.' and point='.$point;
        $sql = '
                select 
                    rating_template.id,
                    rating_template.content
                from 
                    rating_template
                WHERE
                    '.$cond.'
                order by 
                    rating_template.id DESC
            ';
        $rating_templates = DB::fetch_all($sql);
        $str = '<ul class="list-group">';
        foreach($rating_templates as $key=>$val){
            $str .= '<li class="list-group-item"><input name="rating_template[]" type="checkbox" id="rating_template_'.$key.'" value="'.$key.'"> <label for="rating_template_'.$key.'">'.$val['content'].'</label></li>';
        }
        $str .= '</ul>';
        echo $str;
    }
    static function get_total_not_assigned_orders(){
        $group_id = AdminOrders::$group_id;
        $account_type = Session::get('account_type');
        $master_group_id = AdminOrders::$master_group_id;

        if($account_type==TONG_CONG_TY or $master_group_id){
            $sql = '
              select 
                (orders.id) as total 
              from 
                orders 
                JOIN `groups` ON groups.id = orders.master_group_id
              where 
                orders.status_id = '.CHUA_XAC_NHAN.'
                and (group_id='.$group_id.' '.($master_group_id?'':' or groups.id='.$group_id.'').')
                and (user_assigned is null or user_assigned = 0)
            ';
            $total_not_assigned_order = AdminOrdersDB::get_total_item_by_sql($sql);
        }else{
            $sql1 = 'select (id) as total from orders where status_id = '.CHUA_XAC_NHAN.' and (group_id='.$group_id.') and (user_assigned is null)';
            $total_not_assigned_order1 = AdminOrdersDB::get_total_item_by_sql($sql1);
            $sql2 = 'select (id) as total from orders where status_id = '.CHUA_XAC_NHAN.' and (group_id='.$group_id.') and (user_assigned = 0)';
            $total_not_assigned_order2 = AdminOrdersDB::get_total_item_by_sql($sql2);
            $total_not_assigned_order = $total_not_assigned_order1 + $total_not_assigned_order2;
        }
        return $total_not_assigned_order;
    }
    static function get_warehouses(){
        $sql = 'select 
                qlbh_warehouse.id,
                qlbh_warehouse.name
                
            from 
                qlbh_warehouse
            where
                 qlbh_warehouse.group_id='.AdminOrders::$group_id.' and (kho_tong_shop IS null or kho_tong_shop = 0) OR structure_id='.ID_ROOT.'            
            order by
                qlbh_warehouse.is_default desc,qlbh_warehouse.name
        ';
        return $items = DB::fetch_all($sql);
    }
    static function update_reminder($order_id){
        $order_id = DB::escape($order_id);
        if(URl::get('reminder_deleted_ids')){
            $group_deleted_ids = explode(',',URl::get('reminder_deleted_ids'));
            foreach($group_deleted_ids as $delete_id){
                DB::delete('crm_customer_schedule','id='.DB::escape($delete_id).' and crm_customer_schedule.group_id='.AdminOrders::$group_id);
            }
        }
        if(isset($_REQUEST['mi_reminder'])){
            $customer_id = DB::fetch('select customer_id from orders where orders.id='.$order_id,'customer_id');
            foreach($_REQUEST['mi_reminder'] as $key=>$record){
                $record['appointed_time'] = $record['appointed_time']?$record['appointed_time']:0;
                $record['order_id'] = $order_id;
                unset($record['appointed_time_display']);
                unset($record['can_del']);
                $record['customer_id'] = $customer_id;
                if($record['id']){
                    DB::update('crm_customer_schedule',$record,'id = '.$record['id'] .' AND created_user_id =' .get_user_id() );
                }else{
                    unset($record['id']);
                    if($record['note']){
                        $record['note'] = DataFilter::removeXSSinHtml($record['note']);
                        $record['status_id'] = 1;//khách hẹn đến
                        $record['schedule_type'] = 1;//tư vấn
                        $record['group_id'] = AdminOrders::$group_id;
                        $record['branch_id'] = AdminOrders::$group_id;
                        $record['note_services'] = 'khách hẹn theo đơn hàng mã: '.$order_id;
                        DB::insert('crm_customer_schedule',$record + array('created_user_id'=>get_user_id(),'created_time'=>time()));
                    }
                }
            }
        }
    }
    static function get_reminder($order_id=false){
        $order_id = DB::escape($order_id);
        $sql = '
                SELECT 
                    crm_customer_schedule.id,
                    crm_customer_schedule.note,
                    crm_customer_schedule.appointed_time,
                    crm_customer_schedule.created_user_id
                FROM 
                    crm_customer_schedule
                WHERE 
                    crm_customer_schedule.order_id = '.$order_id.'
                ORDER BY
                    crm_customer_schedule.id
        ';
        if($items = DB::fetch_all($sql)){
            $current_user_id = get_user_id();
            foreach($items as $key=>$value){
                $items[$key]['appointed_time_display'] = $value['appointed_time']?date('d/m/Y H:i a',$value['appointed_time']):'';
                $items[$key]['can_del'] = ($value['created_user_id']==$current_user_id)?true:false;
            }
        }else{
            $items = array();
        }
        return $items;
    }
    static function free_order($order_id){ // giai phong order
        DB::update('orders',array('last_online_time'=>0,'last_edited_account_id'=>''),'id="'.DB::escape($order_id).'"');
    }
    static function can_edit_by_status($status_id){
        $status_id = DB::escape($status_id);
        $role_ids = DB::fetch_all('select id,role_id from users_roles where user_id='.get_user_id());
        $return = false;
        if(sizeof($role_ids)>0){
            foreach ($role_ids as $key=>$val){
                $row = DB::fetch('select count(id) as total from roles_statuses where roles_statuses.role_id = '.$val['role_id'].' and roles_statuses.status_id='.$status_id.' and roles_statuses.can_edit=1');
                if($row['total']>0){
                    $return = true;
                    break;
                }
            }
        }else{
            $return = false;
        }
        return $return;
    }
    static function get_order_payment($order_id,$type=1){
        $order_id = DB::escape($order_id);
        $type = DB::escape($type);
        $sql = '
                select
                    cash_flow.id,cash_flow.order_id as order_id,
                    cash_flow.amount,
                    cash_flow.bill_date as payment_date,
                    cash_flow.payment_type,
                    cash_flow.created_account_id,
                    cash_flow.note,
                    cash_flow.bill_type
                from
                    cash_flow
                WHERE
                    IFNULL(cash_flow.del,0) = 0 
                    AND cash_flow.bill_type = '.$type.'
                    AND cash_flow.order_id='.$order_id.'
                order by
                    cash_flow.id DESC
            ';
        $items = DB::fetch_all($sql);
        foreach($items as $key=>$value){
            $items[$key]['amount'] = System::display_number($value['amount']);
        }
        return $items;
    }
    static function update_order_payment($order_id){
        $order_id = DB::escape($order_id);
        if($order_id and isset($_REQUEST['mi_payment'])){
            $order = DB::fetch('select orders.id from orders where orders.id='.$order_id);
            $bill_number = format_code($order['id']);
            $group_id = AdminOrders::$group_id;
            foreach($_REQUEST['mi_payment'] as $key=>$record){
                if($record['id']=='(auto)'){
                    $record['id']=false;
                }
                $record['amount'] = $record['amount']?System::calculate_number($record['amount']):0;
                $data = '';
                $record['team_id'] = 1;
                $type = $record['bill_type'];// thu hoac chi
                if($type==1){
                    $desc =  'Thanh toán hóa đơn bán lẻ '.$bill_number;
                }else{
                    $desc =  'Hoàn tiền hóa đơn bán lẻ '.$bill_number;
                }

                //cap nhat phieu thu/chi da ton tai
                if ( $record['id'] and $payment=DB::select('cash_flow','id='.DB::escape($record['id'])) ){
                    $detail = DB::fetch('select id from cash_flow_detail where cash_flow_id='.DB::escape($record['id']).'');
                    if($detail && $record['amount'] and $payment['amount'] !=  $record['amount'] ){
                        $data .= 'Update thanh toán#'.$detail['id'].' từ <strong>'.$payment['amount'].'</strong> thành <strong>'.$record['amount'].'</strong>';
                    }
                    if($detail && $payment['payment_type'] != $record['payment_type']){
                        $data .= ($data?', ':'').'thay đổi phương thức thanh toán#'.$detail['id'].' từ '.ThuChiModuleDB::getPaymentMethodName($payment['payment_type']) .' thành ' . ThuChiModuleDB::getPaymentMethodName($record['payment_type']);
                    }
                    DB::update('cash_flow',$record,'id='.DB::escape($record['id']));
                    if( $detail ){
                        DB::update('cash_flow_detail',[
                            'amount'=>$record['amount'],
                            'payment_method'=>$record['payment_type']
                        ],'id='.$detail['id']);
                    } else {
                        $newDetailId = DB::insert('cash_flow_detail',[
                            'category_id'=>$type?9:55,
                            'cash_flow_id'=>$record['id'],
                            'amount'=>$record['amount'],
                            'description'=>$desc,
                            'order_type'=>1,
                            'order_id'=>$order_id,
                            'payment_method'=>$record['payment_type']
                        ]);
                        $data .= "Thêm mới thanh toán#{$newDetailId} #{$record['amount']} #hình thức ".ThuChiModuleDB::getPaymentMethodName($record['payment_type']);
                    }
                }
                //them moi thu/chi
                else {
                    unset($record['id']);
                    ///
                    $cond = "group_id = {$group_id} AND bill_type={$type} ";
                    require_once 'packages/vissale/modules/ThuChiModule/db.php';
                    $record['bill_number'] = ThuChiModuleDB::get_max_bill_number($cond) + 1;
                    ///
                    $record['note'] = $desc;
                    $record['created_account_id'] = User::id();
                    $record['created_full_name'] = $_SESSION['user_data']['full_name'];

                    $record['received_account_id'] = User::id();
                    $record['received_full_name'] = $_SESSION['user_data']['full_name'];

                    $record['payment_full_name'] = Url::get('customer_name');
                    $record['payment_time'] = time();
                    $record['created_time'] = time();
                    $record['bill_date'] = date('Y-m-d');

                    $record['group_id'] = $group_id;
                    $record['order_id'] = $order_id;
                    $record['mobile'] = Url::get('mobile');
                    $record['address'] = Url::get('address');
                    $record['bill_type'] = $type;
                    $record['id'] = DB::insert('cash_flow',$record);
                    $newDetailId = DB::insert('cash_flow_detail',[
                        'category_id'=>$type?9:55,
                        'cash_flow_id'=>$record['id'],
                        'amount'=>$record['amount'],
                        'description'=>$desc,
                        'order_type'=>1,
                        'order_id'=>$order_id,
                        'payment_method'=>$record['payment_type']
                    ]);
                    $data .= "Thêm thanh toán: id#{$newDetailId} #{$record['amount']} #phương thức ". ThuChiModuleDB::getPaymentMethodName($record['payment_type']);
                }
                if($data){
                    AdminOrdersDB::update_revision($order_id,false,false,$data);
                    $insertData = array(
                        'type' => 'create',
                        'created_account_id' => Session::get('user_id'),
                        'cash_flow_id' => $record['id'],
                        'group_id' => $group_id,
                        'content' => $data,
                        'created_time' => time()
                    );
                    DB::insert('cash_flow_log', $insertData);
                }
            }

            if (isset($ids) and sizeof($ids)){
                $_REQUEST['selected_ids'].=','.join(',',$ids);
            }
            if(URL::get('payment_deleted_ids')){
                $ids = explode(',',URL::get('payment_deleted_ids'));
                $ids = (array) $ids;
                $data = '';
                foreach($ids as $del_id){
                    if($payment=DB::select('cash_flow','id='.DB::escape($del_id).' and group_id='.AdminOrders::$group_id)){
                        $data .= 'Xóa lượt thanh toán <strong>'.$payment.'</strong>('.(($payment['payment_type']==1)?'Tiền mặt':(($payment['payment_type']==2)?'Chuyển khoản':'Thẻ')).')';
                        DB::delete_id('cash_flow',$del_id);
                    }
                }
                if($data){
                    AdminOrdersDB::update_revision($order_id,false,false,$data);
                }
            }
        }
    }
    static function update_order_refund($order_id){
        $order_id = DB::escape($order_id);
        if(isset($_REQUEST['mi_refund'])){
            $order = DB::fetch('select id,code from orders where id='.$order_id);
            $bill_number = format_code($order['code']);
            $group_id = Session::get('group_id');
            foreach($_REQUEST['mi_refund'] as $key=>$record){
                if($record['id']=='(auto)'){
                    $record['id']=false;
                }
                $record['amount'] = $record['amount']?System::calculate_number($record['amount']):0;
                $data = '';
                $record['team_id'] = 1;
                $type = 0;// khoan chi
                $desc =  'TRẢ GÓI hóa đơn bán lẻ '.$bill_number;
                if($record['id'] and $payment=DB::select('cash_flow','id='.DB::escape($record['id']))){
                    if($record['amount'] and $payment['amount'] !=  $record['amount']){
                        $data .= 'Update trả gói từ <strong>'.$payment['amount'].'</strong> thành <strong>'.$record['amount'].'</strong>';
                    }
                    if($payment['payment_type'] != $record['payment_type']){
                        $data .= ($data?', ':'').'thay đổi phương thức thanh toán từ '.(($payment['payment_type']==1)?'Tiền mặt':(($record['payment_type']==2)?'Chuyển khoản':'Thẻ')).' thành '.(($record['payment_type']==1)?'Tiền mặt':(($record['payment_type']==2)?'Chuyển khoản':'Thẻ'));
                    }
                    DB::update('cash_flow',$record,'id='.DB::escape($record['id']));
                    if($detail = DB::fetch('select id from cash_flow_detail where cash_flow_id='.DB::escape($record['id']).'')){
                        DB::update('cash_flow_detail',[
                            'amount'=>$record['amount'],
                            'payment_method'=>$record['payment_type']
                        ],'id='.$detail['id']);
                    }else{
                        DB::insert('cash_flow_detail',[
                            'category_id'=>55,
                            'cash_flow_id'=>$record['id'],
                            'amount'=>$record['amount'],
                            'description'=>$desc,
                            'order_type'=>1,// ban le
                            'order_id'=>$order_id,
                            'payment_method'=>$record['payment_type']
                        ]);
                    }
                }else{
                    unset($record['id']);
                    if(Url::get('cmd') == 'edit'){
                        $data .= $desc.' với số tiền '.$record['amount'];
                        if($data){
                            $data .= '<br>';
                        }
                    }
                    ///
                    $cond = "group_id = {$group_id} AND bill_type={$type} ";
                    require_once 'packages/vissale/modules/ThuChiModule/db.php';
                    $record['bill_number'] = ThuChiModuleDB::get_max_bill_number($cond) + 1;
                    ///
                    $record['note'] = $desc;
                    $record['created_account_id'] = User::id();
                    $record['created_full_name'] = $_SESSION['user_data']['full_name'];

                    $record['received_account_id'] = User::id();
                    $record['received_full_name'] = $_SESSION['user_data']['full_name'];

                    $record['payment_full_name'] = Url::get('customer_name');
                    $record['payment_time'] = time();
                    $record['created_time'] = time();
                    $record['bill_date'] = date('Y-m-d');

                    $record['group_id'] = $group_id;
                    $record['order_id'] = $order_id;
                    $record['mobile'] = Url::get('mobile');
                    $record['address'] = Url::get('address');
                    $record['bill_type'] = $type;
                    $record['id'] = DB::insert('cash_flow',$record);
                    DB::insert('cash_flow_detail',[
                        'category_id'=>55,
                        'cash_flow_id'=>$record['id'],
                        'amount'=>$record['amount'],
                        'description'=>$desc,
                        'order_type'=>1,// ban le
                        'order_id'=>$order_id,
                        'payment_method'=>$record['payment_type']
                    ]);
                }
                if($data){
                    AdminOrdersDB::update_revision($order_id,false,false,$data);
                }
            }
            if (isset($ids) and sizeof($ids)){
                $_REQUEST['selected_ids'].=','.join(',',$ids);
            }
            if(URL::get('deleted_ids')){
                $ids = explode(',',URL::get('deleted_ids'));
                $data = '';
                foreach($ids as $del_id){
                    if($payment=DB::select('cash_flow','id='.DB::escape($del_id))){
                        $data .= 'Xóa trả gói <strong>'.$payment.'</strong>('.(($payment['payment_type']==1)?'Tiền mặt':(($payment['payment_type']==2)?'Chuyển khoản':'Thẻ')).')';
                        DB::delete_id('cash_flow',$del_id);
                    }
                }
                if($data){
                    AdminOrdersDB::update_revision($order_id,false,false,$data);
                }
            }
        }
    }
    static function get_total_item_by_sql($sql){
        $total = 0;
        if($sql){
            $m_key = md5($sql);
            if (!System::is_local()) {
                DB::query($sql);
                $qr = DB::$db_result;
                $total = 0;
                if ($qr){
                    while($row = mysqli_fetch_row($qr)){
                        $total++;
                    }
                }
                MC::set_items($m_key, $total, time() + 60*5);
            }else{
                DB::query($sql);
                $qr = DB::$db_result;
                $total = 0;
                if ($qr){
                    while($row = mysqli_fetch_row($qr)){
                        $total++;
                    }
                }
            }
        }
        return $total;
    }

    static function getOrdersInfo($ids)
    {
        $idsArray = explode(',', $ids);
        $escapeIds = array_map(function($id){
            return DB::escape($id);
        }, $idsArray);
        $ids = implode(',', $escapeIds);
        return DB::fetch_all("
            SELECT
                o.id, o.group_id, o.customer_name, o.mobile,o.address, o.total_price, o.price, 
                 o.district_id, o.shipping_note, o.shipping_price, o.discount_price, o.other_price,
                 o.status_id, o.created, o.note1, o.note2, o.shipping_note, o.user_created, o.user_confirmed,
                 o.city, o.city_id, o.ward_id, xt.insurance_value,
                (SELECT zone_districts_v2.district_name FROM zone_districts_v2 WHERE zone_districts_v2.district_id = o.district_id) district_reciever,
                (SELECT zone_districts.viettel_district_name FROM zone_districts WHERE zone_districts.district_id = o.district_id) viettel_district_reciever,
                (SELECT zone_provinces.viettel_province_name FROM zone_provinces WHERE zone_provinces.province_id = o.city_id) viettel_city,
                (SELECT zone_wards_v2.ward_name FROM zone_wards_v2 WHERE zone_wards_v2.ward_id = o.ward_id) ward_reciever
            FROM orders AS o
            JOIN orders_extra AS xt ON xt.order_id = o.id
            WHERE o.id IN ($ids)
        ");
    }

    static function getOrdersInfoById($id)
    {
        $groupId = Session::get('group_id');
        $id = DB::escape($id);
        return DB::fetch("
            SELECT o.id, o.group_id, o.total_price, o.price, o.shipping_price, o.discount_price, o.other_price, xt.insurance_value
            FROM orders AS o
            JOIN orders_extra AS xt ON xt.order_id = o.id
            WHERE o.id = $id
        ");
    }

    static function getProductsFromOrderId($order_id) {
        $order_id = DB::escape($order_id);
        return DB::fetch_all("
            SELECT op.id, op.product_id, op.order_id, op.product_name, op.product_price, op.qty, op.weight
            FROM orders_products AS op
            WHERE op.order_id = $order_id
        ");
    }

    static function getProductsFromOrderIds($ids) {
        return DB::fetch_all("
            SELECT op.id, op.product_id, op.order_id, op.product_name, op.product_price, op.qty, op.weight
            FROM orders_products AS op
            WHERE op.order_id IN ($ids)
        ");
    }

    static function getShippingOptionsActive()
    {
        $group_id = Session::get('group_id');

        return DB::fetch_all("
            SELECT id, name, client_id, token, carrier_id, barcode_prefix, del, is_default
            FROM shipping_options
            WHERE group_id = $group_id AND del = 0
            ORDER BY is_default DESC, id DESC
        ");
    }

    static function getShippingOptionById($id)
    {
        return DB::fetch("
            SELECT id, name, client_id, token, carrier_id, barcode_prefix, del
            FROM shipping_options
            WHERE id = ". DB::escape($id)." AND del = 0
            LIMIT 1
        ");
    }

    static function getShippingOptionByCarrierId($carrier_id)
    {
        $carrier_id = DB::escape($carrier_id);
        $group_id = Session::get('group_id');
        return DB::fetch("
            SELECT id, name, client_id, token, carrier_id, barcode_prefix, del, is_default
            FROM shipping_options
            WHERE group_id = $group_id AND carrier_id = '$carrier_id' AND del = 0
            ORDER BY is_default DESC, id DESC
            LIMIT 1
        ");
    }

    static function getInfoSenderByOrderId() {
        $data = [];
        $ids = DB::escape(Url::get('checked_order')) OR $ids= DB::escape(Url::get('ids'));
        $group_id = Session::get('group_id');
        $group_info = DB::fetch("SELECT name AS name_dn, email AS email_dn, address AS address_dn, phone AS phone_dn, image_url from `groups` WHERE id = $group_id");

        $order_shippings = DB::fetch_all("
            SELECT os.order_id AS id, total_weight, zp.province_name AS city_sender, zd.district_name AS district_sender, zw.ward_name AS ward_sender, ssa.name AS name_sender, ssa.phone AS phone_sender, ssa.address AS address_sender
            FROM orders_shipping AS os
            JOIN shop_shipping_address AS ssa ON ssa.id = os.shipping_address_id
            LEFT JOIN zone_provinces_v2 AS zp ON zp.province_id = ssa.zone_id
            LEFT JOIN zone_districts_v2 AS zd ON zd.district_id = ssa.district_id
            LEFT JOIN zone_wards_v2 AS zw ON zw.ward_id = ssa.ward_id
            WHERE os.order_id IN ($ids)
        ");
        $ids_arr = explode(",", $ids);
        $other_arrs = $ids_arr;
        if (!empty($order_shippings)) {
            $keys_arr = array_keys($order_shippings);
            $other_arrs = array_diff($ids_arr, $keys_arr);
            foreach ($order_shippings as $order_id => $value) {
                foreach ($value as $key => $val_shipping) {
                    $data[$order_id][$key] = $val_shipping;
                }

                foreach ($group_info as $key => $value) {
                    $data[$order_id][$key] = $value;
                }
            }
        }
        if (!empty($other_arrs)) {
            foreach ($other_arrs as $order_id) {
                foreach ($group_info as $key => $value) {
                    if ($key == 'name_dn') {
                        $key = 'name_sender';
                    } elseif ($key == 'address_dn') {
                        $key = 'address_sender';
                    } elseif ($key == 'phone_dn') {
                        $key = 'phone_sender';
                    }

                    $data[$order_id][$key] = $value;
                }

                foreach ($group_info as $key => $value) {
                    $data[$order_id][$key] = $value;
                }

                if (!isset($data[$order_id]['ward_sender'])) {
                    $data[$order_id]['ward_sender'] = "";
                }

                if (!isset($data[$order_id]['district_sender'])) {
                    $data[$order_id]['district_sender'] = "";
                }

                if (!isset($data[$order_id]['city_sender'])) {
                    $data[$order_id]['city_sender'] = "";
                }
            }
        }

        return $data;
    }

    static function getPaperSizeDefault($group_id)
    {
        $group_id = DB::escape($group_id);
        $result = DB::fetch("SELECT paper_size FROM prints_templates WHERE group_id = $group_id AND is_default = 1");
        if (!empty($result)) {
            return $result['paper_size'];
        }

        return "A4-A5";
    }

    static function getPrintTemplate($cond)
    {
        $result = DB::fetch("SELECT id, data FROM prints_templates AS pt WHERE $cond");
        if (!empty($result)) {
            return $result;
        }
        return false;
    }

    static function getGroups()
    {
        $sql = "
            SELECT id, name, code
            from `groups`
            WHERE active = 1
        ";

        return DB::fetch_all($sql);
    }

    static function getOrderShippings($conds = [], $item_per_page = '')
    {
        $limit = "";
        if (!empty($item_per_page)) {
            $limit = (page_no() - 1) * $item_per_page;
            $limit = "LIMIT $limit, $item_per_page";
        }

        $sql = "
            SELECT
                os.id, os.order_id, os.shipping_order_code, os.shipping_address_text, os.carrier_id, os.shipping_status,
                os.total_weight, os.total_width, os.total_height, os.total_length, os.shipping_note, os.cod_amount, os.total_service_fee,
                os.created_at, os.total_fee, os.completed_at, os.address_text, os.group_id, os.is_freeship, os.pick_option,
                so.name AS so_name, so.token, os.carrier_id, os.cancel_at, os.return_at, os.delivered_at, os.picked_at, os.is_cod
            FROM orders_shipping AS os
            LEFT JOIN shipping_options AS so ON so.id = os.shipping_option_id
            WHERE os.id > 0 ". implode(" ", $conds) ."
            ORDER BY os.id DESC
            $limit
        ";

        return DB::fetch_all($sql);
    }

    static function getOrderShippingProcessings($conds = [], $item_per_page = '')
    {
        $limit = "";
        if (!empty($item_per_page)) {
            $limit = (page_no() - 1) * $item_per_page;
            $limit = "LIMIT $limit, ".DB::escape($item_per_page);
        }

        $sql = "
            SELECT
                jobs.id, jobs.order_id, jobs.group_id, jobs.lading_code, jobs.status_id, jobs.carrier_id,
                jobs.reasons, jobs.created_at, jobs.available_at
            FROM jobs
            WHERE jobs.id > 0 ". implode(" ", $conds) ."
            ORDER BY jobs.id DESC
            $limit
        ";

        return DB::fetch_all($sql);
    }

    static function getTotalOrderShippingsProcessing($conds = [])
    {
        return DB::fetch("
            SELECT COUNT(jobs.id) AS total
            FROM jobs
            WHERE jobs.id > 0 ". implode(" ", $conds) ."
            ORDER BY jobs.id DESC
        ", "total");
    }

    static function checkExistShippingAddress($order_id)
    {
        $order_id = DB::escape($order_id);
        $sql = "
            SELECT *
            FROM orders_shipping AS os
            WHERE os.order_id = $order_id
        ";
        $result = DB::fetch($sql);
        if (empty($result)) {
            return false;
        }

        return $result;
    }

    static function getOrderShippingByOrderId($order_id)
    {
        $order_id = DB::escape($order_id);
        $sql = "
            SELECT
                os.id, os.shipping_order_code, os.shipping_status, os.note_code, os.total_weight, os.total_width, os.total_height, os.total_length,
                os.shipping_note, os.shipping_address_text, os.carrier_id, os.total_service_fee, os.shipping_order_id,
                os.shipping_address_id, os.is_cod, os.is_freeship, os.pick_option
            FROM orders_shipping AS os
            WHERE os.order_id = $order_id
        ";
        $result = DB::fetch($sql);
        if (empty($result)) {
            return false;
        }

        return $result;
    }
    static function getTotalPrepaid($order_id){
        $order_id = DB::escape($order_id);
        $sql = "select sum(pay_amount) as total_prepaid FROM order_prepaid WHERE order_id =".$order_id;
        $result = DB::fetch($sql);
        if (empty($result)) {
            return false;
        }

        return $result;
    }

    static function getOrderShippingsByOrders()
    {
        if ($ordersId = DB::escape(Url::get('checked_order')) or $ordersId= DB::escape(Url::get('ids'))) {
            $sql = "SELECT id, carrier_id, order_id FROM orders_shipping WHERE order_id IN ($ordersId)";
            return DB::fetch_all($sql);
        }

        return null;
    }

    static function getShippingAddressById($id)
    {
        $id = DB::escape($id);
        $sql = "
            SELECT
                ssa.id, ssa.name, ssa.phone, ssa.address, 
                provinces.province_name, 
                districts.district_name, 
                wards.ward_name,
                provinces.viettel_province_name, 
                districts.viettel_district_name, 
                wards.ward_name as viettel_ward_name,
                ssa.zone_id AS province_id, ssa.district_id, ssa.ward_id,
                ssa.ems_warehouse_id
            FROM shop_shipping_address AS ssa
            LEFT JOIN zone_provinces_v2 AS provinces ON provinces.province_id = ssa.zone_id
            LEFT JOIN zone_districts_v2 AS districts ON districts.district_id = ssa.district_id
            LEFT JOIN zone_wards_v2 AS wards ON wards.ward_id = ssa.ward_id
            WHERE ssa.id = '$id'
        ";

        return DB::fetch($sql);
    }

    static function getShippingAddress($group_id)
    {
        $group_id = DB::escape($group_id);
        $sql = "
            SELECT
                ssa.id, ssa.name, ssa.phone, ssa.address, ssa.is_default,
                provinces.province_name, districts.district_name, wards.ward_name,
                provinces.province_id, ssa.district_id, ssa.ward_id, ssa.ems_warehouse_id
            FROM shop_shipping_address AS ssa
            LEFT JOIN zone_provinces_v2 AS provinces ON provinces.province_id = ssa.zone_id
            LEFT JOIN zone_districts_v2 AS districts ON districts.district_id = ssa.district_id
            LEFT JOIN zone_wards_v2 AS wards ON wards.ward_id = ssa.ward_id
            WHERE ssa.group_id = '$group_id'
            ORDER BY ssa.is_default DESC
        ";
        $results = DB::fetch_all($sql);
        if (count($results) == 1) {
            foreach ($results as $key => &$value) {
                $value['is_default'] = 1;
            }
        }

        return $results;
    }

    static function getProductNameById($product_id)
    {
        $product_id = DB::escape($product_id);
        return DB::fetch("
            SELECT name FROM products WHERE id = '$product_id'
        ", "name");
    }
    static function delete_order($ids){
        $idsArray = explode(',', $ids);
        $escapeIds = array_map(function($id){
            return DB::escape($id);
        }, $idsArray);
        $ids = implode(',', $escapeIds);
        $orders = DB::fetch_all('select orders.id,orders.mobile from orders where orders.id IN ('.$ids.') AND group_id = ' . AdminOrders::$group_id);
        $total_order = sizeof($orders);
        $desc = 'Xoá các đơn hàng: ';
        foreach($orders as $key=>$val){
            $order_id = $key;
            $desc .= 'Mã: '.$key.' - ĐT: '.$val['mobile'].', ';
            DB::delete('orders_products','order_id='.$order_id);
            DB::delete('orders_assigned_log','order_id='.$order_id);
            DB::delete('orders_extra','order_id='.$order_id);
            DB::delete('orders_payment','order_id='.$order_id);
            DB::delete('order_changes','order_id='.$order_id);
            DB::delete('order_revisions','order_id='.$order_id);
            DB::delete('orders','id='.$order_id);
        }
        System::log('DELETE','Xóa '.$total_order.' đơn hàng',$desc);
        Url::js_redirect(true,'Bạn đã xóa '.$total_order.' đơn hàng!');
    }

    static function get_districts(){
        $m_key = 'districts';
        if(!System::is_local()){
            if(!$districts=MC::get_items($m_key)){
                $districts = DB::fetch_all('SELECT district_id as id,district_name as name FROM `zone_districts_v2`');
                $districts = MiString::get_list($districts);
                Mc::set_items($m_key,$districts,time()+ 3600*24);
            }
        }else{
            $districts = DB::fetch_all('SELECT district_id as id,district_name as name FROM `zone_districts_v2`');
            $districts = MiString::get_list($districts);
        }
        return $districts;
    }
    static function get_provinces(){
        $m_key = 'provinces';
        if(!System::is_local()){
            if(!$items=MC::get_items($m_key)){
                $items = DB::fetch_all('SELECT province_id as id,province_name as name FROM `zone_provinces_v2`');
                $items = MiString::get_list($items);
                Mc::set_items($m_key,$items,time()+ 3600*24);
            }
        }else{
            $items = DB::fetch_all('SELECT province_id as id,province_name as name FROM `zone_provinces_v2`');
            $items = MiString::get_list($items);
        }
        return $items;
    }
    static function get_wards(){
        $m_key = 'wards';
        if(!System::is_local()){
            if(!$items=MC::get_items($m_key)){
                $items = DB::fetch_all('SELECT ward_id as id,ward_name as name FROM `zone_wards_v2`');
                $items = MiString::get_list($items);
                Mc::set_items($m_key,$items,time()+ 3600*24);
            }
        }else{
            $items = DB::fetch_all('SELECT ward_id as id,ward_name as name FROM `zone_wards_v2`');
            $items = MiString::get_list($items);
        }
        return $items;
    }
    static function get_zones(){
        $zones = DB::fetch_all('select province_id as id,province_name as name from zone_provinces_v2');
        require_once 'packages/core/includes/utils/vn_code.php';
        require_once 'packages/core/includes/utils/search.php';
        foreach($zones as $key=>$val){
            $zones[$key]['order_name'] = convert_utf8_to_latin($val['name']);
        }
        System::sksort($zones, 'order_name');
        return $zones;
    }

    static function get_order_extra($order_id){
        $order_id = DB::escape($order_id);
        return DB::fetch('
            SELECT 
                order_not_success,
                source_shop_id,
                source_created_user,
                source_upsale,
                insurance_value,
                accounting_user_confirmed,
                accounting_confirmed,
                upsale_from_group_id,
                upsale_from_user_id,
                customer_group,
                saved_date,
                allow_update_mobile,
                deliver_date
            FROM orders_extra 
            WHERE order_id='.$order_id
        );
    }

    static function update_upsale($order_id){
        $order_id = DB::escape($order_id);
        if($upsale_from_user_id = Url::post('upsale_from_user_id')){
            if($row=DB::fetch('select id,order_id,accounting_user_confirmed from orders_extra where order_id='.$order_id)){
                $rows_extra = array('upsale_from_user_id'=>$upsale_from_user_id, 'group_id' => AdminOrders::$group_id);
                DB::update('orders_extra',$rows_extra,'id='.$row['id']);
            }else{
                $rows_extra = array('order_id'=>$order_id,'accounting_user_confirmed'=>0,'accounting_confirmed'=>'0000-00-00 00:00:00','upsale_from_user_id'=>$upsale_from_user_id,'upsale_from_group_id'=>Session::get('group_id'));
                DB::insert('orders_extra',$rows_extra);
            }
        }
    }
    static function update_customer($order_id,$user_id=0)
    {
        require_once 'forms/UpdateCustomer.php';
        new UpdateCustomer($order_id, $user_id);
    }
    static function get_groups($group_id){
        $group_id = DB::escape($group_id);
        return DB::fetch_all('
            SELECT
                groups.id,groups.name
            FROM
                `groups`
            WHERE
                groups.master_group_id = '.$group_id.'
        ');
    }
    static function get_total_orders_from_landingpage($cond){
        return DB::fetch(
            'select
                count(contact_form.id) as total
            from
                contact_form
            where
                '.$cond.'
            ','total');
    }
    static function get_orders_from_landingpage($cond,$item_per_page=50){
        $sql = '
            SELECT
                contact_form.id,contact_form.email,contact_form.phone,
                contact_form.contact,contact_form.message,contact_form.host,
                contact_form.account_id,contact_form.time,contact_form.checked,
                contact_form.product_codes,
                contact_form.contact_type
            FROM
                contact_form
            WHERE
                '.$cond.'
            ORDER BY
                contact_form.id desc
                '.($item_per_page?' LIMIT '.((page_no('l_page_no')-1)*$item_per_page).','.(int) $item_per_page.'':'').'
        ';
        $items = DB::fetch_all($sql);
        foreach($items as $key=>$val){
            $items[$key]['time'] = $val['time']?date('d/m/Y H:i:s',$val['time']):0;
        }
        return $items;
    }
    static function get_prints(){
        $sql = '
                SELECT 
                  id,`print_name` as `name`
                FROM 
                  order_print_template
                WHERE
                    order_print_template.group_id = '.Session::get('group_id').'  
                ORDER BY 
                  order_print_template.set_default DESC
                LIMIT
                    0,5
            ';
        $items = DB::fetch_all($sql);
        return $items;
    }
    static function close_edit_order($id=false,$page_no=1){
        $stay = Url::get('stay')?true:false;
        if($stay and $id){
            Url::redirect_current(array('cmd','id'=>$id));
        }else{
            echo '
                <script>
                    if(window.opener && window.opener.ReloadList){
                        '.((Url::get('cmd')=='add')?'window.opener.ReloadList('.$page_no.');':'').'
                        window.close();
                    }else{
                        window.location = "index062019.php?page='.((Url::get('cmd')=='pos')?'admin_orders&cmd=list_pos':'admin_orders').'";
                    }
                </script>
            ';
            exit();
        }
    }
    static function update_status($order_id,$old_status_id,$status_id,$no_warehouse_invoice=false){
        require_once ROOT_PATH . 'packages/vissale/modules/AdminOrders/OrderStatusUpdater.php';
        $userID = get_user_id();
        $statuses = AdminOrders::$admin_group ? AdminOrdersDB::get_status() : AdminOrdersDB::get_status_from_roles($userID);

        return OrderStatusUpdater::setOrder($order_id, AdminOrders::$group_id)
            ->setStatusID($status_id)
            ->setUserID($userID)
            ->canCreateInvoice(!$no_warehouse_invoice)
            ->setStatuses($statuses)
            ->exec()
            ->getErrors();
    }

    static function get_total_amount($cond){
        $join = '';
        if(
            (Url::get('ngay_chuyen_kt_from') or Url::get('ngay_chuyen_kt_to'))
            or  (Url::get('ngay_thanh_cong_from') or Url::get('ngay_thanh_cong_to'))
            or  (Url::get('ngay_thu_tien_from') or Url::get('ngay_thu_tien_to'))
        ){
            $join .= ' LEFT JOIN orders_extra ON orders_extra.order_id=orders.id';
        }
        $master_group_id = AdminOrders::$master_group_id;
        $sql =
            'select
                orders.total_price
            from
                orders
              JOIN `groups` ON groups.id = orders.group_id
              '.$join.'
              ' . ((DB::escape(Url::sget('product_code')) || (DB::escape(Url::iget('warehouse_id_filter')))) ? ' JOIN orders_products ON orders_products.order_id=orders.id JOIN products ON products.id=orders_products.product_id' : '') . '
              '.(($master_group_id or Url::get('search_group_id'))?' 
                  LEFT JOIN users as UA ON UA.id=orders.user_assigned
                  LEFT JOIN `groups` as G1 ON G1.id = UA.group_id
              ':'').'
              '.((Url::get('search_group_id'))?' 
                  LEFT JOIN users as U2 ON U2.id=orders.user_confirmed
                  LEFT JOIN `groups` as G2 ON G2.id = U2.group_id
              ':'').'
            where
                '.$cond.'
        ';
        //
        $m_key= md5('total_amount_'.$cond);
        if ($m_key and !System::is_local()) {
            $total = MC::get_items($m_key);
            if (!$total) {
                if($sql){
                    DB::query($sql);
                }
                $qr = DB::$db_result;
                $total = 0;
                if ($qr){
                    while($row = mysqli_fetch_row($qr)){
                        $total += $row[0];
                    }
                }
                if ($m_key) {
                    MC::set_items($m_key, $total, time() + 60);
                }
            }
        } else {
            if($sql){
                DB::query($sql);
            }
            $qr = DB::$db_result;
            $total = 0;
            if ($qr){
                while($row = mysqli_fetch_row($qr)){
                    $total += $row[0];
                }
            }
        }
        return $total;
    }

    static function get_total_item($cond){
        $master_group_id = AdminOrders::$master_group_id;
        $join = '';
        if(
            (Url::get('ngay_chuyen_kt_from') or Url::get('ngay_chuyen_kt_to'))
            or  (Url::get('ngay_thanh_cong_from') or Url::get('ngay_thanh_cong_to'))
            or  (Url::get('ngay_thu_tien_from') or Url::get('ngay_thu_tien_to'))
        ){
            $join .= ' LEFT JOIN orders_extra ON orders_extra.order_id=orders.id';
        }
        $sql = 'select
                orders.id
            from
                orders
                JOIN `groups` ON groups.id = orders.group_id
              '.$join.'
              ' . ((DB::escape(Url::sget('product_code')) || (DB::escape(Url::iget('warehouse_id_filter')))) ? ' JOIN orders_products ON orders_products.order_id=orders.id JOIN products ON products.id=orders_products.product_id' : '') . '
              '.(($master_group_id or Url::iget('search_group_id'))?' 
                  LEFT JOIN users as UA ON UA.id=orders.user_assigned
                  LEFT JOIN `groups` as G1 ON G1.id = UA.group_id
              ':'').'
              '.((Url::iget('search_group_id'))?' 
                    LEFT JOIN users as U2 ON U2.id=orders.user_confirmed
                    LEFT JOIN `groups` as G2 ON G2.id = U2.group_id
                ':'').'
            where
                '.$cond.'
                ';
        $m_key= md5('total_item_'.$cond);
        if ($m_key and !System::is_local() and $master_group_id) {
            $total = MC::get_items($m_key);
            if (!$total) {
                DB::query($sql);
                $qr = DB::$db_result;
                $total = 0;
                if(isset($qr->num_rows)){
                    $total = $qr->num_rows;
                }
                MC::set_items($m_key, $total, time() + 60);
            }
        } else {
            DB::query($sql);
            $qr = DB::$db_result;
            $total = 0;
            if(isset($qr->num_rows)){
                $total = $qr->num_rows;
            }
        }
        return $total;
    }
    static function getTotalOrderAndItem($cond)
    {
        $join = '';
        $master_group_id = AdminOrders::$master_group_id;
        $group_id = Session::get('group_id');
        $duplicate = URL::iget('duplicate');
        $duplicate_type = get_group_options('duplicate_type');
        $dupalicateFilter = self::getFilter('duplicate', ['case' => $duplicate, 'type' => $duplicate_type, 'group_id' => $group_id]);

        $sql =
            'SELECT
                '.$dupalicateFilter['select'].'
                orders.total_price,orders.id,orders.shipping_price, orders.mobile
            FROM
                orders
                '.$dupalicateFilter['join'].'
              JOIN `groups` ON groups.id = orders.group_id
              '.$join.'
              '.((Url::sget('product_code') || Url::sget('product_barcode') || Url::iget('warehouse_id_filter'))?' JOIN orders_products ON orders_products.order_id=orders.id JOIN products ON products.id=orders_products.product_id':'').'
              '.(($master_group_id or Url::get('search_group_id'))?' 
                  LEFT JOIN users as UA ON UA.id=orders.user_assigned
                  LEFT JOIN `groups` as G1 ON G1.id = UA.group_id
              ':'').'
              '.((DB::escape(Url::get('search_group_id')))?' 
                  LEFT JOIN users as U2 ON U2.id=orders.user_confirmed
                  LEFT JOIN `groups` as G2 ON G2.id = U2.group_id
              ':'').'
              LEFT JOIN `orders_extra` ON `orders_extra`.`order_id` = `orders`.`id`
            WHERE
                '.$cond.'
                ' . $dupalicateFilter['where'] . '
            GROUP BY orders.id
        ';

        $cacheKey= md5('total_item_and_amount_'.$cond . $dupalicateFilter['where']);

        // principle return first
        if (!System::is_local() && $item = MC::get_items($cacheKey)) {
            return $item;
        }

        $qr = DB::query($sql);
        $data['totalAmount'] = 0;
        $data['totalItem'] = 0;
        $data['shippingPrice'] = 0;
        if ($qr){
            while($row = mysqli_fetch_row($qr)){
                $data['totalAmount'] += $row[0];
                $data['shippingPrice'] += $row[2];
            }
            $data['totalItem'] = $qr->num_rows;
        }

        if (!System::is_local()) {
            MC::set_items($cacheKey, $data, time() + 60);
        }

        return $data;
    }
    static function get_dupplicates($order_id,$mobile,$duplicated_type=0,$bundle_id=0,$no_style=false){

        // Trường hợp kiểu trùng đơn là sdt + loại sản phẩm mà không truyền vào loại sản phẩm thì
        // không hiển thị trùng đơn. Để đảm bảo khôn tốn query lấy dữ liệu trùng đơn thì điều kiện
        // này phải để lên trước các truy vấn
        if($duplicated_type == self::DUPLIDATE_TYPE_PHONE_BUNDLE && !$bundle_id){
            return '';
        }

        if(self::getRequestParamDuplidate() === self::FILTER_DUPLIATE_NOT){
            return '';
        }

        $mobile = trim(addslashes($mobile));
        $mobile = str_replace(array('(',')','-'),'',$mobile);

        $group_id = Session::get('group_id');
        $master_group_id = AdminOrders::$master_group_id;

        if(Session::get('account_type')==TONG_CONG_TY){//khoand edited in 30/09/2018
            $cond = ' (orders.group_id='.$group_id.' or orders.master_group_id = '.$group_id.')';
        }elseif($master_group_id){
            $cond = ' (orders.group_id='.$group_id.' or (orders.master_group_id = '.$master_group_id.'))';
        }else{
            $cond = ' orders.group_id='.$group_id.'';
        }
        $cond .= ' and orders.mobile = "'.$mobile.'" and id != '.$order_id.'';
        if($duplicated_type==1 and $bundle_id){
            $cond .= ' and orders.bundle_id = '.$bundle_id.'';
        }

        $sql = sprintf('SELECT id FROM orders WHERE %s ORDER BY id', $cond);
        $items = DB::fetch_all($sql);


        // Nếu đang ở danh sách đơn hàng mặc định (không lọc đơn trùng) và là đơn được tạo với số điện
        // thoại đầu tiên thì không hiện nút trùng đơn
        if(self::getRequestParamDuplidate() === self::FILTER_DUPLIATE_NONE && self::isFirstOrder($order_id, $items)){
            return '';
        }

        $items = MiString::get_list($items);
        $str = implode(', ',$items);
        if($no_style==false){
            $str = $str?'<a class="btn btn-sm btn-danger" data-event="duplicate" title="Trùng số" data-id="'.$order_id.'" data-content="'.$str.'" data-toggle="popover" data-trigger="hover" onclick="loadOrderDuplicate(this)">xem</a>':'';
        }

        return $str;
    }

    static function get_product_by_orders($cond,$order_by,$item_per_page=false, $encodeMobile = false){
        $statuses = AdminOrdersDB::get_status();
        if($ids = Url::get('checked_order') or $ids=Url::get('ids')){
            $item_per_page = false;
        }
        $districts = Self::get_districts();
        $wards = Self::get_wards();
        require_once 'packages/core/includes/utils/paging.php';
        $master_group_id = AdminOrders::$master_group_id;
        $group_id = Session::get('group_id');
        $group = DB::fetch('select id,account_type,prefix_post_code from `groups` where id='.$group_id);
        $pre = $group['prefix_post_code'];
        $show_full_name = get_group_options('show_full_name');
        $join = '';
        if(
            (Url::get('ngay_chuyen_kt_from') or Url::get('ngay_chuyen_kt_to'))
            or  (Url::get('ngay_thanh_cong_from') or Url::get('ngay_thanh_cong_to'))
            or  (Url::get('ngay_thu_tien_from') or Url::get('ngay_thu_tien_to'))
            or Url::get('act')=='print'
            or Url::get('cmd')=='export_excel'
        ){
            $join .= ' LEFT JOIN orders_extra ON orders_extra.order_id=orders.id';
        }


        // TH 1: KO có code
        // => SELECT * FROM (
        //      SELECT * FROM orders WHERE ... LIMIT aaa,bbb
        // ) orders
        // LEFT JOIN .. products...
        //
        $sql1= '
        SELECT 
            ' . self::getProductByOrderQuerySelects() . '
        FROM (
            SELECT
                ' . self::getProductByOrderSubQuerySelects($show_full_name, $join) . '
            FROM
                orders
                JOIN `groups` ON groups.id = orders.group_id
                '.$join.'
                '.(($master_group_id or Url::iget('search_group_id'))?' 
                  LEFT JOIN users as UA ON UA.id=orders.user_assigned
                  LEFT JOIN `groups` as G1 ON G1.id = UA.group_id
                ':'').'
                '.((Url::iget('search_group_id'))?' 
                    LEFT JOIN users as U2 ON U2.id=orders.user_confirmed
                    LEFT JOIN `groups` as G2 ON G2.id = U2.group_id
                ':'').'
                LEFT JOIN crm_customer ON crm_customer.id = orders.customer_id
            WHERE
                '.$cond.'
            GROUP BY orders.id
            ORDER BY
                ' . $order_by
            . self::getLimitOffsetClause($item_per_page) . '
        ) orders
        LEFT JOIN orders_products on orders_products.order_id = orders.id
        LEFT JOIN products on products.id = orders_products.product_id
        LEFT JOIN master_product on products.code = master_product.code
        LEFT JOIN units ON units.id = products.unit_id
        ';

        // TH 2: có code
        // => SELECT * FROM (
        //      SELECT * FROM orders
        //      JOIN orders_product ON ...
        //      JOIN products ON ...
        //      WHERE product_code LIKE ... LIMIT aaa,bbb
        // ) orders
        // LEFT JOIN .. products...
        $sql2 = '
        SELECT 
            ' . self::getProductByOrderQuerySelects() . '
        FROM (
            SELECT
                ' . self::getProductByOrderSubQuerySelects($show_full_name, $join) . '
            FROM
                orders
                JOIN `groups` ON groups.id = orders.group_id
                JOIN orders_products on orders_products.order_id = orders.id
                JOIN products on products.id = orders_products.product_id
                '.$join.'
                '.(($master_group_id or Url::iget('search_group_id'))?' 
                  LEFT JOIN users as UA ON UA.id=orders.user_assigned
                  LEFT JOIN `groups` as G1 ON G1.id = UA.group_id
              ':'').'
              '.((Url::iget('search_group_id'))?' 
                    LEFT JOIN users as U2 ON U2.id=orders.user_confirmed
                    LEFT JOIN `groups` as G2 ON G2.id = U2.group_id
                ':'').'
                LEFT JOIN crm_customer ON crm_customer.id = orders.customer_id
            WHERE
                '.$cond.'
            GROUP BY orders.id '
            . self::getLimitOffsetClause($item_per_page) . '
        ) orders
        LEFT JOIN orders_products on orders_products.order_id = orders.id
        LEFT JOIN products on products.id = orders_products.product_id
        LEFT JOIN master_product on products.code = master_product.code
        LEFT JOIN units ON units.id = products.unit_id
        ORDER BY '.$order_by;
        
        $items = DB::fetch_all(URL::getString('product_code') ? $sql2 : $sql1);
        $i=1;
        $duplicate_type = get_group_options('duplicate_type');
        $isOwner = AdminOrders::$is_owner;

        $orderIds = [];
        foreach ($items as $k => $v) {
            $orderIds[] = $v['order_id'];
        }
        $sql_prepaid = "select COALESCE(sum(pay_amount), 0) as prepaid,order_id, order_id as id from order_prepaid where order_id in(".implode(',', array_unique($orderIds)).") group by order_id ";
        $prepaids = DB::fetch_all($sql_prepaid);
        $previousOrderId = 0;
        foreach($items as $key =>$value){
            $discount_amount = !empty($value['discount_amount']) ? ($value['discount_amount']/$value['product_quantity']): 0;
            $product_amount = ($value['product_price'] - $discount_amount) *$value['product_quantity'];
            $items[$key]['product_amount'] = $product_amount;

            $no_style = true;
            if((Url::get('act')!='print' and Url::get('cmd')!='export_excel') and Url::get('cmd') != 'export_ship_excel' and Url::get('cmd') != 'quick_edit' ){
                $no_style = false;
            }
            $email = $value['email'];
            if($no_style==false){
                $email = '<span class="label label-default">'.$value['email'].'</span>';
            }
            $items[$key]['email'] = $email;
            $items[$key]['district_reciever'] = ($value['district_id'] and isset($districts[$value['district_id']]))?$districts[$value['district_id']]:'';
            $items[$key]['ward'] = ($value['ward_id'] and isset($wards[$value['ward_id']]))?$wards[$value['ward_id']]:'';
            $status = '...';
            $status_color = '#EFEFEF';
            if(isset($statuses[$value['status_id']])){
                $status = $statuses[$value['status_id']]['name'];
                $status_color = $statuses[$value['status_id']]['color'];
            }
            if($no_style==false){
                $status = '<span class="order-list-status" style="border: 1px solid '.$status_color.';border-left: 3px solid '.$status_color.';">'.$status.'</span>';
            }
            $items[$key]['status_name'] = $status;
            //----------------- end xu ly hien thi trang thai -----------------
            //----------------- start xu ly hien thi mobile -------------------
            $mobile = '';
            $mobile1 = $value['mobile'];
            $mobile2 = $value['mobile2'];
            if($encodeMobile and ($length = AdminOrders::$hide_phone_number  and !$isOwner)){
                $mobile1 = ModifyPhoneNumber::hidePhoneNumber($mobile1,$length);
                $mobile2 = ModifyPhoneNumber::hidePhoneNumber($mobile2,$length);
            }
            $mobile = $mobile1.((empty($mobile1)?'':' ')).$mobile2;
            if($no_style==false){
                $mobile = '<span class="order-list-mobile" style="border: 1px solid '.$status_color.';border-left: 3px solid '.$status_color.';"> <i class="fa fa-phone"></i>'.$mobile.'</span>';
            }
            $items[$key]['mobile'] = $mobile;
            //----------------- end xu ly hien thi mobile ---------------------
            $create_time = strtotime($value['created']);
            $items[$key]['created'] = date('Y-m-d H:i:s\'',$create_time);
            if($item_per_page){
                $index = $i + $item_per_page*(page_no()-1);
            }else{
                $index = $i;
            }
            $items[$key]['index'] = $index;
            $j = 0;
            if($value['fb_customer_id']){
                $items[$key]['fb_customer_id'] = '<a class="btn btn-primary btn-sm" href="https://www.facebook.com/'.$value['fb_customer_id'].'" target="_blank"><i class="fa fa-facebook-square"></i> FB KH</a>';
            }
            if($value['fb_post_id']){
                $items[$key]['plain_fb_post_id'] = $value['fb_post_id'];
                $items[$key]['fb_post_id'] = '<a class="btn btn-primary btn-sm" href="https://www.facebook.com/'.$value['fb_post_id'].'" target="_blank"><i class="fa fa-facebook-square"></i> Bài Post</a>';
            }
            if($no_style==false){
                $source_name = $value['source_name'].'<br><span style="color:#999;font-size:11px;">('.(($value['type']==2)?'CSKH':'SALE').')</span>';
            }else{
                $source_name = $value['source_name'].'';
            }
            $items[$key]['source'] = $source_name;
            $items[$key]['type'] = (($value['type']==2)?'CSKH':'SALE');
            $items[$key]['duplicate_note'] = AdminOrdersDB::get_dupplicates($key,$value['mobile'],$duplicate_type,$duplicate_type?$value['bundle_id']:0,$no_style);

            $items[$key]['code'] = str_pad($key,6,'0',STR_PAD_LEFT);
            $bar_code = ($pre).$items[$key]['code'];
            $items[$key]['bar_code'] = $bar_code;
            $items[$key]['postal_bar_code'] = $items[$key]['postal_code'];
            $items[$key]['pre'] = $pre;
            if(!$previousOrderId || $previousOrderId != $value['order_id']){
                $items[$key]['total_price'] = $value['total_price'];//System::display_number(

                if(isset($prepaids[$items[$key]['order_id']])){
                    $items[$key]['prepaid'] =  $prepaids[$items[$key]['order_id']]['prepaid'];
                    $items[$key]['prepaid_remain']= $items[$key]['total_price'] - $prepaids[$items[$key]['order_id']]["prepaid"];
                }else{
                    $items[$key]['prepaid'] =  0;
                    $items[$key]['prepaid_remain']= $items[$key]['total_price'];
                }
            }else{
                $items[$key]['total_price'] = 0;
                $items[$key]['prepaid'] =  0;
                $items[$key]['prepaid_remain'] =  0;
            }
            $items[$key]['editting'] = AdminOrdersDB::is_edited($key);
            ///
            if($value['fb_page_id']){
                if($page = DB::fetch('select id,page_name,page_id from fb_pages where page_id="'.$value['fb_page_id'].'"')){
                    $page_name = $page['page_name'];
                    $items[$key]['page'] = $page_name?'<a class="btn btn-primary btn-sm" href="https://www.facebook.com/'.$page['page_id'].'" target="_blank"><i class="fa fa-facebook-square"></i> '.$page_name.'</a>':'';
                }else{
                    $items[$key]['page'] = '<a class="btn btn-primary btn-sm" href="https://www.facebook.com/'.$value['fb_page_id'].'" target="_blank"><i class="fa fa-facebook-square"></i> FB Page</a>';
                }
            }else{
                $items[$key]['page'] = '';
            }
            $previousOrderId = $items[$key]['order_id'];
            $i++;
        }
        return ($items);
    }

    /**
     * Gets the product by order query selects.
     *
     * @return     string  The product by order query selects.
     */
    private static function getProductByOrderQuerySelects()
    {
        return '
            orders.id as order_id, 
            orders.*, 
            IF(orders_products.id is null,orders.id,orders_products.id) as id,
            orders_products.discount_amount,
            orders_products.product_name,
            orders_products.product_price,
            orders_products.qty as product_quantity,
            products.code as product_code,
            units.name as product_unit,
            master_product.full_name
        ';
    }
    /**
     * Gets the product by order sub query selects.
     *
     * @param      int     $show_full_name  The show full name
     * @param      bool    $join            The join
     *
     * @return     string  The product by order sub query selects.
     */
    private static function getProductByOrderSubQuerySelects($show_full_name, $join)
    {
        return '
            orders.id,
            orders.group_id,
            orders.master_group_id,
            orders.fb_customer_id,
            orders.fb_page_id,
            orders.fb_post_id,
            orders.fb_comment_id,
            orders.fb_conversation_id,
            orders.total_qty,
            orders.code,
            orders.postal_code,
            orders.customer_name,
            orders.customer_id,
            orders.mobile,
            orders.mobile as mobile1,
            orders.mobile2,
            orders.telco_code,
            orders.city,
            orders.address,
            orders.note1,
            orders.note2,
            orders.note1 as note,
            orders.cancel_note,
            orders.shipping_note,
            orders.status_id,
            orders.price,
            ROUND(orders.discount_price/orders.price,2)*100 AS discount_rate,
            orders.discount_price,
            orders.shipping_price,
            orders.other_price,
            orders.total_price,
            orders.user_modified,
            orders.confirmed,
            orders.delivered,
            orders.created,
            orders.modified,
            orders.last_online_time,
            orders.last_edited_account_id,
            orders.source_id,
            orders.source_name,
            orders.type,
            orders.first_user_assigned,
            orders.first_assigned,
            orders.user_assigned as user_assigned_id,
            orders.user_created as user_created_id,
            orders.bundle_id,
            (select bundles.name from bundles where bundles.id = orders.bundle_id) as  bundle,
            "" as status_color,
            groups.name as group_name,
            orders.district_id,
            "" AS district_reciever,
            orders.ward_id,
            "" AS ward,
            crm_customer.email,
            (SELECT shipping_services.name FROM shipping_services WHERE shipping_services.id = orders.shipping_service_id) shipping_service,
            (SELECT party.label FROM party JOIN users ON users.username=party.user_id WHERE users.id=orders.user_assigned limit 0,1) AS label,
            (SELECT '.(($show_full_name==1)?'users.name':'users.username').' FROM users WHERE users.id=orders.user_assigned limit 0,1) AS user_assigned,
            (SELECT '.(($show_full_name==1)?'users.name':'users.username').' FROM users WHERE users.id=orders.user_confirmed limit 0,1) AS user_confirmed,
            (SELECT '.(($show_full_name==1)?'users.name':'users.username').' FROM users WHERE users.id=orders.user_created limit 0,1) AS user_created,
            (SELECT '.(($show_full_name==1)?'users.name':'users.username').' FROM users WHERE users.id=orders.user_delivered limit 0,1) AS user_delivered,
            (SELECT '.(($show_full_name==1)?'upsale_user.name':'upsale_user.username').' FROM orders_extra JOIN users as upsale_user ON upsale_user.id = orders_extra.upsale_from_user_id WHERE upsale_user.id=orders_extra.upsale_from_user_id AND orders_extra.order_id = orders.id limit 0,1) AS upsale_from_user_id,
            '.(
            ($join?'
                    orders_extra.sort_code,
                    orders_extra.accounting_user_confirmed,
                    orders_extra.accounting_confirmed,
                    (SELECT username from users where id = orders_extra.source_created_user) as source_created_user,
                    (SELECT username from users where id = orders_extra.source_upsale) as source_upsale,
                    (SELECT name from groups where id = orders_extra.source_shop_id) as source_shop_id,
                    orders_extra.update_returned_to_warehouse_time,
                    orders_extra.update_successed_user,
                    orders_extra.update_successed_time,
                    orders_extra.update_paid_user,
                    orders_extra.update_paid_time,
                    orders_extra.update_returned_time,
                    orders_extra.update_returned_user
                    ':'"" as sort_code')
            );
    }
    static function getItemSendEmail($groupId, string $orderIds, $encodeMobile = true, $forceEncodeMobile = false)
    {
        $group = AdminOrders::$group;
        $pre = $group['prefix_post_code'];
        $statuses = AdminOrdersDB::get_status();
        $districts = Self::get_districts();
        $wards = Self::get_wards();
        $isOwner = AdminOrders::$is_owner;
        $sql = "SELECT  orders.id,
                        orders.district_id,
                        orders.ward_id,
                        orders.status_id,
                        orders.mobile,
                        orders.mobile2,
                        orders.fb_customer_id,
                        orders.source_name,
                        orders.group_id,
                        orders.master_group_id,
                        orders.fb_page_id,
                        orders.fb_post_id,
                        orders.fb_comment_id,
                        orders.fb_conversation_id,
                        orders.total_qty,
                        orders.code,
                        orders.customer_name,
                        orders.customer_id,
                        orders.telco_code,
                        orders.city,
                        orders.address,
                        orders.note2,
                        orders.note1 as note,
                        orders.cancel_note,
                        orders.shipping_note,
                        orders.price,
                        orders.postal_code,
                        orders.total_price,
                        orders.discount_price,
                        orders.shipping_price,
                        orders.other_price,
                        orders.user_modified,
                        orders.confirmed,
                        orders.delivered,
                        orders.user_delivered,
                        orders.created,
                        orders.modified,
                        orders.last_online_time,
                        orders.last_edited_account_id,
                        orders.source_id,
                        orders.source_name,
                        orders.type,
                        orders.first_user_assigned,
                        orders.first_assigned,
                        orders.user_assigned as user_assigned_id,
                        orders.user_created as user_created_id,
                        orders.bundle_id,
                        orders_extra.sort_code,crm_customer.email FROM orders
                        LEFT JOIN crm_customer ON orders.customer_id =  crm_customer.id
                        LEFT JOIN orders_extra ON orders_extra.order_id = orders.id
                        WHERE  orders.id IN ($orderIds) AND orders.group_id = $groupId";
        $orders = DB::fetch_all($sql);
        $i = 1;
        $products = AdminOrdersDB::getOrderProductSendEmail($orderIds,false);
        foreach ($orders as $key => $value) {
            $orders[$key]['district_reciever'] = ($value['district_id'] and isset($districts[$value['district_id']]))?$districts[$value['district_id']]:'';
            $orders[$key]['ward'] = ($value['ward_id'] and isset($wards[$value['ward_id']]))?$wards[$value['ward_id']]:'';
            $status = '...';
            if(isset($statuses[$value['status_id']])){
                $status = $statuses[$value['status_id']]['name'];
            }
            $orders[$key]['status_name'] = $status;
            $mobile1 = $value['mobile'];
            $mobile2 = $value['mobile2'];
            $fullMobile = $mobile1.' '.$mobile2;
            if (($encodeMobile and !$isOwner) or $forceEncodeMobile) {
                $length = AdminOrders::$hide_phone_number;
                $mobile1 = ModifyPhoneNumber::hidePhoneNumber($mobile1,$length);
                $mobile2 = ModifyPhoneNumber::hidePhoneNumber($mobile2,$length);
            }
            $mobile = $mobile1.((empty($mobile1)?'':' ')).$mobile2;
            $orders[$key]['mobile'] = $mobile;
            $orders[$key]['fullMobile'] = $fullMobile;
            $product_code = '';
            $product_str = '';
            $j = 0;
            foreach ($products as $kPro => $v) {
                if ($key == $v['order_id']) {
                    $orders[$key]['detail_products'][$kPro] = $v;
                    $product_code .= ($j > 0 ? '<br>' : '') . $v['qty'] . '-' . $v['code'];
                    $product_str .= (($j>0)?'<br> ':'').$v['qty'].' '.$v['code'].' - '.$v['name'].''.($v['size']?' size '.$v['size'].'':'').''.($v['color']?' màu '.$v['color'].'':'');
                    $j++;
                    $orders[$key]['products'] = $product_str;
                    $orders[$key]['product_code'] = $product_code;
                }
            }
            if($value['fb_customer_id']){
                $orders[$key]['fb_customer_id'] = '<a class="btn btn-primary btn-sm" href="https://www.facebook.com/'.$value['fb_customer_id'].'" target="_blank"><i class="fa fa-facebook-square"></i> FB KH</a>';
            }
            if($value['fb_post_id']){
                $orders[$key]['plain_fb_post_id'] = $value['fb_post_id'];
                $orders[$key]['fb_post_id'] = '<a class="btn btn-primary btn-sm" href="https://www.facebook.com/'.$value['fb_post_id'].'" target="_blank"><i class="fa fa-facebook-square"></i> Bài Post</a>';
            }
            $source_name = $value['source_name'].' ('.(($value['type']==2)?'CSKH':'SALE').')';
            $orders[$key]['source'] = $source_name;
            $orders[$key]['code'] = str_pad($key,6,'0',STR_PAD_LEFT);
            $bar_code = ($pre).$orders[$key]['code'];

            $orders[$key]['bar_code_id'] = $value['id'];
            $orders[$key]['bar_code'] = $bar_code;
            $orders[$key]['postal_bar_code'] = $value['postal_code'] !== '' ? $value['postal_code'] : $key;
            $orders[$key]['pre'] = $pre;
            $orders[$key]['total_price'] = $value['total_price'];
            $i++;
        }
        return  $orders;
    }
    static function getOrderProductSendEmail($order_id,$group=true){
        {
            $idsArr = explode(',',$order_id);
            $escape_ids = array_map(function($id){
                return DB::escape($id);
            }, $idsArr);

            $order_id = implode(',', $escape_ids);
            $sql = '
                select
                    orders_products.id,orders_products.order_id,
                    orders_products.product_id,
                    orders_products.product_price,
                    orders_products.weight,
                    orders_products.height,
                    orders_products.width,
                    orders_products.length,
                    orders_products.warehouse_id,
                    '.($group?'sum(orders_products.qty) as qty':'orders_products.qty').',
                    products.name,products.name as product_name,products.color,products.size,products.code,
                    orders_products.which_process, orders_products.staff_rating, orders_products.customer_rating,
                    units.name AS units_name
                from
                    orders_products
                    JOIN products ON products.id = orders_products.product_id
                    LEFT JOIN units ON units.id = products.unit_id
                WHERE
                    orders_products.order_id in ('.$order_id.')
                '.($group?'GROUP BY orders_products.product_id':'').'   
                order by
                    orders_products.product_price DESC
            ';
            $order_product = DB::fetch_all($sql);
            foreach($order_product as $key=>$value){
                $order_product[$key]['product_price'] = System::display_number($value['product_price']);
                $order_product[$key]['total'] = System::display_number($value['product_price']*$value['qty']);
            }
            return $order_product;
        }
    }

    static function get_assign_items($cond,$limit=false,$order_by=false){
        $master_group_id = AdminOrders::$master_group_id;
        $join = '';
        if(
            (Url::get('ngay_chuyen_kt_from') or Url::get('ngay_chuyen_kt_to'))
            or  (Url::get('ngay_thanh_cong_from') or Url::get('ngay_thanh_cong_to'))
            or  (Url::get('ngay_thu_tien_from') or Url::get('ngay_thu_tien_to'))
            or Url::get('act')=='print'
            or Url::get('cmd')=='export_excel'
        ){
            $join .= ' LEFT JOIN orders_extra ON orders_extra.order_id=orders.id';
        }
        $sql = '
            SELECT
                orders.id,
                orders.user_assigned,
                orders.assigned,
                orders.first_user_assigned,
                orders.first_assigned
            FROM
                orders
                JOIN `groups` ON groups.id = orders.group_id
                '.$join.'
                '.(Url::sget('product_code')?' JOIN orders_products ON orders_products.order_id=orders.id JOIN products ON products.id=orders_products.product_id':'').'
                '.($master_group_id?'
                    LEFT JOIN users as UA ON UA.id=orders.user_assigned
                    LEFT JOIN `groups` as G1 ON G1.id = UA.group_id
                ':'').'
                LEFT JOIN crm_customer ON crm_customer.id = orders.customer_id
            WHERE
                '.$cond.'
            '.($order_by?' ORDER BY '.$order_by:'').'
            '.($limit?' LIMIT 0,'.$limit:'').'
        ';
        $items = DB::fetch_all($sql);
        return ($items);
    }
    static function get_items($cond,$order_by,$item_per_page=false, $encodeMobile = true, $forceEncodeMobile = false){
        $master_group_id = AdminOrders::$master_group_id;
        $group_id = AdminOrders::$group_id;
        $group = AdminOrders::$group;
        $statuses = AdminOrdersDB::get_status();
        if($ids = Url::get('checked_order') or $ids=Url::get('ids')){
            $item_per_page = false;
        }
        $districts = Self::get_districts();
        $wards = Self::get_wards();
        $provinces = AdminOrdersDB::get_provinces();
        require_once 'packages/core/includes/utils/paging.php';
        $pre = $group['prefix_post_code'];
        $show_full_name = get_group_options('show_full_name');
        $join = ' LEFT JOIN orders_extra ON orders_extra.order_id=orders.id';

        $duplicate = self::getRequestParamDuplidate();
        $duplicate_type = get_group_options('duplicate_type');
        $dupalicateFilter = self::getFilter('duplicate', ['case' => $duplicate, 'type' => $duplicate_type, 'group_id' => $group_id]);

        $sql = '
            SELECT
                '.$dupalicateFilter['select'].'
                orders.id,
                orders.group_id,
                orders.master_group_id,
                orders.fb_customer_id,
                orders.fb_page_id,
                orders.fb_post_id,
                orders.fb_comment_id,
                orders.fb_conversation_id,
                orders.total_qty,
                orders.code,
                orders.postal_code,
                orders.customer_name,
                orders.customer_id,
                orders.mobile,
                orders.mobile as mobile1,
                orders.mobile2,
                orders.telco_code,
                orders.city,
                orders.address,
                orders.note1,
                orders.note2,
                orders.note1 as note,
                orders.cancel_note,
                orders.shipping_note,
                orders.status_id,
                orders.price,
                orders.discount_price,
                orders.shipping_price,
                orders.other_price,
                orders.total_price,
                orders.user_modified,
                orders.confirmed,
                orders.delivered,
                orders.user_delivered,
                orders.created,
                orders.modified,
                orders.last_online_time,
                orders.last_edited_account_id,
                crm_customer_group.name as customer_group,
                orders.source_id,
                order_source.name as source_name,
                orders.type,
                orders.first_user_assigned,
                orders.first_assigned,
                orders.user_assigned as user_assigned_id,
                orders.user_created as user_created_id,
                orders.bundle_id,
                "" as status_color,     
                groups.name as group_name,
                orders.district_id,
                "" AS district_reciever,
                orders.ward_id,
                orders.city_id,
                "" AS ward,
                crm_customer.email,
                (select name from bundles where id = orders.bundle_id) as bundle_name,
                (SELECT username from users where id = orders_extra.source_created_user) as source_created_user,
                (SELECT username from users where id = orders_extra.source_upsale) as source_upsale,
                (SELECT name from groups where id = orders_extra.source_shop_id) as source_shop_id,
                (SELECT shipping_services.name FROM shipping_services WHERE shipping_services.id = orders.shipping_service_id) shipping_service,
                (SELECT party.label FROM party JOIN users ON users.username=party.user_id WHERE users.id=orders.user_assigned limit 0,1) AS label,
                orders.user_assigned,
                orders.user_confirmed,
                orders.user_created,
                orders.user_delivered,
                orders.user_confirmed,
                (SELECT '.(($show_full_name==1)?'upsale_user.name':'upsale_user.username').' FROM orders_extra JOIN users as upsale_user ON upsale_user.id = orders_extra.upsale_from_user_id WHERE upsale_user.id=orders_extra.upsale_from_user_id AND orders_extra.order_id = orders.id limit 0,1) AS upsale_from_user_id,
                '.(
            ($join?'
                    orders_extra.sort_code,
                    orders_extra.saved_date,
                    orders_extra.deliver_date,
                    orders_extra.accounting_user_confirmed,
                    orders_extra.accounting_confirmed,
                    orders_extra.update_successed_user,
                    orders_extra.update_successed_time,
                    orders_extra.update_paid_user,
                    orders_extra.update_paid_time,
                    orders_extra.update_returned_time,
                    orders_extra.update_returned_user,
                    orders_extra.source_created_user as source_created_user_id,
                    orders_extra.source_shop_id as shop_id,
                    orders_extra.source_upsale as source_upsale_id,
                    orders_extra.update_returned_to_warehouse_time,
                    orders_extra.confused_user_confirmed as confused_user_confirmed_id,
                    orders_extra.not_answer_phone_user_confirmed as not_answer_phone_user_confirmed_id,
                    orders_extra.confused_confirmed,
                    orders_extra.not_answer_phone_confirmed,
                    (SELECT username from users where id = orders_extra.confused_user_confirmed) as confused_user_confirmed,
                    (SELECT username from users where id = orders_extra.not_answer_phone_user_confirmed) as not_answer_phone_user_confirmed,
                    orders_extra.update_returned_to_warehouse_user_id as update_returned_to_warehouse_user
                    ':'"" as sort_code')
            ).'
            FROM
                orders
                '.$dupalicateFilter['join'].'
                JOIN `groups` ON groups.id = orders.group_id
                '.$join.'
                ' . ((DB::escape(Url::sget('product_code')) || (DB::escape(Url::iget('warehouse_id_filter')))) ? ' JOIN orders_products ON orders_products.order_id=orders.id JOIN products ON products.id=orders_products.product_id' : '') . '
                '.(($master_group_id or Url::iget('search_group_id'))?'
                    LEFT JOIN users as UA ON UA.id=orders.user_assigned
                    LEFT JOIN `groups` as G1 ON G1.id = UA.group_id
                ':'').'
                '.((Url::get('search_group_id'))?' 
                    LEFT JOIN users as U2 ON U2.id=orders.user_confirmed
                    LEFT JOIN `groups` as G2 ON G2.id = U2.group_id
                ':'').'
                LEFT JOIN crm_customer ON crm_customer.id = orders.customer_id
                LEFT JOIN crm_customer_group ON crm_customer_group.id = orders_extra.customer_group
                LEFT JOIN order_source ON order_source.id = orders.source_id
            WHERE
                '.$cond.'
                ' . $dupalicateFilter['where'] . '
            GROUP BY orders.id
            ORDER BY
                '.$order_by;

        $sql .= self::getLimitOffsetClause($item_per_page);
        $items = DB::fetch_all($sql);
        $i=1;
        $duplicate_type = get_group_options('duplicate_type');
        $isOwner = AdminOrders::$is_owner;
        $show_product_detail = get_group_options('show_product_detail');
        $orderIds = [];
        foreach ($items as $k => $v) {
            $orderIds[] = $k;
        }
        $sql_prepaid = "select COALESCE(sum(pay_amount), 0) as prepaid, order_id as id from order_prepaid where order_id in(".implode(',', $orderIds).") group by order_id ";
        $prepaids = DB::fetch_all($sql_prepaid);

        $getProductOrder = self::getProductOrder($orderIds);
        $getEdited = self::getEdited($orderIds);
        $userColumns = [
            "user_assigned",
            "user_confirmed",
            "user_created",
            "user_delivered",
            "user_confirmed",
            "accounting_user_confirmed",
            "accounting_confirmed",
            "update_successed_user",
            "update_successed_time",
            "update_paid_user",
            "update_paid_time",
            "update_returned_time",
            "update_returned_user",
            "update_returned_to_warehouse_time",
            "update_returned_to_warehouse_user"
        ];

        $shopUsers = DB::fetch_all('select id, name, username from users where group_id = "' . $group_id . '"');
        $arrShippingServices = self::shipping_services();
        foreach($items as $key =>$value){

            // Map username vao cac truong user_id
            foreach ($userColumns as $column) {

                $_userID = $items[$key][$column] ?? '';
                $shopUser = $shopUsers[$_userID] ?? '';

                if(!empty($shopUser)){
                    $items[$key][$column] = $shopUser[$show_full_name ? 'name' : 'username'];
                }
            }


            $no_style = true;
            if((Url::get('act')!='print' and Url::get('cmd')!='export_excel') and Url::get('cmd') != 'export_ship_excel' and Url::get('cmd') != 'quick_edit' and Url::get('cmd') != 'send_mail_carrier' and Url::get('cmd') != 'export_excel_carrier'){
                $no_style = false;
            }
            $email = $value['email'];
            if($no_style==false){
                $email = '<span class="label label-default">'.$value['email'].'</span>';
            }

            if(isset($prepaids[$key])){
                $items[$key]['prepaid'] =  $prepaids[$key]["prepaid"];
                $items[$key]['prepaid_remain']= $items[$key]['total_price'] - $prepaids[$key]["prepaid"];
            }else{
                $items[$key]['prepaid'] =  0;
                $items[$key]['prepaid_remain']= $items[$key]['total_price'];
            }
            $items[$key]['email'] = $email;
            $items[$key]['district_reciever'] = ($value['district_id'] and isset($districts[$value['district_id']]))?$districts[$value['district_id']]:'';
            $items[$key]['ward'] = ($value['ward_id'] and isset($wards[$value['ward_id']]))?$wards[$value['ward_id']]:'';

            $items[$key]['district_name'] = isset($districts[$value['district_id']]) ? $districts[$value['district_id']]: '';
            $items[$key]['province_name'] = isset($provinces[$value['city_id']]) ? $provinces[$value['city_id']]: '';
            $items[$key]['ward_name'] = isset($wards[$value['ward_id']]) ? $wards[$value['ward_id']] : '';
            $items[$key]['order_type'] = $value['type'] >= 1? AdminOrders::$type[$value['type']] : '';
            $items[$key]['shipping_service'] = isset($value['shipping_service']) ? $value['shipping_service'] : "";
            //----------------- start xu ly hien thi trang thai -----------------
            $status = '...';
            $status_color = '#EFEFEF';
            if(isset($statuses[$value['status_id']])){
                $status = $statuses[$value['status_id']]['name'];
                $status_color = $statuses[$value['status_id']]['color'];
            }
            if($no_style==false){
                $status = '<span class="order-list-status" style="border: 1px solid '.$status_color.';border-left: 3px solid '.$status_color.';">'.$status.'</span>';
            }
            $items[$key]['status_name'] = $status;
            //----------------- end xu ly hien thi trang thai -----------------
            //----------------- start xu ly hien thi mobile -------------------
            $mobile1 = $value['mobile'];
            $mobile2 = $value['mobile2'];

            $fullMobile = $mobile1.' '.$mobile2;
            if (($encodeMobile and !$isOwner) or $forceEncodeMobile) {
                $length = AdminOrders::$hide_phone_number;
                $mobile1 = ModifyPhoneNumber::hidePhoneNumber($mobile1,$length);
                $mobile2 = ModifyPhoneNumber::hidePhoneNumber($mobile2,$length);
            }
            $mobile = $mobile1.((empty($mobile1)?'':' ')).$mobile2;
            if($no_style==false){
                $mobile = '<span class="order-list-mobile" style="border: 1px solid '.$status_color.';border-left: 3px solid '.$status_color.';"> <i class="fa fa-phone"></i>'.$mobile.'</span>';
            }

            $items[$key]['home_network'] = AdminOrdersDB::check_home_network($value['mobile']);
            $items[$key]['mobile'] = $mobile;
            $items[$key]['mobile2'] = $mobile2;
            $items[$key]['fullMobile'] = $fullMobile;
            $items[$key]['mobile_both'] = $mobile1 . ((empty($mobile2) ? '' : ' / ')) . $mobile2;
            //----------------- end xu ly hien thi mobile ---------------------
            $create_time = strtotime($value['created']);
            $items[$key]['created'] = date('Y-m-d H:i:s',$create_time);
            if($item_per_page){
                $index = $i + $item_per_page*(page_no()-1);
            }else{
                $index = $i;
            }
            $items[$key]['index'] = $index;
            // $products = AdminOrdersDB::get_order_product($key);
            $products = [];
            if(isset($getProductOrder[$key])){
                $products = $getProductOrder[$key];
            }
            $items[$key]['detail_products'] = $products;
            $product_code = '';
            $product_str = [];
            $product_fullname = [];
            $product_price = '';
            $product_discount = '';
            $product_weight = '';
            $j = 0;
            $product_bundles = [];
            foreach($products as $k=>$v){
                if($v['bundle_name'] && $v['bundle_name'] != '') $product_bundles[] = $v['bundle_name'];
                $product_code .= ($j > 0 ? '<br>' : '') . $v['qty'] . '-' . $v['code'];
                $product_price .= ($j > 0 ? '<br>' : '') . str_replace(',','',$v['product_price']);
                if($v['discount_amount']){
                    $v['discount_amount'] = str_replace(',','',$v['discount_amount']);
                    $product_discount .= ($j > 0 ? '<br>' : '') . ($v['discount_amount']?($v['discount_amount']):0);
                }
                $product_weight .= ($j > 0 ? '<br>' : '') . ($v['weight']?$v['weight']:0);
                $product_str[] = $v['qty'] . ($show_product_detail ? ' ' . $v['code'] : ''). ' - ' . $v['name']
                    . ($show_product_detail && $v['size'] ? ' size ' . $v['size']  : '')
                    . ($show_product_detail && $v['color'] ? ' màu ' . $v['color'] : '');


                // Gắn tên đầy đủ nếu có setting
                if(AdminOrders::$show_full_name_export_excel_order && isset($v['full_name'])){
                    $product_fullname[] = $v['qty']
                        . ($show_product_detail ? ' ' . $v['code'] : '')
                        . ' - ' . $v['full_name']
                        . ($v['size'] ? ' size ' . $v['size']  : '')
                        . ($v['color'] ? ' màu ' . $v['color'] : '');
                }

                $j++;
            }
            $items[$key]['product_bundles'] = implode(', ', $product_bundles);
            $items[$key]['products'] = implode('<br>', $product_str);
            $items[$key]['products_fullname'] = implode('<br>', $product_fullname);
            $items[$key]['product_code'] = $product_code;
            $items[$key]['discount_amount'] = $product_discount;
            $items[$key]['product_price'] = $product_price;
            $items[$key]['product_weight'] = $product_weight;
            $items[$key]['product_bundle'] = $value['bundle_name'];
            if($value['fb_customer_id']){
                $items[$key]['fb_customer_id'] = '<a class="btn btn-primary btn-sm" href="https://www.facebook.com/'.$value['fb_customer_id'].'" target="_blank"><i class="fa fa-facebook-square"></i> FB KH</a>';
            }
            if($value['fb_post_id']){
                $items[$key]['plain_fb_post_id'] = $value['fb_post_id'];
                $items[$key]['fb_post_id'] = '<a class="btn btn-primary btn-sm" href="https://www.facebook.com/'.$value['fb_post_id'].'" target="_blank"><i class="fa fa-facebook-square"></i> Bài Post</a>';
            }
            if($no_style==false){
                $source_name = $value['source_name'].'<br><span style="color:#999;font-size:11px;">('.(($value['type']==2)?'CSKH':'SALE').')</span>';
            }else{
                $source_name = $value['source_name'].' ('.(($value['type']==2)?'CSKH':'SALE').')';
            }
            $items[$key]['source'] = $source_name;

            $items[$key]['duplicate_note'] = AdminOrdersDB::get_dupplicates($key,$value['mobile'],$duplicate_type,$duplicate_type?$value['bundle_id']:0,$no_style);

            $items[$key]['code'] = str_pad($key,6,'0',STR_PAD_LEFT);
            $bar_code = ($pre).$items[$key]['code'];

            $items[$key]['bar_code_id'] = $value['id'];
            $items[$key]['bar_code'] = $bar_code;
            $items[$key]['postal_bar_code'] = $items[$key]['postal_code'];
            $items[$key]['pre'] = $pre;
            $items[$key]['total_price'] = $value['total_price'];
            $isEdited = '';
            if(isset($getEdited[$key])){
                $isEdited = $getEdited[$key]['last_edited_account_id'];
            }
            $items[$key]['editting'] = $isEdited;
            ///
            if($value['fb_page_id']){
                if($page = DB::fetch('select id,page_name,page_id from fb_pages where page_id="'.$value['fb_page_id'].'"')){
                    $page_name = $page['page_name'];
                    $items[$key]['page'] = $page_name?'<a class="btn btn-primary btn-sm" href="https://www.facebook.com/'.$page['page_id'].'" target="_blank"><i class="fa fa-facebook-square"></i> '.$page_name.'</a>':'';
                }else{
                    $items[$key]['page'] = '<a class="btn btn-primary btn-sm" href="https://www.facebook.com/'.$value['fb_page_id'].'" target="_blank"><i class="fa fa-facebook-square"></i> FB Page</a>';
                }
            }else{
                $items[$key]['page'] = '';
            }
            // for customer
            $customer_name = $value['customer_name'];
            $items[$key]['customer_name'] = $customer_name;
            $i++;
        }
        return ($items);
    }

        static function getProductOrder($orderIds){
            $strOrderIds = implode(',', $orderIds);
            $sql = "
                        SELECT
                            orders_products.id,orders_products.order_id,
                            orders_products.product_id,
                            orders_products.product_price,
                            orders_products.discount_amount,
                            orders_products.weight,
                            orders_products.height,
                            orders_products.width,
                            orders_products.length,
                            orders_products.warehouse_id,
                            orders_products.qty,
                            products.name,products.name as product_name,products.color,products.size,products.code,
                            orders_products.which_process, orders_products.staff_rating, orders_products.customer_rating,
                            units.name AS units_name,
                            master_product.full_name,
                            bundles.name AS bundle_name
                        FROM
                            orders_products
                        JOIN 
                            products ON products.id = orders_products.product_id
                        LEFT JOIN 
                            units ON units.id = products.unit_id
                        LEFT JOIN 
                            master_product ON master_product.code = products.code
                        LEFT JOIN 
                            bundles ON bundles.id = products.bundle_id
                        WHERE
                            orders_products.order_id IN ($strOrderIds) 
                        order by
                            orders_products.product_price DESC
                ";

            $order_product = DB::fetch_all($sql);
            $newArray = [];
            foreach($order_product as $key=>$value){
                $discount_amount = !empty($value['discount_amount']) ? $value['discount_amount'] : 0;
                $discount_amount = $value['qty'] > 0 ? $discount_amount / $value['qty'] : 0;
                $newArray[$value['order_id']][$key] = $value;
                $newArray[$value['order_id']][$key]['discount_price'] = System::display_number($discount_amount) ;
                $newArray[$value['order_id']][$key]['product_price'] = System::display_number($value['product_price']);
                $newArray[$value['order_id']][$key]['product_price_hidden'] = System::display_number($value['product_price']);
                $newArray[$value['order_id']][$key]['total'] = System::display_number(($value['product_price'] - $discount_amount) *$value['qty']);
            }
            return $newArray;
        }

        static function getEdited($orderIds){
            $escape_ids = array_map(function($id){
                return DB::escape($id);
            }, $orderIds);

            $strOrderIds = implode(',', $escape_ids);
            $sql = 'SELECT 
                            id,last_edited_account_id 
                    FROM 
                        orders 
                    WHERE 
                        id IN ('.DB::escape($strOrderIds).') 
                    AND 
                        (orders.group_id='.Session::get('group_id').' '.(AdminOrders::$master_group_id?' OR orders.master_group_id='.AdminOrders::$master_group_id.'':'').') 
                    AND 
                        (last_online_time>'.(time()-1800).') 
                    AND 
                        last_edited_account_id <> "'.Session::get('user_id').'"';
            $data = DB::fetch_all($sql);
            return $data;
        }

        /**
         * Gets the filter.
         *
         * @param      string     $filterName  The filter name
         * @param      array      $options     The options
         *
         * @throws     Exception  Nếu không tìm thấy filter name chỉ định
         *
         * @return     string     The filter.
         */
    private static function getFilter(string $filterName, array $options)
    {
        switch($filterName)
        {
            case 'duplicate':
                return self::getDuplicatesFilter($options);
        }

        throw new Exception('Không tồn tại filter !');
    }

    /**
     * Gets the duplicates filter.
     *
     * @param      int    $case     The case
     * @param      int    $type     The type
     * @param      array  $options  The options
     *
     * @return     array  The duplicates filter.
     */
    private static function getDuplicatesFilter(array $options)
    {
        $queryParams = ['select' => '', 'join' => '', 'where' => ''];

        ['case' => $case, 'type' => $type, 'group_id' => $group_id] = $options;

        switch($case)
        {
            case self::FILTER_DUPLIATE_ONLY:
                return self::_getFilterDuplicate($queryParams, $case, $type, $group_id);

            case self::FILTER_DUPLIATE_NOT:
                return self::_getFilterDuplicate($queryParams, $case, $type, $group_id);

            case self::FILTER_DUPLIATE_NONE:
            default:
                return $queryParams;
        }
    }


    /**
     * Gets the filter duplicate.
     *
     * @param      array  $queryParams  The query parameters
     * @param      int    $case         The case
     * @param      int    $type         The type
     * @param      int    $group_id     The group identifier
     *
     * @return     array  The filter duplicate.
     */
    private static function _getFilterDuplicate(array $queryParams, int $case, int $type, int $group_id)
    {
        $joinCondition = '';
        // Trường hợp chỉ lấy trùng ta sẽ để điều kiện join là id trên bảng gốc và bảng tạm khác nhau
        // mục đích để chỉ lấy các số bị trùng mà không lấy số đầu tiên
        if($case === self::FILTER_DUPLIATE_ONLY){
            $joinCondition = 'AND orders.id != orders_tmp.id';
        }

        $queryParams['join'] = sprintf(
            'JOIN %s ON orders.mobile = orders_tmp.mobile AND orders_tmp.group_id = orders.group_id %s',
            self::getDuplicateFilterTabelTmp($group_id, $type),
            $joinCondition
        );

        if($case === self::FILTER_DUPLIATE_ONLY){
            if($type ===self::DUPLIDATE_TYPE_PHONE){
                $queryParams['where'] = 'AND (orders_tmp.count > 1)';
            }

            if($type ===self::DUPLIDATE_TYPE_PHONE_BUNDLE){
                $queryParams['where'] = 'AND (orders_tmp.count > 1 AND orders_tmp.bundle_id > 0 AND orders.bundle_id = orders_tmp.bundle_id)';
            }
        }

        elseif($case === self::FILTER_DUPLIATE_NOT){
            if($type ===self::DUPLIDATE_TYPE_PHONE){
                $queryParams['where'] = 'AND (orders_tmp.count = 1 OR orders_tmp.id = orders.id)';
            }
            // đoạn logic này cực kì khoai, ko nghịch :3
            if($type ===self::DUPLIDATE_TYPE_PHONE_BUNDLE){
                $queryParams['where'] = 'AND (
                orders_tmp.count = 1 AND orders.bundle_id = orders_tmp.bundle_id 
                OR ( orders_tmp.bundle_id = 0 AND  orders.bundle_id = orders_tmp.bundle_id)
                OR ( orders_tmp.bundle_id IS NULL AND  orders.bundle_id IS NULL)
                OR orders_tmp.id = orders.id
            )';
            }
        }

        return $queryParams;
    }

    /**
     * Gets the duplicate filter tabel temporary.
     *
     * @param      int     $group_id  The group identifier
     * @param      <type>  $type      The type
     *
     * @return     <type>  The duplicate filter tabel temporary.
     */
    private static function getDuplicateFilterTabelTmp(int $group_id, int $type)
    {
        // Khởi tạo một bảng tạm chứa các số điện thoại unique và số lần xuất hiện của nó
        return sprintf('( 
                    SELECT count(o1.id) as count, o1.mobile , o1.group_id, o1.bundle_id, o1.id 
                    FROM orders o1 
                    WHERE o1.group_id = %d 
                    GROUP BY o1.mobile %s
                    ORDER BY count DESC
                ) orders_tmp',
            $group_id,
            $type == self::DUPLIDATE_TYPE_PHONE_BUNDLE ? ', o1.bundle_id' : ''
        );
    }

    /**
     * Determines if first order.
     *
     * @param      int         $orderID   The order id
     * @param      array|bool  $orderIDs  The order i ds
     *
     * @return     bool        True if first order, False otherwise.
     */
    private static function isFirstOrder(int $orderID, array $orderIDs)
    {
        return $orderIDs ? $orderID == min(array_merge([$orderID], array_keys($orderIDs))) : false;
    }

    /**
     * Gets the request parameter duplidate.
     *
     * @return     <type>  The request parameter duplidate.
     */
    private static function getRequestParamDuplidate()
    {
        return URL::iget('duplicate');
    }

    /**
     * Gets the limit offset clause.
     *
     * @param      int     $itemPerPage  The item per page
     *
     * @return     <type>  The limit offset clause.
     */
    private static function getLimitOffsetClause($itemPerPage)
    {
        if(!$itemPerPage = intval($itemPerPage)){
            return;
        }

        $limit = isset($_REQUEST['offset']) ? intval($_REQUEST['offset']) : (page_no()-1) * $itemPerPage;

        return sprintf(' LIMIT %d,%d', $limit < 0 ? 0 : $limit, $itemPerPage);
    }

    public static function get_users($code=false,$account_group_id=false,$by_user_id=false,$restrict_user=false){
        $code = DB::escape($code);
        $account_group_id = DB::escape($account_group_id);
        $account_id = Session::get('user_id');
        $group_id = Session::get('group_id');
        $admin_group = Session::get('admin_group');
        $master_group_id = Session::get('master_group_id');
        $account_type = Session::get('account_type');
        $cond = 'account.is_active=1
                '.((Session::get('account_type')==TONG_CONG_TY)?' AND (groups.master_group_id='.$group_id.' OR account.group_id = '.$group_id.')':'AND account.group_id = '.$group_id.'').'
                '.(($code)?'AND roles_to_privilege.privilege_code="'.$code.'"':'').'
                '.(($account_group_id)?'AND account.account_group_id="'.$account_group_id.'"':'').'';
        //------------Check quyen truong nhom--------------
        if($restrict_user and !$admin_group){
            if(is_account_group_manager()){
                $account_group_ids = get_account_group_ids();
                if($account_group_ids){
                    $cond .= ' AND account.account_group_id IN ('.$account_group_ids.')';
                }
            }else{
                if($account_group_id=DB::fetch('select account_group_id from account where id="'.$account_id.'"','account_group_id')){
                    $cond .= ' AND account.account_group_id = '.$account_group_id.'';
                }
            }
        }
        //------------end Check quyen truong nhom--------------
        $sql = '
            SELECT
                '.($by_user_id?'users.id':'account.id,users.id as user_id').',account.group_id,
                '.(($account_type==TONG_CONG_TY)?'CONCAT(CONCAT(party.full_name,IF(account.admin_group=1," (admin)","")),": cty ",groups.name)':'party.full_name').' AS full_name
            FROM
                account
                JOIN `groups` ON groups.id = account.group_id
                JOIN party ON party.user_id = account.id
                JOIN users ON users.username = account.id
                '.(($code)?'
                JOIN users_roles ON users_roles.user_id = users.id
                JOIN roles_to_privilege ON roles_to_privilege.role_id = users_roles.role_id
                ':'').'
            WHERE
                '.$cond.'
            ORDER BY
                account.group_id
        ';
        if(!System::is_local() and $admin_group) {
            if ($admin_group) {
                if ($account_type == TONG_CONG_TY) {
                    $m_key = 'users_' . $master_group_id . '_' . $group_id;
                } else {
                    $m_key = 'users_' . $group_id;
                }
            } else {
                $m_key = 'users_' . $group_id . '_' . get_user_id();
            }
            if($code){
                $m_key .= '_'.$code;
            }
            if($by_user_id){
                $m_key .= '_'.$code.'_'.$by_user_id;
            }
            if ($m_key) {
                $get_mc = MC::get_items($m_key);
                if ($get_mc) {
                    $items = $get_mc;
                } else {
                    $items = DB::fetch_all($sql);
                    MC::set_items($m_key, $items, time() + 300);
                }
            } else {
                $items = DB::fetch_all($sql);
            }
        }else{
            $items = DB::fetch_all($sql);
        }
        return $items;
    }
    public static function get_user_all($code=false,$account_group_id=false,$by_user_id=false,$restrict_user=false){
        $code = DB::escape($code);
        $account_group_id = DB::escape($account_group_id);
        $account_id = Session::get('user_id');
        $group_id = Session::get('group_id');
        $admin_group = Session::get('admin_group');
        $master_group_id = Session::get('master_group_id');
        $account_type = Session::get('account_type');
        $cond = '1 '
            . ((Session::get('account_type')==TONG_CONG_TY)?' AND (groups.master_group_id='.$group_id.' OR account.group_id = '.$group_id.')':'AND account.group_id = '.$group_id.'').'
                '.(($code)?'AND roles_to_privilege.privilege_code="'.$code.'"':'').'
                '.(($account_group_id)?'AND account.account_group_id="'.$account_group_id.'"':'').'';
        //------------Check quyen truong nhom--------------
        if($restrict_user && !$admin_group){
            if(is_account_group_manager()){
                $account_group_ids = get_account_group_ids();
                if($account_group_ids){
                    $cond .= ' AND account.account_group_id IN ('.$account_group_ids.')';
                }
            }else{
                if($account_group_id=DB::fetch('select account_group_id from account where id="'.$account_id.'"','account_group_id')){
                    $cond .= ' AND account.account_group_id = '.$account_group_id.'';
                }
            }
        }
        if ($master_group_id) {
            $cond .= ' AND users.group_id ='.$master_group_id.'';
        } else {
            $cond .= ' AND users.group_id ='.$group_id.'';
        }
        //------------end Check quyen truong nhom--------------
        $sql = '
            SELECT
                '.($by_user_id?'users.id':'account.id,users.id as user_id').',account.group_id, account.is_active,
                '.(($account_type==TONG_CONG_TY)?'CONCAT(CONCAT(party.full_name,IF(account.admin_group=1," (admin)","")),": cty ",groups.name)':'party.full_name').' AS full_name
            FROM
                account
                JOIN `groups` ON groups.id = account.group_id
                JOIN party ON party.user_id = account.id
                JOIN users ON users.username = account.id
                '.(($code)?'
                JOIN users_roles ON users_roles.user_id = users.id
                JOIN roles_to_privilege ON roles_to_privilege.role_id = users_roles.role_id
                ':'').'
            WHERE
                '.$cond.'
            ORDER BY
                account.group_id
        ';
        if(!System::is_local() && $admin_group) {
            $m_key = md5($sql);
            $items = MC::get_items($m_key);

            if (!$items) {
                $items = DB::fetch_all($sql);
                MC::set_items($m_key, $items, time() + 300);
            }
        } else {
            $items = DB::fetch_all($sql);
        }

        return $items;
    }
    static function get_category($order_id=false){
        $order_id = DB::escape($order_id);
        $categories =  DB::fetch_all('
            SELECT
                category.id
                ,category.name_'.Portal::language().' as name
                ,category.structure_id
                '.($order_id?',product_category.product_id':'').'
            FROM
                category
                '.($order_id?'LEFT JOIN product_category ON product_category.category_id = category.id AND product_category.product_id='.$order_id.'':'').'
            WHERE
                category.type="PRODUCT"
                and category.structure_id <> '.ID_ROOT.'
            ORDER BY
                category.structure_id
        ');
        return $categories;
    }
    static function get_categories($order_id){
        $str = '';
        $sql = 'select category.id,category.name_'.Portal::language().' as name from category JOIN product_category as pc on pc.category_id=category.id where pc.product_id='.$order_id.'';
        $items = DB::fetch_all($sql);
        foreach($items as $key=>$value){
            $str .= ($str?',':'').$value['name'];
        }
        return $str;
    }
    static function get_status_from_roles($user_id){
        $user_id = DB::escape($user_id);
        $sql = '
                SELECT
                    statuses.id, statuses.no_revenue,
                    IF(statuses.level>0,CONCAT(statuses.level,". ",statuses.name),statuses.name) AS name,
                    IF(statuses.is_system=1,statuses.level,statuses_custom.level) as level,
                    IF(statuses.is_system=1,statuses.color,statuses_custom.color) as color
                FROM
                    statuses  
                    JOIN roles_statuses ON roles_statuses.status_id = statuses.id
                    JOIN roles ON roles.id = roles_statuses.role_id
                    JOIN users_roles ON users_roles.role_id = roles.id
                    LEFT JOIN statuses_custom ON statuses_custom.status_id = statuses.id
                WHERE
                    users_roles.user_id='.$user_id.'
                ORDER BY 
                    IF(statuses.is_system=1,statuses.level,statuses_custom.level),
                    statuses_custom.position,
                    statuses.id
        ';
        return DB::fetch_all($sql);
    }
    static function get_status(){
        $group_id = Session::get('group_id');
        $master_group_id = AdminOrders::$master_group_id;
        $m_key = 'statuses_'.$group_id;
        if(Session::get('account_type')==TONG_CONG_TY){//khoand edited in 30/09/2018
            $cond = ' (groups.id='.$group_id.')';
        }elseif($master_group_id){
            $cond = ' (groups.id = '.$master_group_id.')';
            $m_key = 'statuses_'.$master_group_id;
        }else{
            $cond = ' groups.id='.$group_id.'';
        }
        $sql = '
            SELECT 
                statuses.id, statuses.no_revenue,
                IF(statuses.level>0,CONCAT(statuses.level,". ",statuses.name),statuses.name) AS name,
                IF(statuses.is_system=1,statuses.level,statuses_custom.level) as level,
                IF(statuses.is_system=1,statuses.color,statuses_custom.color) as color
            FROM 
                `statuses`
                JOIN `groups` ON groups.id = statuses.group_id
                LEFT JOIN statuses_custom ON statuses_custom.status_id = statuses.id
            WHERE 
                '.$cond.' OR is_system=1
            GROUP BY
                statuses.id
            ORDER BY 
                IF(statuses.is_system=1,statuses.level,statuses_custom.level),
                statuses_custom.position,
                statuses.id
        ';

        if(!System::is_local()){
            if(!$items = MC::get_items($m_key)){
                $items = DB::fetch_all($sql);
                MC::set_items($m_key,$items,time() + 3600);
            }
        }else{
            $items = DB::fetch_all($sql);
        }
        return $items;
    }

    static function get_order_product_qty($order_id){
        return DB::fetch('select sum(orders_products.qty) as total from orders_products where orders_products.order_id='.$order_id,'total');
    }
    static function update_order_product($order_id, $created){
        $order_id = DB::escape($order_id);
        $DISABLE_PRODUCT_PRICE_DATE = AdminOrders::DISABLE_PRODUCT_PRICE_DATE;
        $flagDateDisableEdit = 1;
        $cmd = Url::get('cmd');
        if($cmd == 'edit'){
            if(strtotime($created) >= strtotime($DISABLE_PRODUCT_PRICE_DATE)){
                $flagDateDisableEdit = 0;
            }
        }
        if(isset($_REQUEST['mi_order_product'])){
            foreach($_REQUEST['mi_order_product'] as $key=>$record){
                if($record['id']=='(auto)'){
                    $record['id']=false;
                }
                $productPrice = $record['product_price']?System::calculate_number($record['product_price']):0;
                $productId = intval($record['select_product_id']);
                unset($record['select_product_id']);
                unset($record['product_price_hidden']);
                if($record['product_name']){
                    $record['order_id'] = $order_id;
                    $record['group_id'] = AdminOrders::$group_id;
                    $record['qty'] = !empty($record['qty'])?$record['qty']:1;
                    $record['product_price'] = $record['product_price']?System::calculate_number($record['product_price']):0;
                    $record['discount_price'] = !empty($record['discount_price'])?System::calculate_number($record['discount_price']):0;
                    $record['discount_amount'] = $record['qty'] * $record['discount_price'];
                    unset($record['discount_price']);
                    unset($record['color']);
                    unset($record['size']);
                    unset($record['total']);
                    $record['weight'] = (isset($record['weight']) and $record['weight'])?$record['weight']:0;
                    $record['width'] = !empty($record['width'])?$record['width']:0;
                    $record['height'] = !empty($record['height'])?$record['height']:0;
                    $record['length'] = !empty($record['length'])?$record['length']:0;
                    $record['which_process'] = !empty($record['which_process'])?$record['which_process']:0;
                    $record['staff_rating'] = !empty($record['staff_rating'])?$record['staff_rating']:0;
                    $record['customer_rating'] = !empty($record['customer_rating'])?$record['customer_rating']:0;
                    $data = '';
                    if($record['id'] and $orders_product=DB::select('orders_products','id='.DB::escape($record['id']))){
                        $product = DB::select('products','id='.$productId);
                        $current_product_name = $orders_product['product_id']?DB::fetch('select id,name from products where id='.$orders_product['product_id'],'name'):'';
                        if($record['product_name'] and $current_product_name and $current_product_name != $record['product_name']){
                            $data .= 'Update sản phẩm <strong>'.$current_product_name.'</strong> thành <strong>'.$record['product_name'].'</strong> ';
                        }
                        if($orders_product['qty'] != $record['qty']){
                            $data .= ($data?', ':'').'thay đổi số lượng sp '.$current_product_name.' từ '.$orders_product['qty'].' thành '.$record['qty'];
                        }
                        if($product && $product['price'] != $productPrice){
                            $data .= ($data?', ':'').'thay đổi giá sp '.$product['name'].' từ '.$product['price'].' thành '.$productPrice;
                        } else if(!$product && $orders_product['product_price'] != $record['product_price']){
                            $data .= ($data?', ':'').'thay đổi giá sp '.$current_product_name.' từ '.$orders_product['product_price'].' thành '.$record['product_price'];
                        }
                        if($record['discount_amount'] != $orders_product['discount_amount'] && !empty($orders_product['discount_amount'])){
                            $data .= ($data?', ':'').'Thay đổi giảm giá sp <strong>'.$product['name'].'</strong> từ <strong>'.$orders_product['discount_amount'].'</strong> thành <strong>'.$record['discount_amount'].'</strong>';
                        }
                        if($record['discount_amount'] != 0 && empty($orders_product['discount_amount'])){
                            $data .= ($data?', ':'').'Thêm giảm giá sp <strong>'.$product['name'].'</strong> là:'.$record['discount_amount'].'';
                        }
                        if($data){
                            $data .= '<br>';
                        }

                        if($flagDateDisableEdit == 0){
                            if($product){
                                if($product['id'] == $orders_product['product_id']){
                                    $record['product_price'] = $product['price'];
                                    $record['product_name'] = $product['name'];
                                }
                            } else {
                                $record['product_price'] = $orders_product['product_price'];
                                $record['product_name'] = $orders_product['product_name'];
                            }
                        }
                        DB::update('orders_products',$record,'id='.DB::escape($record['id']));
                    }else{
                        $product = DB::select('products','id='.$productId);
                        $warehouseNew = DB::select('qlbh_warehouse','id='.$record['warehouse_id']);
                        unset($record['id']);
                        if (Url::get('cmd') == 'add') {
                            if($record['discount_amount'] != 0){
                                $data .= 'Thêm sản phẩm '.$record['product_name'].', giá: '.$record['product_price'].', số lượng: '.$record['qty'].', giảm giá: '.$record['discount_amount'].' Kho:' .$warehouseNew['name'];
                            } else {
                                $data .= 'Thêm sản phẩm '.$record['product_name'].', giá: '.$record['product_price'].', số lượng: '.$record['qty'].', Kho: ' .$warehouseNew['name'];
                            }
                        }
                        if(Url::get('cmd') == 'edit'){
                            if($record['discount_amount'] != 0){
                                $data .= 'Thêm sản phẩm '.$record['product_name'].', giá: '.$record['product_price'].', số lượng: '.$record['qty'].', giảm giá: '.$record['discount_amount'].' Kho:' .$warehouseNew['name'];
                            } else {
                                $data .= 'Thêm sản phẩm '.$record['product_name'].', giá: '.$record['product_price'].', số lượng: '.$record['qty'].', Kho: ' .$warehouseNew['name'];
                            }
                            if($data){
                                $data .= '<br>';
                            }
                        }
                        if($product){
                            $record['product_name'] = $product['name'];
                            $record['product_price'] = $product['price'];
                        }

                        $record['id'] = DB::insert('orders_products',$record);

                    }
                    if($data){
                        AdminOrdersDB::update_revision($order_id,false,false,$data);
                    }
                    if(isset($record['product_id']) and $record['product_id']){
                        $total_order =  DB::fetch('select count(id) as total from orders_products where product_id="'.DB::escape($record['product_id']).'"','total');
                        DB::update('products',array('total_order'=>$total_order),'id='.DB::escape($record['product_id']));
                    }
                }
            }
            if (isset($ids) and sizeof($ids)){
                $_REQUEST['selected_ids'].=','.join(',',$ids);
            }
        }
        if($deleted_ids=Url::post('deleted_ids')){
            $ids = explode(',',$deleted_ids);
            $data = '';
            foreach($ids as $del_id){
                if($orders_product=DB::select('orders_products','id='.DB::escape($del_id))){
                    $current_product_name = $orders_product['product_id']?DB::fetch('select id,name from products where id='.$orders_product['product_id'],'name'):'';
                    if($current_product_name){
                        $data .= 'Xóa sản phẩm '.$current_product_name;
                    }
                    DB::delete_id('orders_products',$del_id);
                }
            }
            if($data){
                AdminOrdersDB::update_revision($order_id,false,false,$data);
            }
        }
        $total_product_qty = AdminOrdersDB::get_order_product_qty($order_id);
        DB::update('orders',array('total_qty'=>$total_product_qty?$total_product_qty:0),'id='.$order_id);
    }

    static function get_products($cond='1=1'){
        $master_group_id = Session::get('master_group_id');
        $group_id = Session::get('group_id');
        $sql = '
                select
                    products.id,
                    '.($master_group_id?'CONCAT(products.name," / ",groups.name) as name':'products.name').',
                    products.color,products.size,
                    products.price,
                    products.code,
                    products.width,
                    products.height,
                    products.weight,
                    labels.name as label,
                    units.name as unit,
                    bundles.name as bundle
                from
                    products
                    JOIN `groups` ON groups.id = products.group_id
                    LEFT JOIN units ON units.id=products.unit_id
                    LEFT JOIN bundles ON bundles.id=products.bundle_id
                    LEFT JOIN labels ON labels.id=products.label_id
                WHERE
                    '.$cond.'
                    AND (products.group_id='.$group_id.' '.($master_group_id?' OR groups.id='.$master_group_id:'').')
                    AND (products.del is null or products.del = 0)
                order by
                    products.name
            ';
        $products = DB::fetch_all($sql);
        $index = 0;
        foreach($products as $key=>$value){
            $products[$key]['index'] = ++$index;
            $products[$key]['price'] = System::display_number($value['price']);
        }
        return $products;
    }

    static function get_order_product($order_id,$group=true){
        {
            $order_id = DB::escape($order_id);
            $sql = '
                select
                    orders_products.id,orders_products.order_id,
                    orders_products.product_id,
                    orders_products.product_price,
                    orders_products.discount_amount,
                    orders_products.weight,
                    orders_products.height,
                    orders_products.width,
                    orders_products.length,
                    orders_products.warehouse_id,
                    '.($group?'sum(orders_products.qty) as qty':'orders_products.qty').',
                    products.name,products.name as product_name,products.color,products.size,products.code,
                    orders_products.which_process, orders_products.staff_rating, orders_products.customer_rating,
                    units.name AS units_name,
                    master_product.full_name
                from
                    orders_products
                    JOIN products ON products.id = orders_products.product_id
                    LEFT JOIN units ON units.id = products.unit_id
                    LEFT JOIN master_product ON master_product.code = products.code
                WHERE
                    orders_products.order_id='.$order_id.'
                '.($group?'GROUP BY orders_products.product_id':'').'   
                order by
                    orders_products.product_price DESC
            ';
            $order_product = DB::fetch_all($sql);
            foreach($order_product as $key=>$value){
                $discount_amount = !empty($value['discount_amount']) ? $value['discount_amount'] : 0;
                $discount_amount = $value['qty'] > 0 ? $discount_amount / $value['qty'] : 0;
                $order_product[$key]['discount_price'] = System::display_number($discount_amount) ;
                $order_product[$key]['product_price'] = System::display_number($value['product_price']);
                $order_product[$key]['product_price_hidden'] = System::display_number($value['product_price']);
                $order_product[$key]['total'] = System::display_number(($value['product_price'] - $discount_amount) *$value['qty']);
            }
            return $order_product;
        }
    }

    /**
     * getSystemBundles function
     *
     * @return array
     */
    static function getSystemBundles(): array
    {
        $sql = "SELECT `id`, `name`
            FROM  bundles
            WHERE group_id = 0 AND standardized = 1
            ORDER BY `name`";

        return DB::fetch_all($sql);
    }

    static function get_bundles($group_id=false){
        $group_id = DB::escape($group_id)? $group_id:Session::get('group_id');
        $sql = '
                select
                    bundles.id,bundles.name
                from
                    bundles
                WHERE
                    bundles.group_id='.$group_id.'
                order by
                    bundles.name
            ';
        $bundles = DB::fetch_all($sql);
        return $bundles;
    }

    static function get_source(){
        $group_id = Session::get('group_id');
        $master_group_id = Session::get('master_group_id');
        $account_type = Session::get('account_type');
        $cond = '1=1';
        if($account_type==TONG_CONG_TY){//khoand edited in 30/09/2018
            $cond .= ' and (groups.id = '.$group_id.')';
        }elseif($master_group_id){
            $cond.= ' and (groups.id = '.$master_group_id.')';
        }else{
            $cond.= ' and groups.id = '.$group_id.'';
        }
        $cond  .= '
            OR order_source.group_id=0
        ';
        $sql = '
            select
                order_source.id,order_source.name,order_source.default_select
            from
                order_source
                left join `groups` on groups.id=order_source.group_id
            where
                '.$cond.'
            order by
                order_source.id
        ';
        $order_source = DB::fetch_all($sql);
        //System::Debug($sql);
        return $order_source;
    }

    static function getSystemSources(int $includeSourceId = 0) {
        $cols = ['id', 'name'];
        $select = generateColumns($cols);
        $query = "SELECT $select
            FROM order_source
            WHERE `group_id` = 0
                AND `ref_id` = 0
                AND `name` IS NOT NULL
                AND `name` <> ''
        ";

        if ($includeSourceId) {
            $query .= " OR `id` = $includeSourceId";
        }//end if

        $query = formatQuery($query);
        return DB::fetch_all($query);
    }

    static function get_source_shop(){
        $results = [];
        $groupId = Session::get('group_id');
        $key = 'source_shop_ids';
        $share = DB::fetch('select id,value,group_id from group_options where `key`= "' . $key . '" and group_id = '. $groupId);
        if($share){
            $strIds = $share['value'];
            $sql = "
                select
                   id, name
                from
                    groups
                where
                    groups.id IN ($strIds)
                order by
                    groups.id
            ";
            $results = DB::fetch_all($sql);
        }
        return $results;
    }

    public static function update_revision($order_id,$old_status_id=false,$new_status_id=false,$data='')
    {
        if ( defined('LOG_V2') && !empty(LOG_V2)) {
            return self::update_revision_v2($order_id,$old_status_id,$new_status_id,$data);
        } else {
            return self::update_revision_v1($order_id,$old_status_id,$new_status_id,$data);
        }
    }

    public static function get_order_revisions($page=1,$order_id=false)
    {
        if ( defined('LOG_V2') && !empty(LOG_V2)) {
            return self::get_order_revisions_v2($page,$order_id);
        }
        return self::get_order_revisions_v1($page,$order_id);
    }

    public static function update_revision_v1($order_id,$old_status_id=false,$new_status_id=false,$data='')
    {
        $order_id = DB::escape($order_id);
        $old_status_id = DB::escape($old_status_id);
        $new_status_id = DB::escape($new_status_id);
        $user_id = DB::fetch('select id from users where username="'.Session::get('user_id').'"','id');
        $thanh_cong = 5;
        if($old_status_id and $new_status_id){// log trạng thái
            if($old_status_id and $new_status_id and $old_status_id != $new_status_id){
                $before_order_status = DB::fetch('select id,name from statuses where id='.$old_status_id,'name');
                $order_status = DB::fetch('select id,name from statuses where id='.$new_status_id,'name');
                $order = DB::fetch('select id,total_price from orders where id='.$order_id);
                if($new_status_id==$thanh_cong){
                    $body = 'Đơn hàng #'.$order['id'].' đã được thanh toán thành công với trị giá '.System::display_number($order['total_price']).'đ';
                    notify_to_group(Session::get('group_id'),'ORDER','QLBH: ĐƠN HÀNG THÀNH CÔNG',$body);
                }
                return DB::insert('order_revisions',array(
                    'order_id'=>$order_id,
                    'before_order_status_id'=>$old_status_id,
                    'before_order_status'=>$before_order_status,
                    'order_status_id'=>$new_status_id,
                    'order_status'=>$order_status?$order_status:'',
                    'user_created_name'=>Session::get('user_id'),
                    'user_created'=>$user_id,
                    'created'=>date('Y-m-d H:i:s')
                ));
            }
        }else{
            // Trường hợp log khác
            if($data){
                return DB::insert('order_revisions',array(
                    'order_id'=>$order_id,
                    'data'=> DataFilter::removeXSSinHtml($data),
                    'before_order_status_id'=>0,
                    'before_order_status'=>'',
                    'order_status_id'=>0,
                    'order_status'=>'',
                    'user_created_name'=>Session::get('user_id'),
                    'user_created'=>$user_id,
                    'created'=>date('Y-m-d H:i:s')
                ));
            }
        }
    }

    public static function get_order_revisions_v1($page=1,$order_id=false)
    {
        $order_id = DB::escape($order_id);
        $page = intval($page);
        $group_id = Session::get('group_id');
        $master_group_id = AdminOrders::$master_group_id;
        $per_page = 100;
        $order_id = $order_id?$order_id:Url::iget('id');
        $min_date = DB::fetch('select min(created) as val from order_revisions where order_id='.$order_id,'val');
        $min_time = Date_Time::to_time(date('d/m/Y',strtotime($min_date)));
        $max_date = DB::fetch('select max(created) as val from order_revisions where order_id='.$order_id,'val');
        $max_time = strtotime($max_date);
        $array = array();
        if(Session::get('account_type')==TONG_CONG_TY){
            $cond = '(orders.group_id='.$group_id.' or orders.master_group_id='.$group_id.')';
        }else{
            $cond = '(orders.group_id='.AdminOrders::$group_id.''.(($master_group_id)?' OR orders.master_group_id='.$master_group_id.'':'').')';
        }
        $sql = '
                select
                        order_revisions.id,
                        order_revisions.order_id,
                        order_revisions.data,
                        order_revisions.before_order_status_id,
                        order_revisions.order_status_id,
                        order_revisions.before_order_status,
                        order_revisions.order_status,
                        order_revisions.user_created_name,
                        order_revisions.created,
                        order_revisions.user_created,
                        order_revisions.modified
                    from
                        order_revisions
                        join orders on orders.id = order_revisions.order_id
                    WHERE
                        '.$cond.'
                        and order_revisions.order_id='.$order_id.'
                        and order_revisions.created >= "'.date('Y-m-d',$min_time).' 00:00:00"
                        and order_revisions.created <= "'.date('Y-m-d',$max_time).' 23:59:59"
                    order by
                        order_revisions.id desc
                ';
        $items = DB::fetch_all($sql);
        foreach($items as $key=>$val){
            $created = Date_Time::to_time(date('d/m/Y',strtotime($val['created'])));
            for($i=$max_time;$i>=$min_time;$i=$i-(24*3600)){
                if(date('d/m/Y',$i) == date('d/m/Y',$created)){
                    $array[$i]['id'] = $i;
                    $array[$i]['name'] = date('d/m/Y',$i);
                    $array[$i]['arr'][$val['id']] = $val;
                }
            }
        }

        $total_rows = sizeof($array);
        $from = ($page-1)*$per_page;
        if((($page-1)*$per_page)>$total_rows){
            return array();
        }
        else{
            if((($page-1)*$per_page + $page)>$total_rows){
                $arr = array_slice($array, $from,$total_rows);
            }
            else{
                $arr = array_slice($array, $from, $per_page);
            }
        }
        $result=array();
        for($i=1;$i<=sizeof($arr);$i++){
            $result[$i]=$arr[$i-1];
        }
        return $result;
    }

    static function update_revision_v2($order_id,$old_status_id=false,$new_status_id=false,$data='')
    {
        $order_id = DB::escape($order_id);
        $old_status_id = DB::escape($old_status_id);
        $new_status_id = DB::escape($new_status_id);
        include_once ROOT_PATH.'packages/vissale/lib/php/log.php';

        $user_id = DB::fetch('select id from users where username="' . Session::get('user_id') . '"', 'id');
        $thanh_cong = 5;
        if ($old_status_id and $new_status_id) {// log trạng thái

            if ($old_status_id and $new_status_id and $old_status_id != $new_status_id) {
                $before_order_status = DB::fetch('select id,name from statuses where id=' . $old_status_id . '', 'name');
                $order_status = DB::fetch('select id,name from statuses where id=' . $new_status_id . '', 'name');
                $order = DB::fetch('select id,total_price,group_id from orders where id=' . $order_id . '');
                $groupId = $order['group_id'];
                $groupMasterId = null;
                if (Session::get('account_type') == TONG_CONG_TY) {
                    $groupMasterId = $order['master_group_id'];
                }
                if ($new_status_id == $thanh_cong) {
                    $body = 'Đơn hàng #' . $order['id'] . ' đã được thanh toán thành công với trị giá ' . System::display_number($order['total_price']) . 'đ';
                    notify_to_group(Session::get('group_id'), 'ORDER', 'QLBH: ĐƠN HÀNG THÀNH CÔNG', $body);
                }

                $dataLog = json_encode([
                    'order_id' => $order_id,
                    'before_order_status_id' => $old_status_id,
                    'before_order_status' => $before_order_status,
                    'order_status_id' => $new_status_id,
                    'order_status' => $order_status ? $order_status : '',
                    'user_created_name' => Session::get('user_id'),
                    'user_created' => $user_id,
                    'group_id' => $groupId,
                    'master_group_id' => $groupMasterId,
                    'created' => date('Y-m-d H:i:s')
                ]);
                storeLog($order_id, 'orders', $dataLog);
            }

        } else {
            // Trường hợp log khác
            if ($data) {

                $dataLog = json_encode([
                    'order_id' => $order_id,
                    'data' => DataFilter::removeXSSinHtml($data),
                    'before_order_status_id' => 0,
                    'before_order_status' => '',
                    'order_status_id' => 0,
                    'order_status' => '',
                    'user_created_name' => Session::get('user_id'),
                    'user_created' => $user_id,
                    'group_id' => '',
                    'group_master_id' => '',
                    'created' => date('Y-m-d H:i:s')
                ]);
                storeLog($order_id, 'orders', $dataLog);
            }
        }
    }
    static function get_order_revisions_v2($page=1,$order_id=false){
        $order_id = DB::escape($order_id);
        require_once 'packages/vissale/lib/php/log.php';
        $per_page = 100;
        $order_id = $order_id?$order_id:Url::iget('id');
        $dataLog = getLog($order_id, 'orders', $per_page);
        $array = [];
        if(!empty($dataLog)){
            $min_date = date('Y-m-d h:m:i', end($dataLog)['time_created']);
            $min_time = Date_Time::to_time(date('d/m/Y',strtotime($min_date)));
            $max_date = date('Y-m-d h:m:i', $dataLog[0]['time_created']);
            $max_time = strtotime($max_date);
            $loop = 1;
            foreach($dataLog as $key=>$val){
                for($i=$max_time;$i>=$min_time;$i=$i-(24*3600)){
                    if(date('d/m/Y', $i) == date('d/m/Y', $val['time_created'])){
                        $itemData = json_decode($val['data'], true);
                        $array[$i]['id'] = $i;
                        $array[$i]['name'] = date('d/m/Y',$i);
                        $array[$i]['arr'][$loop] = $itemData;
                        $loop++;
                    }
                }
            }
        }

        $total_rows = count($array);
        $from = ($page-1)*$per_page;
        if((($page-1)*$per_page)>$total_rows){
            return array();
        }
        else{
            if((($page-1)*$per_page + $page)>$total_rows){
                $arr = array_slice($array, $from,$total_rows);
            }
            else{
                $arr = array_slice($array, $from, $per_page);
            }
        }
        $result=array();
        $size = count($arr);
        for($i=1;$i<=$size;$i++){
            $result[$i]=$arr[$i-1];
        }

        return $result;
    }

    static function shipping_services(){
        $sql = '
                select
                    shipping_services.*
                from
                    shipping_services
                WHERE
                    shipping_services.group_id = '.Session::get('group_id').'
                order by
                    shipping_services.name
            ';
        $items = DB::fetch_all($sql);
        return $items;
    }

    static function get_order_info(){
        if(Session::get('user_id')=='PAL.khoand'){
            ini_set('display_errors', 1);
        }
        $party = DB::fetch('select full_name,note1,note2,address,phone,website,image_url from party WHERE user_id="'.Session::get('user_id').'" ');
        $print = DB::fetch('select print_name,print_phone,print_address,template as print_template from order_print_template where group_id='.Session::get('group_id').' order by set_default DESC,order_print_template.id DESC LIMIT 0,1');
        //$party += $print;
        $party['group_image_url'] = '';
        if(empty($print)){
            $party['print_name'] = DB::fetch('select name from `groups` where id='.Session::get('group_id'),'name');
            $party['print_address'] = '';
            $party['print_phone'] = '';
            $party['print_template'] = 1;
        }else{
            $party['print_name'] = $print['print_name'];
            $party['print_address'] = $print['print_address'];
            $party['print_phone'] = $print['print_phone'];
            $party['print_template'] = $print['print_template'];
        }
        $group = DB::fetch('select id,name,image_url from `groups` WHERE id="'.Session::get('group_id').'" ');
        $party['group_image_url'] = $group['image_url'];
        return $party;
    }

    static function is_clicked($id){
        if($id){
            $id = DB::escape($id);
            $row=DB::fetch('select count(orders.id) as total from orders where id='.$id.' and (orders.group_id='.Session::get('group_id').''.((AdminOrders::$master_group_id)?' OR orders.master_group_id='.AdminOrders::$master_group_id.'':'').') and (orders.last_online_time is null OR orders.last_online_time<='.(time()-300).')');
            if($row['total']>0){
                return false;
            }else{
                return true;
            }
        }else{
            return false;
        }
    }

    static function is_edited($id){
        if($id){
            $id = DB::escape($id);
            $row=DB::fetch('select id,last_edited_account_id from orders where id='.$id.' and (orders.group_id='.Session::get('group_id').''.((AdminOrders::$master_group_id)?' OR orders.master_group_id='.AdminOrders::$master_group_id.'':'').') and (last_online_time>'.(time()-1800).') and last_edited_account_id <> "'.Session::get('user_id').'"');
            if ($row){
                return $row['last_edited_account_id'];
            }
        }
        return false;
    }

    static function currentUserEdit($id){
        if($id){
            $order = DB::fetch("SELECT id,last_edited_account_id FROM orders WHERE id = $id");
            if ($order){
                return $order['last_edited_account_id'];
            }
        }
        return false;
    }

    static function get_friendpages($cond='1=1'){
        $sql = '
            SELECT
                fb_pages.id,
                fb_pages.page_id,
                fb_pages.page_name as name,
                fb_pages.page_name
            FROM
                fb_pages
            WHERE
                '.$cond.'
                AND group_id='.Session::get('group_id').'
            ORDER BY
                fb_pages.page_name
            '
        ;
        $items = DB::fetch_all($sql);
        return $items;
    }

    static function update_edited_log($order_id,$new_data){
        $order_id = DB::escape($order_id);
        $text['postal_code'] = 'mã VĐ';
        $text['customer_name'] = 'tên KH';
        $text['mobile'] = 'điện thoại chính';
        $text['mobile2'] = 'điện thoại phụ';
        $text['telco_code'] = 'Mã điện thoại';
        $text['city'] = 'tỉnh/thành';
        $text['address'] = 'địa chỉ';
        $text['note1'] = 'ghi chú chung';
        $text['note2'] = 'ghi chú 2';
        $text['cancel_note'] = 'ghi chú hủy';
        $text['shipping_note'] = 'ghi chú chuyển hàng';
        $text['is_top_priority'] = '';
        $text['is_send_sms'] = '';
        $text['is_inner_city'] = '';
        $text['shipping_service_id'] = 'giao hàng';
        $text['bundle_id'] = 'phân loại';
        $text['price'] = 'thành tiền';
        $text['discount_price'] = 'giảm giá';
        $text['shipping_price'] = 'phí vận chuyển';
        $text['other_price'] = 'Phụ thu';
        $text['code'] = 'Mã FB';
        $text['user_created'] = 'Người tạo';
        $text['fb_page_id'] = 'Fb fanpage ID';
        $text['fb_post_id'] = 'FB post ID';
        $text['fb_customer_id'] = 'Facebook ID của khách';
        $text['confirmed'] = 'Thời gian Xác nhận';
        $text['user_confirmed'] = 'Tài khoản xác nhận';
        $text['type'] = 'Loại Đơn';
        unset($new_data['is_top_priority']);
        unset($new_data['is_send_sms']);
        unset($new_data['is_inner_city']);
        unset($new_data['status_id']);
        unset($new_data['modified']);
        unset($new_data['total_price']);
        unset($new_data['weight']);
        unset($new_data['city_id']);
        unset($new_data['district_id']);
        unset($new_data['ward_id']);
        unset($new_data['source_id']);
        unset($new_data['delivered']);
        unset($new_data['user_delivered']);
        unset($new_data['source_name']);
        unset($new_data['assigned']);
        unset($new_data['user_assigned']);
        unset($new_data['bundle_id']);
        unset($new_data['shipping_service_id']);
        unset($new_data['type']);
        $data = '';
        $order = DB::select('orders','id='.$order_id);
        $user_created =  $order['user_created']?DB::fetch('select id,name from users where id='.$order['user_created'],'name'):'';
        foreach($new_data as $key=>$value){
            if(isset($order[$key]) and ($order[$key] != $value or strlen($order[$key]) != strlen($value))){
                $original = $text[$key];
                if($key == 'user_created'){
                    $from = $user_created;
                    $to = $value?DB::fetch('select id,name from users where id='.$value,'name'):'';
                }else{
                    $from = $order[$key];
                    $to = $value;
                }
                $data .= '<div>Thay đổi '.$original.' từ "'.$from.'" => "'.$to.'"</div>';
            }
        }
        if($data){
            return AdminOrdersDB::update_revision(Url::iget('id'),false,false,$data);
        }
    }

    static function get_condition(){
        $quyen_chia_don = AdminOrders::$quyen_chia_don;
        $quyen_admin_marketing = AdminOrders::$quyen_admin_marketing;
        $quyen_cskh = AdminOrders::$quyen_cskh;
        $quyen_van_don = AdminOrders::$quyen_van_don;
        $quyen_bung_don = AdminOrders::$quyen_bung_don;
        $quyen_bung_don2 = AdminOrders::$quyen_bung_don2;
        $quyen_bung_don_nhom = AdminOrders::$quyen_bung_don_nhom;
        $my_user_id = AdminOrders::$user_id;
        $account_group_ids = get_account_group_ids();//get admin account group
        $search_group_id = Url::iget('search_group_id');
        $account_group_id = Url::get('account_group_id');
        $home_network = Url::get('home_network');
        $ag_cond = '';
        $cr_cond = '';
        if($account_group_ids){
            $user_ids_in_group = get_user_ids_in_group($account_group_ids);
            if($user_ids_in_group and !$account_group_id){
                if(AdminOrders::$account_privilege_code=='MARKETING'){
                    $cr_cond = ' or (orders.user_created IN ('.$user_ids_in_group.'))';
                }else{
                    $ag_cond = ' or (orders.user_assigned IN ('.$user_ids_in_group.'))';
                }
            }
        }
        $group_id = AdminOrders::$group_id;
        $master_group_id = AdminOrders::$master_group_id;
        if($ids = Url::get('checked_order') or $ids=Url::get('ids')){
            $idsArr = explode(',',$ids);
            $escape_ids = array_map(function($id){
                return DB::escape($id);
            }, $idsArr);

            $ids = implode(',', $escape_ids);
            $cond = ' and orders.id IN ('.$ids.')';
            if(AdminOrders::$account_type==TONG_CONG_TY){//khoand edited in 30/09/2018
                $cond .= ' and (groups.id='.$group_id.' or groups.master_group_id = '.$group_id.')';
            }elseif($master_group_id){

            }else{
                $cond .= ' and groups.id='.$group_id.'';
            }
            return $cond;
        }
        //
        $ngay_tao          = self::checkDate(Url::get('ngay_tao_from'), Url::get('ngay_tao_to'));
        $ngay_chia         = self::checkDate(Url::get('ngay_chia_from'), Url::get('ngay_chia_to'));
        $ngay_xn           = self::checkDate(Url::get('ngay_xn_from'), Url::get('ngay_xn_to'));
        $ngay_chuyen_kt    = self::checkDate(Url::get('ngay_chuyen_kt_from'), Url::get('ngay_chuyen_kt_to'));
        $ngay_thanh_cong   = self::checkDate(Url::get('ngay_thanh_cong_from'), Url::get('ngay_thanh_cong_to'));
        $ngay_thu_tien     = self::checkDate(Url::get('ngay_thu_tien_from'), Url::get('ngay_thu_tien_to'));
        $ngay_chuyen       = self::checkDate(Url::get('ngay_chuyen_from'), Url::get('ngay_chuyen_to'));
        $ngay_chuyen_hoan  = self::checkDate(Url::get('ngay_chuyen_hoan_from'), Url::get('ngay_chuyen_hoan_to'));
        $ngay_tra_hang     = self::checkDate(Url::get('ngay_tra_hang_from'), Url::get('ngay_tra_hang_to'));
        $saved_date        = self::checkDate(Url::get('saved_date_from'), Url::get('saved_date_to'));
        $deliver_date      = self::checkDate(Url::get('deliver_date_from'), Url::get('deliver_date_to'));

        $cond = '
            '.((DB::escape(Url::get('cmd')=='list_pos'))?' AND orders.pos = 1':'').'
            '.(($ngay_tao)?' AND orders.created>="'.Date_Time::to_sql_date($ngay_tao[0]).' 00:00:00" AND orders.created<="'.Date_Time::to_sql_date($ngay_tao[1]).' 23:59:59"':'').'
            '.(($ngay_chia)?' AND orders.assigned>="'.Date_Time::to_sql_date($ngay_chia[0]).' 00:00:00" AND orders.assigned<="'.Date_Time::to_sql_date($ngay_chia[1]).' 23:59:59"':'').'
            '.(($ngay_xn)?' AND orders.confirmed>="'.Date_Time::to_sql_date($ngay_xn[0]).' 00:00:00" AND orders.confirmed<="'.Date_Time::to_sql_date($ngay_xn[1]).' 23:59:59"':'').'
            '.(($ngay_chuyen_kt)?' AND orders_extra.accounting_confirmed>="'.Date_Time::to_sql_date($ngay_chuyen_kt[0]).' 00:00:00" AND orders_extra.accounting_confirmed<="'.Date_Time::to_sql_date($ngay_chuyen_kt[1]).' 23:59:59"':'').'
            '.(($ngay_thanh_cong)?' AND orders_extra.update_successed_time>="'.Date_Time::to_sql_date($ngay_thanh_cong[0]).' 00:00:00" AND orders_extra.update_successed_time<="'.Date_Time::to_sql_date($ngay_thanh_cong[1]).' 23:59:59"':'').'
            '.(($ngay_thu_tien)?' AND orders_extra.update_paid_time>="'.Date_Time::to_sql_date($ngay_thu_tien[0]).' 00:00:00" AND orders_extra.update_paid_time<="'.Date_Time::to_sql_date($ngay_thu_tien[1]).' 23:59:59"':'').'
            '.(($ngay_chuyen)?' AND orders.delivered>="'.Date_Time::to_sql_date($ngay_chuyen[0]).' 00:00:00" AND orders.delivered<="'.Date_Time::to_sql_date($ngay_chuyen[1]).' 23:59:59"':'').'
            '.(($ngay_chuyen_hoan)?' AND orders_extra.update_returned_time>="'.Date_Time::to_sql_date($ngay_chuyen_hoan[0]).' 00:00:00" AND orders_extra.update_returned_time<="'.Date_Time::to_sql_date($ngay_chuyen_hoan[1]).' 23:59:59"':'').'
            '.(($ngay_tra_hang)?' AND orders_extra.update_returned_to_warehouse_time>="'.Date_Time::to_sql_date($ngay_tra_hang[0]).' 00:00:00" AND orders_extra.update_returned_to_warehouse_time<="'.Date_Time::to_sql_date($ngay_tra_hang[1]).' 23:59:59"':'').'
            '.(($saved_date)?' AND orders_extra.saved_date>="'.Date_Time::to_sql_date($saved_date[0]).'" AND orders_extra.saved_date<="'.Date_Time::to_sql_date($saved_date[1]).'"':'').'
            '.(($deliver_date)?' AND orders_extra.deliver_date>="'.Date_Time::to_sql_date($deliver_date[0]).'" AND orders_extra.deliver_date<="'.Date_Time::to_sql_date($deliver_date[1]).'"':'').'
        ';

        $term_sdt = URL::getStringEscapeNoQuote('term_sdt');
        $term_order_id = URL::getStringEscapeNoQuote('term_order_id');
        $term_ship_id = URL::getStringEscapeNoQuote('term_ship_id');
        $customer_id = URL::getUInt('customer_id');
        $c_n = URL::getStringEscapeNoQuote('c_n');
        $min_search_phone_number = get_group_options('min_search_phone_number');
        $min_search_phone_number = $min_search_phone_number ? $min_search_phone_number : 3;

        if (!$ngay_tao && !$ngay_chia && !$ngay_xn && !$ngay_chuyen_kt && !$ngay_thanh_cong && !$ngay_thu_tien && !$ngay_chuyen && !$ngay_chuyen_hoan && !$ngay_tra_hang && !$saved_date && !$deliver_date) {

            $checkIgnore = strlen($term_sdt) < $min_search_phone_number
                && strlen($term_order_id) <= 2
                && strlen($term_ship_id) <= 2
                && strlen($c_n) < 2
                && !$customer_id;

            $cond = '
                '.((DB::escape(Url::get('cmd')=='list_pos'))?' AND orders.pos = 1':'').'
                '.(($checkIgnore) ? ' AND orders.created>="'.date('Y/m/01', strtotime('-5 month')).' 00:00:00"' : '').'
                '.(($checkIgnore) ? ' AND orders.created<="'.date('Y/m/d').' 23:59:59"' : '').'
            ';
        }

        // Lọc giá
        $minPrice = URL::getUInt('min_price');
        $maxPrice = URL::getUInt('max_price');

        //  SDT
        if($minPrice > $maxPrice){
            die('<p><center>Vui lòng nhập giá tối thiếu không được lớn hơn giá tối đa</center></p>');
        }

        if($maxPrice && $maxPrice >= $minPrice){
            $cond .= sprintf(' AND orders.total_price >= %d AND orders.total_price <= %d', $minPrice, $maxPrice);
        }

        // Lấy các đơn bị trùng theo danh sách ID
        $orderID = URL::getInt('order_id');
        $orderIDDuplicate = URL::getSafeRawIDs('order_id_duplicate');
        if ($orderID && $orderIDDuplicate) {
            $cond .= 'AND (orders.id = ' . $orderID;
            $cond .= ' OR orders.id IN (' . $orderIDDuplicate .'))';
        }


        if(Url::get('category_id') and DB::exists_id('category',intval(Url::sget('category_id')))){
            $cond.= ' and '.IDStructure::child_cond(DB::structure_id('category',intval(Url::sget('category_id'))));
        }
        if(Session::get('account_type')==TONG_CONG_TY){//khoand edited in 30/09/2018
            $cond.= ' and (orders.group_id='.$group_id.' or groups.master_group_id = '.$group_id.')';
            if($search_group_id){
                if(Url::get('ngay_xn_from') and Url::get('ngay_xn_to')) {
                    $cond .= ' and (
                        G2.id = '.$search_group_id.'
                    )';
                }elseif(Url::get('ngay_chia_from') and Url::get('ngay_chia_to')){
                    $cond .= ' and (
                        G1.id = '.$search_group_id.'
                    )';
                }else{
                    $cond.= ' and orders.group_id = '.$search_group_id.'';
                }
            }
        }elseif($master_group_id){

            $cond.= ' and 
                (
                    groups.id='.$group_id.'
                    or (groups.master_group_id = '.$master_group_id.' and G1.id = '.$group_id.'
                 )
            )';

        }else{
            $cond.= ' and groups.id='.$group_id.'';
        }
        //////
        if($city_id = Url::iget('city_id')){
            $cond.= ' and orders.city_id='.$city_id.'';
        }
        //////
        $isObd = isObd();
        if($bundle_id = Url::iget('bundle_id')){
            if($isObd) {
                $cond .= self::getBundleCond($bundle_id);
            } else {
                if( $bundle_id=='-1'){
                    $cond.= ' AND (IFNULL(orders.bundle_id,0)=0)';
                } else{
                    $cond.= ' AND orders.bundle_id='.$bundle_id.'';
                }//end if
            }//end if
        }//end if
        /////
        if($source_id = Url::iget('source_id')){
            if ($isObd) {
                $_sql = "SELECT `include_ids` FROM `order_source` WHERE id = $source_id";
                $sourceIds = DB::fetch($_sql, 'include_ids');
                $_sourceIds = explode(',', $sourceIds);
                $_sourceIds = array_filter($_sourceIds, function($value) { return !is_null($value) && $value !== ''; });
    
                if ($_sourceIds) {
                    array_unshift($_sourceIds, $source_id);
                    $_sourceIds = DataFilter::removeXSSinArray($_sourceIds);
                    $sourceIds = implode(',', $_sourceIds);
                    $cond.= " AND orders.source_id IN ($sourceIds)";
                } else {
                    $cond.= " AND orders.source_id = $source_id";
                }//end if
            } else {
                $cond.= ' and orders.source_id='.$source_id.'';
            }
        }//end if

        if($source_shop_id = Url::iget('source_shop_id')){
            $cond.= ' and orders_extra.source_shop_id='.$source_shop_id.'';
        }

        if(Url::sget('product_code')){
            $codes = explode(',',Url::sget('product_code'));
            $arr = array_map(function($code){
                return DB::escape(trim($code));
            },$codes);
            if(count($arr) === 1){
                $cond .= ' and products.code like "%'.$arr[0].'%"';
            }
            if(count($arr)>1){
                $cond .= ' and products.code IN ("'.implode('", "', $arr).'")';
            }
        }

        if($warehouse_id = Url::get('warehouse_id_filter')){
            $cond.= ' and orders_products.warehouse_id='.DB::escape($warehouse_id).'';
        }
        if($type = Url::iget('type')){
            $cond.= ' and orders.type='.$type.'';
        }
        if($search_account_id=Url::get('search_account_id') and $user_id = DB::fetch('select id from users where username="'.$search_account_id.'"','id')){
            if(Url::get('ngay_xn_from') and Url::get('ngay_xn_to')){
                $cond.= ' and orders.user_confirmed='.$user_id.' and orders.status_id <> 9';
            }elseif(Url::get('ngay_chuyen_from') and Url::get('ngay_chuyen_to')){
                $cond.= ' and orders.user_delivered='.$user_id.'';
            }else{
                $cond.= ' and orders.user_created='.$user_id.'';
            }
        }

        if($confirmed_account_id=DB::escape(Url::get('confirmed_account_id'))){
            if($user_id = DB::fetch('select id from users where username="'.$confirmed_account_id.'"','id')){
                $cond.= ' and orders.user_confirmed='.$user_id.'';
            }elseif($confirmed_account_id=='-1'){
                $cond.= ' and IFNULL(orders.user_confirmed,0) = 0';
            }
        }
        $mobiephone = '';
        if (!empty($home_network)){
            $mobiephone .= self::get_sql_home_network($home_network);
            if (!empty($mobiephone)) {
                $str_sql = substr($mobiephone, 0, -4) ;
                $cond .=' and ('.$str_sql.')';
            }
        }
        if($assigned_account_id=DB::escape(Url::get('assigned_account_id'))){
            if($user_id = DB::fetch('select id from users where username="'.$assigned_account_id.'"','id')){
                $cond.= ' and orders.user_assigned='.$user_id.'';
            }elseif($assigned_account_id=='-1'){
                $cond.= ' and IFNULL(orders.user_assigned,0) = 0';
            }
        }
        if($mkt_account_id=DB::escape(Url::get('mkt_account_id'))){
            if($user_id = DB::fetch('select id from users where username="'.$mkt_account_id.'"','id')){
                $cond.= ' and orders.user_created='.$user_id.'';
            }elseif($mkt_account_id=='-1'){
                $cond.= ' and IFNULL(orders.user_created,0) = 0';
            }
        }

        // Lọc upsale
        if($upsale_account_id=DB::escape(Url::get('upsale_account_id'))){
            if($user_id = DB::fetch('select id from users where username="'.DB::escape($upsale_account_id).'"','id')){
                $cond.= ' and orders_extra.upsale_from_user_id='.$user_id.'';
            }elseif($upsale_account_id=='-1'){
                $cond.= ' and IFNULL(orders_extra.upsale_from_user_id,0) = 0';
            }
        }

        if($c_n = trim(addslashes(URL::get('c_n')))){
            $c_n = preg_replace('/\"/', '', $c_n);
            if(strlen($c_n)>=2){
                $cond .= ' AND orders.customer_name LIKE "%'.$c_n.'%"';
            }
        }

        // Tìm theo ID khách hàng
        if($customer_id){
            $cond .= ' AND orders.customer_id = '.$customer_id;
        }

        if($customer_group_id = URL::iget('customer_group_id')){
            $cond .= ' AND orders_extra.customer_group = '.DB::escape($customer_group_id);
        }

        // Lọc in đơn
        $printed = URL::iget('printed');
        if($printed == 1){ // đã in
            $cond .= ' AND orders_extra.printed = 1';
        }elseif($printed == 2){ // chưa in
            $cond .= ' AND (orders_extra.printed = 0 OR orders_extra.printed IS NULL)';
        }

        //  SDT
        if($term_sdt && strlen($term_sdt) < $min_search_phone_number){
            die('<p><center>Vui lòng nhập SDT tối thiểu ' . $min_search_phone_number . ' số</center></p>');
        }
        if($term_order_id && strlen($term_order_id) < 3){
            die('<p><center>Vui lòng nhập mã đơn hàng tối thiểu 3 ký tự</center></p>');
        }
        if($term_ship_id && strlen($term_ship_id) < 3){
            die('<p><center>Vui lòng nhập mã vận đơn tối thiểu 3 ký tự</center></p>');
        }

        if($term_sdt || strlen($term_order_id) > 2 || strlen($term_ship_id) > 2){
            $searchConditions = [];

            // sđt
            if ($term_sdt) {
                $arrMobile = explode(',', $term_sdt);
                $searchMobileConditions = array();
                if (!empty($arrMobile)) {
                    foreach ($arrMobile as $mobile) {
                        $mobile = DB::escape(trim($mobile));
                        $searchMobileConditions[] = 'orders.mobile LIKE ("'.$mobile.'%")';
                        $searchMobileConditions[] = 'orders.mobile2 LIKE ("'.$mobile.'%")';
                        if($mobile[0] && (Session::get('account_type') != TONG_CONG_TY || !AdminOrders::$master_group_id)){
                            $searchMobileConditions[] = 'orders.mobile LIKE ("0'.$mobile.'%")';
                        }
                    }
                }
                $searchConditions[] = '(' . implode(' OR ', $searchMobileConditions) . ')';
            }

            //  OrderID
            if($term_order_id){
                $arrOrderId = explode(',', $term_order_id);
                $searchOrderIdConditions = array();
                if (!empty($arrOrderId)) {
                    foreach ($arrOrderId as $order_id) {
                        $order_id = trim($order_id);
                        $searchOrderIdConditions[] = 'orders.id = '.intval($order_id);
                    }
                }
                $searchConditions[] = '(' . implode(' OR ', $searchOrderIdConditions) . ')';
//                $searchConditions[] = 'orders.id = '.intval($term_order_id);
            }

            //  Ship ID
            if($term_ship_id){
                $arrShipId = explode(',', $term_ship_id);
                $searchShipIdConditions = array();
                if (!empty($arrShipId)) {
                    foreach ($arrShipId as $ship_id) {
                        $ship_id = DB::escape(trim($ship_id));
                        $searchShipIdConditions[] = 'orders.postal_code = "'.$ship_id.'"';
                    }
                }
                $searchConditions[] = '(' . implode(' OR ', $searchShipIdConditions) . ')';
//                $searchConditions[] = 'orders.postal_code = "'.$term_ship_id.'"';
            }

            $cond .= $searchConditions ? ' AND ' . implode(' AND ', $searchConditions) : '';
        }
        
        if(!AdminOrders::$admin_group && !$quyen_admin_marketing) {
            if ($quyen_van_don) {//update ngay 4/4/2018 by mr Khoa
                if (!$quyen_chia_don) {
                    $cond .= '
                and 
                (
                    orders.status_id NOT IN (' . CHUA_XAC_NHAN . ')
                    OR (orders.user_assigned=' . $my_user_id . ' OR orders.user_created=' . $my_user_id . ')
                )';
                }
            } elseif ($quyen_bung_don) {
                if (!$quyen_chia_don) {
                    $cond .= '
                and 
                (
                    orders.status_id NOT IN (' . CHUA_XAC_NHAN . ',' . THANH_CONG . ',' . CHUYEN_HOAN . ',' . DA_THU_TIEN . ',' . TRA_VE_KHO . ')
                    OR (orders.user_assigned=' . $my_user_id . ' OR orders.user_created=' . $my_user_id . ')
                )';
                }
            } elseif ($quyen_bung_don2) {//update ngay 14/10/2019 by mr Khoa
                // Loại trừ các trạng thái cho các Quyền Bung đơn
                $cond .= '' . (($quyen_chia_don) ? '' : ' and (orders.status_id NOT IN (' . CHUA_XAC_NHAN . ',' . CHUYEN_HANG . ',' . THANH_CONG . ',' . XAC_NHAN . ',' . CHUYEN_HOAN . ',' . DA_THU_TIEN . ',' . TRA_VE_KHO . ') or (orders.user_assigned=' . $my_user_id . ' OR orders.user_created=' . $my_user_id . '))') . '';
            } elseif ($quyen_bung_don_nhom and !$quyen_chia_don) {
                require_once ROOT_PATH . 'packages/core/includes/common/Arr.php';
                $acc_groups = get_account_groups();

                $excepted_status_ids = AdminOrdersDB::get_excepted_status_ids('BUNGDON_NHOM');
                $user_ids_in_group = get_user_ids_in_group(Arr::of($acc_groups ?? [])->column('id')->join(','));
                $user_ids_without_self = Arr::of(explode(',', $user_ids_in_group))->filter(function ($id) use ($my_user_id) {
                    return $id != $my_user_id;
                })->join(',');

                $cond_bung_don = [];

                // Đơn được tạo, chốt, chia bởi các users trong nhóm, ngoại trừ user hiện tại
                // Loại trừ các trạng thái cho các Quyền Bung đơn nhóm
                if ($user_ids_without_self) {
                    $cond_bung_don[] = '(orders.user_created IN (' . $user_ids_without_self . ') OR orders.user_assigned IN (' . $user_ids_without_self . ') OR orders.user_confirmed IN (' . $user_ids_without_self . '))';
                    $cond_bung_don[] = 'orders.status_id NOT IN (' . THANH_CONG . ',' . CHUYEN_HOAN . ',' . DA_THU_TIEN . ',' . TRA_VE_KHO . ')';
                }

                // ĐƠn không được nằm trong các trạng thái mà quyền bung đơn có thao tác
                if ($excepted_status_ids && $user_ids_without_self) {
                    $cond_bung_don[] = 'orders.status_id NOT IN (' . $excepted_status_ids . ')';
                }
                
                $cond_bung_don = $cond_bung_don ? ' OR ' . implode(' AND ', $cond_bung_don) : '';

                // Đơn được tạo chốt chia cho user hiện tại
                $cond .= ' AND ((orders.user_assigned=' . $my_user_id . ' OR orders.user_created=' . $my_user_id . ' OR orders.user_confirmed=' . $my_user_id . ')' . $cond_bung_don . ')';
            } else if ($quyen_bung_don_nhom and $quyen_chia_don and get_account_groups()) {
                $acc_groups = get_account_groups();
                $excepted_status_ids = AdminOrdersDB::get_excepted_status_ids_new('BUNGDON_NHOM');
                if (!empty($acc_groups)) {
                    $acc_groups = MiString::get_list($acc_groups, 'id');
                    $user_ids_in_group = get_user_ids_in_group(implode($acc_groups, ','));
                    $arrStatus = explode(',', $excepted_status_ids);
                    $get_not_excepted_status_ids = self::get_not_excepted_status_ids($arrStatus);
                    if ($get_not_excepted_status_ids) {
                        $cond .= ' AND (
                                    orders.user_created = ' . $my_user_id . ' 
                                    OR orders.user_confirmed = ' . $my_user_id . ' 
                                    OR orders.user_assigned = ' . $my_user_id . '
                                    OR (orders.status_id=' . CHUA_XAC_NHAN . ' and orders.group_id = ' . Session::get('group_id') . ' 
                                    OR orders.user_assigned IN (' . $user_ids_in_group . ')
                                    OR orders.user_confirmed IN (' . $user_ids_in_group . ')
                                    OR orders.user_created IN (' . $user_ids_in_group . ')  
                                    ) 
                            ) AND (
                                    orders.status_id NOT IN (' . $get_not_excepted_status_ids . ') 
                                    OR orders.user_confirmed = ' . $my_user_id . '
                                    OR orders.user_assigned = ' . $my_user_id . '
                            )';

                    } else {
                        $cond .= ' AND ( 
                                    orders.user_created IN (' . $user_ids_in_group . ') 
                                    OR orders.user_confirmed IN (' . $user_ids_in_group . ') 
                                    OR orders.user_assigned IN (' . $user_ids_in_group . ') 
                                    OR (orders.status_id=' . CHUA_XAC_NHAN . ' and orders.group_id = ' . Session::get('group_id') . ') 
                            )';
                    }
                }
            } else {
                if ($quyen_chia_don) {
                    $cond .= '
                    and 
                    (
                        orders.status_id=' . CHUA_XAC_NHAN . '
                        OR (orders.user_assigned=' . DB::escape($my_user_id) . ' OR orders.user_created=' . DB::escape($my_user_id) . ')
                    )';
                } else {
                    if ($quyen_cskh) {
                        $cond .= ' and ((orders.user_assigned=' . $my_user_id . ' OR orders.user_created=' . $my_user_id . ' OR orders.user_confirmed=' . $my_user_id . ' or orders.status_id = ' . CHUYEN_HANG . ' or orders.status_id = ' . THANH_CONG . ') ' . $ag_cond . '' . $cr_cond . ')';
                    } else {
                        $cond .= ' and (' . (!$account_group_id ? '(orders.user_assigned=' . $my_user_id . ' OR orders.user_created=' . $my_user_id . ' OR orders.user_confirmed=' . $my_user_id . ')' : '1=1') . ' ' . $ag_cond . '' . $cr_cond . ')';
                    }
                }

            }
        }
        
        if($page_id = DB::escape(Url::get('page_id'))){
            $page_id = DB::fetch('select id,page_id from fb_pages where id='.$page_id,'page_id');
            $cond.= ' and orders.fb_page_id="'.$page_id.'"';
        }

        if($fb_post_id = DB::escape(Url::get('fb_post_id'))){
            $cond.= ' and orders.fb_post_id="'.$fb_post_id.'"';
        }

        if(Url::check('is_inner_city')){
            $cond.= ' and orders.is_inner_city=1';
        }
        if($ss = Url::get('shipping_services')){
            $idsArr = explode(',',$ss);
            $escape_ids = array_map(function($id){
                return DB::escape($id);
            }, $idsArr);

            $ss = implode(',', $escape_ids);
            $cond.= ' and orders.shipping_service_id IN ('.($ss).')';
        }
        if($ss = Url::get('dau_so')){
            $idsArr = explode(',',$ss);
            $escape_ids = array_map(function($id){
                return DB::escape($id);
            }, $idsArr);

            $ss = implode(',', $escape_ids);
            $cond.= ' and orders.telco_code IN ("'.($ss).'")';
        }


        if($ss = Url::get('status')){
            if(!is_array($ss)){
                $ss = explode(',',$ss);
            }
            if(!empty($ss)){
                $cond.= ' and (';
                $i=0;
                foreach($ss as $k=>$v){
                    $cond.= ' '.($i?'or':'').' orders.status_id = '.DB::escape($v).'';
                    $i++;
                }
                $cond.= ' )';
            }
        }
        if($account_group_id){
            $arr = [];
            foreach ($account_group_id as $value) {
                $arr[] = DB::escape($value);
            }
            $strAccountGroupIds = implode($arr,',');
            $strAccountIds = self::get_account_group_user($strAccountGroupIds);
            if(Url::get('ngay_xn_from') && Url::get('ngay_xn_to')) {
                $cond .= ' and (
                    orders.user_confirmed IN ('.$strAccountIds.')
                )';
            }elseif(Url::get('ngay_tao_from') && Url::get('ngay_tao_to')){
                $cond .= ' and (
                    orders.user_created IN ('.$strAccountIds.')
                )';
            }elseif(Url::get('ngay_chia_from') && Url::get('ngay_chia_to')){
                $cond .= ' and (
                    orders.user_assigned IN ('.$strAccountIds.')
                )';
            }else{
                $cond .= ' and (
                    orders.user_created IN ('.$strAccountIds.')
                    OR  orders.user_assigned IN ('.$strAccountIds.')
                    OR  orders.user_confirmed IN ('.$strAccountIds.')
                )';
            }
        }

        if(!is_account_group_manager() && !is_account_group_department() && !AdminOrders::$admin_group && !is_group_owner()){
            if(self::checkUserRole(['GANDON','MARKETING'])){
                $statusSale = self::get_excepted_status_ids_new('GANDON') ? explode(',', self::get_excepted_status_ids_new('GANDON')) : [];
                $statusMkt = self::get_excepted_status_ids_new('MARKETING') ? explode(',', self::get_excepted_status_ids_new('MARKETING')) : [];
                $status = array_merge($statusSale,$statusMkt);
                if($status){
                    $strStatus = implode(',', $status);
                    $cond .= ' AND ( orders.user_assigned = ' . $my_user_id . ' OR orders.user_created='.$my_user_id.' OR  orders.user_confirmed='.$my_user_id.' ) AND orders.status_id IN ('. $strStatus .') ';
                }
            } else if(self::checkUserOnlyRole('MARKETING')){
                $status = self::get_excepted_status_ids_new('MARKETING');
                if($status){
                    $cond .= ' AND ( orders.user_assigned = ' . $my_user_id . ' OR orders.user_created='.$my_user_id.' ) AND orders.status_id IN ('. $status .') ';
                }
            } else if(self::checkUserOnlyRole('GANDON')){
                $status = self::get_excepted_status_ids_new('GANDON');
                if($status){
                    $cond .= ' AND ( orders.user_assigned = ' . $my_user_id . ' OR orders.user_created='.$my_user_id.' OR  orders.user_confirmed='.$my_user_id.' ) AND orders.status_id IN ('. $status .') ';
                }
            }
        }
        if($ids = Url::get('ids')){
            $idsArray = explode(',', $ids);
            $escapeIds = array_map(function($id){
                return DB::escape($id);
            }, $idsArray);
            $ids = implode(',', $escapeIds);

            $cond .= ' and orders.id IN ('.$ids.')';
        }
        if(System::get_client_ip_env()=='117.4.245.88' and AdminOrders::$account_id=='sale.anh.az378'){
            System::debug($cond);
        }
        return $cond;
    }
    static function get_not_excepted_status_ids($arr){
        $statusChuaXn = [];
        $statusChuaXn[] = CHUA_XAC_NHAN;
        $not_acept = '';
        if($arr && sizeof($arr)>=1){
            if(in_array(CHUA_XAC_NHAN, $arr)){
                $not_acept = array_diff($arr,$statusChuaXn);
            } else {
                $not_acept = $arr;
            }
        }
        if($not_acept != ''){
            return implode(',', $not_acept);
        } else {
            return '';
        }

    }
    /*
    static function get_not_excepted_status_ids($arr){
        $statusChuaXn = [];
        $statusChuaXn[] = CHUA_XAC_NHAN;
        $not_acept = '';
        if($arr && sizeof($arr)>=1){
            if(in_array(CHUA_XAC_NHAN, $arr)){
                $not_acept = array_diff($arr,$statusChuaXn);
            } else {
                $not_acept = $arr;
            }
        }
        if($not_acept != ''){
            return implode(',', $not_acept);
        } else {
            return '';
        }

    }*/

    static function get_excepted_status_ids_new($privilege_code){
        $user_id = get_user_id();
        $strStatus = '';
        $sql = "SELECT roles_statuses.status_id as id 
                FROM 
                    users_roles
                LEFT JOIN  
                    roles_to_privilege ON users_roles.role_id = roles_to_privilege.role_id
                LEFT JOIN 
                    roles_statuses ON  roles_statuses.role_id = users_roles.role_id
                WHERE 
                    users_roles.user_id = $user_id
                AND 
                    roles_to_privilege.privilege_code  = '".$privilege_code."'
                ";
        $results = DB::fetch_all($sql);
        if($results){
            $strStatus = implode(',',array_keys($results));
        }
        return $strStatus;
    }

    static function checkUserRole($array_privilege_code){
        $user_id = get_user_id();
        $sql = "SELECT roles_to_privilege.id, roles_to_privilege.privilege_code FROM 
                                roles_to_privilege 
                            LEFT JOIN 
                                users_roles ON users_roles.role_id = roles_to_privilege.role_id
                            WHERE 
                                users_roles.user_id = $user_id
                            ";
        $results = DB::fetch_all($sql);

        if($results){
            $array = [];
            foreach ($results as $key => $value) {
                $array[] = $value['privilege_code'];
            }
            if(!empty($array) && sizeof($array) == 2 && empty(array_diff($array_privilege_code, $array))){
                return true;
            }
            return false;
        }
        return false;
    }

    static function get_account_group_user($strAccountGroupIds){
        $cond = '';
        //
        $group_id = AdminOrders::$group_id;
        if(!AdminOrders::$is_owner && !AdminOrders::$admin_group && !AdminOrders::$quyen_bung_don && !AdminOrders::$quyen_cskh && !AdminOrders::$quyen_van_don && !AdminOrders::$quyen_chia_don && !AdminOrders::$quyen_admin_marketing && !AdminOrders::$quyen_bung_don_nhom && !AdminOrders::$is_account_group_department && !AdminOrders::$is_account_group_manager){
            $cond .= ' AND account.id = "' . Session::get('user_id') . '"';
        } else {
            $cond.= ' AND account.account_group_id IN ('.$strAccountGroupIds.')';
        }
        $cond .= ' AND account.is_active = 1';
        $sql = '
            SELECT 
                users.id as id,
                users.username
            FROM `account` 
            LEFT JOIN account_group ON account_group_id = account_group.id 
            JOIN users ON users.username = account.id
            WHERE account.group_id = ' . $group_id . $cond .' '
        ;
        $results = DB::fetch_all($sql);
        return implode(',', array_keys( $results ));
    }

    /*
    static function get_excepted_status_ids_new($privilege_code){
        $user_id = get_user_id();
        $strStatus = '';
        $sql = "SELECT roles_statuses.status_id as id
                FROM
                    users_roles
                LEFT JOIN
                    roles_to_privilege ON users_roles.role_id = roles_to_privilege.role_id
                LEFT JOIN
                    roles_statuses ON  roles_statuses.role_id = users_roles.role_id
                WHERE
                    users_roles.user_id = $user_id
                AND
                    roles_to_privilege.privilege_code  = '".$privilege_code."'
                ";
        $results = DB::fetch_all($sql);
        if($results){
            $strStatus = implode(',',array_keys($results));
        }
        return $strStatus;
    }
    */

    static function checkUserOnlyRole($privilege_code){
        $user_id = get_user_id();
        $sql = "SELECT * FROM 
                                users_roles
                            LEFT JOIN 
                                roles_to_privilege ON users_roles.role_id = roles_to_privilege.role_id
                            WHERE 
                                users_roles.user_id = $user_id
                            ";
        $results = DB::fetch_all($sql);
        if($results){
            $array = [];
            foreach ($results as $key => $value) {
                $array[] = $value['privilege_code'];
            }
            if(!empty($array) && sizeof($array) == 1 && in_array($privilege_code, $array)){
                return true;
            }
            return false;
        }
        return false;
    }

    static function get_excepted_status_ids($privilege_code){
        $status_ids = '';
        $user_id = get_user_id();
        $privilege_code = DB::escape($privilege_code);
        if($role_id = DB::fetch('select roles_to_privilege.id, roles_to_privilege.role_id from roles_to_privilege join roles on roles.id=roles_to_privilege.role_id join users_roles ON roles.id=users_roles.role_id where privilege_code = "'.$privilege_code.'" and roles.group_id='.AdminOrders::$group_id.' and users_roles.user_id='.$user_id,'role_id')){
            $status_ids = DB::fetch_all('select id,status_id from roles_statuses where role_id='.$role_id);
            $status_ids = implode(MiString::get_list($status_ids,'status_id'),',');
        }
        return $status_ids;
    }

    static function autoAssignOrder(Array $options){
        $group_id = AdminOrders::$group_id;
        $master_group_id = AdminOrders::$master_group_id;
        $acc_group_id=isset($options['acc_group_id'])?DB::escape($options['acc_group_id']):false;
        $orders= isset($options['items']) ? $options['items'] : [];
        $account_ids=isset($options['account_ids'])?$options['account_ids']:false;
        $source_id=isset($options['source_id'])?DB::escape($options['source_id']):false;
        $bundle_id=isset($options['bundle_id'])?DB::escape($options['bundle_id']):false;
        $groupsId=isset($options['groupsId'])?DB::escape($options['groupsId']):'';
        $assign_option=isset($options['assign_option'])?$options['assign_option']:false;
        $limit=isset($options['limit'])?$options['limit']:false;

        if(!AdminOrders::$quyen_chia_don && !is_account_group_manager()){
            return false;
        }
        $auto = ' nhanh';
        $users = $account_ids ?  $account_ids
            : AdminOrdersDB::get_users('GANDON', $acc_group_id ? $acc_group_id : null);
        if(!$orders){
            $cond = '(IFNULL(user_assigned,0)=0)';
            if(Session::get('account_type')==TONG_CONG_TY){//khoand edited in 30/09/2018
                $cond.= ' and (groups.id='.$group_id.' or groups.master_group_id = '.$group_id.')';
            }elseif($master_group_id){
                $cond.= ' and (groups.id='.$group_id.' or (groups.master_group_id = '.$master_group_id.' and G1.id = '.$group_id.'))';
            }else{
                $cond.= ' and groups.id='.$group_id.'';
            }

            $cond.= $source_id ? ' and orders.source_id='.$source_id : '';
            $cond.= $bundle_id ? ' and orders.bundle_id='.$bundle_id : '';

            if ($groupsId != 0) {
                $listAccount = DB::fetch_all_array('select users.id as users_id from account inner join users on users.username = account.id where account_group_id = ' . $groupsId);
                if (count($listAccount) > 0) {
                    $strUserId = implode(array_column($listAccount, 'users_id'), ',');
                    $cond .= ' and orders.user_created in (' . $strUserId . ')';
                }
            }

            $cond.= ' and orders.status_id = '.CHUA_XAC_NHAN.' and orders.group_id = '.$group_id.'';
            require_once 'packages/core/includes/utils/paging.php';
            if($assign_option==1){
                $order_by = ' orders.id ASC';
            }else{
                $order_by = 'orders.id DESC';
            }
            $orders = AdminOrdersDB::get_assign_items($cond,$limit,$order_by);
        }

        if(!$orders || !$users){
            return;
        }


        $user_id = get_user_id();

        // Xáo trộn mảng đơn hàng và người dùng để tạo ra sự 'ngẫu nhiên' khi gán order cho user
        array_shuffle($orders);
        array_shuffle($users);
        // Phân mảng order sao cho mỗi phân đoạn có số lượng đúng bằng số user
        $ordersChunk = array_chunk($orders, count($users));
        foreach ($ordersChunk as $index => $_orders) {

            foreach ($users as $i => $user) {

                if (empty($_orders[$i])){
                    break;
                }

                if($_orders[$i]['user_assigned']){
                    continue;
                }
                $arr = ['user_assigned' => $user['user_id'], 'assigned' => date('Y-m-d H:i:s')];
                if (!$_orders[$i]['first_user_assigned']) {
                    $arr['first_user_assigned'] = $user_id;
                    $arr['first_assigned'] = date('Y-m-d H:i:s');
                }

                $orderID = $_orders[$i]['id'];
                DB::update('orders', $arr, 'id=' . $orderID);

                AdminOrdersDB::update_revision($orderID, false, false, 'Số được gán' . $auto . ' cho tài khoản ' . $user['id']);
                AdminOrdersDB::update_assign_log($orderID, $user['user_id']);
            }
        }

        return count($orders);
    }

    static function getAllAccountGroup(){
        $cond = 'account_group.group_id='.Session::get('group_id');
        $sql = '
          select 
            account_group.id,account_group.name 
          from 
            account_group 
            join `account` on `account`.account_group_id=account_group.id
          where 
            '.$cond.' 
          order by 
            account_group.name
        ';
        $groups = DB::fetch_all($sql);
        return $groups;
    }


    static function update_assign_log($order_id,$to_user_id){// added by khoand in 22/10/2018
        DB::insert('orders_assigned_log',array(
            'from_user_id'=>get_user_id(),
            'to_user_id'=>$to_user_id,
            'time'=>time(),
            'order_id'=>$order_id,
            'group_id'=>Session::get('group_id')
        ));
    }

    /**
     * { function_description }
     *
     * @param      array   $status_config   The status configuration
     * @param      array   $order_shipping  The orders shipping
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function update_orders_shipping(array $status_config, array $order_shipping)
    {
        return DB::update_id(
            'orders_shipping',
            ['shipping_status' => $status_config['HUY']],
            $order_shipping['id']
        );
    }

    /**
     * { function_description }
     *
     * @param      int     $id             The identifier
     * @param      array   $status_config  The status configuration
     * @param      int     $user_id        The user identifier
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function insert_order_shipping_status_history(int $id, array $status_config, int $user_id)
    {
        return DB::insert(
            'order_shipping_status_history',
            [
                'order_id' => $id,
                'status' => $status_config['HUY'],
                'user_id' => $user_id
            ]);
    }

    /**
     * Logs an orders printed.
     *
     * @param      array   $orderIDs  The order i ds
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function logOrdersPrinted(array $orderIDs)
    {
        return DB::update('orders_extra', ['printed' => 1], 'order_id IN (' . implode(',', $orderIDs) . ')');
    }

    public static function checkDate($from_date, $to_date) {
        if (Date_Time::to_sql_date($from_date) && Date_Time::to_sql_date($to_date)) {

            $from_date = strtotime(str_replace('/','-', $from_date));
            $to_date = strtotime(str_replace('/','-', $to_date));

            if ($from_date > $to_date) {
                return false;
            }
            $from_date = date('d/m/Y', $from_date);
            $to_date = date('d/m/Y', $to_date);
            return array($from_date, $to_date);
        }
        return false;
    }

    /**
     * Gets the user identifier by username.
     *
     * @param      string  $username  The username
     * @param      int     $groupID   The group id
     *
     * @return     <type>  The user identifier by username.
     */
    public static function getUserIdByUsername(string $username, int $groupID)
    {
        $fmt = 'SELECT `id` FROM `users` WHERE `username`="%s" AND `group_id`=%d';
        $sql = sprintf($fmt, DB::escape($username), $groupID);

        return DB::fetch($sql,'id');
    }

    /**
     * Gets the shop users.
     *
     * @param      int     $groupID  The group id
     * @param      array   $selects  The selects
     *
     * @return     <type>  The shop users.
     */
    public static function getShopUsers(int $groupID, $selects = ['*'])
    {
        $fmt = 'SELECT %s FROM `users` 
            JOIN account ON users.username = account.id 
            LEFT JOIN party ON users.username = party.user_id
            WHERE users.group_id = %d AND account.is_active = 1';
        $sql = sprintf($fmt, implode(',', $selects), $groupID);

        return DB::fetch_all($sql);
    }

    /**
     * Gets the order by identifier.
     *
     * @param      int     $group_id  The group identifier
     * @param      int     $order_id  The order identifier
     *
     * @return     <type>  The order by identifier.
     */
    public static function getOrderById(int $group_id, int $order_id){
        $select = 'id,status_id,customer_name,note1,user_created,created,';
        $select .= 'user_confirmed,confirmed,user_delivered,delivered,group_id';
        $sql = sprintf('SELECT %s FROM orders WHERE group_id =%d AND id=%d', $select, $group_id, $order_id);

        return DB::fetch($sql);
    }
    public static function check_home_network($mobile) {

        if (preg_match( '/^('.implode('|',self::VIETTEL).')/', $mobile )) {
            return 'VIETTEL';
        }
        if (preg_match( '/^('.implode('|',self::VINAPHONE).')/', $mobile )) {
            return 'VINAPHONE';
        }
        if (preg_match( '/^('.implode('|',self::MOBIFONE).')/', $mobile )) {
            return 'MOBIFONE';
        }
        if (preg_match( '/^('.implode('|',self::VIETNAMOBILE).')/', $mobile )) {
            return 'VIETNAMOBILE';
        }
        if (preg_match( '/^('.implode('|',self::GMOBILE).')/', $mobile )) {
            return 'GMOBILE';
        }
        if (preg_match( '/^('.implode('|',self::ITELECOM).')/', $mobile )) {
            return 'ITELECOM';
        }
        return 'KHAC';

    }
    public static function cond_home_network($array = [],$flag=true)
    {
        $string = '';
        if ($flag) {
            foreach ($array as $valueArray) {
                $string .= ' orders.mobile LIKE "' . $valueArray . '%" OR ';
            }
        }else {
            foreach ($array as $valueArray) {
                $string .= ' orders.mobile NOT LIKE "' . $valueArray . '%" AND ';
            }
        }
        return $string;
    }

    public static function get_sql_home_network($home_network){
        $mobiephone = '';
        $checkduplicate = [];
        foreach ($home_network as $carrier) {
            $checkduplicate[] = $carrier;
            $mobiephone .= self::buildCond( $carrier,$checkduplicate);
        }
        return $mobiephone;
    }

    function buildCond( string $carrier, $checkduplicate = []): string {
        $carrierNumbers = self::CARRIERS[$carrier] ?? [];
        if ($carrierNumbers) {
            return  self::cond_home_network($carrierNumbers);
        }
        return self::generateCondOfOtherCarrier($checkduplicate);
    }

    function generateCondOfOtherCarrier($checkduplicate = []) {
        $query_cond = '';
        foreach (self::CARRIERS as $carrierName => $carrierNumbers) {
            if (in_array($carrierName, $checkduplicate)) continue;
            $query_cond .= self::cond_home_network($carrierNumbers, false);
        }
        return $query_cond;
    }

    static function getIncludeIdsByBundleId(int $bundleId)
    {
        $sql = "SELECT id, include_ids
            FROM bundles
            WHERE id = $bundleId
            LIMIT 1";

        return DB::fetch($sql, 'include_ids');
    }

    static function getBundleCond(int $bundleId): string
    {
        if( $bundleId == '-1') {
            return " AND (IFNULL(orders.bundle_id, 0) = 0)";
        }//end if

        $includeIds = self::getIncludeIdsByBundleId($bundleId);
        if (!$includeIds)  {
            return " AND orders.bundle_id = $bundleId";
        }//end if

        $_includeIds = explode(',', $includeIds);
        $_includeIds[] = $bundleId;
        $_includeIds = DB::escapeArray($_includeIds);
        $includeIds = implode(',', $_includeIds);
        return " AND orders.bundle_id in ($includeIds)";
    }
}
