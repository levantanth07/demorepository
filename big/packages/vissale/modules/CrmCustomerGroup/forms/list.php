<?php
class ListCrmCustomerGroupForm extends Form
{
	function __construct()
	{
		Form::Form('ListCrmCustomerGroupForm');
	}
	function on_submit()
	{
		if(URL::get('confirm'))
		{
			foreach(URL::get('selected_ids') as $id)
			{
			}
			require_once 'detail.php';
			foreach(URL::get('selected_ids') as $id)
			{
				CrmCustomerGroupForm::delete($this,$id);
				if($this->is_error())
				{
					return;
				}
			}
			Url::redirect_current(array('name'=>isset($_GET['name'])?$_GET['name']:''));
		}
	}
	function draw()
	{
		$cond = ' (crm_customer_group.group_id='.Session::get('group_id').' or crm_customer_group.group_id=1)'
			.(URL::get('name')?' and `crm_customer_group`.`name` LIKE "%'.URL::get('name').'%"':'') 
			.((URL::get('do')=='delete' and is_array(URL::get('selected_ids')))?' and `crm_customer_group`.id in ("'.join(URL::get('selected_ids'),'","').'")':'')
		;
		$sql = '
			select 
				`crm_customer_group`.id
				,`crm_customer_group`.structure_id
				,`crm_customer_group`.`description` 
				,`crm_customer_group`.`name`
				,`crm_customer_group`.`group_id` 
			from 
			 	`crm_customer_group`
			where '.$cond.'
			order by `crm_customer_group`.structure_id
		';
		$items = DB::fetch_all($sql);
		require_once 'packages/core/includes/utils/category.php';
		category_indent($items);
		$i=1;
		foreach ($items as $key=>$value)
		{
			$items[$key]['i']=$i++;
		}
		$just_edited_id['just_edited_ids'] = array();
		if (UrL::get('selected_ids'))
		{
			if(is_string(UrL::get('selected_ids')))
			{
				if (strstr(UrL::get('selected_ids'),','))
				{
					$just_edited_id['just_edited_ids']=explode(',',UrL::get('selected_ids'));
				}
				else
				{
					$just_edited_id['just_edited_ids']=array('0'=>UrL::get('selected_ids'));
				}
			}
		}
		$this->parse_layout('list',$just_edited_id+
			array(
				'items'=>$items,
			)
		);
	}
}
?>