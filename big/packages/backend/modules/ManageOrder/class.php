<?php
class ManageOrder extends Module
{
	public static $item = array();
	function ManageOrder($row)
	{
		Module::Module($row);
		require_once 'db.php';
		if(User::is_login()){
			if(Url::get('cmd')=='delete' and Url::iget('id') and DB::exists('select * from order_invoice where id = '.Url::iget('id').'')){
				if(User::can_delete(false,ANY_CATEGORY)){
					DB::delete('order_invoice','id = '.Url::iget('id').'');
					DB::delete('order_invoice_detail','invoice_id = '.Url::iget('id').'');
					
					echo '<script>alert("Xóa thành công!");window.location="'.Url::build_current().'"</script>';
				}
			}
			if(Url::get('cmd')=='check_ajax'){
				$this->check(); exit();
			}
			require_once 'forms/list.php';
			$this->add_form(new ManageOrderForm());
		}
	}
	function check(){
		if($comfirm_code = Url::get('verify_comfirm_code'))
		{
			if($comfirm_code == Session::get('security_code')) echo 'true';
			else echo 'false';
		}
	}
}
?>