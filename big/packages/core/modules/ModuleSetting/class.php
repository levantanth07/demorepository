<?php
/******************************
COPY RIGHT BY NYN PORTAL - TCV
WRITTEN BY VUONGIGALONG
******************************/
class MODULESETTING extends Module
{
	function MODULESETTING($row)
	{
		Module::Module($row);
		require_once 'db.php';
		if(User::can_view())
		{
			switch(URL::get('cmd'))
			{
			case 'delete':
				if(is_array(URL::get('selected_ids')) and sizeof(URL::get('selected_ids'))>0 and User::can_delete())
				{
					if(sizeof(URL::get('selected_ids'))>1)
					{
						require_once 'forms/list.php';
						$this->add_form(new ListModuleSettingForm());
					}
					else
					{
						$ids = URL::get('selected_ids');
						$_REQUEST['id'] = $ids[0];
						require_once 'forms/detail.php';
						$this->add_form(new ModuleSettingForm());
					}
				}
				else
				if(User::can_delete()and Url::check('id') and DB::exists_id('module_setting',$_REQUEST['id']))
				{
					require_once 'forms/detail.php';
					$this->add_form(new ModuleSettingForm());
				}
				else
				{
					Url::redirect_current();
				}
				break;
			case 'edit':
				if(User::can_edit() and Url::check('id'))
				{
					require_once 'forms/edit.php';
					$this->add_form(new EditModuleSettingForm());
				}
				else
				{
					Url::redirect_current();
				}
				break;
			case 'add':
				if(User::can_add())
				{
					require_once 'forms/edit.php';
					$this->add_form(new EditModuleSettingForm());
				}
				else
				{
					Url::redirect_current();
				}
				break;
			case 'view':
				if(User::can_view_detail() and Url::check('id') and DB::exists_id('module_setting',$_REQUEST['id']))
				{
					require_once 'forms/detail.php';
					$this->add_form(new ModuleSettingForm());
				}
				else
				{
					Url::redirect_current();
				}
				break;
			default:
				require_once 'forms/list.php';
				$this->add_form(new ListModuleSettingForm());
				break;
			}
		}
		else
		{
			URL::access_denied();
		}
	}
}
?>