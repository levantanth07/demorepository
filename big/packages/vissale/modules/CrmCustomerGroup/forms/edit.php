<?php
class EditCrmCustomerGroupForm extends Form
{
	function __construct()
	{
		Form::Form('EditCrmCustomerGroupForm');
		if(URL::get('do')=='edit')
		{
			$this->add('id',new IDType(true,'Không tồn tại','crm_customer_group'));
		}
		$this->add('name',new TextType(true,'Lỗi nhập tên phân loại khách hàng',0,255));
	}
	function on_submit()
	{
		if($this->check() and URL::get('confirm_edit'))
		{
			$array = array('description'=>'', 'name');
			if(URL::get('do')=='edit' and Url::iget('id') and $row = DB::fetch('select id,name,structure_id from crm_customer_group where id = '.Url::iget('id').' and group_id='.Session::get('group_id')))
			{
				$id = Url::iget('id');
				DB::update_id('crm_customer_group', $array,$id);
				if($row['structure_id']!=ID_ROOT){
					if (Url::check(array('parent_id')))
					{
						$parent = DB::select('crm_customer_group',Url::iget('parent_id'));
						if($parent['structure_id']==$row['structure_id'])
						{
							$this->error('id','invalid_parent');
						}
						else
						{
							require_once 'packages/core/includes/system/si_database.php';
							if(!si_move('crm_customer_group',$row['structure_id'],$parent['structure_id']))
							{
								$this->error('id','invalid_parent');
							}
						}
					}
				}
			}
			else
			{
				require_once 'packages/core/includes/system/si_database.php';
				if(isset($_REQUEST['parent_id']))
				{
					DB::insert('crm_customer_group',$array+array('group_id'=>Session::get('group_id'),'structure_id'=>si_child('crm_customer_group',structure_id('crm_customer_group',$_REQUEST['parent_id']),' and group_id='.Session::get('group_id').'')));
				}
				else
				{
					DB::insert('crm_customer_group',$array+array('structure_id'=>ID_ROOT,'group_id'=>Session::get('group_id')));
				}	
			}
			Url::js_redirect(true);
		}
	}	
	function draw()
	{	
		if(URL::get('do')=='edit' and $row=DB::select('crm_customer_group','id='.URL::iget('id').' and group_id = '.Session::get('group_id')))
		{
			foreach($row as $key=>$value)
			{
				if(!isset($_REQUEST[$key]))
				{
					$_REQUEST[$key] = $value;
				}
			}
			$edit_mode = true;
		}
		else
		{
			$edit_mode = false;
		}
		DB::query('
			select 
				id,
				structure_id
				,description ,name 
			from 
				`crm_customer_group`
			where
				crm_customer_group.group_id = '.Session::get('group_id').'
				or crm_customer_group.structure_id = '.ID_ROOT.'
			order by 
				structure_id
		');
		$parents = DB::fetch_all();
		require_once 'packages/core/includes/system/si_database.php';
		$this->parse_layout('edit',
			($edit_mode?$row:array())+
			array(
			'parent_id_list'=>MiString::get_list($parents),
				'parent_id'=>($edit_mode?si_parent_id('crm_customer_group',$row['structure_id']):1),
			)
		);
	}
}
?>