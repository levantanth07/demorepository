<?php

class AdvMoneyDB{

    static function get_total_item($cond){
        return DB::fetch(
            'select
				count(*) as acount
			from
				roles
			where
				'.$cond.'
				'
            ,'acount');
    }
    static function get_time_slot(){
    	$timeSlot = [
			'time_slot_1' => AdvMoney::TIME_SLOT_1,
			'time_slot_2' => AdvMoney::TIME_SLOT_2,
			'time_slot_3' => AdvMoney::TIME_SLOT_3,
			'time_slot_4' => AdvMoney::TIME_SLOT_4,
			'time_slot_5' => AdvMoney::TIME_SLOT_5,
			'time_slot_6' => AdvMoney::TIME_SLOT_6,
			'time_slot_7' => AdvMoney::TIME_SLOT_7,
		];

		$format = [];
		if(!AdvMoney::$choose_time_declare_advertising_money || AdvMoney::$choose_time_declare_advertising_money['value'] == ''){
			$time = $timeSlot;
		} else {
			$time = json_decode(AdvMoney::$choose_time_declare_advertising_money['value'],true);
			foreach ($timeSlot as $key => $value) {
				foreach ($time as $k => $v) {
					if($value == $v){
						$format[$key] = $value;
					}
				}
			}
		}
		$data['timeSlot'] = $time;
		$data['format'] = $format;
		return $data;
    }
    static function get_items($cond,$item_per_page,$date_from=false,$date_to=false){
        $sql = '
			select
				vs_adv_money.id
				,`account`.id as account_id
				,`account`.group_id
				,account.admin_group
				,groups.name as group_name
				,`account`.`password` ,
				`party`.`email` ,
				`party`.full_name ,
				`party`.`birth_date`,
				`party`.`address` ,
				`account`.`create_date`,
				`party`.`phone` as `phone_number`
				,IF(`party`.`gender`=1, "Male","Female") as gender
				,IF(`account`.`is_active`=1,"Yes","No") as active
				,IF(`account`.`is_block`=1,"Yes","No") as block
				,party.label
				,groups.expired_date
				,party.full_name as name
				,vs_adv_money.total
				,vs_adv_money.date
				,vs_adv_money.created_date
				,vs_adv_money.time_slot_1
				,vs_adv_money.time_slot_2
				,vs_adv_money.time_slot_3
				,vs_adv_money.time_slot_4
				,vs_adv_money.time_slot_5
				,vs_adv_money.time_slot_6
				,vs_adv_money.time_slot_7
				,vs_adv_money.source_id
				,vs_adv_money.bundle_id
				,vs_adv_money.clicks
				,order_source.name as source_name
				,bundles.name as bundle_name
				'.(($date_from and $date_to)?',(select sum(total) from vs_adv_money where vs_adv_money.account_id=account.id and vs_adv_money.date>="'.$date_from.'" and vs_adv_money.date<="'.$date_to.'") as total_per_month':'').'
			from
			 	`account`
				inner join `party` on `party`.user_id=`account`.id
				inner join `vs_adv_money` on `vs_adv_money`.account_id=`account`.`id`
				inner join `groups` on `groups`.id=`account`.`group_id`
				LEFT JOIN order_source on order_source.id = vs_adv_money.source_id
				LEFT JOIN bundles on bundles.id = vs_adv_money.bundle_id
			where
				'.$cond.'
			order by
			    account.id,vs_adv_money.date desc,vs_adv_money.created_date desc 
			limit '.((page_no()-1)*$item_per_page).','.$item_per_page.'
		';
        $items = DB::fetch_all($sql);
        $i=1;
        foreach ($items as $key=>$value) {
            $items[$key]['i']=$i++;
            $page_name = DB::fetch('select id,page_name from fb_pages WHERE account_id="'.$value['id'].'"','page_name');
            $items[$key]['page_name'] = $page_name;
        }
        return $items;
    }
    static function get_statuses(){
        $sql = '
            SELECT
              statuses.*
            FROM
              statuses
            WHERE
              statuses.group_id = '.Session::get('group_id').'
              or statuses.is_system=1
            ORDER BY
              statuses.name
        ';
        $items = DB::fetch_all($sql);
        return $items;
    }

    static function get_source() {
		$conditions = "group_id = 0";
        $query = "SELECT `id`, `name`, `default_select`
            FROM order_source
            WHERE $conditions";

        $query = formatQuery($query);
        return DB::fetch_all($query);
    }

	static function getSystemSources(int $includeSourceId = 0) {
        $cols = ['id', 'name'];
        $select = generateColumns($cols);
        $query = "SELECT $select
            FROM order_source
            WHERE `group_id` = 0
                AND `ref_id` = 0
                AND `name` IS NOT NULL
                AND `name` <> ''
        ";
        if ($includeSourceId) {
            $query .= " OR `id` = $includeSourceId";
        }//end if

        $query = formatQuery($query);
        return DB::fetch_all($query);
    }

    /**
     * getSystemBundles function
     *
     * @return array
     */
    static function getSystemBundles(): array
    {
        $sql = "SELECT `id`, `name`
            FROM  bundles
            WHERE group_id = 0 AND standardized = 1
            ORDER BY `name`";

        return DB::fetch_all($sql);
    }

    static function get_bundle(){
		$groupId = Session::get('group_id');
        $sql = "SELECT `id`, `name`
				FROM bundles
				WHERE group_id = $groupId
				ORDER BY `id`";

        $bundles = DB::fetch_all($sql);
        return $bundles;
    }

    public static function getUserByIDs(array $IDs)
    {
        $IDs = array_filter($IDs, function($ID){
            return $ID > 0;
        });

        return $IDs ? DB::fetch_all('SELECT `id`, `username`, `name` FROM `users` WHERE `id` IN (' . implode(',', $IDs) . ')') : [];
    }

	public static function buildSourceCond(): string
	{
		$sourceId = Url::get('source_id');
		if (!$sourceId) {
			return '';
		}//end if
		
		$sourceId =  DB::escape($sourceId);

		$_sql = "SELECT `include_ids` FROM `order_source` WHERE id = $sourceId";
		$sourceIds = DB::fetch($_sql, 'include_ids');
		$_sourceIds = explode(',', $sourceIds);
		$_sourceIds = array_filter($_sourceIds, function($value) { return !is_null($value) && $value !== ''; });

		if (!$_sourceIds) {
			return " AND vs_adv_money.source_id = $sourceId";
		}//end if
		
		array_unshift($_sourceIds, $sourceId);
		$_sourceIds = DB::escapeArr($_sourceIds);
		$sourceIds = implode(',', $_sourceIds);
		return " AND vs_adv_money.source_id IN ($sourceIds)";
	}

	public static function buildBundleCond(): string
	{
		$bundleId = Url::get('bundle_id');
		if (!$bundleId) {
			return '';
		}//end if

		$bundleId =  DB::escape($bundleId);
		$_sql = "SELECT `include_ids` FROM `bundles` WHERE id = $bundleId";
		$ids = DB::fetch($_sql, 'include_ids');
		$_ids = explode(',', $ids);
		$_ids = array_filter($_ids, function($value) { return !is_null($value) && $value !== ''; });

		if (!$_ids) {
			return " AND vs_adv_money.bundle_id = $bundleId";
		}//end if
		
		array_unshift($_ids, $bundleId);
		$_ids = DB::escapeArr($_ids);
		$ids = implode(',', $_ids);
		return " AND vs_adv_money.bundle_id IN ($ids)";
	}
}
?>
