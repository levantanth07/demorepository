<?php

class CustomerDebitReportDB {

    public static $tableName = 'spa_process';

    public static $payment_methods = array(
        '1'=>'Chuyển Khoản',
        '2'=>'Tiền mặt',
        '3'=>'Thẻ'
    );
    static function get_groups($group_id){
        return DB::fetch_all('
			SELECT
				groups.id,groups.name
			FROM
                `groups`
			WHERE
				groups.master_group_id = '.$group_id.'
		');
    }
    static function get_total_item($cond){
        $group_id = Session::get('group_id');
		$searchQuery = CustomerDebitReportDB::generateSearchConditions();
        $sql = "
            SELECT COUNT(*) as total
			FROM crm_customer
			inner join `groups` on (groups.id = crm_customer.group_id)
			INNER JOIN spa_turn_card ON (spa_turn_card.customer_id = crm_customer.id)
			LEFT JOIN cash_flow ON (cash_flow.turn_card_id = spa_turn_card.id)
			WHERE crm_customer.group_id = {$group_id} {$searchQuery}";
		return DB::fetch($sql,'total');
	}

	static function get_items($cond, $order_by, $item_per_page){
        $group_id = Session::get('group_id');
        $searchQuery = CustomerDebitReportDB::generateSearchConditions();
        $offSet = (page_no()-1)* $item_per_page;
        $sql = "
            SELECT
            crm_customer.id,
            crm_customer.code,
            crm_customer.name,
            crm_customer.mobile,
            crm_customer.address,
            cash_flow.turn_card_id,
            spa_turn_card.total_price,
            (SELECT SUM(amount) FROM cash_flow cf WHERE cf.turn_card_id = spa_turn_card.id AND payment_type=2) bank_transfer,
            (SELECT SUM(amount) FROM cash_flow cf WHERE cf.turn_card_id = spa_turn_card.id AND payment_type=1) cash,		
            (SELECT SUM(amount) FROM cash_flow cf WHERE cf.turn_card_id = spa_turn_card.id AND payment_type=3) card,
            (SELECT SUM(amount) FROM cash_flow cf WHERE cf.turn_card_id = spa_turn_card.id AND cash_flow.bill_type=1) paid_total
            FROM crm_customer
            inner join `groups` on (groups.id = crm_customer.group_id)
            INNER JOIN spa_turn_card ON (spa_turn_card.customer_id = crm_customer.id)
            LEFT JOIN cash_flow ON (cash_flow.turn_card_id = spa_turn_card.id)
            WHERE crm_customer.group_id = {$group_id} $searchQuery
            GROUP BY crm_customer.id
            ORDER BY id DESC
			LIMIT {$offSet},{$item_per_page}
		";
//             System::debug($sql);die;
              //{$searchQuery}

			$items = DB::fetch_all($sql);
		return $items;
	}


	static function get_items_by_customer(){

        if (empty(URL::get('customer_id'))
        ) {
            return  [];
        }

		$group_id = Session::get('group_id');
		$searchQuery = CustomerDebitReportDB::generateSearchConditions();
		$sql = "
			SELECT 
			spa_process.id,
            spa_process.code,
            spa_process.customer_id,
            spa_process.customer_name,
            spa_process.user_created,
            spa_process.status_id,
            spa_process.total_price,
            spa_process.mobile,
            spa_process.address,
            spa_process.city,
            spa_process.turn_card_id,
            
            (SELECT SUM(amount) FROM cash_flow cf WHERE cf.turn_card_id = spa_process.turn_card_id AND payment_type=1) bank_transfer,
            (SELECT SUM(amount) FROM cash_flow cf WHERE cf.turn_card_id = spa_process.turn_card_id AND payment_type=2) cash,		
            (SELECT SUM(amount) FROM cash_flow cf WHERE cf.turn_card_id = spa_process.turn_card_id AND payment_type=3) card,
            (SELECT SUM(amount) FROM cash_flow cf WHERE cf.turn_card_id = spa_process.turn_card_id) paid_total
			 FROM spa_process 
			 inner join `groups` on (groups.id = spa_process.group_id)
			 INNER JOIN cash_flow ON (cash_flow.turn_card_id = spa_process.turn_card_id)
			 WHERE spa_process.group_id = {$group_id} 
			 AND {$searchQuery}
		";
        return DB::fetch_all($sql);
	}

    static function getPaymentByOrder(Array $order_ids)
    {
        $group_id = Session::get('group_id');

        $order_ids = join(',', $order_ids);
        $sql = "SELECT * FROM spa_process_payment WHERE order_id IN ($order_ids)";
        return DB::fetch_all($sql);
    }

	static function get_logs($cashflow_id) {
        $group_id = Session::get('group_id');
        return DB::select_all('spa_process_log', "group_id = {$group_id} AND spa_process_id={$cashflow_id}", "id DESC");
    }

	static function get_max_bill_number($cond){
        return DB::fetch('
			SELECT max(bill_number) as total FROM spa_process WHERE '.$cond.'
		','total');
	}

    static function get_statistics($cond)
    {   
        $group_id = Session::get('group_id');
        $searchQuery = CustomerDebitReportDB::generateSearchConditions();
        $sql = "
            SELECT ( SELECT sum(amount) FROM `spa_process` WHERE bill_type=1 AND del=0 AND {$cond} {$searchQuery}) AS receive, ( 
            SELECT sum(amount) FROM `spa_process` WHERE bill_type=0 AND del=0 AND {$cond} {$searchQuery}) AS pay
            FROM `spa_process` 
            LIMIT 0,1
        ";
        return DB::fetch($sql);
    }

    static function softDelete($id)
    {   $time = time();
        $deleteAccountId = Session::get('user_id');
        $group_id = Session::get('group_id');
        if(DB::exists('select id from spa_process where id='.$id)){
            DB::update('spa_process',array('del'=>$time,'deleted_account_id'=>$deleteAccountId),"id={$id} AND group_id={$group_id}");
        }
    }


    

    static function generateSearchConditions()
    {
        $orConditions = [];
        $andConditions = [];

//        $andConditions[] = "";

        if ($customer_id = URL::get('customer_id')) {
            $andConditions[] = "(customer_id = $customer_id OR id=$customer_id)";
        }

        if ($appliedDate=URL::get('applied_date')) {
            $appliedDate =  Date_Time::to_time($appliedDate);
            $andConditions[] = "cash_flow.created_time >= '$appliedDate'";
        }

        if ($expiredDate=URL::get('expired_date')) {
            $expiredDate =  Date_Time::to_time($expiredDate) + 24*3600;
            $andConditions[] = "cash_flow.created_time < '$expiredDate'";
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
}

