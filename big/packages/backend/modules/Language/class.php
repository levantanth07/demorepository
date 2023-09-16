<?php
class Language extends Module
{
	function __construct($row)
	{
		if(User::can_admin(MODULE_LANGUAGE,ANY_CATEGORY))
		{
			if(URL::get('cmd')=='delete' and is_array(URL::get('selected_ids')) and sizeof(URL::get('selected_ids'))>0 and User::can_delete(MODULE_LANGUAGE,ANY_CATEGORY))
			{
				Module::Module($row);
				require_once 'db.php';
				if(sizeof(URL::get('selected_ids'))>1)
				{
					require_once 'forms/list.php';
					$this->add_form(new ListLanguageForm());
				}
				else
				{
					$ids = URL::get('selected_ids');
					$_REQUEST['id'] = $ids[0];
					require_once 'forms/detail.php';
					$this->add_form(new LanguageForm());
				}
			}
			else
			if(
				(((URL::check(array('cmd'=>'delete'))and User::can_delete(MODULE_LANGUAGE,ANY_CATEGORY))
					or (URL::check(array('cmd'=>'edit')) and User::can_edit(MODULE_LANGUAGE,ANY_CATEGORY))
					or (URL::check(array('cmd'=>'view')) and User::can_view_detail(MODULE_LANGUAGE,ANY_CATEGORY)))
					and Url::check('id') and DB::exists_id('language',$_REQUEST['id']))
				or
				(URL::check(array('cmd'=>'add')) and User::can_add(MODULE_LANGUAGE,ANY_CATEGORY))
				or !URL::check('cmd')
			)
			{
				Module::Module($row);
		require_once 'db.php';
				switch(URL::get('cmd'))
				{
				case 'delete':
					require_once 'forms/detail.php';
					$this->add_form(new LanguageForm());break;
				case 'edit':
				case 'add':
					require_once 'forms/edit.php';
					$this->add_form(new EditLanguageForm());break;
				case 'view':
					require_once 'forms/detail.php';
					$this->add_form(new LanguageForm());break;
				default:
					require_once 'forms/list.php';
					$this->add_form(new ListLanguageForm());
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
	static function update_portal_language()
	{
		@unlink('cache/tables/language.cache.php');
		$query = mysqli_query('SHOW TABLES');
		while($row = mysqli_fetch_row($query))
		{
			Language::update_table_language($row[0]);
		}
		Portal::make_word_cache();
	}
	function update_table_language($table)
	{
		$query = mysqli_query('DESCRIBE `'.$table.'`');
		$fields = array();
		while($row = mysqli_fetch_row($query))
		{
			if(preg_match('/(\w+)\_(\d+)$/',$row[0], $patterns))
			{
				$fields[$patterns[1]][$patterns[2]] = $row[1];
			}
		}
		foreach($fields as $name=>$field)
		{
			Language::update_row_language($table,$name, $field);
		}
	}
	function update_row_language($table, $field, $language_ids)
	{
		$languages = DB::fetch_all('SELECT id FROM language');
		foreach($languages as $language)
		{
			if(!isset($language_ids[$language['id']]))
			{
				DB::query('ALTER TABLE `'.$table.'` ADD `'.$field.'_'.$language['id'].'` '.$language_ids[1].' NOT NULL AFTER `'.$field.'_1` ;');
			}
		}
		foreach($language_ids as $language_id=>$type)
		{
			if(!isset($languages[$language_id]))
			{
				DB::query('ALTER TABLE `'.$table.'` DROP `'.$field.'_'.$language_id.'`');
			}
		}
	}
}
?>