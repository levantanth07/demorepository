<?php
/* 	AUTHOR 	:	KHOAND
	DATE	:11/09/2014
*/
class ProductCategory extends Module
{
	function ProductCategory($row)
	{
		Module::Module($row);
		require_once 'db.php';
		require_once 'forms/list.php';
		$this->add_form(new ProductCategoryForm());
	}
}
?>