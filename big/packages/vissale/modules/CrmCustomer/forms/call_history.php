<?php
/**
 * Created by PhpStorm.
 * User: trinhdinh
 * Date: 2019-01-08
 * Time: 18:54
 */

require_once 'packages/core/includes/utils/paging.php';
class CallHistoryCrmCustomerForm extends Form
{
    protected $map;
    protected $data = [];
    public function __construct()
    {
        Form::Form('CallHistoryCrmCustomerForm');
    }

    public function on_submit()
    {
        $type = Url::sget('type');
        $this->map['title'] = $type;
        $this->data = CrmCustomerDB::get_crm_reports($type);
        $this->map['items'] = $this->data['items'];
        $this->map['count'] = $this->data['count'];
    }
    public function draw(){

        $types = [''=>'Chọn loại', 'callhistory'=>'Cuộc gọi', 'schedule'=>'Lịch hẹn'];
        $this->map['type_list'] = $types;
        //
        //get branches
        $this->map['branch_id_list'] = [''=>'Chọn chi nhánh'] + MiString::get_list( CrmCustomerDB::get_groups() );
        $total_record = $this->map['count'] ;
        $item_per_page = 100;
        $paging = paging($total_record, $item_per_page, 5, false, 'page_no',
            ['branch_id','do','from_date','to_date','type','account_name','form_block_id'] );
        $this->map['paging'] = $paging;
        //
        $this->parse_layout('call_history', $this->map);
    }
}