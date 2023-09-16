<?php
class ModuleAdmin extends Module
{
	function __construct($row)
	{
		Module::Module($row);
		require_once 'db.php';
		if(User::can_edit())
		{
			if(URL::check(array('cmd'=>'delete_block')))
			{
				DB::delete('block_setting','block_id="'.URL::get('block_id').'"');
				DB::delete('block','id="'.URL::get('block_id').'"');
				echo '<script>window.close();</script>';
				System::halt();
			}
			else
			if(URL::check(array('cmd'=>'update')))
			{
				require_once 'packages/core/includes/portal/make_configuration.php';
				make_module_cache();
				require_once 'packages/core/includes/portal/package.php';
				$modules = DB::fetch_all('select id, name, package_id from module');
				foreach($modules as $id=>$module)
				{
					DB::update_id('module',array('path'=>$packages[$module['package_id']]['path'].'modules/'.$module['name'].'/'),$id);
				}
				require_once 'packages/core/includes/utils/dir.php';
				empty_all_dir(ROOT_PATH.'cache/page_layouts');
				empty_all_dir(ROOT_PATH.'cache/modules');
				URL::redirect_current(array('package_id'));
			}
			else
			if(URL::check(array('cmd'=>'update_module_table')))
			{
				require_once 'packages/core/includes/portal/update_module.php';
				update_module_table();
				URL::redirect_current(array('package_id'));
			}
			else
			if(URL::check(array('cmd'=>'update_module_list','package_id')) and DB::exists_id('package',URL::get('package_id')) and User::can_edit())
			{
				require_once 'packages/core/includes/portal/update_module.php';
				update_module_list();
				URL::redirect_current(array('package_id'));
			}
			else
			if(URL::check(array('cmd'=>'delete_cache')) and User::can_edit())
			{
				require_once 'packages/core/includes/utils/dir.php';
				empty_all_dir('cache/modules');
				require_once 'packages/core/includes/portal/make_configuration.php';
				make_module_cache();
				URL::redirect_current(array('package_id'));
			}
			else
			if(URL::check(array('cmd'=>'generate_cache')) and User::can_edit())
			{
				require_once 'packages/core/includes/portal/update_module.php';
				generate_module_cache();
				URL::redirect_current(array('package_id'));
			}
			else
			if(URL::check(array('cmd'=>'delete_word','id')) and $word = DB::exists_id('module_word',URL::get('id')) and User::can_edit())
			{
				DB::delete_id('module_word',URL::get('id'));
				$module = DB::select('module',$word['module_id']);
				require_once 'packages/core/includes/utils/dir.php';
				empty_dir(ROOT_PATH.'cache/modules/'.$module['name']);
				URL::redirect_current(array('cmd'=>'edit_word','id'=>$word['module_id']));
			}
			else
			if(URL::get('cmd')=='delete' and is_array(URL::get('selected_ids')) and sizeof(URL::get('selected_ids'))>0 and User::can_delete())
			{
				if(sizeof(URL::get('selected_ids'))>1)
				{
					require_once 'forms/list.php';
					$this->add_form(new ListModuleAdminForm());
				}
				else
				{
					$ids = URL::get('selected_ids');
					$_REQUEST['id'] = $ids[0];
					require_once 'forms/detail.php';
					$this->add_form(new ModuleAdminForm());
				}
			}
			else
			if(
				(((URL::check(array('cmd'=>'delete'))and User::can_delete())
					or (URL::check(array('cmd'=>'edit')) and User::can_edit())
					or (URL::check(array('cmd'=>'edit_code')) and User::can_edit())
					or (URL::check(array('cmd'=>'edit_content')) and User::can_edit())
					or (URL::check(array('cmd'=>'edit_html')) and User::can_edit())
					or (URL::check(array('cmd'=>'view')) and User::can_view_detail()))
					and Url::check('id') and DB::exists_id('module',$_REQUEST['id']))
				or
				(URL::check(array('cmd'=>'add')) and User::can_add())
				or (URL::check(array('cmd'=>'export')) and User::can_admin())
				or (URL::check(array('cmd'=>'edit_word')) and User::can_special())
				or !URL::check('cmd')
			)
			{
				Module::Module($row);
		require_once 'db.php';
				switch(URL::get('cmd'))
				{
				case 'delete':
					require_once 'forms/detail.php';
					$this->add_form(new ModuleAdminForm());break;
				case 'edit':
				case 'add':
					require_once 'forms/edit.php';
					$this->add_form(new EditModuleAdminForm());break;
				case 'edit_code':
					require_once 'forms/edit_code.php';
					$this->add_form(new EditModuleCodeAdminForm());break;
				case 'edit_content':
					require_once 'forms/edit_content.php';
					$this->add_form(new EditModulecontentAdminForm());break;
				case 'edit_html':
					require_once 'forms/edit_html.php';
					$this->add_form(new EditModuleHTMLAdminForm());break;
				case 'view':
					require_once 'forms/detail.php';
					$this->add_form(new ModuleAdminForm());break;
				case 'export':
					require_once 'forms/export.php';
					$this->add_form(new ExportModuleForm());break;
				case 'edit_word':
					require_once 'forms/list_word.php';
					$this->add_form(new ListModuleWordForm());break;
				default:
					require_once 'forms/list.php';
					$this->add_form(new ListModuleAdminForm());
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