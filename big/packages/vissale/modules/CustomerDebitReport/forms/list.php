<?php

class ListCustomerDebitReportForm extends Form
{
    protected $map;
	function __construct()
	{

		Form::Form('ListCustomerDebitReportForm');
	}

	function delete()
	{
		if(isset($_REQUEST['selected_ids']) and count($_REQUEST['selected_ids'])>0)
		{
			foreach($_REQUEST['selected_ids'] as $key)
			{
				if($item = DB::exists_id('cash_flow', $key))
				{
                    CustomerDebitReportDB::softDelete($key);
				}
			}
		}
		Url::redirect_current();
	}

	function on_submit()
	{
		switch(Url::get('cmd'))
		{
			case 'update_position':
				$this->save_position();
				break;
			case 'delete':
				$this->delete();
				break;
		}
	}

    function modifyOrders(&$orders)
    {
        $this->map['all_orders_price'] = 0;
        $this->map['all_orders_paid'] = 0;
        $this->map['all_orders_debt'] = 0;

        foreach ($orders as &$order) {
            $order['total_debt'] = floatval($order['total_price']) - floatval($order['paid_total']);
            //
            $this->map['all_orders_price'] += floatval( $order['total_price'] );
            $this->map['all_orders_paid'] += floatval( $order['paid_total'] );
            $this->map['all_orders_debt'] += floatval( $order['total_debt'] );
            //
            $order['total_price'] = System::display_number($order['total_price']);
            $order['bank_transfer'] = System::display_number($order['bank_transfer']);
            $order['cash'] = System::display_number($order['cash']);
            $order['card'] = System::display_number($order['card']);
            $order['paid_total'] = System::display_number($order['paid_total']);
            $order['total_debt'] = System::display_number($order['total_debt']);
            $service_code = get_prefix();
            $service_code .= $order['code']?str_pad(($order['code']),6,"0",STR_PAD_LEFT):($order['code']);
            $order['code'] = $service_code;
        }


        $this->map['all_orders_price'] = System::display_number($this->map['all_orders_price']);
        $this->map['all_orders_paid'] = System::display_number($this->map['all_orders_paid']);
        $this->map['all_orders_debt'] = System::display_number($this->map['all_orders_debt']);
    }

	function draw()
	{

        $group_id = Session::get('group_id');
        $master_group_id = Session::get('master_group_id');
        if(Session::get('account_type')==3 and check_user_privilege('ADMIN_KETOAN')){//khoand edited in 14/11/2018
            $cond = ' (group_id='.$group_id.' or groups.master_group_id = '.$group_id.')';
            if($group_id_ = Url::get('group_id')){
                $cond = '(cash_flow.group_id = '.$group_id_.')';
            }
        }elseif($master_group_id){
            $cond = ' (group_id='.$group_id.' or (groups.master_group_id = '.$master_group_id.'))';
        }else{
            $cond = 'group_id = '.Session::get('group_id');
        }
		require_once 'packages/core/includes/utils/paging.php';
		
		$item_per_page = 20;
		if(!Url::get('item_per_page')){
			$_REQUEST['item_per_page'] = $item_per_page;
		}else{
			$item_per_page = Url::get('item_per_page');
		}

		$total = CustomerDebitReportDB::get_total_item($cond);
        $orders = CustomerDebitReportDB::get_items($cond, $total, $item_per_page);
        $this->modifyOrders($orders);
        //paging
		$paging = paging($total, $item_per_page,10,false,'page_no',
			array('cmd','item_per_page','order_id' ,'applied_date', 'expired_date')
		);

        $this->map['group_id_list'] = array(''=>'Chọn chi nhánh') + MiString::get_list(CustomerDebitReportDB::get_groups($group_id));
		$this->parse_layout('list',
            $this->map + array(
			'orders'=>$orders,
			'paging'=>$paging,
			'total'=>$total,
            'all_orders_price' => $this->map['all_orders_price'],
            'all_orders_paid' => $this->map['all_orders_paid'],
            'all_orders_debt' => $this->map['all_orders_debt']
		));
	}
}

