<?php 
class QlbhDaiLyTonKho extends Module
{
	public static $item = array();
	function __construct($row)
	{
		Module::Module($row);
		require_once 'packages/vissale/lib/php/qlbh.php';
		if(Session::get('warehouse_id')){
			require_once 'forms/options.php';
			$this->add_form(new QlbhDaiLyTonKhoOptionsForm());
		}
	}	
}
?>