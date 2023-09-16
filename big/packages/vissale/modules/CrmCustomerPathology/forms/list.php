<?php

class ListCrmCustomerPathologyForm extends Form
{
    const MD5KEY = CATBE;
	protected $map;
	protected $customer_data;
    protected $cid;

    function __construct()
	{
		Form::Form('ListCrmCustomerPathologyForm');

	    $this->init();
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

	function draw()
	{
		$this->map =  array();
		$this->map['title'] = 'Quản lý bệnh lý';
        if (!empty($this->customer_data['name'])) {
            $this->map['title'] .= ": " . $this->customer_data['name'];
        }

        //array_unshift($this->map['status_value'], "Tất cả");
        //array_unshift($this->map['status_list'], "Tất cả");

		require_once 'packages/core/includes/utils/paging.php';
		$item_per_page = 20;
		if(!Url::get('item_per_page')){
			$_REQUEST['item_per_page'] = $item_per_page;
		}else{
			$item_per_page = Url::get('item_per_page');
		}
		$total = CrmCustomerPathologyDB::get_total_item();
		$items = CrmCustomerPathologyDB::get_items(false,false, $item_per_page);
		if(count($items) > 0){
            $length = get_group_options('hide_phone_number');
            foreach($items as $keyItems => $rowItems){
                $items[$keyItems]['customer_mobile'] = ModifyPhoneNumber::hidePhoneNumber($rowItems['customer_mobile'], $length);
            }
        }

		$paging = paging($total,$item_per_page,10,false,'page_no',
			array('cmd','item_per_page','search_bill_text' ,'from_bill_date', 'to_bill_date','cid')
		);


		$this->map += array(
			'items'=>$items,
			'paging'=>$paging,
			'total'=>$total
		);
		$this->parse_layout('list',$this->map);
	}

    protected function init()
    {
        if (empty(Url::get('cid'))) {
            //Url::js_redirect('customer','Bện lý đi kèm với khách hàng nên bạn vui lòng vào chọn khách hàng để xem.');
        }
        $this->cid = URL::get('cid');
        $this->customer_data = CrmCustomerPathologyDB::get_customer($this->cid);
    }

}

