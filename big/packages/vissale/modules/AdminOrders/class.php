<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use GuzzleHttp\Client;
require 'vendor/autoload.php';

require_once(ROOT_PATH . 'packages/core/includes/common/RequestHandler.php');
require_once ROOT_PATH . 'packages/core/includes/common/BiggameAPI.php';

class AdminOrders extends Module{
    const VALIDATE_DATE_DEPLOY = '2021-08-31';
    const VALIDATE_DATE_DEPLOY_TYPE = '2021-10-29';
    const DISABLE_PRODUCT_PRICE_DATE = '2021-10-05';
    const MOBILE_TYPE_DOMESTIC = 1;
    const MOBILE_TYPE_FOREIGN = 2;
    public static $mobile_types = [
        self::MOBILE_TYPE_DOMESTIC => 'VN', 
        self::MOBILE_TYPE_FOREIGN => 'QT',
    ];
    public static $mobile_regex_patterns = [
        self::MOBILE_TYPE_DOMESTIC => '/^0[1-9][0-9]{8,9}$/',
        self::MOBILE_TYPE_FOREIGN => '/^00[1-9][0-9]{1,17}$/',
    ];
    public static $system_id;
    public static $account_id;
    public static $user_id;
    public static $admin_group;
    public static $group_id;
    public static $group;
    public static $integrate_shipping;
    public static $master_group_id;
    public static $account_type;
    public static $is_owner;

    // Shop setting
    public static $show_phone_number_excel_order;
    public static $show_phone_number_print_order;
    public static $show_full_name_export_excel_order;
    public static $create_export_invoice_when_confirmed;
    public static $create_export_invoice_when_delivered;
    public static $create_import_invoice_when_return;
    public static $has_show_history_order;

    public static $quyen_chia_don;
    public static $is_account_group_manager;
    public static $is_account_group_department;
    public static $hide_phone_number;
    public static $display_errors;
    public static $require_address;

