<?php
class ListAdminOrdersForm extends Form{
    public static $user_id;
    public static $quyen_xuat_kho;
    public static $quyen_marketing;
    public static $quyen_admin_marketing;
    public static $quyen_bung_don;
    public static $quyen_cskh;
    public static $quyen_xuat_excel;
    public static $quyen_in_don;
    public static $quyen_ke_toan;
    public static $quyen_admin_ke_toan;
    public static $keyMediadoc;
    public static $isObd;
    protected $map;
    protected $subfix_layout;
    function __construct(){
        Form::Form('ListAdminOrdersForm');
        $this->subfix_layout = '';
        if(Session::get('device') == 'MOBILE'){
            //$this->subfix_layout = '_mobile';
        }
        $this->link_css('assets/lib/DataTables/datatables.min.css');
        $this->link_js('assets/lib/DataTables/datatables.min.js');
        $this->link_css('packages/vissale/modules/AdminOrders/css/common'.$this->subfix_layout.'.css?v=01072020');
        $this->link_js('assets/standard/js/multiple.select.js');
        $this->link_css('assets/standard/css/multiple-select.css');
        require_once "packages/vissale/modules/PrintTemplates/config.php";
        require_once "packages/vissale/lib/php/simple_html_dom.php";
        ListAdminOrdersForm::$user_id = get_user_id();
        ListAdminOrdersForm::$keyMediadoc = AdminOrdersDB::encode(['provider' => 2, 'group_id' => AdminOrders::$group_id]);
        ListAdminOrdersForm::$quyen_xuat_kho = check_user_privilege('XUATKHO');
        ListAdminOrdersForm::$quyen_marketing = check_user_privilege('MARKETING');
        ListAdminOrdersForm::$quyen_admin_marketing = check_user_privilege('ADMIN_MARKETING');
        ListAdminOrdersForm::$quyen_bung_don = check_user_privilege('BUNGDON') || check_user_privilege('VAN_DON');
        ListAdminOrdersForm::$quyen_cskh = check_user_privilege('CSKH');
        ListAdminOrdersForm::$quyen_xuat_excel = check_user_privilege('XUAT_EXCEL',false,false,true);
        ListAdminOrdersForm::$quyen_in_don = check_user_privilege('IN_DON',false,false,true);
        ListAdminOrdersForm::$quyen_ke_toan = check_user_privilege('KE_TOAN');
        ListAdminOrdersForm::$quyen_admin_ke_toan = check_user_privilege('ADMIN_KETOAN');
        // form needs responsive
        //Portal::$extra_header .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
        if((Url::get('act')=='del_order')){//edited by khoand in 03/12/2018
            if($order_ids = Url::get('checked_order')){
                AdminOrdersDB::delete_order($order_ids);
            }
        }


        require_once 'packages/vissale/lib/php/log.php';
        self::$isObd = isObd();
    }
    function on_submit(){
        switch(Url::get('cmd')){
            case 'assign_bundle':
                return $this->assignBundle();

            case 'assign_customer_group':
                return $this->assignCustomerGroup();

            case 'delete':
                $this->delete();
                break;
            default:
                if(Url::get('autoAssignOrder')){// chia nhanh
                    if($all_ass_account_id = Url::get('all_ass_account_id')){// added by khoand in 05/10/2018
                        //echo $account_ids = '"'.join($all_ass_account_id,'","').'"';
                        $new_account_ids = array();
                        foreach($all_ass_account_id as $value){
                            $user = DB::fetch('select id,name from users where username="'.DB::escape($value).'"');
                            $new_account_ids[$user['id']]['user_id'] = $user['id'];
                            $new_account_ids[$user['id']]['id'] = $value;
                            $new_account_ids[$user['id']]['name'] = $value;
                        }
                        $options = [
                            'acc_group_id'=>false,
                            'items'=>[],
                            'account_ids'=>$new_account_ids,
                            'source_id'=>Url::get('ass_source_id'),
                            'bundle_id'=>Url::get('ass_bundle_id'),
                            'assign_option'=>Url::get('assign_option'),
                            'groupsId'=>Url::get('ass_groups_id'),
                            'limit'=>Url::iget('assigned_total')
                        ];
                        if($total = AdminOrdersDB::autoAssignOrder($options)){
                            Url::js_redirect(true,'Gán đơn tự động '.$total.' đơn thành công');
                        }else{
                            Url::js_redirect(true,'Không có tài khoản hoặc đơn hàng nào được chọn để chia đơn');
                        }
                    }else{
                        $account_group_id=Url::iget('assigned_account_group_id')?Url::iget('assigned_account_group_id'):false;
                        $options = [
                            'acc_group_id'=>$account_group_id,
                            'source_id'=>DB::escape(Url::get('ass_source_id')),
                            'bundle_id'=>DB::escape(Url::get('ass_bundle_id')),
                            'assign_option'=>DB::escape(Url::get('assign_option')),
                            'groupsId'=>DB::escape(Url::get('ass_groups_id')),
                            'limit'=>DB::escape(Url::iget('assigned_total'))
                        ];
                        if($total = AdminOrdersDB::autoAssignOrder($options)){
                            Url::js_redirect(true,'Gán đơn tự động '.$total.' đơn thành công');
                        }else{
                            Url::js_redirect(true,'Không có tài khoản hoặc đơn hàng nào được chọn để chia đơn');
                        }
                    }
                }
                // da thay bang drop and drag, nhưng giữ lại để update tính năng này trên mobile
                if(AdminOrders::$admin_group and Url::get('updateOderColumns') and $orders = Url::get('orders_column') and $order_orders = Url::get('order_orders_column')){
                    $show_columns = '';
                    foreach($orders as $key=>$value){
                        $orders[$key] = $value.':'.$order_orders[$key];
                    }
                    $show_columns = implode(',',$orders);
                    if(DB::exists('select id from orders_column_custom where group_id='.AdminOrders::$account_id)){
                        DB::update('orders_column_custom',array('show_columns'=>$show_columns,'last_edited_time'=>time(),'last_edited_account_id'=>AdminOrders::$account_id),'group_id='.AdminOrders::$account_id);
                    }else{
                        DB::insert('orders_column_custom',array('show_columns'=>$show_columns,'last_edited_time'=>time()	,'last_edited_account_id'=>AdminOrders::$account_id,'group_id'=>AdminOrders::$account_id));
                    }
                    Url::js_redirect(true,'Bạn đã cập nhật thành công');
                }
                break;
        }
    }
    public function getLinkMediadoc()
    {
        $links = QRCODE_MEDIDOC_ENDPOINT.self::$keyMediadoc;
        return $links;
    }
    function draw(){
        if(!empty($_REQUEST['customer_id']) && !URL::iget('customer_id')){
            die('Bạn vui lòng nhập đúng định dạng mã đơn hàng.');
        }
        $isSettingMedidoc = AdminOrdersDB::isSettingMedidoc();
        $group_id = AdminOrders::$group_id;
        $master_group_id = AdminOrders::$master_group_id;
        $account_type = $this->map['account_type'] = AdminOrders::$account_type;
        $this->map['is_account_group_manager'] = is_account_group_manager($group_id);
        $this->map['phone_store_id'] = Session::get('phone_store_id');
        $this->map['time_to_refesh_order'] = (int) get_group_options('time_to_refesh_order');
        if (self::$isObd) {
            $sourceIdList = array('Tất cả các nguồn') + MiString::get_list(AdminOrdersDB::getSystemSources(),'name');
        } else {
            $sourceIdList = array('Tất cả các nguồn') + MiString::get_list(AdminOrdersDB::get_source(),'name');
        }//end if

        $this->map['source_id_list'] = $this->map['ass_source_id_list'] = $sourceIdList;
        $this->map['source_shop_id_list'] = array('Chọn tất cả')+MiString::get_list(AdminOrdersDB::get_source_shop(),'name');
        $this->map['quyen_sua_don_nhanh'] = AdminOrders::$quyen_sua_don_nhanh;
        $customerGroup = AdminOrdersDB::getListCustomerGroup();
        $this->map['home_network'] = '';
        foreach(AdminOrdersConfig::homeNetwork() as $key=>$val){
            $this->map['home_network'] .= '<option value="'.$key.'">'.$val.'</option>';
        }
        // MKT
        $mktUsers = AdminOrdersDB::get_user_all('MARKETING');
        [$mktAllFormat, $mktActiveFormat] = $this->formatUserList($mktUsers);

        $mktActive = ['-1'=>'=>Bỏ chia / không chia'] + MiString::get_list($mktActiveFormat);
        $mktAll = ['-1'=>'=>Bỏ chia / không chia'] + MiString::get_list($mktAllFormat);
        if(!empty($mktAll) and in_array(AdminOrders::$account_id,$mktAll)){
            AdminOrders::$account_privilege_code = 'MARKETING';
        }

        // ALL USERS
        $allUsers = AdminOrdersDB::get_user_all();
        [$allUsers, $activeUsers] = $this->formatUserList($allUsers);


        // SALE
        $saleUsers = AdminOrdersDB::get_user_all('GANDON');
        [$saleUsers, $activeSales] = $this->formatUserList($saleUsers);

        $activeSales = MiString::get_list($activeSales,($account_type==TONG_CONG_TY)?'full_name':'');
        $salesAll = MiString::get_list($saleUsers,($account_type==TONG_CONG_TY)?'full_name':'');
        if(AdminOrders::$quyen_chia_don or AdminOrders::$admin_group or ListAdminOrdersForm::$quyen_marketing or ListAdminOrdersForm::$quyen_admin_marketing){
            $activeSales = ['-1'=>'=>Bỏ chia / không chia'] + $activeSales;
            $salesAll = ['-1'=>'=>Bỏ chia / không chia'] + $salesAll;
        }

        if(AdminOrders::$admin_group or AdminOrders::$quyen_chia_don or AdminOrders::$quyen_bung_don){
            $restricted_sales = $activeSales;
        }else{
            $restricted_sales = MiString::get_list(AdminOrdersDB::get_users('GANDON',false,false,true),($account_type==TONG_CONG_TY)?'full_name':'');
        }

        $accept_edit_transport = 'accept';
        $integrate_shipping = get_group_options('integrate_shipping');
        if (empty($integrate_shipping)) {
            $accept_edit_transport = 'not-intergrate-shipping';
        }
        $this->map['accept_edit_transport'] = $accept_edit_transport;
        
        if(self::$isObd) {
            if($account_type==TONG_CONG_TY or $master_group_id){
                $this->map['search_group_id_list'] = array(''=>'Tất cả cty') + MiString::get_list(AdminOrdersDB::get_groups($group_id));
            }//end if
            $bundles = AdminOrdersDB::getSystemBundles();
        } else {
            if($account_type==TONG_CONG_TY or $master_group_id){
                if($account_type==TONG_CONG_TY){
                    $bundles = AdminOrdersDB::get_bundles($group_id);
                }else{
                    $bundles = AdminOrdersDB::get_bundles($master_group_id);
                }//end if
                $this->map['search_group_id_list'] = array(''=>'Tất cả cty') + MiString::get_list(AdminOrdersDB::get_groups($group_id));
            }else{
                $bundles = AdminOrdersDB::get_bundles();
            }//end if
        }//end if


        $this->map['total_not_assigned_order'] = 0;//AdminOrdersDB::get_total_not_assigned_orders();
        $party = AdminOrdersDB::get_order_info();
        $this->map['min_search_phone_number'] = get_group_options('min_search_phone_number');
        $this->map['min_search_phone_number'] = $this->map['min_search_phone_number']?$this->map['min_search_phone_number']:3;
        $this->map['prints'] = AdminOrdersDB::get_prints();

        $this->map['user_id'] = DB::fetch('select id from users where username="'.AdminOrders::$account_id.'"','id');
        $this->map['md5_user_id'] = md5('vs'.$this->map['user_id']);
        $group_name = '';
        if(Url::iget('group_id')){
            $group_name = ' của "'.DB::fetch('select name from `groups` where id='.Url::iget('group_id'),'name').'"';
        }

        $cond = '1=1';
        //Edited by Khoand: 05/05/2018: trường hợp ko tích thì không hiển thị danh sách đơn hàng
        $cond_ = AdminOrdersDB::get_condition();
        //echo '<br>';
        $string = ' and orders.group_id="'.$group_id.'"';
        //echo strcmp($cond_,$string);
        if($cond_==$string){
            $cond = '1=2';
        }

        $cond.=$cond_;
        if($cond == '1=1'){
            $cond = ' orders.group_id="'.$group_id.'"';
        }
        $this->get_just_edited_id();
        require_once 'packages/core/includes/utils/paging.php';
        require_once 'cache/config/product_status.php';
        $item_per_page = Url::get('item_per_page')?DB::escape(Url::get('item_per_page')) :15;
        $check_get_item = false;
        $total = 0;
        $total_amount = 0;
        $shippingPrice = 0;
        if(Url::get('load_ajax')==1 or Url::get('act')=='print') {

            // chỉ gọi hàm tính tổng khi không phải chuyển trang
            if(URL::getUInt('page_no') <= 1){
                $totalAll = AdminOrdersDB::getTotalOrderAndItem($cond);
            }

            $total = empty($totalAll) ? URL::getString('total', 0) : $totalAll['totalItem'];
            $total_amount = empty($totalAll) ? URL::getString('total_amount', 0) : $totalAll['totalAmount'];
            $shippingPrice = empty($totalAll) ? URL::getString('shipping_price', 0) : $totalAll['shippingPrice'];

            $check_get_item = true;
        }

        $paging = '';
        $status = Url::get('status_id')?Url::get('status_id'):Url::get('status');
        if(!Url::get('checked_order')){
            $paging_array = array(
                'page_id'=>Url::get('page_id'),
                'item_per_page'=>Url::get('item_per_page'),
                'group_id'=>Url::get('group_id'),'cmd'=>Url::get('cmd'),'search_account_id'=>Url::get('search_account_id'),
                'type'=>Url::get('type'),'category_id'=>Url::get('category_id'),
                'status'=>is_array($status)?implode(',',$status):$status,
                'term_sdt'=>Url::get('term_sdt'),
                'term_order_id'=>Url::get('term_order_id'),
                'term_ship_id'=>Url::get('term_ship_id'),
                'ngay_tao_from'=>Url::get('ngay_tao_from'),'ngay_tao_to'=>Url::get('ngay_tao_to'),
                'ngay_chia_from'=>Url::get('ngay_chia_from'),'ngay_chia_to'=>Url::get('ngay_chia_to'),
                'ngay_xn_from'=>Url::get('ngay_xn_from'),'ngay_xn_to'=>Url::get('ngay_xn_to'),
                'ngay_chuyen_kt_from'=>Url::get('ngay_chuyen_kt_from'),'ngay_chuyen_kt_to'=>Url::get('ngay_chuyen_kt_to'),
                'ngay_chuyen_from'=>Url::get('ngay_chuyen_from'),'ngay_chuyen_to'=>Url::get('ngay_chuyen_to'),'is_inner_city'=>Url::get('is_inner_city')
            );
            $paging = order_page_ajax($total,$item_per_page,$paging_array,7,'page_no','');
        }
        $this->map['page_no'] = page_no();
        $order_by = $this->get_order_by();
        $layout = 'list'.$this->subfix_layout;
        if(Url::get('cmd') == 'list_pos'){
            $layout='list_pos';
        }
        if(Url::get('load_ajax')==1){
            $layout = 'list_ajax';
        }

        $template = "";
        $bar_code_mediadoc = '';
        if((Url::get('act')=='print')){
            $paper_size = Url::post('paper_size');
            $print_types = prints_type();
            // $layout = 'print_trust';
            $layout = 'new_print';
            $type_print_id = $print_types['DON_HANG']['id'];
            $cond_print_system = [];
            $cond_print_system[] = "type = $type_print_id";
            $cond_print_system['paper_size'] = "AND paper_size = '$paper_size'";
            $cond_print = $cond_print_system;
            $cond_print[] = "AND group_id = $group_id";
            if($isSettingMedidoc){
                $bar_code_mediadoc = $this->getLinkMediadoc();
            }

            $template_obj = AdminOrdersDB::getPrintTemplate(implode(" ", $cond_print));
            if (!empty($template_obj)) {
                $template = $template_obj['data'];
            } else {
                $template_system_obj = AdminOrdersDB::getPrintTemplate(implode(" ", $cond_print_system));
                if (empty($template_system_obj)) {
                    unset($cond_print_system['paper_size']);
                    $template_system_obj = AdminOrdersDB::getPrintTemplate(implode(" ", $cond_print_system));
                }
                $template = $template_system_obj['data'];
            }

            // todo
            $sendersInfor = $this->getSendersInfo();
            AdminOrdersDB::logOrdersPrinted(array_keys($sendersInfor['senders_info']));
            $this->map['info_senders'] = $sendersInfor['senders_info'];
            $this->map['ordersShippingType'] = $sendersInfor['shipping_type'];
        }
        $this->map['template'] = $template;
        $this->map['bar_code_mediadoc'] = $bar_code_mediadoc;
        $items = array();
        if($check_get_item){
            $items = AdminOrdersDB::get_items($cond,$order_by,$item_per_page,false,true);
            if (count($items) > 0) {
                foreach ($items as $key => $value) {
                    $postal_bar_code = $value['postal_code'];
                    $postal_bar_code_qr = $value['postal_code'];
                    $postal_bar_code_sub = $postal_bar_code;
                    $postal_bar_code_sub_qr = $postal_bar_code_qr;
                    if (strrpos($postal_bar_code, '.') > 0) {
                        $postal_bar_code_arr = explode('.', $postal_bar_code);
                        $postal_bar_code_sub = $postal_bar_code_arr[count($postal_bar_code_arr) - 1];
                        $postal_bar_code_sub = '<div style="text-align: center; padding-top: 10px"><img src="assets/lib/php-barcode-master/barcode.php?text='.$postal_bar_code_sub.'"><br /> <center>'.$postal_bar_code_sub.'</center></div>';
                        $postal_bar_code_sub_qr = $postal_bar_code_arr[count($postal_bar_code_arr) - 1];
                        $postal_bar_code_sub_qr = '<div style="text-align: center; padding-top: 10px"><img src="generate-qr.php?text='.$postal_bar_code_sub_qr.'"><br /> <center>'.$postal_bar_code_sub_qr.'</center></div>';
                    }
                    $items[$key]['postal_bar_code_sub'] = $postal_bar_code_sub;
                    $items[$key]['postal_bar_code_sub_qr'] = $postal_bar_code_sub_qr;
                    $items[$key]['bar_code_mediadoc_qr'] = '';

                    $items[$key]['bar_code_qr'] = $value['bar_code'];
                    $items[$key]['bar_code_id_qr'] = $value['bar_code_id'];
                    $items[$key]['bar_code_large_qr'] = isset($value['bar_code_large']) ? $value['bar_code_large'] :'';
                    $items[$key]['postal_bar_code_qr'] = isset($value['postal_bar_code']) ? $value['postal_bar_code'] :'';
                    $items[$key]['postal_bar_code_large_qr'] = isset($value['postal_bar_code_large']) ? $value['postal_bar_code_large'] :'';
                    // $items[$key]['prepaid_remain'] = $value['prepaid_remain'];
                    // $items[$key]['prepaid'] = $value['prepaid'];
                }
            }
        }
        $item_per_page_list = array(''=>'Dòng hiển thị',5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,20=>20,50=>50,100=>100,200=>200,500=>500);//
//        $item_per_page_list = array(''=>'Dòng hiển thị',5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,20=>20,50=>50,100=>100);//
        if(AdminOrders::$admin_group){
            $status_arr = AdminOrdersDB::get_status();
        }else{
            $status_arr = AdminOrdersDB::get_status_from_roles($this->map['user_id']);
        }
        $status_arr_custom = [];
        if(is_array($status_arr)){
            foreach ($status_arr as $value) {
                $level = !empty($value['level']) ? $value['level'] : 0;
                $status_arr_custom[$level][] = $value;
            }
        }

        $this->map['status_arr_custom'] = $status_arr_custom;

        $status = MiString::get_list($status_arr);
        $this->map['device'] = Session::get('device');
        $groups = array();

        $this->map['page_id_list'] = array(''=>'Chọn page','0'=>'Tất cả các page') + MiString::get_list(AdminOrdersDB::get_friendpages('status=2'));
        /// Khởi tạo danh sách///
        $this->mapFilterDates();
        ////
        //$account_groups = $this->get_account_groups();
        if(get_account_id() && AdminOrders::$quyen_chia_don){
            $account_groups = AdminOrdersDB::get_account_group_new();
        } else {
            $account_groups = get_account_groups();
        }
        //$account_groups = AdminOrdersDB::get_account_group_new();
        if(!is_account_group_manager() && !is_account_group_department() && !AdminOrders::$admin_group && !is_group_owner() && !get_account_id()){
            if(AdminOrdersDB::checkUserOnlyRole('GANDON')){
                $account_groups = [];
            }
        }
        $account_request = [];
        $this->map['account_group_id_option'] = '';
        $account_request = Url::get('account_group_id');

        if(!empty($account_request)){
            foreach($account_groups as $key=>$val){
                if (in_array($key,$account_request)) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                $this->map['account_group_id_option'] .= '<option value="'.$key.'"'.$selected.'>'.$val['name'].'</option>';
            }
        } else {
            foreach($account_groups as $key=>$val){
                $selected = '';
                $this->map['account_group_id_option'] .= '<option value="'.$key.'"'.$selected.'>'.$val['name'].'</option>';
            }
        }
        //$account_groups = get_account_groups();
        if(get_account_id() && AdminOrders::$quyen_chia_don){
            $account_groups = AdminOrdersDB::getAllAccountGroup();
        }

        $this->map['account_group_is_empty'] = (sizeof($account_groups)>0)?false:true;
        //$this->map['account_group_id_list'] = array(''=>'Tất cả') + MiString::get_list($account_groups);
        $this->map['ass_account_group_id_list'] = array(''=>'Chọn nhóm tài ') + MiString::get_list($account_groups);
        $this->map['assigned_account_group_id_list'] = array(''=>'Tất cả') + MiString::get_list($account_groups);

        ////
        $columns = get_order_columns();
        $this->map['last_edited_info'] = '';
        if($column = DB::select('orders_column_custom','group_id='.$group_id) and $column['last_edited_account_id']){
            $this->map['last_edited_info'] = '<strong>'.$column['last_edited_account_id'].'</strong> sửa lúc '.date('H:i\' \n\g\à\y d/m/Y',$column['last_edited_time']);
        }
        if($account_type==TONG_CONG_TY){
            $store_phones = DB::fetch_all('select id,name from phone_store where group_id='.$group_id);
            $this->map['phone_store_id_list'] = [''=>'Tất cả kho số'] + MiString::get_list($store_phones);
        }

        $print_paper_sizes = prints_paper_sizes();
        $paper_size_default = AdminOrdersDB::getPaperSizeDefault($group_id);
        $this->map['paper_size_default'] = $paper_size_default;
        $paper_sizes = [];
        foreach ($print_paper_sizes as $key => $value) {
            if (in_array("DON_HANG", $value['types'])) {
                $paper_sizes[$key] = $value;
            }
        }
        $warehouse = DB::select_all('qlbh_warehouse','structure_id='.ID_ROOT.' OR group_id = '.$group_id.'','structure_id');
        $arrWarehouse = [];
        $arrWarehouse[] = [
            'id' => '',
            'name'=> 'Chọn kho',
            'email'=> ''
        ];
        $unsetKey = '';
        $keyWarehouse = '';
        $emailKey = '';

        foreach ($warehouse as $key => $value) {
            $arrWarehouse[$key] = [
                'id' => $value['id'],
                'name'=> $value['name'],
                'email'=> $value['email']
            ];
            if ($value['kho_tong_shop'] && $value['kho_tong_shop'] == 1) {
                $emailKey = $value['email'];
                $unsetKey = $key;
            }
            if ($value['id'] == 1) {
                 $keyWarehouse = $value['id'];
            }
        }
        if ($keyWarehouse) {
            $arrWarehouse[$keyWarehouse]['email'] = $emailKey;
        }
        if ($unsetKey) {
            unset($arrWarehouse[$unsetKey]);
        }
        // $this->map['arrReturnCarrier'] = $arrReturnCarrier;
        $this->map['arrWarehouse'] = $arrWarehouse;
        $this->map['paper_sizes'] = $paper_sizes;
        $this->map['type_list'] = AdminOrders::$type;
        $zones = AdminOrdersDB::get_zones();
        $this->map['zones'] = $zones;
        $this->map['city_id_list'] = ['0'=>'Chọn tỉnh thành'] + MiString::get_list($zones);
        $listWarehouse = MiString::get_list(DB::select_all('qlbh_warehouse','structure_id='.ID_ROOT.' OR group_id = '.$group_id.' and (kho_tong_shop IS null or kho_tong_shop = 0)','structure_id'));

        if(AdminOrdersDB::checkUserOnlyRole('MARKETING') && !is_account_group_manager() && !is_account_group_department() && !AdminOrders::$admin_group && !is_group_owner()){
            $statusMkt = AdminOrdersDB::get_excepted_status_ids_new('MARKETING');
            if(!$statusMkt){
                $items = [];
                $paging = '';
                $total = 0;
            }
        }
        if(AdminOrdersDB::checkUserOnlyRole('GANDON') && !is_account_group_manager() && !is_account_group_department() && !AdminOrders::$admin_group && !is_group_owner()){
            $statusMkt = AdminOrdersDB::get_excepted_status_ids_new('GANDON');
            if(!$statusMkt){
                $items = [];
                $paging = '';
                $total = 0;
            }
        }
        $this->map += array(
            'account_type'=>$account_type,
            'is_master_group'=>0,//
            'group_name'=>$group_name,
            'shipping_services'=>AdminOrdersDB::shipping_services(),
            'status'=>$status_arr,
            'items'=>$items,
            'paging'=>$paging,
            'group_id_list'=>array(''=>'Chọn GROUP') + MiString::get_list($groups),
            'category_id_list'=>array(""=>'Chọn phân loại')+MiString::get_list(AdminOrdersDB::get_category()),
            'status_id_list'=>array(""=>'Chọn trạng thái')+$status,
            'search_account_id_list'=>array(""=>'Tất cả NV')+MiString::get_list($activeUsers),
            'all_search_account_id_list'=>array(""=>'Tất cả NV')+MiString::get_list($allUsers),
            'mkt_account_id_list'=>array(""=>'Tất cả mkt')+$mktActive,
            'all_mkt_account_id_list'=>array(""=>'Tất cả mkt')+$mktAll,
            'upsale_account_id_list'=>[""=>'Tất cả UpSale']+MiString::get_list($activeUsers),
            'ass_upsale_id_list'=> [""=>'Tất cả UpSale', '-1' => 'Bỏ chia / Không chia']+MiString::get_list($activeUsers),
            'all_upsale_account_id_list'=>array(""=>'Tất cả UpSale')+MiString::get_list($allUsers),
            'ass_mkt_account_id_list'=>array(""=>'Tất cả mkt')+$mktActive,
            'assigned_account_id_list'=>array(""=>'Tất cả sale')+$activeSales,
            'all_assigned_account_id_list'=>array(""=>'Tất cả sale')+$salesAll,
            'confirmed_account_id_list'=>array(""=>'Tất cả sale')+$activeSales,
            'all_confirmed_account_id_list'=>array(""=>'Tất cả sale')+$salesAll,
            'ass_account_id_list'=>array(""=>'Chọn sale để gán đơn hàng')+$restricted_sales,
            'all_ass_account_id[]_list'=>array(""=>'Chọn sale để gán đơn hàng')+$restricted_sales,
            'bundle_id_list'=>array(""=>'Phân loại')+array("-1"=>'Đơn chưa phân loại')+MiString::get_list($bundles),
            'ass_bundle_id_list'=>array(""=>'Tất cả phân loại')+MiString::get_list($bundles),
            'ass_groups_id_list'=>array("0"=>'Tất cả nhóm tài khoản')+MiString::get_list($account_groups),
            'item_per_page_list'=>$item_per_page_list,
            'total'=>$total,
            'shipping_price'=>$shippingPrice,
            'total_amount'=>System::display_number($total_amount),
            'quyen_xuat_kho'=>ListAdminOrdersForm::$quyen_xuat_kho,
            'quyen_xuat_excel'=>ListAdminOrdersForm::$quyen_xuat_excel,
            'quyen_in_don'=>ListAdminOrdersForm::$quyen_in_don,
            'quyen_ke_toan'=>ListAdminOrdersForm::$quyen_ke_toan,
            'quyen_chia_don'=>AdminOrders::$quyen_chia_don,
            'quyen_marketing'=>ListAdminOrdersForm::$quyen_marketing,
            'quyen_admin_marketing'=>ListAdminOrdersForm::$quyen_admin_marketing,
            'quyen_admin_ke_toan'=>ListAdminOrdersForm::$quyen_admin_ke_toan,
            'warehouse_id_list' => array(''=>'Chọn kho để xuất')+MiString::get_list($warehouse),
            'warehouse_id_filter_list' => array(''=>'Chọn kho')+$listWarehouse,
            'has_show_history_order'=>AdminOrders::$has_show_history_order,
            'columns'=>$columns,
            'customer_group_id_list'=>array(""=>'Nhóm KH')+MiString::get_list($customerGroup),
            'all_columns'=>get_order_columns(true),
            'show_phone_number_excel_order' => AdminOrders::$show_phone_number_excel_order,
            'show_phone_number_print_order' => AdminOrders::$show_phone_number_print_order,
            'isOwner' => AdminOrders::$is_owner,
            'isAdmin' => AdminOrders::$admin_group,
            'bundles' => $bundles,
            'customer_groups' => $customerGroup
        );
        $this->parse_layout($layout,$party + $this->just_edited_id+$this->map);
    }


