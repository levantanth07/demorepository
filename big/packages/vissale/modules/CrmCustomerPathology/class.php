<?php
class CrmCustomerPathology extends Module
{
	function __construct($row)
	{
		Module::Module($row);
		require_once 'db.php';
        if(User::is_login() )
		{
			switch(Url::get('cmd'))
			{
				case 'add':
					$this->add_cmd();
					break;
				case 'edit':
					$this->edit_cmd();
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
		$this->add_form(new ListCrmCustomerPathologyForm());
	}

	function add_cmd()
	{
		if(User::is_login())
		{
			require_once 'forms/edit.php';
			$this->add_form(new EditCrmCustomerPathologyForm());
		}
		else
		{
			Url::access_denied();
		}
	}
	function edit_cmd()
	{
        $nid = Url::get('nid');
        $sql = "SELECT id FROM crm_customer_pathology WHERE MD5(CONCAT(id,'".CATBE."'))='{$nid}'";
        if(Url::check('cid') and $row = DB::fetch($sql) and CrmCustomerPathologyDB::can_edit($row['id'])){
			require_once 'forms/edit.php';
			$this->add_form(new EditCrmCustomerPathologyForm());
		}else{
			echo '<script>alert("Bạn không có quyền!");</script>';
			Url::redirect_current();
		}
	}
	static public function generateCode($bill_number){
        return strlen($bill_number)< 6 ? str_pad(($bill_number),6,"0",STR_PAD_LEFT): ($bill_number);
	}

    static public function generatePrefixType($bill_type)
    {
        if ($bill_type == 'receive') {
            return 'PT';
        }

        return 'PC';
    }

}

