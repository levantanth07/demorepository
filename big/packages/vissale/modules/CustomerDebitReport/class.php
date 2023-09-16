<?php
class CustomerDebitReport extends Module
{
	function __construct($row)
	{
		Module::Module($row);
		require_once 'db.php';
		if(check_user_privilege('ADMIN_KETOAN'))
		{
			switch(Url::get('cmd'))
			{
				case 'detail':
					$this->detail_cmd();
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
		$this->add_form(new ListCustomerDebitReportForm());
	}

	function detail_cmd()
	{
		if(Session::get('admin_group'))
		{
			require_once 'forms/detail.php';
			$this->add_form(new DetailCustomerDebitReportForm());
		}
		else
		{
			Url::access_denied();
		}
	}

	function add_cmd()
	{
		if(Session::get('admin_group'))
		{
			require_once 'forms/edit.php';
			$this->add_form(new EditCustomerDebitReportForm());
		}
		else
		{
			Url::access_denied();
		}
	}

	function edit_cmd()
	{
		if(Session::get('admin_group')){
			require_once 'forms/edit.php';
			$this->add_form(new EditCustomerDebitReportForm());
		}else{
			echo '<script>alert("Bạn không có quyền!");</script>';
			Url::redirect_current();
		}
	}
}

?>

