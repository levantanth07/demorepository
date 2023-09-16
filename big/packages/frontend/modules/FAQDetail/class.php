<?php
/*
WRITTEN BY : SANGVT
CREATE DATE : 09/08/2009
FUNCTION : HIEN THI CHI TIET TIN
*/
class FAQDetail extends Module
{
	static $item = false;
	function FAQDetail($row)
	{
		Module::Module($row);
		require_once 'db.php';
		require_once 'forms/list.php';
		$this->init();
		$this->add_form(new FAQDetailForm());
	}
	function init()
	{
		if(Url::get('name_id') and $item = FAQDetailDB::get_item('news.name_id="'.Url::get('name_id').'" and news.portal_id="'.PORTAL_ID.'"'))
		{
			FAQDetail::$item = $item;
			$_REQUEST['category_id'] = $item['category_id'];
			$_REQUEST['redirect_page'] = 'trang-tin';
		}
	}
}
?>