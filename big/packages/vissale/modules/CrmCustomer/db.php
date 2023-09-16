<?php
class CrmCustomerDB {
    static function can_view($customer_id=false){
        return true;
    }
    static function can_add($customer_id=false){
        return true;
    }

    /**
     * admin group / Người được phụ trách / người tạo => được quyền sửa thông tin user
     * @param bool $customer_id
     * @return bool
     */
    static function can_edit($customer_id=false) {
        if(check_user_privilege('CSKH')){
            return true;
        }else{
            return false;
        }
        /*if (Session::get('admin_group')) {
            return true;
        } else {
            $currentUserId = get_user_id();
            $sql ="SELECT id FROM crm_customer WHERE id={$customer_id} AND (creator_id={$currentUserId} OR user_id={$currentUserId})";
            if ($customer_id and !DB::exists($sql)) {
                return false;
            } else {
                return true;
            }
        }*/
    }
    static function can_del($customer_id=false){
        return true;
    }
	static function update_card($customer_id) {
		if (URl::get('card_deleted_ids')) {
			$group_deleted_ids = explode(',', URl::get('card_deleted_ids'));
			foreach ($group_deleted_ids as $delete_id) {
				DB::delete_id('crm_customer_card', $delete_id);
			}
		}
		if (isset($_REQUEST['mi_card'])) {
			$user_id = get_user_id();
			foreach ($_REQUEST['mi_card'] as $key => $record) {
				$record['start_date'] = Date_Time::to_sql_date($record['start_date']);
				$record['end_date'] = Date_Time::to_sql_date($record['end_date']);
				$record['customer_id'] = $customer_id;
				if ($record['id']) {
					DB::update('crm_customer_card', $record, 'id = ' . $record['id']);
				} else {
					if ($record['name']) {
						unset($record['id']);
						DB::insert('crm_customer_card', $record + array('created_time' => time(), 'created_user_id' => $user_id));
					}
				}
			}
		}
	}
	static function get_card($customer_id = false) {
        $customer_id = DB::escape($customer_id);
		$sql = '
				SELECT
					crm_customer_card.*
				FROM
					crm_customer_card
				WHERE
					crm_customer_card.customer_id = "' . $customer_id . '"
				ORDER BY
					crm_customer_card.id
		';
		if ($items = DB::fetch_all($sql)) {
			foreach ($items as $key => $value) {
				$items[$key]['start_date'] = $value['start_date'] ? Date_Time::to_common_date($value['start_date']) : '';
				$items[$key]['end_date'] = $value['end_date'] ? Date_Time::to_common_date($value['end_date']) : '';
			}
		} else {
			$items = array();
		}
		return $items;
	}
	static function get_contacts() {
		return array();
		/*$items = DB::fetch_all('
			select
				id, `crm_customer_contact`.`full_name` as name
			from
				`crm_customer_contact`
			where
				`crm_customer_contact`.group_id = '.Session::get('group_id').'
			order by
				`crm_customer_contact`.`full_name`
			'
		);*/
		$items = DB::fetch_all('
			select
				crm_customer.id, concat(`crm_customer`. name," - ",groups.name) as name
			from
				`crm_customer`
				inner join `groups` on groups.id = crm_customer.group_id
			where
				(
					`crm_customer`.group_id = ' . Session::get('group_id') . '
					' . (Session::get('master_group_id') ? ' or `crm_customer`.master_group_id = ' . Session::get('master_group_id') . '' : '') . '
					' . ((Session::get('account_type') == 3) ? ' or `crm_customer`.master_group_id = ' . Session::get('group_id') . '' : '') . '
				)
			order by
				`crm_customer`.`name`
			'
		);
		return $items;
	}
	static function get_crm_groups() {
		$items = DB::fetch_all('
			select
				id, `crm_customer_group`.`name` as name
			from
				`crm_customer_group`
			where
			    crm_customer_group.structure_id <> '.ID_ROOT.'
				AND (`crm_customer_group`.group_id = ' . Session::get('group_id') . '
				OR `crm_customer_group`.group_id = 1)
			order by
				`crm_customer_group`.`structure_id`
			'
		);
		return $items;
	}
	static function get_total_amount($customer_id) {
		return DB::fetch('select sum(total_price) as total from orders where customer_id = ' .  DB::escape($customer_id), 'total');
	}
	static function get_total_turn_card($customer_id) {
		return DB::fetch('select count(id) as total from spa_turn_card where customer_id = ' .  DB::escape($customer_id), 'total');
	}
	static function get_turn_cards($customer_id) {
		$sql = '
				SELECT
					spa_turn_card.id,
					spa_turn_card.code,
					spa_turn_card.sold_date,
					spa_turn_card.expired_date,
					spa_turn_card.note,
					spa_turn_card.htcs,
					spa_turn_card.total_price
				FROM
					spa_turn_card
				WHERE
					spa_turn_card.customer_id = "' . DB::escape($customer_id) . '"
				ORDER BY
					spa_turn_card.id DESC
		';
		if ($items = DB::fetch_all($sql)) {
			$index = 0;
			foreach ($items as $key => $value) {
				$items[$key]['index'] = ++$index;
				$code = get_prefix();
				$code .= str_pad(($value['code']), 6, "0", STR_PAD_LEFT);
				$items[$key]['code'] = $code;
				$items[$key]['sold_date'] = $value['sold_date'] ? Date_Time::to_common_date($value['sold_date']) : '';
				$items[$key]['expired_date'] = $value['expired_date'] ? Date_Time::to_common_date($value['expired_date']) : '';
				$items[$key]['total_price'] = System::display_number($value['total_price']);
			}
		} else {
			$items = array();
		}
		return $items;
	}
	static function get_process($customer_id = false) {
		$sql = '
				SELECT
					orders.id,
					orders.code,
				    orders.code AS code_no,   
					orders.user_created,
				    orders.created,
					IF(spa_turn_card.id is not null,spa_turn_card.total_price,orders.total_price) as total_price,
					orders.note,
					spa_turn_card.code as turn_card_code,
					orders.turn_card_id,
				    orders.group_id,
				    spa_turn_card.group_id as turn_card_group_id,
					`users`.name
				FROM
					orders
					inner join users on users.id = orders.user_created
					left join spa_turn_card on spa_turn_card.id = orders.turn_card_id
				WHERE
					orders.customer_id = ' .  DB::escape($customer_id) . '
				ORDER BY
					orders.id DESC
		';
		if ($customer_id and $items = DB::fetch_all($sql)) {
			$i = 0;
			foreach ($items as $key => $value) {
				$items[$key]['index'] = ++$i;
				$code = format_code($items[$key]['code'], $value['group_id']);
				$items[$key]['code'] = $code;
				$items[$key]['turn_card_code'] = format_code($value['turn_card_code'], $value['turn_card_group_id']);
				$items[$key]['total_price'] = System::display_number($value['total_price']);
				$items[$key]['created'] = $value['created'] ? date('d/m/Y H:i', strtotime($value['created'])) : '';
				$items[$key]['products'] = AdminProcessDB::get_process_product($key);
			}
		} else {
			$items = array();
		}
		return $items;
	}
	static function get_orders($customer_id = false) {
		$sql = '
				SELECT
					orders.id,
					orders.user_created,orders.created,
					orders.user_confirmed,orders.confirmed,
					orders.total_price,
					orders.code,
					orders.note1,
					orders.note2,
					orders.shipping_note,
					users.name,
					statuses.name as status_name
				FROM
					orders
					inner join users on users.id = orders.user_created
					inner join statuses on statuses.id = orders.status_id
				WHERE
					orders.customer_id = ' . DB::escape($customer_id) . '
					AND orders.status_id IN ('.THANH_CONG.','.CHUYEN_HANG.')
				ORDER BY
					orders.id DESC
		';
		if ($customer_id and $items = DB::fetch_all($sql)) {
			foreach ($items as $key => $value) {
				$items[$key]['total_price'] = System::display_number($value['total_price']);
				$items[$key]['created'] = $value['created'] ? date('d/m/Y H:i', strtotime($value['created'])) : '';
                $items[$key]['confirmed'] = $value['confirmed'] ? date('d/m/Y H:i', strtotime($value['confirmed'])) : '';
			}
		} else {
			$items = array();
		}
		return $items;
	}
	static function update_contact($customer_id) {
		if (URl::get('contact_deleted_ids')) {
			$group_deleted_ids = explode(',', URl::get('contact_deleted_ids'));
			foreach ($group_deleted_ids as $delete_id) {
				DB::delete_id('crm_customer_contact', $delete_id);
			}
		}
		if (isset($_REQUEST['mi_contact'])) {
			$employee_id = DB::fetch('select id from hrm_employee where user_id="' . Session::get('user_id') . '"', 'id');
			foreach ($_REQUEST['mi_contact'] as $key => $record) {
				$record['customer_id'] = $customer_id;
				$record['employee_id'] = $employee_id;
				if ($record['id']) {
					DB::update('crm_customer_contact', $record, 'id = ' . $record['id']);
				} else {
					if ($record['full_name']) {
						DB::insert('crm_customer_contact', $record + array('time' => time()));
					}
				}
			}
		}
	}
	static function delete($id) {
        $id = DB::escape($id);
		$row = DB::select('crm_customer', $id);
		DB::delete_id('crm_customer', $id);
		$data_log = 'Xóa khách hàng #' . $row['id'] . ': ' . $row['name'];
		System::account_log(0, $data_log);
	}
	static function get_customer($id,$group_id){
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
				crm_customer.id = ' .  DB::escape($id) . '
				AND crm_customer.group_id = '. DB::escape($group_id).'
		';
	    //DB::check_query('171.241.167.107',$sql);
	    //die;
	    return DB::fetch($sql);
    }
    static function get_total_customer($conditions){
        $sql = '
			SELECT
				crm_customer.id 
				'.$conditions['extra_select'].'
			from
			 	`crm_customer`
                    JOIN `groups` ON `groups`.id = crm_customer.group_id
                    JOIN `users` on `users`.id=`crm_customer`.`creator_id`
                    '.$conditions['inner_join_sql'].'
                    LEFT JOIN `crm_customer_group` on `crm_customer_group`.id=`crm_customer`.`crm_group_id`
                    LEFT JOIN zone_provinces_v2 on `zone_provinces_v2`.province_id=`crm_customer`.`zone_id`
			        WHERE
				'. $conditions['cond'] .'
				GROUP BY `crm_customer`.id
				'.$conditions['having_cond'].' 
		';
        $m_key= md5('total_customer_'.$conditions['cond'].$conditions['extra_select']);
        $total =0;
        if ($m_key and !System::is_local()) {
            $total = MC::get_items($m_key);
            if (!$total) {
                $items = DB::fetch_all($sql);
                $total = sizeof($items);
                MC::set_items($m_key, $total, time() + 60);
            }
        } else {
            $items = DB::fetch_all($sql);
            $total = sizeof($items);
        }
        return $total;
    }
    static function get_customers($conditions, $item_per_page, $order_by=null)
    {   $order_by_sql = 'order by crm_customer.id desc';
        if ($order_by) {
            $order_by_sql = $order_by;
        }
        $offset = (page_no() - 1) * $item_per_page;
        $sql = "SELECT
				`crm_customer`.id
			    ,`crm_customer`.group_id
				,`crm_customer`.`job_title`
				,CONCAT(IF(`crm_customer`.`gender`=1,'Anh ',IF(`crm_customer`.`gender`=2,'Chị ','')),`crm_customer`.`name`) AS name
				,`crm_customer`.`birth_date` 
				,`crm_customer`.`email` ,`crm_customer`.`phone` ,`crm_customer`.`mobile` ,`crm_customer`.`address` ,`crm_customer`.`description`
				,`crm_customer_group`.`name` as crm_group_name
				,`zone_provinces_v2`.`province_name` as zone
				,`crm_customer`.`website`
				,`users`.`name` as creator
				,`crm_customer`.`time` as created_time
				,crm_customer.warning_note
				,\"\" as card_name
                ,(select id from orders where orders.customer_id = crm_customer.id order by orders.id desc limit 0,1) as last_order_id
                ,(select customer_group from orders_extra where orders_extra.order_id = last_order_id order by orders_extra.id desc limit 0,1) as name_customer_extra
				,(select name from orders join statuses on statuses.id = orders.status_id where orders.customer_id = crm_customer.id order by orders.id desc limit 0,1) as last_order_status
				,(select color from orders join statuses on statuses.id = orders.status_id where orders.customer_id = crm_customer.id order by orders.id desc limit 0,1) as last_order_status_color
                
				,`groups`.name as group_name
                ,(SELECT `users`.name FROM users WHERE users.id=`crm_customer`.user_id) as follow_user_name
			    ,(SELECT `schedule`.appointed_time FROM `crm_customer_schedule` `schedule` WHERE `schedule`.customer_id=`crm_customer`.id ORDER BY `schedule`.appointed_time DESC LIMIT 0,1) as appointed_time
			    ,(SELECT `notes`.created_time FROM `crm_customer_notes` `notes` WHERE `notes`.customer_id=`crm_customer`.id ORDER BY `notes`.created_time DESC  LIMIT 0,1) as noted_time
			    ,(SELECT `callhistory`.status FROM `crm_customer_callhistory` `callhistory` WHERE `callhistory`.customer_id=`crm_customer`.id ORDER BY `callhistory`.created_time DESC LIMIT 0,1) as status_id
                ,(SELECT `callhistory`.created_time FROM `crm_customer_callhistory` `callhistory` WHERE `callhistory`.customer_id=`crm_customer`.id ORDER BY `callhistory`.created_time DESC LIMIT 0,1) as called_time
                ,(SELECT COUNT(*) FROM orders WHERE orders.group_id = ".Session::get('group_id')." and orders.customer_id=crm_customer.id AND orders.status_id = 5) AS success_total_quantity
                {$conditions['extra_select']}		    
                FROM
			 	crm_customer
                JOIN `groups` ON `groups`.id = crm_customer.group_id
                JOIN `users` on `users`.id=`crm_customer`.`creator_id`
                {$conditions['inner_join_sql']}
                LEFT JOIN `crm_customer_group` on `crm_customer_group`.id=`crm_customer`.`crm_group_id`
                LEFT JOIN `orders_extra` ON `orders_extra`.order_id = `orders`.id
                LEFT JOIN zone_provinces_v2 on `zone_provinces_v2`.province_id=`crm_customer`.`zone_id`
			WHERE
				{$conditions['cond']}
			GROUP BY `crm_customer`.id
			{$conditions['having_cond']} 
			{$order_by_sql}
			LIMIT {$offset},{$item_per_page}
		";
        return DB::fetch_all($sql);
    }
    static function update_share($customer_id) {
        if (URl::get('group_deleted_ids')) {
            $group_deleted_ids = explode(',', URl::get('group_deleted_ids'));
            foreach ($group_deleted_ids as $delete_id) {
                DB::delete_id('crm_customer_share', $delete_id);
            }
        }
        if (isset($_REQUEST['mi_shared_group'])) {
            $user_id = get_user_id();
            foreach ($_REQUEST['mi_shared_group'] as $key => $record) {
                $record['customer_id'] = $customer_id;
                if ($record['id']) {
                    DB::update('crm_customer_share', $record, 'id = ' . $record['id']);
                } else {
                    if ($record['group_id']) {
                        unset($record['id']);
                        DB::insert('crm_customer_share', $record + array('created_time' => time(), 'created_user_id' => $user_id));
                    }
                }
            }
        }
    }
    static function get_share($customer_id = false) {
        $customer_id = DB::escape($customer_id);
        $sql = '
				SELECT
					crm_customer_share.*
				FROM
					crm_customer_share
				WHERE
					crm_customer_share.customer_id = "' . $customer_id . '"
				ORDER BY
					crm_customer_share.id
		';
        if ($items = DB::fetch_all($sql)) {
        } else {
            $items = array();
        }
        return $items;
    }
    public static function get_groups(){
        $group_id = Session::get('group_id');

        if( Session::get('account_type')==3 ){
            $cond = '(groups.id = '.$group_id.' or groups.master_group_id = '.$group_id.')';
        }

        if (Session::get('account_type')!=3){
            $cond = 'groups.id = '.$group_id;
        }

        $sql = '
			SELECT
				groups.id,groups.name
			FROM
                `groups`
			WHERE
				'.$cond;

        return DB::fetch_all($sql);
    }