    /**
     * { function_description }
     */
    private function mapFilterDates()
    {
        $fromFieldsName = [
            'ngay_tao_from',
            'ngay_chia_from',
            'ngay_xn_from',
            'ngay_chuyen_from',
            'ngay_chuyen_kt_from',
            'ngay_thanh_cong_from',
            'ngay_thu_tien_from',
            'ngay_chuyen_hoan_from',
            'ngay_tra_hang_from',
        ];

        array_map(function($fieldName){
            $_REQUEST[$fieldName] = URL::getModifyDateTime($fieldName, 'd/m/Y', 'd/m/Y', '-3 day');
        }, $fromFieldsName);

        $toFieldsName = [
            'ngay_tao_to',
            'ngay_chia_to',
            'ngay_xn_to',
            'ngay_chuyen_to',
            'ngay_chuyen_kt_to',
            'ngay_thanh_cong_to',
            'ngay_thu_tien_to',
            'ngay_chuyen_hoan_to',
            'ngay_tra_hang_to',
        ];

        $currentDay = date('d/m/Y');
        array_map(function($fieldName) use($currentDay){
            $_REQUEST[$fieldName] = URL::getDateTimeFmt($fieldName, 'd/m/Y', 'd/m/Y', $currentDay);
        }, $toFieldsName);
    }


