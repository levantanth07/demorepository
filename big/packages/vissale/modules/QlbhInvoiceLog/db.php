<?php
class QlbhInvoiceLogDB{
	function get_shop($accout_id){
		return DB::fetch_all('SELECT id,CONCAT(name,CONCAT(\' - \',address)) AS name FROM qlbh_shop where account_id="'.$accout_id.'" ORDER BY name');
	}
}