<?php
/******************************
COPY RIGHT BY NYN PORTAL - TCV
WRITTEN BY VUONGIGALONG
******************************/
class PackageAdmin extends Module
{
	function PackageAdmin($row)
	{
		if(User::can_view())
		{
			if(
				(URL::check(array('cmd'=>'move_up'))
					or URL::check(array('cmd'=>'move_down')))
				and Url::check('id')
				and User::can_edit()
				and $category=DB::exists_id('package',$_REQUEST['id']))
			{
				if($category['structure_id']!=ID_ROOT)
				{
					IDStructure::move_position('package');
				}
				System::log('change position','Change position of #'.$_REQUEST['id'],'Change position of #'.$_REQUEST['id']);
				Url::redirect_current();
			}
			else
			if(URL::check(array('cmd'=>'make_library_cache'))and User::can_view())
			{
				require_once 'packages/core/includes/system/make_library.php';
				URL::redirect_current();
			}
			else
			if(URL::check(array('cmd'=>'delete_cache'))and User::can_view())
			{
				require_once 'packages/core/includes/utils/dir.php';
				empty_all_dir('cache/modules');
				empty_all_dir('cache/page_layouts');
				empty_all_dir('cache/tables');
				URL::redirect_current();
			}
			else
			if(URL::get('cmd')=='delete' and is_array(URL::get('selected_ids')) and sizeof(URL::get('selected_ids'))>0 and User::can_delete())
			{
				Module::Module($row);
		require_once 'db.php';
				if(sizeof(URL::get('selected_ids'))>1)
				{
					require_once 'forms/list.php';
					$this->add_form(new ListPackageAdminForm());
				}
				else
				{
					$ids = URL::get('selected_ids');
					$_REQUEST['id'] = $ids[0];
					require_once 'forms/detail.php';
					$this->add_form(new PackageAdminForm());
				}
			}
			else
			if(
				(((URL::check(array('cmd'=>'delete'))and User::can_delete())
					or (URL::check(array('cmd'=>'edit')) and User::can_edit())
					or (URL::check(array('cmd'=>'view')) and User::can_view_detail()))
					and Url::check('id') and DB::exists_id('package',$_REQUEST['id']))
				or
				(URL::check(array('cmd'=>'add')) and User::can_add())
				or URL::check(array('cmd'=>'export'))
				or !URL::check('cmd')
			)
			{
				Module::Module($row);
		require_once 'db.php';
				switch(URL::get('cmd'))
				{
				case 'delete':
					require_once 'forms/detail.php';
					$this->add_form(new PackageAdminForm());break;
				case 'edit':
				case 'add':
					require_once 'forms/edit.php';
					$this->add_form(new EditPackageAdminForm());break;
				case 'view':
					require_once 'forms/detail.php';
					$this->add_form(new PackageAdminForm());break;
				case 'export':
					require_once 'forms/export_package.php';
					$this->add_form(new ExportPackageForm());break;
				default:
					require_once 'forms/list.php';
					$this->add_form(new ListPackageAdminForm());
					break;
				}
			}
			else
			{
				Url::redirect_current();
			}
		}
		else
		{
			URL::access_denied();
		}
	}
}
?>