<?php
class LogStatusForm extends Form
{
	function __construct()
	{
		Form::Form('LogStatusForm');
		//$this->link_css('assets/default/css/cms.css');
	}
	function on_submit()
	{
		/*if(Url::get('cmd') == 'delete' and Url::get('selected_ids') and count(Url::get('selected_ids'))>0)
		{
			foreach(Url::get('selected_ids') as $key=>$value)
			{
				DB::delete_id('log',$value);
			}
		}*/
		Url::redirect_current(['do','keyword','from_date','to_date','before_order_status_id','order_status_id']);
	}
	function draw()
	{
        $group_id = Url::get('group_id')?Url::get('group_id'):Session::get('group_id');
        $from_date = Url::get('from_date')?Url::get('from_date'):date('Y-m-d 00:00:00');
        $to_date = Url::get('to_date')?Url::get('to_date'):date('Y-m-d 23:59:59');;
        $item_per_page =  20;
        if (Session::get('account_type') == TONG_CONG_TY) {
            $cond = '(orders.group_id=' . Session::get('group_id') . ' or orders.master_group_id=' . Session::get('group_id') . ')';
        } else {
            $cond = '(orders.group_id=' . Session::get('group_id') . '' . ((Session::get('master_group_id')) ? ' OR orders.master_group_id=' . Session::get('master_group_id') . '' : '') . ')';
        }
        $cond .= '
        AND IFNULL(before_order_status_id,0) > 0
        '.($from_date?'and order_revisions.created >= "' . $from_date . ' 00:00:00"':'').'
	    '.($from_date?'and order_revisions.created <= "' . $to_date . ' 23:59:59"':'').'
	    '.(Url::iget('before_order_status_id')?'and order_revisions.before_order_status_id = ' . Url::iget('before_order_status_id') . '':'').'
	    '.(Url::iget('order_status_id')?'and order_revisions.order_status_id = ' . Url::iget('order_status_id') . '':'').'
        ';
        if($keyword =DB::escape(Url::get('keyword'))){
            $cond .= ' AND (order_revisions.id = "'.$keyword.'")';
        }

		$total = LogDB::get_total_revision($cond);
		require_once 'packages/core/includes/utils/paging.php';
		$item_per_page = 50;
		$paging = paging($total,$item_per_page,10,false,'page_no',['keyword','group_name','group_id']);
		$items = LogDB::get_revisions($cond,$item_per_page);
		$statuses = LogDB::get_status();
		$this->parse_layout('log_status',array(
			'paging'=>$paging
			,'items'=>$items
			,'total'=>$total
            ,'before_order_status_id_list'=>[''=>'Chọn'] + MiString::get_list($statuses)
            ,'order_status_id_list'=>[''=>'Chọn'] + MiString::get_list($statuses)
		));
	}
}
?>