    public static function get_other_groups()
    {
        $group_id = Session::get('group_id');
        $master_group_id = Session::get('master_group_id');
        if ($master_group_id) {
            $cond = "`groups`.master_group_id = $master_group_id";
        } else {
            $cond = "`groups`.master_group_id = $group_id";
        }
        $sql = "
			SELECT
				`groups`.id,`groups`.name
			FROM
				`groups`
			WHERE {$cond}";


        return DB::fetch_all($sql);
    }
    public static function get_schedule_filter()
    {
        return [
            ''                  => 'Phân Loại',
            'khach_moi'         => 'Mới',
            'khach_mua_tiep'    => 'Tiếp tục mua',
            'process_schedule'  => 'Hẹn gọi lại'

            ];
    }
    public static function get_customer_by_phone($mobile)
    {
        $mobile = preg_replace('/\D/', '', $mobile);
        $mobile = (float)$mobile;

        $sql = "
              SELECT crm_customer.*, 
                     `groups`.name as group_name,
                     `crm_customer_group`.`name` as crm_group_name,
                     CONCAT(IF(`crm_customer`.`gender`=1,'Anh ',IF(`crm_customer`.`gender`=2,'Chị ','')),`crm_customer`.`name`) AS customer_name,
                     IF(`crm_customer`.`birth_date`<> '0000-00-00',DATE_FORMAT(`crm_customer`.`birth_date`,'%d/%m/%Y'),'') as birth_date
              FROM crm_customer
              INNER JOIN `groups` on (`groups`.id=crm_customer.group_id)
              left outer join `crm_customer_group` on (`crm_customer_group`.id=`crm_customer`.crm_group_id)
              WHERE crm_customer.mobile LIKE '%$mobile'
              LIMIT 0,1";
        $customer =  DB::fetch($sql);

        if (empty($customer)) {
            return [
                'customer_name' => '',
                'customer_age' => '',
                'mobile' => '',
                'group_name' => '',
                'crm_group_name' => '',
                'url' => '#',
                'birth_date' => '',
            ];
        }

        return $customer;
    }
    public static function get_duplicated_phone($mobile,$group_id){
        $mobile = preg_replace('/\D/', '', $mobile);
        $mobile = (float)$mobile;
        $sql = "
              SELECT crm_customer.name, 
                     `groups`.name as group_name,
                     `crm_customer_group`.`name` as crm_group_name,
                     CONCAT(IF(`crm_customer`.`gender`=1,'Anh ',IF(`crm_customer`.`gender`=2,'Chị ','')),`crm_customer`.`name`) AS customer_name,
                     IF(`crm_customer`.`birth_date`<> '0000-00-00',DATE_FORMAT(`crm_customer`.`birth_date`,'%d/%m/%Y'),'') as birth_date
              FROM crm_customer
              inner join `groups` on (groups.id=crm_customer.group_id)
              left outer join `crm_customer_group` on (`crm_customer_group`.id=`crm_customer`.crm_group_id)
              WHERE 
                crm_customer.mobile LIKE '%$mobile'
                AND crm_customer.group_id = $group_id
        ";
        return DB::fetch($sql);
    }

