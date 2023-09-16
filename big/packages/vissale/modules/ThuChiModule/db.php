<?php
define('CASH',1);
define('BANK',2);
define('CARD',3);

class ThuChiModuleDB {
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
    public static $teams = array(
        '-1'=>'Chọn nhóm',
        '1'=>'Khách hàng',
        '2'=>'Nhà cung cấp',
        '3'=>'Nhân viên',
        '4'=>'Công ty tổng',
        '5'=>'Khác',
    );
    static function get_total_amount($cond,$type){
        $searchQuery = ThuChiModuleDB::generateSearchConditions();
        $sql = '
          SELECT 
            sum(cash_flow_detail.amount) as total
          FROM 
            cash_flow_detail
            INNER JOIN cash_flow ON cash_flow.id =  cash_flow_detail.cash_flow_id
            inner join `groups` on groups.id =  cash_flow.group_id
          WHERE 
            '.$cond.' '.$searchQuery.' AND cash_flow_detail.payment_method='.$type.' 
          ';
        return DB::fetch($sql,'total');
    }
    static function get_payment($order_id){
        $sql = '
				select
					cash_flow_detail.*
				from
					cash_flow_detail
				WHERE
					cash_flow_detail.cash_flow_id='.$order_id.'
				order by
					cash_flow_detail.id DESC
			';
        $items = DB::fetch_all($sql);
        foreach($items as $key=>$value){
            $items[$key]['amount'] = System::display_number($value['amount']);
        }
        return $items;
    }
    static function update_payment($rows){
        $order_id = $rows['id'];
        if(isset($_REQUEST['mi_payment'])){
            $data = '';
            foreach($_REQUEST['mi_payment'] as $key=>$record){
                if($record['id']=='(auto)'){
                    $record['id']=false;
                }
                $record['cash_flow_id'] = $order_id;
                if($record['cash_flow_id']){
                    $record['order_type'] = $record['order_type']?$record['order_type']:0;
                    $record['order_id'] = $record['order_id']?$record['order_id']:0;
                    $record['amount'] = $record['amount']?System::calculate_number($record['amount']):0;

                    if($record['id'] and $payment=DB::select('cash_flow_detail','id='.$record['id'])){
                        if($record['amount'] and $payment['amount'] !=  $record['amount']){
                            $data .= 'Thay đổi Số tiền của mã thanh toán#'.$record['id'].' từ <strong>'.$payment['amount'].'</strong> thành <strong>'.$record['amount'].'</strong>';
                        }
                        if($payment['payment_method'] != $record['payment_method']){
                            $data .= ($data?', ':''). "Mã thanh toán #{$payment['id']} " . 'thay đổi phương thức thanh toán từ '.self::getPaymentMethodName($payment['payment_method']) .' thành ' . self::getPaymentMethodName($record['payment_method']);
                        }
                        //$record['user_id'] = get_user_id();
                        DB::update('cash_flow_detail', $record,'id='.$record['id']);
                    } else {
                        unset($record['id']);
                        $record['id'] = DB::insert('cash_flow_detail',$record);
                        $data .= "Thêm mã thanh toán: id#{$record['id']} #{$record['amount']} phương thức ". self::getPaymentMethodName($record['payment_method']);
                        if($data){
                            $data .= '<br>';
                        }
                    }
                }
            }
            if($data){
                $insertData = array(
                    'type' => 'create',
                    'created_account_id' => Session::get('user_id'),
                    'cash_flow_id' => $rows['id'],
                    'group_id' => $rows['group_id'],
                    'content' => $data,
                    'created_time' => time()
                );
                DB::insert('cash_flow_log', $insertData);
            }

            if(URL::get('deleted_ids')){
                $ids = explode(',',URL::get('deleted_ids'));
                $data = '';
                foreach($ids as $del_id){
                    if($payment=DB::select('cash_flow_detail','id='.$del_id)){
                        $data .= 'Xóa lượt thanh toán <strong> #'.$del_id.':'.$payment['amount'].'</strong>' . self::getPaymentMethodName($payment['payment_method']) . ' ';
                        DB::delete_id('cash_flow_detail',$del_id);
                    }
                }
                if($data){
                    $insertData = array(
                        'type' => 'delete',
                        'created_account_id' => Session::get('user_id'),
                        'cash_flow_id' => $rows['id'],
                        'group_id' => $rows['group_id'],
                        'content' => $data,
                        'created_time' => time()
                    );
                    DB::insert('cash_flow_log', $insertData);
                }
            }
        }
    }
    static function get_total_item($cond){
		$searchQuery = ThuChiModuleDB::generateSearchConditions();
        return DB::fetch(
			'select
				count(cash_flow.id) as total
			from
				cash_flow
				inner join `groups` on groups.id = cash_flow.group_id
                '.(Url::get('cid')?' INNER JOIN spa_turn_card ON spa_turn_card.id=cash_flow.turn_card_id':'').'
			where
				'.$cond.' '.$searchQuery.'
				'
			,'total');
	}
	static function get_items($cond,$order_by='cash_flow.id DESC',$item_per_page){
		$searchQuery = ThuChiModuleDB::generateSearchConditions();
        $sql = '
				SELECT 
					cash_flow.id,
					cash_flow.bill_number,
					cash_flow.amount,
					cash_flow.bill_date,
					cash_flow.created_time,
					cash_flow.created_full_name,
					cash_flow.created_account_id,
					cash_flow.received_full_name,
					cash_flow.payment_full_name,
					cash_flow.note,
                    cash_flow.turn_card_id,
                    cash_flow.order_id,
					IF(cash_flow.bill_type=1,"receive", "pay") bill_type,
					cash_flow.category_id,
					cash_flow.team_id,
					cash_flow.deleted_account_id,
					cash_flow.del,
					groups.name AS group_name,
					(SELECT sum(amount) FROM cash_flow_detail WHERE cash_flow_id = cash_flow.id AND payment_method='.CASH.' GROUP BY cash_flow_id) AS cash_amount,
					(SELECT sum(amount) FROM cash_flow_detail WHERE cash_flow_id = cash_flow.id AND payment_method='.CARD.' GROUP BY cash_flow_id) AS card_amount,
					(SELECT sum(amount) FROM cash_flow_detail WHERE cash_flow_id = cash_flow.id AND payment_method='.BANK.' GROUP BY cash_flow_id) AS bank_amount,
					(SELECT sum(amount) FROM cash_flow_detail WHERE cash_flow_id = cash_flow.id) AS total_amount
				FROM 
					cash_flow
					inner join `groups` on groups.id = cash_flow.group_id
                    '.(Url::get('cid')?' INNER JOIN spa_turn_card ON spa_turn_card.id=cash_flow.turn_card_id':'').'
				WHERE
					'.$cond.' '.$searchQuery.'
				ORDER BY cash_flow.id DESC
				LIMIT
					'.((page_no()-1)*$item_per_page).','.$item_per_page.'
			';

		$items = DB::fetch_all($sql);
		return $items;
	}

