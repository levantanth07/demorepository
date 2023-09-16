<?php

class CrmCustomerScheduleDB {

    public static $status = array(
        '1'=>'Khách chốt hẹn',
        '2'=>'Khách hủy hẹn',
        '3'=>'Khách đã đến',
        '4'=>'Khách làm dịch vụ',
        '5'=>'Khách bỏ về'
    );

    public static $schedule_type = [
        1 => 'Tư vấn',
        2 => 'Tư vấn VIP',
        3 => 'Tái khám'
    ];

    function __construct()
    {

    }

    static function can_view($customer_id=false){
        return true;
    }
    static function can_add($customer_id=false){
        return true;
    }
    static function can_edit($customer_schedule_id=false) {
        return true;

        /*if (Session::get('admin_group')) {
            return true;
        } else {
            if ($customer_schedule_id and !DB::exists('SELECT id FROM crm_customer_schedule WHERE id=' . $customer_schedule_id . ' AND created_user_id = ' . get_user_id())) {
                return false;
            } else {
                return true;
            }
        }*/
    }
    static function get_customer_last_order_id($id,$group_id){

        $sql = '
            select
                `crm_customer`.id
                ,`crm_customer`.`job_title`
                ,CONCAT(IF(`crm_customer`.`gender`=1,"Anh ",IF(`crm_customer`.`gender`=2,"Chị ","")),`crm_customer`.`name`) AS name
                ,IF(`crm_customer`.`birth_date`<>"0000-00-00",
                DATE_FORMAT(`crm_customer`.`birth_date`,"%d/%m/%Y"),"") as birth_date ,
                `crm_customer`.`email` ,`crm_customer`.`phone` ,`crm_customer`.`mobile`
                ,`crm_customer`.`website`
                ,`crm_customer`.`address` ,`crm_customer`.`description`
                ,crm_customer.bank_name
                ,crm_customer.career
                ,crm_customer.weight
                ,crm_customer.bank_account_number
                ,crm_customer.bank_account_name
                ,`crm_customer_group`.`name` as crm_group_name
                ,`zone_provinces_v2`.`province_name` as zone_name
                ,crm_customer_contact.full_name as contact_name
                ,users.name as follow_user_name
                ,crm_customer.contact_id
                ,crm_customer.warning_note
                ,crm_customer.image_url
                ,groups.name as group_name
                ,(select id from orders where orders.customer_id = crm_customer.id order by orders.id desc limit 0,1) as last_order_id
                ,(select name from orders join statuses on statuses.id = orders.status_id where orders.customer_id = crm_customer.id order by orders.id desc limit 0,1) as last_order_status
                ,(select color from orders join statuses on statuses.id = orders.status_id where orders.customer_id = crm_customer.id order by orders.id desc limit 0,1) as last_order_status_color
            from
                `crm_customer`
                LEFT JOIN orders ON orders.customer_id = crm_customer.id
                JOIN `groups` on `groups`.id=`crm_customer`.group_id
                left join `crm_customer_group` on `crm_customer_group`.id=`crm_customer`.crm_group_id
                LEFT JOIN zone_provinces_v2 on `zone_provinces_v2`.province_id=`crm_customer`.`zone_id`
                left join `crm_customer_contact` on `crm_customer_contact`.id=`crm_customer`.contact_id
                left join `users` on `users`.id=`crm_customer`.user_id
            where
                crm_customer.id = ' . $id . '
                AND crm_customer.group_id = '.$group_id.'
        ';
        return DB::fetch($sql);
    }
    static function getIdUser(){
        $quyenQuanLyKhachHang = check_user_privilege('CUSTOMER');
        if(Session::get('admin_group') || $quyenQuanLyKhachHang){
            return '';
        } else{
            return get_user_id();
        }
    }

