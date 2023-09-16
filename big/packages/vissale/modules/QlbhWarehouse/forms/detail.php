<?php
class QlbhWarehouseForm extends Form
{
	function __construct()
	{
		Form::Form("QlbhWarehouseForm");
		$this->add('id',new IDType(true,'object_not_exists','qlbh_warehouse'));
	}
	function on_submit()
	{
		if(Url::get('id') and $warehouse=DB::fetch('select id,structure_id from qlbh_warehouse where id='.intval(Url::get('id')).' and group_id='.Session::get('group_id').''))
		{
			$this->delete($this,$_REQUEST['id']);
			Url::js_redirect(true,'Xoá thành công!');
		}
		else
		{
			Url::redirect_current();
		}
	}
	function draw()
	{
		$this->load_data();
		$this->parse_layout('detail',$this->item_data);
	}
	function delete(&$form,$id)
	{
		$this->item_data = DB::select('qlbh_warehouse',$id);
		DB::delete_id('qlbh_warehouse', $id);
	}
	function load_data()
	{
		DB::query('
			select 
				*
			from 
			 	qlbh_warehouse
			where
				qlbh_warehouse.id = '.URL::iget('id').'');
		$this->item_data = DB::fetch();
	}
}
?>