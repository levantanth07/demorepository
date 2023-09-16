<?php
class PosForm extends Form{
    protected $map;
    function __construct(){
        Form::Form('PosForm');
        //$languages = DB::fetch_all('select id,name,icon_url from language where active=1');
        if(!Url::get('exit_order')) {
            $this->add('mobile', new TextType(true, 'Chưa nhập số điện thoại', 0, 225));
        }
        //$this->link_css('assets/default/css/tabs/tabpane.css');
        //$this->link_js('assets/default/css/tabs/tabpane.js');
        $this->link_js('packages/core/includes/js/jquery/jquery.MultiFile.js');
        //$this->link_js('assets/standard/js/datetimepicker.js');
        //$this->link_css('assets/standard/css/datetimepicker.css');
        $this->link_js('assets/standard/js/autocomplete.js');
        $this->link_css('assets/standard/css/autocomplete/autocomplete.css');
        $this->link_css('packages/vissale/modules/AdminOrders/css/common.css?v=30062019');

        $this->add('orders_products.product_id',new TextType(false,'lỗi nhập sản phẩm',0,225));
    }
    function on_submit(){
        //ALTER TABLE `orders` CHANGE `user_confirmed_trust` `pos` TINYINT(1) NULL DEFAULT '0';
        $submit_success = true;
        if($this->check()){
            if(Url::get('exit_order')){
                if($url = Url::get('refer_url')){
                    $id = false;
                    if($id = Url::iget('id')){
                        DB::update('orders',array('last_online_time'=>0,'last_edited_account_id'=>''),'id="'.$id.'"');
                    }
                    AdminOrdersDB::close_edit_order($id);
                }else{
                    if($id = Url::iget('id')){
                        DB::update('orders',array('last_online_time'=>0,'last_edited_account_id'=>''),'id="'.$id.'"');
                        AdminOrdersDB::close_edit_order($id);
                        //Url::redirect_current(array('exit_edit'=>Url::iget('id')));
                    }else{
                        AdminOrdersDB::close_edit_order();
                        //Url::redirect_current();
                    }
                }
            }else{
                $nhan_vien_phu_trach = 0;
                $rows = $this->save_item();
                $assigned_log_str = '';
                if(!$this->is_error()){
                    $status_config = AdminOrdersConfig::config_shipping_status();
                    $user_id = get_user_id();
                    // mysqli_begin_transaction(DB::$db_connect_id);
                    try {
                        if(Session::get('admin_group') or check_user_privilege('CHIADON')){
                            if($assigned_user_id = DB::escape(URL::get('assigned_user_id')) and $assigned_user=DB::fetch('select id,username from users where id='.$assigned_user_id,'username')){
                                $rows += array('user_assigned'=>$assigned_user_id,'assigned'=>date('Y-m-d H:i:s'));
                                $assigned_log_str = 'Chia đơn cho '.$assigned_user;
                            }
                        }
                        $status_id = DB::escape(Url::get('status_id'));
                        if(Url::get('checkout')){//trường hợp bán lẻ checkout thì chuyển về trạng thái thành công
                            $status_id = 5;//trạng thái thành công
                        }
                        $create_export_invoice = false;
                        $create_import_invoice = false;
                        if(Url::get('id')  and $item = AdminOrders::$item){
                            $id = $item['id'];
                            if(Session::get('admin_group') and Url::get('user_created')){
                                $user_id_ = DB::escape( Url::get('user_created'));
                                $rows += array('user_created'=>$user_id_);
                            }
                            $rows += array('modified'=>date('Y-m-d H:i:s'));
                            /*if(!$item['fb_post_id']){
                                $rows += array('code'=>Url::get('code'));
                            }*/
                            if(Session::get('admin_group') or !$item['status_id'] or $item['status_id'] != 5){//5: trang thai thanh cong
                                if($status_id){
                                    $rows += array('status_id'=>$status_id);
                                }
                                if(!$item['user_confirmed'] and $status_id == 7 and Url::get('old_status_id') != $status_id){//7: trang thai xac nhan
                                    $create_export_invoice = true;
                                    $nhan_vien_phu_trach = get_user_id();
                                    $rows += array('confirmed'=>date('Y-m-d H:i:s'),'user_confirmed'=>$user_id);
                                }
                            }
                            if($status_id == 9 && Url::get('old_status_id') != $status_id && $order_shipping = AdminOrdersDB::checkExistShippingAddress($id)){
                                if ($order_shipping['shipping_status'] == $status_config['CHO_LAY_HANG']) {
                                    // Chỉ cho phép hủy nếu đơn ở trạng thái chờ lấy hàng
                                    $costs_config = AdminOrdersConfig::get_list_shipping_costs();
                                    if ($order_shipping['carrier_id'] == 'api_ghn') {
                                        $url_cancel = $costs_config[$order_shipping['carrier_id']]['cancel_shipping_url'];
                                        $api_token = $costs_config[$order_shipping['carrier_id']]['token'];
                                        $params_create_order = [
                                            'token' => $api_token,
                                            'OrderCode' => $order_shipping['shipping_order_code']
                                        ];
                                        $response_api = AdminOrdersConfig::execute_curl($url_cancel, json_encode($params_create_order));
                                        $response_api = json_decode($response_api, true);
                                        if ($response_api['code']) {
                                            DB::update_id('orders_shipping', [
                                                'shipping_status' => $status_config['HUY']
                                            ], $order_shipping['id']);

                                            DB::insert('order_shipping_status_history', [
                                                'order_id' => $id,
                                                'status' => $status_config['HUY'],
                                                'user_id' => $user_id
                                            ]);
                                        }
                                    } else if ($order_shipping['carrier_id'] == 'api_ghtk') {
                                        $url_cancel = $costs_config[$order_shipping['carrier_id']]['cancel_shipping_url'] . '/' . $order_shipping['shipping_order_code'];
                                        $api_token = $costs_config[$order_shipping['carrier_id']]['token'];
                                        $curl = curl_init();

                                        curl_setopt_array($curl, array(
                                            CURLOPT_URL => $url_cancel,
                                            CURLOPT_RETURNTRANSFER => true,
                                            CURLOPT_CUSTOMREQUEST => "POST",
                                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                            CURLOPT_HTTPHEADER => array(
                                                "Token: " . $api_token,
                                            ),
                                        ));

                                        $response = curl_exec($curl);
                                        curl_close($curl);
                                        $response_api = json_decode($response, true);
                                        if ($response_api['success']) {
                                            DB::update_id('orders_shipping', [
                                                'shipping_status' => $status_config['HUY']
                                            ], $order_shipping['id']);

                                            DB::insert('order_shipping_status_history', [
                                                'order_id' => $id,
                                                'status' => $status_config['HUY'],
                                                'user_id' => $user_id
                                            ]);
                                        }
                                    }
                                }
                            }

                            if($status_id == 8 and Url::get('old_status_id') != $status_id){//8: chuyển hàng
                                $rows += array('delivered'=>date('Y-m-d H:i:s'),'user_delivered'=>$user_id);
                            }
                            $order_extra = DB::fetch('select id,order_id,accounting_user_confirmed,accounting_confirmed from orders_extra where order_id='.$id);
                            if($item['status_id'] != 3 and $status_id == 3 and Url::get('old_status_id') != $status_id){//3: trang thai ke toan mac dinh
                                if(empty($order_extra)){
                                    $rows_extra = array('order_id'=>$id,'group_id'=>AdminOrders::$group_id,'accounting_confirmed'=>date('Y-m-d H:i:s'),'accounting_user_confirmed'=>$user_id);
                                    DB::insert('orders_extra',$rows_extra);
                                }else{
                                    if(!$order_extra['accounting_user_confirmed']){
                                        $rows_extra = array('accounting_confirmed'=>date('Y-m-d H:i:s'),'accounting_user_confirmed'=>$user_id);
                                        DB::update('orders_extra',$rows_extra,'id='.$order_extra['id']);
                                    }
                                }
                            }

                            $order_viettel = DB::fetch('SELECT id FROM orders_viettel WHERE order_id = '.$id);
                            if (empty($order_viettel) && Url::get('viettel_service')) {
                                DB::insert('orders_viettel', [
                                    'order_id' => $id,
                                    'viettel_service' => DB::escape(Url::get('viettel_service'))
                                ]);
                            } elseif (!empty($order_viettel)) {
                                // System::debug(Url::get('viettel_service'));
                                DB::update('orders_viettel', [
                                    'viettel_service' => DB::escape(Url::get('viettel_service'))
                                ], 'id='.$order_viettel['id']);
                            }

                            AdminOrdersDB::update_edited_log($id,$rows);
                            DB::update_id('orders',$rows+array('last_online_time'=>0,'last_edited_account_id'=>''),$id);

                            if($status_id == CHUYEN_HANG){//8: chuyển hàng
                                $submit_success = $this->update_deliver($id);//goi ham xu ly van chuyen
                            }
                            if($status_id == CHUYEN_HOAN and Url::get('old_status_id') != $status_id and $item['user_confirmed']){
                                $create_import_invoice = true;
                            }
                            if($status_id){
                                AdminOrdersDB::update_revision($item['id'],Url::get('old_status_id'),$status_id);
                            }
                        }else{// trường hợp thêm mới
                            $rows += array('status_id'=>$status_id?$status_id:10,'pos'=>1);
                            if($status_id){
                                $rows += array('user_assigned'=>0);//$user_id
                            }
                            if(Url::get('cmd')=='add' or Session::get('admin_group') and Url::get('user_created')){
                                $user_id = Url::get('user_created')?DB::escape(Url::get('user_created')):get_user_id();
                            }
                            $rows += array(
                                'code'=> DB::escape(Url::get('code')),
                                'user_created'=>$user_id,
                                'created'=>date('Y-m-d H:i:s'),
                                'group_id'=>Session::get('group_id')
                            );
                            if(Session::get('master_group_id')){
                                $rows += array(
                                    'master_group_id'=>Session::get('master_group_id')
                                );
                            }else{
                                $rows += array(
                                    'master_group_id'=>0
                                );
                            }
                            if($status_id==XAC_NHAN or $status_id==THANH_CONG or $status_id==CHUYEN_HANG){// trang thai thanh cong
                                $rows += array('confirmed'=>date('Y-m-d H:i:s'),'user_confirmed'=>get_user_id());
                                $create_export_invoice = true;
                            }
                            //added by khoand in 04/12/2018
                            $id = DB::insert('orders',$rows);

                            if($phone_store_id = Session::get('phone_store_id')){
                                $rows_extra = array(
                                    'order_id'=>$id,
                                    'group_id'=>AdminOrders::$group_id,
                                    'accounting_confirmed'=>'0000-00-00 00:00:00',
                                    'accounting_user_confirmed'=>'0',
                                    'upsale_from_group_id'=>'0',
                                    'upsale_from_user_id'=>'0',
                                    'phone_store_id'=>$phone_store_id
                                );
                                DB::insert('orders_extra',$rows_extra);
                            }

                            // Lưu dịch vụ Viettel Post
                            if (Url::get('viettel_service')) {
                                DB::insert('orders_viettel', [
                                    'order_id' => $id,
                                    'viettel_service' => Url::get('viettel_service')
                                ]);
                            }

                            // Khởi tạo trạng thái đơn hàng được đặt
                            DB::insert('order_shipping_status_history', [
                                'order_id' => $id,
                                'status' => $status_config['DA_DAT'],
                                'created_at' => date('Y-m-d H:i:s')
                            ]);

                            if($status_id == 8){//8: chuyển hàng
                                $this->update_deliver($id);//goi ham xu ly van chuyen
                            }
                        }
                        if($assigned_log_str and $id){
                            AdminOrdersDB::update_revision($id,false,false,$assigned_log_str);
                        }
                        AdminOrdersDB::update_customer($id,$nhan_vien_phu_trach);
                        AdminOrdersDB::update_order_product($id);
                        AdminOrdersDB::update_upsale($id);
                        if(AdminOrders::$create_export_invoice_when_confirmed == false and AdminOrders::$create_export_invoice_when_delivered == false){
                            $create_export_invoice = false;
                        }
                        if($create_export_invoice){
                            require_once ('packages/vissale/modules/QlbhStockInvoice/db.php');
                            QlbhStockInvoiceDB::xuat_kho($id);
                        }
                        if(AdminOrders::$create_import_invoice_when_return == false){
                            $create_import_invoice = false;
                        }
                        if($create_import_invoice){
                            require_once ('packages/vissale/modules/QlbhStockInvoice/db.php');
                            QlbhStockInvoiceDB::nhap_kho($id);
                        }
                        if(Url::get('total_price')>0){// voi ban le
                            AdminOrdersDB::update_order_payment($id);
                            AdminOrdersDB::update_order_refund($id);
                        }
                        if ($submit_success) {
                            // mysqli_commit(DB::$db_connect_id);
                            mysqli_close(DB::$db_connect_id);
                        }
                        if(Session::get('user_id') == 'dinhkkk'){
                            echo Url::get('status_id');
                            exit();
                        }
                        exit();
                        AdminOrdersDB::close_edit_order($id);
                    } catch (Exception $e) {
                        // mysqli_rollback(DB::$db_connect_id);
                        mysqli_close(DB::$db_connect_id);
                        echo "Lỗi chia đơn.";
                        die();
                    }

                }
            }
        }
    }
    function draw(){
        /*if(User::is_admin()){
            define('FAYE_SERVER', 'https://vissale.com:8001/');
            $channel = "/channel_group_".Session::get('group_id');
            $data = array(
                    'order_id' => Url::iget('id'),
                    'account_id' => Session::get('user_id')
            );
            $curlResult = $this->postJSONFaye($channel, $data);
        }*/
        $group_id = Session::get('group_id');
        $master_group_id = Session::get('master_group_id');
        $account_type = $this->map['account_type'] = Session::get('account_type');
        $this->map = array();
        $this->map['require_address'] = AdminOrders::$require_address;
        $shipping_address = AdminOrdersDB::getShippingAddress($group_id);
        $this->map['shipping_address'] = $shipping_address;
        $shipping_info = '';
        if($account_type==3 or $master_group_id){
            if($account_type!=3){
                $group_id = $master_group_id;
            }
            $bundles = AdminOrdersDB::get_bundles($group_id);
            $this->map['search_group_id_list'] = array(''=>'Tất cả cty') + MiString::get_list(AdminOrdersDB::get_groups($group_id));
        }else{
            $bundles = AdminOrdersDB::get_bundles();
        }
        require_once 'cache/config/product_status.php';
        require_once 'cache/tables/currency.cache.php';
        $accept_edit_transport = 'accept';
        $enable_product_rating = get_group_options('enable_product_rating');
        // $enable_product_rating = 1;
        $this->map['enable_product_rating'] = $enable_product_rating;
        $viettel_post_config = AdminOrdersConfig::viettel_post_config();
        $viettel_post_arr = ['' => 'Chọn dịch vụ'];
        foreach ($viettel_post_config as $value) {
            $viettel_post_arr[$value['SERVICE_CODE']] = $value['SERVICE_NAME'];
        }

        $this->map['viettel_service_list'] = $viettel_post_arr;
        $this->map['business_model'] = $business_model = get_group_options('business_model');
        $this->map['user_id'] = DB::fetch('select id from users where username="'.Session::get('user_id').'"','id');
        $this->map['md5_user_id'] = md5('vs'.$this->map['user_id']);
        $this->map['refer_url'] =  isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';

        $this->map['categories'] = 'N/A';
        $this->map['colors'] = 'N/A';
        $this->map['sizes'] = 'N/A';
        $this->map['fb_name'] = '';
        $this->map['fb_user_page'] = '';
        $this->map['fb_post_link'] = '';
        $this->map['fb_comment'] = '';
        $this->map['fb_conversation_id'] = '';
        $this->map['assigned_user_name'] = '';
        $this->map['created_user_name'] = '';
        $this->map['can_edit_status'] =  false;
        $this->map['user_confirmed_name'] = '';
        $this->map['confirmed'] = '';
        $this->map['stt_id'] = 0;
        $this->map['fb_customer_id'] = '';
        $this->map['status_name'] = '';
        $this->map['status_id'] = 0;
        if(Url::iget('id') and $order = AdminOrders::$item){
            $this->map['status_name'] = AdminOrders::$item['status_id']?DB::fetch('select name from statuses where id='.AdminOrders::$item['status_id'],'name'):'';
            $this->map['status_id'] = AdminOrders::$item['status_id'];
            if(AdminOrders::$item['status_id'] == CHUA_XAC_NHAN){
                $this->map['status_name'] = 'Đơn mới';
            }
            $order['email'] = $order['customer_id']?DB::fetch('select email from crm_customer where id='.$order['customer_id'],'email'):'';
            ///
            $order_extra = AdminOrdersDB::get_order_extra($order['id']);
            if(!empty($order_extra)){
                $order += $order_extra;
            }
            ///
            $users_can_edit_order = get_group_options('users_can_edit_order');

            if($users_can_edit_order == 1 and $order['user_assigned'] and !Session::get('admin_group') and !AdminOrders::$quyen_sale and !AdminOrders::$quyen_ke_toan and !AdminOrders::$quyen_admin_ke_toan){
                echo '<div class="container"><br>';
                echo '<div class="text-danger">Đơn hàng mã #'.Url::iget('id').' đã được gán.  Marketing (hoặc người tạo đơn) không có quyền sửa.</div><hr><button onclick="window.location=\'index062019.php?page=admin_orders&\'" class="btn btn-success">Quay lại</button>';
                echo '</div>';
                die;
            }
            if($acc=AdminOrdersDB::is_edited(Url::iget('id'))){
                echo '<div class="container"><br>';
                echo '<div class="text-danger">Đơn hàng mã #'.Url::iget('id').' đang được thao tác bởi "'.$acc.'"</div><hr><button onclick="window.location=\'index062019.php?page=admin_orders&\'" class="btn btn-success">Quay lại</button>';
                echo '</div>';
                die;
            }else{
                if(AdminOrdersDB::is_clicked(Url::iget('id'))==false){
                    AdminOrdersDB::update_revision(Url::iget('id'),false,false,'Mở đơn hàng');
                }
                DB::update('orders',array('last_online_time'=>time(),'last_edited_account_id'=>Session::get('user_id')),'id="'.DB::escape(Url::iget('id')).'"');
            }

            if ($order_shipping = AdminOrdersDB::getOrderShippingByOrderId($order['id'])) {
                $except_array = ['id'];
                foreach ($order_shipping as $key => $value) {
                    if (!in_array($key, $except_array)) {
                        $_REQUEST[$key] = $value;
                    }
                }

                $shipping_address_text = $order_shipping['shipping_address_text'];
                $shipping_info = $order_shipping;
                if ($order_shipping['shipping_status'] != 1) {
                    $accept_edit_transport = 'refuse';
                }
            }

            if ($order_viettel = DB::fetch("SELECT id, viettel_service FROM orders_viettel WHERE order_id = " . $order['id'])) {
                $_REQUEST['viettel_service'] = $order_viettel['viettel_service'];
            }
            ///
            $this->map['confirmed'] = date('H:i:s d/m/Y',strtotime($order['confirmed']));
            $this->map['user_confirmed_name'] = $order['user_confirmed']?DB::fetch('select id,concat(name," - ",username) as name from users where id='.$order['user_confirmed'],'name'):'';
            $this->map['source_id'] = $order['source_id'];
            $this->map['assigned_user_name'] = $order['user_assigned']?DB::fetch('select concat(name," - ",username) as name from users where id='.$order['user_assigned'],'name'):' ... ';
            $this->map['created_user_name'] = $order['user_created']?DB::fetch('
				select 
					'.((Session::get('account_type')==3 or Session::get('master_group_id'))?'concat(users.name,"/",concat(users.username,"<br>cty ",groups.name)) as name':'concat(users.name,"/",users.username) as name').'
				from 
					users
					join  `groups` on groups.id = users.group_id
				where 
					users.id='.$order['user_created']
                ,'name'):' ... ';
            if($city = DB::fetch('select province_id as id,province_name from zone_provinces_v2 where province_id='.$order['city_id'])){
                $this->map['city_name'] = $city['province_name'];
                $this->map['city_id'] = $city['id'];
            }
            if($district = DB::fetch('select district_id as id,district_name from zone_districts_v2 where district_id='.$order['district_id'])){
                $this->map['district_name'] = $district['district_name'];
                $this->map['district_id'] = $district['id'];
            }
            if($ward_id = DB::fetch('select ward_id as id,ward_name from zone_wards_v2 where ward_id='.$order['ward_id'])){
                $this->map['ward_name'] = $ward_id['ward_name'];
                $this->map['ward_id'] = $ward_id['id'];
            }
            $this->map['categories'] = AdminOrdersDB::get_categories($order['id']);
            $order['price'] = System::display_number($order['price']);
            $order['discount_price'] = System::display_number($order['discount_price']);
            $order['shipping_price'] = System::display_number($order['shipping_price']);
            $order['other_price'] = System::display_number($order['other_price']);
            $order['total_price'] = System::display_number($order['total_price']);

            if($status_cr = DB::select_id('statuses',$order['status_id'])){
                $this->map['stt_id'] = $status_cr['id'];
                $_REQUEST['status_id'] = $status_cr['id'];
                if(isset($status_cr['name'])){
                    $this->map['stt_name'] = $status_cr['name'];
                }
                else{
                    $this->map['stt_name'] = '';
                }
                if(isset($status_cr['color'])){
                    $this->map['stt_color'] = $status_cr['color'];
                }
                else{
                    $this->map['stt_color'] = '';
                }
            }

            foreach($order as $key=>$value){
                if(!isset($_REQUEST[$key])){
                    if($key=='cataloge' and $value){
                        eval('$catalogs ='.$value.';');
                    }else{
                        $_REQUEST[$key] = $value;
                    }
                }
                $images = DB::fetch_all('select id,name_'.Portal::language().' as name,image_url,thumb_url,small_thumb_url from product_image where product_id = '.DB::escape(Url::iget('id')).'');
            }
            if(!isset($_REQUEST['mi_order_product'])){
                $_REQUEST['mi_order_product'] = AdminOrdersDB::get_order_product($order['id'],false);
                //System::debug($_REQUEST['mi_order_product']);die;
            }
            /////
            if(!isset($_REQUEST['mi_payment'])){
                $_REQUEST['mi_payment'] = AdminOrdersDB::get_order_payment($order['id'],1);
            }
            if(!isset($_REQUEST['mi_refund'])){
                $_REQUEST['mi_refund'] = AdminOrdersDB::get_order_payment($order['id'],0);
            }
            /////
            if($order['fb_customer_id']){
                $this->map['fb_customer_id'] = $order['fb_customer_id'];
               /* $fb_user = get_fb_user_page($order['fb_customer_id']);
                $this->map['fb_user_page'] = $fb_user['fb_id'];
                $this->map['fb_name'] = $fb_user['fb_name']?$fb_user['fb_name']:'FB của khách';
                if(preg_match('/\_/',$order['fb_post_id'])){
                    $this->map['fb_post_id'] = $order['fb_post_id'];
                }else{
                    $fb_post=get_fb_post($order['fb_customer_id']);
                    $this->map['fb_post_id'] = $fb_post['post_id'];
                }
                if(preg_match('/\-/',$order['fb_comment_id'])){
                    $this->map['fb_comment_id'] = $order['fb_comment_id'];
                }else{
                    $fb_post_comment = get_fb_post_comment($order['fb_comment_id']);
                    $this->map['fb_comment_id'] = $fb_post_comment['comment_id'];
                }
                if($order['fb_comment_id'] and $cm = DB::fetch('select id,fb_conversation_id from fb_post_comments where id="'.$order['fb_comment_id'].'"')){
                    $this->map['fb_conversation_id'] = $cm['fb_conversation_id'];
                }*/
            }
        }else{// truong hop them moi
            if(!Url::get('user_created')){
                $_REQUEST['user_created'] = get_user_id();
            }
        }

        $shipping_options = [];
        $shipping_option_viettel = [];
        if (empty(get_group_options('integrate_shipping'))) {
            $accept_edit_transport = 'not-intergrate-shipping';
        } else {
            $shipping_options = AdminOrdersDB::getShippingOptionsActive();
            if (!empty($shipping_options)) {
                $shipping_options = array_values($shipping_options);
                foreach ($shipping_options as $item) {
                    if ($item['carrier_id'] == "api_viettel_post") {
                        $config = AdminOrdersConfig::get_list_shipping_costs();
                        $config = $config['api_viettel_post'];
                        $token = $item['token'];
                        $headers = ["Token: " . $token];
                        $response_viettel = AdminOrdersConfig::execute_get_curl($config['api_get_list_inventory'], $headers);
                        $response_viettel_api = json_decode($response_viettel, true);
                        if ($response_viettel_api['status'] == 200) {
                            $shipping_option_viettel[$item['id']] = $response_viettel_api['data'];
                        }
                    }
                }
            }
        }

        $this->map['shipping_options'] = json_encode($shipping_options, JSON_UNESCAPED_UNICODE);
        $this->map['shipping_option_viettel'] = json_encode($shipping_option_viettel, JSON_UNESCAPED_UNICODE);
        $this->map['accept_edit_transport'] = $accept_edit_transport;
        $this->map['shipping_info'] = $shipping_info;
        $this->map['note_code_list'] = [
            'CHOXEMHANGKHONGTHU' => 'Cho xem hàng và không cho thử',
            'CHOTHUHANG' => 'Cho xem hàng và thử hàng',
            'KHONGCHOXEMHANG' => 'Không cho xem hàng'
        ];
        $is_cod_list = [
            1 => 'Cho phép thu tiền hộ',
            2 => 'Không thu tiền hộ'
        ];
        $this->map['is_cod_list'] = $is_cod_list;

        $is_freeship_list = [
            0 => 'Không miễn phí vận chuyển',
            1 => 'Miễn phí vận chuyển'
        ];
        $this->map['is_freeship_list'] = $is_freeship_list;

        $pick_option_list = [
            'cod' => 'COD đến lấy hàng',
            'post' => 'Gửi hàng tại bưu cục'
        ];
        $this->map['pick_option_list'] = $pick_option_list;

        $this->map['stt_color_add']='';
        if(Url::get('cmd')=='add'){
            $status_cr = DB::select_id('statuses',10);
            if(isset($status_cr['color'])){
                $this->map['stt_color_add'] = $status_cr['color'];
            }
            else{
                $this->map['stt_color_add']='';
            }
        }
        $order_id = (isset($order['id'])?$order['id']:false);
        $categories = AdminOrdersDB::get_category($order_id);
        require_once 'packages/core/includes/utils/category.php';
        combobox_indent($categories);
        $category_options = '';
        foreach($categories as $value){
            $category_options .= '<option value="'.$value['id'].'" '.(($order_id and $value['product_id']==$order_id)?'selected':'').'>'.$value['name'].'</option>';
        }
        if(!Url::get('time')){
            $_REQUEST['time'] = date('d/m/Y');
        }
        /////
        $products = AdminOrdersDB::get_products();
        $product_options = '<option value="">Chọn sản phẩm</option>';
        foreach($products as $value){
            $product_options .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
        }
        /////
        if(Url::iget('id')){
            $this->map['order_revisions'] = AdminOrdersDB::get_order_revisions();
        }else{
            $this->map['order_revisions'] = array();
        }
        //////
        if(Session::get('admin_group')){
            $status_arr = AdminOrdersDB::get_status();
        }else{
            $status_arr = AdminOrdersDB::get_status_from_roles($this->map['user_id']);
        }

        $status_arr_custom = [];
        foreach ($status_arr as $value) {
            $level = !empty($value['level']) ? $value['level'] : 0;
            $status_arr_custom[$level][] = $value;
        }
        // System::debug($status_arr_custom); die();
        $this->map['status_arr_custom'] = $status_arr_custom;

        if(!Url::get('status_id')){
            $_REQUEST['status_id'] = 10; //mặc định trạng thái đơn hàng là chưa xác nhận
        }
        if(!Url::get('source_id')){
            $_REQUEST['source_id'] = 3; //mặc định tạo tay
        }
        $arr = MiString::get_list($status_arr);
        if(array_key_exists(Url::iget('status_id'),$arr)){
            if(Url::get('status_id')!=5){
                $this->map['can_edit_status'] = true;
            }
        }
        if(Session::get('admin_group')){
            $this->map['can_edit_status'] = true;
        }
        //////
        $this->map['assigned_user_id_list']=array(""=>'Chọn nhân viên')+MiString::get_list(AdminOrdersDB::get_users('GANDON',false,true),'full_name');
        //////
        $this->map += array(
            'shipping_service_id_list'=>array(""=>'--Chọn--')+MiString::get_list(AdminOrdersDB::shipping_services()),
            'category_options'=>$category_options,
            'product_options'=>$product_options,
            'status_list'=> $status_arr,
            'bundle_id_list'=>array(""=>'--Chọn--')+MiString::get_list($bundles),
            //'source_id_list'=>array(""=>'--Chọn--')+MiString::get_list(AdminOrdersDB::get_source())
        );
        $zones = AdminOrdersDB::get_zones();
        $this->map['zones'] = $zones;
        $this->map['city_id_list'] = ['0'=>'Chọn tỉnh thành'] + MiString::get_list($zones);
        //System::debug($this->map);die;
        $this->map['sources'] = AdminOrdersDB::get_source();
        $this->map['source_id_list'] = MiString::get_list($this->map['sources']);
        $this->map['type_list'] = [
            1=>'SALE',
            2=>'CSKH',
            9=>'Tối ưu',
            3=>'Đặt lại',
            4=>'Đặt lại lần 1',
            5=>'Đặt lại lần 2',
            6=>'Đặt lại lần 3',
            7=>'Đặt lại lần 4',
            8=>'Đặt lại lần 5',
        ];

        if(Url::get('id')){
            $title = 'Sửa ';
        }else{
            $title = 'Thêm mới ';
        }
        $title .= 'đơn hàng';
        //if($business_model)
        {
            $layout = 'pos';
            $this->map['payment_method_options'] = '<option value="1">Tiền mặt</option><option value="3">Thẻ</option><option value="2">Chuyển khoản</option>';
        }
        $this->map['title'] = $title;
        $users = AdminOrdersDB::get_users(false,false,true);
        $this->map['user_created_list'] = array(""=>'Chọn người tạo')+MiString::get_list($users,'full_name');
        $users = AdminOrdersDB::get_users('MARKETING',false,true);
        $this->map['upsale_from_user_id_list'] = array(""=>'Chọn tài khoản MKT')+MiString::get_list($users,'full_name');

        $invoice_type = Session::get('invoice_type');
        if(Url::get('id')){
            $title = 'Sửa thông tin ';
        }else{
            $title = 'Sử dụng ';
        }
        if($invoice_type){
            $layout = $invoice_type;
            $type_title = ($invoice_type=='ban_le') ? 'đơn lẻ' : 'liệu trình';
        }else{
            $type_title = 'đơn hàng';
        }
        $title .= $type_title;
        $this->map['type_title'] = $type_title;
        $this->map['is_deleted'] = 0;
        $this->map['payment_type_options'] = '<option value="1">Tiền mặt</option><option value="3">Thẻ</option><option value="2">Chuyển khoản</option>';
        $this->parse_layout($layout,$this->map);
    }
    function postJSONFaye($channel, Array $data = [], Array $ext = [], $server = null) {
        if (empty($server)) {
            $server = FAYE_SERVER;
        }
        $body = json_encode(array(
            'channel' => $channel,
            'data' => $data,
            'ext' => $ext,
        ));
        $curl = curl_init($server);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($body),
        ));
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    // Xử lý vận chuyển
    function update_deliver($id)
    {
        $group_id = Session::get('group_id');
        $params_create_order = $this->get_params_create_order();
        $result_deliver = true;
        $is_freeship = Url::get('is_freeship');
        if (!empty($params_create_order)) {
            $carrier_id = Url::get('shipping_carrier_id');

            $status_config = AdminOrdersConfig::config_shipping_status();
            if ($carrier_id == 'api_ghtk') {
                $rows_carrier = [
                    'order_id' => $id,
                    'shipping_address_text' => '<div><b>'. $params_create_order['order']['pick_name'] .'</b></div> <div>'. $params_create_order['order']['pick_tel'] .'</div> <div>'. $params_create_order['order']['pick_address'] . ',' . $params_create_order['order']['pick_district'] . ',' . $params_create_order['order']['pick_province'] . '</div>',
                    'carrier_id' => $carrier_id,
                    'shipping_status' => 1,
                    'note_code' => $params_create_order['order']['NoteCode'],
                    'total_weight' => $params_create_order['order']['Weight'],
                    'total_width' => $params_create_order['order']['Width'],
                    'total_height' => $params_create_order['order']['Height'],
                    'total_length' => $params_create_order['order']['Length'],
                    'shipping_note' => $params_create_order['order']['note'],
                    'cod_amount' => $params_create_order['order']['cod_amount'],
                    'shipping_address_id' => $params_create_order['order']['shipping_address_id'],
                    'is_cod' => $params_create_order['order']['is_cod'],
                    'address_text' => $params_create_order['order']['address_text'],
                    'pick_option' => $params_create_order['order']['pick_option'],
                    'is_freeship' => $params_create_order['order']['is_freeship'],
                    'group_id' => $group_id,
                    'shipping_option_id' => Url::get('shipping_option_id') ?DB::escape( Url::get('shipping_option_id')) : null
                ];
                if (!$order_shipping = AdminOrdersDB::checkExistShippingAddress($id)) {
                    $curl = curl_init();
                    $params_create_order['order']['id'] = $id;
                    $order = json_encode($params_create_order);

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $params_create_order['order']['url_create'],
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => $order,
                        CURLOPT_HTTPHEADER => array(
                            "Content-Type: application/json",
                            "Token: " . $params_create_order['order']['token'],
                            "X-Refer-Token: " . $params_create_order['order']['refer_token']
                        ),
                    ));

                    $response = curl_exec($curl);
                    curl_close($curl);
                    $response_api = json_decode($response, true);
                    if ($response_api['success']) {
                        $rows_carrier['shipping_order_code'] = $response_api['order']['label'];
                        $rows_carrier['total_service_fee'] = $response_api['order']['fee'];
                        DB::insert('orders_shipping', $rows_carrier);

                        DB::insert('order_shipping_status_history', [
                            'order_id' => $id,
                            'status' => $status_config['CHO_LAY_HANG'],
                            'created_at' => date('Y-m-d H:i:s')
                        ]);

                        $shipping_fee = $response_api['order']['fee'];
                        if ($is_freeship == 1) {
                            $shipping_fee = 0;
                        }

                        $cod_amount_origin = $params_create_order['order']['cod_amount_origin'];
                        $total_fee = $cod_amount_origin + $shipping_fee;
                        DB::update_id('orders', [
                            'shipping_price' => $shipping_fee,
                            'postal_code' => $response_api['order']['label'],
                            'total_price' => $total_fee
                        ], $id);
                    } else {
                        $result_deliver = false;
                    }
                    // System::debug($response_api); die();
                } else {
                    $result_deliver = false;
                }
            } else if ($carrier_id == 'api_ghn') {
                $rows_carrier = [
                    'order_id' => $id,
                    'shipping_address_text' => '<div><b>'. $params_create_order['ClientContactName'] .'</b></div> <div>'. $params_create_order['ClientContactPhone'] .'</div> <div>'. $params_create_order['ClientAddress'] .'</div>',
                    'carrier_id' => $carrier_id,
                    'shipping_status' => 1,
                    'note_code' => $params_create_order['NoteCode'],
                    'total_weight' => $params_create_order['Weight'],
                    'total_width' => $params_create_order['Width'],
                    'total_height' => $params_create_order['Height'],
                    'total_length' => $params_create_order['Length'],
                    'shipping_note' => $params_create_order['Note'],
                    'cod_amount' => $params_create_order['CoDAmount'],
                    'shipping_address_id' => $params_create_order['shipping_address_id'],
                    'is_cod' => $params_create_order['is_cod'],
                    'pick_option' => $params_create_order['pick_option'],
                    'is_freeship' => $params_create_order['is_freeship'],
                    'address_text' => $params_create_order['CustomerName'] . ',' . $params_create_order['CustomerPhone']  . ',' .$params_create_order['ShippingAddress'],
                    'group_id' => $group_id,
                    'shipping_option_id' => Url::get('shipping_option_id') ? DB::escape(Url::get('shipping_option_id')) : null
                ];
                if (!$order_shipping = AdminOrdersDB::checkExistShippingAddress($id)) {
                    // System::debug(json_encode($params_create_order)); die();
                    $response_api = AdminOrdersConfig::execute_curl($params_create_order['url_create'], json_encode($params_create_order));
                    $response_api = json_decode($response_api, true);
                    if ($response_api['code']) {
                        // System::debug($response_api); die();
                        $response_api = $response_api['data'];
                        $rows_carrier['shipping_order_code'] = $response_api['OrderCode'];
                        $rows_carrier['total_service_fee'] = $response_api['TotalServiceFee'];
                        $rows_carrier['shipping_order_id'] = $response_api['OrderID'];
                        DB::insert('orders_shipping', $rows_carrier);

                        DB::insert('order_shipping_status_history', [
                            'order_id' => $id,
                            'status' => $status_config['CHO_LAY_HANG'],
                            'created_at' => date('Y-m-d H:i:s')
                        ]);

                        $shipping_fee = $response_api['TotalServiceFee'];
                        if ($is_freeship == 1) {
                            $shipping_fee = 0;
                        }

                        $cod_amount_origin = $params_create_order['cod_amount_origin'];
                        $total_fee = $cod_amount_origin + $shipping_fee;

                        DB::update_id('orders', [
                            'shipping_price' => $shipping_fee,
                            'postal_code' => $response_api['OrderCode'],
                            'total_price' => $total_fee
                        ], $id);
                    } else {
                        $result_deliver = false;
                    }
                } else {
                    $ShippingOrderID = (int)$order_shipping['shipping_order_id'];
                    $OrderCode = $order_shipping['shipping_order_code'];
                    $params_create_order['ShippingOrderID'] = $ShippingOrderID;
                    $params_create_order['OrderCode'] = $OrderCode;
                    $response_api = AdminOrdersConfig::execute_curl($params_create_order['url_update'], json_encode($params_create_order));
                    $response_api = json_decode($response_api, true);
                    if ($response_api['code']) {
                        $response_api = $response_api['data'];
                        $rows_carrier['total_service_fee'] = $response_api['TotalServiceFee'];
                        DB::update_id('orders_shipping', $rows_carrier, $order_shipping['id']);

                        $shipping_fee = $response_api['TotalServiceFee'];
                        if ($is_freeship == 1) {
                            $shipping_fee = 0;
                        }

                        $cod_amount_origin = $params_create_order['cod_amount_origin'];
                        $total_fee = $cod_amount_origin + $shipping_fee;

                        DB::update_id('orders', [
                            'shipping_price' => $shipping_fee,
                            'total_price' => $total_fee
                        ], $id);
                        // System::debug($order_shipping['id']); die();
                    } else {
                        $result_deliver = false;
                    }

                    // System::debug($response_api); die();
                }
            } else if ($carrier_id == 'api_bdhn') {
                $ma_xac_thuc = "";
                if (Url::get('shipping_option_id')) {
                    $shipping_option = AdminOrdersDB::getShippingOptionById(Url::get('shipping_option_id'));
                    $ma_xac_thuc = $shipping_option['token'];
                }

                if (empty($ma_xac_thuc)) {
                    return;
                }

                $rows_carrier = [
                    'order_id' => $id,
                    'shipping_address_text' => '<div><b>'. $params_create_order['HoTenNguoiGui'] .'</b></div> <div>'. $params_create_order['DienThoaiNguoiGui'] .'</div> <div>'. $params_create_order['DiaChiNguoiGui'] .'</div>',
                    'carrier_id' => $carrier_id,
                    'shipping_status' => 1,
                    'note_code' => $params_create_order['NoteCode'],
                    'total_weight' => (float)Url::get('total_weight'),
                    'total_width' => (float)Url::get('total_width'),
                    'total_height' => (float)Url::get('total_height'),
                    'total_length' => (float)Url::get('total_length'),
                    'shipping_note' => $params_create_order['ShippingNote'],
                    'cod_amount' => 0,
                    'shipping_address_id' => $params_create_order['shipping_address_id'],
                    'is_cod' => $params_create_order['PhuongThuc'],
                    'pick_option' => $params_create_order['pick_option'],
                    'is_freeship' => $params_create_order['is_freeship'],
                    'address_text' => $params_create_order['HoTenNguoiNhan'] . ',' . $params_create_order['DienThoaiNguoiNhan']  . ',' .$params_create_order['DiaChiNguoiNhan'],
                    'group_id' => $group_id,
                    'total_service_fee' => 0,
                    'shipping_option_id' => Url::get('shipping_option_id') ? DB::escape(Url::get('shipping_option_id')) : null
                ];
                if (!$order_shipping = AdminOrdersDB::checkExistShippingAddress($id)) {
                    $soapclient = new SoapClient($params_create_order['url_ghi_du_lieu']);
                    $params = $params_create_order;
                    unset($params['url_ghi_du_lieu']);
                    unset($params['shipping_address_id']);
                    unset($params['NoteCode']);
                    unset($params['ShippingNote']);
                    unset($params['MaXacThuc']);
                    $params['SoDonHang'] = $id;
                    $response_phien = $soapclient->KetNoi(['Ma' => $ma_xac_thuc]);
                    if (!empty($response_phien->KetNoiResult)) {
                        $params['MaPhien'] = $response_phien->KetNoiResult;
                        $response_create_order = $soapclient->TaoYeuCauThuGom2017($params);
                        $result_order = explode('|', $response_create_order->TaoYeuCauThuGom2017Result);
                        $status_order = $result_order[0];
                        if ($status_order == 99) {
                            $rows_carrier['shipping_order_code'] = $result_order[1];
                            $rows_carrier['updated_at'] = date('Y-m-d H:i:s');
                            DB::insert('orders_shipping', $rows_carrier);

                            DB::insert('order_shipping_status_history', [
                                'order_id' => $id,
                                'status' => $status_config['CHO_LAY_HANG'],
                                'created_at' => date('Y-m-d H:i:s')
                            ]);

                            DB::update_id('orders', [
                                'postal_code' => $result_order[1]
                            ], $id);
                        } else {
                            $result_deliver = false;
                        }
                    } else {
                        $result_deliver = false;
                    }
                }
            } else if ($carrier_id == 'api_viettel_post') {
                $rows_carrier = $params_create_order['ignore'];
                $token = $rows_carrier['token'];
                $cod_amount_origin = $rows_carrier['cod_amount_origin'];
                unset($rows_carrier['token']);
                unset($rows_carrier['cod_amount_origin']);
                $rows_carrier['order_id'] = $id;
                $rows_carrier['group_id'] = $group_id;
                if (!$order_shipping = AdminOrdersDB::checkExistShippingAddress($id)) {
                    unset($params_create_order['ignore']);
                    $config = AdminOrdersConfig::get_list_shipping_costs();
                    $config = $config['api_viettel_post'];
                    $response_api = AdminOrdersConfig::execute_post_curl($config['api_create_order'], json_encode($params_create_order), $token);
                    $response_api = json_decode($response_api, true);
                    if ($response_api['status'] == 200) {
                        $response_api = $response_api['data'];
                        $rows_carrier['shipping_order_code'] = $response_api['ORDER_NUMBER'];
                        $rows_carrier['total_service_fee'] = $response_api['MONEY_TOTAL'];
                        DB::insert('orders_shipping', $rows_carrier);

                        DB::insert('order_shipping_status_history', [
                            'order_id' => $id,
                            'status' => $status_config['CHO_LAY_HANG'],
                            'created_at' => date('Y-m-d H:i:s')
                        ]);

                        $shipping_fee = $response_api['MONEY_TOTAL'];
                        if ($is_freeship == 1) {
                            $shipping_fee = 0;
                        }

                        $total_fee = $cod_amount_origin + $shipping_fee;
                        DB::update_id('orders', [
                            'shipping_price' => $shipping_fee,
                            'postal_code' => $response_api['ORDER_NUMBER'],
                            'total_price' => $total_fee
                        ], $id);
                    } else {
                        $result_deliver = false;
                    }
                    // System::debug($response_api); die();
                }
            }
        }

        return $result_deliver;
    }

    function get_params_create_order() {
        $data = [];
        if (Url::get('shipping_carrier_id')) {
            $shipping_carrier_id = Url::get('shipping_carrier_id');
            $costs_config = AdminOrdersConfig::get_list_shipping_costs();
            if (!empty($costs_config[$shipping_carrier_id])) {
                $config = $costs_config[$shipping_carrier_id];
                if ($shipping_address_id = Url::get('radio_shipping_address')) {
                    $shipping_address = AdminOrdersDB::getShippingAddressById($shipping_address_id);
                    $mobile = trim(addslashes(trim(Url::get('mobile'))));
                    $mobile = str_replace(array('(',')','-'),'.',$mobile);
                    $total_price = Url::get('total_price') ? System::calculate_number(Url::get('total_price')) : 0;
                    $shipping_price = Url::get('shipping_price') ? System::calculate_number(Url::get('shipping_price')) : 0;
                    $cod_amount = $total_price - $shipping_price;
                    $cod_amount_origin = $total_price - $shipping_price;
                    if (Url::get('is_cod') == 2) {
                        $cod_amount = 0;
                    }

                    if ($shipping_carrier_id == 'api_ghn') {
                        $clientAddress = $shipping_address['address'] . ', ' .$shipping_address['ward_name'] . ', '. $shipping_address['district_name'] . ', ' . $shipping_address['province_name'];

                        $from_district_id = $shipping_address['district_id'];
                        $to_district_id = Url::get('district_id');
                        $ghn_token = $config['token'];
                        /*if (!empty(get_group_options('ghn_token'))) {
                            $ghn_token = get_group_options('ghn_token');
                        }*/
                        if (Url::get('shipping_option_id')) {
                            $shipping_option = AdminOrdersDB::getShippingOptionById(Url::get('shipping_option_id'));
                            $ghn_token = $shipping_option['token'];
                        }

                        $pick_option = DB::escape(Url::get('pick_option'));
                        if ($pick_option == "cod") {
                            $service_id = $config['ServiceIdCod'];
                        } else {
                            $service_id = $config['ServiceIdPost'];
                        }

                        $is_freeship = Url::get('is_freeship');
                        $paymentTypeID = $config['PaymentTypeID'];
                        if ($is_freeship == 1) {
                            $paymentTypeID = 1;
                        }

                        $data = [
                            'shipping_address_id' => $shipping_address_id,
                            'url_create' => $config['create_shipping_url'],
                            'url_update' => $config['update_shipping_url'],
                            'token' => $ghn_token,
                            'PaymentTypeID' => $paymentTypeID,
                            'FromDistrictID' => (int)$from_district_id,
                            'ToDistrictID' => (int)$to_district_id,
                            'ClientContactName' => $shipping_address['name'],
                            'ClientContactPhone' => $shipping_address['phone'],
                            'ClientAddress' => $clientAddress,
                            'CustomerName' => trim(Url::get('customer_name')),
                            'CustomerPhone' => $mobile,
                            'ShippingAddress' => trim(DB::escape(Url::get('address'))) . ', ' . trim(DB::escape(Url::get('city_name'))),
                            'NoteCode' => Url::get('note_code'),
                            'ServiceID' => (int)Url::get('ServiceId'),
                            'Weight' => (float)Url::get('total_weight'),
                            'Length' =>(float) Url::get('total_length'),
                            'Width' => (float)Url::get('total_width'),
                            'Height' => (float)Url::get('total_height'),
                            'Note' => DB::escape(Url::get('shipping_note')),
                            'ReturnContactName' => DB::escape(trim(Url::get('customer_name'))),
                            'ReturnContactPhone' => $mobile,
                            'ReturnAddress' => DB::escape(trim(Url::get('address')) . ', ' . trim(Url::get('city_name'))),
                            'ReturnDistrictID' => (int)$to_district_id,
                            'ExternalReturnCode' => '',
                            'AffiliateID' => $config['AffiliateID'],
                            'is_cod' => Url::get('is_cod'),
                            'total_fee' => ($cod_amount + $shipping_price),
                            'CoDAmount' => $cod_amount,
                            'ShippingOrderCosts' => [
                                [
                                    'ServiceID' => $service_id
                                ]
                            ],
                            'pick_option' => $pick_option,
                            'is_freeship' => $is_freeship,
                            'cod_amount_origin' => $cod_amount_origin
                        ];
                    } else if ($shipping_carrier_id == 'api_ghtk') {
                        // GHTK
                        $token = $config['token'];
                        $refer_token = $config['token'];
                        if (Url::get('shipping_option_id')) {
                            $shipping_option = AdminOrdersDB::getShippingOptionById(Url::get('shipping_option_id'));
                            $token = $shipping_option['token'];
                        }
                        $data['order'] = [
                            'token' => $token,
                            'refer_token' => $refer_token,
                            'url_create' => $config['create_shipping_url'],
                            'shipping_address_id' => $shipping_address_id,
                            'pick_name' => $shipping_address['name'],
                            'pick_address' => $shipping_address['address'],
                            'pick_province' => $shipping_address['province_name'],
                            'pick_district' => $shipping_address['district_name'],
                            'pick_tel' => $shipping_address['phone'],
                            'tel' => $mobile,
                            'name' => Url::get('customer_name'),
                            'address' => Url::get('address'),
                            'province' => Url::get('city_name'),
                            'district' => Url::get('district_name'),
                            'pick_money' => $cod_amount,
                            'cod_amount' => $cod_amount,
                            'note' => Url::get('shipping_note'),
                            'value' => ($total_price - $shipping_price),
                            'Weight' => (float)Url::get('total_weight'),
                            'Length' =>(float) Url::get('total_length'),
                            'Width' => (float)Url::get('total_width'),
                            'Height' => (float)Url::get('total_height'),
                            'NoteCode' => Url::get('note_code'),
                            'is_cod' => Url::get('is_cod'),
                            'address_text' => Url::get('customer_name') . '- ' . $mobile .',' . trim(Url::get('address')) . ', ' . trim(Url::get('city_name')),
                            'pick_option' => Url::get('pick_option'),
                            'is_freeship' => Url::get('is_freeship'),
                            'cod_amount_origin' => $cod_amount_origin
                        ];
                        foreach (Url::get('mi_order_product') as $key => $value) {
                            $data['products'][] = [
                                'name' => AdminOrdersDB::getProductNameById($value['product_id']),
                                'weight' => (int)$value['qty'] * ($value['weight'] / 1000),
                                'quantity' => $value['qty']
                            ];
                        }
                    } else if ($shipping_carrier_id == 'api_bdhn') {
                        $dia_chi_kho_hang = $shipping_address['address'] . ', ' . $shipping_address['district_name'] . ', ' . $shipping_address['province_name'];
                        $data = [
                            'url_ghi_du_lieu' => $config['url_ghi_du_lieu'],
                            'shipping_address_id' => $shipping_address_id,
                            'HoTenNguoiGui' => $shipping_address['name'],
                            'DiaChiNguoiGui' => $dia_chi_kho_hang,
                            'DienThoaiNguoiGui' => $shipping_address['phone'],
                            'TenKhoHang' => $shipping_address['name'],
                            'DiaChiKhoHang' => $dia_chi_kho_hang,
                            'DienThoaiLienHeKhoHang' => $shipping_address['phone'],
                            'HoTenNguoiNhan' => DB::escape(Url::get('customer_name')),
                            'DiaChiNguoiNhan' => DB::escape(Url::get('address') . ', ' . Url::get('district_name') . ', ' . Url::get('city_name')),
                            'DienThoaiNguoiNhan' => $mobile,
                            'TongTrongLuong' => (float)Url::get('total_weight'),
                            'TongCuoc' => 0,
                            'TongTienPhaiThu' => ($total_price - $shipping_price),
                            'NgayGiao' => date('m/d/Y'),
                            'TinhThanh' => DB::escape(Url::get('city_name')),
                            'QuanHuyen' => DB::escape(Url::get('district_name')),
                            'PhuongThuc' => DB::escape(Url::get('is_cod')),
                            'NoiDungHang' => DB::escape(Url::get('shipping_note')),
                            'DonHangDoiTra' => false,
                            'MaHuyenPhat' => $config['MaHuyenPhat'],
                            'iddichvu' => 2,
                            'NoteCode' => DB::escape(Url::get('note_code')),
                            'ShippingNote' => DB::escape(Url::get('shipping_note')),
                            'MaXacThuc' => $config['ma_xac_thuc'],
                            'pick_option' => DB::escape( Url::get('pick_option')),
                            'is_freeship' => DB::escape(Url::get('is_freeship'))
                        ];
                    } else if ($shipping_carrier_id == 'api_viettel_post') {
                        if (Url::get('shipping_option_id') && Url::get('kho_viettel')) {
                            $shipping_option = AdminOrdersDB::getShippingOptionById(Url::get('shipping_option_id'));
                            $token = $shipping_option['token'];
                            $params = [
                                'from_province_name' => $shipping_address['viettel_province_name'],
                                'from_district_name' => $shipping_address['viettel_district_name'],
                                'to_province_name' => DB::escape(Url::get('city_name')),
                                'to_district_name' => DB::escape(Url::get('district_name')),
                            ];
                            $response_params = AdminOrdersConfig::get_zones_viettel_id($params, $config);
                            foreach ($response_params as $param) {
                                if (empty($param)) {
                                    continue;
                                }
                            }

                            $from_province_id = $response_params['from_province_id'];
                            $to_province_id = $response_params['to_province_id'];
                            $from_district_id = $response_params['from_district_id'];
                            $to_district_id = $response_params['to_district_id'];
                            $order_payment = 2; // Thu hộ tiền cước - tiền hàng
                            if (Url::get('is_freeship') == 1) {
                                $order_payment = 3;
                            }

                            if (Url::get('is_cod') == 2) {
                                // Không cho phép thu tiền hộ
                                $order_payment = 1; // Không thu tiền
                            }

                            $data = [
                                "ORDER_NUMBER" => Url::get('id'),
                                "GROUPADDRESS_ID" => Url::get('kho_viettel'),
                                "SENDER_PROVINCE" => $from_province_id,
                                "SENDER_DISTRICT" => $from_district_id,
                                "SENDER_FULLNAME" => $shipping_address['name'],
                                "SENDER_ADDRESS" => $shipping_address['address'],
                                "SENDER_PHONE" => $shipping_address['phone'],
                                "SENDER_WARD" => 0,
                                "RECEIVER_FULLNAME" => Url::get('customer_name'),
                                "RECEIVER_ADDRESS" => Url::get('address'),
                                "RECEIVER_PHONE" => $mobile,
                                "RECEIVER_WARD" => 0,
                                "RECEIVER_DISTRICT" => $to_district_id,
                                "RECEIVER_PROVINCE" => $to_province_id,
                                "PRODUCT_WEIGHT" => (float)Url::get('total_weight'),
                                "PRODUCT_LENGTH" => (float)Url::get('total_length'),
                                "PRODUCT_WIDTH" => (float)Url::get('total_width'),
                                "PRODUCT_HEIGHT" => (float)Url::get('total_height'),
                                "PRODUCT_TYPE" => "HH",
                                "ORDER_PAYMENT" => $order_payment,
                                "ORDER_SERVICE" => DB::escape(Url::get('ServiceId')),
                                "MONEY_COLLECTION" => $cod_amount, // Số tiền thu hộ không bao gồm tiền cước
                                "MONEY_TOTAL" => 0,
                            ];
                            $list_item = [];
                            $product_name = [];
                            $total_quantity = 0; $product_price = 0;
                            foreach (Url::get('mi_order_product') as $key => $value) {
                                $product_name_item = AdminOrdersDB::getProductNameById($value['product_id']);
                                $product_price_item = (int)str_replace(',', '', $value['total']);
                                $list_item[] = [
                                    'PRODUCT_NAME' => $product_name_item,
                                    'PRODUCT_WEIGHT' => ($value['weight']),
                                    'PRODUCT_QUANTITY' => $value['qty'],
                                    "PRODUCT_PRICE" => $product_price_item
                                ];
                                $product_name[] = $product_name_item;
                                $total_quantity += $value['qty'];
                                $product_price += $product_price_item;
                            }

                            $data['PRODUCT_NAME'] = implode("-", $product_name);
                            $data['PRODUCT_QUANTITY'] = $total_quantity;
                            $data['PRODUCT_PRICE'] = $product_price;
                            $data['ORDER_SERVICE_ADD'] = "";
                            if (!empty($list_item)) {
                                $data['LIST_ITEM'] = $list_item;
                            }

                            if (Url::get('pick_option') == "post") {
                                $data['ORDER_SERVICE_ADD'] = "GNG";
                            }

                            $note_code = DB::escape(Url::get('note_code'));
                            $text_note[] = DB::escape(Url::get("shipping_note"));
                            if ($note_code == 'CHOXEMHANGKHONGTHU') {
                                $text_note[] = "Cho xem hàng và không cho thử";
                            } elseif ($note_code == "CHOTHUHANG") {
                                $text_note[] = "Cho thử hàng";
                            } elseif ($note_code == "KHONGCHOXEMHANG") {
                                $text_note[] = "Không cho xem hàng";
                            }

                            $text_note = implode("|", $text_note);
                            $data['ORDER_NOTE'] = $text_note;
                            $data["ignore"] = [
                                "shipping_address_text" => '<div><b>'. $shipping_address['name'] .'</b></div> <div>'. $shipping_address['phone'] .'</div> <div>'. $shipping_address['address'] . ',' . $shipping_address['district_name'] . ',' . $shipping_address['district_name'] . '</div>',
                                'carrier_id' => 'api_viettel_post',
                                'shipping_status' => 1,
                                'note_code' => $note_code,
                                'total_weight' => $data['PRODUCT_WEIGHT'],
                                'total_width' => $data['PRODUCT_WIDTH'],
                                'total_height' => $data['PRODUCT_HEIGHT'],
                                'total_length' => $data['PRODUCT_LENGTH'],
                                'shipping_note' => $text_note,
                                'cod_amount' => $data['MONEY_COLLECTION'],
                                'shipping_address_id' => $shipping_address_id,
                                'is_cod' => DB::escape(Url::get('is_cod')),
                                'address_text' => DB::escape(Url::get('customer_name') . '- ' . $mobile .',' . trim(Url::get('address')) . ', ' . trim(Url::get('city_name'))),
                                'pick_option' => DB::escape(Url::get('pick_option')),
                                'is_freeship' =>DB::escape( Url::get('is_freeship')),
                                'shipping_option_id' => Url::get('shipping_option_id') ? DB::escape(Url::get('shipping_option_id')) : null,
                                "token" => $token,
                                'kho_viettel' => Url::get('kho_viettel'),
                                'cod_amount_origin' => $cod_amount_origin
                            ];
                        }
                    }
                }
            }
        }

        return $data;
    }

    function save_item(){
        $mobile = trim(addslashes(trim(Url::get('mobile'))));
        $mobile = str_replace(array('(',')','-'),'.',$mobile);

        $mobile2 = trim(addslashes(trim(Url::get('mobile2'))));
        $mobile2 = str_replace(array('(',')','-'),'.',$mobile2);
        $rows = array(
            'postal_code'=>DB::escape(trim(Url::get('postal_code')))
        ,'customer_name'=>DB::escape(trim(Url::get('customer_name')))
        ,'mobile'=>$mobile
        ,'mobile2'=>$mobile2
        ,'telco_code'=>DB::escape(trim(Url::get('telco_code')))
        ,'city'=>DB::escape(trim(Url::get('city_name')))
        ,'address'=>DB::escape(trim(Url::get('address')))
        ,'note1'=>DB::escape(trim(Url::get('note1')))
        ,'note2'=>DB::escape(trim(Url::get('note2')))
        ,'cancel_note'=>DB::escape(trim(Url::get('cancel_note')))
        ,'shipping_note'=>DB::escape(trim(Url::get('shipping_note')))
        ,'is_top_priority'=>Url::check('is_top_priority')?1:0
        ,'is_send_sms'=>Url::check('is_send_sms')?1:0
        ,'is_inner_city'=>Url::check('is_inner_city')?1:0
        ,'shipping_service_id'=>Url::get('shipping_service_id')?DB::escape(Url::get('shipping_service_id')):0
        ,'bundle_id'=>Url::get('bundle_id')?Url::get('bundle_id'):0
        ,'price'=>Url::get('price')?System::calculate_number(Url::get('price')):0
        ,'discount_price'=>Url::get('discount_price')?System::calculate_number(Url::get('discount_price')):0
        ,'shipping_price'=>Url::get('shipping_price')?System::calculate_number(Url::get('shipping_price')):0
        ,'other_price'=>Url::get('other_price')?System::calculate_number(Url::get('other_price')):0
        ,'total_price'=>Url::get('total_price')?System::calculate_number(Url::get('total_price')):0
        ,'weight'=>Url::get('weiget')?DB::escape(Url::get('weight')):0
        ,'type'=>Url::get('type')?DB::escape(Url::get('type')):1
        );
        if(AdminOrders::$quyen_marketing or AdminOrders::$quyen_admin_marketing){
            $rows += array(
                'fb_post_id'=>DB::escape(trim(Url::get('fb_post_id')))
                ,'fb_page_id'=>DB::escape(trim(Url::get('fb_page_id')))
                ,'fb_customer_id'=>Url::get('fb_customer_id')?DB::escape(trim(Url::get('fb_customer_id'))):'0'
            );
        }
        if(Session::get('admin_group') or User::is_admin() or Url::get('cmd')=='add'){
            $source_id = Url::get('source_id')?DB::escape(Url::get('source_id')):0;
            $source_name = '';
            if($source_id and $source=DB::fetch('select id,name from order_source where id='.$source_id)){
                $source_name = $source['name'];
            }
            $rows += array('source_id'=>$source_id,'source_name'=>$source_name);
        }
        if(Url::get('city_id')){
            $rows['city_id'] = DB::escape(Url::get('city_id'));
        }
        if(Url::get('district_id')){
            $rows['district_id'] = DB::escape(Url::get('district_id'));
        }
        if(Url::get('ward_id')){
            $rows['ward_id'] = DB::escape(Url::get('ward_id'));
        }

        $filter_fields = ['code','customer_name','mobile', 'telco_code', 'note2', 'city','address', 'postal_code', 'shipping_note', 'note1', 'fb_page_id', 'fb_post_id', 'fb_customer_id', 'source_name', 'email'];
        $rows = $this->filterData($rows, $filter_fields);

        return ($rows);
    }


    private function filterData($data, $fields){
        foreach ($fields as $field) {
            if(isset($data[$field])){
                $val =  $data[$field];
                $data[$field] = DataFilter::removeXSSinHtml($val);
            }
        }
        return $data;
    }
}
?>
