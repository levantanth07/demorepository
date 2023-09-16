<?php
class ListModeratorForm extends Form
{
	function __construct()
	{
		Form::Form('ListModeratorForm');
		$this->link_css('assets/default/css/cms.css');
	}
	function on_submit()
	{
		if(URL::get('cmd')=='delete')
		{
			$this->deleted_selected_ids();
		}
		Url::redirect_current(array('user_id'));
	}
	function draw()
	{
		$this->get_just_edited_id();
		$this->get_select_condition();
		$this->get_paging();
		$this->parse_layout('list',$this->just_edited_id+
			array(
				'items'=>ModeratorDB::get_items($this->cond, $this->item_per_page),
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
				if($item = DB::exists_id('account_privilege',$id))
				{
					DB::delete_id('account_privilege',$id);
					DB::delete('account_setting','account_id = "'.$item['account_id'].'" and setting_id = "privilege"');
					DB::update('account',array('cache_privilege'=>''),' id ="'.$item['account_id'].'"');
				}
			}
			Url::redirect_current(array('portal_id'=>Url::get('portal_id'),'privilege'=>Url::get('privilege')));
		}
	}
	function get_paging()
	{
		if (Url::get('item_per_page'))
		{
			$this->item_per_page = Url::get('item_per_page');
		}else
		{
			$this->item_per_page = 30;
		}
		$this->total_item = ModeratorDB::get_item_count($this->cond);
		require_once 'packages/core/includes/utils/paging.php';
		$this->paging = paging($this->total_item,$this->item_per_page,4);
	}
	function get_select_condition()
	{
		if(URL::get('user_id') and User::is_admin())
		{
			$this->cond = ' account_id="'.URL::get('user_id').'"';
		}
		else
		{
			$portal_id=Url::get('portal_id')?addslashes(Url::get('portal_id')):str_replace('#','',PORTAL_ID);
			$this->cond = '
				'.(User::is_admin()?'account_privilege.group_id='.Session::get('group_id'):'1=1').'
				 and  account_privilege.portal_id="#'.$portal_id.'"'
				.((URL::get('cmd')=='delete' and is_array(URL::get('selected_ids')))?' and `account_privilege`.id in ("'.join(URL::get('selected_ids'),'","').'")':'')
			;
		}
	}
}
?>