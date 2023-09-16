<?php

class AdminTrackingOrders extends Module
{
	function __construct($row)
	{
		Module::Module($row);
		require_once 'db.php';
		require_once 'forms/list.php';
		require_once 'ArrHelper.php';

		$this->add_form(new ListOrdersForm());
	}
}
