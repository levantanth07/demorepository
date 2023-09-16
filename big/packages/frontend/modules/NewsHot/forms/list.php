<?php
class NewsHotForm extends Form
{
	function NewsHotForm()
	{
		Form::Form('NewsHotForm');
	}
	function draw(){
		$this->map = array();
		$cond ='publish and portal_id="'.PORTAL_ID.'" and status="HOT"';
		$limit ='0,1';
		$this->map['news'] = NewsHotDB::get_news($cond,$limit);
		$this->parse_layout('list',$this->map);
	}
}
?>