<?php
class DetailCustomerDebitReportForm extends Form {

	protected $today;
    protected $team_ids;
    protected $map = [];

    function __construct(){
		Form::Form('DetailCustomerDebitReportForm');

        // $this->add('category_id',new IntType(true,'Bạn vui long chọn loại Thu / Chi'));
        // $this->add('team_id',new IntType(true,'Bạn vui long chọn nhóm người nộp / nhận'));
        //$this->add('received_full_name',new TextType(true,'Bạn vui long nhập tên người nhận', 5, 50));
		//
        $this->init();

}
    protected function init(){
        $this->today = date("Y-m-d H:i:s");
    }

	function save_item() {
        
	}


	function on_submit(){
        // var_dump($_REQUEST);
	}

    function generatePrintData()
    {
        $print = DB::fetch('select print_name,print_phone,print_address,template as print_template from order_print_template where group_id='.Session::get('group_id').' order by set_default DESC,order_print_template.id DESC LIMIT 0,1');
        $party = DB::fetch('select name as print_name from `groups` where id='.Session::get('group_id'));
        $printData = array_merge($print, $party);
        foreach ($printData as $key => $value) {
            $this->map[$key] = $value;
        }
    }

    function modifyOrders(&$orders)
    {
        $this->map['all_orders_price'] = 0;
        $this->map['all_orders_paid'] = 0;
        $this->map['all_orders_debt'] = 0;
        foreach ($orders as &$order) {
            $order['left_money'] = intval($order['total_price']) - intval($order['paid_total']);
            //
            $this->map['all_orders_price'] += intval( $order['total_price'] );
            $this->map['all_orders_paid'] += intval( $order['paid_total'] );
            $this->map['all_orders_debt'] += intval( $order['left_money'] );
            //
            $order['left_money'] = System::display_number($order['left_money']);
            $order['total_price'] = System::display_number($order['total_price']);
            $order['bank_transfer'] = System::display_number($order['bank_transfer']);
            $order['cash'] = System::display_number($order['cash']);
            $order['card'] = System::display_number($order['card']);
            $order['paid_total'] = System::display_number($order['paid_total']);
            $service_code = get_prefix();
            $service_code .= $order['code']?str_pad(($order['code']),6,"0",STR_PAD_LEFT):($order['code']);
            $order['code'] = $service_code;
        }

        $this->map['all_orders_price'] = System::display_number($this->map['all_orders_price']);
        $this->map['all_orders_paid'] = System::display_number($this->map['all_orders_paid']);
        $this->map['all_orders_debt'] = System::display_number($this->map['all_orders_debt']);
    }

    function getCustomer($orders)
    {
        $this->map['name'] = null;
        $this->map['mobile'] = null;
        $this->map['address'] = null;

        foreach ($orders as $order) {
            $this->map['name'] = $order['customer_name'];
            $this->map['mobile'] = $order['mobile'];
            $this->map['address'] = $order['address'];
            break;
        }
    }

	function draw() {
        $this->map = array();
        //
        $orders = CustomerDebitReportDB::get_items_by_customer();

        var_dump( $orders ); die();

        $this->modifyOrders($orders);
        $this->getCustomer($orders);
        //
        $this->map['orders'] = $orders;
        //
		$layout = 'detail';
        //
		$this->parse_layout($layout, $this->map);
	}
}