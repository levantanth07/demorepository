<?php
class MODERATOR extends Module
{
	function __construct($row)
	{
		Module::Module($row);
		require_once 'db.php';
		if(User::can_admin(false,ANY_CATEGORY))
		{
			switch(Url::get('cmd'))
			{
				case 'update':
					$this->update();
					break;
				case 'grant':
					require_once 'forms/select_portal.php';
					$this->add_form(new GrantModeratorForm());
					break;
				default:
					require_once 'forms/list.php';
					$this->add_form(new ListModeratorForm());
					break;												}
		}
		else
		{
			URL::access_denied();
		}
	}
	function update()
	{
		$valueGrant=Url::get('valueGrant');
		if($valueGrant=='1')
		{
			$valueGrant=0;
		}
		elseif($valueGrant=='0')
		{
			$valueGrant=1;
		}
		$typeGrant=Url::get('typeGrant')?Url::get('typeGrant'):'edit';
		$id=Url::get('idGrant');
		if(isset($valueGrant) and isset($typeGrant))
		{
				DB::update_id('account_privilege',array($typeGrant=>$valueGrant),$id);
		}
	}
}
?>