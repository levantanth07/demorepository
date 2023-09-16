<?php
class ListQlbhProductCategoryForm extends Form
{
	function __construct()
	{
		Form::Form('ListQlbhProductCategoryForm');
		$this->link_css('skins/default/css/cms.css');
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
		$this->get_items(PORTAL_ID);
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
			if($id and $category=DB::exists_id('qlbh_product_category',$id) and User::can_edit(false,$category['structure_id']))
			{
				save_recycle_bin('qlbh_product_category',$category);
				DB::delete_id('qlbh_product_category',$id);
				@unlink($category['icon_url']);
				save_log($id);
			}	
			if($this->is_error())
			{
				return;
			}
		}
		Url::redirect_current(Module::$current->redirect_parameters);
	}
	function get_items($portal_id)
	{
		$this->get_select_condition($portal_id);
		$extra_cond  = '';
		if(Url::get('type'))
		{
			$extra_cond = ' and (category.type="'.Url::get('type','NEWS').'")';
		}
		$this->items = DB::fetch_all('
			select 
				`qlbh_product_category`.id
				,`qlbh_product_category`.structure_id
				,`qlbh_product_category`.`status` 
				,`qlbh_product_category`.`icon_url` 
				,`qlbh_product_category`.name
				,`qlbh_product_category`.description
			from 
			 	`qlbh_product_category`
				left outer join `type` on `type`.id=`qlbh_product_category`.`type` 
			where
				 '.$this->cond.$extra_cond.'									
			order by 
				`qlbh_product_category`.structure_id
		');
		require_once 'packages/core/includes/utils/category.php'; 
		category_indent($this->items);
		$i=0;
		foreach ($this->items as $key=>$value)
		{
			$this->items[$key]['i']=$i++;
			if(!User::can_view(false,$value['structure_id']))
			{
				unset($this->items[$key]);
			}
		}
	}
	function get_select_condition($portal_id)
	{
		$this->cond = '
				qlbh_product_category.type="PRODUCT"' 
			.((URL::get('cmd')=='delete' and is_array(URL::get('selected_ids')))?' and `qlbh_product_category`.id in ("'.join(URL::get('selected_ids'),'","').'")':'')
		;
	}
	
}
?>