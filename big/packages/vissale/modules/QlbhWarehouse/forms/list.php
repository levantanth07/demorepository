<?php
class ListQlbhWarehouseForm extends Form
{
	function __construct()
	{
		Form::Form('ListQlbhWarehouseForm');
	}
	function on_submit()
	{
		if(URL::get('confirm'))
		{
			$this->deleted_selected_ids();
		}
	}
	function draw()
	{
		$this->get_just_edited_id();
		$this->get_items();
		$items=QlbhWarehouseDB::check_categories($this->items);
		$email = '';
		$keyUnset = '';
		foreach ($items as $key => $value) {
			if ($value['kho_tong_shop'] && $value['kho_tong_shop'] == 1) {
				$email = $value['email'];
				$keyUnset = $key;
			}
		}
		$items[1]['email'] = $email;
		if ($keyUnset) {
			unset($items[$keyUnset]);
		}
		$this->parse_layout('list',$this->just_edited_id+
			array(
				'items'=>$items,
			)
		);
	}	
	function get_just_edited_id()
	{
		$this->just_edited_id['just_edited_ids'] = array();
		if (UrL::get('selected_ids'))
		{
			if(is_string(UrL::get('selected_ids')))
			{
				if (strstr(UrL::get('selected_ids'),','))
				{
					$this->just_edited_id['just_edited_ids']=explode(',',UrL::get('selected_ids'));
				}
				else
				{
					$this->just_edited_id['just_edited_ids']=array('0'=>UrL::get('selected_ids'));
				}
			}
		}
	}
	function deleted_selected_ids()
	{

		foreach(URL::get('selected_ids') as $id)
		{
			if($id and $warehouse=DB::fetch('select id,structure_id from qlbh_warehouse where id='.intval($id).' and group_id='.Session::get('group_id').''))
			{
				DB::delete_id('qlbh_warehouse',$id);
			}	
			if($this->is_error())
			{
				return;
			}
		}
		Url::js_redirect(true,'Bạn xoá thành công');
	}
	function get_items()
	{
		$this->get_select_condition();
		$this->items = DB::fetch_all('
			select 
				qlbh_warehouse.*
			from 
			 	qlbh_warehouse
			where
				 '.$this->cond.'			
			order by 
				qlbh_warehouse.structure_id
		',false);
		require_once 'packages/core/includes/utils/category.php';
		category_indent($this->items);
	}
	function get_select_condition()
	{
		$this->cond = '(qlbh_warehouse.group_id='.Session::get('group_id').' OR structure_id='.ID_ROOT.') ';
		if( URL::get('cmd')=='delete' and 
		is_array(URL::get('selected_ids')) 
		){
			$ids = URL::get('selected_ids');
			$arr = array();
			foreach($ids as $id){
				if(is_numeric($id) && intval($id) > 0){}
				$arr[] = $id ;
			}
			$arr = array_unique($arr);
			if (count($arr)){
				$this->cond .=	' and qlbh_warehouse.id in ('.implode(',',$arr).') ';
			}
		}
		
	}
}
?>