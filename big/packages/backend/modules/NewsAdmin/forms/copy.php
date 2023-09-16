<?php
class CopyNewsAdminForm extends Form
{
	function __construct()
	{
		Form::Form('CopyNewsAdminForm');
		$this->add('category_id',new TextType(true,'invalid_category_id',0,2000));
		$this->link_css('assets/default/css/cms.css');
	}
	function copy_items()
	{
		if(Url::get('category_id') and $_REQUEST['selected_ids']!='' and $items =@explode(',',$_REQUEST['selected_ids']))
		{
			foreach($items as $key)
			{
				if($news = DB::exists_id('news',$key))
				{
					unset($news['id']);
					$news['category_id'] = intval(Url::get('category_id'));
					$news['name_id'] = $news['name_id'].date('d-m',time());
					$id = DB::insert('news',$news);
					save_log($id);
				}
			}
			Url::redirect_current();
		}
	}
	function move_items()
	{
		if(Url::get('category_id') and $_REQUEST['selected_ids']!='' and $items =@explode(',',$_REQUEST['selected_ids']))
		{
			foreach($items as $key)
			{
				DB::update_id('news',array('category_id'=>intval(Url::get('category_id'))),$key);
			}
			Url::redirect_current();
		}
	}
	function on_submit()
	{
		switch(Url::get('cmd'))
		{
			case 'move':
				$this->move_items();
				break;
			case 'copy':
				$this->copy_items();
				break;
		}
	}
	function draw()
	{
		$this->parse_layout('copy',array(
			'category_id_list'=>MiString::get_list(NewsAdminDB::get_category())
		));
	}
}
?>
