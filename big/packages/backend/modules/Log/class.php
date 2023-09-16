<?php
class Log extends Module
{
	function __construct($row)
	{
		Module::Module($row);
		require_once 'db.php';
		if(Session::get('admin_group') or User::is_admin())
		{
		    switch (Url::get('do')){
                case 'status':
                    require_once 'forms/log_status.php';
                    $this->add_form(new LogStatusForm());
                    break;
                case 'log_order':
                	require_once 'forms/log_order.php';
                    $this->add_form(new LogOrderForm());
                    break;
                case 'log_order_ajax':
                	require_once 'forms/log_order_ajax.php';
                    $this->add_form(new LogOrderAjaxForm());
                    break;
                default:
                    require_once 'forms/list.php';
                    $this->add_form(new ListLogForm());
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