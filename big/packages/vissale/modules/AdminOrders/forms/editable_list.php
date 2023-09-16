<?php
use GuzzleHttp\Client;
require_once ROOT_PATH.'packages/core/includes/common/BiggameAPI.php';

class EditableListForm extends Form{
    function __construct(){
        Form::Form('EditableListForm');
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
                if($item = DB::exists('orders','id='.DB::escape($key).' and group_id='.Session::get('group_id'))){
                    save_recycle_bin('orders',$item);
                    //DB::delete_id('orders',intval($key));
                    @unlink($item['image_url']);
                    @unlink($item['small_thumb_url']);
                    save_log($key);
                }
            }
        }
        Url::redirect_current();
    }

    function on_submit(){
        if(!Url::get('update')){
            $arr = array('cmd'=>'quick_edit',
                'term_sdt','term_order_id','term_ship_id','ngay_tao_from','ngay_tao_to','ngay_xn_from','ngay_xn_to',
                'customer_name', 'ngay_chuyen_from','ngay_chuyen_to', 'bundle_id','search_account_id','act','status_id',
                'item_per_page');
            Url::redirect_current($arr);
        }

        if(!isset($_REQUEST['mi_order'])){
            Url::redirect_current(['cmd']);
        }

        $labelNames = [
            'id'=>'ID',
            'customer_name'=>'Tên khách',
            'note1'=>'Ghi chú',
            'user_created'=>'Người tạo',
            'created'=>'Ngày tạo',
            'user_confirmed'=>'Người xác nhận',
            'confirmed'=>'Ngày xác nhận',
            'user_delivered'=>'Người chuyển',
            'delivered'=>'Ngày chuyển hàng',
        ];
        $datas = [];
        foreach($_REQUEST['mi_order'] as $key=>$value){
            if($value['id']=='(auto)'){
                $value['id']=false;
            }
            $datas[$value['id']]['id'] = $value['id'];
            $datas[$value['id']]['user_confirmed'] = $value['user_confirmed'];
            $datas[$value['id']]['confirmed'] = $value['confirmed'] ? date('Y-m-d H:i:s',strtotime(str_replace('/','-',$value['confirmed']))) : '';
            $datas[$value['id']]['total_price'] = $value['total_price']?System::calculate_number($value['total_price']):'0';
        }
        if($datas && defined('BIGGAME_SYNC') && BIGGAME_SYNC === 1 ){
            $this->sentDataToApiTuha($datas);
        }
        foreach($_REQUEST['mi_order'] as $key=>$record){
            if($record['id']=='(auto)'){
                $record['id']=false;
            }
            $record['total_price'] = $record['total_price']?System::calculate_number($record['total_price']):'0';
            if(isset($record['user_confirmed']) && $record['user_confirmed'] == -1){
                   $record['user_confirmed'] = 0;
            }

            if(isset($record['user_created']) && $record['user_created'] == -1){
                $record['user_created'] = 0;
            }

            if(isset($record['user_delivered']) && $record['user_delivered'] == -1){
                $record['user_delivered'] = 0;
            }

            $edited_log_data = ['<b>Chỉnh sửa nhanh: </b>'];
            if($record['id'] and $order = AdminOrdersDB::getOrderById(Session::get('group_id'), DB::escape($record['id']))){
                $order_id = $order['id'];
                foreach($labelNames as $fieldName => $v){
                    // Trùng nhau thì không cập nhật
                    if(!isset($record[$fieldName]) || $order[$fieldName] == $record[$fieldName]){
                        continue;
                    }

                    if($this->isUserIDField($fieldName)){
                        $order[$fieldName . '_name'] = $this->userIDToName((int)$order[$fieldName]);
                        $record[$fieldName . '_name'] = $this->userIDToName((int)$record[$fieldName]);
                    }

                    if ($this->isDatetimeField($fieldName)) {
                        $order[$fieldName] = $this->getValidDate($order[$fieldName] . "");
                        $record[$fieldName] = $this->getValidDate($record[$fieldName] . "");
                    }

                    if($this->isDatetimeField($fieldName)){
                        if ($order[$fieldName] == AdminOrders::$date_init_value && $record[$fieldName] != AdminOrders::$date_init_value) {
                            $edited_log_data[] = sprintf(
                                'Thêm %s "<b>%s</b>"',
                                $labelNames[$fieldName],
                                $this->toDateTimeFormat($record[$fieldName] ? $record[$fieldName] : $record[$fieldName])
                            );
                        }

                        elseif($record[$fieldName] == AdminOrders::$date_init_value && $order[$fieldName] != AdminOrders::$date_init_value){
                            $edited_log_data[] = sprintf(
                                'Xóa %s  "<b>%s</b>"',
                                $labelNames[$fieldName],
                                $this->toDateTimeFormat($order[$fieldName])
                            );
                        }

                        else if($order[$fieldName] != AdminOrders::$date_init_value && $record[$fieldName] != AdminOrders::$date_init_value && $order[$fieldName] != $record[$fieldName]){
                            $edited_log_data[] = sprintf(
                                'Thay đổi %s từ "<b>%s</b>" thành "<b>%s</b>"',
                                $labelNames[$fieldName],
                                $this->toDateTimeFormat($order[$fieldName]),
                                $this->toDateTimeFormat($record[$fieldName] ? $record[$fieldName] : $record[$fieldName])
                            );
                        }
                    }else{
                        $isUserIDField = $this->isUserIDField($fieldName);

                        if (empty($order[$fieldName]) && !empty($record[$fieldName])) {
                            $edited_log_data[] = sprintf(
                                'Thêm %s "<b>%s</b>"',
                                $labelNames[$fieldName],
                                $record[$fieldName . ($isUserIDField ?  '_name' : '')]
                            );
                        }

                        elseif(empty($record[$fieldName]) && !empty($order[$fieldName])){
                            $edited_log_data[] = sprintf(
                                'Xóa %s  "<b>%s</b>"',
                                $labelNames[$fieldName],
                                $order[$fieldName . ($isUserIDField ?  '_name' : '')]
                            );
                        }

                        else if(!empty($order[$fieldName]) && !empty($record[$fieldName]) && $order[$fieldName] != $record[$fieldName]){
                            $edited_log_data[] = sprintf(
                                'Thay đổi %s từ "<b>%s</b>" thành "<b>%s</b>"',
                                $labelNames[$fieldName],
                                $order[$fieldName . ($isUserIDField ?  '_name' : '')],
                                $record[$fieldName . ($isUserIDField ?  '_name' : '')]
                            );
                        }

                        if($this->isUserIDField($fieldName)){
                            unset($order[$fieldName . '_name']);
                            unset($record[$fieldName . '_name']);
                        }
                    }
                }

                if(count($edited_log_data) > 1){
                    $record['created'] = $this->getValidDate($record['created']);
                    $record['confirmed'] = $this->getValidDate($record['confirmed']);
                    $record['delivered'] = $this->getValidDate($record['delivered']);

                    AdminOrdersDB::update_revision($record['id'],false,false,implode('<br>', $edited_log_data));
                    AdminOrdersDB::logConnect($record['id']);

                    $record['customer_name'] =  DataFilter::removeXSSinHtml( $record['customer_name']);
                    $record['note1'] =  DataFilter::removeXSSinHtml( $record['note1']);

                    DB::update('orders',$record,'id='.DB::escape($record['id']));
                }
            }
        }
        Url::redirect_current(['cmd']);
    }
    private function sentDataToApiTuha($datas){
        // $url = TUHA_BIGGAME_ENDPOINT;
        $data = $this->processDataToApiTuha($datas);
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
        }*/
    }

    private function processDataToApiTuha($datas){
        $orderIds = array_keys($datas);
        $strOrderIds = implode(',',$orderIds);
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
                    orders_extra.accounting_confirmed 
                FROM 
                    orders 
                JOIN 
                    groups ON orders.group_id = groups.id
                JOIN
                    users ON orders.user_confirmed = users.id
                LEFT JOIN 
                    orders_extra ON orders_extra.order_id = orders.id
                LEFT JOIN 
                    statuses ON orders.status_id = statuses.id
                WHERE
                    orders.id IN ($strOrderIds) AND orders.user_confirmed IS NOT NULL;
                ";
        $result = DB::fetch_all($sql);
        $data = [];
        $userIds = [];
        $strUserIds = '';
        foreach ($datas as $user) {
            $userIds[] = $user['user_confirmed'];
        }
        $strUserIds = implode(',',$userIds);
        $sqlUser = "SELECT id, name FROM users WHERE id IN ($strUserIds)";
        $resultUser = DB::fetch_all($sqlUser);
        foreach($result as $key => $value){
            if(isset($datas[$key])){
                if(strtotime($datas[$key]['confirmed']) != strtotime($value['confirmed']) || $datas[$key]['user_confirmed'] != $value['user_confirmed'] ){
                    $data[$key] = [
                        'id' => $value['id'],
                        'confirmed' => $datas[$key]['confirmed']??'',
                        'user_confirmed' => $datas[$key]['user_confirmed']??'',
                        'total_price' => $value['total_price'],
                        'group_id' => $value['group_id']??'',
                        'group_name' => $value['group_name']??'',
                        'user_confirmed_name' => isset($resultUser[$datas[$key]['user_confirmed']]) ? $resultUser[$datas[$key]['user_confirmed']]['name'] : '',
                        'accounting_confirmed' => $value['accounting_confirmed']??'',
                        'status_id' => $value['status_id'],
                        'no_revenue' => $value['status_no_revenue']??1,
                        'level' => $value['status_level']??0
                    ];
                }
            }

        }
        return $data;
    }
    /**
     * { function_description }
     *
     * @param      string  $datetime  The datetime
     * @param      string  $format    The format
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function toDateTimeFormat(string $datetime, string $format = 'd/m/Y H:i:s')
    {
        return date($format, strtotime($datetime));
    }

    /**
     * { function_description }
     *
     * @param      int     $ID     { parameter_description }
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function userIDToName(int $ID = null)
    {
        return $ID ? DB::fetch('SELECT `name` FROM `users` WHERE `id`='.DB::escape($ID),'name') : '';
    }

    /**
     * Gets the valid date.
     *
     * @param      string  $date   The date
     *
     * @return     <type>  The valid date.
     */
    private function getValidDate(string $date)
    {
        if(!$date || $date === AdminOrders::$date_init_value){
            return AdminOrders::$date_init_value;
        }

        if(!$date = strtotime(str_replace('/', '-', $date))){
            return AdminOrders::$date_init_value;
        }

        return date('Y-m-d H:i:s', $date);
    }

    /**
     * Determines whether the specified field name is datetime field.
     *
     * @param      string  $fieldName  The field name
     */
    private function isDatetimeField(string $fieldName)
    {
        return in_array($fieldName, ['confirmed', 'delivered', 'created']);
    }

    /**
     * Determines whether the specified field name is user id field.
     *
     * @param      string  $fieldName  The field name
     *
     * @return     bool    True if the specified field name is user id field, False otherwise.
     */
    private function isUserIDField(string $fieldName)
    {
        return in_array($fieldName, ['user_confirmed', 'user_delivered', 'user_created']);
    }

    function draw(){
        $this->map = array();
        $item_per_page_list = array(''=>'Dòng hiển thị',20=>20,50=>50,100=>100);
        $group_name = '';
        if(Url::iget('group_id')){
            $group_name = ' của "'.DB::fetch('select name from `groups` where id='.Url::iget('group_id'),'name').'"';
        }
        $arrError = array();
        $cond = $this->get_condition();
        if (is_array($cond)) {
            $arrError = $cond;
            $cond = '1=2 and 1=2 and 1=2';
        } elseif ($cond) {
            $cond = '1=1' . $cond;
        } else {
            $cond = '1=2 and 1=2 and 1=2';
        }
        if(Url::get('act')=='error'){
            $cond .= '
                AND (
                    (select group_id from users where users.id=orders.user_confirmed) <> orders.group_id
                    OR (select group_id from users where users.id=orders.user_delivered) <> orders.group_id
                )
            ';
        }
        if(count(explode('AND', strtoupper($cond))) < 3){
            $group_id = Url::get('group_id') ?: Session::get('group_id');
            $cond = ' groups.id="'.$group_id.'"' .
                ' AND orders.created>="'.date('Y/m/01', strtotime('-5 month')).' 00:00:00"' .
                ' AND orders.created<="'.date('Y/m/d').' 23:59:59"';
        }
        $this->get_just_edited_id();
        require_once 'packages/core/includes/utils/paging.php';
        require_once 'cache/config/product_status.php';
        $item_per_page = Url::get('item_per_page')?Url::get('item_per_page'):20;
        $item_per_page = in_array((int)$item_per_page, $item_per_page_list) ? $item_per_page : 20;
        $total = (Url::iget('total')) ?: AdminOrdersDB::get_total_item($cond);
        $total_amount = (Url::iget('total_amount')) ?: AdminOrdersDB::get_total_amount($cond);
        $_REQUEST['total'] = $total;
        $_REQUEST['total_amount'] = $total_amount;
        $paging_array = array('item_per_page','group_id','cmd','search_account_id','type','category_id','status_id','keyword','term_sdt','term_order_id','term_ship_id','ngay_tao_from','ngay_tao_to','ngay_xn_from','ngay_xn_to','ngay_chuyen_from','ngay_chuyen_to','is_inner_city','act','total','total_amount','customer_name');
        $paging = paging($total,$item_per_page,20,false,'page_no',$paging_array);
        ///////////////
        $items = $this->get_items($cond,'orders.id DESC',$item_per_page);
        System::sksort($items, "id",false);
        $_REQUEST['mi_order'] = ($items);
        ///////////////
        $status_arr = AdminOrdersDB::get_status();
        $status = MiString::get_list($status_arr);
        $status_options = '<option value="">Chọn</option>';
        foreach($status_arr as $key=>$val){
            $status_options .= '<option value="'.$key.'">'.$val['name'].'</option>';
        }
        $this->map['status_options'] = $status_options;
        //////////////

        // Bind danh sách trạng thái ra view để dùng trong việc validate ở javascript
        $this->map['statuses'] = json_encode($status_arr);

        $this->map['device'] = Session::get('device');
        $layout = 'editable_list';
        if(Url::get('load_ajax')==1){
            $layout = 'editable_list_form';
        }
        $party = AdminOrdersDB::get_order_info();
        $master_group_id = get_master_group_id();
        if($master_group_id and is_master_group()){
            $groups = get_groups($master_group_id);
        }else{
            $groups = array();
        }
        $sales = AdminOrdersDB::get_users('GANDON',false,true);
        $this->map['user_confirmed_options'] = '<option value="0">-/-Chọn-/-</option><option value="-1">-/-Bỏ-/-</option>';
        foreach($sales as $key=>$value){
            $this->map['user_confirmed_options'] .= '<option value="'.$key.'">'.$value['full_name'].'</option>';
        }

        $creates = AdminOrdersDB::get_users(false,false,true);
        $this->map['user_created_options'] = '<option value="0">-/-Chọn-/-</option><option value="-1">-/-Bỏ-/-</option>';
        foreach($creates as $key=>$value){
            $this->map['user_created_options'] .= '<option value="'.$key.'">'.$value['full_name'].'</option>';
        }

        $this->map['term_sdt'] = Url::get('term_sdt');
        $this->map['term_order_id'] = Url::get('term_order_id');
        $this->map['term_ship_id'] = Url::get('term_ship_id');
        $this->map['min_search_phone_number'] = get_group_options('min_search_phone_number');
        $this->map += array(
            'is_master_group'=>is_master_group(),
            'group_name'=>$group_name,
            'shipping_services'=>AdminOrdersDB::shipping_services(),
            'status'=>AdminOrdersDB::get_status(),
            'items'=>$items,
            'paging'=>$paging,
            'group_id_list'=>array(''=>'Chọn GROUP') + MiString::get_list($groups),
            'total'=>$total,
            'arrError'=>$arrError,
            'category_id_list'=>array(""=>'Chọn phân loại')+MiString::get_list(AdminOrdersDB::get_category()),
            'status_id_list'=>array(""=>'Chọn trạng thái')+$status,
            'search_account_id_list'=>array(""=>'Chọn nhân viên')+MiString::get_list(AdminOrdersDB::get_users()),
            'account_id_list'=>array(""=>'Chọn nhân viên để gán đơn hàng')+MiString::get_list(AdminOrdersDB::get_users()),
            'bundle_id_list'=>array(""=>'Phân loại')+MiString::get_list(AdminOrdersDB::get_bundles()),
            'item_per_page_list'=>$item_per_page_list,
            'total_amount'=>System::display_number($total_amount)
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
    function get_condition(){
        $arrErrors = array();
        $term_sdt = URL::getStringEscapeNoQuote('term_sdt');
        $term_order_id = URL::getStringEscapeNoQuote('term_order_id');
        $term_ship_id = URL::getStringEscapeNoQuote('term_ship_id');

        $cond = '
            '.(DB::escape(Url::get('ngay_tao_from'))?' AND orders.created>="'.Date_Time::to_sql_date(DB::escape(Url::get('ngay_tao_from'))).' 00:00:00"':'').'
            '.(DB::escape(Url::get('ngay_tao_to'))?' AND orders.created<="'.Date_Time::to_sql_date(DB::escape(Url::get('ngay_tao_to'))).' 23:59:59"':'').'
            '.(DB::escape(Url::get('ngay_xn_from'))?' AND orders.confirmed>="'.Date_Time::to_sql_date(DB::escape(Url::get('ngay_xn_from'))).' 00:00:00"':'').'
            '.(DB::escape(Url::get('ngay_xn_to'))?' AND orders.confirmed<="'.Date_Time::to_sql_date(DB::escape(Url::get('ngay_xn_to'))).' 23:59:59"':'').'
            '.(DB::escape(Url::get('ngay_chuyen_from'))?' AND orders.delivered>="'.Date_Time::to_sql_date(DB::escape(Url::get('ngay_chuyen_from'))).' 00:00:00"':'').'
            '.(DB::escape(Url::get('ngay_chuyen_to'))?' AND orders.delivered<="'.Date_Time::to_sql_date(DB::escape(Url::get('ngay_chuyen_to'))).' 23:59:59"':'').'
        ';
        if(Url::get('category_id') and DB::exists_id('category',intval(Url::sget('category_id')))){
            $cond.= ' and '.IDStructure::child_cond(DB::structure_id('category',intval(Url::sget('category_id'))));
        }
        if(Url::get('group_id')){
            $cond.= ' and orders.group_id="'.Url::get('group_id').'"';
        }else{
            $cond.= ' and orders.group_id="'.Session::get('group_id').'"';
        }
        if(Url::get('bundle_id')){
            $cond.= ' and orders.bundle_id="'.Url::get('bundle_id').'"';
        }
        if(Session::get('admin_group')){
            if($search_account_id=DB::escape(Url::get('search_account_id')) and $user_id = DB::fetch('select id from users where username="'.$search_account_id.'"','id')){
                $cond.= ' and orders.user_assigned='.$user_id.'';
            }
        }else{
            $user_id = DB::fetch('select id from users where username="'.Session::get('user_id').'"','id');
            $cond.= ' and (orders.user_assigned='.$user_id.' or orders.user_created='.$user_id.')';
        }
        if(Url::check('is_inner_city')){
            $cond.= ' and orders.is_inner_city=1';
        }
        if($ss = Url::get('shipping_services')){
            //$str = implode($ss,',');
            $idsArr = explode(',',$ss);
            $escape_ids = array_map(function($id){
                return DB::escape($id);
            }, $idsArr);

            $ss = implode(',', $escape_ids);
            $cond.= ' and orders.shipping_service_id IN ('.($ss).')';
        }
        if($ss = Url::get('status')){
            //echo $ss;die;
            //echo $str = implode($ss,',');
            $idsArr = explode(',',$ss);
            $escape_ids = array_map(function($id){
                return DB::escape($id);
            }, $idsArr);

            $ss = implode(',', $escape_ids);
            $cond.= ' and orders.status_id IN ('.($ss).')';
        }
        if($ss = Url::get('dau_so')){
            //$str = implode($ss,'","');
            $idsArr = explode(',',$ss);
            $escape_ids = array_map(function($id){
                return DB::escape($id);
            }, $idsArr);

            $ss = implode(',', $escape_ids);
            $cond.= ' and orders.telco_code IN ("'.($ss).'")';
        }
        if($status_id = DB::escape(Url::iget('status_id'))){
            $cond.= ' and orders.status_id = '.$status_id;
        }
        if($ids = Url::get('ids')){
            $idsArray = explode(',', $ids);
            $escapeIds = array_map(function($id){
                return DB::escape($id);
            }, $idsArray);
            $ids = implode(',', $escapeIds);
            $cond.= ' and orders.id IN ('.$ids.')';
        }

        $min_search_phone_number = get_group_options('min_search_phone_number');
        $min_search_phone_number = $min_search_phone_number?$min_search_phone_number:8;

        if(($term_sdt && strlen($term_sdt) >= $min_search_phone_number) || strlen($term_order_id) > 2 || strlen($term_ship_id) > 2){
            $searchConditions = [];

            // sđt
            if ($term_sdt) {
                $arrMobile = explode(',', $term_sdt);
                $searchMobileConditions = array();
                if (!empty($arrMobile)) {
                    foreach ($arrMobile as $mobile) {
                        $mobile = trim($mobile);
                        if (strlen($mobile) >= $min_search_phone_number) {
                            $searchMobileConditions[] = 'orders.mobile LIKE ("'.$mobile.'%")';
                            $searchMobileConditions[] = 'orders.mobile2 LIKE ("'.$mobile.'%")';
                            if($mobile[0] && (Session::get('account_type') != TONG_CONG_TY || !AdminOrders::$master_group_id)){
                                $searchMobileConditions[] = 'orders.mobile LIKE ("0'.$mobile.'%")';
                            }
                        } else {
                            $arrErrors['Vui lòng nhập SDT tối thiểu '.$min_search_phone_number.' số'][] = $mobile;
                        }
                    }
                }
                $searchConditions[] = '(' . implode(' OR ', $searchMobileConditions) . ')';
            }

            //  OrderID
            if($term_order_id && strlen($term_order_id) > 2){
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
            } elseif($term_order_id) {
                $arrErrors['Vui lòng nhập mã đơn hàng tối thiểu 3 ký tự'][] = $term_order_id;
            }

            //  Ship ID
            if($term_ship_id && strlen($term_ship_id) > 2){
                $arrShipId = explode(',', $term_ship_id);
                $searchShipIdConditions = array();
                if (!empty($arrShipId)) {
                    foreach ($arrShipId as $ship_id) {
                        $ship_id = trim($ship_id);
                        $searchShipIdConditions[] = 'orders.postal_code = "'.$ship_id.'"';
                    }
                }
                $searchConditions[] = '(' . implode(' OR ', $searchShipIdConditions) . ')';
//                $searchConditions[] = 'orders.postal_code = "'.$term_ship_id.'"';
            }elseif($term_ship_id){
                $arrErrors['Vui lòng nhập mã vận đơn tối thiểu 3 ký tự'][] = $term_ship_id;
            }

            $cond .= $searchConditions ? ' AND ' . implode(' AND ', $searchConditions) : '';

        } else {
            if ($term_sdt && strlen($term_sdt) < $min_search_phone_number) {
                $arrErrors['Vui lòng nhập SDT tối thiểu '.$min_search_phone_number.' số'][] = $term_sdt;
            }
            if ($term_order_id && strlen($term_order_id) < 3) {
                $arrErrors['Vui lòng nhập mã đơn hàng tối thiểu 3 ký tự'][] = $term_order_id;
            }
            if ($term_ship_id && strlen($term_ship_id) < 3) {
                $arrErrors['Vui lòng nhập mã vận đơn tối thiểu 3 ký tự'][] = $term_ship_id;
            }
        }

//      $min_search_phone_number = get_group_options('min_search_phone_number');
//      $min_search_phone_number = $min_search_phone_number?$min_search_phone_number:3;
//      if($keyword = trim(addslashes(URL::get('keyword'))) and strlen($keyword) >= 2){
//          $keyword = preg_replace('/\"/', '', $keyword);
//          if(strlen($keyword)>=$min_search_phone_number){
//              if(Session::get('account_type')==TONG_CONG_TY or AdminOrders::$master_group_id){
//                  $cond .= ' AND (
//                                      (orders.postal_code) = "'.$keyword.'"
//                                      or orders.id = '.intval($keyword).'
//                                      or (orders.code) = "'.$keyword.'"
//
//                                      or (
//                                          (orders.mobile) LIKE ("'.$keyword.'%")
//                                          OR (orders.mobile2) LIKE ("'.$keyword.'%")
//                                      )
//                                  )';
//              }else{
//                  $cond .= ' AND (
//                      (orders.postal_code) = "'.$keyword.'"
//                      or orders.id = '.intval($keyword).'
//                      or (orders.code) = "'.$keyword.'"
//
//                      or (
//                          (orders.mobile) LIKE ("0'.$keyword.'%")
//                          OR (orders.mobile) LIKE ("'.$keyword.'%")
//                          OR (orders.mobile2) LIKE ("'.$keyword.'%")
//                      )
//                  )';
//              }
//              //  or orders.note2 like "%'.$keyword.'%" or orders.note1 like "%'.$keyword.'%"
//              //OR (orders.mobile2) LIKE ("'.$keyword.'%")
//              //OR (orders.customer_name) LIKE ("'.$keyword.'")
//          }else{
//              $cond .= ' AND (
//                  orders.postal_code = "'.$keyword.'"
//                  or orders.id = '.intval($keyword).'
//                  or orders.code = "'.$keyword.'"
//              )';
//          }
//      }
        if($c_n = trim(addslashes(URL::get('customer_name')))){
            $c_n = preg_replace('/\"/', '', $c_n);
            if(strlen($c_n)>=2){
                $cond .= ' AND orders.customer_name LIKE "%'.$c_n.'%"';
                //$cond .= ' AND MATCH(orders.customer_name) AGAINST ("'.$c_n.'" IN NATURAL LANGUAGE MODE)';
            }
        }
        if ($arrErrors) return $arrErrors;
        return $cond;
    }
    function get_items($cond,$order_by,$item_per_page=false){
        $statuses = AdminOrdersDB::get_status();
        if($ids = Url::get('checked_order') or $ids=Url::get('ids')){
            $item_per_page = false;
        }
        $districts = AdminOrdersDB::get_districts();
        $wards = AdminOrdersDB::get_wards();
        require_once 'packages/core/includes/utils/paging.php';
        $master_group_id = AdminOrders::$master_group_id;
        $group_id = Session::get('group_id');
        $group = DB::fetch('select id,account_type,prefix_post_code from `groups` where id='.$group_id);
        $show_full_name = get_group_options('show_full_name');
        $join = '';
        if(
            (Url::get('ngay_chuyen_kt_from') or Url::get('ngay_chuyen_kt_to'))
            or  (Url::get('ngay_thanh_cong_from') or Url::get('ngay_thanh_cong_to'))
            or  (Url::get('ngay_thu_tien_from') or Url::get('ngay_thu_tien_to'))
        ){
            $join .= ' LEFT JOIN orders_extra ON orders_extra.order_id=orders.id';
        }
        $sql = '
            SELECT
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
                orders.user_confirmed,
                orders.confirmed,
                IFNULL(orders.user_created, 0) AS orders_user_created,
                IFNULL(orders.user_confirmed, 0) AS orders_user_confirmed,
                IFNULL(orders.user_delivered, 0) AS orders_user_delivered,
                DATE_FORMAT(orders.confirmed,"%d/%m/%Y %H:%i:%s") AS orders_confirmed,
                DATE_FORMAT(orders.delivered,"%d/%m/%Y %H:%i:%s") AS orders_delivered,
                DATE_FORMAT(orders.created,"%d/%m/%Y %H:%i:%s") AS orders_created,
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
                "" as status_color,     
                groups.name as group_name,
                orders.district_id,
                "" AS district_reciever,
                orders.ward_id,
                "" AS ward,
                (SELECT shipping_services.name FROM shipping_services WHERE shipping_services.id = orders.shipping_service_id) shipping_service,
                (SELECT party.label FROM party JOIN users ON users.username=party.user_id WHERE users.id=orders.user_assigned limit 0,1) AS label,
                (SELECT '.(($show_full_name==1)?'users.name':'users.username').' FROM users WHERE users.id=orders.user_assigned limit 0,1) AS user_assigned,
                (SELECT '.(($show_full_name==1)?'users.name':'users.username').' FROM users WHERE users.id=orders.user_confirmed limit 0,1) AS user_confirmed_name,
                (SELECT '.(($show_full_name==1)?'users.name':'users.username').' FROM users WHERE users.id=orders.user_created limit 0,1) AS user_created,
                (SELECT '.(($show_full_name==1)?'users.name':'users.username').' FROM users WHERE users.id=orders.user_delivered limit 0,1) AS user_delivered_name,
                (SELECT '.(($show_full_name==1)?'upsale_user.name':'upsale_user.username').' FROM orders_extra JOIN users as upsale_user ON upsale_user.id = orders_extra.upsale_from_user_id WHERE upsale_user.id=orders_extra.upsale_from_user_id AND orders_extra.order_id = orders.id limit 0,1) AS upsale_from_user_id
            FROM
                orders
                JOIN `groups` ON groups.id = orders.group_id
                '.$join.'
                '.(Url::sget('product_code')?' JOIN orders_products ON orders_products.order_id=orders.id JOIN products ON products.id=orders_products.product_id':'').'
                '.($master_group_id?'
                    LEFT JOIN users as UA ON UA.id=orders.user_assigned
                    LEFT JOIN groups as G1 ON G1.id = UA.group_id
                ':'').'
            WHERE
                '.$cond.'
            ORDER BY
                '.$order_by.'
            '.($item_per_page?' LIMIT '.((page_no()-1)*$item_per_page).','.$item_per_page.'':'').'
        ';
        $items = DB::fetch_all($sql);
        $i=1;
        $duplicate_type = get_group_options('duplicate_type');
        $show_product_detail = get_group_options('show_product_detail');
        $length = AdminOrders::$hide_phone_number;
        foreach($items as $key =>$value){
            $items[$key]['customer_name'] = MiString::string2js($value['customer_name']);
            $items[$key]['address'] = MiString::string2js($value['address']);
            $items[$key]['note1'] = MiString::string2js($value['note1']);
            $items[$key]['note2'] = MiString::string2js($value['note2']);
            $items[$key]['note'] = MiString::string2js($value['note']);
            $no_style = true;
            if((Url::get('act')!='print' and Url::get('cmd')!='export_excel') and Url::get('cmd') != 'export_ship_excel' and Url::get('cmd') != 'quick_edit' ){
                $no_style = false;
            }
            /*$email = $value['email'];
            if($no_style==false){
                $email = '<span class="label label-default">'.$value['email'].'</span>';
            }
            $items[$key]['email'] = $email;*/
            $items[$key]['district_reciever'] = ($value['district_id'] and isset($districts[$value['district_id']]))?$districts[$value['district_id']]:'';
            $items[$key]['ward'] = ($value['ward_id'] and isset($wards[$value['ward_id']]))?$wards[$value['ward_id']]:'';
            //if(!$value['master_group_id'] or $value['group_id'] != $group_id)
            //{
            //$items[$key]['group_name'] = '';
            //}
            /*if($value['user_delivered'] and $user_delivered=DB::fetch('select id,name,username from users where id='.$value['user_delivered'])){
                $items[$key]['user_delivered'] = $user_delivered['username'];
            }*/
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
            $mobileResult = '';
            $mbl1 = $value['mobile'];
            $mobileResult .= ModifyPhoneNumber::hidePhoneNumber($mbl1, $length);
            $mbl2 = $value['mobile2'];
            if($mbl2){
                $mobileResult .= ' '. ModifyPhoneNumber::hidePhoneNumber($mbl2, $length);
            }

            $items[$key]['mobile'] = $mobileResult;
            //----------------- end xu ly hien thi mobile ---------------------

            if($item_per_page){
                $index = $i + $item_per_page*(page_no()-1);
            }else{
                $index = $i;
            }
            $items[$key]['index'] = $index;
            /*$products = AdminOrdersDB::get_order_product($key);
            $items[$key]['detail_products'] = $products;
            $product_str = '';
            $j = 0;
            foreach($products as $k=>$v){
                if($show_product_detail){
                    $product_str .= (($j>0)?'<br> ':'').$v['qty'].' '.$v['code'].' - '.$v['name'].''.($v['size']?' size '.$v['size'].'':'').''.($v['color']?' mầu '.$v['color'].'':'');
                }else{
                    $product_str .= (($j>0)?'<br> ':'').$v['qty'].' '.$v['name'].''.($v['size']?' size '.$v['size'].'':'').''.($v['color']?' mầu '.$v['color'].'':'');
                }
                $j++;
            }
            $items[$key]['products'] = $product_str;*/
            $source_name = $value['source_name'].' ('.(($value['type']==2)?'CSKH':'SALE').')';
            $items[$key]['source'] = $source_name;

            $items[$key]['confirmed'] = $value['orders_confirmed'];
            $items[$key]['delivered'] = $value['orders_delivered'];
            $items[$key]['created'] = $value['orders_created'];

            $items[$key]['user_created'] = $value['orders_user_created'];
            $items[$key]['user_confirmed'] = $value['orders_user_confirmed'];
            $items[$key]['user_delivered'] = $value['orders_user_delivered'];

            $items[$key]['code'] = str_pad($key,6,'0',STR_PAD_LEFT);
            $items[$key]['total_price'] = System::display_number($value['total_price']);
            $items[$key]['editting'] = AdminOrdersDB::is_edited($key);
            $i++;
        }
        return ($items);
    }
}
?>