    public static function get_crm_reports($type)
    {
        switch ($type) {
            case 'callhistory':
                return self::get_calls();
                break;
            case 'notes':
                return self::get_notes();
                break;
            case 'schedule':
                return self::get_schedules();
                break;
            default :
                break;
        }
    }

    public static function count_calls()
    {
        $conditions = self::getCrmReportConditions();
        $sql = "
            SELECT
            COUNT(*) as total
            FROM crm_customer_callhistory `callhistory`
            INNER JOIN users ON (`users`.id = `callhistory`.created_user_id)
            INNER JOIN crm_customer `customer` ON (`customer`.id = `callhistory`.customer_id)
            WHERE $conditions ";
        return DB::fetch($sql, 'total');
    }

    public static function get_calls()
    {
        //
        require_once 'packages/vissale/modules/CrmCustomerCallHistory/db.php';
        $limit = 100;
        $offset = (page_no() - 1) * $limit;
        $conditions = self::getCrmReportConditions();
        $sql = "
            SELECT
            `callhistory`.id,
            `callhistory`.status,
            users.name AS created_full_name,
            users.username AS created_user_name,
            `callhistory`.content,
            `callhistory`.created_time,
            `callhistory`.created_user_id,
            `customer`.mobile as mobile,
            `customer`.name as customer_name,
            `customer`.id as customer_id,
            `customer`.group_id as group_id
            FROM crm_customer_callhistory `callhistory`
            INNER JOIN users ON (`users`.id = `callhistory`.created_user_id)
            INNER JOIN crm_customer `customer` ON (`customer`.id = `callhistory`.customer_id)
            WHERE $conditions
            ORDER BY `callhistory`.created_time DESC
            LIMIT $offset, $limit
        ";

        $items = DB::fetch_all($sql);
        $offset++;
        $status = CrmCustomerCallHistoryDB::$status;
        foreach ($items as &$item) {
            $item['code'] = format_code($item['customer_id'], $item['group_id']);
            $item['index'] = $offset;
            $item['status_name'] = $status[$item['status']];
            $item['editable'] = ($item['created_user_id']==get_user_id());
            $offset ++;

        }
        unset($item);
        $count  = self::count_calls();
        return ['count'=>$count, 'items'=>$items];
    }

