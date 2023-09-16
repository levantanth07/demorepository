<?php

class CrmCustomerPathologyDB {

    function __construct()
    {

    }

    static function can_view($customer_id=false){
        return true;
    }
    static function can_add($customer_id=false){
        return true;
    }

    static function can_edit($customer_pathology_id=false) {
        return true;
        /*if (Session::get('admin_group')) {
            return true;
        } else {
            if ($customer_pathology_id and !DB::exists('SELECT id FROM crm_customer_pathology WHERE id=' . $customer_pathology_id . ' AND created_user_id = ' . get_user_id())) {
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
		$searchQuery = CrmCustomerPathologyDB::generateSearchConditions();
        // $group_id = Session::get('group_id');
        // $cid = URL::get('cid');
        $sql = "
            SELECT COUNT(crm_customer_pathology.id) as total
            FROM crm_customer_pathology
            INNER JOIN crm_customer ON (crm_customer.id = crm_customer_pathology.customer_id)
            WHERE {$searchQuery}
        ";

        return DB::fetch($sql, 'total');
	}

	static function get_items($cond,$order_by,$item_per_page){
		$searchQuery = CrmCustomerPathologyDB::generateSearchConditions();
        $offset = (page_no()-1)*$item_per_page;
        $limit = $item_per_page;
		$sql = "
				SELECT
					crm_customer_pathology.id,
					FROM_UNIXTIME(crm_customer_pathology.created_time,'%d/%m/%Y') AS created_time,
					crm_customer_pathology.customer_id,
					crm_customer_pathology.created_user_id,
					crm_customer_pathology.name,
					crm_customer_pathology.note,
					crm_customer.name AS customer_name,
					crm_customer.mobile AS customer_mobile,
					users.name as created_user_name
				FROM
					crm_customer_pathology
                    INNER JOIN crm_customer ON (crm_customer.id = crm_customer_pathology.customer_id)
                    INNER JOIN users ON (users.id = crm_customer_pathology.created_user_id)
				WHERE 
				  {$searchQuery}
				ORDER  BY crm_customer_pathology.id DESC
				LIMIT {$offset}, {$limit}
			";

        $items = DB::fetch_all($sql);
        $idUser = CrmCustomerPathologyDB::getIdUser();
        $returnItems = array();
        if($idUser != ''){
            foreach($items as $key => $row){
                if($row['created_user_id'] == $idUser){
                    $returnItems[$key] = $row;
                }
            }
        }else{
            $returnItems = $items;
        }

        return $returnItems;
	}

    static function get_item($nid)
    {
        $nid = DB::escape($nid);
        $group_id = Session::get('group_id');
        $sql = "
				SELECT
					crm_customer_pathology.id,
					crm_customer_pathology.name,
					crm_customer_pathology.note,
					FROM_UNIXTIME(crm_customer_pathology.created_time,'%d/%m/%Y') AS created_time,
					crm_customer_pathology.customer_id,
					crm_customer_pathology.created_user_id,
					crm_customer.name AS customer_name,
					crm_customer.mobile AS customer_mobile,
					users.name as created_user_name
				FROM
					crm_customer_pathology
                INNER JOIN crm_customer ON (crm_customer.id = crm_customer_pathology.customer_id)
                INNER JOIN users ON (users.id = crm_customer_pathology.created_user_id)
				WHERE MD5(CONCAT(crm_customer_pathology.id, '".CATBE."'))='{$nid}' AND crm_customer_pathology.group_id = {$group_id}
			";
        return DB::fetch($sql);
    }

    static function get_customer($cid) {
        $group_id = Session::get('group_id');
        if($cid){
            $cid = DB::escape($cid);
            $sql = "
            SELECT id,name,email 
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
        return DB::select_all('crm_customer_pathology_log', "group_id = {$group_id} AND crm_customer_pathology_id={$cashflow_id}", "id DESC");
    }

	static function get_max_bill_number($cond){
        return DB::fetch('
			SELECT max(bill_number) as total FROM crm_customer_pathology WHERE '.$cond.'
		','total');
	}

    static function generateSearchConditions()
    {
        $group_id = Session::get('group_id');
        $orConditions = [];
        $andConditions = [];
        $andConditions[] = "crm_customer_pathology.group_id=$group_id";
        if ($cid = URL::iget('cid')) {
            $andConditions[] = "crm_customer.id=$cid";
        }

        if ($customer_text = DB::escape(URL::get('customer_text'))) {
            $orConditions[] = "crm_customer.id=" . intval($customer_text);
            $orConditions[] = "crm_customer.mobile LIKE '%{$customer_text}%'";
            $orConditions[] = "crm_customer.name LIKE '%{$customer_text}%'";
        }

        if ($from_date=URL::get('from_date')) {
            $fromDate =  Date_Time::to_time($from_date);
            $andConditions[] = "crm_customer_pathology.created_time >= '$fromDate'";
        
        }


        if ($to_date=URL::get('to_date')) {
            $toDate =  Date_Time::to_time($to_date) + (24*3600);
            $andConditions[] = "crm_customer_pathology.created_time <= '$toDate'";
        }
        $results = '';
        $orQuery = join(' OR ', $orConditions);
        $andQuery = join(' AND ', $andConditions);

        if ( !empty($andQuery) ) {
            $results .= "($andQuery)";
        }
        if ( !empty($orQuery) ) {
            $results .= " AND ($orQuery)";
        }

        return $results;
    }

    public static function generateEmotion($kind)
    {
        switch (intval($kind)) {
            case 1:
                return '<i class="fa fa-2x text-success fa-smile-o"></i>';
            case -1:
                return '<i class="fa fa-2x text-danger fa-frown-o"></i>';
            default :
                return '<i class="fa fa-2x text-dark fa-meh-o"></i>';
        }
    }
}

