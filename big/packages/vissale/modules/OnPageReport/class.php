<?php
class OnPageReport extends Module
{

    function __construct($row)
    {
        //var_dump($row); exit;
        //ini_set('display_errors',1);
        if(Session::get('group_id')){
    			Module::Module($row);
	        require_once 'db.php';
	        require_once 'helper/AssigneeReportSchema.php';
	        require_once 'forms/report.php';

	        $this->add_form(new ReportForm());
    		}
    }
}
?>