    public static function get_notes()
    {

    }

    public static function get_schedules()
    {

    }

    public static function getCrmReportConditions()
    {
        $type =  Url::get('type');
        $strSql = '';
        $andCond = [];
        $orCond = [];
        $andCond[] = '1=1';
        if ( !empty(Url::get('branch_id')) ) {
            $branch_id = Url::get('branch_id');
            $andCond[] = " `customer`.group_id=$branch_id";
        }
        if ( !empty(Url::get('from_date')) ) {
            $from_date = Url::get('from_date');
            $from_date = Date_Time::to_sql_date($from_date);
            $from_date = strtotime($from_date);
            $andCond[] = " `$type`.created_time>=$from_date";
        }
        if ( !empty(Url::get('to_date')) ) {
            $to_date = Url::get('to_date');
            $to_date = Date_Time::to_sql_date($to_date) . ' 23:59:59';
            $to_date = strtotime($to_date);
            $andCond[] = " `$type`.created_time<=$to_date";
        }
        if ( !empty(Url::get('account_name')) ) {
            $account_name = Url::get('account_name');
            $andCond[] = " `users`.username LIKE '$account_name'";
        }

        if ($andCond) {
            $strSql .= implode(' AND ', $andCond);
        }

        return $strSql;
    }

    public static function get_all_statuses()
    {
        $business_model = get_group_options('business_model');
        $id_arr = '"4","5","6","7","8","9","10","96"';
        $group_id = Session::get('group_id');
        $master_group_id = (int)Session::get('master_group_id');
        $cond = '
			id NOT IN ('.$id_arr.') and 
			(statuses.group_id IN('.$group_id.','.$master_group_id.') OR ('.(($business_model==1)?'statuses.is_default=2':'statuses.is_system=1').'))
				'.(Url::get('keyword')?' AND statuses.name LIKE "%'.Url::get('keyword').'%"':'').'
			';
        $sql = 'select 
					statuses.*,
                    (SELECT CONCAT("LV",level,"-",`statuses`.name)) AS `name`
				from 
					statuses
				WHERE
					'.$cond.'
				GROUP BY
					statuses.id
				order by 
					statuses.is_system DESC, statuses.id  ASC';
        return DB::fetch_all($sql);
    }

