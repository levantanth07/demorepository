<?php 
/******************************
COPY RIGHT BY Catbeloved - Framework

******************************/
class QlbhProductCategory extends Module
{
	function __construct($row)
	{
		Module::Module($row);
		require_once 'db.php';
		$arr = array('product_category'=>'PRODUCT');
		if(isset($arr[Url::sget('page')]))
		{
			$_REQUEST['type'] = $arr[Url::sget('page')];
		}
		$this->redirect_parameters = array('type');
		if(User::can_view(false,ANY_CATEGORY))
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
			case 'unlink':
				$this->delete_file();		
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
	function export_cache()
	{
	
	}
	function delete_file()
	{
		if(Url::get('link') and file_exists(Url::get('link')) and User::can_delete(false,ANY_CATEGORY))
		{
			@unlink(Url::get('link'));
		}
		echo '<script>window.close();</script>';
	}
	function add_cmd()
	{
		if(User::can_add(false,ANY_CATEGORY))
		{
			require_once 'forms/edit.php';
			$this->add_form(new EditQlbhProductCategoryForm());
		}
		else
		{
			Url::redirect_current();
		}
	}
	function delete_cmd()
	{
		if(is_array(URL::get('selected_ids')) and sizeof(URL::get('selected_ids'))>0 and User::can_delete(false,ANY_CATEGORY))
		{
			if(sizeof(URL::get('selected_ids'))>1)
			{
				require_once 'forms/list.php';
				$this->add_form(new ListQlbhProductCategoryForm());
			}
			else
			{
				$ids = URL::get('selected_ids');
				$_REQUEST['id'] = $ids[0];
				require_once 'forms/detail.php';
				$this->add_form(new QlbhProductCategoryForm());
			}
		}
		else
		if(User::can_delete(false,ANY_CATEGORY) and Url::check('id') and DB::exists_id('qlbh_product_category',$_REQUEST['id']))
		{
			require_once 'forms/detail.php';
			$this->add_form(new QlbhProductCategoryForm());
		}
		else
		{
			Url::redirect_current();
		}
	}
	function edit_cmd()
	{
		if(Url::get('id') and $category=DB::fetch('select id,structure_id from qlbh_product_category where id='.intval(Url::get('id'))) and User::can_edit(false,$category['structure_id']))
		{
			require_once 'forms/edit.php';
			$this->add_form(new EditQlbhProductCategoryForm());
		}
		else
		{
			Url::redirect_current();
		}
	}
	function list_cmd()
	{
		require_once 'forms/list.php';
		$this->add_form(new ListQlbhProductCategoryForm());
	}
	function view_cmd()
	{
		if(User::can_view_detail(false,ANY_CATEGORY) and Url::check('id') and DB::exists_id('qlbh_product_category',$_REQUEST['id']))
		{
			require_once 'forms/detail.php';
			$this->add_form(new QlbhProductCategoryForm());
		}
		else
		{
			Url::redirect_current();
		}
	}
	function move_cmd()
	{
		if(User::can_edit(false,ANY_CATEGORY)and Url::check('id')and $category=DB::exists_id('qlbh_product_category',$_REQUEST['id']))
		{
			if($category['structure_id']!=ID_ROOT)
			{
				require_once 'packages/core/includes/system/si_database.php';
				si_move_position('qlbh_product_category','');
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