	static function get_item(){
        $group_id = ThuChiModule::$group_id;
        $sql = '
			SELECT cash_flow.id,
					cash_flow.bill_number,
					cash_flow.amount,
					cash_flow.bill_date,
					cash_flow.created_full_name,
					cash_flow.created_account_id,
					cash_flow.received_full_name,
					cash_flow.payment_full_name,
					cash_flow.note,
                    cash_flow.turn_card_id,
                    cash_flow.mobile,
                    cash_flow.address,
                    cash_flow.payment_type,
					IF(cash_flow.bill_type=1,"receive", "pay") bill_type,
					cash_flow.category_id,
					cash_flow.team_id,
					attachment_file
			 FROM 
			  cash_flow 
			 WHERE 
			  md5(CONCAT(cash_flow.id,"'.CATBE.'"))="'.Url::sget('id').'"
		';
		return DB::fetch($sql);
	}

	static function get_logs($cashflow_id) {
        return DB::select_all('cash_flow_log', "md5(CONCAT(cash_flow_id,'".CATBE."'))='".$cashflow_id."'", "id DESC");
    }

	static function get_max_bill_number($cond){
        return DB::fetch('
			SELECT max(bill_number) as total FROM cash_flow WHERE '.$cond.'
		','total');
	}

    static function get_statistics($cond)
    {   
        $group_id = ThuChiModule::$group_id;
        $searchQuery = ThuChiModuleDB::generateSearchConditions();
        $sql = "
            SELECT 
              ( SELECT sum(amount) FROM `cash_flow` WHERE bill_type=1 AND del=0 AND {$cond} {$searchQuery}) AS receive, ( 
              SELECT sum(amount) FROM `cash_flow` WHERE bill_type=0 AND del=0 AND {$cond} {$searchQuery}) AS pay
            FROM 
              `cash_flow`
              inner join `groups` on groups.id = cash_flow.group_id 
            LIMIT 0,1
        ";
        return DB::fetch($sql);
    }

    static function softDelete($id)
    {   $time = time();
        $deleteAccountId = Session::get('user_id');
        $group_id = ThuChiModule::$group_id;
        $account_type = Session::get('account_type');
        if($account_type==3 and check_user_privilege('ADMIN_KETOAN')){// KE TOAN TONG
            $cond = '';
        }else{
            $cond = " AND group_id={$group_id}";
        }
        if(DB::exists('select id from cash_flow where id='.$id)){
            DB::update('cash_flow',array('del'=>$time,'deleted_account_id'=>$deleteAccountId),"id={$id} {$cond}");
        }
    }

    static function update_edited_log($cashflow_id, $new_data, $type) {
        $group_id = ThuChiModule::$group_id;

        //for create
        if ($type == 'create') {
            $insertData = array(
                'type' => $type,
                'created_account_id' => Session::get('user_id'),
                'cash_flow_id' => $cashflow_id,
                'group_id' => $group_id,
                'content' => null,
                'created_time' => time()
            );
            DB::insert('cash_flow_log', $insertData);
            return;
        }
        //

        $text['bill_date'] = 'Ngày lập phiếu';
        $text['category_id'] = 'Danh mục';
        $text['team_id'] = 'Nhóm người';
        $text['received_full_name'] = 'Tên người nhận';
        $text['payment_full_name'] = 'Tên người nộp';
        $text['amount'] = 'Số tiền';
        $text['note'] = 'Nội dung';
        $text['attachment_file'] = 'Ảnh chụp';
        $text['address'] = 'Địa chỉ';
        $text['mobile'] = 'Điện thoại';
        $text['del'] = 'Xóa';

        ///
        unset($new_data['bill_number']);
        unset($new_data['bill_type']);
        unset($new_data['total_price']);
        unset($new_data['created_time']);
        unset($new_data['received_account_id']);
        unset($new_data['created_account_id']);
        unset($new_data['created_full_name']);
        unset($new_data['created_full_name']);
        unset($new_data['approved_account_id']);
        unset($new_data['approved_full_name']);
        unset($new_data['approved_time']);
        unset($new_data['payment_type']);
        unset($new_data['payment_type']);
        unset($new_data['payment_account_id']);
        unset($new_data['payment_full_name']);
        unset($new_data['payment_time']);


        $categories = ThuChiModuleDB::generateCategories();
        $data = '';
        $cashflow = DB::select('cash_flow',"id={$cashflow_id} AND del=0");

        foreach($new_data as $key => $value){

            if( isset($cashflow[$key]) and $cashflow[$key] != $value ) {
                if ($key == 'team_id') {
                    $fromValue = self::$teams[$cashflow[$key]];
                    $toValue = self::$teams[$value];
                    $data .= '<div>Thay đổi '.$text[$key].' từ "'. $fromValue .'" => "'.$toValue.'"</div>';
                    continue;
                }
                if ($key == 'category_id') {
                    $fromValue = $categories[$cashflow[$key]];
                    $toValue = $categories[$value];
                    $data .= '<div>Thay đổi '.$text[$key].' từ "'. $fromValue .'" => "'.$toValue.'"</div>';
                    continue;
                }

                $data .= '<div>'.$text[$key].' từ "'.$cashflow[$key].'" => "'.$value.'"</div>';
            }
        }
        //var_dump( $data,$new_data, $cashflow);die;
        if ( empty($data) ) {
            return;
        }

        $insertData = array(
            'type' => $type,
            'created_account_id' => Session::get('user_id'),
            'cash_flow_id' => $cashflow_id,
            'group_id' => $group_id,
            'content' => $data,
            'created_time' => time()
        );
        DB::insert('cash_flow_log', $insertData);
    }

    static function getCategories($type)
    {
        $group_id = ThuChiModule::$group_id;
        return DB::select_all('cash_flow_category',
            'type='.$type.' AND (group_id = '.$group_id.' '.(Session::get('master_group_id')?' OR group_id='.Session::get('master_group_id'):' OR group_id=0').')', "id DESC");
    }

    static function generateCategories()
    {   $type = 0;
        if (Url::get('type')=='receive') {
            $type = 1;
        }
        $data = ThuChiModuleDB::getCategories($type);
        $categories = ['' => 'Chọn loại'];
        foreach ($data as $value) {
            $categories[$value['id']] = $value['name'];
        }
        return $categories;
    }

    static function generateSearchConditions()
    {
        $group_id = ThuChiModule::$group_id;
        $orConditions = [];
        $andConditions = [];
        if (URL::get('search_bill_text')) {
            $search_bill_text = trim(URL::get('search_bill_text'));
            $code = preg_replace('/\D/', '', $search_bill_text);
            $code = (float)$code;
            $orConditions[] = 'cash_flow.bill_number = '.$code;
            $orConditions[] = "cash_flow.received_full_name LIKE '%$search_bill_text%'";
            $orConditions[] = "cash_flow.payment_full_name LIKE '%$search_bill_text%'";
            $orConditions[] = "cash_flow.created_full_name LIKE '%$search_bill_text%'";
            $orConditions[] = "cash_flow.created_account_id LIKE '%$search_bill_text%'";
            $orConditions[] = "cash_flow.note LIKE '%$search_bill_text%'";
        }
        if ($from_bill_date=URL::get('from_bill_date')) {
            $fromDate =  Date_Time::to_sql_date($from_bill_date);
            $andConditions[] = "cash_flow.bill_date >= '$fromDate'";
        }
        if ($toBillDate=URL::get('to_bill_date')) {
            $toBillDate =  Date_Time::to_sql_date($toBillDate);
            $andConditions[] = "cash_flow.bill_date <= '$toBillDate'";
        }
        $team_id= URL::get('team_id');
        if ($team_id &&  $team_id!=-1) {
            $andConditions[] = "cash_flow.team_id = $team_id";
        }
        if ( URL::get('view_type')=='recycle_bin' ) {
            $andConditions[] = "cash_flow.del<>0";
        } else {
            $andConditions[] = "cash_flow.del=0";
        }
        //

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

    public static  function getPaymentMethodName($methodId)
    {
        switch ($methodId) {
            case BANK :
                return 'Chuyển khoản';
            case CASH :
                return 'Tiền mặt';
            case CARD :
                return 'Thẻ';
        }
        return null;
    }
}

