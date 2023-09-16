<?php
class PartnerAdmin extends Module
{
	function __construct($row)
	{
		Module::Module($row);

		require_once 'db.php';
		if(User::can_view(false,ANY_CATEGORY))
		{
			switch(Url::get('cmd'))
			{
                case 'unlink':
                    if($link = Url::get('link')){
                        @unlink($link);
                        echo '<script>window.close();</script>';
                    }
                    break;
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
	function list_cmd()
	{
		require_once 'forms/list.php';
		$this->add_form(new ListPartnerAdminForm());
	}
	function add_cmd()
	{
		if(User::can_add(false,ANY_CATEGORY))
		{
			require_once 'forms/edit.php';
			$this->add_form(new EditPartnerAdminForm());
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
			$this->add_form(new EditPartnerAdminForm());
		}
		else
		{
			Url::access_denied();
		}
	}
}
?>