    function get_account_groups(){
        return $groups = DB::fetch_all('select id,name from account_group where group_id='.Session::get('group_id').' order by name');
    }
    private function getTransporters()
    {
        $data_request = array('shop_id' => AdminOrders::$group_id);
        $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/shop-setting/get', $data_request);
        $arrReturnCarrier = [];
        if(is_array($dataRes) && count($dataRes) > 0){
            $arrReturnCarrier[] = [
                'carrierKey' => '',
                'name'=> 'Chọn NVC'
            ];
            foreach($dataRes['data']['settings']['settings'] as $keyRes => $rowRes){
                if(!empty($rowRes['name'])) {
                    $arrReturnCarrier[] = array(
                        'carrierKey' => $keyRes,
                        'name' => $rowRes['name']
                    );
                }
            }
        }

        return $arrReturnCarrier;
    }

    private function getSendersInfo()
    {
        $paper_size = Url::post('paper_size');
        $data_request = array();
        $info_senders = array();
        $info_senders_data = array();
        $info_receiver_data = array();
        $ordersShippingType = array();

        $print_constants = prints_constant();
        $constants = [];
        foreach ($print_constants as $key => $value) {
            if (in_array('DON_HANG', $value['types'])) {
                $constants[$key] = $value;
            }
        }

        if ($paper_size !== 'A4-A5-NGANG-NEW') {
            $this->map['print_constants'] = $constants;
            $info_senders = AdminOrdersDB::getInfoSenderByOrderId();
            $this->map['info_senders'] = $info_senders;
        } else {
            $ids = Url::get('selected_ids');
            $data_request = array('order_arr' => $ids);
            $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/get', $data_request);
            if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                if (count($dataRes['data']) > 0) {
                    foreach ($dataRes['data'] as $orderDetail) {
                        $order_id = $orderDetail['order']['order_id'];
                        $info_senders[$order_id] = array();
                        if ($orderDetail['carrier']['alias'] === 'api_jt') {
                            $info_senders[$order_id]['bar_code'] = '<div style="text-align: center; padding-top: 10px"><img src="assets/lib/php-barcode-master/barcode.php?text='.$orderDetail['order']['billcode'].'"><br /> <center>'.$orderDetail['order']['billcode'].'</center></div>';
                            $info_senders[$order_id]['bar_code_qr'] = '<div style="text-align: center; padding-top: 10px"><img src="generate-qr.php?text='.$orderDetail['order']['billcode'].'"><br /> <center>'.$orderDetail['order']['billcode'].'</center></div>';
                            $info_senders[$order_id]['postal_bar_code'] = '';
                        } elseif ($orderDetail['carrier']['alias'] === 'api_ghn' || $orderDetail['carrier']['alias'] === 'api_ghn_v2') {
                            $info_senders[$order_id]['bar_code'] = '<div style="text-align: center; padding-top: 10px"><img src="assets/lib/php-barcode-master/barcode.php?text='.$orderDetail['order']['billcode'].'"><br /> <center>'.$orderDetail['order']['billcode'].'</center></div>';
                            $info_senders[$order_id]['bar_code_qr'] = '<div style="text-align: center; padding-top: 10px"><img src="generate-qr.php?text='.$orderDetail['order']['billcode'].'"><br /> <center>'.$orderDetail['order']['billcode'].'</center></div>';
                            $info_senders[$order_id]['postal_bar_code'] = '';
                        } elseif ($orderDetail['carrier']['alias'] === 'api_best') {
                            $info_senders[$order_id]['bar_code'] = '<div style="text-align: center; padding-top: 10px"><img src="assets/lib/php-barcode-master/barcode.php?text='.$orderDetail['order']['billcode'].'"><br /> <center>'.$orderDetail['order']['billcode'].'</center></div>';
                            $info_senders[$order_id]['postal_bar_code'] = '<div style="text-align: center; padding-top: 10px"><img src="assets/lib/php-barcode-master/barcode.php?text='.$orderDetail['order']['routecode'].'"><br /> <center>'.$orderDetail['order']['routecode'].'</center></div>';
                            $info_senders[$order_id]['bar_code_qr'] = '<div style="text-align: center; padding-top: 10px"><img src="generate-qr.php?text='.$orderDetail['order']['billcode'].'"><br /> <center>'.$orderDetail['order']['billcode'].'</center></div>';
                            $info_senders[$order_id]['postal_bar_code_qr'] = '<div style="text-align: center; padding-top: 10px"><img src="generate-qr.php?text='.$orderDetail['order']['routecode'].'"><br /> <center>'.$orderDetail['order']['routecode'].'</center></div>';
                        } else {
                            $info_senders[$order_id]['postal_bar_code'] = '';
                            $info_senders[$order_id]['postal_code'] = '';
                        }

                        $itemsvalue = ($orderDetail['order']['ordertype'] === 2) ? 0 : $orderDetail['detail']['itemsvalue'];
                        $inquiryFee = ($orderDetail['order']['paytype'] === 2) ? 0 : $orderDetail['detail']['inquiryFee'];
                        $info_senders[$order_id]['price_not_shipping'] = $itemsvalue;
                        $info_senders[$order_id]['total_price'] = number_format($itemsvalue + $inquiryFee);
                        if ($inquiryFee > 0)
                            $info_senders[$order_id]['shipping_price'] = number_format($inquiryFee);
                        else
                            $info_senders[$order_id]['shipping_price'] = 0;

                        $arrNote = array('CHOTHUHANG' => 'Cho thử hàng', 'CHOXEMHANGKHONGTHU' => 'Cho xem hàng không thử', 'KHONGCHOXEMHANG' => 'Không cho xem hàng');
                        $info_senders[$order_id]['shipping_note'] = (!isset($arrNote[$orderDetail['detail']['note']])) ? null : $arrNote[$orderDetail['detail']['note']];
                        $info_senders[$order_id]['delivery_time'] = (!isset($orderDetail['order']['delivery_time'])) ? null : date('d/m/Y H:i', strtotime($orderDetail['order']['delivery_time']));
                        $info_senders[$order_id]['sort_code'] = (!isset($orderDetail['order']['sort_code'])) ? null : "Sort code: " . $orderDetail['order']['sort_code'];
                        $info_senders[$order_id]['image_url'] = '/assets/standard/images/tuha_logo.png?v=03122021';
                        $info_senders_data[$order_id] = $orderDetail['detail']['sender'];
                        $info_receiver_data[$order_id] = $orderDetail['detail']['receiver'];
                        $ordersShippingType[$order_id] = $orderDetail['carrier']['alias'];
                    }
                }
            }
            ////////////////////////////////
            if (count($info_senders_data) > 0) {
                foreach ($info_senders_data as $order_id => $sender) {
                    $info_senders[$order_id]['name_sender'] = $sender['name'];
                    $info_senders[$order_id]['phone_sender'] = $sender['phone'];
                    $info_senders[$order_id]['address_sender'] = $sender['address'];
                }
            }
            if (count($info_receiver_data) > 0) {
                foreach ($info_receiver_data as $order_id => $receiver) {
                    $info_senders[$order_id]['name'] = $receiver['name'];
                    $info_senders[$order_id]['phone'] = $receiver['phone'];
                    $info_senders[$order_id]['address'] = $receiver['address'];
                }
            }
        }

