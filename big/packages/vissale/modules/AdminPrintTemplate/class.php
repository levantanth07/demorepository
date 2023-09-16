<?php
class AdminPrintTemplate extends Module
{
	function __construct($row)
	{
		Module::Module($row);
		require_once 'db.php';
		if(Session::get('admin_group'))
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
					$this->add_form(new DeleteAdminPrintTemplateForm());
					break;	
				default:
					$this->list_cmd();
					break;
			}
		}
		else
		{
			Url::js_redirect('admin_orders','Bạn không có quyền truy cập.');
		}

	}
	function delete_file()
	{
		if(Url::get('link') and file_exists(Url::get('link')))
		{
			@unlink(Url::get('link'));
		}

		echo '<script>window.close();</script>';
	}
	function list_cmd()
	{
		require_once 'forms/list.php';
		$this->add_form(new ListAdminPrintTemplateForm());
	}
	function add_cmd()
	{
		if(Session::get('admin_group'))
		{
			require_once 'forms/edit.php';
			$this->add_form(new EditAdminPrintTemplateForm());
		}
		else
		{
            Url::js_redirect(true,'Bạn không có quyền truy cập.');
		}
	}
	function edit_cmd(){
		if(Session::get('admin_group')){
			require_once 'forms/edit.php';
			$this->add_form(new EditAdminPrintTemplateForm());
		}else{
			echo '<script>alert("Bạn không có quyền!");</script>';
			Url::redirect_current();
		}
	}
}

?>

