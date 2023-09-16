<?php
class CurrencyEditForm extends Form
{
	function CurrencyEditForm()
	{
		Form::Form('CurrencyEditForm');
		$this->add('id',new TextType(true,'invalid_id',0,2000));
		$this->add('name',new TextType(true,'invalid_name',0,2000));
		$this->add('exchange',new TextType(true,'invalid_exchange',0,2000));
		$this->link_css('assets/default/css/cms.css');
	}
	function on_submit()
	{
		if($this->check())
		{
			$currency = array('id','name','exchange','position');
			if(Url::get('cmd')=='edit' and Url::sget('id') and $item = DB::exists_id('currency',Url::sget('id')))
			{
				DB::update_id('currency',$currency,$item['id']);
			}
			else
			{
				if(!$item = DB::exists_id('currency',Url::sget('id')))
				{
					DB::insert('currency',$currency);
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
		if(Url::get('cmd')=='edit' and $row = DB::exists_id('currency',Url::sget('id')))
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