    public static function get_customers_by_levels($conditions)
    {
        $group_id = Session::get('group_id');
        $master_group_id = Session::get('master_group_id');
        $account_type = Session::get('account_type');
        //share customer giua cac chi nhanh
        $shareCond = '';//" OR (crm_customer.id IN (SELECT customer_id FROM crm_customer_share WHERE crm_customer_share.group_id={$group_id}))";

        if ($account_type == 3) {
            $cond = '(crm_customer.group_id = ' . $group_id . ' or groups.master_group_id = ' . $group_id .')';
        } else {
            if ($master_group_id) {
                $cond = '(crm_customer.group_id = ' . $group_id . $shareCond . ')';
                // share chi nhanh
            } else {
                $cond = '(crm_customer.group_id = ' . $group_id . $shareCond . ')';
            }
        }
        $sql = "
            SELECT 
                  `status`.level AS id, 
                  `status`.level,
            COUNT(`crm_customer`.`id`) AS count
            FROM statuses `status`
            INNER JOIN crm_customer ON (`crm_customer`.status_id=`status`.id)
            INNER JOIN `groups` ON (`groups`.id = crm_customer.group_id)
            WHERE $cond
            GROUP BY `status`.level
        ";
        //System::debug( $sql );die;
        //INNER JOIN `groups` ON (`groups`.id = crm_customer.group_id)
        //            INNER JOIN `users` ON (`users`.id=`crm_customer`.`creator_id`)
        //                {$conditions['inner_join_sql']}
        return DB::fetch_all($sql);
    }

