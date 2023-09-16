<?php
class ListManageAdvertismentForm extends Form
{
	function ListManageAdvertismentForm()
	{
		Form::Form('ListManageAdvertismentForm');
		$this->link_css('assets/default/css/cms.css');
	}
	function on_submit()
	{
		if(URL::get('cmd')=='delete')
		{
			$this->deleted_selected_ids();
		}
		Url::redirect_current();
	}
	function draw()
	{
		$this->get_just_edited_id();
		$this->get_select_condition();
		$this->get_paging();
		$items = ManageAdvertismentDB::get_items($this->cond, $this->item_per_page);
	 		$this->parse_layout('list',$this->just_edited_id+
			array(
				'items'=>$items,
				'paging'=>$this->paging,
				'total_page'=>$this->total_item,
			)
		);
	}
	function get_just_edited_id()
	{
		$this->just_edited_id['just_edited_ids'] = array();
		if (UrL::get('selected_ids'))
		{
			if(is_string(UrL::get('selected_ids')))
			{
				if (strstr(UrL::get('selected_ids'),','))
				{
					$this->just_edited_id['just_edited_ids']=explode(',',UrL::get('selected_ids'));
				}
				else
				{
					$this->just_edited_id['just_edited_ids']=array('0'=>UrL::get('selected_ids'));
				}
			}
		}
	}
	function deleted_selected_ids()
	{
		if(URL::get('selected_ids'))
		{
			foreach(URL::get('selected_ids') as $id)
			{
				if($item = DB::exists_id('advertisment',$id))
				{
					save_recycle_bin('advertisment',$item);
					DB::delete_id('advertisment',$id);
					save_log($id);
				}
			}
		Url::redirect_current();
		}
	}
	function get_paging()
	{
		if (Url::get('item_per_page'))
		{
			$this->item_per_page = Url::get('item_per_page');
		}else
		{
			$this->item_per_page = 15;
		}
		$this->total_item = ManageAdvertismentDB::get_item_count($this->cond);
		require_once 'packages/core/includes/utils/paging.php';
		$this->paging = paging($this->total_item,$this->item_per_page,4);
	}
	function get_select_condition()
	{
		$this->cond = '
				media.portal_id="'.PORTAL_ID.'"  '
			.((URL::get('cmd')=='delete' and is_array(URL::get('selected_ids')))?' and `advertisment`.id in ("'.join(URL::get('selected_ids'),'","').'")':'')
		;
	}
}
?>