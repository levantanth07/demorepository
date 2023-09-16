<?php
class QlbhSaleReport extends Module{
	public static $item = array();
	function __construct($row){
		Module::Module($row);
		require_once 'forms/list.php';
		$this->add_form(new ListQlbhSaleReportForm());
	}
}
?>