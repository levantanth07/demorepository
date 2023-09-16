<?php
/******************************
COPY RIGHT BY NYN PORTAL - TCV
WRITTEN BY thedeath
******************************/
class CurrencyAdmin extends Module
{
	function CurrencyAdmin($row)
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
					$this->add_form(new CurrencyAdminForm());
					break;
			}
		}
		else
		{
			Url::access_denied();
		}
	}
	function update()
	{
		if(User::can_admin(false,ANY_CATEGORY))
		{
			$source = 'http://vietcombank.com.vn/ExchangeRates/ExrateXML.aspx';
			$content = file_get_contents($source);
			if(preg_match_all('/Exrate CurrencyCode="(.*)" CurrencyName="(.*)" Buy="(.*)" Transfer="(.*)" Sell="(.*)"/',$content,$matches) and count($matches)>0)
			{
				foreach($matches[1] as $key=>$value)
				{
					$row = array(
						'id'=>$value
						,'name'=>$matches[2][$key]
						,'exchange'=>$matches[5][$key]
						,'position'=>$key
					);
					if($item = DB::exists_id('currency',$value))
					{
						DB::update_id('currency',$row,$item['id']);
					}
					else
					{
						DB::insert('currency',$row);
					}
				}
			}
		}
		Url::redirect_current();
	}
	function cache()
	{
		if(User::can_admin(false,ANY_CATEGORY))
		{
			$items = CurrencyAdminDB::get_items();
			require_once 'packages/core/includes/utils/upload_file.php';
			$file = 'cache/tables/currency.cache.php';
			write_file($file,'<?php $currency = '.var_export($items,true).';?>');
		}
		Url::redirect_current();
	}
	function delete()
	{
		if(User::can_delete(false,ANY_CATEGORY) and Url::get('id') and $item = DB::exists_id('currency',Url::sget('id')))
		{
			save_recycle_bin('currency',$item);
			DB::delete_id('currency',Url::sget('id'));
			save_log(Url::get('id'));
		}
		Url::redirect_current();
	}
	function add()
	{
		if(User::can_add(false,ANY_CATEGORY))
		{
			require_once 'forms/edit.php';
			$this->add_form(new CurrencyEditForm());
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
			$this->add_form(new CurrencyEditForm());
		}
		else
		{
			Url::access_denied();
		}
	}
}
?>