<?php
class SlideListMediaAdminForm extends Form
{
	function SlideListMediaAdminForm()
	{
		Form::Form('SlideListMediaAdminForm');
		$this->link_css('assets/default/css/cms.css');
	}
	function delete()
	{
		if(isset($_REQUEST['selected_ids']) and count($_REQUEST['selected_ids'])>0)
		{
			foreach($_REQUEST['selected_ids'] as $key)
			{
				if($item = DB::exists_id('slide',$key) and User::can_delete(false,ANY_CATEGORY))
				{
					save_recycle_bin('slide',$item);
					DB::delete_id('slide',$key);
					save_log($key);
				}
			}
		}
		Url::redirect_current(array('cmd'=>'slide_list'));
	}
	function on_submit()
	{
		if(Url::get('cmd'))
		{
			$this->delete();
		}
	}
	function draw()
	{
		$items = MediaAdminDB::get_slide();
		$this->parse_layout('slide_list',array(
			'items'=>$items
			,'total'=>count($items)
		));
	}
}
?>
