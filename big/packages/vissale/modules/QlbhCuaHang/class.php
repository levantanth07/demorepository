<?php 
class QlbhCuaHang extends Module
{
	function __construct($row)
	{
		Module::Module($row);
		if(User::can_view(false,ANY_CATEGORY) or Session::is_set('warehouse_id'))
		{
			require_once 'forms/edit.php';
			$this->add_form(new EditQlbhCuaHangForm());
		}
	}
}
?>