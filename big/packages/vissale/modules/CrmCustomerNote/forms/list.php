<?php

class ListCrmCustomerNoteForm extends Form
{
    const MD5KEY = CATBE;
	protected $map;
	protected $customer_data;

    function __construct()
	{

		Form::Form('ListCrmCustomerNoteForm');

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
		$this->map['title'] = 'Quản lý ghi chú';
        if (!empty($this->customer_data['name'])) {
            $this->map['title'] .= ": " . $this->customer_data['name'];
        }

		require_once 'packages/core/includes/utils/paging.php';
		$item_per_page = 20;
		if(!Url::get('item_per_page')){
			$_REQUEST['item_per_page'] = $item_per_page;
		}else{
			$item_per_page = Url::get('item_per_page');
		}
		$total = CrmCustomerNoteDB::get_total_item();
		$items = CrmCustomerNoteDB::get_items(false,false, $item_per_page);
        if(count($items) > 0) {
            $this->modifyNotes($items);
        }


        //paging
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
        $cid = URL::get('cid');
        $this->customer_data = CrmCustomerNoteDB::get_customer($cid);
    }

    protected function generateEmotion($kind)
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

    protected function modifyNotes(&$notes)
    {
        $length = get_group_options('hide_phone_number');
        foreach ($notes as &$note) {
//            $note['emotion'] = $this->generateEmotion($note['kind']);
            $note['created_time'] = date('d/m/Y H:i', $note['created_time']);
            $note['customer_mobile'] = ModifyPhoneNumber::hidePhoneNumber($note['customer_mobile'], $length);
        }
    }

}

