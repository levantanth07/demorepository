<?php
class ListShopForm extends Form
{
    function __construct()
    {
        $this->link_css('assets/admin/scripts/bootstrap_datepicker/css/bootstrap-datepicker.min.css');

        Form::Form('ListShopForm');
        if (URL::get('do') == 'save_package' && !empty($_POST) && Url::post('group_id')) {
            require_once ROOT_PATH . '/packages/vissale/modules/AdminShop/AccountPackageManager.php';
            return new AccountPackageManager();
        }

        if (URL::get('do') == 'delete_history_package' && Url::get('id')) {
            $data['success'] = false;
            try {
                DB::delete_id('acc_packages_groups', Url::get('id'));
                $data['success'] = true;
            } catch (Exception $e) {
                
            }
            
            echo json_encode($data); die();
        }

        if (URL::get('do') == 'get_history_package' && Url::get('group_id')) {
            $html = '<div class="text-center text-danger">Chưa có dữ liệu</div>';
            $data = [];
            $items = AdminShopDB::getHistoryPackages(Url::get('group_id'));
            if (!empty($items)) {
                $html = '
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="50">STT</th>
                                <th>Gói cước</th>
                                <th>Số tháng</th>
                                <th class="text-right">Tổng tiền</th>
                                <th class="text-right">Chiết khấu</th>
                                <th class="text-right">Khách trả</th>
                                <th class="text-right">Khách trả Palion</th>
                                <th class="text-right">Ngày thanh toán Palion</th>
                                <th>Ngày tạo</th>
                                <th>Ngày nhập</th>
                                <th width="100">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                ';
                $i = 1; $total_price = 0;
                foreach ($items as $item) {
                    $html .= '
                        <tr>
                            <td>'. $i++ .'</td>
                            <td>'. $item['name'] .'</td>
                            <td>'. $item['months'] .'</td>
                            <td class="text-right"><b>'. number_format($item['discount'] + $item['total_price']) .'</b></td>
                            <td class="text-right"><b>'. number_format($item['discount']) .'</b></td>
                            <td class="text-right"><b>'. number_format($item['total_price']) .'</b></td>
                            <td class="text-right"><b>'. number_format($item['palion_price']) .'</b></td>
                            <td>'. $item['palion_paid_at'] .'</td>
                            <td>'. $item['date_at'] .'</td>
                            <td>'. $item['created_at'] .'</td>
                            <td><a href="javascript:void(0)" data-id="'. $item['id'] .'" class="btn btn-danger btn-delete-group">Xóa</a></td>
                        </tr>
                    ';

                    $total_price += $item['total_price'];
                }

                $html .= '
                    <tr>
                        <td colspan="5" class="text-center"><b>TỔNG</b></td>
                        <td class="text-right"><b>'. number_format($total_price) .'</b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                ';

                $html .= '</tbody></table>';
            }

            $data['success'] = true;
            $data['data'] = $html;

            echo json_encode($data, JSON_UNESCAPED_UNICODE); die();
        }
    }

    function date_expired_difference ($start_time, $end_time) {
        $val_1 = new DateTime($start_time);
        $val_2 = new DateTime($end_time);
        if ($val_1 < $val_2) {
            return [
                'js_date_expired' => date('d/m/Y',strtotime($end_time)),
                'text_expired' => 'Hết hạn'
            ];
        } elseif ($val_1 == $val_2) {
            return [
                'js_date_expired' => date('d/m/Y',strtotime($end_time)),
                'text_expired' => 'Hết hạn hôm nay'
            ];
        }

        $interval = $val_1->diff($val_2);
        $year     = $interval->y;
        $month    = $interval->m;
        $day      = $interval->d;

        $output   = 'Còn ';

        if ($year > 0) {
            $output .= $year." năm ";
        }

        if ($month > 0) {
            $output .= $month." tháng ";
        }

        if ($day > 0) {
            $output .= $day." ngày ";
        }

        return [
            'js_date_expired' => date('d/m/Y', strtotime($start_time)),
            'text_expired' => $output
        ];
    }

