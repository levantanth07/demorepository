<?php
const CONG_TY_TONG = 3;
class LogDB
{
	static $page;
	static $orderId;
    static function get_status(){
        $group_id = Session::get('group_id');
        $master_group_id = Session::get('master_group_id');
        if(Session::get('account_type')==TONG_CONG_TY){//khoand edited in 30/09/2018
            $cond = ' (groups.id='.$group_id.')';
        }elseif($master_group_id){
            $cond = ' (groups.id = '.$master_group_id.')';
        }else{
            $cond = ' groups.id='.$group_id.'';
        }
        $sql = '
			SELECT 
				statuses.id, statuses.no_revenue,
				IF(statuses.level>0,CONCAT(statuses.level,". ",statuses.name),statuses.name) AS name,
				IF(statuses.is_system=1,statuses.level,statuses_custom.level) as level,
				statuses.color
			FROM 
				`statuses`
				LEFT JOIN statuses_custom ON statuses_custom.status_id = statuses.id
				inner join `groups` on groups.id = statuses.group_id
			WHERE 
				'.$cond.' OR is_system=1
			ORDER BY 
				statuses.level,statuses_custom.position,statuses.id
		';
        return DB::fetch_all($sql);
    }
    static function get_total_revision($cond)
    {
        return DB::fetch('
			select
				count(order_revisions.id) as total
			from
				order_revisions
				join orders on orders.id = order_revisions.order_id
				join groups on groups.id = orders.group_id
			where
				'.$cond.'
		','total');
    }
    static function get_revisions($cond,$item_per_page)
    {
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
						order_revisions.modified,
						orders.customer_name,
						orders.mobile
					from
						order_revisions
						join orders on orders.id = order_revisions.order_id
						join groups on groups.id = orders.group_id
					WHERE
						' . $cond . '
					order by
						order_revisions.id desc
					limit
				        '.((page_no()-1)*$item_per_page).','.$item_per_page.'
						 
				';
        $items = DB::fetch_all($sql);
        return $items;
    }
	static function get_total_item($cond)
	{
		return DB::fetch('
			select
				count(log.id) as account
			from
				log
				left join `groups` on groups.id=log.group_id
			where
				'.$cond.'
		','account');
	}
	static function get_items($cond = '',$item_per_page=20)
	{
		return DB::fetch_all('
			select
				log.*,groups.name as group_name
			from
				log
				left join `groups` on groups.id=log.group_id
			where
				'.$cond.'
			ORDER BY
				log.id desc
			limit
				'.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
	}

    static function get_list_user($privilegeCode){
        $arrUserExportExcel = array();
        $listUser = DB::fetch_all('select users.* from users where group_id = '.Session::get('group_id'));
        $arrUserId = array_column($listUser,'id');
        $listRole = DB::fetch_all('select id,role_id,user_id from users_roles where user_id  in ('.implode(',',$arrUserId).')');
        if(count($listRole) > 0) {
            $arrRoleUser = array();
            $arrRoleId = array();
            foreach ($listRole as $rowRole) {
                $arrRoleUser[$rowRole['role_id']][] = $rowRole;
                $arrRoleId[] = $rowRole['role_id'];
            }
            $listRoleToPrivilege = DB::fetch_all('select id,role_id from roles_to_privilege where role_id  in (' . implode(',', $arrRoleId) . ') and privilege_code = "'.$privilegeCode.'"');
            if (count($listRoleToPrivilege) > 0) {
                foreach ($listRoleToPrivilege as $rowRoleToPrivilege) {
                    if (isset($arrRoleUser[$rowRoleToPrivilege['role_id']])) {
                        foreach ($arrRoleUser[$rowRoleToPrivilege['role_id']] as $rowRoleUser) {
                            $arrUserExportExcel[$listUser[$rowRoleUser['user_id']]['username']] = $listUser[$rowRoleUser['user_id']]['username'];
                        }
                    }
                }
            }
        }
        $group = DB::fetch('select id,account_type,prefix_post_code,name, email, code from `groups` where id= "'.Session::get('group_id').'"');
        $ownerInfo = DB::fetch($sql = 'select account.id, users.username, account.group_id from account inner join users on account.id = users.username where account.id = "' .$group['code'] . '"');
        if(!isset($arrUserExportExcel[$ownerInfo['id']])){
            $arrUserExportExcel[$ownerInfo['id']] = $ownerInfo['username'];
        }
        return $arrUserExportExcel;
    }
    static function get_order_revisions(){
    	if ( defined('LOG_V2') && !empty(LOG_V2)) {
            return self::get_order_revisions_mongodb(self::$page,self::$orderId);
        }
        return self::get_order_revisions_mysql(self::$page,self::$orderId);
    }
    static function get_order_revisions_mysql($page=1,$order_id=false)
    {
        $page = intval($page);
        // $group_id = Session::get('group_id');
        // $master_group_id = Session::get('master_group_id');
        $per_page = 500;
        $order_id = $order_id?$order_id:Url::iget('order_id');
        $order_id = DB::escape($order_id);
        $min_date = DB::fetch('select min(created) as val from order_revisions where order_id='.$order_id,'val');
        $min_time = Date_Time::to_time(date('d/m/Y',strtotime($min_date)));
        $max_date = DB::fetch('select max(created) as val from order_revisions where order_id='.$order_id,'val');
        $max_time = strtotime($max_date);
        $array = array();
        $cond = '1=1';
        // if(Session::get('account_type')==CONG_TY_TONG){
        //     $cond = '(orders.group_id='.$group_id.' or orders.master_group_id='.$group_id.')';
        // }else{
        //     $cond = '(orders.group_id='.$group_id.''.(($master_group_id)?' OR orders.master_group_id='.$master_group_id.'':'').')';
        // }
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
    static function get_order_revisions_mongodb($page=1,$order_id=false){
        require_once 'packages/vissale/lib/php/log.php';
        $per_page = 500;
        $order_id = $order_id?$order_id:Url::iget('order_id');
        $dataLog = getLog($order_id, 'orders', $per_page);
        $array = [];
        if(!empty($dataLog)){
            //var_dump($dataLog[0]);
            $min_date = date('Y-m-d h:m:i', end($dataLog)['time_created']);
            $min_time = Date_Time::to_time(date('d/m/Y',strtotime($min_date)));
            $max_date = date('Y-m-d h:m:i', $dataLog[0]['time_created']);
            $max_time = strtotime($max_date);

            $array = [];
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
        $total_rows = is_array($array)?count($array):0;
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
}
?>
