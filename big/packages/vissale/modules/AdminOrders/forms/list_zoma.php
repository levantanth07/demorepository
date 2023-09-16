<?php
class ListAdminOrdersForm extends Form{
    public static $user_id;
    public static $quyen_xuat_kho;
    public static $quyen_marketing;
    public static $quyen_admin_marketing;
    public static $quyen_bung_don;
    public static $quyen_cskh;
    public static $quyen_xuat_excel;
    public static $quyen_ke_toan;
    public static $quyen_admin_ke_toan;
    protected $map;
    function __construct(){
        Form::Form('ListAdminOrdersForm');
        $this->link_css('assets/lib/DataTables/datatables.min.css');
        $this->link_js('assets/lib/DataTables/datatables.min.js');
        $this->link_css('packages/vissale/modules/AdminOrders/css/common.css?v=22082019');
        require_once "packages/vissale/modules/PrintTemplates/config.php";
        require_once "packages/vissale/lib/php/simple_html_dom.php";
        ListAdminOrdersForm::$user_id = get_user_id();
        ListAdminOrdersForm::$quyen_xuat_kho = check_user_privilege('XUATKHO');
        ListAdminOrdersForm::$quyen_marketing = check_user_privilege('MARKETING');
        ListAdminOrdersForm::$quyen_admin_marketing = check_user_privilege('ADMIN_MARKETING');
        ListAdminOrdersForm::$quyen_bung_don = check_user_privilege('BUNGDON');
        ListAdminOrdersForm::$quyen_cskh = check_user_privilege('CSKH');
        ListAdminOrdersForm::$quyen_xuat_excel = check_user_privilege('XUAT_EXCEL');
        ListAdminOrdersForm::$quyen_ke_toan = check_user_privilege('KE_TOAN');
        ListAdminOrdersForm::$quyen_admin_ke_toan = check_user_privilege('ADMIN_KETOAN');

        if((Url::get('act')=='del_order')){//edited by khoand in 03/12/2018
            if($order_ids = Url::get('checked_order')){
                AdminOrdersDB::delete_order($order_ids);
            }
        }
    }
    function save_position(){
        foreach($_REQUEST as $key=>$value){
            if(preg_match('/position_([0-9]+)/',$key,$match) and isset($match[1])){
                DB::update_id('orders',array('position'=>Url::get('position_'.$match[1])),$match[1]);
            }
        }
        Url::redirect_current();
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
    function on_submit(){
        switch(Url::get('cmd')){
            case 'update_position':
                $this->save_position();
                break;
            case 'delete':
                $this->delete();
                break;
            default:
                if(Url::get('autoAssignOrder')){
                    //System::debug($_REQUEST);die;
                    if($all_ass_account_id = Url::get('all_ass_account_id')){// added by khoand in 05/10/2018
                        //echo $account_ids = '"'.join($all_ass_account_id,'","').'"';
                        $new_account_ids = array();
                        foreach($all_ass_account_id as $value){
                            $user = DB::fetch('select id,name from users where username="'.$value.'"');
                            $new_account_ids[$user['id']]['user_id'] = $user['id'];
                            $new_account_ids[$user['id']]['id'] = $value;
                            $new_account_ids[$user['id']]['name'] = $value;
                        }
                        if($total = AdminOrdersDB::autoAssignOrder(false,false,$new_account_ids,Url::get('ass_source_id'),Url::get('ass_bundle_id'),Url::iget('assigned_total'))){
                            Url::js_redirect(true,'Gán đơn tự động '.$total.' đơn thành công');
                        }else{
                            Url::js_redirect(true,'Không có tài khoản hoặc đơn hàng nào được chọn để chia đơn');
                        }
                    }else{
                        $account_group_id=Url::iget('account_group_id')?Url::iget('account_group_id'):false;
                        if($total = AdminOrdersDB::autoAssignOrder($account_group_id)){
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
                        DB::update('orders_column_custom',array('show_columns'=>$show_columns,'last_edited_time'=>time(),'last_edited_account_id'=>Session::get('user_id')),'group_id='.AdminOrders::$account_id);
                    }else{
                        DB::insert('orders_column_custom',array('show_columns'=>$show_columns,'last_edited_time'=>time()	,'last_edited_account_id'=>Session::get('user_id'),'group_id'=>AdminOrders::$account_id));
                    }
                    Url::js_redirect(true,'Bạn đã cập nhật thành công');
                }
                break;
        }
    }
    function draw(){
        $group_id = AdminOrders::$group_id;
        $master_group_id = AdminOrders::$master_group_id;
        $account_type = $this->map['account_type'] = AdminOrders::$account_type;

        $this->map['is_account_group_manager'] = is_account_group_manager($group_id);
        $this->map['phone_store_id'] = Session::get('phone_store_id');
        $this->map['time_to_refesh_order'] = (int) get_group_options('time_to_refesh_order');
        $this->map['source_id_list'] = $this->map['ass_source_id_list'] = array('Tất cả các nguồn')+MiString::get_list(AdminOrdersDB::get_source(),'name');

        $accept_edit_transport = 'accept';
        if (empty(get_group_options('integrate_shipping'))) {
            $accept_edit_transport = 'not-intergrate-shipping';
        }
        $this->map['accept_edit_transport'] = $accept_edit_transport;
        $total_not_assigned_order = 0;// added by khoand in 05/10/2018
        if($account_type==3 or $master_group_id){
            if($account_type==3){
                $bundles = AdminOrdersDB::get_bundles($group_id);
            }else{
                $bundles = AdminOrdersDB::get_bundles($master_group_id);
            }
            $this->map['search_group_id_list'] = array(''=>'Tất cả cty') + MiString::get_list(AdminOrdersDB::get_groups($group_id));
            $sql = '
              select 
                (orders.id) as total 
              from 
                orders 
                JOIN `groups` ON groups.id = orders.master_group_id
              where 
                orders.status_id = 10 
                and (group_id='.$group_id.' '.($master_group_id?'':' or groups.id='.$group_id.'').')
                and (user_assigned is null or user_assigned = 0)
            ';
            $total_not_assigned_order = AdminOrdersDB::get_total_item_by_sql($sql);
        }else{
            $sql = 'select (id) as total from orders where status_id = 10 and (group_id='.$group_id.') and (user_assigned is null or user_assigned = 0)';
            $total_not_assigned_order = AdminOrdersDB::get_total_item_by_sql($sql);
            $bundles = AdminOrdersDB::get_bundles();
        }

        $this->map['total_not_assigned_order'] = $total_not_assigned_order;
        $party = AdminOrdersDB::get_order_info();
        $this->map['min_search_phone_number'] = get_group_options('min_search_phone_number');
        $this->map['min_search_phone_number'] = $this->map['min_search_phone_number']?$this->map['min_search_phone_number']:3;
        $this->map['prints'] = AdminOrdersDB::get_prints();

        $this->map['user_id'] = DB::fetch('select id from users where username="'.Session::get('user_id').'"','id');
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
        if(Session::get('user_id') == 'zm.admin4'){
            //echo $cond;
        }
        if($cond == '1=1'){
            $cond = ' orders.group_id="'.$group_id.'"';
        }
        $this->get_just_edited_id();
        require_once 'packages/core/includes/utils/paging.php';
        require_once 'cache/config/product_status.php';
        $item_per_page = Url::get('item_per_page')?Url::get('item_per_page'):15;
        $check_get_item = false;
        $total = 0;
        $total_amount = 0;
        if(Url::get('load_ajax')==1 or Url::get('act')=='print') {
            $check_get_item = true;
            $total = AdminOrdersDB::get_total_item($cond);
            $total_amount = AdminOrdersDB::get_total_amount($cond);
        }
        $status = Url::get('status_id')?Url::get('status_id'):Url::get('status');
        if(!Url::get('checked_order')){
            $paging_array = array(
                'page_id'=>Url::get('page_id'),'item_per_page'=>Url::get('item_per_page'),
                'group_id'=>Url::get('group_id'),'cmd'=>Url::get('cmd'),'search_account_id'=>Url::get('search_account_id'),
                'type'=>Url::get('type'),'category_id'=>Url::get('category_id'),
                'status'=>is_array($status)?implode(',',$status):$status,
                'keyword'=>Url::get('keyword'),
                'ngay_tao_from'=>Url::get('ngay_tao_from'),'ngay_tao_to'=>Url::get('ngay_tao_to'),
                'ngay_chia_from'=>Url::get('ngay_chia_from'),'ngay_chia_to'=>Url::get('ngay_chia_to'),
                'ngay_xn_from'=>Url::get('ngay_xn_from'),'ngay_xn_to'=>Url::get('ngay_xn_to'),
                'ngay_chuyen_kt_from'=>Url::get('ngay_chuyen_kt_from'),'ngay_chuyen_kt_to'=>Url::get('ngay_chuyen_kt_to'),
                'ngay_chuyen_from'=>Url::get('ngay_chuyen_from'),'ngay_chuyen_to'=>Url::get('ngay_chuyen_to'),'is_inner_city'=>Url::get('is_inner_city')
            );
            $paging = order_page_ajax($total,$item_per_page,$paging_array,7,'page_no','');
        }else{
            $paging = '';
        }
        $this->map['page_no'] = page_no();
        //echo $cond;
        $order_by = $this->get_order_by();

        $layout = 'list';
        if(Url::get('cmd') == 'list_pos'){
            $layout='list_pos';
        }
        if(Url::get('load_ajax')==1){
            $layout = 'list_ajax';
        }
        $template = "";
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
            //System::debug($cond_print);die;
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
            $print_constants = prints_constant();
            $constants = [];
            foreach ($print_constants as $key => $value) {
                if (in_array('DON_HANG', $value['types'])) {
                    $constants[$key] = $value;
                }
            }

            $this->map['print_constants'] = $constants;
            $info_senders = AdminOrdersDB::getInfoSenderByOrderId();
            $this->map['info_senders'] = $info_senders;
        }

        $this->map['template'] = $template;
        $items = array();
        if($check_get_item){
            $items = AdminOrdersDB::get_items($cond,$order_by,$item_per_page,'cache_orders');
        }
        //die;
        $item_per_page_list = array(''=>'Dòng hiển thị',5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,20=>20,50=>50,100=>100,200=>200,500=>500);
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
        if(!Url::get('ngay_tao_from')){
            $_REQUEST['ngay_tao_from'] = date('d/m/Y',time()-3*24*3600);
        }
        if(!Url::get('ngay_chia_from')){
            $_REQUEST['ngay_chia_from'] = date('d/m/Y',time()-3*24*3600);
        }
        if(!Url::get('ngay_xn_from')){
            $_REQUEST['ngay_xn_from'] = date('d/m/Y',time()-3*24*3600);
        }
        if(!Url::get('ngay_chuyen_from')){
            $_REQUEST['ngay_chuyen_from'] = date('d/m/Y',time()-3*24*3600);
        }
        if(!Url::get('ngay_chuyen_kt_from')){
            $_REQUEST['ngay_chuyen_kt_from'] = date('d/m/Y',time()-3*24*3600);
        }
        if(!Url::get('ngay_thanh_cong_from')){
            $_REQUEST['ngay_thanh_cong_from'] = date('d/m/Y',time()-3*24*3600);
        }
        if(!Url::get('ngay_thu_tien_from')){
            $_REQUEST['ngay_thu_tien_from'] = date('d/m/Y',time()-3*24*3600);
        }
        if(!Url::get('ngay_tao_to')){
            $_REQUEST['ngay_tao_to'] = date('d/m/Y');
        }
        if(!Url::get('ngay_chia_to')){
            $_REQUEST['ngay_chia_to'] = date('d/m/Y');
        }
        if(!Url::get('ngay_xn_to')){
            $_REQUEST['ngay_xn_to'] = date('d/m/Y');
        }
        if(!Url::get('ngay_chuyen_to')){
            $_REQUEST['ngay_chuyen_to'] = date('d/m/Y');
        }
        if(!Url::get('ngay_chuyen_kt_to')){
            $_REQUEST['ngay_chuyen_kt_to'] = date('d/m/Y');
        }
        if(!Url::get('ngay_thanh_cong_to')){
            $_REQUEST['ngay_thanh_cong_to'] = date('d/m/Y',time());
        }
        if(!Url::get('ngay_thu_tien_to')){
            $_REQUEST['ngay_thu_tien_to'] = date('d/m/Y',time());
        }
        ////
        $account_groups = get_account_groups();
        $this->map['account_group_is_empty'] = (sizeof($account_groups)>0)?false:true;
        $this->map['account_group_id_list'] = array(''=>'Tất cả các tài khoản được quyền gán đơn') + MiString::get_list($account_groups);
        $this->map['ass_account_group_id_list'] = array(''=>'Chọn nhóm tài ') + MiString::get_list($account_groups);

        ////
        $columns = get_order_columns();
        $mkts = MiString::get_list(AdminOrdersDB::get_users('MARKETING'));

        $sales = MiString::get_list(AdminOrdersDB::get_users('GANDON'),($account_type==TONG_CONG_TY)?'full_name':'');
        if(AdminOrders::$admin_group or ListAdminOrdersForm::$quyen_marketing or ListAdminOrdersForm::$quyen_admin_marketing){
            $sales = array('-1'=>'=>Bỏ chia / không chia') + $sales;
        }
        if(AdminOrders::$admin_group or AdminOrders::$quyen_chia_don or AdminOrders::$quyen_bung_don){
            $restricted_sales = $sales;
        }else{
            $restricted_sales = MiString::get_list(AdminOrdersDB::get_users('GANDON',false,false,true),($account_type==TONG_CONG_TY)?'full_name':'');
        }
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

        $this->map['paper_sizes'] = $paper_sizes;
        $this->map['type_list'] = [
            ""=>'Tất cả',
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
        $this->map += array(
            'account_type'=>$account_type,
            'is_master_group'=>0,//
            'group_name'=>$group_name,
            'shipping_services'=>AdminOrdersDB::shipping_services(),
            'status'=>$status_arr,
            'items'=>$items,
            'paging'=>$paging,
            'group_id_list'=>array(''=>'Chọn GROUP') + MiString::get_list($groups),
            'total'=>$total,
            'category_id_list'=>array(""=>'Chọn phân loại')+MiString::get_list(AdminOrdersDB::get_category()),
            'status_id_list'=>array(""=>'Chọn trạng thái')+$status,
            'search_account_id_list'=>array(""=>'Tất cả NV')+MiString::get_list(AdminOrdersDB::get_users()),
            'mkt_account_id_list'=>array(""=>'Tất cả mkt')+$mkts,
            'ass_mkt_account_id_list'=>array(""=>'Tất cả mkt')+$mkts,
            'assigned_account_id_list'=>array(""=>'Tất cả sale')+$sales,
            'ass_account_id_list'=>array(""=>'Chọn sale để gán đơn hàng')+$restricted_sales,
            'all_ass_account_id[]_list'=>array(""=>'Chọn sale để gán đơn hàng')+$restricted_sales,
            'bundle_id_list'=>array(""=>'Phân loại')+array("-1"=>'Đơn chưa phân loại')+MiString::get_list($bundles),
            'ass_bundle_id_list'=>array(""=>'Tất cả phân loại')+MiString::get_list($bundles),
            'item_per_page_list'=>$item_per_page_list,
            'total_amount'=>System::display_number($total_amount),
            'quyen_xuat_kho'=>ListAdminOrdersForm::$quyen_xuat_kho,
            'quyen_xuat_excel'=>ListAdminOrdersForm::$quyen_xuat_excel,
            'quyen_ke_toan'=>ListAdminOrdersForm::$quyen_ke_toan,
            'quyen_chia_don'=>AdminOrders::$quyen_chia_don,
            'quyen_marketing'=>ListAdminOrdersForm::$quyen_marketing,
            'quyen_admin_marketing'=>ListAdminOrdersForm::$quyen_admin_marketing,
            'quyen_admin_ke_toan'=>ListAdminOrdersForm::$quyen_admin_ke_toan,
            'warehouse_id_list' => array(''=>'Chọn kho để xuất')+MiString::get_list(DB::select_all('qlbh_warehouse','structure_id='.ID_ROOT.' OR group_id = '.$group_id.'','structure_id')),
            'columns'=>$columns,
            'all_columns'=>get_order_columns(true)
        );

        $this->parse_layout($layout,$party + $this->just_edited_id+$this->map);
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
            $ob = (Url::get('c_n') or Url::get('customer_name'))?'orders.customer_name,orders.id DESC':'orders.id DESC';

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
}
?>
