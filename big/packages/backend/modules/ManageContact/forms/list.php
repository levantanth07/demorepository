<?php
class ManageContactForm extends Form
{
	function ManageContactForm()
	{
		Form::Form('ManageContactForm');
		$this->link_css('skins/default/css/cms.css');
	}
	function on_submit()
	{
	}
	function draw()
	{
		$this->map = array();
		$item_per_page = 50;
		$cond = ' 1=1 ';
		$count = ManageContactDB::get_total($cond);
		require_once 'packages/core/includes/utils/paging.php';
		$paging = paging($count['acount'],$item_per_page);
		$items = ManageContactDB::get_items($item_per_page,$cond);
		$this->parse_layout('list',array(
			'paging'=>$paging,
			'items'=>$items
		));
	}
}
?>