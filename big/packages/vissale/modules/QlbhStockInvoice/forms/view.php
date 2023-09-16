<?php
class ViewQlbhStockInvoiceForm extends Form
{
    protected $map;
    function __construct()
    {
        Form::Form('ViewQlbhStockInvoiceForm');
    }
    function draw()
    {
        $this->map = array();
        $this->map['has_price'] = get_group_options('show_price_in_export_invoice');
        require_once 'packages/core/includes/utils/currency.php';
        $group_id = Session::get('group_id');
        $group = DB::fetch('select name,address from `groups` where id = '.$group_id);
        $this->map['group_name'] = $group['name'];
        $this->map['address'] = $group['address'];
        $this->map['total_debit'] = 0;
        $this->map['total_debit'] = 0;
        $this->map['total'] = 0;
        $this->map['total_discount'] = 0;
        $item = QlbhStockInvoice::$item;
        if($item){
            if ($item['type'] == 'IMPORT' && !empty($item['order_id'])) {
                $item['note'] = $item['note'] ? $item['note'] : 'Từ ' . sizeof(explode(',',$item['order_id'])) . ' đơn chuyển hoàn (Mã: ' . str_replace(',', ', ', $item['order_id']) . ')';
            }
            if ($item['type'] == 'EXPORT' && !empty($item['order_id']) && !empty($item['bill_number'])) {
                $item['note'] =  $item['note'] ? $item['note'] : 'Từ ' . sizeof(explode(',',$item['order_id'])) . ' đơn hàng (Mã: ' . str_replace(',', ', ', $item   ['order_id']) . ')';
            }
            $cond = 'qlbh_stock_invoice.group_id='.QlbhStockInvoice::$group_id;
            if(Url::get('type')=='IMPORT'){
                $cond .= ' and qlbh_stock_invoice.create_date <= \''.$item['create_date'].'\' '.($item['supplier_id']?'AND qlbh_stock_invoice.supplier_id = '.$item['supplier_id']:'');
                $group_by = 'qlbh_stock_invoice.id';
                $this->map['title'] = 'Phiếu nhập kho';
                $item['bill_number'] = 'PN'.$item['bill_number'];
            }else{
                $cond .= ' and qlbh_stock_invoice.create_date <= \''.$item['create_date'].'\'
				'.($item['warehouse_id']?' AND qlbh_stock_invoice.warehouse_id = '.$item['warehouse_id'].'':'').'
				';
                $group_by = '
				    qlbh_stock_invoice.warehouse_id,qlbh_stock_invoice.id
				';
                $this->map['title'] = 'Phiếu xuất kho';
                $item['bill_number'] = 'PX'.$item['bill_number'];
            }
            $sql1 = '
				SELECT 
					qlbh_stock_invoice.id,
					SUM(qlbh_stock_invoice_detail.price*qlbh_stock_invoice_detail.quantity) as total_amount
				FROM
					qlbh_stock_invoice_detail
					INNER JOIN qlbh_stock_invoice ON qlbh_stock_invoice.id = qlbh_stock_invoice_detail.invoice_id
					LEFT OUTER JOIN qlbh_warehouse W1 ON W1.id = qlbh_stock_invoice_detail.warehouse_id
					LEFT OUTER JOIN qlbh_warehouse W2 ON W2.id = qlbh_stock_invoice_detail.to_warehouse_id
				WHERE
					'.$cond.'
				GROUP BY 
					'.$group_by.'
			';
            if(Session::get('user_id')=='PAL.khoand'){
                System::debug($sql1);
            }
            $total_amount = DB::fetch($sql1,'total_amount');
            $this->map['supplier_name'] = DB::fetch('SELECT id,name FROM qlbh_supplier WHERE id=\''.$item['supplier_id'].'\'','name');
            $this->map['warehouse_name'] = DB::fetch('SELECT id,name FROM qlbh_warehouse WHERE id=\''.$item['warehouse_id'].'\'','name');
            $this->map['staff_name'] = DB::fetch('SELECT id,full_name FROM party WHERE user_id=\''.$item['user_id'].'\'','full_name');
            $item['create_date'] = Date_Time::to_common_date($item['create_date']);
            $arr = explode('/',$item['create_date']);
            $this->map['year'] = $arr[2];
            $this->map['month'] = $arr[1];
            $this->map['day'] = $arr[0];
            //$item['create_date'] = Date_Time::to_common_date($item['create_date']);
            $sql  = '
				SELECT
					qlbh_stock_invoice_detail.id,
					qlbh_stock_invoice_detail.free,
					qlbh_stock_invoice_detail.discount,
					qlbh_stock_invoice_detail.product_code,
					qlbh_stock_invoice_detail.price,
					sum(qlbh_stock_invoice_detail.price*qlbh_stock_invoice_detail.quantity) as payment_price,
					sum(qlbh_stock_invoice_detail.quantity) as quantity,
					products.name as name,units.name as unit_name,
					W1.name as warehouse ,W2.name as to_warehouse,
					qlbh_stock_invoice_detail.expired_date
				FROM
					qlbh_stock_invoice_detail
					JOIN products ON products.id = qlbh_stock_invoice_detail.product_id and '.(Session::get('master_group_id')?' (products.group_id = '.Session::get('master_group_id').' or products.group_id = '.$group_id.') ':'products.group_id = '.$group_id.'').'
					LEFT JOIN units ON units.id = products.unit_id
					LEFT JOIN qlbh_warehouse W1 ON W1.id = qlbh_stock_invoice_detail.warehouse_id
					LEFT JOIN qlbh_warehouse W2 ON W2.id = qlbh_stock_invoice_detail.to_warehouse_id
				WHERE
					qlbh_stock_invoice_detail.invoice_id=\''.$item['id'].'\'
					AND '.(Session::get('master_group_id')?' (products.group_id = '.Session::get('master_group_id').' or products.group_id = '.$group_id.') ':'products.group_id = '.$group_id.'').'
				GROUP BY
				    qlbh_stock_invoice_detail.product_id,qlbh_stock_invoice_detail.price
			';
            $products = DB::fetch_all($sql);
            $i=0;
            $total_amount = 0;
            if(Session::get('user_id')=='PAL.khoand'){
                System::debug($sql);
            }
            foreach($products as $k=>$v){
                $products[$k]['i'] = ++$i;
                $payment_amount = ($v['free']?0:$v['price'])*$v['quantity'];
                $discount = $v['discount'];
                $this->map['total_discount'] += $discount;
                $this->map['total'] += $payment_amount - $discount;
                $payment_amount = $v['free']?'KM':$payment_amount - $discount;
                $total_amount += intval($payment_amount);
                $products[$k]['payment_amount'] = System::display_number($payment_amount);
                $products[$k]['price'] = System::display_number($v['price']);
                $products[$k]['discount'] = System::display_number($v['discount']);
                $products[$k]['number'] = System::display_number($v['quantity']);
                $products[$k]['payment_price'] = System::display_number($v['payment_price']);
            }
            $this->map['products'] = $products;
        }
        $this->map['total_amount'] = System::display_number($total_amount);
        $currency = DB::select('currency','id=\'VND\'');
        $this->map['total_by_letter'] = currency_to_text($this->map['total']).' '.$currency['name'];
        $this->map['total'] = System::display_number($this->map['total']);
        $this->map['total_discount'] = System::display_number($this->map['total_discount']);
        $this->map += $item;
        $layout = ((Url::get('page')=='qlbh_dai_ly_xuat_ban')?'m_':'').'view';
        if($this->map['move_product']==1){
            $layout = 'view_moved_product';
            $this->map['title'] = 'Chuyển hàng hoá nội bộ';
        }
        $this->map += QlbhStockInvoiceDB::get_order_info();
        $this->parse_layout($layout,$this->map);
    }
}
?>