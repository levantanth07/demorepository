<?php
date_default_timezone_set('Asia/Saigon');
define('DEVELOPING',false);
define( 'ROOT_PATH', strtr(dirname( __FILE__ ) ."/../",array('\\'=>'/')));

require_once 'JWT/JWT.php';
require_once ROOT_PATH.'packages/core/includes/system/config.php';

header("Access-Control-Allow-Origin: *");

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$cmd = $_GET['cmd']; 
if($cmd == 'manager_dashboard' or $cmd == 'user_dashboard' or $cmd == 'system_dashboard' or $cmd == 'check_system_user') {
    try { 
        $token = Url::sget('jwt'); 
        $orginalString = JWT::decode(
        $token,     
        TUHA_TOKEN,
        'HS512'    
        ); 
        $user = json_decode($orginalString);
    } catch(Exeption $exp) { 
        echo "Lỗi lấy thông tin user";
    }
}

switch ($cmd) {
    case 'check_system_user':
        is_system_user($user->user_id);
        break;
    case 'system_dashboard':
        echo json_encode(get_system_dashboard($user->user_id));
        break;
   case  'manager_dashboard': 
        if(Url::get('group_id')){
            $group_id = Url::get('group_id');
        }else{
            $group_id = $user->group_id;
        }
        //$group_id = 1414;
        echo json_encode(get_manager_dashboard($group_id));
        break;
   case  'user_dashboard':
        if(Url::get('id')){
            $id = Url::get('id');
        }else{
            $id=$user->id;
        }
        echo json_encode(get_user_dashboard($id));
        break;
}
function is_system_user($user_id){
    $user_id = DB::escape($user_id);
    $row = DB::fetch('select count(id) as total from groups_system_account where user_id='.$user_id.'','total');
    if($row['total']>0){
        echo json_encode(array('is_system_user'=>1));
    }else{
        echo json_encode(array('is_system_user'=>0));
    }
}
function get_system_dashboard($user_id){
    $user_id = DB::escape($user_id);
    $map =  array();
    $system_group_id = DB::fetch('select system_group_id from groups_system_account where user_id = '.$user_id,'system_group_id');
    if(!$system_group_id){
        echo '{"error":"'.$user_id.'"}';
        exit();
    }
    $map['ten_he_thong'] = DB::fetch('select name from `groups_system` where id='.$system_group_id,'name');
    $groups = DB::fetch_all('select id,name,address,image_url from `groups` where system_group_id = '.$system_group_id);
    $group_ids = implode(',',MiString::get_list($groups,'id'));
    //////////////////////////////////////////////////
    //return array('is_system_user'=>$group_ids);
    $month = Url::get('month')?Url::get('month'):date('m');
    $year = date('Y');
    $_REQUEST['date_to'] = date('d/m/Y');
    $dates = array();

    $start_time = date(''.$year.'-'.$month.'-01');
    $end_time = date('Y-m-t',strtotime($start_time));
    //////////////////////////////////////////////////
    $cond = ' group_id IN ('.$group_ids.') and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59"  and orders.status_id <> 9';
    $tong_don_xac_nhan = $map['tong_don_xac_nhan'] = DB::fetch('select count(*) as total from orders where '.$cond.' ','total');
    $tt_ds = DB::fetch('select sum(total_price) as total from orders where '.$cond.' ','total');
    $map['doanh_so'] = System::display_number($tt_ds);
    //ds huy
    $cond = ' group_id IN ('.$group_ids.') and orders.created>="'.$start_time.' 00:00:00 " and  orders.created<="'.$end_time.' 23:59:59"  and orders.status_id=9 ';    
    $tt_cancel = DB::fetch('select sum(total_price) as total from orders where '.$cond.'','total');
    if(($tt_ds + $tt_cancel)>0){
        $map['ty_le_chot']=number_format((float)($tt_ds*100)/($tt_ds + $tt_cancel), 2, '.', '').'%';// xu ly chia cho 0    
    }
    else{
        $map['ty_le_chot']='0';
    }
    $cond = ' group_id IN ('.$group_ids.') and orders.created>="'.$start_time.' 00:00:00" and  orders.created<="'.$end_time.' 23:59:59"  and orders.status_id =10';
    
    $tt_cxl = DB::fetch('select count(*) as total from orders where '.$cond.' group by user_confirmed','total');
    $map['chua_xu_ly'] = $tt_cxl?$tt_cxl:0;

    $map['tong_nhan_su'] = DB::fetch('select count(*) as total from account where group_id IN ('.$group_ids.') and is_active=1','total');
    
    $map['doanh_thu_cong_ty'] = array();
    foreach($groups as $key=>$val){
       $map['doanh_thu_cong_ty'][$key]['id'] = $key;
       $map['doanh_thu_cong_ty'][$key]['ten_cty'] = $val['name'];
       $cond = ' group_id = '.$key.' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59"  and orders.status_id <> 9';
       $tt_ds = DB::fetch('select sum(total_price) as total from orders where '.$cond.' ','total');
       $map['doanh_thu_cong_ty'][$key]['doanh_so'] = $tt_ds;
       $map['doanh_thu_cong_ty'][$key]['address'] = $val['address'];
       $map['doanh_thu_cong_ty'][$key]['image_url'] = $val['image_url'];
       if(sizeof($map['doanh_thu_cong_ty'])>1){
        //$this->sort_array_of_array();
            System::sksort($map['doanh_thu_cong_ty'], 'doanh_so','DESC');
            
        }
    }
    return $map;
}
function get_user_dashboard($acc_id){
    $acc_id = DB::escape($acc_id);
    ////bieu do cua tai khoan dang login
    $month = Url::get('month')?Url::get('month'):date('m');
    $year = date('Y');
    $date_from = date('01/m/Y');
    $date_to = date('d/m/Y');
    $dates = array();

    $start_time = date(''.$year.'-'.$month.'-01');
    $end_time = date('Y-m-t',strtotime($start_time));
    $map = array();
    $chart_per = array();
    if($user =  DB::fetch('
        SELECT
            account.id,party.full_name,account.group_id,users.id as user_id,
            party.image_url,groups_system.name as system_name,groups.name as group_name
        FROM
            account
            INNER JOIN users ON users.username = account.id
            INNER JOIN party ON party.user_id = account.id
            inner join `groups` on groups.id = account.group_id
            LEFT OUTER JOIN groups_system ON groups_system.id = groups.system_group_id
        WHERE
            account.id = "'.$acc_id.'" 
    ')){
        $user_id = $user['user_id'];
        $map['full_name'] = $user['full_name'];
        $map['ten_cty'] = $user['group_name'];
        $map['ten_he_thong'] = $user['system_name'];
        $map['avatar'] = $user['image_url']?'https://tuha.vn/'.$user['image_url']:'https://tuha.vn/assets/default/images/cms/header/icon-48-user.png';
        $group_id = $user['group_id'];
        $no_revenue_status = get_no_revenue_status($group_id);
        $map['ty_le_chot'] = 0;
        $map['comment'] = 0;
        $map['don_xac_nhan'] = 0;
        $map['don_chua_xu_ly'] = 0;
        $map['inbox'] = 0;
        if(strtotime($end_time) - strtotime($start_time)>0){
            $cond = ' orders.user_confirmed='.$user_id.' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59"  and orders.status_id = 5';
            $tong_don_xac_nhan = $map['don_xac_nhan'] = DB::fetch('select count(*) as total from orders where '.$cond.' ','total');
            $tt_ds = DB::fetch('select sum(total_price) as total from orders where '.$cond.' ','total');
            $map['doanh_so'] = System::display_number($tt_ds);

            $cond = 'group_id='.$group_id.' and orders.assigned>="'.$start_time.' 00:00:00" and orders.assigned<="'.$end_time.' 23:59:59" and orders.user_assigned='.$user_id.' and orders.status_id<>9';
            $tong_so_duoc_chia = DB::fetch('select count(id) as total from orders where '.$cond.'','total');
            $map['ty_le_chot']='0';
            $map['tong_so_duoc_chia'] = System::display_number($tong_so_duoc_chia);
            if($tong_so_duoc_chia>0){
                $map['ty_le_chot']=number_format((float)($tong_don_xac_nhan*100)/($tong_don_xac_nhan + $tong_so_duoc_chia), 0, '.', '').'';// xu ly chia cho 0    
            }
            $cond = ' orders.user_assigned='.$user_id.' and orders.assigned>="'.$start_time.' 00:00:00" and  orders.assigned<="'.$end_time.' 23:59:59"  and orders.status_id = 10';
            $cxl = DB::fetch('select sum(total_price) as total from orders where '.$cond.' group by user_assigned','total');
            $map['don_chua_xu_ly'] = $cxl?$cxl:0;
            ///
            $cond = 'group_id = '.$group_id.' and type="1" ';
            $map['comment'] = DB::fetch('select count(*) as total from fb_conversation where '.$cond,'total');
            $cond = 'group_id = '.$group_id.' and type="0" ';
            $map['inbox']=DB::fetch('select count(*) as total from fb_conversation where '.$cond,'total');
            for($i=strtotime($start_time);$i<=strtotime($end_time);$i=$i+24*3600){
                $end_time_1 = date('Y-m-d', $i);
                $cond_confirm = ' orders.confirmed>="'.$end_time_1.' 00:00:00" and  orders.confirmed<="'.$end_time_1.' 23:59:59" and orders.user_confirmed='.$user_id.' and orders.status_id = 5';
                $chart_per[$i]['doanh_so'] = (DB::fetch('select sum(total_price) as total from orders where '.$cond_confirm.' group by user_assigned','total'))/1000000;
                $don_hang = DB::fetch('select count(*) as total from orders where '.$cond_confirm.' group by user_assigned','total');
                $chart_per[$i]['don_hang'] = $don_hang?$don_hang:0;
                $chart_per[$i]['ngay'] =  date('Y-m-d', strtotime($end_time_1));
            }
        } 
    }
    $map['date_from'] = $date_from;
    $map['date_to'] = $date_to;
    $map['doanh_thu_ngay'] = $chart_per;
    return $map;
}
function get_manager_dashboard($group_id){
    $group_id = DB::escape($group_id);
    $map = array();
    $map['ten_cong_ty'] = DB::fetch('select name from `groups` where id='.$group_id,'name');
    //////////////////////////////////////////////////
    $month = Url::get('month')?Url::get('month'):date('m');
    $year = date('Y');
    $date_from = '01/'.date('m/Y');
    $date_to = date('d/m/Y');
    $dates = array();

    $start_time = date(''.$year.'-'.$month.'-01');
    $end_time = date('Y-m-t',strtotime($start_time));
   
    $status = get_report_statuses();
    $users = get_users('GANDON',$group_id);
    
    $reports = array();
    /*
    $reports['label']['id'] = 'label';
    $reports['label']['name'] = 'Nhân viên';
    $reports['label']['sl'] = 'SL';
    //$status[1000000000] = array('id'=>1000000000,'name'=>'Tổng','total'=>0,'qty'=>0);
    //$status[1000000000] = array('id'=>1000000000,'total_price'=>'Doanh thu','qty'=>'Số Lượng','name'=>'Tổng');
    foreach($users as $key=>$value){
        foreach($status as $k=>$v){
            $reports['label'][$k][1] = array('total_price'=>'Doanh thu','qty'=>'Số Lượng','name'=>$v['name']);
        }
    }
    $reports['label']['total'] = 'Tổng';
    // 7: đã xác nhận,9: hủy, 6: chuyển hoàn, 5 thành công
    */
    $no_revenue_status = get_no_revenue_status($group_id);
    $tt_ds = 0;
    foreach($users as $key=>$value){
        $reports[$key]['id'] = $key;
        $reports[$key]['name'] = $value['full_name'];
        $total_ = 0;
        $total_total_price = 0;
        $total_qty = 0;
        $cond = 'orders.group_id = '.$group_id.'';
        $cond .= ' and orders.status_id NOT IN ('.$no_revenue_status.')';
        $cond .= ' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59" and orders.user_confirmed='.$value['user_id'].' and orders.status_id = 5';
        $total_price = DB::fetch('select sum(total_price) as total from orders where '.$cond.' group by user_confirmed','total');
        $total_order = DB::fetch('select count(*) as total from orders where '.$cond.' group by user_confirmed','total');
        //$status[1000000000]['total'] += $total_total_price;
        //$status[1000000000]['qty'] += $total_qty;
       // $reports[$key][1000000000] = array(1=>array('total_price'=>System::display_number($total_total_price),'qty'=>System::display_number($total_qty),'name'=>'Tổng'));
        $reports[$key]['tong_tien'] = $total_price?$total_price:0;
        $tt_ds += $total_price;
        $reports[$key]['so_don'] = $total_order?$total_order:0;
        //$reports[$key]['turnover']=$total_total_price/1000000;
    }
    $map['doanh_so'] = System::display_number($tt_ds);
    $map['date_from'] = $date_from;
    $map['date_to'] = $date_to;
    if(sizeof($reports)>1){
    //$this->sort_array_of_array();
        System::sksort($reports, 'tong_tien','DESC');
        
    }
    $map['reports'] = $reports;
    $map['status'] = $status;
    //statistics
    $cond = 'group_id = '.$group_id.' and type="1" ';
    $map['tong_comment'] = DB::fetch('select count(*) as total from fb_conversation where '.$cond,'total');
    $cond = 'group_id = '.$group_id.' and type="0" ';
    $map['tong_inbox']=DB::fetch('select count(*) as total from fb_conversation where '.$cond,'total');

    //$cond = 'orders.group_id = '.$group_id.'';
    $cond = ' group_id='.$group_id.' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59"  and orders.status_id <> 9';
    $tong_don_xac_nhan = DB::fetch('select count(*) as total from orders where '.$cond.' ','total');
    $map['tong_don_xac_nhan'] = $tong_don_xac_nhan?System::display_number($tong_don_xac_nhan):0;
    $cond = 'group_id='.$group_id.' and orders.assigned>="'.$start_time.' 00:00:00" and orders.assigned<="'.$end_time.' 23:59:59"  and orders.status_id<>9';
    $tong_so_duoc_chia = DB::fetch('select count(id) as total from orders where '.$cond.'','total');
    $map['ty_le_chot']='0%';
    $map['tong_so_duoc_chia'] = System::display_number($tong_so_duoc_chia);
    if($tong_so_duoc_chia>0){
        $map['ty_le_chot']=number_format((float)($tong_don_xac_nhan*100)/($tong_don_xac_nhan + $tong_so_duoc_chia), 0, '.', '').'%';// xu ly chia cho 0    
    }
    $cond = ' orders.group_id = '.$group_id.' and orders.created>="'.$start_time.' 00:00:00" and  orders.created<="'.$end_time.' 23:59:59"  and orders.status_id=10';
    $tt_cxl = DB::fetch('select count(*) as total from orders where '.$cond.' group by user_confirmed','total');
    $map['chua_xu_ly'] = $tt_cxl?$tt_cxl:0;
    return $map;
}
function get_report_statuses(){
    return DB::fetch_all('select statuses.id,statuses.name,0 as total,0 as qty,(IF(statuses.id=7,10,statuses.id)) as order_by from statuses where (id=5 or id=6 or id=7 or id=9) order by order_by DESC');
}
function get_report_info($account_id){
    $party = DB::fetch('select full_name,note1,note2,address,phone,website from party WHERE user_id="'.$account_id.'" ');
    return $party;
}
function get_users($code='GANDON',$group_id,$check_is_active=false){
    /*if($account_group_id=Url::iget('account_group_id')){
        $cond = 'account.account_group_id='.$account_group_id;
    }else{
        $cond = '1=1';
    }
    if($check_is_active){
        if($check_is_active==1){
            $cond .= ' and (account.is_active = 0 or account.is_active is null)';
        }
    }else{
        $cond .= ' and account.is_active';
    }*/
    $cond = 'account.is_active';
    return DB::fetch_all('
        SELECT
            account.id,party.full_name,account.group_id,users.id as user_id
        FROM
            account
            INNER JOIN party ON party.user_id = account.id
            INNER JOIN users ON users.username = account.id
            INNER JOIN users_roles ON users_roles.user_id = users.id
            INNER JOIN roles_to_privilege ON roles_to_privilege.role_id = users_roles.role_id
    WHERE
            '.$cond.' AND account.group_id = '.$group_id.'
            AND roles_to_privilege.privilege_code="'.$code.'"
    ');
}
function get_no_revenue_status($group_id){
    $no_revenue_status = MiString::get_list(DB::fetch_all('select id from statuses where no_revenue=1 and (group_id = '.$group_id.' or statuses.is_system=1)'));
    $no_revenue_status=implode(',', $no_revenue_status);
    return $no_revenue_status;
}