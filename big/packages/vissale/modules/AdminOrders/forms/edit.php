<?php
use GuzzleHttp\Client;
class EditAdminOrdersForm extends Form{
    protected $map;
    function __construct(){
        Form::Form('EditAdminOrdersForm');
        //$languages = DB::fetch_all('select id,name,icon_url from language where active=1');
        $this->add('mobile',new TextType(true,'Chưa nhập số điện thoại',0,225));
        //$this->link_css('assets/default/css/tabs/tabpane.css');
        //$this->link_js('assets/default/css/tabs/tabpane.js');
        $this->link_js('packages/core/includes/js/jquery/jquery.MultiFile.js');
        //$this->link_js('assets/standard/js/datetimepicker.js');
        //$this->link_css('assets/standard/css/datetimepicker.css');
        $this->link_js('assets/standard/js/autocomplete.js');
        $this->link_css('assets/standard/css/autocomplete/autocomplete.css?v=26072019');
        $this->link_css('packages/vissale/modules/AdminOrders/css/common.css?v=30062019');
        $this->link_js('gentelella-master/gentelella-master/vendors/jquery.inputmask/dist/inputmask/inputmask.js');
        $this->link_js('gentelella-master/gentelella-master/vendors/jquery.inputmask/dist/inputmask/inputmask.date.extensions.js');
        $this->link_js('gentelella-master/gentelella-master/vendors/jquery.inputmask/dist/inputmask/jquery.inputmask.js');
        //Portal::$extra_header .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
        $this->add('orders_products.product_id',new TextType(false,'lỗi nhập sản phẩm',0,225));
    }
    function updateOrderToVichat($id, $order) {
        $id = intval($id);
        $order = DB::escapeAssociativeArr($order);
        $order['id'] = $id;
        $baseUri = API_UPDATE_ORDER_PALBOX;
        $apiKey = API_KEY_PALBOX;
        $client = new Client();
        try {
            $response = $client->put(
                $baseUri,
                array(
                    'json' => $order,
                    'headers' => ['api-key' => $apiKey],
                    'allow_redirects' => false,
                    'timeout' => 5
                )
            );

        } catch (Exception $e) {

        }
    }
    public function checkAllowUpdateMobile(): int
    {
        $allow_update_mobile = 1;
        $order = AdminOrders::$item ?? null;
        if (empty($order)) {
            return $allow_update_mobile;
        }//end if

        $extra_order = AdminOrdersDB::get_order_extra($order['id']);
        $allow_update_mobile = $extra_order['allow_update_mobile'] ?? 1;

        return $allow_update_mobile;
    }

    function phoneValidator(array $rows, string $cmd, int $allow_update_mobile = 0)
    {
        $redirect_msg = 'Lưu thông tin đơn hàng thất bại. Bạn vui lòng kiểm tra lại số điện thoại đơn hàng.';
        if ($rows['mobile2'] && !validateTelephoneNumber($rows['mobile2'], $rows['mobile2_type'])) {
            return URL::js_redirect(true, $redirect_msg);
        }//end if  

        if ($cmd == 'edit' && !$allow_update_mobile) {
            return;
        }//end if

        if (!validateTelephoneNumber($rows['mobile'], $rows['mobile_type'])) {
            return URL::js_redirect(true, $redirect_msg);
        }//end if
    }

    function handleAllowUpdateMobile(string $cmd, array $rows, int $allow_update_mobile): int
    {
        if ($cmd == 'add') {
            return 0;
        }//end if

        if ($cmd != 'edit') {
            return $allow_update_mobile;
        }//end if

        $order = AdminOrders::$item;
        if (strval($order['mobile']) !== strval($rows['mobile'])) {
            return 0;
        }//end if
        
        return $allow_update_mobile;
    }