    function on_submit()
    {
        if(URL::get('confirm'))
        {
            foreach(URL::get('selected_ids') as $id)
            {
                $this->delete_group($id);
            }
            require_once 'packages/core/includes/system/update_privilege.php';
            //make_privilege_cache();
            //die;
            Url::js_redirect(true,'Đã xóa thành công', array('expired_month','order_by','order_dir','cmd'=>'shop'));
        }
    }

    function delete_users($group_id){
        $accounts = DB::select_all('account','group_id='.$group_id);
        DB::query('delete party.* from party INNER JOIN account ON account.id=party.user_id WHERE account.group_id = '.$group_id.'');

        DB::delete('account','group_id = '.$group_id.'');
        foreach($accounts as $id=>$val)
            {
                DB::delete('account_privilege','account_id = "'.$id.'"');
                ///
                $user_id = DB::fetch('select id from users where username="'.$id.'"','id');
                DB::delete('users','username = "'.$id.'"');
                DB::delete('users_roles','user_id = '.$user_id.'');
                ///
                DB::delete('account_privilege','account_id = "'.$id.'"');
                ///
                ///
                DB::delete_id('account',$id);
            }
    }

    function delete_group($group_id){
        $group = DB::fetch('select id,name from `groups` where id = "'.$group_id.'"');
        $group_name = $group['name'];
        if(User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY) and $group_id){
            $this->delete_users($group_id);
            //$this->delete_orders($group_id);
            DB::delete('fb_pages','group_id = '.$group_id.'');
            DB::delete('fb_cron_config','group_id = '.$group_id.'');
            
            DB::delete('fb_post_comments','group_id = '.$group_id.'');
            
            DB::delete('fb_customers','group_id = '.$group_id.'');
            
            DB::delete('fb_conversation','group_id = '.$group_id.'');
            DB::delete('orders_column_custom','group_id = '.$group_id.'');
            DB::delete('fb_conversation_messages','group_id = '.$group_id.'');
            
            DB::delete('roles','group_id = '.$group_id.'');
            //roles_perms
            
            DB::delete('statuses','group_id = '.$group_id.'');
            DB::delete('order_print_template','group_id = '.$group_id.'');
            DB::delete('order_source','group_id = '.$group_id.'');
            DB::delete('products','group_id = '.$group_id.'');
            DB::delete('api_keys','group_id = '.$group_id.'');

            DB::delete('groups','id = '.$group_id.'');
            System::log('DELETE','Xóa group','Xóa group '.$group_name);
        }
    }

    function draw()
    {
        if(!can_tuha_administrator()){
            Url::access_denied();
        }
        $this->map = array();
        //$selected_ids="";
        if(URL::get('selected_ids'))
        {
            $selected_ids=URL::get('selected_ids');
            foreach($selected_ids as $key=>$selected_id)
            {
                $selected_ids[$key]='"'.$selected_id.'"';
            }
        }
        $cond = '1=1'
            .((URL::get('cmd')=='delete_shop' and is_array(URL::get('selected_ids')))?' and `groups`.id in ("'.join(URL::get('selected_ids'),'","').'")':'')
            .(Url::iget('account_type')?' and groups.account_type='.Url::iget('account_type'):'')
            .(Url::iget('status')?' and groups.status='.Url::iget('status'):'')
            .(Url::get('keyword')?' and ( convert(groups.name using utf8) like "%'.addslashes(Url::get('keyword')).'%" or convert(groups.code using utf8) like "%'.addslashes(Url::get('keyword')).'%" or convert(groups.username using utf8) like "%'.addslashes(Url::get('keyword')).'%" or convert(groups.phone using utf8) like "%'.addslashes(Url::get('keyword')).'%")':'')
            .(Url::get('expired_month')?' and (groups.expired_date >="'.date('Y').'-'.Url::get('expired_month').'-01 00:00:00" AND groups.expired_date <="'.date('Y').'-'.Url::get('expired_month').'-'.(date('t',strtotime(''.date('Y').'-'.Url::get('expired_month').'-01'))).' 00:00:00")':'')
            .(Url::get('system_group_id')?' and groups.system_group_id='.Url::get('system_group_id').'':'');
        if(Url::iget('is_crm')){
            if(Url::iget('is_crm') == 1){
                $cond .= ' and groups.is_crm = 1';
            } else {
                $cond .= ' and (groups.is_crm IS NULL or groups.is_crm = 0)';
            }
        }
        
        if(Url::iget('status_palion')){
            if(Url::iget('status_palion') == 1 || Url::iget('status_palion') == 2){
                $cond .= ' and groups.palion_payment_status = '.Url::iget('status_palion');
            } else {
                $cond .= ' and groups.palion_payment_status IS NULL';
            }
            
        }
        if(User::is_admin()) {
            
        } else {
            if(Session::get('account_type')==TONG_CONG_TY){
                $cond .= ' and groups.master_group_id='.Session::get('group_id');
            }
        }   
            
        if(User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY)){
            //echo $cond;
        }
        $item_per_page = 20;
        DB::query($sql = '
            select count(groups.id) as total
            from
                `groups`
                left join groups_system on groups_system.id=groups.system_group_id
                left join `groups` as parent on parent.id=groups.master_group_id
                left join phone_store on phone_store.id=groups.phone_store_id
            where
                '.$cond.'
            limit 0,1
        ');
        $count = DB::fetch();
        $total = isset($count['total'])?$count['total']:0;
        require_once 'packages/core/includes/utils/paging.php';
        $paging = paging($total,$item_per_page,10,false,'page_no',array('user_id','cmd','keyword','account_type','system_group_id','expired_month','status','is_crm','status_palion'));
        DB::query('
            SELECT
                groups.id,
                groups.code,
                groups.name,
                groups.email,
                groups.phone,
                groups.created,
                groups.account_type,
                groups.active,
                groups.expired_date,
                groups.prefix_post_code,
                groups.image_url,
                groups.master_group_id,
                groups.user_counter,
                groups.page_counter,
                groups.description,
                groups.palion_payment_status,
                groups.palion_paid_at,
                groups.palion_price,
                groups.palion_expired_at,
                groups.is_crm,
                groups.status,
                "" AS total_order,
                (select count(account.id) as total from account WHERE group_id=groups.id) AS total_user,
                groups_system.name as system_group_name,
                parent.name as master_group_name,
                phone_store.name as phone_store_name,
                groups.package_id,
                acc_packages.name AS package_name
            FROM
                `groups`
                LEFT JOIN groups_system on groups_system.id=groups.system_group_id
                LEFT JOIN `groups` as parent on parent.id=groups.master_group_id
                LEFT JOIN phone_store on phone_store.id=groups.phone_store_id
                LEFT JOIN acc_packages ON acc_packages.id = groups.package_id
            WHERE
                '.$cond.'
            '.(URL::get('order_by')?'order by '.URL::get('order_by').(URL::get('order_dir')?' '.URL::get('order_dir'):''):(Url::get('expired_month')?'order by groups.expired_date desc':'order by groups.id desc')).'
            limit '.((page_no()-1)*$item_per_page).','.$item_per_page.'
        ');
        $items = DB::fetch_all();
        $i=1;
        $type_label = array(''=>'Chưa xác định','0'=>'Thường','1'=>'Dùng thử','2'=>'Cũ','3'=>'Hệ thống');
        $date_now = date('d-m-Y');
        foreach ($items as $key => &$value)
        {
            $items[$key]['i']=$i++;
            $items[$key]['admins'] = AdminShopDB::get_admins_of_shop($value['id']);
            $items[$key]['account_type'] = $type_label[$value['account_type']];
            $months = $this->getAccountPakage($value['id']);
            $items[$key]['months'] = $months ? $months['months'] : '';
            $expired_date = '';
            $period_date = '';
            $js_date_expired = date('d/m/Y',strtotime($value['expired_date']));
            if (is_set_date($value['expired_date'])) {
                $expired_date = date('d-m-Y', strtotime($value['expired_date']));
                $date_expired_difference = $this->date_expired_difference($expired_date, $date_now);
                $period_date = $date_expired_difference['text_expired'];
                $js_date_expired = $date_expired_difference['js_date_expired'];
            }

            $value['palion_expired_at'] = is_set_date($value['palion_expired_at']) ? date('d-m-Y', strtotime($value['palion_expired_at'])) : NULL_TIME;
            $value['palion_paid_at'] = is_set_date($value['palion_paid_at']) ? date('d-m-Y', strtotime($value['palion_paid_at'])) : NULL_TIME;

            $value['expired_date'] = $expired_date;
            $value['period_date'] = $period_date;
            $value['js_date_expired'] = $js_date_expired;
        }
        
        $just_edited_id['just_edited_ids'] = array();
        if (UrL::get('selected_ids'))
        {
            if(is_string(UrL::get('selected_ids')))
            {
                if (strstr(UrL::get('selected_ids'),','))
                {
                    $just_edited_id['just_edited_ids']=explode(',',UrL::get('selected_ids'));
                }
                else
                {
                    $just_edited_id['just_edited_ids']=array('0'=>UrL::get('selected_ids'));
                }
            }
        }
        $groups_system = DB::fetch_all('select id,name,structure_id from groups_system where 1=1 order by structure_id');
        $this->map['system_group_id_list'] = array(''=>'Thuộc hệ thống') + MiString::get_list($groups_system);
        $expired_month = array();
        for($i=1;$i<=12;$i++){
            $expired_month[$i] = 'Tháng '.$i;
        }
        $this->map['expired_month_list'] = array('Tài khoản hết hạn tháng') + $expired_month;
        ///////
        $account_types =  array(''=>'Loại tài khoản',0=>'Tài khoản thường',1=>'Dùng thử',2=>'Tài khoản cũ');
        $isCrm =  array(0=>'Đồng bộ CRM',1=>'Đã đồng bộ',2=>'Chưa đồng bộ');
        $statusPal =  array(0=>'Trạng thái dùng Palion',1=>'Đã thanh toán',2=>'Dùng thử',3=>'Không sử dụng');
        $this->map['account_type_list'] = $account_types;
        $this->map['is_crm_list'] = $isCrm;
        $this->map['status_palion_list'] = $statusPal;
        ///////
        $this->map['actived_shop'] = AdminShopDB::get_total_actived_shop($cond);
        $this->map['expired_shop'] = AdminShopDB::get_total_expired_shop($cond);
        $this->map['good_shop'] = AdminShopDB::get_total_good_shop($cond);
        $packages = AdminShopDB::getPackages(false);
        $statusPalion = AdminShopDB::statusPalion();
        $this->map['status_id_list'] = '';
        foreach ($statusPalion as $key => $val) {
            $this->map['status_id_list'] .= '<option value = "'. $key .'">'.$val.'</option>';
        }
        $this->map['acc_packages'] = $packages;
        $this->parse_layout('shop', $just_edited_id + $this->map +
            array(
                'total'=>$total,
                'items'=>$items,
                'paging'=>$paging,
                    'total_group'=>AdminShopDB::get_total_group()
            )
        );
    }

    public function getAccountPakage($groupId)
    {
        $sql = "SELECT id,months FROM acc_packages_groups WHERE group_id = $groupId ORDER BY id DESC LIMIT 0,1";
        return DB::fetch($sql);
    }
}
?>