    static function can_del($customer_id=false){
        return true;
    }
    static function get_total_item($cond){
        $sql = "
            SELECT 
              COUNT(crm_customer_schedule.id) as total
            FROM
                crm_customer_schedule
                LEFT JOIN crm_customer ON (crm_customer.id = crm_customer_schedule.customer_id)
                JOIN users ON (users.id = crm_customer_schedule.created_user_id)
                JOIN orders ON (orders.id = `crm_customer_schedule`.order_id)
                left join `groups` on (groups.id = crm_customer_schedule.branch_id)
            WHERE 
              1=1 $cond
        ";
        return DB::fetch($sql, 'total');
	}
	static function get_items($cond,$order_by,$item_per_page){
        $offset = (page_no()-1)*$item_per_page;
        $limit = $item_per_page;
		$sql = "
				SELECT
					crm_customer_schedule.id,
					crm_customer_schedule.note,
					from_unixtime(crm_customer_schedule.created_time,'%d/%m/%Y %H:%i') AS created_time,
					crm_customer_schedule.customer_id,
					crm_customer_schedule.group_id,
					crm_customer_schedule.created_user_id,
					crm_customer_schedule.branch_id,
					crm_customer_schedule.staff_id,
					crm_customer_schedule.staff_name,
					crm_customer_schedule.schedule_type,
					crm_customer_schedule.appointed_time AS appointed_time,
					crm_customer_schedule.arrival_time AS arrival_time,
					from_unixtime(crm_customer_schedule.appointed_time,'%d/%m/%Y %H:%i') AS appointed_time_display,
					from_unixtime(crm_customer_schedule.arrival_time,'%d/%m/%Y %H:%i') AS arrival_time_display,
					crm_customer_schedule.status_id,
					crm_customer.name AS customer_name,
					crm_customer.mobile AS customer_mobile,
					users.name as created_user_name,
					groups.name AS branch_name,
					groups.address AS branch_address
				FROM
					crm_customer_schedule
                    LEFT JOIN crm_customer ON (crm_customer.id = crm_customer_schedule.customer_id)
                    JOIN users ON (users.id = crm_customer_schedule.created_user_id)
                    JOIN orders ON (orders.id = `crm_customer_schedule`.order_id)
                    left join `groups` on (groups.id = crm_customer_schedule.branch_id)
				WHERE
				  1=1 $cond
				ORDER BY crm_customer_schedule.appointed_time DESC, crm_customer_schedule.id DESC
				LIMIT {$offset}, {$limit}
			";
            //System::debug($sql);die;
		$items = DB::fetch_all($sql);
        $idUser = CrmCustomerScheduleDB::getIdUser();
        $returnItems = array();
        if($idUser != ''){
            foreach($items as $key => $row){
                if($row['created_user_id']){
                    $returnItems[$key] = $row;
                }
            }
        }else{
            $returnItems =  $items;
        }
		return $returnItems;
	}
    static function get_item($sid)
    {
        $group_id = Session::get('group_id');
        $sql = "
				SELECT
					crm_customer_schedule.id,
					crm_customer_schedule.note,
					FROM_UNIXTIME(crm_customer_schedule.created_time,'%d/%m/%Y %H:%i') AS created_time,
					crm_customer_schedule.customer_id,
					crm_customer_schedule.group_id,
					crm_customer_schedule.created_user_id,
					crm_customer_schedule.branch_id,
					crm_customer_schedule.staff_id,
					crm_customer_schedule.staff_name,
				    crm_customer_schedule.sale_staff_id,
					crm_customer_schedule.sale_staff_name,
				    crm_customer_schedule.note_services,
					crm_customer_schedule.schedule_type,
					FROM_UNIXTIME(crm_customer_schedule.appointed_time,'%d/%m/%Y %H:%i') AS appointed_time_display,
					crm_customer_schedule.appointed_time AS appointed_time,
					FROM_UNIXTIME(crm_customer_schedule.arrival_time, '%d/%m/%Y %H:%i') AS arrival_time_display,
					crm_customer_schedule.arrival_time AS arrival_time,
					crm_customer_schedule.status_id,
					crm_customer.name AS customer_name,
					crm_customer.mobile AS customer_mobile,
					users.name as created_user_name,
					groups.name AS branch_name,
					groups.address AS branch_address
				FROM
					crm_customer_schedule
                    LEFT JOIN crm_customer ON (crm_customer.id = crm_customer_schedule.customer_id)
                    JOIN users ON (users.id = crm_customer_schedule.created_user_id)
                    left join `groups` on (groups.id = crm_customer_schedule.branch_id)
                    WHERE MD5(CONCAT(crm_customer_schedule.id, '".CATBE."'))='{$sid}'
				AND crm_customer_schedule.group_id=$group_id
			";

        return DB::fetch($sql);
    }
	static function get_customer($cid){
        $cid = DB::escape($cid);
        $group_id = Session::get('group_id');
        if($cid){
            $sql = "
                SELECT crm_customer.*
                FROM crm_customer
                WHERE id=$cid and group_id=$group_id
                LIMIT 0,1
            ";
            return DB::fetch($sql);
        }else{
            return [];
        }
	}
	static function get_max_bill_number($cond){
        return DB::fetch('
			SELECT max(bill_number) as total FROM crm_customer_schedule WHERE '.$cond.'
		','total');
	}
    static function get_statistics($cond)
    {
        $group_id = Session::get('group_id');
        $searchQuery = CrmCustomerScheduleDB::generateSearchConditions();
        $sql = "
            SELECT ( SELECT sum(amount) FROM `crm_customer_schedule` WHERE bill_type=1 AND del=0 AND {$cond} {$searchQuery}) AS receive, ( 
            SELECT sum(amount) FROM `crm_customer_schedule` WHERE bill_type=0 AND del=0 AND {$cond} {$searchQuery}) AS pay
            FROM `crm_customer_schedule` 
            LIMIT 0,1
        ";
        return DB::fetch($sql);
    }
    static  function conditions()
    {
        $group_id = Session::get('group_id');
        $adminGroup = Session::get('admin_group');
        $orConditions = [];
        $andConditions = [];
        $andConditions[] = "crm_customer_schedule.group_id=$group_id";
        if ($cid = URL::iget('cid')) {
            $andConditions[] = "crm_customer.id=$cid";
        }
        if ($customer_text = DB::escape(trim(URL::get('customer_text'))) ) {
            $orConditions[] = "crm_customer.id=" . intval($customer_text);
            $orConditions[] = "crm_customer.mobile LIKE '%{$customer_text}%'";
            $orConditions[] = "crm_customer.name LIKE '%{$customer_text}%'";
        }
        if ($from_date=DB::escape(URL::get('from_date'))) {
            $fromDate =  Date_Time::to_time($from_date);
            $andConditions[] = "crm_customer_schedule.appointed_time >= '$fromDate'";
        }
        if(!check_user_privilege('CUSTOMER') && !Session::get('admin_group') && !is_group_owner()){
            $andConditions[] = "( crm_customer_schedule.created_user_id = ".get_user_id()." OR orders.user_assigned = " . get_user_id() . ")";
        }
        if ($branch_id=DB::escape(URL::get('branch_id'))) {
            $andConditions[] = "crm_customer_schedule.branch_id = {$branch_id}";
        }
        if ($schedule_type=DB::escape(URL::get('schedule_type'))) {
            $andConditions[] = "crm_customer_schedule.schedule_type = {$schedule_type}";
        }
        if ($status_id=DB::escape(URL::get('status_id'))) {
            $andConditions[] = "crm_customer_schedule.status_id = {$status_id}";
        }
        if ($user_id=DB::escape(URL::get('user_id'))) {
            $andConditions[] = "crm_customer_schedule.created_user_id = {$user_id}";
        }

        if ($to_date=URL::get('to_date')) {
            $toDate =  Date_Time::to_time($to_date) + (24*3600);
            $andConditions[] = "crm_customer_schedule.appointed_time <= '$toDate'";
        }
        $results = '';
        $orQuery = join(' OR ', $orConditions);
        $andQueryDate = join(' AND ', $andConditions);
        if ( !empty($orQuery) ) {
            $results .= " AND ($orQuery)";
        }

        if ( !empty($andQueryDate) ) {
            $results .= " AND ($andQueryDate)";
        }
        return $results;
    }
    static function generateSearchConditions()
    {
        $group_id = Session::get('group_id');
        $orConditions = [];
        $andConditions = [];
        $andConditions[] = "crm_customer_schedule.group_id=$group_id";
        if ($cid = URL::iget('cid')) {
            $andConditions[] = "crm_customer.id=$cid";
        }
        if ($customer_text = trim(URL::get('customer_text')) ) {
            $orConditions[] = "crm_customer.id=" . intval($customer_text);
            $orConditions[] = "crm_customer.mobile LIKE '%{$customer_text}%'";
            $orConditions[] = "crm_customer.name LIKE '%{$customer_text}%'";
        }
        if ($from_date=URL::get('from_date')) {
            $fromDate =  Date_Time::to_time($from_date);
            $andConditions[] = "crm_customer_schedule.appointed_time > '$fromDate'";
        }
        if(!check_user_privilege('CUSTOMER')){
            $andConditions[] = "crm_customer_schedule.created_user_id = ".get_user_id();
        }
        if ($branch_id=URL::get('branch_id')) {
            $andConditions[] = "crm_customer_schedule.branch_id = {$branch_id}";
        }
        if ($schedule_type=URL::get('schedule_type')) {
            $andConditions[] = "crm_customer_schedule.schedule_type = {$schedule_type}";
        }
        if ($status_id=URL::get('status_id')) {
            $andConditions[] = "crm_customer_schedule.status_id = {$status_id}";
        }
        if ($user_id=URL::get('user_id')) {
            $andConditions[] = "crm_customer_schedule.created_user_id = {$user_id}";
        }

        if ($to_date=URL::get('to_date')) {
            $toDate =  Date_Time::to_time($to_date) + (24*3600);
            $andConditions[] = "crm_customer_schedule.appointed_time < '$toDate'";
        }
        $results = '';
        $orQuery = join(' OR ', $orConditions);
        $andQueryDate = join(' AND ', $andConditions);
        if ( !empty($orQuery) ) {
            $results .= " AND ($orQuery)";
        }

        if ( !empty($andQueryDate) ) {
            $results .= " AND ($andQueryDate)";
        }
        return $results;
    }
    static function getGroupsByMasterGroup()
    {
        $group_id = Session::get('group_id');
        if (Session::get('account_type')==3) {
            $cond = "(groups.master_group_id={$group_id} or groups.id={$group_id})";
        } else {
            $cond = "groups.id={$group_id}";
        }
        $sql = "
            SELECT groups.id, groups.name, groups.address
            from `groups`
            WHERE {$cond}
        ";
        return DB::fetch_all($sql);
    }

    public static function generate_branches($branches)
    {
        $__branches = [];
        foreach ($branches as $branch) {
            $__branches[$branch['id']] = $branch['name'];
        }
        return $__branches;
    }

    public static function get_today_schedules($item_per_page)
    {
        $limit = $item_per_page;
        $offset = (page_no()-1)*$limit;
        $cond = self::generateTodayScheduleConditions();
        $sql = "
            SELECT 
                    `crm_customer_schedule`.id,
					`crm_customer_schedule`.note,
					`crm_customer_schedule`.note_services,
					`crm_customer_schedule`.created_time,
					`crm_customer_schedule`.customer_id,
					`crm_customer_schedule`.group_id,
					`crm_customer_schedule`.created_user_id,
					`crm_customer_schedule`.branch_id,
                    (SELECT `groups`.name FROM `groups` WHERE `crm_customer_schedule`.branch_id=`groups`.id LIMIT 0,1) AS branch_name,
					`crm_customer_schedule`.staff_id,
					`crm_customer_schedule`.status_id,
					`crm_customer_schedule`.staff_name,
					`crm_customer_schedule`.schedule_type,
					`crm_customer_schedule`.appointed_time AS appointed_time,
					`crm_customer_schedule`.arrival_time AS arrival_time,
                    crm_customer.name AS customer_name,
					crm_customer.mobile AS customer_mobile,
					crm_customer.status_id AS customer_status_id,
                   (SELECT COUNT(*) FROM orders WHERE orders.customer_id=crm_customer.id AND orders.status_id <> 9) AS count_order,
					users.name as created_user_name
            FROM crm_customer_schedule `crm_customer_schedule`
            JOIN `groups` ON (`groups`.id=`crm_customer_schedule`.group_id)
            LEFT JOIN crm_customer ON (crm_customer.id = `crm_customer_schedule`.customer_id)
            JOIN users ON (users.id = `crm_customer_schedule`.created_user_id)
            WHERE $cond
            ORDER BY `crm_customer_schedule`.arrival_time ASC, `crm_customer_schedule`.appointed_time
            LIMIT $offset,$limit
        ";
        return DB::fetch_all($sql);
    }

    public static function update_customer_status($status_id, $customer_id)
    {
        DB::update_id('crm_customer', ['status_id'=>$status_id], $customer_id);
    }

    public static function count_total_today_schedules(){
        $cond = self::generateTodayScheduleConditions();
        $sql = "
            SELECT total FROM (SELECT 
                    COUNT(`crm_customer_schedule`.id) AS total
            FROM crm_customer_schedule `crm_customer_schedule`
            INNER JOIN `groups` ON (`groups`.id=`crm_customer_schedule`.group_id)
            INNER JOIN crm_customer ON (crm_customer.id = `crm_customer_schedule`.customer_id)
            INNER JOIN users ON (users.id = `crm_customer_schedule`.created_user_id)
            WHERE $cond ) AS total
        ";
        return DB::fetch($sql,'total');
    }

    public static function count_arrival_today_customer()
    {
        $cond = self::generateTodayScheduleConditions();
        $sql = "
            SELECT COUNT(*)  AS total FROM (SELECT 
                    `crm_customer_schedule`.id
            FROM crm_customer_schedule `crm_customer_schedule`
            INNER JOIN `groups` ON (`groups`.id=`crm_customer_schedule`.group_id)
            INNER JOIN crm_customer ON (crm_customer.id = `crm_customer_schedule`.customer_id)
            INNER JOIN users ON (users.id = `crm_customer_schedule`.created_user_id)
            WHERE $cond AND IFNULL(`crm_customer_schedule`.arrival_time, 0)<>0 ) AS total
        ";
        return DB::fetch($sql,'total');
    }

    public static function count_old_customers()
    {
        $cond = self::generateTodayScheduleConditions();
        $sql = "
            SELECT COUNT(*) AS total
            FROM (
                 SELECT `crm_customer_schedule`.id
            FROM crm_customer_schedule `crm_customer_schedule`
            INNER JOIN `groups` ON (`groups`.id=`crm_customer_schedule`.group_id)
            INNER JOIN crm_customer ON (crm_customer.id = `crm_customer_schedule`.customer_id)
            INNER JOIN orders ON (orders.customer_id=crm_customer.id AND orders.status_id <> 9) 
            INNER JOIN users ON (users.id = `crm_customer_schedule`.created_user_id)
            WHERE $cond
            GROUP BY `crm_customer_schedule`.id
            ) AS total
        ";
        return DB::fetch($sql,'total');
    }

    public static function count_news_customers()
    {
        $cond = self::generateTodayScheduleConditions();
        $sql = "
            SELECT COUNT(*) AS total
            FROM (
                 SELECT `crm_customer_schedule`.id
            FROM crm_customer_schedule `crm_customer_schedule`
            INNER JOIN `groups` ON (`groups`.id=`crm_customer_schedule`.group_id)
            INNER JOIN crm_customer ON (crm_customer.id = `crm_customer_schedule`.customer_id)
            INNER JOIN users ON (users.id = `crm_customer_schedule`.created_user_id)
            WHERE $cond 
                 AND (SELECT COUNT(*) FROM orders WHERE crm_customer.id=orders.customer_id)=0
            GROUP BY `crm_customer_schedule`.id
            ) AS total
        ";

        return DB::fetch($sql,'total');
    }

    protected static function generateTodayScheduleConditions()
    {
        $group_id = Session::get('group_id');
        $orConditions = [];
        $andConditions = [];

        if (Session::get('account_type')==3) {
            $andConditions[] = "(`groups`.master_group_id=$group_id OR `crm_customer_schedule`.branch_id=$group_id)";
        } else {
            $andConditions[] = "(`crm_customer_schedule`.branch_id=$group_id OR `crm_customer_schedule`.group_id=$group_id)";
        }

        if ($customer_text = DB::escape(trim(URL::get('customer_text'))) ) {
            $orConditions[] = "crm_customer.id=" . intval($customer_text);
            $orConditions[] = "crm_customer.mobile LIKE '%{$customer_text}%'";
            $orConditions[] = "crm_customer.name LIKE '%{$customer_text}%'";
            $orConditions[] = "`crm_customer_schedule`.staff_name LIKE '%{$customer_text}%'";
            $orConditions[] = "`crm_customer_schedule`.sale_staff_name LIKE '%{$customer_text}%'";
        }
        $start_time = (new DateTime(date('Y-m-d')))->getTimestamp();
        $end_time = (new DateTime(date('Y-m-d 23:59:59')))->getTimestamp();
        if ($from_date = URL::get('from_date')) {
            $fromDate =  Date_Time::to_time($from_date);
            $andConditions[] = "`crm_customer_schedule`.appointed_time >= '$fromDate'";
        } else {
            $andConditions[] = "`crm_customer_schedule`.appointed_time >= '$start_time'";
            $_REQUEST['from_date'] = date('d/m/Y');
        }
        if ($to_date = URL::get('to_date')) {
            $toDate =  Date_Time::to_time($to_date) + (24*3600);
            $andConditions[] = "`crm_customer_schedule`.appointed_time <= '$toDate'";
        } else {
            $andConditions[] = "`crm_customer_schedule`.appointed_time <= '$end_time'";
            $_REQUEST['to_date'] = date('d/m/Y');
        }

        if ($branch_id = DB::escape(URL::get('branch_id'))) {
            $andConditions[] = "`crm_customer_schedule`.branch_id = {$branch_id}";
        }
        if ($schedule_type = DB::escape(URL::get('schedule_type'))) {
            $andConditions[] = "`crm_customer_schedule`.schedule_type = {$schedule_type}";
        }
        if ($status_id = DB::escape(URL::get('status_id'))) {
            $andConditions[] = "`crm_customer_schedule`.status_id = {$status_id}";
        }

        $results = '';
        $orQuery = implode(' OR ', $orConditions);
        $andQuery = implode(' AND ', $andConditions);
        if ( !empty($andQuery) ) {
            $results .= " ($andQuery)";
        }
        if ( !empty($orQuery) ) {
            $results .= " AND ($orQuery)";
        }
        return $results;
    }
    public static function get_users(){
        require_once 'packages/vissale/modules/AdminOrders/db.php';
        return AdminOrdersDB::get_users('GANDON',false,true);
    }
}

