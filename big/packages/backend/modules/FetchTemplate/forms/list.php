<?php
class ListFetchTemplateForm extends Form
{
	function ListFetchTemplateForm()
	{
		Form::Form('ListFetchTemplateForm');
	}
	function on_submit()
	{
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
		$this->parse_layout('list',array('data'=>$data));
	}
}
?>