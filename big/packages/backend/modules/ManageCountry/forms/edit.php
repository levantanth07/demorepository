<?php
class countryEditForm extends Form
{
	function countryEditForm()
	{
		Form::Form('countryEditForm');
		$this->add('name',new TextType(true,'invalid_name',0,2000)); 
		$this->link_css('skins/default/css/cms.css');
	}
	function on_submit()
	{
		if($this->check())
		{
			$country = array('name');
			if(Url::get('cmd')=='edit' and Url::get('id') and $item = DB::exists_id('country',Url::iget('id')))
			{
				DB::update_id('country',$country,$item['id']);
			}
			else
			{
				if(!$item = DB::exists_id('country',Url::iget('id')))
				{
					DB::insert('country',$country);
				}
				else
				{
					$this->error('id','duplicate_id');
					return false;
				}	
			}		
			Url::redirect_current();
		}		
	}
	function draw()
	{	
		if(Url::get('cmd')=='edit' and $row = DB::exists_id('country',Url::iget('id')))
		{
			foreach($row as $key=>$value)
			{
				if(is_string($value) and !isset($_REQUEST[$key]))
				{
					$_REQUEST[$key] = $value;
				}
			}	
		}	
		$this->parse_layout('edit');
	}
}
?>