<?php
class DeleteNewsAdminForm extends Form
{
	function __construct()
	{
		Form::Form('DeleteNewsAdminForm');
		$this->link_css('assets/default/css/cms.css');
	}
	function on_submit()
	{
		if(Url::get('cmd')=='delete_id' and $item = DB::exists_id('news',intval(Url::get('id')))){
			DB::delete_id('news',intval(Url::get('id')));
			@unlink($item['image_url']);
			@unlink($item['small_thumb_url']);
			@unlink($item['file']);
			Url::redirect_current();
		}
	}
	function draw()
	{
		$this->map = array();
		if(Url::get('id') and $this->map['item'] = NewsAdminDB::get_item()){
			$this->parse_layout('delete',$this->map);
		}else{
			echo '<div class="notice">'.Portal::language('data_dont_exists').'</div>';
		}
	}
}
?>
