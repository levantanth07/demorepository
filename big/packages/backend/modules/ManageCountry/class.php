<?php 
/******************************
COPY RIGHT BY Catbeloved - Framework
******************************/
class ManageCountry extends Module
{
	function ManageCountry($row)
	{
		Module::Module($row);
		require_once 'db.php';
		if(User::can_view(false,ANY_CATEGORY))
		{
			switch(Url::get('cmd'))
			{
				case 'delete':
					$this->delete();
					break;
				case 'add':
					$this->add();	
					break;	
				case 'edit':
					$this->edit();	
					break;
				case 'cache':
					$this->cache();	
					break;
				case 'update':	
					$this->update();
					break;
				default:	
					require_once 'forms/list.php';
					$this->add_form(new ManageCountryForm());
					break;
			}	
		}
		else
		{
			Url::access_denied();
		}
	}
	function cache()
	{
		if(User::can_admin(false,ANY_CATEGORY))
		{
			$items = ManageCountryDB::get_items();
			require_once 'packages/core/includes/utils/upload_file.php';
			$file = 'cache/tables/countries.cache.php';
			write_file($file,'<?php $countries = '.var_export($items,true).';?>');
		}
		Url::redirect_current();
	}
	function delete()
	{
		if(User::can_delete(false,ANY_CATEGORY) and Url::get('id') and $item = DB::exists_id('country',Url::sget('id')))
		{
			save_recycle_bin('country',$item);
			DB::delete_id('country',Url::sget('id'));
			save_log(Url::get('id'));
		}
		Url::redirect_current();
	}
	function add()
	{
		if(User::can_add(false,ANY_CATEGORY))
		{
			require_once 'forms/edit.php';
			$this->add_form(new countryEditForm());
		}
		else
		{
			Url::access_denied();
		}
	}
	function edit()
	{
		if(User::can_edit(false,ANY_CATEGORY))
		{
			require_once 'forms/edit.php';
			$this->add_form(new countryEditForm());
		}
		else
		{
			Url::access_denied();
		}
	}
}
?>