    public static $quyen_marketing;
    public static $quyen_indon;
    public static $quyen_admin_marketing;
    public static $quyen_sale;//quyen sale
    public static $quyen_cskh;
    public static $quyen_cs; // customer service
    public static $quyen_admin_cs; // customer service
    public static $quyen_ke_toan;
    public static $quyen_admin_ke_toan;
    public static $quyen_xuat_excel;
    public static $quyen_bung_don;
    public static $quyen_bung_don2;
    public static $quyen_bung_don_nhom;
    public static $quyen_van_don;
    public static $quyen_admin_warehouse;
    public static $quyen_xuat_kho;
    public static $quyen_sua_don_nhanh;
    public static $account_privilege_code;
    public static $new_user;
    public static $type;
    public static $orderNotSuccess;
    public static $item;
    public static $date_init_value;
    public static $no_create_order_when_duplicated;
    public static $sale_can_assigned_created_user;
    public static $cs_status;
    public static $hien_sdt_bung_don;
    public static $add_deliver_order;
    function __construct($row){
        ob_start();
        //$display_errors = intval(Portal::get_setting('display_errors'));
        //ini_set('display_errors',$display_errors);
        Module::Module($row);
        if(System::is_local()){
            self::$date_init_value = 'NULL';//'0000-00-00 00:00:00';
        }else{
            self::$date_init_value = '0000-00-00 00:00:00';
        }
        require_once 'packages/vissale/lib/php/vissale.php';
        self::$account_id = Session::get('user_id');
        self::$user_id = get_user_id();
        self::$admin_group = Session::get('admin_group');
        self::$group_id = Session::get('group_id');
        self::$group = DB::fetch('select id,account_type,prefix_post_code,email,code,name from `groups` where id='.self::$group_id);
        self::$master_group_id = Session::get('master_group_id');
        self::$account_type = Session::get('account_type');
        self::$is_owner = is_group_owner();
        self::$quyen_chia_don = check_user_privilege('CHIADON');
        self::$is_account_group_manager = is_account_group_manager();
        self::$is_account_group_department = is_account_group_department();
        self::$create_export_invoice_when_confirmed = get_group_options('create_export_invoice_when_confirmed');
        self::$create_export_invoice_when_delivered = get_group_options('create_export_invoice_when_delivered');
        self::$create_import_invoice_when_return = get_group_options('create_import_invoice_when_return');
        self::$has_show_history_order = !get_group_options('show_history_order');
        self::$hide_phone_number = get_group_options('hide_phone_number');
        self::$hide_phone_number = self::$hide_phone_number?self::$hide_phone_number:0;
        self::$require_address = get_group_options('require_address');
        self::$quyen_admin_warehouse = check_user_privilege('ADMIN_KHO');
        self::$quyen_xuat_kho = check_user_privilege('XUATKHO');
        self::$quyen_marketing = check_user_privilege('MARKETING');
        self::$quyen_admin_marketing = check_user_privilege('ADMIN_MARKETING');
        self::$add_deliver_order = get_group_options('add_deliver_order');
        self::$quyen_sale = check_user_privilege('GANDON');//quyen sale
        self::$quyen_indon = check_user_privilege('IN_DON');//quyen in don + xuat file in don gui kho
        self::$quyen_cskh = check_user_privilege('CSKH');
        self::$quyen_cs = check_user_privilege('CS');
        self::$quyen_admin_cs = check_user_privilege('ADMIN_CS');
        self::$quyen_ke_toan = check_user_privilege('KETOAN');
        self::$quyen_admin_ke_toan = check_user_privilege('ADMIN_KETOAN');
        self::$quyen_xuat_excel = check_user_privilege('XUAT_EXCEL');
        self::$quyen_bung_don = check_user_privilege('BUNGDON') || check_user_privilege('VAN_DON');
        self::$quyen_bung_don2 = check_user_privilege('BUNGDON2');
        self::$quyen_bung_don_nhom = check_user_privilege('BUNGDON_NHOM');
        self::$quyen_van_don = check_user_privilege('VAN_DON');
        self::$quyen_sua_don_nhanh = check_user_privilege('SUA_DON_NHANH', false, false, true);
        self::$account_privilege_code = '';
        self::$type = [
            0 => '-Chọn-',
            1 => 'SALE (Số mới)',
            2 => 'CSKH',
            9 => 'Tối ưu',
            3 => 'Đặt lại',
            4 => 'Đặt lại lần 1',
            5 => 'Đặt lại lần 2',
            6 => 'Đặt lại lần 3',
            7 => 'Đặt lại lần 4',
            8 => 'Đặt lại lần 5',
            10 => 'Đặt lại lần 6',
            11 => 'Đặt lại lần 7',
            12 => 'Đặt lại lần 8',
            13 => 'Đặt lại lần 9',
            14 => 'Đặt lại lần 10',
            15 => 'Đặt lại lần 11',
            16 => 'Đặt lại lần 12',
            17 => 'Đặt lại lần 13',
            18 => 'Đặt lại lần 14',
            19 => 'Đặt lại lần 15',
            20 => 'Đặt lại lần 16',
            21 => 'Đặt lại lần 17',
            22 => 'Đặt lại lần 18',
            23 => 'Đặt lại lần 19',
            24 => 'Đặt lại lần 20',
            25 => 'Đặt lại trên lần 20',
            26 => 'Cross sale',
            27 => 'Afiliate'
        ];
        self::$orderNotSuccess = [
            0 => '-Chọn lý do-',
            1 => 'Đơn đang đi, chưa tới BCP',
            2 => 'Khách hẹn',
            3 => 'Không liên lạc được',
            4 => 'Chưa có tiền',
            5 => 'Khách đi vắng, không có nhà',
            6 => 'Chưa tin tưởng, người nhà không cho dùng',
            7 => 'Lý do khác'
        ];
        self::$cs_status = [
            ''=>'Trạng thái CS',
            '0'=>'Chưa xử lý',
            '1'=>'Đã xử lý',
            '2'=>'Gọi máy bận',
            '3'=>'Không nghe máy',
            '4'=>'Khách không đánh giá'
        ];
        $group_options = DB::fetch_all_key($sql = 'select id, `key`,`value` from `group_options` where group_id='.self::$group_id,'key');
        if(count($group_options) > 0) {
            if(isset($group_options['integrate_shipping'])) {
                self::$integrate_shipping = $group_options['integrate_shipping']['value'];
            }
            if(isset($group_options['show_phone_number_excel_order'])) {
                self::$show_phone_number_excel_order = $group_options['show_phone_number_excel_order']['value'];
            }
            if(isset($group_options['show_phone_number_print_order'])) {
                self::$show_phone_number_print_order = $group_options['show_phone_number_print_order']['value'];
            }
            if(isset($group_options['hien_sdt_bung_don'])){
                self::$hien_sdt_bung_don = $group_options['hien_sdt_bung_don']['value'];
            }
            if(isset($group_options['show_full_name_export_excel_order'])){
                self::$show_full_name_export_excel_order = $group_options['show_full_name_export_excel_order']['value'];
            }
        }
        if(self::$is_owner){
            self::$show_phone_number_excel_order = 1;
            self::$show_phone_number_print_order = 1;
            self::$hien_sdt_bung_don = 1;
        }
        if(self::$admin_group){
            self::$hien_sdt_bung_don = 1;
        }
        self::$system_id = false;
        $system_group_id = 0;
        if(self::$group_id){
            $system_group_id = DB::fetch('select system_group_id from `groups` where id='.self::$group_id,'system_group_id');
        }
        if($system_group_id and (IDStructure::is_child(DB::structure_id('groups_system',$system_group_id),DB::structure_id('groups_system',2)) or $system_group_id==2)){
            self::$system_id = 2;
        }
        self::$new_user = (isset($_SESSION['user_data']['user_status']) and $_SESSION['user_data']['user_status']<=1)?1:0;
        self::$no_create_order_when_duplicated = get_group_options('no_create_order_when_duplicated');
        self::$sale_can_assigned_created_user = get_group_options('sale_can_assigned_created_user');
        require_once 'db.php';
        require_once 'config.php';
        require_once "packages/core/includes/utils/PHPExcel/Classes/PHPExcel.php";
        if(User::is_login() and self::$group_id){
            switch(Url::get('cmd')){
                case 'get_status':
                    $this->get_status();
                    break;
                case 'care_detail':
                    $this->care_detail_cmd();
                    break;
                case 'care_list':
                    $this->care_cmd();
                    break;
                case 'get_rating_template':
                    if($point = Url::iget('point')) {
                        AdminOrdersDB::get_rating_template($point);
                    }
                    exit();
                    break;
                case 'free_order':
                    if($order_id = Url::iget('order_id')) {
                        AdminOrdersDB::free_order($order_id);
                    }
                    exit();
                    break;
                case 'update_quick_order_info':
                    // Validate quyền sửa đơn nhanh
                    if(!AdminOrders::$quyen_sua_don_nhanh && !self::$is_owner){
                        RequestHandler::sendJsonError('Bạn không được phép sửa dụng tính năng này');
                    }

                    if($order_id = Url::iget('order_id')){
                        // giai phong order
                        AdminOrdersDB::free_order($order_id);
                        $arr = [];
                        $order = DB::fetch('select id,note1,note2,cancel_note,shipping_note from orders where id=' . $order_id);
                        $log_str = '';
                        if($note1=DB::escape(DataFilter::removeXSSinHtml(Url::get('note1'))) and !empty($order['note1']) and strcmp($note1 , $order['note1'])<>0){
                            $log_str .= 'Sửa nhanh ghi chú chung từ "'.$order['note1'].'" => "'.$note1.'"';
                            $arr['note1'] = $note1;
                        } else if($note1=DB::escape(DataFilter::removeXSSinHtml(Url::get('note1'))) and empty($order['note1'])){
                            $log_str .= ($log_str?', ':'').'Thêm nhanh ghi chú chung "'.$note1.'"';
                            $arr['note1'] = $note1;
                        }
                        if($note2=DB::escape(DataFilter::removeXSSinHtml(Url::get('note2'))) and !empty($order['note2']) and strcmp($note2, $order['note2'])<>0){
                            $log_str .= ($log_str?', ':'').'Sửa nhanh ghi chú 2 từ "'.$order['note2'].'" => "'.$note2.'"';

                            $_note2 = json_decode($note2);
                            if(($_note2 && self::validateMedidoc($_note2, true)) || !$_note2){
                                $arr['note2'] = $note2;
                            }
                        } else if($note2=DB::escape(DataFilter::removeXSSinHtml(Url::get('note2'))) and empty($order['note2'])){
                            $log_str .= ($log_str?', ':'').'Thêm nhanh ghi chú 2 "'.$note2.'"';
                            $_note2 = json_decode($note2);
                            if(($_note2 && self::validateMedidoc($_note2, true)) || !$_note2){
                                $arr['note2'] = $note2;
                            }
                        }
                        if(!empty($arr)){
                            DB::update('orders',$arr,'id='.$order_id);
                            AdminOrdersDB::update_revision($order_id,false,false,$log_str);
                            echo json_encode(['result'=>'TRUE']);
                            die;
                        }
                    }
                    echo json_encode(['result'=>'FALSE']);
                    break;

                case 'get_quick_order_info':
                    // Validate quyền sửa đơn nhanh
                    if(!AdminOrders::$quyen_sua_don_nhanh && !self::$is_owner){
                        RequestHandler::sendJsonError('Bạn không được phép sửa dụng tính năng này');
                    }

                    $str = '';
                    if ($order_id = Url::iget('order_id') and $order = DB::fetch('select id,user_created,user_assigned,mobile,mobile2,customer_name,note1,note2,cancel_note,shipping_note,address,city,price,discount_price,total_price from orders where group_id = ' . AdminOrders::$group_id . ' and id=' . $order_id)) {
                        $user_id = AdminOrders::$user_id;
                        $checkCensoredPhoneNumber = true;
                        // if (!AdminOrders::$admin_group and !AdminOrders::$quyen_chia_don and $user_id != $order['user_assigned'] and $user_id != $order['user_created']) {
                        //     $checkCensoredPhoneNumber = false;
                        // }
                        if (AdminOrders::$quyen_bung_don or AdminOrders::$quyen_bung_don2 or AdminOrders::$quyen_bung_don_nhom) {
                            if (AdminOrders::$hien_sdt_bung_don == 1) {
                                $checkCensoredPhoneNumber = true;
                            } else {
                                $checkCensoredPhoneNumber = false;
                            }
                        }
                        if ($acc = AdminOrdersDB::is_edited($order_id)) {
                            echo '<div class="text-danger">Đơn hàng mã #' . $order_id . ' đang được thao tác <strong>Chỉnh sửa</strong> bởi "' . $acc . '"</div><hr>
                                <div class="text-warning">* Chỉnh sửa nhanh: tính năng mới được cập nhật giúp xem và sửa nhanh ghi chú khi chăm sóc đơn hàng.</div>
                                   ';
                            die;
                        } else {
                            if (AdminOrdersDB::is_clicked($order_id) == false) {
                                // $strContent = $checkCensoredPhoneNumber ? 'ẩn số điện thoại' : 'không ẩn số điện thoại';
                                // AdminOrdersDB::update_revision($order_id, false, false, 'Mở nhanh đơn hàng '.$strContent);
                            }
                            DB::update('orders',array('last_online_time' => time(), 'last_edited_account_id' => AdminOrders::$account_id), 'id = ' . DB::escape($order_id) . ' and group_id ='.self::$group_id);
                        }
                        $mobile1 = $order['mobile'];
                        $mobile2 = $order['mobile2'];
                        $length = AdminOrders::$hide_phone_number;

                        if(!$checkCensoredPhoneNumber){
                            $mobile1 = ModifyPhoneNumber::hidePhoneNumber($mobile1, $length);
                            $mobile2 = ModifyPhoneNumber::hidePhoneNumber($mobile2, $length);
                        }
                        $strContent = !$checkCensoredPhoneNumber ? 'ẩn số điện thoại' : 'không ẩn số điện thoại';
                        AdminOrdersDB::update_revision($order_id, false, false, 'Mở nhanh đơn hàng '.$strContent);
                        $mobile = $mobile1 . ($mobile1 ? ' ' : '') . $mobile2;
                        if ($_SERVER['HTTP_HOST'] != 'big.shopal.vn' and $_SERVER['HTTP_HOST'] != 'big02.shopal.vn') {
                            $address = $order['address'] . ', ' . $order['city'];
                        } else {
                            $address = 'Ẩn (vui lòng vào chi tiết đơn hàng)';
                        }
                        $str .= '
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        Họ và tên: ' . $order['customer_name'] . '<br>
                                        Điện thoại: ' . $mobile . '<br>
                                        Địa chỉ: ' . $address . '<br>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        Tổng tiền: ' . System::display_number($order['price']) . '<br>
                                        Giảm giá: ' . System::display_number($order['discount_price']) . '<br>
                                        <span class="text-bold text-success">Thành tiền: ' . System::display_number($order['total_price']) . '</span><br>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Ghi chú chung</th>
                                    <th>Ghi chú 2</th>
                                </tr>
                                <tr>
                                    <td><textarea  name="note1" id="note1" class="form-control">' . $order['note1'] . '</textarea></td>
                                    <td><textarea  name="note2" id="note2" class="form-control"'.(json_decode($order['note2']) ? 'readonly' : '').'>' . $order['note2'] . '</textarea></td>
                                </tr>
                            </table>
                        ';
                    }
                    echo $str;
                    exit();
                    break;
                case 'pos':
                    $group_id = AdminOrders::$group_id;
                    $master_group_id = AdminOrders::$master_group_id;
                    if($id=Url::iget('id')){
                        if(AdminOrders::$account_type==TONG_CONG_TY){//khoand edited in 30/09/2018
                            $cond = ' (orders.group_id='.$group_id.' or orders.master_group_id = '.$group_id.')';
                        }elseif($master_group_id){
                            $cond = ' (orders.group_id='.$group_id.' or (orders.master_group_id = '.$master_group_id.'))';
                        }else{
                            $cond = ' orders.group_id='.$group_id.'';
                        }
                        $cond .= ' and id='.$id.'';
                        AdminOrders::$item = DB::select('orders',$cond);
                        Portal::$document_title = 'QLBH: sửa đơn hàng '.$id;
                        if(!AdminOrders::$item['pos']){
                            Url::js_redirect(true,'Đơn này là đơn bán online. Màn hình sẽ chuyển ngay bây giờ!',['cmd'=>'edit','id'=>AdminOrders::$item['id']]);
                        }
                    }
                    require_once 'forms/pos.php';
                    $this->add_form(new PosForm());
                    break;
                case 'change_order_column_position':
                    $this->change_order_column_position();
                    exit();
                    break;
                case 'get_not_assigned_order_by_source':
                    $group_id = self::$group_id;
                    $groupsId = DB::escape(Url::get('groupsId'));
                    $source_id= DB::escape(Url::get('source_id'));
                    $bundle_id= DB::escape(Url::get('bundle_id'));
                    $phone_store_id=DB::escape(Url::get('phone_store_id'));
                    $total_not_assigned_order = 0;
                    if ($groupsId != 0) {
                        $listAccount = DB::fetch_all_array('select users.id as users_id from account inner join users on users.username = account.id where account_group_id = ' . $groupsId);
                        if (count($listAccount) > 0) {
                            $strUserId = implode(',', array_column($listAccount, 'users_id'));
                            $cond = 'orders.status_id = 10 AND (user_assigned = 0 or user_assigned is null)  AND orders.group_id =' .$group_id .' ';
                            if($source_id){
                                $cond.= ' AND orders.source_id ='.$source_id.' ';
                            }
                            if($bundle_id){
                                $cond.= ' AND orders.bundle_id ='.$bundle_id.' ';
                            }
                            if($phone_store_id){
                                $cond.= ' AND orders_extra.phone_store_id ='.$phone_store_id.' ';
                            }
                            $sql = 'SELECT count(*) as total FROM orders 
                            LEFT JOIN orders_extra ON orders_extra.order_id = orders.id 
                            WHERE '. $cond. ' AND orders.user_created IN (' . $strUserId . ')';
                            $total_not_assigned_order = DB::fetch($sql,'total');
                        }
                    } else {
                        $cond = 'orders.status_id = 10 AND (user_assigned = 0 or user_assigned is null)  AND orders.group_id =' .$group_id .' ';
                        if($source_id){
                            $cond.= ' AND orders.source_id ='.$source_id.' ';
                        }
                        if($bundle_id){
                            $cond.= ' AND orders.bundle_id ='.$bundle_id.' ';
                        }
                        if($phone_store_id){
                            $cond.= ' AND orders_extra.phone_store_id ='.$phone_store_id.' ';
                        }
                        $sql = 'SELECT count(*) as total FROM orders 
                        LEFT JOIN orders_extra ON orders_extra.order_id = orders.id 
                        WHERE '. $cond. '';
                        $total_not_assigned_order = DB::fetch($sql,'total');
                    }
                    echo $total_not_assigned_order; exit();
                    break;
                case 'get_not_assigned_order_by_cs':
                    $group_id = self::$group_id;
                    $cond = 'orders.group_id="'.$group_id.'"';
                    $cond .= '  
                        AND (
                            (orders.status_id = '.THANH_CONG.' AND orders_extra.update_successed_time >= "'.date('Y-m-d',time()-2*24*3600).'")
                        )
                        AND ((select count(id) from order_rating where order_id=orders.id) = 0 or order_rating.cs_status_id=0)
                    ';
                    $sql = '
                        select
                            count(orders.id) as total
                        FROM
                            orders
                            JOIN `groups` ON groups.id = orders.group_id
                            LEFT JOIN  order_rating on order_rating.order_id=orders.id
                            LEFT JOIN  orders_extra on orders_extra.order_id=orders.id
                        WHERE
                            '.$cond.'
                            ';
                    $total_not_assigned_order = DB::fetch($sql,'total');
                    echo $total_not_assigned_order;exit();
                    break;
                case 'get_dupplicates':
                    if($order_id = Url::iget('order_id') and $mobile = DB::escape(Url::get('mobile'))){
                        echo AdminOrdersDB::get_dupplicates($order_id,$mobile);
                    }
                    die;
                case 'get_vichat_history':
                    if($fb_conversation_id = DB::escape(Url::get('fb_conversation_id')) and $page_id = DB::escape(Url::get('page_id'))){
                        if(preg_match('/t_/',$fb_conversation_id)){
                            $this->get_vichat_history($fb_conversation_id,$page_id);
                        }else{
                            $this->get_vichat_history1($fb_conversation_id,$page_id);
                        }
                    }
                    break;
                case "show_modal_shipping":
                    $this->show_modal_shipping();
                    break;
                case "get-list-inventory":
                    $this->get_list_inventory();
                    break;
                case "cancel_order_transport":
                    $this->cancel_order_transport();
                    break;
                case "manager-shipping":
                    if(AdminOrders::$quyen_van_don) {
                        require_once 'forms/manager_shipping.php';
                        $this->add_form(new ManagerShippingForm());
                    }else{
                        die('Bạn không có quyền sử dụng tính năng này!');
                    }
                    break;
                case "shipping-processing":
                    require_once 'forms/shipping_processing.php';
                    $this->add_form(new ShippingProcessingForm());
                    break;
                case "shipping-processing-v3":
                    require_once 'forms/shipping_processing_v3.php';
                    $this->add_form(new ShippingProcessingFormV3());
                    break;
                case "multi-shipping":
                    require_once 'forms/multi_shipping.php';
                    $this->add_form(new MultiShippingForm());
                   break;
                case "shipping_history":
                    require_once 'forms/shipping_history.php';
                    $this->add_form(new ShippingHistoryForm());
                    break;

                case "view_bdhn":
                    require_once 'forms/view_bdhn.php';
                    $this->add_form(new ViewBuuDienHNForm());
                    break;

                case 'waiting_list':
                    $this->waiting_list();
                    break;

                // Cài đặt shop check hiện lịch sử tại popup sửa nhanh thì tất cả tài khoản có quyền xem popup sửa
                // nhanh đều xem được lịch sử đơn ở tất cả các trạng thái.
                // https://pm.tuha.vn/issues/8104
                case 'get_order_history':
                    if(AdminOrders::$is_owner || (AdminOrders::$quyen_sua_don_nhanh && self::$has_show_history_order)){
                        $this->get_order_history();
                    }
                    break;
                case 'get_phone_number':
                    $this->get_phone_number();
                    break;
                case 'check_duplicated':
                    $this->check_duplicated();
                    break;
                case 'get_order_by_keyword':
                    $this->get_order_by_keyword();
                    break;
                case 'get_zone':
                    $this->get_zone();
                    break;
                case 'add_country':
                    $this->add_country();
                    break;
                case 'add_district':
                    $this->add_district();
                    break;
                case 'get_zone_city':
                    $this->get_zone_city();
                    break;
                case 'suggest_customers':
                    $this->suggest_customers();
                    break;
                case 'get_select2_product':
                    $this->get_select2_product();
                    break;
                case 'get_suggest_product':
                    $this->get_suggest_product();
                    break;
                case 'get_product':
                    $this->get_product();
                    break;
                case 'assign_order_by_group':
                    $this->assign_order_by_group();
                    break;
                case 'assign_mkt_order':
                    $this->assign_mkt_order();
                    break;
                case 'assign_upsale_order':
                    $this->assign_upsale_order();
                    break;
                case 'assign_order':
                    $this->assign_order();
                    break;
                case 'change_status':
                    $this->change_status();
                    break;
                case 'import_excel':
                    $this->import_excel();
                    break;
                case 'download_excel_fail':
                    return $this->download_excel_fail();
                case 'export_excel_system':
                    if (is_system_user() && check_system_user_permission('xuatexcel')) {
                        return $this->export_excel_system();
                    } else {
                        die('Bạn không có quyền sử dụng tính năng này!');
                    }
                    break;
                case 'export_ship_excel':
                    if(AdminOrders::$quyen_admin_ke_toan){
                        $this->export_excel(true);
                    }else{
                        die('Bạn không có quyền sử dụng tính năng này!');
                    }
                    break;
                case 'export_excel':
                    if(AdminOrders::$quyen_xuat_excel){
                        if(Url::get('export_type')==1){
                            $this->exportExcelOrSendMail('product');
                        }else{
                            $this->exportExcelOrSendMail('order');
                        }
                    }else{
                        die('Bạn không có quyền sử dụng tính năng này!');
                    }
                    break;
                case 'export_excel_carrier':
                    if(AdminOrders::$quyen_xuat_excel){
                        $this->exportExcelOrSendMail('order',false,true);
                    } else {
                        die('Bạn không có quyền sử dụng tính năng này!');
                    }
                    break;
                case 'send_mail_carrier':
                    if(AdminOrders::$quyen_xuat_excel){
                        $this->exportExcelOrSendMail('order',true);
                    }else{
                        die('Bạn không có quyền sử dụng tính năng này!');
                    }
                    break;
                case 'add_group':
                    $this->add_group();
                    break;
                case 'add':
                    $this->add_cmd();
                    break;
                case 'edit':
                    $this->edit_cmd();
                    break;
                case 'unlink':
                    $this->delete_file();
                    break;
                case 'copy':
                    $this->copy_items();
                    break;
                case 'move':
                    $this->copy_items();
                    break;
                case 'quick_edit':
                    $this->editable_list();
                    break;
                case 'getListCarrier':
                    $this->getlistCarrier();
                    break;
                case 'processSendFileEmailToWarehouse':
                    require_once 'forms/ProcessSendFileEmailToWarehouse.php';
                    $this->add_form(new ProcessSendFileEmailToWarehouse());
                    break;
                case 'sendFileEmailToWarehouse':
                    require_once 'forms/SendFileEmailToWarehouse.php';
                    $this->add_form(new SendFileEmailToWarehouse());
                    break;
                case 'view_history_order':
                    require_once 'forms/view_history_order.php';
                    $this->add_form(new ViewHistoryOrder());
                    break;
                default:
                    if($id=Url::iget('exit_edit')){
                        DB::update('orders',array('last_online_time'=>0,'last_edited_account_id'=>''),'id="'.$id.'"');
                    }
                    $this->list_cmd();
                    break;
            }
        }
    }

    function get_status(){
        $statusId = Url::iget('statusId');
        $cond = ' statuses.id = '.$statusId.'';
        $sql = '
            SELECT 
                statuses.id, statuses.no_revenue,
                IF(statuses.level>0,CONCAT(statuses.level,". ",statuses.name),statuses.name) AS name,
                IF(statuses.is_system=1,statuses.level,statuses_custom.level) as level,
                IF(statuses.is_system=1,statuses.color,statuses_custom.color) as color
            FROM 
                `statuses`
                LEFT JOIN statuses_custom ON statuses_custom.status_id = statuses.id
            WHERE 
                '.$cond.'
            GROUP BY
                statuses.id
            ORDER BY 
                IF(statuses.is_system=1,statuses.level,statuses_custom.level),
                statuses_custom.position,
                statuses.id
        ';

        $items = DB::fetch($sql);
        echo json_encode($items);
    }
    function getlistCarrier(){
        $data_request = array('shop_id' => self::$group_id);
        $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/shop-setting/get', $data_request);
        $arrReturn = array();
        if(count($dataRes) > 0){
            foreach($dataRes['data']['settings']['settings'] as $keyRes => $rowRes){
                if(!empty($rowRes['name'])) {
                    $arrReturn[] = array(
                        'carrierKey' => $keyRes,
                        'name' => $rowRes['name']
                    );
                }
            }
        }
        echo json_encode($arrReturn);
        die();
    }

    function get_list_inventory() {
        $data['success'] = false;
        if (Url::get('token')) {
            $config = AdminOrdersConfig::get_list_shipping_costs();
            $config = $config['api_viettel_post'];
            $token = Url::get('token');
            $headers = ["Token: " . $token];
            $response = $this->execute_get_curl($config['api_get_list_inventory'], $headers);
            $response_api = json_decode($response, true);
            System::debug($response_api); die();
        }
    }

