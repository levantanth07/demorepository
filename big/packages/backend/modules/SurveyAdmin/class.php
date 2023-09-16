<?php
/******************************
COPY RIGHT BY NYN PORTAL - TCV
WRITTEN BY thedeath
******************************/
class SurveyAdmin extends Module
{
	function SurveyAdmin($row)
	{
		Module::Module($row);
		if(User::can_admin(false,ANY_CATEGORY))
		{
			if(
				Url::check('survey_id') and $survey=DB::exists_id('survey',$_REQUEST['survey_id']) and Url::check('block_id') and $block=DB::exists_id('block',$_REQUEST['block_id'])
			)
			{
				DB::query('replace block_setting(block_id, setting_id, value) values('.$_REQUEST['block_id'].',"survey_id","'.DB::escape($_REQUEST['survey_id']).'")');
				require_once 'packages/core/includes/portal/update_page.php';
				update_page($block['page_id']);
				if(URL::check('href'))
				{
					Url::redirect_url($_REQUEST['href']);
				}
				else
				{
					URL::redirect_current();
				}
			}
			else
			if(URL::get('cmd')=='delete' and is_array(URL::get('selected_ids')) and sizeof(URL::get('selected_ids'))>0 and User::can_delete(false,ANY_CATEGORY))
			{
				Module::Module($row);
				require_once 'db.php';
				if(sizeof(URL::get('selected_ids'))>0)
				{
					foreach(URL::get('selected_ids') as $key)
					{
						$this->delete($this,$key);
					}
					Url::redirect_current(array());
				}
			}
			else
			if(
				(((URL::check(array('cmd'=>'delete'))and User::can_delete(false,ANY_CATEGORY))
					or (URL::check(array('cmd'=>'edit')) and User::can_edit(false,ANY_CATEGORY)))
					and Url::check('id') and $record = DB::exists_id('survey',$_REQUEST['id'])and User::can_edit(false,ANY_CATEGORY))
				or
				(URL::check(array('cmd'=>'add')) and User::can_edit(false,ANY_CATEGORY))
				or !URL::check('cmd')
			)
			{
				require_once 'db.php';
				switch(URL::get('cmd'))
				{
				case 'edit':
				case 'add':
					require_once 'forms/edit.php';
					$this->add_form(new EditSurveyAdminForm());break;
				default:
					require_once 'forms/list.php';
					$this->add_form(new ListSurveyAdminForm());
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
	function delete(&$form,$id)
	{
		$row = DB::select('survey',$id);
		if(!User::can_admin())
		{
			URL::redirect_current();
		}
		$item_row = DB::select_all('survey_options','survey_id='.$id);
		DB::delete('survey_options', 'survey_id='.$id);
		DB::delete_id('survey', $id);
	}
}
?>
