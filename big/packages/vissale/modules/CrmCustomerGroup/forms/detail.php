<?php
class CrmCustomerGroupForm extends Form
{
	function __construct()
	{
		Form::Form("CrmCustomerGroupForm");
		$this->add('id',new IDType(true,'object_not_exists','crm_customer_group'));
	}
	function on_submit()
	{
		if($this->check() and URL::get('confirm'))
		{
			$this->delete($this,$_REQUEST['id']);
			Url::redirect_current(array(
	 'name'=>isset($_GET['name'])?$_GET['name']:'', 
	));
		}
	}
	function draw()
	{
		DB::query('
			select 
				`crm_customer_group`.id
				,`crm_customer_group`.structure_id
				,`crm_customer_group`.`description` ,`crm_customer_group`.`name` 
			from 
			 	`crm_customer_group`
			where
				`crm_customer_group`.id = "'.DB::escape(URL::sget('id')).'"');
		if($row = DB::fetch())
		{
		}
		$this->parse_layout('detail',$row);
	}
	static function delete(&$form,$id)
	{
		$row = DB::select('crm_customer_group',$id);
		DB::delete_id('crm_customer_group', $id);
	}
}
?>