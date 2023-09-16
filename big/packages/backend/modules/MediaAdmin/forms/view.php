<?php
class ViewSlideMediaAdminForm extends Form
{
	function ViewSlideMediaAdminForm()
	{
		Form::Form('ViewSlideMediaAdminForm');
		$this->link_css('assets/default/css/cms.css');
	}
	function draw()
	{
		if(!$row = MediaAdminDB::get_html_slide(intval(Url::get('slide_id'))))
		{
			$row = array('html'=>'');
		}
		$slides = MediaAdminDB::get_slide();
		$this->parse_layout('view',$row+array(
			'slide_id_list'=>String::get_list($slides)
		));
	}
}
?>