    function cancel_order_transport() {
        $data['success'] = false;
        try {
            $carrierArr = array("api_jt", "api_ghn", "api_best", "api_ghn_v2", "api_bdvn", "api_bigfastv2", "api_ghtk", "api_ems", "api_viettel_post");
            if (Url::get('carrier_id') && Url::get('order_code') && Url::get('order_id')) {
                $order_code = Url::get('order_code');
                $order_id = (int) Url::get('order_id');
                $carrier_id = Url::get('carrier_id');
                $user_name = Url::get('user_name');
                $user_id = Url::get('user_id');

                $check = false;
                if ($user_id == 2646 && $carrier_id == 'api_ghtk') {
                    $check = true;
                }

                if (in_array($carrier_id, $carrierArr) || $check) {

                    if ($carrier_id === 'api_jt') $partner = 'jt';
                    if ($carrier_id === 'api_ems') $partner = 'ems';
                    if ($carrier_id === 'api_best') $partner = 'best';
                    if ($carrier_id === 'api_bdvn') $partner = 'bdvn';
                    if ($carrier_id === 'api_ghtk') $partner = 'ghtk';
                    if ($carrier_id === 'api_bigfastv2') $partner = 'bigfastv2';
                    if ($carrier_id === 'api_viettel_post') $partner = 'viettelpost';
                    if ($carrier_id === 'api_ghn' || $carrier_id === 'api_ghn_v2') $partner = 'ghn';

                    $data_request = array(
                        'order_id' => $order_id,
                        'user_id' => $user_id,
                        'user_name' => $user_name,
                    );
                    $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/partner/' . $partner . '/destroy-order', $data_request);

                    if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                        $data['success'] = true;
                    }
                } elseif (Url::get('id')) {
                    $id = (int) Url::get('id');
                    $cost_config = AdminOrdersConfig::get_list_shipping_costs();
                    $status_config = AdminOrdersConfig::config_shipping_status();
                    $transport_config = $cost_config[$carrier_id];

                    // get token from api
                    $token = ''; //tuha15#bdhn
                    $data_request = array("order_arr" => array($order_id));
                    $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/get', $data_request);
                    if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                        $orderDetail = $dataRes['data'][0];
                        $token = (string) $orderDetail['carrier']['setting_token'];

                        if ($token) {
                            if ($carrier_id == 'api_ghtk') {
                                $curl = curl_init();

                                curl_setopt_array($curl, array(
                                    CURLOPT_URL => $transport_config['cancel_shipping_url'] . "/$order_code",
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_CUSTOMREQUEST => "POST",
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_HTTPHEADER => array(
                                        "Token: " . $token,
                                    ),
                                ));

                                $response = curl_exec($curl);
                                curl_close($curl);

                                $response_api = json_decode($response, true);
                                if ($response_api['success']) {
                                    DB::update_id('orders_shipping', ['shipping_status' => $status_config['HUY']], $id);

                                    DB::insert('order_shipping_status_history', [
                                        'order_id' => $order_id,
                                        'status' => $status_config['HUY'],
                                        'created_at' => date('Y-m-d H:i:s')
                                    ]);

                                    $data_request = array("order_id" => $order_id, "status" => 4, "note" => "Hủy đơn");
                                    EleFunc::cUrlPost(HOST_API . '/api/transport/cancel', $data_request);

                                    $data['success'] = true;
                                }
                            } elseif ($carrier_id == 'api_ghn') {
                                $params = [
                                    'token' => $token,
                                    'OrderCode' => $order_code
                                ];
                                $response_api = AdminOrdersConfig::execute_curl($transport_config['cancel_shipping_url'], json_encode($params));
                                $response_api = json_decode($response_api, true);
                                if ($response_api['code'] == 1) {
                                    DB::update_id('orders_shipping', ['shipping_status' => $status_config['HUY']], $id);

                                    DB::insert('order_shipping_status_history', [
                                        'order_id' => $order_id,
                                        'status' => $status_config['HUY'],
                                        'created_at' => date('Y-m-d H:i:s')
                                    ]);

                                    $data_request = array("order_id" => $order_id, "status" => 4, "note" => "Hủy đơn");
                                    EleFunc::cUrlPost(HOST_API . '/api/transport/cancel', $data_request);

                                    $data['success'] = true;
                                }
                            } elseif ($carrier_id == 'api_bdhn') {
                                $soapclient = new SoapClient('http://buudienhanoi.com.vn/Nhanh/BDHNNhanh.asmx?WSDL');
                                // echo $token; die();
                                if (empty($token)) {
                                    // echo json_encode($data); die();
                                    return;
                                }

                                // $ma_xac_thuc = get_group_options('vnpost_hn_verify_code');
                                $params = [
                                    'sodonhang' => $order_id,
                                    'maxacthuc' => $token
                                ];
                                $response = $soapclient->HuyYeuCauThuGom($params);
                                $data['response'] = $response;
                                $data['params'] = $params;
                                $response_api = explode('|', $response->HuyYeuCauThuGomResult);
                                $response_code = $response_api[0];
                                // System::debug($response_code); die();
                                if ($response_code == 0) {
                                    DB::update_id('orders_shipping', ['shipping_status' => $status_config['HUY']], $id);

                                    DB::insert('order_shipping_status_history', [
                                        'order_id' => $order_id,
                                        'status' => $status_config['HUY'],
                                        'created_at' => date('Y-m-d H:i:s')
                                    ]);

                                    $data_request = array("order_id" => $order_id, "status" => 4, "note" => "Hủy đơn");
                                    EleFunc::cUrlPost(HOST_API . '/api/transport/cancel', $data_request);

                                    $data['success'] = true;
                                }
                            } elseif ($carrier_id == 'api_viettel_post') {
                                if (empty($token)) {
                                    return;
                                }

                                $params = [
                                    'TYPE' => 4, // Hủy đơn hàng
                                    'ORDER_NUMBER' => $order_code,
                                    'NOTE' => "Hủy đơn"
                                ];
                                $response_api = AdminOrdersConfig::execute_post_curl($transport_config['api_cancel_order'], json_encode($params), $token);
                                $response_api = json_decode($response_api, true);

                                if ($response_api['status'] == 200) {
                                    DB::update_id('orders_shipping', ['shipping_status' => $status_config['HUY']], $id);

                                    DB::insert('order_shipping_status_history', [
                                        'order_id' => $order_id,
                                        'status' => $status_config['HUY'],
                                        'created_at' => date('Y-m-d H:i:s')
                                    ]);

                                    $data_request = array("order_id" => $order_id, "status" => 4, "note" => "Hủy đơn");
                                    EleFunc::cUrlPost(HOST_API . '/api/transport/cancel', $data_request);

                                    $data['success'] = true;
                                } else {
                                    $data['error'] = $response_api;
                                }
                            } elseif ($carrier_id == 'api_ems') {
                                if (empty($token)) {
                                    return;
                                }

                                $params = [
                                    'tracking_code' => $order_code
                                ];
                                $urlCancelOrder = $transport_config['api_cancel_order'] . '?merchant_token=' . $token;
                                $response_api = AdminOrdersConfig::execute_post_curl($urlCancelOrder, json_encode($params));
                                $response_api = json_decode($response_api, true);
                                if ($response_api['code'] !== 'error') {
                                    DB::update_id('orders_shipping', ['shipping_status' => $status_config['HUY']], $id);

                                    DB::insert('order_shipping_status_history', [
                                        'order_id' => $order_id,
                                        'status' => $status_config['HUY'],
                                        'created_at' => date('Y-m-d H:i:s')
                                    ]);

                                    $data['success'] = true;
                                } else {
                                    $data['error'] = $response_api;
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {

        }

        echo json_encode($data); die();
    }

    /**
     * { function_description }
     *
     * @param      array  $props  The properties
     *
     * @return     bool   ( description_of_the_return_value )
     */
    public static function validateMedidoc(array $props)
    {
        switch($props['form']){
            case 'TANG_CHIEU_CAO':
                $validFields = ['can_nang', 'chieu_cao', 'chieu_cao_chong', 'chieu_cao_vo', 'ghi_chu', 'gioi_tinh', 'ho_ten', 'ho_ten_chong', 'ho_ten_vo', 'ma_BHYT', 'ngay_sinh', 'phan_loai', 'tu_van_vien'];

                return self::validateFormTangCHieuCao($props, $validFields);

            case 'TANG_CHIEU_CAO_NEW':
                $validFields = ['can_nang', 'chieu_cao', 'ghi_chu', 'gioi_tinh', 'ho_ten', 'ngay_tao', 'ngay_sinh', 'phan_loai', 'tu_van_vien'];

                return self::validateFormTangCHieuCao($props, $validFields);

            break;

            case 'TRI_MAT_NGU':
                return true;

            case 'SP_TOC':
                return true;

            case 'GIAM_CAN':
                return true;

            case 'MO_HOI':
                return true;

            case 'TIEU_DUONG_MO_MAU':
                return true;

            default:
                return false;
        }

    }

    /**
     * { function_description }
     *
     * @param      array  $props        The properties
     * @param      array  $validFields  The valid fields
     *
     * @return     bool   ( description_of_the_return_value )
     */
    private static function validateFormTangCHieuCao(array $props, array $validFields)
    {
        if(!$props = $props['data']){
            return false;
        }

        foreach($props as $prop){
            $keys = array_keys($prop);
            if(array_diff($validFields, $keys)){
                return false;
            }
        }

        return true;
    }

    function show_modal_shipping()
    {
        $data = ['success' => false];
        $errors = [];
        try {
            $post_fields_required = ['Weight', 'FromDistrictID', 'ToDistrictID'];
            foreach ($post_fields_required as $value) {
                if (empty($_POST[$value])) {
                    $errors[] = 'Field ' . $value . ' is required';
                }
            }

            if (!empty($errors)) {
                $data['errors'] = $errors;
                echo json_encode($data); die();
            }

            $shipping_option = [];
            if (!empty($_POST['shipping_option'])) {
                $shipping_option = AdminOrdersDB::getShippingOptionById($_POST['shipping_option']);
            }

            $costs = [];
            $arr_shipping_costs = AdminOrdersConfig::get_list_shipping_costs();
            foreach ($arr_shipping_costs as $k => $item) {
                if ($k == 'api_ghtk') {
                    $total_shipping_option = AdminOrdersDB::getShippingOptionByCarrierId($k);
                    if (empty($total_shipping_option)) {
                        continue;
                    }
                    $arr_post_fileds = [
                        "weight" => (int)$_POST['Weight'],
                        "pick_province" => $_POST['FromProvinceName'],
                        "pick_district" => $_POST['FromDistrictName'],
                        "province" => $_POST['ToProvinceName'],
                        "district" => $_POST['ToDistrictName'],
                        'transport' => $_POST['transport_ghtk'] ?? 'road'
                    ];
                    $curl = curl_init();
                    $token = "";
                    if (empty($shipping_option)) {
                        $shipping_option = AdminOrdersDB::getShippingOptionByCarrierId($k);
                    } elseif ($shipping_option['carrier_id'] != $k) {
                        $shipping_option = AdminOrdersDB::getShippingOptionByCarrierId($k);
                    }

                    if (!empty($shipping_option)) {
                        $token = $shipping_option['token'];
                    }

                    if (empty($token)) {
                        continue;
                    }

                    $arr = array(
                        CURLOPT_URL => $item['api_calculatefee_url'] . "?" . http_build_query($arr_post_fileds),
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_HTTPHEADER => array(
                            "Token: " . $token,
                        ),
                    );
                    curl_setopt_array($curl, $arr);
                    $response = curl_exec($curl);

                    curl_close($curl);
                    $results = json_decode($response, true);
                    if ($results['success']) {
                        if ($results['fee']['delivery']) {
                            $costs[] = [
                                'calculatedFee' => $results['fee']['fee'],
                                'CoDFee' => 0,
                                'logo' => $item['avatar'],
                                'id' => $k,
                                'ServiceName' => 'Giao nhanh',
                                'expected_time' => '1-3 ngày',
                                'key' => 'ghtk',
                                'title' => 'Giao hàng tiết kiệm',
                                'totalFee' => $results['fee']['fee'],
                                'isDefault' => $total_shipping_option['is_default']
                            ];
                        }
                    }

                } else if ($k == 'api_ghn') {
                    $total_shipping_option = AdminOrdersDB::getShippingOptionByCarrierId($k);
                    if (empty($total_shipping_option)) {
                        continue;
                    }

                    $calculatefee_url = $item['api_calculatefee_url'];
                    $service_url = $item['api_find_service_url'];
                    $ghn_token = "";
                    if (empty($shipping_option)) {
                        $shipping_option = AdminOrdersDB::getShippingOptionByCarrierId($k);
                    } elseif ($shipping_option['carrier_id'] != $k) {
                        $shipping_option = AdminOrdersDB::getShippingOptionByCarrierId($k);
                    }

                    if (!empty($shipping_option)) {
                        $ghn_token = $shipping_option['token'];
                    }

                    if (empty($ghn_token)) {
                        continue;
                    }

                    $pick_option = $_POST['pick_option'];
                    $arr_post_fileds = [
                        "token" => $ghn_token,
                        "Weight" => (int)$_POST['Weight'],
                        "FromDistrictID" => (int)$_POST['FromDistrictID'],
                        "ToDistrictID" => (int)$_POST['ToDistrictID']
                    ];
                    $services = json_decode(AdminOrdersConfig::execute_curl($service_url, json_encode($arr_post_fileds)), true);

                    if (!empty($_POST['Length'])) {
                        $arr_post_fileds['Length'] = (int)$_POST['Length'];
                    }

                    if (!empty($_POST['Width'])) {
                        $arr_post_fileds['Width'] = (int)$_POST['Width'];
                    }

                    if (!empty($_POST['Height'])) {
                        $arr_post_fileds['Height'] = (int)$_POST['Height'];
                    }

                    if ($pick_option == 'post') {
                        $arr_post_fileds['OrderCosts'] = [
                            [
                                'ServiceID' => 53337
                            ]
                        ];
                    }
                    if ($services['code']) {
                        $services = $services['data'];
                        foreach ($services as $key => $value) {
                            $arr_post_fileds['ServiceID'] = $value['ServiceID'];

                            $response = json_decode(AdminOrdersConfig::execute_curl($calculatefee_url, json_encode($arr_post_fileds)), true);
                            if ($response['code']) {
                                $serviceFee = !empty($response['data']['DiscountFee']) ? $response['data']['DiscountFee'] : $response['data']['CalculatedFee'];
                                $codFee = $response['data']['CoDFee'];
                                $totalFee = $serviceFee + $codFee;
                                $costs[] = [
                                    'calculatedFee' => $serviceFee,
                                    'CoDFee' => $codFee,
                                    'logo' => $item['avatar'],
                                    'id' => $k,
                                    'ServiceName' => 'Giao ' . $value['Name'],
                                    'expected_time' => $arr_shipping_costs[$k]['service_list_period'][$value['ServiceID']],
                                    'key' => 'ghn_' . $value['ServiceID'],
                                    'title' => 'Giao hàng nhanh',
                                    'ServiceId' => $value['ServiceID'],
                                    'totalFee' => $totalFee,
                                    'isDefault' => $key == 0 ? $total_shipping_option['is_default'] : 0
                                ];
                            }
                        }
                    }
                } else if ($k == 'api_bdhn') {
                    $total_shipping_option = AdminOrdersDB::getShippingOptionByCarrierId($k);
                    if (empty($total_shipping_option)) {
                        continue;
                    }

                    $costs[] = [
                        'calculatedFee' =>0,
                        'CoDFee' => 0,
                        'logo' => $item['avatar'],
                        'id' => $k,
                        'ServiceName' => 'Giao nhanh',
                        'expected_time' => '1-3 ngày',
                        'key' => 'ghbdhn',
                        'title' => $item['name'],
                        'totalFee' => 0,
                        'isDefault' => $total_shipping_option['is_default']
                    ];
                }
            }

            $data = [
                'success' => true,
                'data' => $costs
            ];

            echo json_encode($data); die();
        } catch (Exception $e) {
            echo "Lỗi lấy thông tin shipping."; die();

        }

        echo json_encode($data); die();
    }

    function custom_mb_strtolower($string)
    {
        $string = trim(str_replace(['–', '-', '\r\n'], '', $string));

        return mb_strtolower($string);
    }

    function get_zones_viettel_id($params = [], $config)
    {
        $from_province_name = $this->custom_mb_strtolower($params['from_province_name']);
        $to_province_name = $this->custom_mb_strtolower($params['to_province_name']);
        $from_district_name = $this->custom_mb_strtolower($params['from_district_name']);
        $to_district_name = $this->custom_mb_strtolower($params['to_district_name']);
        $from_province_id = ""; $to_province_id = ""; $from_district_id = ""; $to_district_id = "";
        $response_province = json_decode($this->execute_get_curl($config['api_list_province_url']), true);
        if ($response_province['status'] == 200) {
            $provinces = $response_province['data'];
            foreach ($provinces as $province) {
                $province_name_service = $this->custom_mb_strtolower($province['PROVINCE_NAME']);
                if ($province_name_service == $from_province_name) {
                    $from_province_id = $province['PROVINCE_ID'];
                }

                if ($province_name_service == $to_province_name) {
                    $to_province_id = $province['PROVINCE_ID'];
                }

                if (!empty($from_province_id) && !empty($to_province_id)) {
                    break;
                }
            }

            if (!empty($from_province_id) && !empty($to_province_id)) {
                $url_district = $config['api_list_district_url'];
                $response_from_district = json_decode($this->execute_get_curl($url_district . '?provinceId=' . $from_province_id), true);
                if ($response_from_district['status'] == 200) {
                    $districts = $response_from_district['data'];
                    foreach ($districts as $district) {
                        $district_name_service = $this->custom_mb_strtolower($district['DISTRICT_NAME']);
                        if ($district_name_service == $from_district_name) {
                            $from_district_id = $district['DISTRICT_ID'];
                            break;
                        }
                    }
                }

                $response_to_district = json_decode($this->execute_get_curl($url_district . '?provinceId=' . $to_province_id), true);
                if ($response_to_district['status'] == 200) {
                    $districts = $response_to_district['data'];
                    foreach ($districts as $district) {
                        $district_name_service = $this->custom_mb_strtolower($district['DISTRICT_NAME']);
                        if ($district_name_service == $to_district_name) {
                            $to_district_id = $district['DISTRICT_ID'];
                            break;
                        }
                    }
                }
            }
        }

        return [
            'from_province_id' => $from_province_id,
            'to_province_id' => $to_province_id,
            'from_district_id' => $from_district_id,
            'to_district_id' => $to_district_id
        ];
    }

    function execute_get_curl($url, $headers = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);

        curl_close ($ch);

        return $response;
    }

    function get_order_by_keyword(){
        if($keyword = DB::escape(trim(URL::get('term'))) and strlen($keyword) > 1){
            $cond = 'orders.group_id='.self::$group_id.'';
            $cond .= ' AND (
                crm_customer.name like "%'.$keyword.'%" 
            )';
            $sql = '
                SELECT 
                    crm_customer.id,crm_customer.mobile,crm_customer.name as customer_name,
                    crm_customer.address,
                    crm_customer.zone_id as city_id,
                    zone_provinces_v2.province_name as city,
                    CONCAT(crm_customer.mobile," - Khách: ",crm_customer.name," - ĐC: ",crm_customer.address,", ",zone_provinces_v2.province_name ) AS label
                FROM 
                    crm_customer 
                    left join zone_provinces_v2 on zone_provinces_v2.province_id = crm_customer.zone_id
                WHERE '.$cond.'
            ';
            if($items = DB::fetch_all($sql)){
                System::sksort($items,'id','asc');
                echo json_encode($items);
                exit();
            }
        }
    }
    function import_excel(){
        Portal::$document_title = 'QLBH: Import Excel DATA số';
        require_once 'forms/import_excel.php';
        $this->add_form(new ImportExcelForm());
    }

    function export_excel_system(){
        Portal::$document_title = 'Xuất excel đơn hàng hệ thống';
        require_once 'forms/export_excel_system.php';
        $this->add_form(new ExportExcelSystemForm());
    }

    /**
     * Downloads an excel fail.
     */
    private function download_excel_fail()
    {
        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator("QLBH")
            ->setLastModifiedBy("QLBH")
            ->setTitle("Sản phẩm hệ thống")
            ->setSubject("Sản phẩm hệ thống");

        $rows = Session::get('order_import_excel_fail_rows');
        $columns = Session::get('order_import_excel_fail_columns');

        $sheet = $spreadsheet->getActiveSheet()->fromArray($rows);

        foreach ($columns as $rowIdx => $cellErrors) {
            foreach ($cellErrors as $cellIdx) {
                $sheet->getStyleByColumnAndRow($cellIdx, $rowIdx+1)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('ffff7103');
            }
        }

        $objWriter = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        ob_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="danh-sach-don-hang-import-khong-thanh-cong-' . $_SESSION['user_id'] . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter->save('php://output');

        exit;
    }

    function copy_items(){
        if(isset($_REQUEST['selected_ids']) and count($_REQUEST['selected_ids'])>0){
            require_once 'forms/copy.php';
            $this->add_form(new CopyAdminOrdersForm());
        }else{
            Url::redirect_current(array('cmd'=>'list'));
        }
    }
    function delete_file(){
        if(Url::get('link') and file_exists(Url::get('link'))){
            @unlink(Url::get('link'));
        }
        echo '<script>window.close();</script>';
    }
    function waiting_list(){
        require_once 'forms/waiting_list.php';
        $this->add_form(new WaitingListForm());
    }
    function editable_list(){
        if(AdminOrders::$admin_group){
            require_once 'forms/editable_list.php';
            $this->add_form(new EditableListForm());
        }else{
            die('Bạn không có quyền sử dụng tính năng này!');
        }
    }
    function list_cmd(){
        Portal::$document_title = 'QLBH: Danh sách đơn hàng - data số';
        if(AdminOrders::$account_type == TONG_CONG_TY or self::$master_group_id){
           // die('Hệ thống tạm dừng trong 5 phút để hiệu chỉnh lại tốc độ server. Thành thật xin lỗi quý khách vì sự bất tiện!');
            //require_once 'forms/list_zoma.php';
            require_once 'forms/list.php';
        }else{
            require_once 'forms/list.php';
        }

        $this->add_form(new ListAdminOrdersForm());
    }
    function add_cmd(){
        Portal::$document_title = 'QLBH: thêm đơn hàng mới';
        if(User::is_login() and self::$group_id){
            require_once 'forms/edit.php';
            $this->add_form(new EditAdminOrdersForm());
        }else{
            Url::access_denied();
        }
    }
    function edit_cmd(){
        $group_id = AdminOrders::$group_id;
        $master_group_id = AdminOrders::$master_group_id;
        if($id=Url::iget('id')){
            if(AdminOrders::$account_type==TONG_CONG_TY){//khoand edited in 30/09/2018
                $cond = ' (orders.group_id='.$group_id.' or groups.master_group_id = '.$group_id.')';
            }elseif($master_group_id){
                $cond = ' (orders.group_id='.$group_id.' or (groups.master_group_id = '.$master_group_id.'))';
            }else{
                $cond = ' orders.group_id='.$group_id.'';
            }
            $cond .= ' and orders.id='.$id.'';
            $sql = '
                SELECT
                    orders.*
                FROM
                    orders
                    JOIN `groups` ON groups.id = orders.group_id
                WHERE
                    '.$cond.'
            ';
            Portal::$document_title = 'QLBH: sửa đơn hàng '.$id;
            if(!AdminOrders::$item = DB::fetch($sql)){
                die('Đơn hàng không tồn tại trên shop của bạn!');
            }else{
                if(AdminOrders::$item['pos']){
                    Url::js_redirect(true,'Đơn này là đơn bán lẻ. Màn hình sẽ chuyển ngay bây giờ!',['cmd'=>'pos','id'=>AdminOrders::$item['id']]);
                }
            }
        }
        if(User::is_login() and self::$group_id){
            require_once 'forms/edit.php';
            $this->add_form(new EditAdminOrdersForm());
        }else{
            Url::access_denied();
        }
    }
    function add_group(){
        if($value=DB::escape(Url::get('value'))){
            DB::insert('bundles',array('name'=>$value,'group_id'=>self::$group_id,'created'=>date('Y-m-d H:i:s')));
            echo 'TRUE';
        }else{
            echo 'EMPTY';
        }
        exit();
    }
    function assign_order_by_group(){
        if($ids=(Url::get('ids')) and $account_group_id = Url::iget('account_group_id') and (self::$quyen_chia_don or self::$is_account_group_manager)){
            $idsArr = explode(',',$ids);
            $escape_ids = array_map(function($id){
                return intval($id);
            }, $idsArr);

            $ids = implode(',', $escape_ids);
            if($account_group_id and DB::exists('select id from account_group WHERE id="'.$account_group_id.'"')) {
                $cond = 'orders.id IN ('.$ids.')';
                $cond.= ' and orders.group_id='.self::$group_id.'';
                //$items = AdminOrdersDB::get_items($cond,'orders.created DESC',false);
                $items = AdminOrdersDB::get_assign_items($cond,false,'orders.id DESC');
                if(AdminOrdersDB::autoAssignOrder(['acc_group_id'=>$account_group_id,'items'=>$items])){
                    echo 'TRUE';
                }else{
                    echo 'FALSE1';
                }
            }else{
                echo 'FALSE2';
            }
        }else{
            echo 'FALSE3';
        }
        exit();
    }
    function assign_order()
    {
        if(!self::$quyen_chia_don && !self::$is_account_group_manager){
            die('FALSE');
        }

        $acc_id = DB::escape(Url::get('account_id'));
        if($acc_id != -1){
            $account = DB::fetch('SELECT `id` FROM `account` WHERE `id` ="' .$acc_id . '" AND `group_id` = ' . self::$group_id);
            if(!$account){
                die('FALSE');
            }
        }

        $ids = parse_id(Url::get('ids'));
        $orders = DB::fetch('SELECT count(*) as num_order FROM orders WHERE id IN (' . implode(',', $ids) . ') AND group_id = '. self::$group_id);
        if($orders['num_order'] != count($ids) || !$ids){
            die('FALSE');
        }

        foreach ($ids as $val) {
            if($acc_id == '-1'){
                $log_content = ' Đơn hàng đã hủy gán';
                $update = ['user_assigned'=>'0', 'assigned'=>self::$date_init_value];
            }

            else{
                $user_id = DB::fetch('select id from users WHERE username="'.$acc_id.'"','id');
                $order = DB::fetch('select id,first_user_assigned from orders where id='.$val);
                $update = ['user_assigned'=>$user_id, 'assigned'=> date("Y-m-d H:i:s")];

                if(!$order['first_user_assigned']){
                    $update['first_user_assigned'] = get_user_id();
                    $update['first_assigned'] = date('Y-m-d H:i:s');
                }

                $log_content = ' Đơn hàng được gán cho tài khoản "'.$acc_id.'"';
            }

            DB::update('orders', $update, 'id = '.$val);
            AdminOrdersDB::update_revision($val, false,false, $log_content);
        }

        die($acc_id == '-1' ? 'CANCEL_ASSIGNMENT' : 'TRUE');
    }
    function assign_mkt_order(){
        $idsArr = parse_id(Url::get('ids'));
        $idsStr = implode( ',', $idsArr);
        
        $orders = DB::fetch('SELECT count(*) as num_order FROM orders WHERE id IN (' . $idsStr  . ') AND group_id = '. self::$group_id);
        if($orders['num_order'] != count($idsArr) || !$idsArr){
            die('FALSE');
        }

        $acc_id = DB::escape(Url::get('account_id'));
        if($acc_id == -1){
            $message = 'Hủy người tạo';
            $userID = 'NULL';
        }else if($userID = AdminOrdersDB::getUserIdByUsername($acc_id, self::$group_id)){
            $message = ' Đơn hàng được gán cho tài khoản marketing "'.$acc_id.'"';
            $userID = DB::fetch('select id from users WHERE username="'.$acc_id.'"','id');
        }else{
            die('FALSE');
        }

        foreach ($idsArr as $val) {
            AdminOrdersDB::update_revision($val, false,false, $message);
        }

        DB::update('orders',array('user_created'=>$userID),'id IN ('. $idsStr .')');

        die('TRUE');
    }

    /**
     * Gán uupsale cho danh sách đơn hàng
     */
    private function assign_upsale_order()
    {
        if(!AdminOrders::$is_owner && !AdminOrders::$admin_group && !AdminOrders::$quyen_chia_don && !is_account_group_manager(AdminOrders::$group_id)){
            RequestHandler::sendJsonError('FALSE');
        }

        $orderIDs = parse_id(Url::get('ids'));

        $orders = DB::fetch('SELECT count(*) as num_order FROM orders WHERE id IN (' . implode(',', $orderIDs) . ') AND group_id = '. self::$group_id);
        if($orders['num_order'] != count($orderIDs) || !$orderIDs){
            RequestHandler::sendJsonError('FALSE');
        }

        $upSaleUsername = Url::getString('account_id');

        // Hủy upsale
        if($upSaleUsername == -1){
            $message = 'Hủy gán Upsale';
            $userID = 'NULL';
        }

        // Tồn tại user
        else if($userID = AdminOrdersDB::getUserIdByUsername($upSaleUsername, self::$group_id)){
            $message = ' Đơn hàng được gán upsale cho "'.$upSaleUsername.'"';
        }

        // Không tồn tại user
        else{
            RequestHandler::sendJsonError('FALSE');
        }

        if(DB::update('orders_extra', ['upsale_from_user_id' => $userID],'order_id IN ('. implode(',', $orderIDs) .')')){
            // Log lịch sử
            foreach ($orderIDs as $val) {
                AdminOrdersDB::update_revision($val, false,false, $message);
            }
            RequestHandler::sendJsonSuccess('TRUE');
        }

        RequestHandler::sendJsonError('FALSE');
    }

    function change_status(){
        $status = DB::escape(Url::get('status'));
        $ids = parse_id(Url::get('ids'));
        $userID = get_user_id();
        $statuses = AdminOrders::$admin_group ? AdminOrdersDB::get_status() : AdminOrdersDB::get_status_from_roles($userID);
        $statusIdsLv3 = [];
        foreach ($statuses as $value) {
            if($value['level'] >= 3 && $value['id'] != CHUYEN_HANG){
                $statusIdsLv3[] = $value['id'];
            }
        }
        $orders = DB::fetch_all('SELECT id,user_delivered,city,city_id,user_confirmed,type,confirmed,DATE(created) as created FROM orders WHERE id IN (' . implode(',', $ids) . ') AND group_id = '. self::$group_id);
        $num_order = sizeof($orders);
        if($num_order != count($ids) || !$ids || !$status){
            die('FALSE');
        }
        $orderCityFail = '';
        $orderTypeFail = '';
        $orderSuccess = [];
        $orderArrCityFail = [];
        $orderArrTypeFail = [];
        $orderArrLv3Fail = [];
        $orderFail = [];
        foreach ($orders as $key => $value) {
            if ( empty($value['city_id']) && $status == XAC_NHAN && strtotime($value['created']) >= strtotime(AdminOrders::VALIDATE_DATE_DEPLOY) ) {
                $orderCityFail .= ($orderCityFail ? ', ' : '') . $value['id'];
                $orderArrCityFail[] = $value['id'];
            }
            if(empty($value['type']) && $status == XAC_NHAN && strtotime($value['created']) >= strtotime(AdminOrders::VALIDATE_DATE_DEPLOY_TYPE) ) {
                $orderTypeFail .= ($orderTypeFail ? ', ' : '') . $value['id'];
                $orderArrTypeFail[] = $value['id'];
            }
            if(empty($value['user_delivered']) && in_array($status, $statusIdsLv3)){
                $orderArrLv3Fail[] = $value['id'];
            }
        }
        if(!empty($orderArrCityFail) || !empty($orderArrTypeFail) || !empty($orderArrLv3Fail)){
            $orderFail = array_unique(array_merge($orderArrCityFail,$orderArrTypeFail,$orderArrLv3Fail));
        }
        if(sizeof($orderFail) != sizeof($ids)){
            $orderSuccess = array_diff($ids, $orderFail);
        }
        if(empty($orderSuccess)){
            $this->handleErrorsTypeAndDistric($orderCityFail, $orderTypeFail);
        }
        // if ((!empty($orderArrCityFail) || !empty($orderArrTypeFail)) && !empty($orderSuccess)) {
        //     $ids = $orderSuccess;
        // }
        $ids = $orderSuccess;
        $ex_ids = '';
        $im_ids = '';
        $errors = []; // danh sách lỗi nếu có khi chuyển trạng thái theo từng ID đơn hàng
        $userID = get_user_id();
        $idsTrue = [];
        $statuses = AdminOrders::$admin_group ? AdminOrdersDB::get_status() : AdminOrdersDB::get_status_from_roles($userID);
        require_once ROOT_PATH . 'packages/vissale/modules/AdminOrders/OrderStatusUpdater.php';
        foreach ($ids as $id) {
            $old_status = DB::fetch('select status_id,IFNULL(user_confirmed,0) AS user_confirmed,IFNULL(user_delivered,0) AS user_delivered from orders where id='.$id . ' AND group_id = ' . AdminOrders::$group_id);
            $errors[$id] = OrderStatusUpdater::setOrder($id, AdminOrders::$group_id)
                ->setStatusID($status)
                ->setUserID($userID)
                ->canCreateInvoice(false)
                ->setStatuses($statuses)
                ->exec()
                ->getErrors();

            if($errors[$id]){
                continue;
            }
            if($id){
                $idsTrue[$id] = [
                    'id'=>$id,
                    'old_status'=>$old_status['status_id']
                ];
            }
            if((AdminOrders::$create_export_invoice_when_confirmed)){
                if(!$old_status['user_confirmed'] and $old_status['status_id'] != $status and $status==XAC_NHAN){
                    $ex_ids .= ($ex_ids?',':'').$id;
                }
            }else{
                if(AdminOrders::$create_export_invoice_when_delivered){
                    if(!$old_status['user_delivered'] and $old_status['status_id'] != $status and $status==CHUYEN_HANG){
                        $ex_ids .= ($ex_ids?',':'').$id;
                    }
                }
            }
            if($old_status['status_id'] != $status and $status==TRA_VE_KHO){
                $im_ids .= ($im_ids?',':'').$id;
            }
        }
        if($ex_ids){
            if((AdminOrders::$create_export_invoice_when_confirmed)){
                require_once ('packages/vissale/modules/QlbhStockInvoice/db.php');
                QlbhStockInvoiceDB::xuat_kho($ex_ids);
            }else{
                if(AdminOrders::$create_export_invoice_when_delivered){
                    require_once ('packages/vissale/modules/QlbhStockInvoice/db.php');
                    QlbhStockInvoiceDB::xuat_kho($ex_ids);
                }
            }
        }
        if(AdminOrders::$create_import_invoice_when_return and $im_ids){
            require_once ('packages/vissale/modules/QlbhStockInvoice/db.php');
            QlbhStockInvoiceDB::nhap_kho($im_ids);
        }
        if (defined('BIGGAME_SYNC') && BIGGAME_SYNC === 1) {
            $this->processDataToApiBigGame($idsTrue, $status);
        }

        $this->handleUpdateStatusErrors($errors, $orderCityFail, $orderTypeFail, $orderArrLv3Fail);
    }

    /**
     * { function_description }
     *
     * @param      array  $errors  The errors
     */
    private function processDataToApiBigGame($idsTrue, $status){
        $ids = array_keys($idsTrue);
        $esIds = DB::escapeArray($ids);
        $strIds = implode(',', $esIds);
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
                    orders.id IN ($strIds);
                ";
        $result = DB::fetch_all($sql);
        $data = [];
        foreach ($result as $key => $value) {
            if(!empty($value['user_confirmed'])){
                $data[$key] = [
                    'id' => $value['id'],
                    'confirmed' => $value['confirmed']??'',
                    'user_confirmed' => $value['user_confirmed']??'',
                    'total_price' => $value['total_price'],
                    'group_id' => $value['group_id']??'',
                    'group_name' => $value['group_name']??'',
                    'user_confirmed_name' => $value['user_confirmed_name'],
                    'accounting_confirmed' => $value['accounting_confirmed']??'',
                    'status_id' => $value['status_id'],
                    'no_revenue' => $value['status_no_revenue']??1,
                    'level' => $value['status_level']??0
                ];
            }
        }
        BiggameAPI::instance()->sendListCarts($data);
        /*
        $url = TUHA_BIGGAME_ENDPOINT;
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
    private function handleErrorsTypeAndDistric($orderCityFail, $orderTypeFail){
        $messages = [];
        if($orderCityFail){
            $orderArrCityFail = explode(',', $orderCityFail);
            $messages[] =  'Các đơn hàng chưa nhập tỉnh thành ( '.sizeof($orderArrCityFail).' đơn ) : '.$orderCityFail;
        }
        if($orderTypeFail){
            $orderArrTypeFail = explode(',', $orderTypeFail);
            $messages[] =  'Các đơn hàng chưa chọn loại đơn ( '.sizeof($orderArrTypeFail).' đơn ) : '.$orderTypeFail;
        }
        if(!empty($messages)){
            RequestHandler::sendJsonError(['data' =>implode("\n",$messages)]);
        }

    }
    private function handleUpdateStatusErrors(array $ordersErrors, string $orderCityFail, string $orderTypeFail, array $orderArrLv3Fail)
    {

        $messages = [
            OrderStatusUpdater::UNCONFIRMED_ORDER => [
                'id' => [],
                'msg' => 'Các đơn hàng phải qua trạng thái Xác nhận chốt đơn mới chuyển được lên level cao hơn: '
            ],
            OrderStatusUpdater::UNKNOW_STATUS => [
                'id' => [],
                'msg' => 'Trạng thái chuyển đến không hợp lệ: '
            ],
            OrderStatusUpdater::ORDER_ID_OR_GROUP_ID_INVALID => [
                'id' => [],
                'msg' => 'ID đơn hàng hoặc ID shop không hợp lệ: '
            ],
            OrderStatusUpdater::USER_INVALID => [
                'id' => [],
                'msg' => 'Thông tin người dùng không hợp lệ: '
            ],
            OrderStatusUpdater::ORDER_NOT_EXISTS => [
                'id' => [],
                'msg' => 'Không tồn tại đơn hàng: '
            ],
            OrderStatusUpdater::EMPTY_STATUSES => [
                'id' => [],
                'msg' => 'Danh sách trạng thái rỗng: '
            ],
            OrderStatusUpdater::NO_STATUS_CHANGE => [
                'id' => [],
                'msg' => 'Không có sự thay đổi trạng thái: '
            ],
            OrderStatusUpdater::NOT_ALLOWED_TO_UPDATE => [
                'id' => [],
                'msg' => 'Bạn không có quyền hoặc đơn hàng không được phép cập nhật trạng thái: '
            ]
        ];
        $success = [];
        foreach ($ordersErrors as $ID => $errors) {
            if(!$errors){
                $success[] = $ID;
                continue;
            }

            foreach ($errors as $errorCode) {
                $messages[$errorCode]['id'][] = $ID;
            }
        }
        $messages = array_map(function($message){
            return !$message['id'] ? '' : sprintf('%s%s', $message['msg'], implode(', ', $message['id']));
        }, $messages);

        $messages = array_filter($messages, function($m){ return $m;});
        if($success){
            $messages[] = 'Chuyển trạng thái thành công các đơn hàng ( '.sizeof($success).' đơn) : '.implode(', ',$success);
        }
        if($orderCityFail){
            $orderArrCityFail = explode(',', $orderCityFail);
            $messages[] =  'Các đơn hàng chưa nhập tỉnh thành ( '.sizeof($orderArrCityFail).' đơn ) : '.$orderCityFail;
        }
        if($orderTypeFail){
            $orderArrTypeFail = explode(',', $orderTypeFail);
            $messages[] =  'Các đơn hàng chưa chọn loại đơn ( '.sizeof($orderArrTypeFail).' đơn ) : '.$orderTypeFail;
        }
        if($orderArrLv3Fail){
            $orderStrCityFail = implode(',', $orderArrLv3Fail);
            $messages[] =  'Các đơn hàng phải qua trạng thái Chuyển hàng mới chuyển được lên level cao hơn ( '.sizeof($orderArrLv3Fail).' đơn ) : '.$orderStrCityFail;
        }
        RequestHandler::sendJsonSuccess(['data' => implode("\n", $messages)]);
    }
    function get_select2_product(){
        $name = DB::escape(Url::get('term'));
        $page = Url::iget('page')?Url::iget('page'):1;
        $item_per_page = 20;
        $items = [];
        $items['total_count'] = 0;
        $items['incomplete_results'] = true;
        $items['items'] = [];
        $cond = ' '.($name?'(products.name LIKE "%'.$name.'%" or products.code LIKE "%'.$name.'%")':'1=1').'
                and IFNULL(products.del,0) = 0';
        if(AdminOrders::$master_group_id){
            $cond .= ' and products.group_id='.AdminOrders::$master_group_id;
        }else{
            $cond .= ' and products.group_id='.self::$group_id.'';
        }
        {
            $sql = '
                SELECT 
                products.id,products.name,products.image_url, products.code, products.price, products.standardized
                FROM products
                join `groups` on groups.id=products.group_id
                WHERE 
               '.$cond.'
                ORDER BY
                    products.code
                LIMIT    
                '.((($page-1)*$item_per_page).','.$item_per_page).'
            ';
            if($products = DB::fetch_all($sql)){
                $items['incomplete_results'] = false;
                foreach($products as $key=>$val){
                    $val['text'] = (($val['code'] and $val['name']!=$val['code'])?'[Mã: '.$val['code'].'] ':'').''.$val['name'].' | '.System::display_number($val['price']).'đ';
                    if ($val['standardized']) {
                        $val['text'] .= ' <div style="display: inline-block;color: red;font-weight: bold;font-style: italic;font-size: 12px;">SP CHUẨN HÓA</div>';
                    }
                    $items['items'][] = ['id'=>$key,
                        'name'=>$val['name'],
                        'image_url'=>$val['image_url'],
                        'text'=>$val['text'],
                        'standardized'=>intval($val['standardized'])
                    ];
                }
            }
            $items['total_count'] = DB::fetch('select count(*) as total from products where '.$cond,'total');
        }
        echo json_encode($items);
        exit();
    }
    function get_suggest_product(){
        $name = DB::escape(Url::get('term'));
        $items = array();
        if(strlen($name) > 1){
            $sql = '
                SELECT 
                products.id,products.name,
                CONCAT(products.name,", giá: ",FORMAT(products.price,0)) as label
                FROM products 
                WHERE 
                (products.name LIKE "%'.$name.'%" or products.code LIKE "%'.$name.'%") 
                and products.group_id='.self::$group_id.'
            ';
                if($items = DB::fetch_all($sql)){
                    echo json_encode($items);
                }
        }else{
            echo json_encode($items);
        }
        exit();
    }
    function get_product(){
        if(Url::iget('product_id') and $product=DB::select('products','id='.Url::iget('product_id'))){
            $warehouse_id = Url::iget('warehouse_id')?Url::iget('warehouse_id'):false;
            $product['on_hand'] = get_product_remain($product['id'],$warehouse_id);
            echo json_encode($product);
            exit();
        }else{
            echo 'FALSE';
        }
    }
    function export_product_excel(){// phuc vu cho ben ke toan
        $group_id = self::$group_id;
        $cond = '1=1 '.AdminOrdersDB::get_condition();
        $colomns = array();
        $value_colomns = array();
        /////////////////////
        $c_temp = get_order_by_product_columns();
        /////////////////////
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
            37=>'AK',
            38=>'AL'
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
        $items = AdminOrdersDB::get_product_by_orders($cond,'orders.id DESC',2000);
        //System::debug($items);
        //die;
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

        $subfix = 'donhang_theo_sp_'.$group_id.'_'.self::$account_id;
        $file = $subfix.'.xls';
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
    function export_excel($ship=false){
        $group_id = self::$group_id;
        $cond = '1=1 '.AdminOrdersDB::get_condition();
        /*$colomns = array(
                'STT','Mã','Mã vận đơn','Tên khách hàng','Số điện thoại','Địa chỉ','Mã SP','Sản  phẩm','Ghi chú','Mã bưu điện','Ghi Chú giao hàng','Hình Thức Giao','Trạng Thái','Tổng Tiền','Nhân Viên XN','Ngày Xác Nhận','Ngày Tạo'
        );*/
        $colomns = array();
        $value_colomns = array();
        /////////////////////
        if($ship){
            $c_temp = get_ship_order_columns(); // lay cot don hang cua nguoi dung
        }else{
            $c_temp = get_order_columns(); // lay cot don hang cua nguoi dung
        }
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
            if ($value['id'] === 'total_price') {
                $colomns[] = 'Phí vận chuyển';
                $value_colomns[] = 'shipping_price';
                $letter_arr[$i] = $temp_letter_arr[$i];
                $i++;
                $colomns[] = 'Phụ thu';
                $value_colomns[] = 'other_price';
                $letter_arr[$i] = $temp_letter_arr[$i];
                $i++;
                $colomns[] = 'Giảm giá';
                $value_colomns[] = 'discount_price';
                $letter_arr[$i] = $temp_letter_arr[$i];
                $i++;
            }
            if ($value['id'] === 'products') {
                $colomns[] = 'Mã SP';
                $value_colomns[] = 'product_code';
                $letter_arr[$i] = $temp_letter_arr[$i];
                $i++;
            }
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
        $items = AdminOrdersDB::get_items($cond,'orders.id DESC',2000);
        $requet = $_REQUEST;
        if((is_array($items) and sizeof($items)>0)){
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

            if($ship){
                $subfix = 'ship_donhang_'.$group_id.'_'.self::$account_id;
            }else{
                $subfix = 'donhang_'.$group_id.'_'.self::$account_id;
            }
            $file = ''.$subfix.'.xls';
            $desc = 'Xuất '.sizeof($items).' đơn hàng ra file excel: '.$file;
            if(isset($requet['ngay_tao_from'])){
                $desc .= '<br>Ngày tạo từ: '.$requet['ngay_tao_from'];
            }
            if(isset($requet['ngay_tao_to'])){
                $desc .= '<br>Ngày tạo đến: '.$requet['ngay_tao_to'];
            }
            if(isset($requet['ngay_xn_from'])){
                $desc .= '<br>Ngày chốt từ: '.$requet['ngay_xn_from'];
            }
            if(isset($requet['ngay_xn_to'])){
                $desc .= '<br>Ngày chốt đến: '.$requet['ngay_xn_to'];
            }
            if(isset($requet['ngay_chuyen_from'])){
                $desc .= '<br>Ngày chuyển hàng từ: '.$requet['ngay_chuyen_from'];
            }
            if(isset($requet['ngay_chuyen_to'])){
                $desc .= '<br>Ngày chuyển hàng đến: '.$requet['ngay_chuyen_to'];
            }
            if(isset($requet['ngay_thanh_cong_from'])){
                $desc .= '<br>Ngày thành công từ: '.$requet['ngay_thanh_cong_from'];
            }
            if(isset($requet['ngay_thanh_cong_to'])){
                $desc .= '<br>Ngày thành công đến: '.$requet['ngay_thanh_cong_to'];
            }
            if(isset($requet['status']) and $requet['status']){
                $statuses_str = '';
                $statuses = DB::fetch_all('select id,name from statuses where id in ('.$requet['status'].')');
                $statuses = MiString::get_list($statuses);
                $statuses_str = implode(', ',$statuses);
                $desc .= '<br>Trang thái: '.$statuses_str;
            }
            $desc .= '<br><div class="small text-gray">Thiết bị: '.$_SERVER['HTTP_USER_AGENT'].'</div>';
            System::log('EXPORT_EXCEL','Xuất excel từ danh sách đơn hàng',$desc);
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
            $_SESSION['just_exported'] = time();
        }else{
            die('Không có data nào được xuất!');
        }
        return;
    }

    function exportExcelOrSendMail($type, $sendMailToCarrier = false, $ship = false)
    {
        $cond = '1=1 ' . AdminOrdersDB::get_condition();
        $group_id = self::$group_id;
        if ($type == 'order') {
            $items = AdminOrdersDB::get_items($cond, 'orders.id DESC', 2000, false);
            $keyOrderId = 'id';
            if (!$ship) {
                $subfix = 'donhang_' . $group_id . '_' . self::$account_id;
                $logTitle = 'Xuất excel từ danh sách đơn hàng';
            } else {
                $subfix = 'ship_donhang_' . $group_id . '_' . self::$account_id;
                $logTitle = 'Xuất excel vận chuyển';
            }
        } else {
            $items = AdminOrdersDB::get_product_by_orders($cond, 'orders.id DESC', 2000, false);
            $keyOrderId = 'order_id';
            $subfix = 'donhang_theo_sp_' . $group_id . '_' . self::$account_id;
            $logTitle = 'Xuất excel từ danh sách đơn hàng theo sản phẩm';
        }
        $itemsForDownload = array();
        $arrOrderId = array();
        if (count($items) > 0) {
            $censoredPhoneNumber = 0;
            $getFullPhoneNumber = $_REQUEST['getFullPhoneNumber'];
            $isOwner = AdminOrders::$is_owner;
            $checkCensoredPhoneNumber = true;
            $length = AdminOrders::$hide_phone_number;
            if ($isOwner || $sendMailToCarrier) {
                if ($getFullPhoneNumber) {
                    $checkCensoredPhoneNumber = false;
                } else {
                    $checkCensoredPhoneNumber = true;
                }
            } else {
                if ($getFullPhoneNumber and self::$show_phone_number_excel_order == 1) {
                    $checkCensoredPhoneNumber = false;
                } else {
                    $checkCensoredPhoneNumber = true;
                }
            }
            $itemsForDownload = $items;
            $censoredPhoneNumber = $checkCensoredPhoneNumber;
            foreach ($items as $keyitems => $rowitems) {
                $itemsForDownload[$keyitems]['mobile'] = $rowitems['mobile1'];

                if ($checkCensoredPhoneNumber) {
                    $mobile1 = ModifyPhoneNumber::hidePhoneNumber($rowitems['mobile1'], $length);
                    $mobile2 = ModifyPhoneNumber::hidePhoneNumber($rowitems['mobile2'], $length);
                    $itemsForDownload[$keyitems]['mobile'] = $mobile1;
                    $itemsForDownload[$keyitems]['mobile2'] = $mobile2;
                    $itemsForDownload[$keyitems]['mobile_both'] = $mobile1 . ($mobile2 ? ' / ' . $mobile2 : '');
                }
            }

            foreach ($itemsForDownload as $rowItems) {
                $arrOrderId[] = $rowItems[$keyOrderId];
            }
            $arrOrderId = array_unique($arrOrderId);
        }
        $userInfo = Session::get('user_data');
        $requet = $_REQUEST;
        if ($sendMailToCarrier) {
            $arrReturn = array();
            if (count($itemsForDownload) > 0) {
                $data_request = array('shop_id' => self::$group_id);
                $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/shop-setting/get', $data_request);
                $sendToCarrier = Url::get('sendToCarrier');
                if (count($dataRes) > 0 && isset($dataRes['data']['settings']['settings'][$sendToCarrier])) {
                    $infoCarrierSetting = $dataRes['data']['settings']['settings'][$sendToCarrier];
                    $infoCarrierBrand = array();
                    foreach ($dataRes['data']['brand'] as $keyBrand => $rowBrand) {
                        if ($rowBrand['alias'] == $infoCarrierSetting['carrier_id']) {
                            $infoCarrierBrand = $rowBrand;
                            break;
                        }
                    }
                    $carrierEmail = $infoCarrierSetting['carrier_email'];
                    $carrierName = $infoCarrierBrand['name'];
                    $shopName = $infoCarrierSetting['shop_name'];

                    $groupInfo = self::$group;
                    if (!empty($groupInfo['email'])) {
                        if (!empty($shopName) && !empty($carrierEmail)) {
                            require_once 'packages/core/includes/utils/mailer/ConfigMailer.php';
                            $configMailer = new ConfigMailer();
                            $mail = new PHPMailer(true);
                            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
                            $mail->isSMTP();
                            $mail->Host = $configMailer::$host;
                            $mail->SMTPAuth = $configMailer::$smtpAuth;
                            $mail->Username = $configMailer::$username;
                            $mail->Password = $configMailer::$password;
                            $mail->SMTPSecure = $configMailer::$smtpsSecure;
                            $mail->Port = $configMailer::$port;
                            $mail->CharSet = $configMailer::$charset;
                            $mail->isHTML(true);
                            $statuses_str = '';
                            if (isset($requet['status']) and $requet['status']) {
                                $status = parse_id($requet['status']);
                                $statuses = DB::fetch_all('select id,name from statuses where id in (' . implode(',', $status) . ')');
                                $statuses = MiString::get_list($statuses);
                                $statuses_str = implode(', ', $statuses);
                            }

                            $fileName = $carrierName . ' - ' . $shopName . ' ngày ' . date('d-m-Y');
                            $mail->setfrom('noreply@delivery.tuha.vn', $shopName);
                            $mail->addaddress($carrierEmail, $carrierName);     // Add a recipient
                            $mail->Subject = $shopName . ' - Thông tin đơn hàng ngày ' . date('d-m-Y');
                            $mailBody = 'Kính gửi đơn vị vận chuyển ' . $carrierName;
                            $mailBody .= '<br>';
                            $mailBody .= 'Tôi là ' . $userInfo['full_name'] . ' – Nhân viên Shop/Công ty ' . $shopName . '.';
                            $mailBody .= '<br>';
                            $mailBody .= 'Tôi thay mặt Công ty/Shop gửi thông tin đơn hàng phát sinh trong ngày ' . date('d-m-Y') . ' tới ĐVVC ' . $carrierName
                                . ' với thông tin chi tiết trong tệp tin đính kèm (' . $fileName . ') để đơn hàng được chuyển đi thuận lợi. ';
                            $mailBody .= '<br>';
                            $mailBody .= 'Nếu anh/chị có thắc mắc hay cần chỉnh sửa nội dung thì phản hồi lại cho tôi qua email: (' . $groupInfo['email'] . ') . Cảm ơn anh/chị và chúc anh/chị có một ngày làm việc hiệu quả!';
                            $mailBody .= '<br>';
                            $mailBody .= 'Cảm ơn sự đồng hành và hỗ trợ của đơn vị vận chuyển ' . $carrierName.'.';
                            $mailBody .= '<br><br>';
                            $mailBody .= 'Trân trọng!';
                            $mail->Body = $mailBody;

                            $itemsCarrier = $itemsForDownload;
                            $arrOrderIdCarrier = $arrOrderId;

                            $resultUpload = AdminOrders::exportExcelFile($type, $itemsCarrier, $fileName, true, $censoredPhoneNumber, $keyOrderId, true);
                            $mail->addAttachment($resultUpload['fullPath']);
                            $mail->send();

                            $desc = 'Gửi email cho nhà vận chuyển ' . sizeof($itemsCarrier) . ' đơn hàng';
                            if ($statuses_str) {
                                $desc .= '<br>Trang thái: ' . $statuses_str;
                            }

                            // Log xuất execl full name nếu có
                            if(AdminOrders::$show_full_name_export_excel_order){
                                $desc .= "<br><b>Xuất excel với tên sản phẩm đầy đủ</b>";
                            }

                            $arrPatchData = array(
                                'list_export_order_id' => implode(', ', array_unique($arrOrderIdCarrier)),
                                'carrier' => $infoCarrierBrand['alias'],
                                'carrier_email' => $carrierEmail,
                                'censored_phone_number' => $censoredPhoneNumber? 1: 0
                            );
                            System::log('SEND_EMAIL_TO_CARRIER', 'Gửi email cho nhà vận chuyển', $desc, '', '', false, $arrPatchData);
                            //xoa file tam
                            unlink($resultUpload['fullPath']);
                            $arrReturn = array(
                                'success' => 1,
                                'message' => 'Đã gửi mail cho đơn vị vận chuyển!',
                            );
                        } else {
                            $arrReturn = array(
                                'success' => 0,
                                'message' => 'Vui lòng kiểm tra lại email đơn vị vận chuyển hoặc tên shop đăng ký với đơn vị vận chuyển!',
                            );
                        }
                    } else {
                        $arrReturn = array(
                            'success' => 0,
                            'message' => 'Vui lòng kiểm tra lại email của shop!',
                        );
                    }
                } else {
                    $arrReturn = array(
                        'success' => 0,
                        'message' => 'Không tìm thấy thông tin đơn vị vận chuyển!',
                    );
                }
            } else {
                $arrReturn = array(
                    'success' => 0,
                    'message' => 'Không tìm thấy thông tin đơn hàng!',
                );
            }
            echo json_encode($arrReturn);
            exit();
        } else {
            if (count($itemsForDownload) > 0) {
                //gui thong bao cho shop owner
                if (!self::$is_owner) {
                    //insert bang notification
                    $notification_id = DB::insert('notifications', [
                        'title' => 'Thông báo xuất excel đơn hàng',
                        'content' => 'Nhân viên ' . $userInfo['full_name'] . ' (' . $userInfo['id'] . ') đã xuất excel ' . count($arrOrderId) . ' đơn hàng, thời gian: ' . date('H:i d/m/Y'),
                        'type' => 1,
                        'is_public' => 2,
                        'notificationable_type' => 0,
                    ]);
                    $ownerInfo = DB::fetch($sql = 'select users.id, account.group_id from account inner join users on account.id = users.username where account.id = "' . self::$group['code'] . '"');
                    //insert bang notification_received
                    DB::insert('notifications_recieved', [
                        'notification_id' => $notification_id,
                        'user_id' => $ownerInfo['id'],
                        'group_id' => $ownerInfo['group_id'],
                        'is_excel_notification' => 1,
                        'is_read' => 0,
                    ]);
                }

                //log lich su xuat excel
                $desc = 'Xuất ' . sizeof($arrOrderId) . ' đơn hàng ra file excel: ' . $subfix . '.xls';
                if (isset($requet['ngay_tao_from'])) {
                    $desc .= '<br>Ngày tạo từ: ' . $requet['ngay_tao_from'];
                }
                if (isset($requet['ngay_tao_to'])) {
                    $desc .= '<br>Ngày tạo đến: ' . $requet['ngay_tao_to'];
                }
                if (isset($requet['ngay_xn_from'])) {
                    $desc .= '<br>Ngày chốt từ: ' . $requet['ngay_xn_from'];
                }
                if (isset($requet['ngay_xn_to'])) {
                    $desc .= '<br>Ngày chốt đến: ' . $requet['ngay_xn_to'];
                }
                if (isset($requet['ngay_chuyen_from'])) {
                    $desc .= '<br>Ngày chuyển hàng từ: ' . $requet['ngay_chuyen_from'];
                }
                if (isset($requet['ngay_chuyen_to'])) {
                    $desc .= '<br>Ngày chuyển hàng đến: ' . $requet['ngay_chuyen_to'];
                }
                if (isset($requet['ngay_thanh_cong_from'])) {
                    $desc .= '<br>Ngày thành công từ: ' . $requet['ngay_thanh_cong_from'];
                }
                if (isset($requet['ngay_thanh_cong_to'])) {
                    $desc .= '<br>Ngày thành công đến: ' . $requet['ngay_thanh_cong_to'];
                }
                if (isset($requet['status']) and $requet['status']) {
                    $statuses_str = '';
                    $status = parse_id($requet['status']);
                    $statuses = DB::fetch_all('select id,name from statuses where id in (' . implode(',', $status) . ')');
                    $statuses = MiString::get_list($statuses);
                    $statuses_str = implode(', ', $statuses);
                    $desc .= '<br>Trang thái: ' . $statuses_str;
                }
                $desc .= '<br><div class="small text-gray">Thiết bị: ' . $_SERVER['HTTP_USER_AGENT'] . '</div>';

                // Log xuất execl full name nếu có
                if(AdminOrders::$show_full_name_export_excel_order){
                    $desc .= "<br><b>Xuất excel với tên sản phẩm đầy đủ</b>";
                }

                $arrPatchData = array(
                    'censored_phone_number' => $censoredPhoneNumber? 1: 0,
                    'list_export_order_id' => implode(', ', $arrOrderId)
                );
                System::log('EXPORT_EXCEL', $logTitle, $desc, '', '', false, $arrPatchData);

                //download file
                AdminOrders::exportExcelFile($type, $itemsForDownload, $subfix, false, $censoredPhoneNumber, $keyOrderId, $ship);
                exit;
            } else {
                die('Không có đơn hàng nào được xuất!');
            }
        }

    }

    static function exportExcelFile($type, $items, $subfix, $toCarrier = false, $censoredPhoneNumber = true, $keyOrderId='',$ship=false)
    {
        require_once ROOT_PATH . 'packages/user/modules/AdminUserInfo/forms/manager_columns_export_excel.php';
        $temp_letter_arr = array(
            1 => 'A',
            2 => 'B',
            3 => 'C',
            4 => 'D',
            5 => 'E',
            6 => 'F',
            7 => 'G',
            8 => 'H',
            9 => 'I',
            10 => 'J',
            11 => 'K',
            12 => 'L',
            13 => 'M',
            14 => 'N',
            15 => 'O',
            16 => 'P',
            17 => 'Q',
            18 => 'R',
            19 => 'S',
            20 => 'T',
            21 => 'U',
            22 => 'V',
            23 => 'W',
            24 => 'X',
            25 => 'Y',
            26 => 'Z',
            27 => 'AA',
            28 => 'AB',
            29 => 'AC',
            30 => 'AD',
            31 => 'AE',
            32 => 'AF',
            33 => 'AG',
            34 => 'AH',
            35 => 'AI',
            36 => 'AJ',
            37 => 'AK',
            38 => 'AL',
            39 => 'AM',
            40 => 'AN',
            41 => 'AO',
            42 => 'AP',
            43 => 'AQ',
            44 => 'AR',
            45 => 'AS',
            46 => 'AT',
            44 => 'AU',
            47 => 'AV',
            48 => 'AW',
            49 => 'AX',
            50 => 'AY',
            51 => 'AZ',
            52 => 'BA',
            53 => 'BB',
            54 => 'BC',
            55 => 'BD',
            56 => 'BE',
            57 => 'BF',
            58 => 'BG',
            59 => 'BH',
            60 => 'BI',
            61 => 'BJ',
            62 => 'BK',
            63 => 'BL',
            64 => 'BM',
            65 => 'BN',
        );
        $i = 1;
        $letter_arr = array();
        $arrColNumber = array();
        $colomns = array();
        $value_colomns = array();
        if ($type == 'order') {
            $c_temp = ManagerColumnsExportExcel::getExportColumns(self::$group_id);
            if($ship) {
                $c_temp = get_ship_order_columns();
            }
            foreach ($c_temp as $key => $value) {
                if ($value['id'] === 'total_price') {
                    // $colomns[] = 'Phí vận chuyển';
                    // $value_colomns[] = 'shipping_price';
                    // $letter_arr[$i] = $temp_letter_arr[$i];
                    // $arrColNumber[] = $i;
                    // $i++;
                    // $colomns[] = 'Phụ thu';
                    // $value_colomns[] = 'other_price';
                    // $letter_arr[$i] = $temp_letter_arr[$i];
                    // $arrColNumber[] = $i;
                    // $i++;
                    $colomns[] = 'Giảm giá DH';
                    $value_colomns[] = 'discount_price';
                    $letter_arr[$i] = $temp_letter_arr[$i];
                    $arrColNumber[] = $i;
                    $i++;
                }
                if ($value['id'] === 'products' && get_group_options('show_product_detail')) {
                    $colomns[] = 'Mã SP';
                    $value_colomns[] = 'product_code';
                    $letter_arr[$i] = $temp_letter_arr[$i];
                    $i++;
                }
                $colomns[] = $value['name'];
                $value_colomns[] = $value['id'];
                $letter_arr[$i] = $temp_letter_arr[$i];
                $i++;

                if ($value['id'] === 'products') {
                    if(AdminOrders::$show_full_name_export_excel_order){
                        $colomns[] = 'Tên đầy đủ';
                        $value_colomns[] = 'products_fullname';
                        $letter_arr[$i] = $temp_letter_arr[$i];
                        $i++;
                    }

                    $colomns[] = 'Trọng lượng';
                    $value_colomns[] = 'product_weight';
                    $letter_arr[$i] = $temp_letter_arr[$i];
                    $i++;

                    $colomns[] = 'Giá SP';
                    $value_colomns[] = 'product_price';
                    $letter_arr[$i] = $temp_letter_arr[$i];
                    $i++;

                    $colomns[] = 'Giảm giá SP';
                    $value_colomns[] = 'discount_amount';
                    $letter_arr[$i] = $temp_letter_arr[$i];
                    $i++;
                }
            }
        } else {
            $c_temp = get_order_by_product_columns();
            // loại bỏ cột full_name trường hợp cài đặt shop không cho phép
            if(!AdminOrders::$show_full_name_export_excel_order){
                $c_temp = array_filter($c_temp, function($item){
                    return $item['id'] != 'full_name';
                });
            }
            $show_product_detail = get_group_options('show_product_detail');
            if (!$show_product_detail) {
                $c_temp = array_filter($c_temp, function($item){
                    return $item['id'] != 'product_code';
                });
            }

            foreach ($c_temp as $key => $value) {
                $colomns[] = $value['name'];
                $value_colomns[] = $value['id'];
                $letter_arr[$i] = $temp_letter_arr[$i];
                $i++;
            }
        }
        $writer = WriterEntityFactory::createXLSXWriter();
        $file = $subfix . '.xlsx';
        $toCarrier ? $writer->openToFile('temp/' . $file) : $writer->openToBrowser($file);

        // Write header
        $style = (new StyleBuilder())
           ->setFontBold()
           ->setShouldWrapText()
           ->setCellAlignment(CellAlignment::LEFT)
           ->build();
        $writer->addRow(WriterEntityFactory::createRowFromArray($colomns, $style));


        $ex_items = [];
        $arrOrderId = array();
        foreach ($items as $key => $value) {
            $j = 1;
            $arrOrderId[] = $value[$keyOrderId];
            foreach ($value_colomns as $k => $v) {
                $ex_items[$key][$j] = '';

                if(isset($value[$v])) {
                    $val = str_replace(array('<br>', "="), array(", ", ""), $value[$v]);
                    if ($v == 'mobile') {
                        $val = ' ' . $val;
                    }
                    $ex_items[$key][$j] = $val;
                }
                $j++;
            }
        }

        foreach ($ex_items as $key => $value) {
            $j = 1;
            $row = [];
            foreach ($colomns as $v) {
                if(isset($value[$j])) {
                    $valueFormat = $value[$j];
                    if (in_array($j, $arrColNumber)) {
                        if (empty($valueFormat)) {
                            $valueFormat = 0;
                        }
                    }

                    $row[] = self::generalValue($valueFormat);
                }

                $j++;
            }

            $writer->addRow(WriterEntityFactory::createRowFromArray($row));
        }


        if ($toCarrier) {
            $writer->close();

            return [
                'status' => true,
                'msg' => 'Xuất Data thành công',
                'fullPath' => 'temp/' . $file
            ];
        }

        if ($type == 'order') {
            $_SESSION['just_exported'] = time();
        }

        $writer->close();
    }

    /**
     * { function_description }
     *
     * @param      <type>  $val    The value
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private static function generalValue($val)
    {
        if(!is_numeric($val)){
            return $val;
        }

        if(filter_var($val, FILTER_VALIDATE_FLOAT) === true) {
            return floatval($val);
        }

        $_val = intval($val);

        return strlen($_val) === strlen($val) ? $_val : $val;
    }

    //hieudev 21/3/2018
    //Edited by khoand 27/11/2018
    function get_zone(){
        $html = '';
        if($zone = DB::fetch('select province_id as id,province_name as name from zone_provinces_v2 where province_id='.Url::iget('province_id').'')){
            $cond = 'province_id='.$zone['id'];
            if(Url::get('key')){
                $cond .=' and district_name LIKE "%'.DB::escape(Url::get('key')).'%"';
            }
            if($districts = DB::fetch_all('select district_id as id,district_name as name from zone_districts_v2 where '.$cond.' order by district_name')){
                foreach($districts as $k => $v){
                    $html.='<li><a data-name="'.$v['name'].'" data-id="'.$v['id'].'">'.$v['name'].'</a></li>';
                }
                if(Url::get('type')=='district'){
                    $html.="
                        <script>
                        $('#district ul li a').click(function(){
                            $('#district .input-name').val($(this).attr('data-name'));
                            $('#district .input-id').val($(this).attr('data-id'));
                            $('#ward .input-id').val();
                            $('#address').val('');
                            let district_id = $(this).attr('data-id');
                            $.ajax({
                                method: 'POST',
                                url: 'form.php?block_id=".Module::block_id()."',
                                data : {
                                    'cmd':'get_zone',
                                    'district_id':district_id,
                                    'type': 'ward'
                                },
                                beforeSend: function(){
                                },
                                success: function(content){
                                   $('#ward ul').html(content);
                                }
                            });
                        });
                        </script>
                    ";
                }
            }
            echo $html;
        }else{
            if(Url::get('type')=='ward' and Url::iget('district_id')){
                $cond = 'district_id='.Url::iget('district_id');
                if(Url::get('key')){
                    $cond .=' and ward_name LIKE "%'.DB::escape(Url::get('key')).'%"';
                }
                $wards = DB::fetch_all('select ward_id as id,ward_name as name from zone_wards_v2 where '.$cond.' order by ward_name');
                foreach($wards as $k => $v){
                    $html.='<li><a data-name="'.$v['name'].'" data-id="'.$v['id'].'">'.$v['name'].'</a></li>';
                }
                $html.='
                    <script>
                    $("#ward ul li a").click(function(){
                        $("#ward .input-name").val($(this).attr("data-name"));
                        $("#ward .input-id").val($(this).attr("data-id"));
                    });
                    </script>
                ';
            }
            echo $html;
        }
    }

    function get_zone_city(){//keyup input (city)
        $cond = '1=1';
        if(Url::get('key')){
            $cond .=' and province_name LIKE "%'.DB::escape(Url::get('key')).'%"';
        }
        $zones = DB::fetch_all('select province_id as id,province_name as name from zone_provinces_v2 where '.$cond);
        $html ='';
        foreach($zones as $k => $v){
            $html.='<li><a data-name="'.$v['name'].'" data-id="'.$v['id'].'">'.$v['name'].'</a></li>';
        }
        $html.="
            <script>
            $('#city-drd ul li a').click(function(){
                $('#city-drd .input-name').val($(this).attr('data-name'));
                $('#city-drd .input-id').val($(this).attr('data-id'));
                $('#country ul').html('');
                $('#district .input-name').attr('placeholder','Quận / Huyện');
                $('#district .input-name').val('');
                $('#district .input-id').val('');
                $('#country .input-name').attr('placeholder','Phường / Xã');
                $('#country .input-name').val('');
                $('#country .input-id').val('');
                $('#city').val($(this).attr('data-name'));
                var province_id = $(this).attr('data-id');
                $.ajax({
                    method: 'POST',
                    url: 'form.php?block_id=".Module::block_id()."',
                    data : {
                        'cmd':'get_zone',
                        'province_id':province_id,
                        'type': 'district'
                    },
                    beforeSend: function(){
                    },
                    success: function(content){
                       $('#district ul').html(content);
                    }
                });
            });
            </script>
        ";
        echo $html;
    }
    //get info for user from phone number
    function suggest_customers(){
        if($keyword = DB::escape(trim(URL::get('term')))){
            $cond = 'crm_customer.group_id="'.self::$group_id.'"';
            $cond .= ' AND (
                crm_customer.name like "%'.$keyword.'%" 
                OR crm_customer.id = '.intval($keyword).'
            )';
            if(strlen($keyword)>=8){
                $cond .= '
                    OR (
                        (crm_customer.phone) LIKE "'.$keyword.'%"
                        OR (crm_customer.mobile) LIKE "'.$keyword.'%"
                    )
                ';
            }
            $sql = '
                SELECT 
                    crm_customer.id,crm_customer.mobile,crm_customer.name as customer_name,
                    crm_customer.address,
                    zone_provinces_v2.province_name as city,
                    crm_customer.zone_id,
                    CONCAT(crm_customer.name," - ĐT: ",crm_customer.mobile,", ĐC: ",crm_customer.address) AS label
                FROM 
                    crm_customer
                    LEFT JOIN zone_provinces_v2 ON zone_provinces_v2.province_id = crm_customer.zone_id
                WHERE 
                    '.$cond.'
                ORDER BY
                    crm_customer.id DESC
            ';
            if($items = DB::fetch_all($sql)){
                //$items = MiString::get_list($items,'name');
                echo json_encode($items);
                exit();
            }
        }
    }
    function get_phone_number(){
        $cond = 'orders.group_id='.self::$group_id.'';
        $show_full_name = get_group_options('show_full_name');
        if($keyword = DB::escape(Url::get('phone')) and strlen($keyword) > 1){
            if(strpos($keyword,'0')===0){
                $keyword = substr($keyword,1,strlen($keyword)-1);
            }
            if(strlen($keyword)>=8){
                $cond .= ' AND (
                    orders.mobile like "'.$keyword.'%"
                    OR orders.mobile like "0'.$keyword.'%"
                )';
            }else{
                $cond .= ' and 1>1';
            }
            if($id = Url::iget('order_id')){
                $cond .= ' AND orders.id <> '.$id;
            }
            $provinces = AdminOrdersDB::get_provinces();
            $districts = AdminOrdersDB::get_districts();
            $wards = AdminOrdersDB::get_wards();
            $sql = '
            SELECT 
                orders.id,
                orders.mobile,
                orders.mobile2,
                orders.created,
                orders.customer_name,
                orders.code,
                orders.city_id,
                orders.district_id,
                orders.ward_id,
                orders.address,
                orders.city,
                orders.bundle_id,
                orders.note1,
                orders.note2,
                (select name from bundles where id = orders.bundle_id) as bundle_name,
                (select name from statuses where id = orders.status_id) as status_name,
                (SELECT '.(($show_full_name==1)?'users.name':'users.username').' FROM users WHERE users.id=orders.user_created limit 0,1) AS user_created,
                CONCAT(orders.mobile," - ĐT: ",orders.customer_name," - ĐC: ",orders.address,", ",orders.city ) AS label
            FROM 
                orders
            WHERE '.$cond.'
            ';
            if($items = DB::fetch_all($sql)){
                System::sksort($items,'id','asc');
                $html='';
                $i=1;
                foreach ($items as $key => $value) {
                    $items[$key]['province_name'] = $value['province_name'] = ($value['district_id'] and isset($provinces[$value['city_id']]))?$provinces[$value['city_id']]:'';
                    $items[$key]['district_name'] = $value['district_name'] = ($value['district_id'] and isset($districts[$value['district_id']]))?$districts[$value['district_id']]:'';
                    $items[$key]['ward_name'] = $value['ward_name'] = ($value['ward_id'] and isset($wards[$value['ward_id']]))?$wards[$value['ward_id']]:'';
                    $html.='<tr>';
                    $html.='
                        <td>'.$i.'</td>
                        <td>'.$value['id'].'</td>
                        <td>'.$value['user_created'].'<br><span class="small"><i class="fa fa-clock-o"></i> '.date('H:i\' d/m',strtotime($value['created'])).'</span></td>
                        <td>'.$value['customer_name'].'</td>
                        <td><span class="text-info text-bold">'.$value['mobile'].'</span><br><span class="label label-default">'.$value['status_name'].'</span></td>
                        <td><span class="text-info text-bold">'.$value['mobile2'].'</span></td>
                        <td>'.$value['bundle_name'].'</td>
                        <td style="max-width: 250px;">'.$value['address'].'</td>
                        <td>'.$value['city'].'</td>
                        <td>'.$value['note1'].'</td>
                        <td style="word-break: break-word">'.$value['note2'].'</td>
                        <td>
                            <button type="button" class="btn btn-success btn-sm c-number"
                                data-bundle_id="'.$value['bundle_id'].'"
                                data-customer_id="'.$value['id'].'"
                                data-customer_name="'.$value['customer_name'].'"
                                data-address="'.$value['address'].'"
                                data-city="'.$value['city'].'"
                                data-mobile2="'.$value['mobile2'].'"
                                data-city_id="'.$value['city_id'].'"
                                data-district_id="'.$value['district_id'].'"
                                data-ward_id="'.$value['ward_id'].'"
                                data-province-name="'. $value['province_name'] .'"
                                data-district-name="'. $value['district_name'] .'"
                                data-ward-name="'. $value['ward_name'] .'"
                                data-note1=\''. $value['note1'] .'\'
                                data-note2=\''. $value['note2'] .'\'
                            > Chọn</button>
                        </td>
                    ';
                    $html.='</tr>';
                    $i++;
                }
                $html.="
                    <script>
                    $('.c-number').click(function(){
                        let note2 = $(this).data('note2');
                        $('#customer_name').val($(this).attr('data-customer_name'));
                        $('#customer_id').val($(this).attr('data-customer_id'));
                        $('#bundle_id').val($(this).attr('data-bundle_id'));
                        $('#address').val($(this).attr('data-address'));
                        $('#city').val($(this).attr('data-city'));
                        $('#city-drd .input-id').val($(this).attr('data-city_id'));
                        $('#city-drd .input-name').val($(this).data('province-name'));
                        $('#district .input-id').val($(this).attr('data-district_id'));
                        $('#district .input-name').val($(this).data('district-name'));
                        $('#ward .input-id').val($(this).attr('data-ward_id'));
                        $('#ward .input-name').val($(this).data('ward-name'));
                        $('#show-phone-number').removeClass('open');
                        $('#md-show-phone-number').modal('hide');
                        $('#note1').val($(this).data('note1'));
                        $('#mobile2').val($(this).data('mobile2'));
                        if(typeof note2 == 'object'){
                            $('#note2').val(JSON.stringify(note2));
                            $('#note2').prop('readonly', true);
                        }else{
                            $('#note2').val(note2);
                        }
                    });
                    </script>
                ";
                echo $html;
                //echo json_encode($items);
                exit();
            }
            else{
                echo 0;
                exit();
            }
        }
        else{
            echo 0;
            exit();
        }

    }
    function check_duplicated(){
        $cond = 'orders.group_id='.self::$group_id.'';
        if($keyword = DB::escape(Url::get('phone')) and strlen($keyword) > 1){
            if(strpos($keyword,'0')===0){
                $keyword = substr($keyword,1,strlen($keyword)-1);
            }
            if(strlen($keyword)>=8){
                $cond .= ' AND (
                    orders.mobile like "'.$keyword.'%"
                    OR orders.mobile like "0'.$keyword.'%"
                )';
            }else{
                $cond .= ' and 1>1';
            }
            if($id = Url::iget('order_id')){
                $cond .= ' AND orders.id <> '.$id;
            }
            $sql = '
                SELECT 
                    orders.id
                FROM 
                    orders
                WHERE '.$cond.'
            ';
            if(DB::exists($sql)){
                echo json_encode(['result'=>1]);
            }
            else{
                echo json_encode(['result'=>2]);
            }
        }
        else{
            echo json_encode(['result'=>3]);
        }
        exit();
    }
    function get_order_history(){
        $page = Url::iget('page');
        $html='';
        $order_id = Url::iget('order_id');
        $status_id = DB::fetch("select status_id from orders where id=".$order_id . ' AND group_id = ' . AdminOrders::$group_id,'status_id');
        if(!$status_id){
            die();
        }

        if(!AdminOrders::$admin_group and !AdminOrders::$quyen_admin_ke_toan and !AdminOrdersDB::can_edit_by_status($status_id) && !self::$has_show_history_order){
            echo $html = 'Đơn hàng mã #'.$order_id.' bạn không có quyền xem lịch sử.';
            die;
        }
        $items = AdminOrdersDB::get_order_revisions($page,$order_id);
        if(sizeof($items)==0){
            echo 0;
            exit();
        }
        foreach ($items as $key => $value) {
            $html.='
                <div class="timeline__group">
                    <span class="timeline__year" style="color:#fff;">'.date('d/m/y',$value['id']).'</span>
                    <div class="timeline__box">
                        <div class="timeline__date">
                            <span class="timeline__month">
                                <i class="fa fa-folder-open"></i>                                                        
                            </span>
                        </div>';
                        foreach ($value['arr'] as $k => $v) {
                            $html.='
                                <div class="timeline__post">
                                    <div class="timeline__content">
                                        <div class="box box-default">
                                            <div class="box-header">
                                                <div class="box-title">'.$v['user_created_name'].'</div>
                                                <div class="box-tools pull-right">
                                                    <i class="fa fa-clock-o"></i>'.date('H:i:s',strtotime($v['created'])).'
                                                </div> 
                                            </div>
                                            <div class="box-body">
                                                <div class="text-warning">'.$v['data'].'</div>';
                                                if($v['before_order_status'] && $v['before_order_status']!=''){
                                                    $html.='<div> Chuyển trạng thái từ: <strong>'.$v['before_order_status'].'</strong> thành <strong>'.$v['order_status'].'</strong> </div>';
                                                }
                                            $html.='
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ';
                        }

            $html.='
                    </div>
                </div>
            ';
        }
        echo $html;
        exit();
    }
    function get_vichat_history1($fb_conversation_id,$page_id){
        //$fb_post_id = '409705996180249_422273808256801';
        //$page_id = '360102997807216';
        //echo $fb_conversation_id;
        //echo '<br>';
        //echo $page_id;
        $url  = 'https://api-vichat.tuha.vn/api/conversations/get/public?id='.$fb_conversation_id.'&page_id='.$page_id.'&skip=0&limit=10';
        $content = file_get_contents($url);
        $json = json_decode($content, true);
        $str = '<h4><a href="https://www.facebook.com/'.$fb_conversation_id.'" target="_blank">Vào comment</a></h4><hr>';
        $str .= '<ul class="quick-chat-history">';
        $left = '';
        $i = 1;

        foreach($json as $key=>$val){
            if($i==1){
                $left = $val['from']['id'];
                $class = 'left clearfix';
                $pull = 'pull-left';
            }
            if($left == $val['from']['id']){
                $class = 'left clearfix';
                $pull = 'pull-left';
            }else{
                $left = $val['from']['id'];
                $class = 'right clearfix';
                $pull = 'pull-right';
            }
            $i++;
            $str .= '
                <li class="'.$class.'">
                    <span class="chat-img '.$pull.'">
                    <img src="assets/standard/images/avatar3.png" alt="User Avatar" class="img-circle" /></span>
                    <div class="chat-body clearfix">
              <div class="header">
                ';
                if($pull == 'pull-left'){
                    $str .= '
                            <strong class="primary-font">'.$val['from']['name'].'</strong> 
                            <small class="pull-right text-muted">
                                <span class="glyphicon glyphicon-time"></span>'.date('H:i\' d/m',($val['created_time'])).'
                            </small>';
                }else{
                    $str .= '
                            <small class="text-muted">
                                <span class="glyphicon glyphicon-time"></span>'.date('H:i\' d/m',($val['created_time'])).'
                            </small>
                            <strong class="pull-right primary-font">'.$val['from']['name'].'</strong> ';
                }
      $str .='        </div>
              <p class="'.(($pull == 'pull-left')?'text-left':'text-right').'">
                  '.$val['message'].'
              </p>
          </div>
                </li>';
        }
        $str .= '</ul>';
        echo $str;
        exit();
    }
    function get_vichat_history($fb_conversation_id,$page_id){
        // truong hop inbox
        $url  = 'https://api-vichat.tuha.vn/api/conversations/get/public?id='.$fb_conversation_id.'&page_id='.$page_id.'&skip=0&limit=50';
        $content = file_get_contents($url);
        $json = json_decode($content, true);
        $str = '<h4><a href="https://www.facebook.com/'.$page_id.'" target="_blank">Vào Fanpage</a></h4><hr>';
        $str .= '<ul class="quick-chat-history">';
        $i = 1;
        if(isset($json['data'])){
            System::sksort($json['data'],'created_time','ASC');
            foreach($json['data'] as $key=>$val){
                if((isset($val['from']['id']) and $val['from']['id'] == $page_id) or (isset($val['to']['id']) and $val['to']['id'] == $page_id)){
                    $class = 'right clearfix';
                    $pull = 'pull-right';
                }else{
                    $class = 'left clearfix';
                    $pull = 'pull-left';
                }
                $i++;
                $str .= '
                    <li class="'.$class.'">
                        <span class="chat-img '.$pull.'">
                        <img src="assets/standard/images/avatar3.png" alt="User Avatar" class="img-circle" /></span>
                        <div class="chat-body clearfix">
                  <div class="header">
                    ';
                    if($pull == 'pull-left'){
                        $str .= '
                                <strong class="primary-font">'.$val['from']['name'].'</strong> 
                                <small class="pull-right text-muted">
                                    <span class="glyphicon glyphicon-time"></span>'.date('H:i\' d/m',strtotime($val['created_time'])).'
                                </small>';
                    }else{
                        $str .= '
                                <small class="text-muted">
                                    <span class="glyphicon glyphicon-time"></span>'.date('H:i\' d/m',strtotime($val['created_time'])).'
                                </small>
                                <strong class="pull-right primary-font">'.$val['from']['name'].'</strong> ';
                    }
          $str .='        </div>
                 <p '.(($pull == 'pull-left')?'class="text-left" style="color:#666;background:#f1f0f0;padding: 6px 12px;border-radius:15px;margin-right:40%;"':'class="text-right" style="color:#fff;background:#0084ff;padding: 6px 12px;border-radius:15px;margin-left:40%;"').'>
                      '.$val['message'].'
                  </p>
              </div>
                    </li>';
            }
        }
        $str .= '</ul>';
        echo $str;
        exit();
    }
    function change_order_column_position(){
        $column_order = Url::get('column');
        $columns_data = explode(',',Url::get('columns'));
        $show_columns = '';
        //System::debug($column_order);
        //System::debug($columns_data);
        foreach($column_order as $key=>$position){
            $position = $position-1;
            if(isset($columns_data[$position]) and $columns_data[$position]){
                $show_columns .= ($show_columns?',':'').$columns_data[$position].':'.$position;
            }
        }
        if($show_columns){
            if(DB::exists('select id from orders_column_custom where group_id='.self::$group_id)){
                DB::update('orders_column_custom',array('show_columns'=>DB::escape(DataFilter::removeXSSinHtml($show_columns)),'last_edited_time'=>time(),'last_edited_account_id'=>self::$account_id),'group_id='.self::$group_id);
            }else{
                DB::insert('orders_column_custom',array('show_columns'=>DB::escape(DataFilter::removeXSSinHtml($show_columns)),'last_edited_time'=>time()	,'last_edited_account_id'=>self::$account_id,'group_id'=>self::$group_id));
            }
            echo 'DONE';
        }else{
            echo 'ERROR';
        }
    }
    function care_cmd(){
        Portal::$document_title = 'QLBH: danh sách đơn hàng đánh giá chất lượng sale / cskh';
        require_once 'forms/care_list.php';
        $this->add_form(new CareListForm());
    }
    function care_detail_cmd(){
        $group_id = AdminOrders::$group_id;
        if($id=Url::iget('order_id')){
            $cond = ' orders.group_id='.$group_id.'';
            $cond .= ' and orders.id='.$id.'';
            $sql = '
                SELECT
                    orders.*,
                    users.name as staff_name,
                    party.image_url as staff_avatar,
                    orders_extra.insurance_value,
                    orders_extra.update_successed_time,
                    orders_extra.update_successed_time
                FROM
                    orders
                    LEFT JOIN orders_extra ON orders_extra.order_id=orders.id
                    JOIN users on users.id = orders.user_confirmed
                    JOIN party on party.user_id = users.username
                    JOIN `groups` ON groups.id = orders.group_id
                WHERE
                    '.$cond.'
            ';
            AdminOrders::$item = DB::fetch($sql);
            Portal::$document_title = 'Đánh giá SALE / CSKH của đơn hàng '.$id;
        }
        if(User::is_login() and self::$group_id){
            require_once 'forms/care_detail.php';
            $this->add_form(new CareDetailForm());
        }else{
            Url::access_denied();
        }
    }
}

