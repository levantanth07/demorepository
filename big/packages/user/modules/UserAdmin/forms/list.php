<?php
class ListUserAdminForm extends Form
{
    function __construct()
    {
        Form::Form('ListUserAdminForm');
        $this->link_css('assets/default/css/cms.css');
        $this->link_js('assets/standard/js/multiple.select.js');
        $this->link_css('assets/standard/css/multiple-select.css');
    }
    function on_submit()
    {
        if(URL::get('confirm'))
        {
            return;
            foreach(URL::get('selected_ids') as $id)
            {
                DB::delete('party','user_id = "'.$id.'"');
                DB::delete('account_privilege','account_id = "'.$id.'"');
                ///
                $user_id = DB::fetch('select id from users where username="'.$id.'"','id');
                DB::delete('users','username = "'.$id.'"');
                DB::delete('users_roles','user_id = '.$user_id.'');
                ///
                DB::delete('account_privilege','account_id = "'.$id.'"');
                ///
                $group = DB::fetch('select id,name from `groups` where code = "'.$id.'"');
                $group_id = $group['id'];
                $group_name = $group['name'];
                if(User::can_admin(false,ANY_CATEGORY) and $group_id){
                    DB::delete('fb_pages','group_id = '.$group_id.'');
                    DB::delete('fb_cron_config','group_id = '.$group_id.'');
                    
                    DB::delete('fb_post_comments','group_id = '.$group_id.'');
                    
                    DB::delete('fb_customers','group_id = '.$group_id.'');
                    
                    DB::delete('fb_conversation','group_id = '.$group_id.'');
                    
                    DB::delete('fb_conversation_messages','group_id = '.$group_id.'');
                    
                    DB::delete('roles','group_id = '.$group_id.'');
                    //roles_perms
                    
                    DB::delete('statuses','group_id = '.$group_id.'');

                    DB::delete('groups','id = '.$group_id.'');
                }
                ///
                DB::delete_id('account',$id);
                //save_log($id);
                System::log('DELETE','Xóa tài khoản','Xóa tài khoản "'.$id.'" của group '.$group_name);
            }
            require_once 'packages/core/includes/system/update_privilege.php';
            make_privilege_cache();
            //die;
            Url::js_redirect(true,'Đã xóa thành công');
        }
    }
    function draw()
    {
        $this->map = array();
        
        // sync from callio
        $totalCallioUser = 0;
        $callioClientUsers = array();
        $callioClientUsersBlock = array();
        $groupDetail = DB::select_id('groups', Session::get('group_id'));
        $integrate_callio = ($groupDetail['integrate_callio'] && $groupDetail['callio_info']) ? 1 : 0;
        $integrate_voip24h = ($groupDetail['integrate_voip24h'] && $groupDetail['voip24h_info']) ? 1 : 0;
        if ($integrate_callio) {
            $callio_info = json_decode($groupDetail['callio_info']);
            $url = CALLIO_AGENCY_HOST . '/client-user?page=1&pageSize=1000&client='.$callio_info->id;
            $dataRes = EleFunc::callioGet($url, array());
            $resClientUsers = $dataRes['docs'];
            if (!empty($resClientUsers)) {
                $totalCallioUser = count($resClientUsers);
                foreach ($resClientUsers as $user) {
                    $email = substr($user['email'], 0, strpos($user['email'], '@'));
                    $email = explode('_', $email);
                    if (count($email) === 3) {
                        if ($user['active']) {
                            $callioClientUsers[$email[2]] = json_encode($user, JSON_UNESCAPED_UNICODE);
                        } else {
                            $callioClientUsersBlock[$email[2]] = json_encode($user, JSON_UNESCAPED_UNICODE);
                        }
                    }
                }
            }
        }
        $callioClientUsersID = array_keys($callioClientUsers);
        $callioClientUsersBlockID = array_keys($callioClientUsersBlock);
        //
        
        $selected_ids="";
        if(URL::get('selected_ids'))
        {
            $selected_ids=URL::get('selected_ids');
            foreach($selected_ids as $key=>$selected_id)
            {
                $selected_ids[$key]='"'.DB::escape($selected_id).'"';
            }
        }
        
        $keyword = DB::escape(Url::get('keyword'));
        $cond = 'account.id<>"admin"';
        $cond .= !User::is_admin() ? ' AND account.group_id='.Session::get('group_id') : '';

        $cond .= ((URL::get('cmd')=='delete' and is_array(URL::get('selected_ids')))?' and `account`.id in ("'.implode('", "', $selected_ids).'")':'')
                .(Url::get('keyword')?' and (users.id = '.Url::iget('keyword').' or groups.name LIKE "%'.$keyword.'%" OR account.id like CONVERT( _utf8 "%'.$keyword.'%" USING latin1) OR party.full_name like "%'.$keyword.'%" OR users.phone like "%'.$keyword.'%")':'')
                .(Url::get('expired_month')?' and (groups.expired_date >="'.date('Y').'-'.Url::get('expired_month').'-01 00:00:00" AND groups.expired_date <="'.date('Y').'-'.Url::get('expired_month').'-'.(date('t',strtotime(''.date('Y').'-'.Url::get('expired_month').'-01'))).' 00:00:00")':'')
                .(Url::get('system_group_id')?' and groups.system_group_id='.Url::iget('system_group_id').'':'')
                .(($account_group_id=Url::iget('account_group_id'))?' and account.account_group_id='.$account_group_id:'')
        ;
        if(Url::get('_not_is_active')){
            $cond .= ' AND (account.is_active=0 or account.is_active is null)';
        }else{
            $cond .= ' AND account.is_active=1';
        }
        
        $vaccinationCond = [];

        $vaccination_count = URL::getInt('vaccination_count', -1);
        if($vaccination_count > -1){
            $vc = '`vaccination`.`count` = ' . $vaccination_count; 
            $vc = !$vaccination_count ? '(' . $vc . ' OR `vaccination`.`count` IS NULL)' : $vc;
            $vaccinationCond[] = $vc;
        }

        $vaccination_status = URL::getInt('vaccination_status', -1);
        if($vaccination_status > -1){
            $vc = '`vaccination`.`status` = ' . $vaccination_status; 
            $vc = !$vaccination_status ? '(' . $vc . ' OR `vaccination`.`status` IS NULL)' : $vc;
            $vaccinationCond[] = $vc;
        }
        $item_per_page = 20;
        if(Url::iget('item_per_page')){
            $item_per_page = Url::iget('item_per_page');
        }
        if($item_per_page < 5) $item_per_page = 5;
        if($item_per_page > 200) $item_per_page = 200;
        $this->map['item_per_page'] = $item_per_page;
        $sql = '
            select 
                count(`account`.id) as acount
            from
                `account`
                join `party` on `party`.user_id=`account`.id
                join `users` on `users`.username=`account`.id
                left join `groups` on `groups`.id=`account`.`group_id`
                left join `groups_system` on `groups_system`.id=`groups`.`system_group_id`
                left join `account_group` on `account_group`.id=`account`.`account_group_id`
            where
                '.$cond.'
            limit 0,1
        ';
        $count = DB::fetch($sql);
        require_once 'packages/core/includes/utils/paging.php';
        $paging = paging($count['acount'],$item_per_page,10,false,'page_no',array('_not_is_active','keyword','system_group_id','expired_month','account_group_id', 'item_per_page'));
        $sql = '
            select
                `account`.id
                ,`account`.group_id
                ,account.admin_group
                ,groups.name as group_name
                ,groups.code
                ,`account`.`password` ,
                `party`.`email` ,
                `party`.full_name ,
                `party`.`birth_date`,
                `account`.`create_date`,
                `users`.`phone` as `phone_number`
                ,account.is_active AS active
                ,IF(`party`.`gender`=1, "Nam","Nữ") as gender
                ,IF(`account`.`is_block`=1,"Có","Không") as `block`
                ,party.label
                ,(select name from zone where zone.id=zone_id) AS zone_name
                ,groups_system.name as master_group
                ,groups.expired_date
                ,users.id as users_id
                ,account.last_online_time
                ,account.last_ip
                ,users.created
                ,users.callio_info
                ,users.integrate_callio
                ,users.voip24h_info
                ,users.integrate_voip24h
                ,(select name from users as created_user where created_user.id = users.user_created) as user_created
                ,users.extension
                ,account.account_group_id
                ,users.rated_point
                ,users.rated_quantity
                ,users.identity_card
                ,account_group.name as account_group_name
            from
                `account`
                join `party` on `party`.user_id=`account`.id
                join `users` on `users`.username=`account`.id
                ' . ($vaccinationCond ? 'left join `vaccination` on `users`.id=`vaccination`.user_id' : '') . '
                left join `groups` on `groups`.id=`account`.`group_id`
                left join `groups_system` on `groups_system`.id=`groups`.`system_group_id`
                left join `account_group` on `account_group`.id=`account`.`account_group_id`
            where
                '.$cond.'
                ' . ($vaccinationCond ? ' AND ' . implode(' AND ', $vaccinationCond) : '') . '  
            '.(URL::get('order_by')?'order by '.DB::escape(URL::get('order_by')).(URL::get('order_dir')?' '.DB::escape(URL::get('order_dir')):''):(Url::get('expired_month')?'order by groups.expired_date desc':'order by users.id desc')).'
            limit '.((page_no()-1)*$item_per_page).','.$item_per_page.'
        ';
        //System::debug($sql);
        $items = DB::fetch_all($sql);
        $userIds = [];
        $shop_ids = [];
        foreach ($items as $k => $v) {
            $userIds[] = $v['users_id'];
            $shop_ids[] = $v['group_id'];
        }

        $sql = '
                select 
                    account_group.*,
                    account_group_admin.user_id as leader_id
                from 
                    account_group
                    LEFT JOIN account_group_admin on account_group.id = account_group_admin.account_group_id
                WHERE
                    account_group.group_id in('.implode(', ', $shop_ids).') 
                GROUP BY
                    account_group.id
                order by 
                    account_group.id  DESC';
        $mi_account_group = DB::fetch_all($sql);
        $this->map['account_groups'] = $mi_account_group;


        $group_id = UserAdmin::$group_id;
        $strUserIds = implode(',', $userIds);
        
        $queryAccountGroup = $this->getAccountGroup($strUserIds, $group_id);
        $queryAccountDepartmentGroup = $this->getAccountDepartmentGroup($strUserIds, $group_id);
        $i=1;
        foreach ($items as $key=>$value)
        {
            $items[$key]['i']=$i++;
            $account_groups_str = '';
            $account_groups_department_str = '';
            foreach ($queryAccountGroup as $kAcc => $valAcc) {
                if($valAcc['admin_user_id'] == $value['users_id']){
                    $account_groups_str .= ($account_groups_str?', ':'').$valAcc['name'];
                }
            }
            foreach ($queryAccountDepartmentGroup as $kAccD => $valAccD) {
                if($valAccD['user_id'] == $value['users_id']){
                    $account_groups_department_str .= ($account_groups_department_str?', ':'').$valAccD['name'];
                }
            }
            $items[$key]['account_group'] = $account_groups_str;
            $items[$key]['account_group_department'] = $account_groups_department_str;
            $items[$key]['rated_point'] = ($value['rated_point']>0?'<span class="small text-warning">'.round($value['rated_point'],2).'<i class="fa fa-star"></i>('.$value['rated_quantity'].')</span>':'').'';
            $page_name = DB::fetch('select id,page_name from fb_pages WHERE account_id="'.$value['id'].'"','page_name');    
            $items[$key]['page_name'] = $page_name;
            $items[$key]['roles'] = UserAdminDB::get_roles_of_the_user($value['id'],$value['group_id']);
            $total_confirmed_order = '';//DB::fetch('select count(*) as total from orders WHERE user_confirmed='.$value['users_id'],'total');
            //or user_assigned='.$value['users_id'].' or user_created='.$value['users_id'].'
            $items[$key]['total_order'] = $total_confirmed_order;
            $items[$key]['is_online'] = User::is_online($key)?'ON':'OFF';
            $items[$key]['last_online_time'] = $value['last_online_time']?'<span class="small">Online gần nhất: '.date('H:i\' d/m/Y',$value['last_online_time']).'<br>tại IP: '.$value['last_ip'].'</span>':'';

            // view callio + voip24h
            $user_integrate_callio = (in_array($value['users_id'], $callioClientUsersID) || $value['integrate_callio']) ? 1 : 0;
            $callio_info = $value['callio_info'];
            if ($user_integrate_callio && !$value['callio_info']) {
                $callio_info = $callioClientUsers[$value['users_id']];
                $rowUpdate = array(
                    'integrate_callio' => 1,
                    'callio_info' => $callio_info
                );
                DB::update_id('users', $rowUpdate, $value['users_id']);
            }
            if ($user_integrate_callio && !$value['integrate_callio']) {
                $rowUpdate = array(
                    'integrate_callio' => 1
                );
                DB::update_id('users', $rowUpdate, $value['users_id']);
            }
            if (in_array($value['users_id'], $callioClientUsersBlockID) && $user_integrate_callio) {
                $rowUpdate = array(
                    'integrate_callio' => 0
                );
                DB::update_id('users', $rowUpdate, $value['users_id']);
                $user_integrate_callio = 0;
            }

            $voip24h_info = $value['voip24h_info'];
            $items[$key]['callio_info'] = $callio_info ? true : false;
            $items[$key]['integrate_callio'] = $user_integrate_callio;
            $items[$key]['voip24h_info'] = $voip24h_info ? true : false;
            $items[$key]['integrate_voip24h'] = (int) $value['integrate_voip24h'];
            $items[$key]['users_id'] = $value['users_id'];
            if ($callio_info) {
                $callio_info = json_decode($callio_info);
                $items[$key]['callio_info_email'] = $callio_info->email;
                $items[$key]['callio_info_ext'] = $callio_info->ext;
                $items[$key]['callio_info_id'] = $callio_info->id;
            } else {
                $items[$key]['callio_info_email'] = '';
                $items[$key]['callio_info_ext'] = '';
                $items[$key]['callio_info_id'] = '';
            }
            if ($voip24h_info) {
                $voip24h_info = json_decode($voip24h_info);
                $items[$key]['voip24h_info_line'] = $voip24h_info->line;
                $items[$key]['voip24h_info_password'] = $voip24h_info->password;
            } else {
                $items[$key]['voip24h_info_line'] = '';
                $items[$key]['voip24h_info_password'] = '';
            }
            //
        }
        
        if(System::get_client_ip_env()=='171.224.178.151'){
            //System::debug($sql);
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
        $this->map['system_group_id_list'] = array(''=>'Chọn hệ thống') + MiString::get_list($groups_system);
        $expired_month = array();
        for($i=1;$i<12;$i++){
            $expired_month[$i] = 'Tháng '.$i;
        }
        $this->map['expired_month_list'] = array('Chọn tháng hết hạn') + $expired_month;
        
        $this->map['vaccination_count_list'] = [
            '-1' => 'Số mũi tiêm'
        ] + UserAdmin::getVaccinationCountFields();

        $this->map['vaccination_status_list'] = [
            -1 => 'Tình trạng sức khỏe',
        ] + UserAdmin::getVaccinationStatusFields();

        $this->map['vaccination_count'] = $_REQUEST['vaccination_count'] ?? - 1;
        $this->map['vaccination_status'] = $_REQUEST['vaccination_status'] ?? -1;
        
        $this->map['account_group_id_list'] = [''=>'Tất cả các nhóm'] + MiString::get_list($this->get_account_groups());
        $this->parse_layout('list',$just_edited_id+$this->map +
            array(
                'total'=>$count['acount'],
                'items'=>$items,
                'paging'=>$paging,
                'totalCallioUser'=>$totalCallioUser,
                'integrate_callio'=>$integrate_callio,
                'integrate_voip24h'=>$integrate_voip24h,
                'callioClientUsers'=>$callioClientUsers,
                'total_group'=>UserAdminDB::get_total_group()
            )
        );
    }
    function get_account_groups(){
        return $groups = DB::fetch_all('select id,name from account_group where group_id='.Session::get('group_id').' order by name');
    }
    function getAccountGroup($strUserIds, $group_id){
        $cond = !User::is_admin() ? 'account_group.group_id='.$group_id : '1=1';
        $sql = "SELECT 
                        id,name,
                        admin_user_id 
                FROM 
                        account_group 
                WHERE   
                        $cond
                AND 
                    admin_user_id IN ($strUserIds)";
        $data = DB::fetch_all($sql);
        return $data;
    }
    function getAccountDepartmentGroup($strUserIds, $group_id){
        $cond = !User::is_admin() ? ' account_group.group_id='.$group_id : '1=1';
        $sql = "SELECT 
                        account_group_admin.id,
                        account_group.name,
                        account_group_admin.user_id 
                FROM 
                        account_group_admin 
                JOIN    
                        account_group ON account_group_admin.account_group_id =  account_group.id 
                WHERE 
                    $cond
                AND 
                    account_group_admin.user_id IN ($strUserIds)";
        $data = DB::fetch_all($sql);
        return $data;
    }
}
?>
