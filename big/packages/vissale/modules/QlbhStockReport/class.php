<?php 
class QlbhStockReport extends Module
{
	public static $item = array();
	function __construct($row)
	{
		Module::Module($row);
		require_once 'db.php';
        if(check_user_privilege('ADMIN_KHO')){
			require_once 'forms/options.php';
			$this->add_form(new QlbhStockReportOptionsForm());
		}else{
			Url::access_denied();
		}
	}	
}
?>