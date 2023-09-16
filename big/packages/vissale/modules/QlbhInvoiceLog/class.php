<?php 
class QlbhInvoiceLog extends Module
{
	public static $item = array();
	function __construct($row)
	{
		Module::Module($row);
		require_once('packages/working/includes/php/qlbh.php');
		require_once('db.php');
		switch (Url::get('cmd')){
			case 'view':
				if((User::can_edit(false,ANY_CATEGORY)  or Session::is_set('warehouse_id') or Session::get('qlkv')==1)  and Url::get('id') and QlbhInvoiceLog::$item = DB::select('qlbh_stock_invoice','id ='.Url::iget('id'))){
					require_once 'forms/view.php';
					$this->add_form(new ViewQlbhInvoiceLogForm());
				}else{
					Url::access_denied();
				}
				break;
			case 'add':
				if(User::can_add(false,ANY_CATEGORY) or Session::is_set('warehouse_id')){
					require_once 'forms/edit.php';
					$this->add_form(new EditQlbhInvoiceLogForm());
				}else{
					Url::access_denied();
				}
				break;
			case 'edit':
				if((User::can_edit(false,ANY_CATEGORY)  or Session::is_set('warehouse_id')) and Url::get('id') and QlbhInvoiceLog::$item = DB::select('qlbh_stock_invoice','id ='.Url::iget('id'))){
					require_once 'forms/edit.php';
					$this->add_form(new EditQlbhInvoiceLogForm());
				}else{
					Url::access_denied();
				}
				break;
			case 'delete':
				if(User::can_delete(false,ANY_CATEGORY) or Session::is_set('warehouse_id')){
					if(Url::get('id') and DB::exists('SELECT id FROM qlbh_stock_invoice WHERE id = '.Url::iget('id').'')){
						DB::delete('qlbh_stock_invoice_detail','invoice_id= '.Url::iget('id'));
						DB::delete('qlbh_stock_invoice','id= '.Url::iget('id'));
						Url::redirect_current();
					}
					if(Url::get('item_check_box')){
						$arr = Url::get('item_check_box');	
						for($i=0;$i<sizeof($arr);$i++){
							DB::delete('qlbh_stock_invoice','id = '.$arr[$i]);
							DB::delete('qlbh_stock_invoice_detail','invoice_id= '.$arr[$i]);
						}
						Url::redirect_current();
					}else{
						Url::redirect_current();
					}
				}else{
					//
					echo 'Không có quyền truy cập!';
					exit();
				}
				break;
			default:
				if(User::can_view(false,ANY_CATEGORY) or Session::get('qlkv')==1){
					require_once 'forms/list.php';
					$this->add_form(new ListQlbhInvoiceLogForm());
				}else{
					Url::access_denied();
				}
				break;
		}
	}	
}
?>