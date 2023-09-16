<?php

class ListAdminPrintTemplateForm extends Form
{
	function __construct()
	{
		Form::Form('ListAdminPrintTemplateForm');
	}
	function delete()
	{
		if(isset($_REQUEST['selected_ids']) and count($_REQUEST['selected_ids'])>0)
		{
			foreach($_REQUEST['selected_ids'] as $key)
			{
				$key = DB::escape($key);
				if($item = DB::exists_id('order_print_template',$key))
				{
					save_recycle_bin('order_print_template',$item);
					DB::delete_id('order_print_template',intval($key));
					save_log($key);
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
	function draw()
	{
		$cond = 'group_id = '.Session::get('group_id');
		require_once 'packages/core/includes/utils/paging.php';
		
		$item_per_page = 20;
		if(!Url::get('item_per_page')){
			$_REQUEST['item_per_page'] = $item_per_page;
		}else{
			$item_per_page = Url::get('item_per_page');
		}
		$total = AdminPrintTemplateDB::get_total_item($cond);
		$items = AdminPrintTemplateDB::get_items($cond,'id',$item_per_page);
		$paging = paging($total,$item_per_page,10,false,'page_no',array('cmd','item_per_page'));
		$this->parse_layout('list',array(
			'items'=>$items,
			'paging'=>$paging,
			'total'=>$total,
		));
	}
}

?>

