<?php
class ListRecycleBinForm extends Form
{
	function ListRecycleBinForm()
	{
		Form::Form('ListRecycleBinForm');
		$this->link_css('assets/default/css/cms.css');
	}
	function on_submit()
	{
		if(Url::get('cmd') == 'delete')
		{
			$dir = 'backup/recycle bin/';
			empty_dir($dir);
		}
		//Url::redirect_current();
	}
	function draw()
	{
		$items = RecycleBinDB::get_items();
		$this->parse_layout('list',array(
			'items'=>$items
		));
	}
}
?>