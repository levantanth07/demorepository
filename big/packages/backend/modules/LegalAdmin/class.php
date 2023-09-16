<?php
/******************************
COPY RIGHT BY NYN PORTAL - TCV
WRITTEN BY thedeath
******************************/
class LegalAdmin extends Module
{
	function LegalAdmin($row)
	{
		Module::Module($row);
		require_once 'db.php';
		if(User::can_view(false,ANY_CATEGORY))
		{
			switch(Url::get('cmd'))
			{
				case 'add':
					$this->add_cmd();
					break;
				case 'edit':
					$this->edit_cmd();
					break;
				case 'delete_id':
					require_once 'forms/delete.php';
					$this->add_form(new DeleteNewsAdminForm());
					break;
				case 'unlink':
					$this->delete_file();
					break;
				case 'front_page':
					$this->front_page();
					break;
				case 'copy':
					$this->copy_items();
					break;
				case 'move':
					$this->copy_items();
					break;
				default:
					$this->list_cmd();
					break;
			}
		}
		else
		{
			Url::access_denied();
		}
	}
	function copy_items()
	{
		if(User::can_edit(false,ANY_CATEGORY) and isset($_REQUEST['selected_ids']) and count($_REQUEST['selected_ids'])>0)
		{
			require_once 'forms/copy.php';
			$this->add_form(new CopyNewsAdminForm());
		}
		else
		{
			Url::redirect_current(array('cmd'=>'list'));
		}
	}
	function delete_file()
	{
		if(Url::get('link') and file_exists(Url::get('link')) and User::can_delete(false,ANY_CATEGORY))
		{
			@unlink(Url::get('link'));
		}
		echo '<script>window.close();</script>';
	}
	function front_page()
	{
		if(Url::get('id') and $news = DB::exists_id('news',intval(Url::get('id'))) and User::can_edit(false,ANY_CATEGORY))
		{
			DB::update_id('news',array('front_page'=>$news['front_page']==1?'0':'1'),intval(Url::get('id')));
		}
		echo '<script>location="'.Url::redirect_current(array('cmd'=>'list','just_edited_id'=>Url::get('id',1))).'";</script>';
	}
	function list_cmd()
	{
		require_once 'forms/list.php';
		$this->add_form(new ListNewsAdminForm());
	}
	function add_cmd()
	{
		if(User::can_add(false,ANY_CATEGORY))
		{
			require_once 'forms/edit.php';
			$this->add_form(new EditNewsAdminForm());
		}
		else
		{
			Url::access_denied();
		}
	}
	function edit_cmd()
	{
		if(User::can_edit(false,ANY_CATEGORY))
		{
			require_once 'forms/edit.php';
			$this->add_form(new EditNewsAdminForm());
		}
		else
		{
			Url::access_denied();
		}
	}
}
?>
