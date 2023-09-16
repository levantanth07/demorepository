<?php
class FilterWordBadForm extends Form
{
	function FilterWordBadForm()
	{
		Form::Form('FilterWordBadForm');
		$this->add('word_bad',new TextType(true,'invalid_word_bad',0,2000));
		$this->link_css('assets/default/css/cms.css');
	}
	function filter_bad_word($string,$badword,$symbol = '***')
	{
		foreach($badword as $key => $value)
		{
			$string = eregi_replace($value,$symbol,$string);
		}
		return $string;
	}
	function on_submit()
	{
		if($this->check())
		{
			if(strpos(Url::sget('word_bad'),'*') === false)
			{
				$content = Url::sget('word_bad');
				$symbol = Url::get('symbol');
				$words = explode(',',$content);
				if(sizeof($words)>0)
				{
					set_time_limit(0);
					$field = 'id';
					$languages = DB::select_all('language');
					foreach($languages as $language)
					{
						$field .= ',name_'.$language['id'].',brief_'.$language['id'].',description_'.$language['id'];
					}
					$items = DB::fetch_all('select '.$field.' from news');
					foreach($items as $key=>$value)
					{
						$arr = array();
						foreach($languages as $language)
						{
							$arr['name_'.$language['id']] = $this->filter_bad_word($value['name_'.$language['id']],$words,$symbol);
							$arr['brief_'.$language['id']] = $this->filter_bad_word($value['brief_'.$language['id']],$words,$symbol);
							$arr['description_'.$language['id']] = $this->filter_bad_word($value['description_'.$language['id']],$words,$symbol);
						}
						DB::update_id('news',$arr,$key);
					}
				}
				@file_put_contents('cache/config/bad_word.txt',$content);
				Url::redirect_current();
			}
			else
			{
				$this->error('error_word_bad','error_word_bad');
			}
		}
	}
	function draw()
	{
		$bad_word = @file_get_contents('cache/config/bad_word.txt');
		$this->parse_layout('list',array(
			'bad_word'=>$bad_word
		));
	}
}
?>