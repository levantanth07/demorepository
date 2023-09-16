<?php

class CrmCustomerNoteDB {
    public static $global_cond = '';
    public static $teams = array(
        ''=>'Chọn nhóm',
        '1'=>'Khách hàng',
        '2'=>'Nhà cung cấp',
        '3'=>'Nhân viên',
        '4'=>'Khác'
    );

    function __construct()
    {
    }

    static function can_view($customer_id=false){
        return true;
    }
    static function can_add($customer_id=false){
        return true;
    }

    static function can_edit($crm_customer_note_id=false) {
        return true;
        /*if (Session::get('admin_group')) {
            return true;
        } else {
            if ($crm_customer_note_id and !DB::exists('SELECT id FROM crm_customer_notes WHERE id=' . $crm_customer_note_id . ' AND created_user_id = ' . get_user_id())) {
                return false;
            } else {
                return true;
            }
        }*/
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

    static function get_total_item(){
		$searchQuery = CrmCustomerNoteDB::generateSearchConditions();
        // $group_id = Session::get('group_id');
        // $cid = URL::get('cid');
        // $cond = '';

        $sql = "
            SELECT COUNT(crm_customer_notes.id) as total
            FROM crm_customer_notes
            INNER JOIN crm_customer ON (crm_customer.id = crm_customer_notes.customer_id)
            WHERE  {$searchQuery}
        ";
        return DB::fetch($sql, 'total');
	}

	static function get_items($cond,$order_by,$item_per_page){
        $group_id = Session::get('group_id');
		$searchQuery = CrmCustomerNoteDB::generateSearchConditions();
        $offset = (page_no()-1)*$item_per_page;
        $limit = $item_per_page;
		$sql = "
				SELECT
					crm_customer_notes.id,
					crm_customer_notes.content,
					crm_customer_notes.created_time,
					crm_customer_notes.customer_id,
					crm_customer_notes.kind,
					crm_customer_notes.created_user_id,
					crm_customer.name AS customer_name,
					crm_customer.mobile AS customer_mobile,
					users.name as created_user_name
				FROM
					crm_customer_notes
                INNER JOIN crm_customer ON (crm_customer.id = crm_customer_notes.customer_id)
                INNER JOIN users ON (users.id = crm_customer_notes.created_user_id)
				WHERE
					{$searchQuery}
				ORDER BY id DESC
				LIMIT {$offset}, {$limit}
			";

//             System::debug($sql);die;

		$items = DB::fetch_all($sql);
		$idUser = CrmCustomerNoteDB::getIdUser();
		$returnItems = array();
		if($idUser != ''){
            foreach ($items as $key => $item) {
                if($item['created_user_id'] == $idUser){
                    $returnItems[$key] = $item;
                    $returnItems[$key]['emotion'] = self::generateEmotion($item['kind']);
                }
            }
        }else{
            foreach ($items as $key => $item) {
                $returnItems[$key] = $item;
                $returnItems[$key]['emotion'] = self::generateEmotion($item['kind']);
            }
        }

		return $returnItems;
	}

    static function get_item($nid)
    {
        $nid = DB::escape($nid);
        $group_id = Session::get('group_id');
        $sql = "
				SELECT
					crm_customer_notes.id,
					crm_customer_notes.content,
					from_unixtime(crm_customer_notes.created_time,'%d/%m/%Y %H:%i') AS created_time,
					crm_customer_notes.customer_id,
					crm_customer_notes.kind,
					crm_customer_notes.created_user_id,
					crm_customer.name AS customer_name,
					crm_customer.mobile AS customer_mobile,
					users.name as created_user_name
				FROM
					crm_customer_notes
                INNER JOIN crm_customer ON (crm_customer.id = crm_customer_notes.customer_id)
                INNER JOIN users ON (users.id = crm_customer_notes.created_user_id)
				WHERE MD5(CONCAT(crm_customer_notes.id, '".CATBE."'))='{$nid}' AND crm_customer_notes.group_id = {$group_id}
			";
        return DB::fetch($sql);
    }

    static function get_customer($cid){
        $group_id = Session::get('group_id');
        $cid = DB::escape($cid);
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

	static function get_logs($cashflow_id) {
        $cashflow_id = DB::escape($cashflow_id);
        $group_id = Session::get('group_id');
        return DB::select_all('crm_customer_notes_log', "group_id = {$group_id} AND crm_customer_notes_id={$cashflow_id}", "id DESC");
    }

	static function get_max_bill_number($cond){
        return DB::fetch('
			SELECT max(bill_number) as total FROM crm_customer_notes WHERE '.$cond.'
		','total');
	}

    static function get_statistics($cond)
    {   
        $group_id = Session::get('group_id');
        $searchQuery = CrmCustomerNoteDB::generateSearchConditions();
        $sql = "
            SELECT ( SELECT sum(amount) FROM `crm_customer_notes` WHERE bill_type=1 AND del=0 AND {$cond} {$searchQuery}) AS receive, ( 
            SELECT sum(amount) FROM `crm_customer_notes` WHERE bill_type=0 AND del=0 AND {$cond} {$searchQuery}) AS pay
            FROM `crm_customer_notes` 
            LIMIT 0,1
        ";
        return DB::fetch($sql);
    }

    static function softDelete($id)
    {   $time = time();
        $deleteAccountId = Session::get('user_id');
        $group_id = Session::get('group_id');
        if(DB::exists('select id from crm_customer_notes where id='.$id)){
            DB::update('crm_customer_notes',array('del'=>$time,'deleted_account_id'=>$deleteAccountId),"id={$id} AND group_id={$group_id}");
        }
    }

    public static function generateEmotion($kind)
    {
        switch (intval($kind)) {
            case 1:
                return '<i class="fa fa-3x text-success fa-smile-o"></i>';
            case -1:
                return '<i class="fa fa-3x text-danger fa-frown-o"></i>';
            default :
                return '<i class="fa fa-3x text-dark fa-meh-o"></i>';
        }
    }
    static function generateSearchConditions()
    {
        $group_id = Session::get('group_id');
        $orConditions = [];
        $andConditions = [];
        $andConditions[] = "crm_customer_notes.group_id = $group_id";
        if ( Session::get('account_type')!=3 ) {
            //$andConditions[] = "crm_customer_notes.group_id = {$group_id}";
        }
        if ($cid = URL::iget('cid')) {
            $andConditions[] = "crm_customer_notes.customer_id=$cid";
        }

        if ($customer_text = DB::escape(URL::get('customer_text'))) {
            $orConditions[] = "crm_customer.id=" . intval($customer_text);
            $orConditions[] = "crm_customer.mobile LIKE '%{$customer_text}%'";
            $orConditions[] = "crm_customer.name LIKE '%{$customer_text}%'";
        }

        if ($from_date = URL::get('from_date')) {
            $fromDate =  Date_Time::to_time($from_date);
            $andConditions[] = "created_time >= '$fromDate'";
        
        }
        if ($to_date=URL::get('to_date')) {
            $toDate =  Date_Time::to_time($to_date) + (24*3600);
            $andConditions[] = "created_time <= '$toDate'";
        }
        $results = '';
        $orQuery = join(' OR ', $orConditions);
        $andQuery = join(' AND ', $andConditions);

        if ( !empty($andQuery) ) {
            $results .= " ($andQuery)";
        }
        if ( !empty($orQuery) ) {
            $results .= " AND ($orQuery)";
        }

        return $results;
    }
}

