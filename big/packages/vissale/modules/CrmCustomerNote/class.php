<?php
class CrmCustomerNote extends Module
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
		$this->add_form(new ListCrmCustomerNoteForm());
	}

	function add_cmd()
	{
		if(User::is_login() && CrmCustomerNoteDB::can_add())
		{
			require_once 'forms/edit.php';
			$this->add_form(new EditCrmCustomerNoteForm());
		}
		else
		{
			Url::access_denied();
		}
	}
	function edit_cmd()
	{
        $nid = Url::get('nid');
        $sql = "SELECT id FROM crm_customer_notes WHERE MD5(CONCAT(id,'".CATBE."'))='{$nid}'";
        if(Url::check('cid') and $row = DB::fetch($sql) and CrmCustomerNoteDB::can_edit($row['id'])){
            require_once 'forms/edit.php';
            $this->add_form(new EditCrmCustomerNoteForm());
            return true;
	    }
        echo '<script>alert("Bạn không có quyền!");</script>';
//        Url::redirect_current();
	}
	public function generateCode($bill_number){
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

