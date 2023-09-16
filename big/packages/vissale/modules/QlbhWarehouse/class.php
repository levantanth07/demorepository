<?php 
class QlbhWarehouse extends Module
{
	function __construct($row)
	{
		Module::Module($row);
		require_once 'db.php';
		if(check_user_privilege('ADMIN_KHO'))
		{
			switch(URL::get('cmd'))
			{			
			case 'export_cache':				
				$this->export_cache();
				break;
			case 'delete':				
				$this->delete_cmd();
				break;
			case 'edit':				
				$this->edit_cmd();
				break;
			case 'add':				
				$this->add_cmd();
				break;
			case 'view':
				$this->view_cmd();
				break;
			case 'move_up':
			case 'move_down':
				$this->move_cmd();
				break;
			default: 
				$this->list_cmd();
				break;
			}
		}
		else
		{
			URL::access_denied();
		}
	}	
	function add_cmd()
	{
		if(Session::get('admin_group'))
		{
			require_once 'forms/edit.php';
			$this->add_form(new EditQlbhWarehouseForm());
		}
		else
		{
			Url::redirect_current();
		}
	}
	function delete_cmd()
	{
		if(is_array(URL::get('selected_ids')) and sizeof(URL::get('selected_ids'))>0 and Session::get('admin_group'))
		{
			if(sizeof(URL::get('selected_ids'))>1)
			{
				require_once 'forms/list.php';
				$this->add_form(new ListQlbhWarehouseForm());
			}
			else
			{
				$ids = URL::get('selected_ids');
				$_REQUEST['id'] = $ids[0];
				require_once 'forms/detail.php';
				$this->add_form(new QlbhWarehouseForm());
			}
		}
		else
		if(Session::get('admin_group') and Url::iget('id') and DB::exists_id('qlbh_warehouse',Url::iget('id')))
		{
			require_once 'forms/detail.php';
			$this->add_form(new QlbhWarehouseForm());
		}
		else
		{
			Url::redirect_current();
		}
	}
	function edit_cmd()
	{
	    // if(Url::iget('id') == 1 and !User::is_admin()){
     //        die('Đây là kho mặc định của hệ thống. Bạn vui lòng không sửa chữa.');
     //    }
		if((Session::get('admin_group') and Url::iget('id') and $warehouse=DB::fetch('select id,structure_id from qlbh_warehouse where group_id = '.Session::get('group_id').' and  id='.intval(Url::get('id')))) || Url::iget('id') == 1)
		{
			require_once 'forms/edit.php';
			$this->add_form(new EditQlbhWarehouseForm());
		}
		else
		{
			Url::redirect_current();
		}
	}
	function list_cmd()
	{
		if(Session::get('group_id'))
		{
			require_once 'forms/list.php';
			$this->add_form(new ListQlbhWarehouseForm());
		}	
		else
		{
			Url::access_denied();
		}
	}
	function view_cmd()
	{
		if(Session::get('group_id') and Url::iget('id') and DB::exists_id('qlbh_warehouse',Url::iget('id')))
		{
			require_once 'forms/detail.php';
			$this->add_form(new QlbhWarehouseForm());
		}
		else
		{
			Url::redirect_current();
		}
	}
	function move_cmd()
	{
		if(Session::get('admin_group') and Url::iget('id')and $warehouse=DB::exists_id('qlbh_warehouse',Url::iget('id')))
		{
			if($warehouse['structure_id']!=ID_ROOT)
			{
				require_once 'packages/core/includes/system/si_database.php';
				si_move_position('qlbh_warehouse');
			}
			Url::redirect_current();
		}
		else
		{
			Url::redirect_current();
		}
	}
}
?>