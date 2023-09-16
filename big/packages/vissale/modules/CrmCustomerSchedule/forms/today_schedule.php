<?php
/**
 * Created by PhpStorm.
 * User: trinhdinh
 * Date: 2019-01-17
 * Time: 17:00
 */
require_once 'packages/core/includes/utils/paging.php';
class CrmCustomerTodayScheduleForm extends Form {

    protected $map;
    protected $branches;

    public function __construct()
    {
        $this->init();

    }


    public function on_submit()
    {

    }

    public function draw()
    {

        //get branches
        //$branches = CrmCustomerScheduleDB::getGroupsByMasterGroup();
        //$this->map['branch_id_list'] = [''=>'Chọn chi nhánh'] + CrmCustomerScheduleDB::generate_branches($branches);
        $this->parse_layout('today_schedule',$this->map);
    }

    public function init()
    {
        //$this->branches                     = CrmCustomerScheduleDB::getGroupsByMasterGroup();
        $this->map['status']                = CrmCustomerScheduleDB::$status;
        $this->map['customer_statuses']     = CrmCustomerDB::get_all_statuses();
        $this->map['customer_status_list']  = MiString::get_list($this->map['customer_statuses'], 'name');
        $this->map['total']                 = CrmCustomerScheduleDB::count_total_today_schedules();
        $this->map['total_arrival']         = CrmCustomerScheduleDB::count_arrival_today_customer();
        $this->map['total_old_customers']   = CrmCustomerScheduleDB::count_old_customers();
        $this->map['total_news_customers']   = CrmCustomerScheduleDB::count_news_customers();
        //paging
        $item_per_page =  100;
        $this->map['item_per_page']   = $item_per_page;
        $this->map['page_no']   = page_no();
        $paging = paging($this->map['total'], $item_per_page, 5,false,'page_no',
            array('cmd','item_per_page','customer_text' ,'from_date', 'to_date','cid','branch_id','status_id')
        );
        $this->map['paging'] = $paging;
        $listSchedules = CrmCustomerScheduleDB::get_today_schedules($item_per_page);
        if(count($listSchedules) > 0){
            $length = get_group_options('hide_phone_number');
            foreach($listSchedules as $keySchedules => $rowSchedules){
                $listSchedules[$keySchedules]['customer_mobile'] = ModifyPhoneNumber::hidePhoneNumber($rowSchedules['customer_mobile'], $length);
            }
        }
        $this->map['schedules'] = $listSchedules;
        $this->map['status_id_list'] = [''=>'Trạng thái hẹn'] + CrmCustomerScheduleDB::$status;
        $this->map['schedule_type_list'] = [''=>'Loại hẹn'] + CrmCustomerScheduleDB::$schedule_type;
    }
}