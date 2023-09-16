<?php
class FetchTemplateForm extends Form
{
	function FetchTemplateForm()
	{
		Form::Form('FetchTemplateForm');
		$this->link_css(Portal::template('cms').'/css/category.css');
	}
	function draw()
	{
		$template=FetchTemplateDB::get_template();
		$data='';
		$first=false;
		foreach($template as $key=>$value)
		{
			if($first)
			{
				$data.=',';
			}
			$data.='['.$value['id'].',"'.$value['name'].'['.$value['id'].']"]';
			$first=true;
		}
		$this->parse_layout('template',array('data'=>$data));
	}
}
?>