    function on_submit(){
        $submit_success = true;
        $this->map['created'] = '';
        $cmd = Url::get('cmd');

        if($this->check()){
            $publishEventOnInsertOrder = false;
            $publishEventOnUpdateOrder = false;

            if(Url::get('exit_order')){
                if($url = Url::get('refer_url')){
                    $id = false;
                    if($id = Url::iget('id')){
                        if(AdminOrdersDB::currentUserEdit($id) == Session::get('user_id')){
                            AdminOrdersDB::free_order($id);
                            $publishEventOnUpdateOrder = true;
                        }
                    }
                    AdminOrdersDB::close_edit_order($id);
                } else {
                    if($id = Url::iget('id')){
                        $updatedOrder = DB::update('orders',array('last_online_time'=>0,'last_edited_account_id'=>''),'id="'.$id.'"');
                        if ($updatedOrder) {
                            $publishEventOnUpdateOrder = true;
                        }

                        AdminOrdersDB::close_edit_order($id);
                    }else{
                        AdminOrdersDB::close_edit_order();
                    }
                }
            }else{
                $nhan_vien_phu_trach = 0;
                $rows = $this->save_item();

                $update_mobile_status = true;
                if (isAccountTestValidatePhone()) {
                    $allow_update_mobile = $this->checkAllowUpdateMobile();
                    $this->phoneValidator($rows, $cmd, $allow_update_mobile);
                    $update_mobile_status = $this->handleAllowUpdateMobile($cmd, $rows, $allow_update_mobile);
                }//end if
                unset($rows['mobile_type'], $rows['mobile2_type']);


                $assigned_log_str = '';
                if(!$this->is_error()){
                    $status_config = AdminOrdersConfig::config_shipping_status();
                    $user_id = get_user_id();
                    // Case: Khi curl hủy đơn bên nhà vận chuyển thành công nhưng thao tác mysql sau đó gặp vấn đề (treo máy chủ, deadlock ...)
                    // => dữ liệu trên hệ thống sẽ khác bên nhà vận chuyển => cần xử lý (Pending)

                    // mysqli_begin_transaction(DB::$db_connect_id);
                    try {
                        $sale_can_self_assigned = get_group_options('sale_can_self_assigned');
                        if(AdminOrders::$quyen_chia_don){
                            if($assigned_user_id = Url::iget('assigned_user_id') and $assigned_user=DB::fetch('select id,username from users where id='.$assigned_user_id,'username')){
                                $rows += array('user_assigned'=>$assigned_user_id,'assigned'=>date('Y-m-d H:i:s'));
                                $assigned_log_str = 'Chia đơn cho '.$assigned_user;
                            }else{
                                if(Url::get('cmd')=='add') {
                                    $rows += array('user_assigned' => 0);//$user_id
                                }
                            }
                        }else{
                            if($sale_can_self_assigned){
                                if(Url::iget('assigned_user_id')){
                                    $rows += array('user_assigned'=>AdminOrders::$user_id,'assigned'=>date('Y-m-d H:i:s'));
                                    $assigned_log_str = AdminOrders::$account_id.' tự chia đơn';
                                }else{
                                    if(Url::get('cmd')=='add'){
                                        $rows += array('user_assigned'=>0);
                                    }
                                }
                            }else{
                                if(Url::get('cmd')=='add'){
                                    $rows += array('user_assigned'=>0);
                                }
                            }
                        }
                        $status_id = Url::iget('status_id');
                        $old_status_id = Url::iget('old_status_id');

                        if(Url::get('checkout')){//trường hợp bán lẻ checkout thì chuyển về trạng thái thành công
                            $status_id = 5;//trạng thái thành công
                        }
                         $dataRequest = $_REQUEST;
                        if(Url::get('cmd')=='edit' and $item = AdminOrders::$item){
                            if(AdminOrdersDB::currentUserEdit($item['id']) != Session::get('user_id')){
                                URL::js_redirect(true, 'Bạn không được phép sửa đơn này.');
                            }
                            $this->map['created'] = date('Y-m-d',strtotime($item['created']));
                            // [#6964] Nếu level trạng thái đơn hàng hiện tại lớn hơn xác nhận chốt đơn và chưa xác
                            // nhận thì không được lưu đơn
                            // Bo sung: chuyển đơn về trạng thái Xác nhận chốt đơn hoặc những trạng thái <= level 2 thì mình được phép sửa
                            $statuses = $this->getOrderStatuses();
                            $toLevel = (int) $statuses[$status_id]['level'];
                            $currentLevel = (int) $statuses[$item['status_id']]['level'];
                            $confirmLevel = $statuses[XAC_NHAN] ? (int) $statuses[XAC_NHAN]['level'] : 0;
                            // if(($status_id == 0 || ($toLevel > $confirmLevel && $currentLevel > $confirmLevel)) && !$item['user_confirmed']){
                            //     URL::js_redirect(true, 'Đơn hàng chưa qua xác nhận chốt đơn.', ['cmd', 'id']);
                            // }

                            $id = $item['id'];
                            $this->addLogOrtherField($id, $item, $dataRequest);
                            
                            if (!validatePhoneNumber($item['mobile'])) {
                                $allow_update_mobile = 1;
                            }//end if

                            if ((isAccountTestValidatePhone() && !$allow_update_mobile) || !AdminOrdersDB::canEditPhoneNumber()) {
                                unset($rows['mobile']);
                            }
                            $insurance_value = Url::post('insurance_value')?System::calculate_number(Url::post('insurance_value')):'0'; // khai gia
                            if($this->canChangeOrderCreatedUser(false) and Url::get('user_created')){
                                // gán người tạo đơn
                                $user_id_ = DB::escape(Url::get('user_created'));

                                $rows += array('user_created'=>$user_id_);
                            }
                            $rows += array('modified'=>date('Y-m-d H:i:s'));

                            if(AdminOrders::$admin_group or !$item['status_id']){//5: trang thai thanh cong
                                if(!$item['user_confirmed'] and $status_id == XAC_NHAN and Url::get('old_status_id') != $status_id){//7: trang thai xac nhan
                                    $nhan_vien_phu_trach = get_user_id();
                                    //$rows += array('confirmed'=>date('Y-m-d H:i:s'),'user_confirmed'=>$user_id);
                                }
                            }
                            if($status_id == HUY && Url::get('old_status_id') != $status_id && $order_shipping = AdminOrdersDB::checkExistShippingAddress($id)){
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
                                            if(!AdminOrdersDB::update_orders_shipping($status_config, $order_shipping)){
                                                throw new Exception('api_ghn_update_orders_shipping');
                                            }

                                            if(!AdminOrdersDB::insert_order_shipping_status_history($id,$status_config, $user_id)){
                                                throw new Exception('api_ghn_insert_order_shipping_status_history');
                                            }
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
                                            if(!AdminOrdersDB::update_orders_shipping($status_config, $order_shipping)){
                                                throw new Exception('api_ghtk_update_orders_shipping');
                                            }

                                            if(!AdminOrdersDB::insert_order_shipping_status_history($id,$status_config, $user_id)){
                                                throw new Exception('api_ghtk_insert_order_shipping_status_history');
                                            }
                                        }
                                    }
                                }
                            }
                            $order_viettel = DB::fetch('SELECT id FROM orders_viettel WHERE order_id = '.$id);
                            if (empty($order_viettel) && Url::get('viettel_service')) {
                                DB::insert('orders_viettel', [
                                    'order_id' => $id,
                                    'viettel_service' => Url::get('viettel_service')

                                ]);
                            } elseif (!empty($order_viettel)) {
                                // System::debug(Url::get('viettel_service'));
                                DB::update('orders_viettel', [
                                    'viettel_service' =>DataFilter::removeXSSinHtml(Url::get('viettel_service'))
                                ], 'id='.$order_viettel['id']);
                            }

                            $order_ghtk = DB::fetch('SELECT id FROM orders_ghtk WHERE order_id = '.$id);
                            if (empty($order_ghtk) && Url::get('transport_ghtk')) {
                                DB::insert('orders_ghtk', [
                                    'order_id' => $id,
                                    'transport_ghtk' => DataFilter::removeXSSinHtml(Url::get('transport_ghtk'))
                                ]);
                            } elseif (!empty($order_ghtk)) {
                                // System::debug(Url::get('viettel_service'));
                                DB::update('orders_ghtk', [
                                    'transport_ghtk' =>DataFilter::removeXSSinHtml(Url::get('transport_ghtk'))
                                ], 'id='.$order_ghtk['id']);
                            }
                            // Cập nhật dịch vụ EMS
                            $orderEms = DB::fetch('SELECT id FROM orders_ems WHERE order_id = '.$id);
                            if (empty($orderEms) && Url::get('ems_service')) {
                                DB::insert('orders_ems', [
                                    'order_id' => $id,
                                    'service_id' => Url::get('ems_service')

                                ]);
                            } elseif (!empty($orderEms)) {
                                DB::update('orders_ems', [
                                    'service_id' =>Url::get('ems_service')
                                ], 'id='.$orderEms['id']);
                            }

                            $filter_fields = ['code','customer_name','mobile', 'telco_code', 'note2', 'city','address', 'postal_code', 'shipping_note', 'note1', 'fb_page_id', 'fb_post_id', 'fb_customer_id', 'source_name', 'email'];
                            $rows = $this->filterData($rows, $filter_fields);

                            $vichat_clone_rows = $rows;
                            $vichat_clone_rows['status_id'] = $status_id;


                            AdminOrdersDB::update_edited_log($id,$rows);
                            AdminOrdersDB::logConnect($id);
                            if(Url::iget('bundle_id') && $item['bundle_id'] == 0){
                                $$rows['bundle_id'] = Url::iget('bundle_id');

                            } else {
                                $rows['bundle_id'] = $item['bundle_id'];
                            }
                            $updatedOrder = DB::update_id('orders',$rows+array('last_online_time'=>0,'last_edited_account_id'=>''),$id);
                            $prepaidData = URL::get('prepaidData');
                            $prepaidLog = "Thanh toán trước:". ''.DB::escape(URL::get('prepaid')). "đ, còn nợ:" .DB::escape(URL::get('prepaid-remain')).'đ.<br>';
                            $prepaidLog .= $this->save_prepaid_data($id, $prepaidData);
                            AdminOrdersDB::update_revision($id,false,false,$prepaidLog);

                            if ($updatedOrder) {
                                $publishEventOnUpdateOrder = true;
                            }else{
                                throw new Exception('DB::update_id_orders_last_online_time');
                            }

                            if($status_id == CHUYEN_HANG){//8: chuyển hàng

                            }

                            $deliver_date = Url::get('deliver_date') != '' ? date('Y-m-d', strtotime(str_replace('/','-',Url::get('deliver_date')))) : 'NULL';
                            $saved_date = Url::get('saved_date') != '' ? date('Y-m-d', strtotime(str_replace('/','-',Url::get('saved_date')))) : 'NULL';

                            if(!DB::exists('select id from orders_extra where order_id='.$id)){
                                $phone_store_id = Session::get('phone_store_id') ? Session::get('phone_store_id') : '0';

                                $rows_extra = array(
                                    'order_id'=>$id,
                                    'group_id'=>AdminOrders::$group_id,
                                    'accounting_confirmed'=>AdminOrders::$date_init_value,
                                    'accounting_user_confirmed'=>'0',
                                    'update_successed_user'=>'0',
                                    'update_successed_time'=>AdminOrders::$date_init_value,
                                    'update_paid_user'=>'0',
                                    'update_paid_time'=>AdminOrders::$date_init_value,
                                    'update_returned_user'=>'0',
                                    'update_returned_time'=>AdminOrders::$date_init_value,
                                    'upsale_from_group_id'=>'0',
                                    'upsale_from_user_id'=>'0',
                                    'customer_group'=>Url::iget('customer_group') != '' ? Url::iget('customer_group') : 0,
                                    'insurance_value'=>$insurance_value,
                                    'phone_store_id'=>$phone_store_id,
                                    'deliver_date'=>$deliver_date,
                                    'saved_date'=>$saved_date,
                                    'order_not_success'=>Url::iget('order_not_success')  ? Url::iget('order_not_success') : 0,
                                    'allow_update_mobile' => $update_mobile_status

                                );
                                if(!DB::insert('orders_extra',$rows_extra)){
                                    throw new Exception('DB::insert_orders_extra');
                                }
                            } else {
                                $statusId = Url::iget('status_id');
                                $orderStatus = (int)$item['status_id'];
                                $itemStatus = $this->get_status($statusId);
                                $order_extra = AdminOrdersDB::get_order_extra($item['id']);
                                $order_not_success = 0;
                                if($itemStatus['id'] == DA_THU_TIEN || $itemStatus['id'] == THANH_CONG || (int)$itemStatus['level'] < 3){
                                    $order_not_success = 0;
                                } else if(Url::iget('order_not_success')){
                                     $order_not_success = Url::iget('order_not_success');
                                }
                                $rows_extra = array(
                                    'insurance_value'=>$insurance_value,
                                    'customer_group'=>Url::iget('customer_group') != '' ? Url::iget('customer_group') : 0,
                                    'order_not_success'=>$order_not_success,
                                );
                                if (Url::get('show_saved_date')) {
                                    $rows_extra += array(
                                        'deliver_date'=>$deliver_date,
                                        'saved_date'=>$saved_date,
                                    );
                                }
                                AdminOrdersDB::log_order_extra($id);
                                $this->logOrderNotSuccess($id,$order_extra,$order_not_success);
                                DB::update('orders_extra',$rows_extra,'order_id='.$id);
                            }
                            $old_status_id = intval($item['status_id']);
                        }else{// trường hợp thêm mới chỉ cho thêm với trạng thái xác nhận
                            if (!URL::get('bundle_id')) {
                                return URL::js_redirect(true, 'Bạn chưa chọn loại sản phẩm');
                            }//end if

                            $insurance_value = Url::post('insurance_value')?Url::post('insurance_value'):'0'; // khai gia


                            if($this->canChangeOrderCreatedUser(true)){
                                $user_id = Url::getUInt('user_created', get_user_id());
                            }
                            $rows += array(
                                'code'=>DB::escape(DataFilter::removeXSSinHtml(Url::get('code'))),
                                'user_created'=>$user_id,
                                'created'=>date('Y-m-d H:i:s'),
                                'group_id'=>AdminOrders::$group_id,
                                'status_id'=> CHUA_XAC_NHAN
                            );
                            if(AdminOrders::$master_group_id){
                                $rows += array(
                                    'master_group_id'=>AdminOrders::$master_group_id
                                );
                            }else{
                                $rows += array(
                                    'master_group_id'=>0
                                );
                            }
                            if (Url::get('cmd') == 'add') {
                                $customerName = Url::get('customer_name');
                                $customerPhone = Url::get('mobile');
                                if($customerPhone){
                                    $customerId = AdminOrdersDB::update_name_customer($customerPhone, $customerName);
                                    if($customerId){
                                        $rows += array(
                                            'customer_id'=>$customerId
                                        );
                                    }
                                }
                            }
                            $filter_fields = ['code','customer_name','mobile', 'telco_code', 'note2', 'city','address', 'postal_code', 'shipping_note', 'note1', 'fb_page_id', 'fb_post_id', 'fb_customer_id', 'source_name', 'email'];
                            $rows = $this->filterData($rows, $filter_fields);
                            $id = DB::insert('orders',$rows);

                            if ($id > 0) {
                                $publishEventOnInsertOrder = true;
                            }else{
                                throw new Exception('DB::insert_orders');
                            }
                            $prepaidData = URL::get('prepaidData');
                            $prepaidLog = "Thanh toán trước:". ''.DB::escape(URL::get('prepaid')). "đ, còn nợ:" .DB::escape(URL::get('prepaid-remain')).'đ.<br>';
                            $prepaidLog .= $this->save_prepaid_data($id, $prepaidData);
                            AdminOrdersDB::update_revision($id,false,false,$prepaidLog);

                            $this->addLogCreatedOrder($id);
                            AdminOrdersDB::logConnect($id);
                            $phone_store_id = Session::get('phone_store_id')?Session::get('phone_store_id'):'0';
                            $rows_extra = array(
                                'order_id'=>$id,
                                'group_id'=>AdminOrders::$group_id,
                                'accounting_confirmed'=>AdminOrders::$date_init_value,
                                'accounting_user_confirmed'=>'0',
                                'update_successed_user'=>'0',
                                'update_successed_time'=>AdminOrders::$date_init_value,
                                'update_paid_user'=>'0',
                                'update_paid_time'=>AdminOrders::$date_init_value,
                                'update_returned_user'=>'0',
                                'update_returned_time'=>AdminOrders::$date_init_value,
                                'upsale_from_group_id'=>'0',
                                'upsale_from_user_id'=>'0',
                                'customer_group'=>Url::iget('customer_group') ? Url::iget('customer_group') : 0,
                                'insurance_value'=>str_replace(',','',$insurance_value),
                                'phone_store_id'=>$phone_store_id,
                                // 'deliver_date'=> 'NULL',
                                // 'saved_date'=> 'NULL',
                                'order_not_success'=>Url::iget('order_not_success') ? Url::iget('order_not_success') : 0,
                                'allow_update_mobile'=> $update_mobile_status,

                            );
                            AdminOrdersDB::log_order_extra($id);
                            DB::insert('orders_extra',$rows_extra);
                            // Lưu dịch vụ Viettel Post
                            if (Url::get('viettel_service')) {
                                DB::insert('orders_viettel', [
                                    'order_id' => $id,
                                    'viettel_service' => DataFilter::removeXSSinHtml(Url::get('viettel_service'))
                                ]);
                            }

                            //AdminOrdersDB::update_revision($id,false,false, "Tạo đơn hàng");

                            // Hình thức vận chuyển GHTK
                            if (Url::get('transport_ghtk')) {
                                DB::insert('orders_ghtk', [
                                    'order_id' => $id,
                                    'transport_ghtk' =>DataFilter::removeXSSinHtml(Url::get('transport_ghtk'))
                                ]);
                            }

                            // Dịch vụ EMS
                            if (Url::get('ems_service')) {
                                DB::insert('orders_ems', [
                                    'order_id' => $id,
                                    'service_id' =>  DataFilter::removeXSSinHtml(Url::get('ems_service'))
                                ]);
                            }
                            // Khởi tạo trạng thái đơn hàng được đặt
                            DB::insert('order_shipping_status_history', [
                                'order_id' => $id,
                                'status' => $status_config['DA_DAT'],
                                'created_at' => date('Y-m-d H:i:s')
                            ]);
                            /*if($status_id == 8){//8: chuyển hàng
                                $this->update_deliver($id);//goi ham xu ly van chuyen
                            }*/
                        }// end them moi
                        $this->addLogSchedule($id, $dataRequest);
                        if (Url::get('cmd') == 'add') {
                            $data = [];
                            $this->addLogOrtherField($id, $data, $dataRequest);
                        }
                        if($assigned_log_str and $id){
                            AdminOrdersDB::update_revision($id,false,false,$assigned_log_str);
                        }
                        AdminOrdersDB::update_customer($id,$nhan_vien_phu_trach);
                        AdminOrdersDB::update_order_product($id, $this->map['created']);
                        AdminOrdersDB::update_upsale($id);
                        AdminOrdersDB::update_reminder($id);

                        if($this->canUpdateOrderStatus($old_status_id, $status_id)){

                            $errors = AdminOrdersDB::update_status($id,$old_status_id,$status_id);
                            $this->handleUpdateStatusErrors($errors);
                        }

                        if ($submit_success) {
                            // mysqli_commit(DB::$db_connect_id);
                            mysqli_close(DB::$db_connect_id);
                        }

                        if(Url::get('cmd')=='edit' and $item = AdminOrders::$item && !empty($item['fb_page_id']) && isset($updatedOrder) && isset($vichat_clone_rows)) {
                            $this->updateOrderToVichat($id, $vichat_clone_rows);
                        }
                        AdminOrdersDB::close_edit_order($id);
                    } catch (Exception $e) {
                        // mysqli_rollback(DB::$db_connect_id);
                        mysqli_close(DB::$db_connect_id);
                        Form::set_flash_message('admin_order_add_or_update_error', 'Lưu thông tin đơn hàng thất bại. Bạn vui lòng kiểm tra lại thông tin đơn hàng.');
                    }
                }
            }
        }
    }

    static function logOrderNotSuccess($id,$data,$order_not_success_request){
        $change = '';
        $orderNotSuccess = $data['order_not_success'];
        if($order_not_success_request){
            if($orderNotSuccess){
               $change .= 'Lý do đơn chưa thành công thay đổi tử : ' . AdminOrders::$orderNotSuccess[$orderNotSuccess] .' => '. AdminOrders::$orderNotSuccess[$order_not_success_request] .'<br>'; 
            } else{
                $change .= 'Lý do đơn chưa thành công thay đổi tử : Không chọn => ' . AdminOrders::$orderNotSuccess[$order_not_success_request] .'<br>';
            }
        } else {
            if($orderNotSuccess){
                $change .= 'Lý do đơn chưa thành công thay đổi tử : ' . AdminOrders::$orderNotSuccess[$orderNotSuccess] .' => Không chọn <br>';
            }
        }
        AdminOrdersDB::update_revision($id,false,false,$change);
    }
    static function addLogCreatedOrder($id)
    {
        $id = intval($id);
        $data = Url::post_many(['status_id', 'customer_name', 'mobile', 'other_price', 'discount_price', 'shipping_price', 'total_price','source_name']);
        $data = DB::escapeAssociativeArr($data);
        $status = DB::select('statuses','id='.$data['status_id']);
        $customerNameHis = '';
        $customerPhoneHis = '';
        if ($data['customer_name'] != '') {
            $customerNameHis = '. Tên khách hàng: '.$data['customer_name'];
        } else {
            $customerNameHis = '';
        }
        if ($data['mobile'] != '') {
            $customerPhoneHis = '. Sđt khách hàng: '.$data['mobile'];
        } else {
            $customerPhoneHis = '';
        }
        if(Url::get('user_created') && $users= DB::fetch('SELECT id,username FROM users WHERE id = '.Url::iget('user_created'))){
            $userAssignCreated = '. Gán người tạo đơn :<b>'.$users['username'].'</b>';
        }else{
            $userAssignCreated = '';
        }
        $statusName = '. Trạng thái đơn hàng: <b>'.$status['name'].'</b>';
        $userCreated = ' Người tạo đơn: <b>'.Session::get('user_id').'</b>';
        $timeCreated = '. Thời gian tạo đơn: <b>'.date('Y-m-d H:i:s').'</b>';
        $sourceName = $data['source_name']?'. Nguồn tạo đơn: <b>'.$data['source_name'].'</b>':'';
        $ortherPrice = $data['other_price'];
        $discount_price = $data['discount_price'];
        $shipping_price = $data['shipping_price'];
        if ($ortherPrice != '' && $ortherPrice > 0) {
            $ortherPrice ='</b>. Phụ thu: <b>'.$ortherPrice.'</b>';
        } else {
            $ortherPrice = '';
        }
        if ($discount_price != '' && $discount_price > 0) {
            $discount_price ='</b>. Giảm giá: <b>'.$discount_price.'</b>';
        }else{
            $discount_price = '';
        }
        if ($shipping_price != '' && $shipping_price > 0) {
            $shipping_price ='</b>. Phí vận chuyển: <b>'.$shipping_price.'</b>';
        }else{
            $shipping_price = '';
        }
        $totalPrice = '. Tổng tiền: <b>'.$data['total_price'].$ortherPrice.$discount_price.$shipping_price.'</b>';
        $data = '<b>Tạo mới đơn hàng.</b>'.$userCreated.$statusName.$customerNameHis.$customerPhoneHis.$timeCreated.$totalPrice.$sourceName.$userAssignCreated;
        AdminOrdersDB::update_revision($id,false,false,$data);
    }

    private function addLogSchedule($orderId, $data){
        if(isset($data['mi_reminder'])){
            foreach ($data['mi_reminder'] as $key => $value) {
                $value = DB::escapeAssociativeArr($value);
                $id = '';
                $change = '';
                if($value['id'] != ''){
                    $id = $value['id'];
                }
                if ($id && $schedule=DB::select('crm_customer_schedule','id='.$id)){
                    if($schedule['note'] != $value['note']){
                        $change .= 'Lịch hẹn thay đổi từ "'.$schedule['note'].'" => "'.$value['note'].'"'.'<br>';
                    }
                    if(!empty($schedule['appointed_time']) && !empty($value['appointed_time_display'])){
                        if(date('d/m/Y H:i',$schedule['appointed_time'])!=$value['appointed_time_display']){
                            $change .= 'Thời gian hẹn thay đổi từ "'.date('d/m/Y H:i',$schedule['appointed_time']).'" => "'.$value['appointed_time_display'].'"'.'<br>';
                        }
                    }
                    if($change){
                        $change.= '<br>';
                    }
                } else {
                    if (Url::get('cmd') == 'add') {
                        $change .= 'Thêm lịch hẹn "'. $value['note'] .'"'.'<br>';
                        $change .= 'Thêm thời gian hẹn "'. $value['appointed_time_display'] .'"'.'<br>';
                    }
                    if(Url::get('cmd') == 'edit'){
                        $change .= 'Thêm lịch hẹn "'. $value['note'] .'"'.'<br>';
                        $change .= 'Thêm thời gian hẹn "'. $value['appointed_time_display'] .'"'.'<br>';
                        if($change){
                            $change .= '<br>';
                        }
                    }
                }
                if($change){
                    AdminOrdersDB::update_revision($orderId,false,false,$change);
                }

            }
        }
    }

    private function addLogOrtherField($id, $item, $data){
        $change = '';
        $adminOrdersDB = new AdminOrdersDB();
        $source = AdminOrdersDB::get_source();
        if(empty($item)){
            $order = DB::fetch("SELECT * FROM orders WHERE id = ".DB::escape($id )." LIMIT 0,1");
        } else {
            $order = $item;
            $order_extra = AdminOrdersDB::get_order_extra($order['id']);
            if(!empty($order_extra)){
                $order += $order_extra;
            }
        }
        $typeList = AdminOrders::$type;
        $group_id = AdminOrders::$group_id;
        $master_group_id = AdminOrders::$master_group_id;
        $account_type = AdminOrders::$account_type;
        if($account_type==TONG_CONG_TY or $master_group_id){
            if($account_type!=TONG_CONG_TY){
                $group_id = $master_group_id;
            }
            $bundles = AdminOrdersDB::get_bundles($group_id);
        }else{
            $bundles = AdminOrdersDB::get_bundles();
        }
        $sourceNew = isset($data['source_id']) ? $data['source_id'] : 0;
        $bundleNew = isset($data['bundle_id']) ? $data['bundle_id'] : 0;
        $bundleOld = $order['bundle_id'] ? $order['bundle_id'] : 0;
        $sourceOld = $order['source_id'] ? $order['source_id'] : 0;
        if($bundleNew && $bundleNew != $bundleOld){
            if($bundleNew == 0 ){
                $newBundle = 'Không có phân loại';
            } else {
                $newBundle = $bundles[$bundleNew]['name'];
            }
            if($bundleOld == 0){
                $oldBundle = 'Không có phân loại';
            } else {
                $oldBundle = $bundles[$bundleOld]['name'];
            }
            $change .= 'Phân loại thay đổi từ "'.$oldBundle.'" => "'.$newBundle.'"'. '<br>';
        }

        $customerId = intval($order['customer_id']);
        $orderNotSuccess = $order['order_not_success'];
        $sqlCustomer = "SELECT id,name,email,gender,birth_date FROM crm_customer WHERE id = $customerId LIMIT 0,1";
        $queryCustomer = DB::fetch($sqlCustomer);
        $requestEmail = isset($data['email'])?$data['email'] : '';
        $requestGender = $data['gender'] != '' ? $data['gender'] : 0;
        $requestBirthDate = isset($data['birth_date'])?$data['birth_date'] : '';
        $requestBundle = isset($data['bundle_id'])?$data['bundle_id'] : '';
        $requestNoiThanh = isset($data['is_inner_city'])?1 : 0;
        $requestNoiBo = isset($data['is_send_sms'])?1 : 0;
        $requestKhuyenMai = isset($data['is_top_priority'])?1 : 0;
        $birth_date = '';
        $emsServiceConfig = AdminOrdersConfig::ems_services();
        $ghtk_config = AdminOrdersConfig::ghtk_config();
        $viettel_post_config = AdminOrdersConfig::viettel_post_config();
        $shipping_services = MiString::get_list(AdminOrdersDB::shipping_services());
        foreach ($viettel_post_config as $value) {
            $viettel_post_arr[$value['SERVICE_CODE']] = $value['SERVICE_NAME'];
        }
        foreach ($ghtk_config as $k => $value) {
            $ghtk_config_arr[$k] = $value;
        }
        foreach ($emsServiceConfig as $k => $value) {
            $emsServiceList[$k] = $value;
        }
        if(empty($queryCustomer['birth_date']) || $queryCustomer['birth_date'] == '0000-00-00'){
            $birth_date = '';
        } else {
            $birth_date = $queryCustomer['birth_date'];
        }
        $users = AdminOrdersDB::get_users(false,false,true);
        if (Url::get('cmd') == 'add') {
            if($sourceNew != 0){
                $change .= 'Nguồn đơn hàng : '.$source[$sourceNew]['name'].'<br>';
            }
            if(Url::get('order_not_success')){
                $change .= 'Thêm lý do đơn chưa thành công : ' . AdminOrders::$orderNotSuccess[Url::get('order_not_success')] .'<br>';
            }
            if(Url::get('fb_post_id')){
                $change .= 'Thêm FB post ID : ' . Url::get('fb_post_id') .'<br>';
            }
            if(Url::get('fb_page_id')){
                $change .= 'Thêm FB fanpage ID : ' . Url::get('fb_page_id') .'<br>';
            }
            if(Url::get('fb_customer_id')){
                $change .= 'Thêm Facebook ID của khách : ' . Url::get('fb_customer_id') .'<br>';
            }
            if(Url::get('upsale_from_user_id')){
                $change .= 'Thêm Upsale : ' . $users[$data['upsale_from_user_id']]['full_name'] .'<br>';
            }
            if(isset($data['shipping_service_id']) && $data['shipping_service_id'] != ''){
                $change .= 'Thêm giao hàng : ' . $shipping_services[$data['shipping_service_id']] .'<br>';
            }
            if(isset($data['address']) && $data['address'] != ''){
                $change .= 'Thêm địa chỉ : ' . $data['address'] .'<br>';
            }
            if(isset($data['city']) && $data['city'] != ''){
                $change .= 'Thêm tỉnh thành: ' . $data['city'] . '<br>';
            }
            if(isset($data['type']) && $data['type'] != 0){
                $change .= 'Thêm loại đơn : ' . $typeList[$data['type']] . '<br>';
            }
            if(isset($data['viettel_service']) && $data['viettel_service'] != ''){
                $change .= 'Thêm dịch vụ Viettel Post : ' . $viettel_post_arr[$data['viettel_service']] . '<br>';
            }
            if(isset($data['transport_ghtk']) && $data['transport_ghtk'] != ''){
                $change .= 'Thêm dịch vụ GHTK : ' . $ghtk_config_arr[$data['transport_ghtk']] . '<br>';
            }
            if(isset($data['ems_service']) && $data['ems_service'] != ''){
                $change .= 'Thêm dịch vụ EMS : ' . $emsServiceList[$data['ems_service']] . '<br> ';
            }
            if($bundleNew != ''){
                $change .= 'Thêm phân loại : ' . $bundles[$bundleNew]['name'].'<br>';
            }
            if($data['note1'] != ''){
                $change .= 'Thêm ghi chú chung : ' . $data['note1']. '<br>';
            }
            if($data['shipping_note'] != ''){
                $change .= 'Thêm ghi chú chuyển hàng : ' . $data['shipping_note']. '<br>';
            }
            if($data['note2'] != ''){
                $change .= 'Thêm ghi chú 2 : ' . $data['note2']. '<br>';
            }
            if($data['cancel_note'] != ''){
                $change .= 'Thêm ghi chú hủy : ' . $data['cancel_note'].'<br>';
            }
            if($requestEmail != ''){
                $change .= 'Thêm email : ' .$requestEmail . '<br>';
            }
            if($requestKhuyenMai == 1){
                $change .= 'Chọn ưu tiên : có' . '<br>';
            }
            if($requestNoiThanh == 1){
                $change .= 'Chọn nội thành : có' . '<br>';
            }
            if($requestNoiBo == 1){
                $change .= 'Đã SMS : có' . '. ';
            }
            if($requestBirthDate != ''){
                $change .= 'Thêm ngày sinh khách hàng : ' .$requestBirthDate.'<br>';
            }
            if($data['postal_code'] != ''){
                $change .= 'Thêm mã VĐ : ' .$data['postal_code'].'<br>';
            }
        } else {
            // var_dump(Url::get('order_not_success'),$orderNotSuccess);die;
            if(Url::get('upsale_from_user_id') && !empty($order['upsale_from_user_id']) && (Url::get('upsale_from_user_id') != $order['upsale_from_user_id'])){
                $change .= 'Thay đổi Upsale từ : ' . $users[$order['upsale_from_user_id']]['full_name'] . ' thành '. $users[$data['upsale_from_user_id']]['full_name'].'<br>';
            }
            

            if($sourceNew !=  $sourceOld){
                if($sourceOld == 0){
                    $oldSource = 'Không có nguồn';
                } else {
                    $oldSource = $source[$sourceOld]['name'];
                }
                if($sourceNew == 0){
                    $newSource = 'Không có nguồn';
                } else {
                    $newSource = $source[$sourceNew]['name'];
                }
                $change .= 'Nguồn thay đổi từ "'.$oldSource.'" => "'.$newSource.'"' .'<br>';
            }
            $id = DB::escape($id);
            if(isset($data['ems_service'])){
                $orderEms = DB::fetch('SELECT id, service_id FROM orders_ems WHERE order_id = '.$id);
                if($orderEms && $data['ems_service'] != $orderEms['service_id']){
                    if($data['ems_service'] != ''){
                        $change .= 'EMS thay đổi từ '.$emsServiceList[$orderEms['service_id']].' => '.$emsServiceList[$data['ems_service']] .'<br>';
                    } else {
                        $ems = 'Không chọn';
                        $change .= 'EMS thay đổi từ '.$emsServiceList[$orderEms['service_id']].' => '.$ems.'<br>';
                    }
                }
            }

            if(isset($data['transport_ghtk'])){
                $orderEms = DB::fetch('SELECT id, transport_ghtk FROM orders_ghtk WHERE order_id = '.$id);
                if($orderEms && $data['transport_ghtk'] != $orderEms['transport_ghtk']){
                    if($data['transport_ghtk'] != ''){
                        $change .= 'GHTK thay đổi từ '.$ghtk_config_arr[$orderEms['transport_ghtk']].' => '.$ghtk_config_arr[$data['transport_ghtk']].'<br>';
                    } else {
                        $ems = 'Không chọn';
                        $change .= 'GHTK thay đổi từ '.$ghtk_config_arr[$orderEms['transport_ghtk']].' => '.$ems.'<br>';
                    }
                }
            }

            if(isset($data['viettel_service'])){
                $orderViettel = DB::fetch('SELECT id, viettel_service FROM orders_viettel WHERE order_id = '.$id);
                if($orderViettel && $data['viettel_service'] != $orderViettel['viettel_service']){
                    if($data['viettel_service'] != ''){
                        $change .= 'Viettel Post thay đổi từ '.$viettel_post_arr[$orderViettel['viettel_service']].' => '.$viettel_post_arr[$data['viettel_service']].'<br>';
                    } else {
                        $ems = 'Không chọn';
                        $change .= 'Viettel Post thay đổi từ '.$viettel_post_arr[$orderViettel['viettel_service']].' => '.$ems.'<br>';
                    }
                }
            }
            if($requestBirthDate != ''){
                $newtime = date('Y-d-m',strtotime($requestBirthDate));
                $oldtime = $birth_date != '' ? strtotime($birth_date) : 0;
                if((strtotime($newtime) != $oldtime) && ($oldtime != 0)){
                    $change .= 'Ngày sinh thay đổi từ '.date('d/m/Y',strtotime($birth_date)).' => '.$requestBirthDate.'<br>';
                }
                if((strtotime($newtime) != $oldtime) && ($oldtime == 0)){
                    $change .= 'Thêm ngày sinh khách hàng : ' .$requestBirthDate.'<br>';
                }
            }

            if($requestNoiBo != $order['is_send_sms']){
                $new = $requestNoiBo == 1? 'Có' : 'Không';
                $old = $order['is_send_sms'] == 1? 'Có' : 'Không';
                $change .= 'Đã SMS thay đổi từ '.$old.' => '.$new.'<br>';
            }
            if($data['postal_code'] != $order['postal_code']){
                $change .= 'Mã vận chuyển thay đổi từ '.$order['postal_code'].' => '.$data['postal_code'].'<br>';
            }
            $shippingService = isset($data['shipping_service_id']) ? $data['shipping_service_id'] : 0;
            if($shippingService != $order['shipping_service_id']){
                if($data['shipping_service_id'] != 0){
                    $change .= 'Giao hàng thay đổi từ '.$shipping_services[$order['shipping_service_id']].' => '.$shipping_services[$data['shipping_service_id']].'<br>';
                } else {
                    $ems = 'Không chọn';
                    $change .= 'Giao hàng thay đổi từ '.$shipping_services[$order['shipping_service_id']].' => '.$ems.'<br>';
                }
            }
            $orderType = isset($order['type']) ? $order['type'] : 0;
            if($data['type'] !=  $orderType){
                if($data['type'] != 0 && $orderType == 0){
                    $change .= 'Thêm loại đơn '.$typeList[$data['type']].'<br>';
                } else if($data['type'] != 0 && $orderType != 0) {
                    $change .= 'Loại đơn thay đổi từ '.$typeList[$orderType].' => '.$typeList[$data['type']].'<br>';
                } else if($data['type'] == 0 && $orderType != 0) {
                    $ems = 'Không chọn';
                    $change .= 'Loại đơn thay đổi từ '.$typeList[$orderType].' => '.$ems.'<br>';
                }
            }
            if($requestNoiThanh != $order['is_inner_city']){
                $new = $requestNoiThanh == 1? 'Có' : 'Không';
                $old = $order['is_inner_city'] == 1? 'Có' : 'Không';
                $change .= 'Nội thành thay đổi từ '.$old.' => '.$new.'<br>';
            }
            if($requestKhuyenMai != $order['is_top_priority']){
                $new = $requestKhuyenMai == 1? 'Có' : 'Không';
                $old = $order['is_top_priority'] == 1? 'Có' : 'Không';
                $change .= 'Ưu tiên thay đổi từ '.$old.' => '.$new.'<br>';
            }

            if($requestEmail != '' && $queryCustomer['email'] == ''){
                $change .= 'Thêm email :' .$requestEmail.'<br>';
            } else if ($requestEmail != $queryCustomer['email']){
                $change .= 'Email thay đổi từ '.$queryCustomer['email'].' => '.$requestEmail.'<br>';
            }
        }
        if(!isset($queryCustomer['gender'])) $queryCustomer['gender'] = -1;
        if ($requestGender != $queryCustomer['gender']) {
            if($requestGender == 1){
                $sexNew = 'Nam';
            } else if ($requestGender == 2){
                $sexNew = 'Nữ';
            } else {
                $sexNew = 'Không xác định';
            }
            if($queryCustomer['gender'] == 1){
                $sexOld = 'Nam';
            } else if ($queryCustomer['gender'] == 2){
                $sexOld = 'Nữ';
            } else {
                $sexOld = 'Không xác định';
            }
            $change .= 'Giới tính thay đổi từ '.$sexOld.' => ' .$sexNew;
        }
        if ($change) {
            $adminOrdersDB->update_revision($id,false,false,$change);
        }

    }

    /**
     * Determines ability to update order status.
     *
     * @param      int   $oldStatus  The old status
     * @param      int   $newStatus  The new status
     *
     * @return     bool  True if able to update order status, False otherwise.
     */
    private function get_status($statusId){
        $statusId = (int)$statusId;
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
        return $items;
    }
    private function canUpdateOrderStatus(int $oldStatus, int $newStatus)
    {
        if($oldStatus === 0){
            return true;
        }

        if($newStatus != 0 && $oldStatus != $newStatus){
            return true;
        }
    }

    /**
     * Gets the order statuses.
     *
     * @return     <type>  The order statuses.
     */
    private function getOrderStatuses()
    {
        return AdminOrders::$admin_group
            ? AdminOrdersDB::get_status()
            : AdminOrdersDB::get_status_from_roles(get_user_id());
    }

    /**
     * { function_description }
     *
     * @param      array  $errors  The errors
     */
    private function handleUpdateStatusErrors(array $errors)
    {
        $messages = [];
        foreach ($errors as $errorCode) {
            switch($errorCode){
                case OrderStatusUpdater::UNCONFIRMED_ORDER:
                    $messages[] = 'Đơn hàng phải qua trạng thái Xác nhận chốt đơn mới được chuyển lên level cao hơn.';
                    break;

                case OrderStatusUpdater::UNKNOW_STATUS:
                    $messages[] = 'Trạng thái chuyển đến không hợp lệ.';
                    break;

                case OrderStatusUpdater::ORDER_ID_OR_GROUP_ID_INVALID:
                    $messages[] = 'ID đơn hàng hoặc ID shop không hợp lệ.';
                    break;

                case OrderStatusUpdater::USER_INVALID:
                    $messages[] = 'Thông tin người dùng không hợp lệ.';
                    break;

                case OrderStatusUpdater::ORDER_NOT_EXISTS:
                    $messages[] = 'Không tồn tại đơn hàng.';
                    break;

                case OrderStatusUpdater::EMPTY_STATUSES:
                    $messages[] = 'Danh sách trạng thái rỗng.';
                    break;

                case OrderStatusUpdater::NOT_ALLOWED_TO_UPDATE:
                    $messages[] = 'Bạn không có quyền hoặc đơn hàng không được phép cập nhật trạng thái.';
                    break;
            }
        }

        if($messages){
            URL::js_redirect(true, implode("\n", $messages), ['cmd', 'id']);
        }
    }

    function draw() {
        $group_id = AdminOrders::$group_id;
        $master_group_id = AdminOrders::$master_group_id;
        $account_type = $this->map['account_type'] = AdminOrders::$account_type;
        $this->map = array();
        $this->map['require_address'] = AdminOrders::$require_address;
        $shipping_address = AdminOrdersDB::getShippingAddress($group_id);
        $this->map['shipping_address'] = $shipping_address;
        $this->map['isAccountTestValidatePhone'] = isAccountTestValidatePhone();
        $shipping_info = '';
        $isObd = isObd();
        if($account_type==TONG_CONG_TY or $master_group_id){
            if($account_type!=TONG_CONG_TY){
                $group_id = $master_group_id;
            }
            $this->map['search_group_id_list'] = array(''=>'Tất cả cty') + MiString::get_list(AdminOrdersDB::get_groups($group_id));
        }//end if

        if ($isObd) {
            $bundles = AdminOrdersDB::getSystemBundles();
        } else {
            if($account_type==TONG_CONG_TY or $master_group_id){
                $bundles = AdminOrdersDB::get_bundles($group_id);
            } else {
                $bundles = AdminOrdersDB::get_bundles();
            }//end if
        }//end if

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

        $ghtk_config_arr = ['' => 'Chọn hình thức vận chuyển'];
        $ghtk_config = AdminOrdersConfig::ghtk_config();
        foreach ($ghtk_config as $k => $value) {
            $ghtk_config_arr[$k] = $value;
        }
        $this->map['transport_ghtk_list'] = $ghtk_config_arr;

        $emsServiceList = ['' => 'Chọn dịch vụ'];
        $emsServiceConfig = AdminOrdersConfig::ems_services();
        foreach ($emsServiceConfig as $k => $value) {
            $emsServiceList[$k] = $value;
        }

        $this->map['ems_service_list'] = $emsServiceList;

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
        $this->map['created'] = '';
        $this->map['user_delivered'] = '';
        $this->map['user_delivered_name'] = '';
        $this->map['delivered'] = '';
        $this->map['stt_id'] = 0;
        $this->map['fb_customer_id'] = '';
        $this->map['quyen_chia_don'] = false;
        $edit_mode = false;
        $this->map['mobile'] = '';
        $this->map['mobile2'] = '';
        $this->map['allow_update_mobile'] = 1;

        if(Url::get('cmd')=='edit' and Url::iget('id') and $order =  AdminOrders::$item){
            $this->map['mobile'] = $order['mobile'];
            $this->map['mobile2'] = $order['mobile2'];
    
            $prepaid = DB::fetch_all("SELECT * FROM  order_prepaid WHERE order_id=".$order['id']);

            $this->map['prepaidData'] = json_encode(array_values($prepaid), true);
            if($order['customer_id'] && ($customer = $this->getCustomerByID($order['customer_id']))){
                $customer['birth_date'] = is_datetime($customer['birth_date'])?Date_Time::to_common_date($customer['birth_date']):'';
            }else{
                $customer = ['email'=>'','birth_date'=>'','gender'=>''];
            }
            $this->map['created'] = date('Y-m-d',strtotime($order['created']));
            $order += $customer;
            $edit_mode = true;
            if(!isset($_REQUEST['mi_reminder'])){
                $_REQUEST['mi_reminder'] = AdminOrdersDB::get_reminder(Url::iget('id')?Url::iget('id'):false);

            }
            ///
            $statuses = $this->getOrderStatuses();
            $currentLevel = (int) $statuses[$order['status_id']]['level'];
            $this->map['currentLevel'] = $currentLevel;
            $this->map['currentStatus'] = $order['status_id'];
            $order_extra = AdminOrdersDB::get_order_extra($order['id']);
            $allow_update_mobile = $order_extra['allow_update_mobile'] ?? true;
            if (!validatePhoneNumber($order['mobile'])) {
                $allow_update_mobile = true;
            }//end if

            $this->map['allow_update_mobile'] = $allow_update_mobile;

            $this->map['user_delivered'] = $order['user_delivered'] ?? '';
            if(!empty($order_extra)){
                $order_extra['insurance_value'] = number_format($order_extra['insurance_value']);
                $order += $order_extra;

                if(!empty($order['source_shop_id'])){
                    $shop_id = (int) $order['source_shop_id'];
                    $shops = DB::fetch("SELECT id,name FROM groups WHERE id = $shop_id");
                    $this->map['source_shop_id'] = $shops ? $shops['name'] : '';
                }
                if(!empty($order['source_created_user'])){
                    $source_created_user_id = (int) $order['source_created_user'];
                    $user = DB::fetch("SELECT id,username FROM users WHERE id = $source_created_user_id");
                    $this->map['source_created_user'] = $user ? $user['username'] : '';
                }

                if(!empty($order['source_upsale'])){
                    $source_upsale_id = (int) $order['source_upsale'];
                    $user = DB::fetch("SELECT id,username FROM users WHERE id = $source_upsale_id");
                    $this->map['source_upsale'] = $user ?  $user['username'] : '';
                }
            }
            $users_can_edit_order = get_group_options('users_can_edit_order');
            if($users_can_edit_order == 1 and $order['user_assigned'] and !AdminOrders::$admin_group and !AdminOrders::$quyen_sale and !AdminOrders::$quyen_ke_toan and !AdminOrders::$quyen_admin_ke_toan){
                echo '<div class="container"><br>';
                echo '<div class="text-danger">Đơn hàng mã #'.Url::iget('id').' đã được gán.  Marketing (hoặc người tạo đơn) không có quyền sửa.</div><hr><button onclick="window.location=\'index062019.php?page=admin_orders&\'" class="btn btn-success">Quay lại</button>';

                echo '</div>';
                die;
            }
            if(!AdminOrders::$admin_group and !AdminOrders::$quyen_admin_ke_toan and !AdminOrdersDB::can_edit_by_status($order['status_id'])){
                echo '<div class="container"><br>';
                 echo '<div class="text-danger">Đơn hàng mã #'.Url::iget('id').' bạn không có quyền sửa.</div><hr><button onclick="window.location=\'index062019.php?page=admin_orders&\'" class="btn btn-success">Quay lại</button>';

                echo '</div>';
                die;
            }
            if($acc=AdminOrdersDB::is_edited(Url::iget('id'))){

                if(!is_group_owner() && !Session::get('admin_group')){
                    echo '<div class="container"><br>';
                    echo '<div class="text-danger">Đơn hàng mã #'.Url::iget('id').' đang được thao tác bởi "'.$acc.'"</div><hr><button onclick="window.location=\'index062019.php?page=admin_orders&\'" class="btn btn-success">Quay lại</button>';

                    echo '</div>';
                    die;
                }
                $log = "Tài khoản: ".Session::get('user_id')." đã mở đơn hàng ra xem";
                AdminOrdersDB::update_revision(Url::iget('id'),false,false,$log);

            }else{
                if(AdminOrdersDB::is_clicked(Url::iget('id'))==false){
                    AdminOrdersDB::update_revision(Url::iget('id'),false,false,'Mở đơn hàng');

                }
                DB::update('orders',array('last_online_time'=>time(),'last_edited_account_id'=>Session::get('user_id')),'id="'.Url::iget('id').'"');

            }

            if ($order_shipping = AdminOrdersDB::getOrderShippingByOrderId($order['id'])) {
                $except_array = ['id', 'shipping_note'];
                foreach ($order_shipping as $key => $value) {
                    if (!in_array($key, $except_array)) {
                        $_REQUEST[$key] = $value;
                    }
                }
                $shipping_address_text = $order_shipping['shipping_address_text'];
                $shipping_info = $order_shipping;
                /*if ($order_shipping['shipping_status'] != 1) {
                    $accept_edit_transport = 'refuse';
                }*/
                // ĐÃ chuyển sang vận chuyển, sẽ không cho sửa đơn vận chuyển nữa
                $accept_edit_transport = 'refuse';
            }

            if ($order_viettel = DB::fetch("SELECT id, viettel_service FROM orders_viettel WHERE order_id = " . $order['id'])) {
                $_REQUEST['viettel_service'] = $order_viettel['viettel_service'];
            }
            if ($order_ghtk = DB::fetch("SELECT id, transport_ghtk FROM orders_ghtk WHERE order_id = " . $order['id'])) {
                $_REQUEST['transport_ghtk'] = $order_ghtk['transport_ghtk'];
            }
            if ($orderEms = DB::fetch("SELECT id, service_id FROM orders_ems WHERE order_id = " . $order['id'])) {
                $_REQUEST['ems_service'] = $orderEms['service_id'];
            }
            ///
            $_REQUEST['saved_date'] = $order['saved_date'] ? date("d/m/Y", strtotime($order['saved_date'])) : '';
            $_REQUEST['deliver_date'] = $order['deliver_date'] ? date("d/m/Y", strtotime($order['deliver_date'])) : '';
            $this->map['confirmed'] = date('H:i\' d/m/Y',strtotime($order['confirmed']));
            $this->map['user_confirmed'] = $order['user_confirmed'];
            $this->map['user_confirmed_name'] = $order['user_confirmed']?DB::fetch('select id,concat(name," - ",username) as name from users where id='.$order['user_confirmed'].' and users.group_id='.AdminOrders::$group_id,'name'):'';
            $this->map['user_delivered'] = $order['user_delivered'];
            $this->map['delivered'] = date('H:i\' d/m/Y',strtotime($order['delivered']));
            $this->map['user_delivered_name'] = $order['user_delivered']?DB::fetch('select id,concat(name," - ",username) as name from users where id='.$order['user_delivered'].' and users.group_id='.AdminOrders::$group_id,'name'):'';
            $this->map['source_id'] = $order['source_id'];
            $this->map['assigned_user_name'] = $order['user_assigned']?DB::fetch('select concat(name," - ",username) as name from users where id='.$order['user_assigned'].' and users.group_id='.AdminOrders::$group_id,'name'):' ... ';
            $this->map['created_user_name'] = $order['user_created']?DB::fetch('
				select 
					'.((AdminOrders::$account_type==3 or AdminOrders::$master_group_id)?'concat(users.name,"/",concat(users.username,"<br>cty ",groups.name)) as name':'concat(users.name,"/",users.username) as name').'
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
            if($prepaid = AdminOrdersDB::getTotalPrepaid($order['id'])){
                $total_prepaid = $prepaid['total_prepaid'];
                $this->map['prepaid'] = System::display_number($total_prepaid);
                $this->map['prepaid_remain'] = System::display_number(intval($order['total_price']) - $total_prepaid);
            }else{
                $this->map['prepaid'] = 0;
                $this->map['prepaid_remain'] = System::display_number(intval($order['total_price']));
            }
            $this->map['categories'] = AdminOrdersDB::get_categories($order['id']);
            $order['price'] = System::display_number($order['price']);
            $order['discount_price'] = System::display_number($order['discount_price']);
            $order['shipping_price'] = System::display_number($order['shipping_price']);
            $order['other_price'] = System::display_number($order['other_price']);
            $order['total_price'] = System::display_number($order['total_price']);

            if($status_cr = DB::select_id('statuses',$order['status_id'])){
                $statuses_custom = DB::select('statuses_custom','status_id='.$order['status_id']);
                $this->map['stt_id'] = $status_cr['id'];
                $this->map['status_level'] = $statuses_custom ? $statuses_custom['level'] : $status_cr['level'];
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
            }
            if(!isset($_REQUEST['mi_order_product'])){
                $_REQUEST['mi_order_product'] = AdminOrdersDB::get_order_product($order['id'],false);
            }
            /////
            if(!isset($_REQUEST['mi_payment'])){
            }
            /////
            if($order['fb_customer_id']){
                $this->map['fb_customer_id'] = $order['fb_customer_id'];
            }
        }else{// truong hop them moi
            if(!Url::get('user_created')){
                $_REQUEST['user_created'] = get_user_id();
            }
            $this->map['prepaidData'] = "[]";
            $this->map['prepaid'] = "0";
            $this->map['prepaid_remain'] = "0";
        }
        /////
        $warehouses = AdminOrdersDB::get_warehouses();
        $warehouse_id_options = '<option value="0">Chọn kho xuất</option>';
        $orderNotSuccess = '';
        foreach (AdminOrders::$orderNotSuccess as $key => $value) {
            $orderNotSuccess .= '<option value="'.$key.'">'.$value.'</option>';
        }
        $this->map['order_not_success_list'] = AdminOrders::$orderNotSuccess;
        foreach($warehouses as $value){
            if ($value['id'] == 1) {
                $warehouse_id_options .= '<option value="'.$value['id'].'" selected>'.$value['name'].'</option>';
            } else {
                $warehouse_id_options .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
            }
        }
        $this->map['warehouse_id_options'] = $warehouse_id_options;
        $this->map['default_warehouse_id'] = get_default_warehouse();
        /////
        $shipping_options = [];
        $shipping_option_viettel = [];
        if (empty(get_group_options('integrate_shipping'))) {
            $accept_edit_transport = 'not-intergrate-shipping';
        } else {
            $shipping_options = AdminOrdersDB::getShippingOptionsActive();
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
        if(Url::iget('id')){
            $this->map['order_revisions'] = AdminOrdersDB::get_order_revisions();
        }else{
            $this->map['order_revisions'] = array();
        }
        //////
        if(AdminOrders::$admin_group){
            $status_arr = AdminOrdersDB::get_status();
        }else{
            $status_arr = AdminOrdersDB::get_status_from_roles($this->map['user_id']);
        }

        $status_arr_custom = [];
        $statusIdsLv3 = [];
        foreach ($status_arr as $value) {
            $level = !empty($value['level']) ? $value['level'] : 0;
            $status_arr_custom[$level][] = $value;
            if($value['level'] >= 3 && $value['id'] != CHUYEN_HANG){
                $statusIdsLv3[] = $value['id'];
            }
        }

        $this->map['status_arr_custom'] = $status_arr_custom;
        $this->map['statusIdsLv3'] = $statusIdsLv3;

        if(!Url::get('status_id')){
            $_REQUEST['status_id'] = 10; //mặc định trạng thái đơn hàng là chưa xác nhận
        }

        
        if(!Url::get('source_id')){
            $_REQUEST['source_id'] = 3; //mặc định tạo tay
        }
        $arr = MiString::get_list($status_arr);
        if(array_key_exists(Url::iget('status_id'),$arr)){
            if(Url::get('status_id')!=THANH_CONG){
                $this->map['can_edit_status'] = true;
            }
        }
        if(AdminOrders::$admin_group){
            $this->map['can_edit_status'] = true;
        }
        /// edited by khoand at 12:00 04/09/2019
        $this->map['assigned_user_id_list'] = [];
        $sale_can_self_assigned = get_group_options('sale_can_self_assigned');
        $this->map['sale_can_self_assigned'] = $sale_can_self_assigned;
        if($sale_can_self_assigned and AdminOrders::$quyen_sale){
            $this->map['quyen_chia_don'] = true;
            $this->map['assigned_user_id_list'] = [""=>'Chọn',get_user_id()=>'Tài khoản của tôi'];
            if(!empty(AdminOrders::$item)){
                if(AdminOrders::$item['user_created'] != AdminOrders::$user_id){
                    $this->map['quyen_chia_don'] = false;
                }
            }
        }
        $this->map['quyen_chia_don'] = AdminOrders::$quyen_chia_don?AdminOrders::$quyen_chia_don:$this->map['quyen_chia_don'];
        if(AdminOrders::$quyen_chia_don){
            $this->map['assigned_user_id_list'] = array(""=>'Chọn nhân viên')+MiString::get_list(AdminOrdersDB::get_users('GANDON',false,true),'full_name');
        }
        //////
        $this->map += array(
            'shipping_service_id_list'=>array(""=>'--Chọn--')+MiString::get_list(AdminOrdersDB::shipping_services()),
            'category_options'=>$category_options,
            'status_list'=> $status_arr,
            'bundle_id_list'=>array("0"=>"--Chọn--")+MiString::get_list($bundles),
        );
        $zones = AdminOrdersDB::get_zones();
        $this->map['zones'] = $zones;
        $this->map['city_id_list'] = ['0'=>'Chọn tỉnh thành'] + MiString::get_list($zones);
        if ($isObd) {
            $this->map['sources'] = AdminOrdersDB::getSystemSources(Url::iget('source_id') ?? 0);
        } else {
            $this->map['sources'] = AdminOrdersDB::get_source();
        }//end if
        $this->map['source_id_list'] = MiString::get_list($this->map['sources']);
        $this->map['type_list'] = AdminOrders::$type;
        $this->map['mobiletype_list'] = AdminOrders::$mobile_types;
        $this->map['mobiletype2_list'] = AdminOrders::$mobile_types;


        if(Url::get('cmd')=='edit'){
            $title = 'Sửa ';
        }else{
            $title = 'Thêm mới ';
        }
        $title .= 'đơn hàng';
        //if($business_model)
        {
            $layout = 'edit';
            $this->map['payment_method_options'] = '<option value="1">Tiền mặt</option><option value="3">Thẻ</option><option value="2">Chuyển khoản</option>';
        }
        $customerGroup = AdminOrdersDB::getListCustomerGroup();
        $this->map['title'] = $title;
        $users = AdminOrdersDB::get_users(false,false,true);
        $this->map['user_created_list'] = array(""=>'Chọn người tạo')+MiString::get_list($users,'full_name');
        $this->map['upsale_from_user_id_list'] = array(""=>'Chọn nhân viên')+MiString::get_list($users,'full_name');
        $this->map['disable_negative_export'] = get_group_options('disable_negative_export');
        $this->map['no_create_order_when_duplicated'] = AdminOrders::$no_create_order_when_duplicated;
        $this->map['customer_group_list'] = array(''=>'Nhóm khách hàng')+MiString::get_list($customerGroup);
        $this->map['gender_list'] = array('0' => 'Chưa xác định', '1' => 'Nam', '2' => 'Nữ');

        $this->map['can_change_created_user'] = $this->canChangeOrderCreatedUser(Url::get('cmd') === 'add');

        $this->parse_layout($layout,$this->map);
    }

    /**
     * Kiểm tra liệu người dùng hiện tại có được phép thay đổi người tạo đơn hàng ở màn sửa + thêm đơn ?
     *
     * @param      bool  $isCreatingOrder  Indicates if creating order
     *
     * @return     bool  True if able to change order created user, False otherwise.
     */
    private function canChangeOrderCreatedUser(bool $isCreatingOrder = false)
    {
        $roles = getUserRoles();

        // ĐƯợc thay đổi nếu  nếu là admin shop, owner hoặc có quyền ADMIN_MARKETING
        if(AdminOrders::$admin_group || AdminOrders::$is_owner || in_array('ADMIN_MARKETING', $roles)){
            return true;
        }

        // ĐƯợc sửa nếu là tạo đơn và có 2 quyền Quản lý chăm sóc và quan ly KH
        $rolesNotExists = array_diff(['ADMIN_CS', 'CUSTOMER'], $roles);
        if($isCreatingOrder && count($rolesNotExists) != count(['ADMIN_CS', 'CUSTOMER'])){
            return true;
        }

        // ĐƯợc sửa nếu là tạo đơn và quyền GANDON và bật tính năng  sale được gán người tạo
        return $isCreatingOrder && get_group_options('sale_can_assigned_created_user') && in_array('GANDON', $roles);
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
        $id = intval($id);
        $group_id = AdminOrders::$group_id;
        $params_create_order = $this->get_params_create_order();
        $result_deliver = true;
        $is_freeship = Url::get('is_freeship');
        $addDeliverOrder = get_group_options('add_deliver_order');
		if (empty($addDeliverOrder)) {
			$addDeliverOrder = 1;
		}
        if (!empty($params_create_order)) {
            $carrier_id = Url::get('shipping_carrier_id');

            $status_config = AdminOrdersConfig::config_shipping_status();
            $ghtk_config = AdminOrdersConfig::ghtk_config();
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
                    'shipping_option_id' => Url::get('shipping_option_id') ? Url::get('shipping_option_id') : null
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

                        if ($addDeliverOrder == 2) {
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
                    'shipping_option_id' => Url::get('shipping_option_id') ? Url::get('shipping_option_id') : null
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

                        if ($addDeliverOrder == 2) {
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
                        /// khoand added at 18:02 19/06/2019
                        if(DB::exists('select id from orders_extra where order_id='.$id)){
                            DB::update('orders_extra', [
                                'sort_code' => $response_api['SortCode'],
                                'group_id'=>AdminOrders::$group_id,
                            ], 'order_id='.$id.'');
                        }
                        ///
                        // System::debug($order_shipping['id']); die();
                    } else {
                        $result_deliver = false;
                    }

                    // System::debug($response_api); die();
                }
            } else if ($carrier_id == 'api_bdhn') {
                $ma_xac_thuc = "";
                if (Url::get('shipping_option_id')) {
                    $shipping_option = AdminOrdersDB::getShippingOptionById(Url::iget('shipping_option_id'));
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

                        if ($addDeliverOrder == 2) {
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
                        $to_district_id = Url::iget('district_id');
                        $ghn_token = $config['token'];
                        /*if (!empty(get_group_options('ghn_token'))) {
                            $ghn_token = get_group_options('ghn_token');
                        }*/
                        if (Url::get('shipping_option_id')) {
                            $shipping_option = AdminOrdersDB::getShippingOptionById(Url::iget('shipping_option_id'));
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
                            'shipping_address_id' => DB::escape($shipping_address_id),
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
                            'ShippingAddress' => trim(Url::get('address')) . ', ' . trim(Url::get('city_name')),
                            'NoteCode' => Url::get('note_code'),
                            'ServiceID' => (int)Url::get('ServiceId'),
                            'Weight' => (float)Url::get('total_weight'),
                            'Length' =>(float) Url::get('total_length'),
                            'Width' => (float)Url::get('total_width'),
                            'Height' => (float)Url::get('total_height'),
                            'Note' => Url::get('shipping_note'),
                            'ReturnContactName' => trim(Url::get('customer_name')),
                            'ReturnContactPhone' => $mobile,
                            'ReturnAddress' => trim(Url::get('address')) . ', ' . trim(Url::get('city_name')),
                            'ReturnDistrictID' => (int)$to_district_id,
                            'ExternalReturnCode' => '',
                            'AffiliateID' => $config['AffiliateID'],
                            'is_cod' => DB::escape(Url::get('is_cod')),
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
                            'name' => DB::escape(Url::get('customer_name')),
                            'address' => DB::escape(Url::get('address')),
                            'province' => DB::escape(Url::get('city_name')),
                            'district' => DB::escape(Url::get('district_name')),
                            'pick_money' => $cod_amount,
                            'cod_amount' => $cod_amount,
                            'note' => DB::escape(Url::get('shipping_note')),
                            'value' => ($total_price - $shipping_price),
                            'Weight' => (float)Url::get('total_weight'),
                            'Length' =>(float) Url::get('total_length'),
                            'Width' => (float)Url::get('total_width'),
                            'Height' => (float)Url::get('total_height'),
                            'NoteCode' => DB::escape(Url::get('note_code')),
                            'is_cod' => DB::escape(Url::get('is_cod')),
                            'address_text' => DB::escape(Url::get('customer_name') . '- ' . $mobile .',' . trim(Url::get('address')) . ', ' . trim(Url::get('city_name'))),
                            'pick_option' => DB::escape(Url::get('pick_option')),
                            'is_freeship' => DB::escape(Url::get('is_freeship')),
                            'cod_amount_origin' => $cod_amount_origin,
                            'transport' => !empty(Url::get('transport_ghtk')) ? DB::escape(Url::get('transport_ghtk')): 'road'
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
                            'pick_option' => DB::escape(Url::get('pick_option')),
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
                                "ORDER_NUMBER" => DB::escape(Url::get('id')),
                                "GROUPADDRESS_ID" => DB::escape(Url::get('kho_viettel')),
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
                                'is_freeship' => DB::escape(Url::get('is_freeship')),
                                'shipping_option_id' => Url::get('shipping_option_id') ? DB::escape(Url::get('shipping_option_id')) : null,
                                "token" => $token,
                                'kho_viettel' => DB::escape(Url::get('kho_viettel')),
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
        $mobile = DataFilter::removeXSSinHtml(Url::get('mobile'));
        $mobile = str_replace(array('(',')','-'),'.',$mobile);

        $mobile2 = DataFilter::removeXSSinHtml(Url::get('mobile2'));
        $mobile2 = str_replace(array('(',')','-'),'.',$mobile2);
        $rows = array(
            'postal_code'=>DataFilter::removeXSSinHtml(trim(Url::get('postal_code')))
        ,'customer_name'=>DataFilter::removeXSSinHtml(trim(Url::get('customer_name')))
        ,'mobile'=>$mobile
        ,'mobile2'=>$mobile2
        ,'mobile_type'=>DataFilter::removeXSSinHtml(Url::get('mobiletype'))
        ,'mobile2_type'=>DataFilter::removeXSSinHtml(Url::get('mobiletype2'))
        ,'telco_code'=>trim(DataFilter::removeXSSinHtml(Url::get('telco_code')))
        ,'city'=>trim(DataFilter::removeXSSinHtml(Url::get('city_name')))
        ,'address'=>trim(DataFilter::removeXSSinHtml(Url::get('address')))
        ,'note1'=>trim(DataFilter::removeXSSinHtml(Url::get('note1')))
        ,'cancel_note'=>trim(DataFilter::removeXSSinHtml(Url::get('cancel_note')))
        ,'shipping_note'=>trim(DataFilter::removeXSSinHtml(Url::get('shipping_note')))
        ,'is_top_priority'=>Url::check('is_top_priority')?1:0
        ,'is_send_sms'=>Url::check('is_send_sms')?1:0
        ,'is_inner_city'=>Url::check('is_inner_city')?1:0
        ,'shipping_service_id'=>Url::get('shipping_service_id')?DataFilter::removeXSSinHtml(Url::get('shipping_service_id')):0
        ,'bundle_id'=>Url::get('bundle_id')?DataFilter::removeXSSinHtml(Url::get('bundle_id')):0
        ,'price'=>Url::get('price')?System::calculate_number(Url::get('price')):0
        ,'discount_price'=>Url::get('discount_price')?System::calculate_number(Url::get('discount_price')):0
        ,'shipping_price'=>Url::get('shipping_price')?System::calculate_number(Url::get('shipping_price')):0
        ,'other_price'=>Url::get('other_price')?System::calculate_number(Url::get('other_price')):0
        ,'total_price'=>Url::get('total_price')?System::calculate_number(Url::get('total_price')):0
        ,'weight'=>Url::get('weiget')?DataFilter::removeXSSinHtml(Url::get('weight')):0
        ,'type'=>Url::get('type')?DataFilter::removeXSSinHtml(Url::get('type')):0
        );

        $note2 = DataFilter::removeXSSinHtml(URL::getString('note2'));
        $_note2 = json_decode($note2, true);
        if(!is_array($_note2) || AdminOrders::validateMedidoc($_note2)){
            $rows['note2'] = $note2;
        }

        if(AdminOrders::$quyen_marketing or AdminOrders::$quyen_admin_marketing){
            $rows += array(
                'fb_post_id'=>trim(DataFilter::removeXSSinHtml(Url::get('fb_post_id')))
                ,'fb_page_id'=>trim(DataFilter::removeXSSinHtml(Url::get('fb_page_id')))
                ,'fb_customer_id'=>Url::get('fb_customer_id')?trim(DataFilter::removeXSSinHtml(Url::get('fb_customer_id'))):'0'
            );
        }
        if(AdminOrders::$admin_group or User::is_admin() or Url::get('cmd')=='add'){
            $source_id = Url::iget('source_id');
            $source_name = '';
            if($source_id and $source=DB::fetch('select id,name from order_source where id='.$source_id)){

                $source_name = $source['name'];
            }
            $rows += array('source_id'=>$source_id,'source_name'=>$source_name);

        }
        if(Url::get('city_id')){
            $rows['city_id'] = Url::iget('city_id');

        }
        if(Url::get('district_id')){
            $rows['district_id'] = Url::iget('district_id');

        }
        if(Url::get('ward_id')){
            $rows['ward_id'] = Url::iget('ward_id');

        }
        return ($rows);
    }

    /**
     * Gets the customer by id.
     *
     * @param      int     $customerID  The customer id
     *
     * @return     <type>  The customer by id.
     */
    private function getCustomerByID(int $customerID)
    {
        return DB::fetch('select email,birth_date,gender from crm_customer where id='.$customerID);

    }

    private function save_prepaid_data($order_id, $prepaid_data){
        $order_id = DB::escape($order_id);
        $jsonArr = json_decode($prepaid_data, true);
        // $jsonArr = DB::escapeAssociativeArr($jsonArr);

        $message = "";
        $rsql = "";
        $payTypeArr = [1 => 'ATM', 2 => 'Tiền mặt', 3 => "Chuyển khoản"];
        if($order_id && is_array($jsonArr)){
            $sql = "SELECT * FROM order_prepaid WHERE order_id = ". $order_id;
            $oldPrepaid = array_values(DB::fetch_all($sql));
            $oldIds = count($oldPrepaid)? array_column(($oldPrepaid), 'id') : [];
            $newIds = array_column($jsonArr, 'id');

            $deletedIds = array_diff( $oldIds, $newIds);

            // xoá các thanh toán trước không nằm trong dữ liệu mới
            if($deletedIds && count($deletedIds)) {
                $deletedIds = DB::escapeArray($deletedIds);
                $oldsql = 'SELECT * FROM order_prepaid WHERE id IN('.implode(', ', $deletedIds).');';
                $oldrows = DB::fetch_all($oldsql);
                $rsql = 'DELETE FROM order_prepaid WHERE id IN('.implode(', ', $deletedIds).');';
                foreach($oldrows as $row){
                    $message .= "Xoá thanh toán trước. (".$row['pay_amount']." - ".$payTypeArr[$row['pay_type']].")<br>";
                }
                DB::query($rsql);
            }
            if(count($jsonArr)){

                foreach($jsonArr as $newData){
                    if($newData['id'] == "" || $newData['id'] === 0 || $newData['id'] == null){
                        $rsql = "INSERT INTO order_prepaid (order_id, pay_type, pay_amount, note) VALUES (".$order_id.", ". DataFilter::removeXSSinHtml($newData['pay_type']) .", ". DataFilter::removeXSSinHtml($newData['pay_amount']) .", '". DataFilter::removeXSSinHtml($newData['note']) ."'); ";

                        DB::query($rsql);
                        $message .= "Thêm mới thanh toán trước: ".$newData['pay_amount']."đ. Hình thức: ".$payTypeArr[$newData['pay_type']]."<br>";
                    }else{


                        $key = array_search($newData['id'], array_column($oldPrepaid, 'id'));
                        $oldRow = $oldPrepaid[$key];
                        if($oldRow['pay_type'] != $newData['pay_type'] || $oldRow['pay_amount'] != $newData['pay_amount'] || $oldRow['note'] != $newData['note'] ){
                            $rsql = "UPDATE order_prepaid SET pay_type = ".
                                 DataFilter::removeXSSinHtml($newData['pay_type']) . ", pay_amount = ".
                                DataFilter::removeXSSinHtml($newData['pay_amount']) .", note='".
                                DataFilter::removeXSSinHtml($newData['note']) ."'  WHERE order_id=".$order_id." AND id =".$newData['id']."; ".PHP_EOL;

                            DB::query($rsql);
                            $message .= "Sửa thanh toán trước: từ ".$oldRow['pay_amount']."đ - ".$payTypeArr[$oldRow['pay_type']]." sang ".$newData['pay_amount']."đ - ".$payTypeArr[$newData['pay_type']]."<br>";
                        }

                    }
                }
            }
        }


        return $message;

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
