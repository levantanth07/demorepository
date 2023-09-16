<?php
class ListFunctionCategoryForm extends Form
{
	function ListFunctionCategoryForm()
	{
		Form::Form('ListFunctionCategoryForm');
	}
	function on_submit()
	{
		if(URL::get('confirm'))
		{
			$this->deleted_selected_ids();
		}
	}
	function draw()
	{
		$this->get_just_edited_id();
		$this->get_items();
		$items=$this->items;
		$this->parse_layout('list',$this->just_edited_id+
			array(
				'items'=>$items,
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
		require_once 'detail.php';
		foreach(URL::get('selected_ids') as $id)
		{
			if($id and $category=DB::fetch('select * from function where id='.intval($id)))
			{
				save_recycle_bin('function',$category);
				DB::delete_id('function',$id);
				save_log($id);
			}
			if($this->is_error())
			{
				return;
			}
		}
		Url::redirect_current();
	}
	function get_items()
	{
		$this->get_select_condition();
		$this->items = DB::fetch_all('
			select
				function.id
				,function.structure_id
				,function.status
				,function.icon_url
				,function.name_'.Portal::language().' as name
				,function.description_'.Portal::language().' as description
				,function.open_new_window
			from
			 	function
			where
				 '.$this->cond.'
			order by
				function.structure_id
		',false);
		require_once 'packages/core/includes/utils/category.php';
		category_indent($this->items);
	}
	function get_select_condition()
	{
		$this->cond = ' 1 '
			.((URL::get('cmd')=='delete' and is_array(URL::get('selected_ids')))?' and function.id in (\''.join(URL::get('selected_ids'),'\',\'').'\')':'')
		;
	}
}
?>