    public static function update_customer_status($oldData, $newData, $new_status_id)
    {
        if ($oldData['customer_status_id'] == $newData['customer_status_id']) {
            return false;
        }
        DB::update_id('crm_customer', ['status_id'=>$new_status_id], $oldData['customer_id']);
        $array['status'] = $newData['customer_status_id'];
        $old_array['status'] = $oldData['customer_status_id'];
        $text = array(
            'status' => 'Phân loại'
        );
        $message = "đã sửa khách hàng: <br>" . System::generate_log_message($old_array, $array, $text);
        System::log('EDIT', "customer_id_{$oldData['customer_id']}", $message, "customer_id_={$oldData['customer_id']}", false, false,
            73359); //neu sua mdoule se phai sua 73359
    }

    public static function modifyMobileFirstNo($mobile){
        $mobile = preg_replace('/\D/', '', $mobile);
        if ( strpos($mobile, '0') !== 0 ){
            $mobile = '0' . $mobile;
        }
        return $mobile;
    }

    public static function convertVietnamMobileNo($mobile){

        $mobile = self::modifyMobileFirstNo($mobile);

        if( strlen($mobile)<11 ) {
            return $mobile;
        }

        $items = self::getMobilePrefix();
        foreach ($items as $key => $value ) {
            if (strpos($mobile, $key) === 0) {
                $mobile =  $items[$key] . substr($mobile, -7, 7);
                break;
            }
        }
        return $mobile;
    }

    public static function getMobilePrefix(){
        return [

            // viettel
            '0169' => '039',
            '0168' => '038',
            '0167' => '037',
            '0166' => '036',
            '0165' => '035',
            '0164' => '034',
            '0163' => '033',
            '0162' => '032',

            //mobile
            '0120' => '070',
            '0121' => '079',
            '0122' => '077',
            '0126' => '076',
            '0128' => '078',

            //vnphone
            '0123' => '083',
            '0124' => '084',
            '0125' => '085',
            '0127' => '081',
            '0129' => '082',

            // vietnam mobile
            '0199' => '059',
            '0186' => '056',
            '0188' => '058',
        ];
    }
    static function get_customer_groups(){
        $group_id = Session::get('group_id');
        $sql = '
			SELECT
				id, `crm_customer_group`.`name`
			FROM
				`crm_customer_group`
			WHERE
				structure_id <> ' . ID_ROOT . ' 
				and 
				(
				    (group_id = ' . $group_id . ' ' . (Session::get('master_group_id') ? ' or group_id=' . Session::get('master_group_id') : '') . ')
				    OR group_id = 1
				)
				';
        return DB::fetch_all($sql);
    }
}
