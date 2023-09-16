<?php 
class QlbhInventory extends Module
{
	public static $item = array();
	function __construct($row)
	{
		Module::Module($row);
		mysql_query("SET charset 'utf8';");
		mysql_query("SET names 'utf8';");
		require_once('packages/vissale/lib/php/vissale.php');
		switch (Url::get('cmd')){
			case 'add':
				if(Session::get('admin_group')){
					require_once 'forms/edit.php';
					$this->add_form(new EditQlbhInventoryForm());
				}else{
					Url::access_denied();
				}
				break;
			case 'edit':
				if(Session::get('admin_group') and QlbhInventory::$item = DB::select('qlbh_inventory','id ='.Url::iget('id').' and group_id='.Session::get('group_id').'')){
					require_once 'forms/edit.php';
					$this->add_form(new EditQlbhInventoryForm());
				}else{
					Url::access_denied();
				}
				break;
			case 'delete':
				if(Session::get('admin_group')){
					if(Url::get('id') and DB::exists('SELECT id FROM qlbh_inventory WHERE id = '.Url::iget('id').' and group_id='.Session::get('group_id').'')){
						DB::delete('qlbh_inventory','id= '.Url::iget('id'));
						Url::redirect('notice',array('cmd'=>'delete','href'=>'index062019.php?page='.Url::get('page')));
					}
					if(Url::get('item_check_box')){
						$arr = Url::get('item_check_box');	
						for($i=0;$i<sizeof($arr);$i++){
							DB::delete('qlbh_inventory','id = '.$arr[$i]);
						}
						Url::js_redirect(true,'Bạn đã xoá thành công');
					}else{
						Url::redirect_current();
					}
				}else{
					Url::access_denied();
				}
				break;
			default:
				if(User::is_login()){
					require_once 'forms/list.php';
					$this->add_form(new ListQlbhInventoryForm());
				}else{
					Url::access_denied();
				}
				break;
		}
	}	
}
?>