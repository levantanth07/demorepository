<?php 
class CrmCustomerGroup extends Module
{
	function __construct($row)
	{
		Module::Module($row);
		if(User::is_login() and check_user_privilege('CUSTOMER'))
		{
			switch(URL::get('do'))
			{
			case 'delete':
				if(is_array(URL::get('selected_ids')) and sizeof(URL::get('selected_ids'))>0)
				{
					if(sizeof(URL::get('selected_ids'))>=1)
					{
						require_once 'forms/list.php';
						$this->add_form(new ListCrmCustomerGroupForm());
					}
					else
					{
						$ids = URL::get('selected_ids');
						$_REQUEST['id'] = $ids[0];
						require_once 'forms/detail.php';
						$this->add_form(new CrmCustomerGroupForm());
					}
				}
				else
				if(Url::check('id') and DB::exists_id('crm_customer_group',$_REQUEST['id']))
				{
					require_once 'forms/detail.php';
					$this->add_form(new CrmCustomerGroupForm());
				}
				else
				{
					Url::redirect_current();
				}
				break;
			case 'edit':
				if(Url::check('id') and DB::exists_id('crm_customer_group',$_REQUEST['id']))
				{
					require_once 'forms/edit.php';
					$this->add_form(new EditCrmCustomerGroupForm());
				}
				else
				{
					Url::redirect_current();
				}
				break;
			case 'add':
				require_once 'forms/edit.php';
				$this->add_form(new EditCrmCustomerGroupForm());
				break;
			case 'view':
				if(Url::check('id') and DB::exists_id('crm_customer_group',$_REQUEST['id']))
				{
					require_once 'forms/detail.php';
					$this->add_form(new CrmCustomerGroupForm());
				}
				else
				{
					Url::redirect_current();
				}
				break;
			case 'move_up':
			case 'move_down':
				if(Url::check('id')and $category=DB::exists_id('crm_customer_group',$_REQUEST['id']))
				{
					if($category['structure_id']!=ID_ROOT)
					{
						require_once 'packages/portal/includes/system/si_database.php';
						si_move_position('crm_customer_group');
					}
					Url::redirect_current();
				}
				else
				{
					Url::redirect_current();
				}
				break;
			default: 
				require_once 'forms/list.php';
				$this->add_form(new ListCrmCustomerGroupForm());
				break;
			}
		}
		else
		{
			URL::access_denied();
		}
	}
}
?>