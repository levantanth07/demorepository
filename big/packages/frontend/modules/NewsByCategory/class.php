<?php
/* 	AUTHOR 	:	KHOAND
	DATE	:	26/06/2013
*/
class NewsByCategory extends Module
{
	function NewsByCategory($row)
	{
		Module::Module($row);
		require_once 'db.php';
		//$this->update_seo();
		if(Url::get('do')=='update_hitcount' and Url::iget('id')){
			NewsByCategoryDB::update_hitcount(Url::iget('id'));
			exit();
		}
		require_once 'forms/list.php';
		$this->add_form(new NewsByCategoryForm());
	}
	function update_seo(){
		Portal::$document_title =  'Trang thông tin tổng hợp '.' - '.Portal::get_setting('site_name').'';
		Portal::$meta_description =  'Trang thông tin tổng hợp, tin tức xã hội, công nghệ, mua sắm, khuyến mãi, tra cứu điểm thi đại học,...'.' - '.Portal::get_setting('site_name').'';
		Portal::$meta_keywords = 'thông tin tổng hợp, tin tức xã hội, công nghệ, mua sắm, khuyến mãi, tra cứu điểm thi đại học,...';
	}
}
?>