        return ['senders_info' => $info_senders, 'shipping_type' => $ordersShippingType];
    }

    function get_just_edited_id(){
        $this->just_edited_id['just_edited_ids'] = array();
        if (UrL::get('selected_ids')){
            if(is_string(UrL::get('selected_ids'))){
                if (strstr(UrL::get('selected_ids'),',')){
                    $this->just_edited_id['just_edited_ids']=explode(',',UrL::get('selected_ids'));
                }
                else{
                    $this->just_edited_id['just_edited_ids']=array('0'=>UrL::get('selected_ids'));
                }
            }
        }
    }
    function get_order_by(){
        if(Url::get('order_by')){
            $order_by = 'orders.'.DB::escape(Url::get('order_by'));
            if(Url::get('order_by_dir')){
                $order_by .= ' '.DB::escape(Url::get('order_by_dir'));
            }
        }else{
//            $ob = (Url::get('c_n') or Url::get('customer_name'))?'orders.id DESC':'orders.id DESC';
            $ob = 'orders.id DESC';

            if(Url::get('ngay_tao_from') or Url::get('ngay_tao_to')){
                $ob = 'orders.id DESC';
            }
            if(Url::get('ngay_xn_from') or Url::get('ngay_xn_to')){
                $ob = 'orders.confirmed DESC';
            }
            if(Url::get('ngay_chuyen_from') or Url::get('ngay_chuyen_to')){
                $ob = 'orders.delivered DESC';
            }
            //$default_sort_of_order_list = get_group_options('default_sort_of_order_list');
            $default_sort_of_order_list = $ob;//$default_sort_of_order_list?$default_sort_of_order_list:
            $order_by = $default_sort_of_order_list;//orders.created DESC,assigned_order ASC,
        }
        return $order_by;
    }
    function delete(){
        if(isset($_REQUEST['selected_ids']) and count($_REQUEST['selected_ids'])>0){
            foreach($_REQUEST['selected_ids'] as $key){
                if($item = DB::exists('orders','id='.DB::escape($key).' and group_id='.AdminOrders::$account_id)){
                    //save_recycle_bin('orders',$item);
                    //DB::delete_id('orders',intval($key));1
                    @unlink($item['image_url']);
                    @unlink($item['small_thumb_url']);
                    save_log($key);
                }
            }
        }
        Url::redirect_current();
    }

    /**
     * { function_description }
     *
     * @param      array   $users  The users
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function formatUserList(array $users)
    {
        return array_reduce($users, function($results, $user){
            if($user['is_active'] == 1){
                $results[1][] = $user;
            }else{
                $user['id'] .= ' (Chưa kích hoạt)';
            }

            $results[0][] = $user;

            return $results;
        }, [[], []]);
    }

    /**
     * { function_description }
     */
    private function assignBundle()
    {
        if(!AdminOrders::$is_owner && !AdminOrders::$admin_group){
            equestHandler::sendJsonError('BAD_REQUEST');
        }

        if(!$this->getOrdersByIDs($orderIDs = URL::getSafeIDs('order_ids'))){
            RequestHandler::sendJsonError('BAD_REQUEST');
        }

        if(!$bundleID = URL::getInt('bundle')){
            RequestHandler::sendJsonError('BAD_REQUEST');
        }

        if($bundleID > 0 && (!$name = $this->isValidBundle($bundleID))){
            RequestHandler::sendJsonError('BAD_REQUEST');
        }

        $where = sprintf('id IN (%s)', implode(',', $orderIDs));
        $data = ['bundle_id' => $bundleID < 0 ? 'NULL' : $bundleID];
        if(!DB::update('orders', $data, $where)){
            RequestHandler::sendJsonError('ERROR');
        }

        foreach ($orderIDs as $orderID) {
            $message = $bundleID < 0 ? 'Hủy phân loại của đơn hàng.' : 'Gán phân loại <b>' . $name . '</b> cho đơn hàng.';
            AdminOrdersDB::update_revision($orderID,false,false, $message);
        }

        RequestHandler::sendJsonSuccess('SUCCESS');
    }

    /**
     * { function_description }
     */
    private function assignCustomerGroup()
    {
        if(!AdminOrders::$is_owner && !AdminOrders::$admin_group){
            equestHandler::sendJsonError('BAD_REQUEST');
        }

        if(!$customerIDs = $this->getOrdersByIDs($orderIDs = URL::getSafeIDs('order_ids'))){
            RequestHandler::sendJsonError('BAD_REQUEST');
        }

        if(!$customerGroupID = URL::getInt('customer_group')){
            RequestHandler::sendJsonError('BAD_REQUEST');
        }

        if($customerGroupID > 0 && (!$name = $this->isValidCustomerGroup($customerGroupID))){
            RequestHandler::sendJsonError('BAD_REQUEST');
        }

        $where = sprintf('order_id IN (%s)', implode(',', $orderIDs));
        $data = ['customer_group' => $customerGroupID < 0 ? '0' : $customerGroupID];
        if(!DB::update('orders_extra', $data, $where)){
            RequestHandler::sendJsonError('ERROR');
        }

        $where = sprintf('id IN (%s)', implode(',', $customerIDs));
        $data = ['crm_group_id' => $customerGroupID < 0 ? '0' : $customerGroupID];
        if(!DB::update('crm_customer', $data, $where)){
            RequestHandler::sendJsonError('ERROR');
        }

        foreach ($orderIDs as $orderID) {
            $message = $customerGroupID < 0 ? 'Hủy nhóm khách hàng của đơn hàng.' : 'Gán nhóm khách hàng <b>' . $name . '</b> cho đơn hàng.';
            AdminOrdersDB::update_revision($orderID,false,false, $message);
        }

        RequestHandler::sendJsonSuccess('SUCCESS');
    }

    /**
     * Determines whether the specified order i ds is valid order i ds.
     *
     * @param      array  $orderIDs  The order i ds
     *
     * @return     bool   True if the specified order i ds is valid order i ds, False otherwise.
     */
    private function getOrdersByIDs(array $orderIDs)
    {
        $sql = 'SELECT `customer_id` FROM `orders` WHERE `id` IN (' . implode(',', $orderIDs) . ')';
        $saved = DB::fetch_all_column($sql, 'customer_id');

        return count($orderIDs) === count($saved) ? $saved : false;
    }

    /**
     * Determines whether the specified customer group id is valid customer group.
     *
     * @param      int   $customerGroupID  The customer group id
     */
    private function isValidCustomerGroup(int $customerGroupID)
    {
        return DB::fetch('
            SELECT `name` 
            FROM `crm_customer_group` 
            WHERE 
                `group_id` IN (' . AdminOrders::$group_id . ',1)
                AND `id` = ' . $customerGroupID,
            'name'
        );
    }

    /**
     * Determines whether the specified bundle id is valid bundle.
     *
     * @param      int   $bundleID  The bundle id
     *
     * @return     bool  True if the specified bundle id is valid bundle, False otherwise.
     */
    private function isValidBundle(int $bundleID)
    {
        return DB::fetch('
            SELECT `name` 
            FROM `bundles` 
            WHERE 
                `group_id` = ' . AdminOrders::$group_id . '
                AND `id` = ' . $bundleID,
            'name'
        );
    }
}
