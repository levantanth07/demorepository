<?php
class ListQlbhStockInvoiceForm extends Form
{
	function __construct()
	{
		Form::Form('ListQlbhStockInvoiceForm');
	}
	function draw()
	{
		$this->map = array();
		$item_per_page = 100;
		$cond = 'qlbh_stock_invoice.group_id= '.Session::get('group_id').'
			'.((!User::can_admin() and Session::is_set('warehouse_id'))?' AND qlbh_stock_invoice_detail.warehouse_id = \''.Session::get('warehouse_id').'\'':'').'
			'.(Url::get('type')?' AND qlbh_stock_invoice.type = \''.DB::escape(Url::sget('type')).'\'':'').'
			'.(Url::get('user_id') && Url::get('type') == 'EXPORT'?' AND qlbh_stock_invoice.user_id = \''.DB::escape(Url::sget('user_id')).'\'':'').'
			'.(Url::get('note')?' AND UPPER(qlbh_stock_invoice.note) LIKE \'%'.strtoupper(DB::escape(Url::sget('note'))).'%\'':'').'
			'.(Url::get('receiver_name')?' AND qlbh_stock_invoice.receiver_name LIKE \'%'.DB::escape(Url::sget('receiver_name')).'%\'':'').'
			'.(Url::get('create_date_from')?' AND qlbh_stock_invoice.create_date >= \''.Date_Time::to_sql_date(Url::sget('create_date_from')).'\'':'').'
			'.(Url::get('create_date_to')?' AND qlbh_stock_invoice.create_date <= \''.Date_Time::to_sql_date(Url::sget('create_date_to')).'\'':'').'
			'.(Url::get('warehouse_id')?' AND qlbh_stock_invoice_detail.warehouse_id = '.Url::iget('warehouse_id').'':'').'
			'.(Url::get('supplier_id')?' AND qlbh_stock_invoice.supplier_id = '.Url::iget('supplier_id').'':'').'
			';
		$billNumber = trim(Url::get('bill_number'));
        $subBillNumber = substr($billNumber,0,2);
        if (strtoupper($subBillNumber) == 'PX' || strtoupper($subBillNumber) == 'PN') {
            $billNumber = substr($billNumber,2);
        }
        $andBillNumber = '';
        if (strlen($billNumber) == 0) {
            $andBillNumber = '';
        } else {
            $andBillNumber = ' AND qlbh_stock_invoice.bill_number LIKE  "'.DB::escape($billNumber).'%"';
        }
        $cond .= $andBillNumber;
						//'.(Url::get('bill_number')?' AND qlbh_stock_invoice.bill_number LIKE \'%'.DB::escape(Url::sget('bill_number')).'%\'':'').'
		$this->map['title'] = (Url::get('type')=='IMPORT')?'Quản lý nhập kho':'Quản lý xuất kho';
		$sqlTotal = '
                SELECT
                    count(*) as total
                FROM
                    qlbh_stock_invoice
                    LEFT JOIN qlbh_stock_invoice_detail ON qlbh_stock_invoice_detail.invoice_id = qlbh_stock_invoice.id
                WHERE
                    '.$cond.'
                GROUP BY bill_number
                ORDER BY
                    qlbh_stock_invoice.create_date DESC,qlbh_stock_invoice.id DESC
        ';
		require_once 'packages/core/includes/utils/paging.php';
        $data = DB::query($sqlTotal);

        $this->map['total'] =  $data->num_rows;
		$this->map['paging'] =  paging($this->map['total'],$item_per_page,10,$smart=false,$page_name='page_no',array('type','create_date_from','create_date_to'));
		$sql = '
				SELECT
					qlbh_stock_invoice.*
				FROM
					qlbh_stock_invoice
				LEFT JOIN 
					qlbh_stock_invoice_detail ON qlbh_stock_invoice_detail.invoice_id = qlbh_stock_invoice.id
				WHERE
					'.$cond.'
				ORDER BY
					qlbh_stock_invoice.create_date DESC,qlbh_stock_invoice.id DESC
				LIMIT
					'.((page_no()-1)*$item_per_page).','.$item_per_page.'
		';
		$items = DB::fetch_all($sql);
		$i = 1;
		$suppliers = DB::select_all('qlbh_supplier','group_id='.Session::get('group_id').'');
		$customers = DB::select_all('qlbh_warehouse','group_id=0 or group_id='.Session::get('group_id').'','structure_id');
		$shops = QlbhStockInvoiceDB::get_shop(Session::get('user_id'));
		foreach($items as $key=>$value){
			$items[$key]['bill_number'] = ($value['type']=='IMPORT'?'PN':'PX').$value['bill_number'];
			$items[$key]['i'] = $i++;
			$items[$key]['create_date'] = Date_Time::to_common_date($value['create_date']);
			if(isset($suppliers[$value['supplier_id']])){
				$items[$key]['supplier_name'] = $suppliers[$value['supplier_id']]['name'];
			}else{
				$items[$key]['supplier_name'] = '';
			}
			if(isset($shops[$value['shop_id']])){
				$items[$key]['shop_name'] = $shops[$value['shop_id']]['name'];
			}else{
				$items[$key]['shop_name'] = '';
			}
			if(isset($customers[$value['warehouse_id']])){
				$items[$key]['warehouse_name'] = $customers[$value['warehouse_id']]['name'];
			}else{
				$items[$key]['warehouse_name'] = '';
			}
		}
		$this->map['items'] = $items;
		$this->map['shop_id_list'] = array(''=>'Chọn')+MiString::get_list($shops);
		$this->map['supplier_id_list'] = array(''=>'Tất cả NCC')+MiString::get_list($suppliers);
		$this->map['warehouse_id_list'] = array(''=>'Tất cả Kho')+MiString::get_list($customers);
		$this->map['user_id_list'] = '<option value="0">Tất cả nhân viên</option>';
        $userRequest = Url::get('user_id');
        if(!empty($userRequest)){
            foreach($this->getUser() as $key=>$val){
                if ($val['username'] == $userRequest) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                $this->map['user_id_list'] .= '<option value="'.$val['username'].'"'.$selected.'>'.$val['username'].'</option>';
            }
        } else {
            foreach($this->getUser() as $key=>$val){
                $selected = '';
                $this->map['user_id_list'] .= '<option value="'.$val['username'].'"'.$selected.'>'.$val['username'].'</option>';
            }

        }
		$layout = 'list';
		$this->parse_layout($layout,$this->map);
	}
	function getUser(){
        $group_id = Session::get('group_id');
        $cond = '';
        $cond .= ' AND account.is_active = 1';
        $sql = '
            SELECT 
               users.id,
                -- users.id as user_id,
                -- users.group_id,
                users.username
                -- account.id as username
            FROM 
                account 
            JOIN 
                users 
            ON 
                users.username = account.id
            WHERE 
                account.group_id = ' . $group_id . $cond .' '
                ;
        $results = DB::fetch_all($sql);
        return $results;
    }	
}
?>