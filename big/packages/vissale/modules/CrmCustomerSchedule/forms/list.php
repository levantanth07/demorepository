<?php

class ListCrmCustomerScheduleForm extends Form
{
    const MD5KEY = CATBE;
	protected $map;
    protected $customer_kinds;
    protected $customer_data;
    protected $branches;
    protected $categories;
    protected $status;
    protected $schedule_type;
    protected $cid;
    protected $today;

    function __construct()
	{
		Form::Form('ListCrmCustomerScheduleForm');

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
		$this->map['title'] = 'Quản lý lịch hẹn';

        if (!empty($this->customer_data['name'])) {
            $this->map['title'] .= ": " . '<a href="'.Url::build('customer',['cid'=>$this->customer_data['id'],'do'=>'view']).'#lichhen">'.$this->customer_data['name'].'</a>';
        }

        $this->map['status'] = $this->status;
        $this->map['branch_id_list'] = $this->__generate_branches();
        $this->map['status_id_list'] = $this->__generate_status();
        $this->map['schedule_type_list'] = $this->__generate_schedule_types();
		$item_per_page = 20;
		if ( !Url::get('item_per_page') ) {
			$_REQUEST['item_per_page'] = $item_per_page;
		} else {
			$item_per_page = Url::get('item_per_page');
		}
        $this->map['item_per_page'] = $item_per_page;
        //and check_user_privilege('CUSTOMER')
       $searchQuery = CrmCustomerScheduleDB::conditions();
        $total = CrmCustomerScheduleDB::get_total_item($searchQuery);
		$items = CrmCustomerScheduleDB::get_items($searchQuery,false, $item_per_page);
        if(count($items) > 0){
            $length = get_group_options('hide_phone_number');
            foreach($items as $key => $value){
                $items[$key]['customer_mobile'] = ModifyPhoneNumber::hidePhoneNumber($value['customer_mobile'], $length);
            }
        }
        //paging
		$paging = paging($total, $item_per_page,10,false,'page_no',
			array('cmd','item_per_page','customer_text' ,'from_date', 'to_date','cid','branch_id','status_id','from_date','to_date','user_id')
		);
        $this->map['user_id_list'] = [''=>'Người tạo'] + MiString::get_list(CrmCustomerScheduleDB::get_users(),'full_name');

		$this->map += array(
			'items'=>$items,
			'paging'=>$paging,
			'total'=>$total
		);

		$this->parse_layout('list',$this->map);
	}

    protected function init()
    {
        $this->today = date("Y-m-d H:i:s");
        $this->cid = URL::iget('cid');
        $this->customer_data = CrmCustomerScheduleDB::get_customer($this->cid);
        $this->branches = CrmCustomerScheduleDB::getGroupsByMasterGroup();
        //$this->categories = CrmCustomerScheduleDB::get_categories();
        $this->status = CrmCustomerScheduleDB::$status;
        $this->schedule_type = CrmCustomerScheduleDB::$schedule_type;
    }

    protected function modifySchedules(&$notes)
    {

    }

    private function __generate_branches()
    {
        $branches[0] = 'Tất cả chi nhánh';
        foreach ($this->branches as $branch) {
            $branches[$branch['id']] = $branch['name'];
        }
        return $branches;
    }

    private function __generate_schedule_types()
    {
        $types[0] = 'Tất cả loại';
        foreach ($this->schedule_type as $index => $type) {
            $types[$index] = $type;
        }
        return $types;
    }

    private function __generate_status()
    {
        $status[0] = 'Tất cả trạng thái';
        foreach ($this->status as $key => $value) {
            $status[$key] = $value;
        }
        return $status;
    }

}

