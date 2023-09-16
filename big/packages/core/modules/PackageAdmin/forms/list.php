<?php
class ListPackageAdminForm extends Form
{
	function ListPackageAdminForm()
	{
		Form::Form('ListPackageAdminForm');
	}
	function on_submit()
	{
		if(URL::get('confirm'))
		{
			foreach(URL::get('selected_ids') as $id)
			{
			}
			require_once 'detail.php';
			foreach(URL::get('selected_ids') as $id)
			{
				PackageAdminForm::delete($this,$id);
				if($this->is_error())
				{
					return;
				}
			}
			Url::redirect_current(array(
	'name'=>isset($_GET['name'])?$_GET['name']:'',
	  ));
		}
	}
	function draw()
	{
		$languages = DB::select_all('language');
		$cond = '
				1 '
				.(URL::get('name')?' and `package`.`name` LIKE "%'.URL::get('name').'%"':'')
			.((URL::get('cmd')=='delete' and is_array(URL::get('selected_ids')))?' and `package`.id in ('.join(URL::get('selected_ids'),',').')':'')
		;
		DB::query('
			select
				`package`.id
				,`package`.structure_id
				,`package`.`name`
				,`package`.`type`
				,`package`.title_'.Portal::language().' as title ,`package`.description_'.Portal::language().' as description
			from
			 	`package`
			where '.$cond.'
			order by `package`.structure_id
		');
		$items = DB::fetch_all();
		require_once 'packages/core/includes/utils/category.php';
		category_indent($items);
		$i=1;
		foreach ($items as $key=>$value)
		{
			$items[$key]['i']=$i++;
		}
		$just_edited_id['just_edited_ids'] = array();
		if (UrL::get('selected_ids'))
		{
			if(is_string(UrL::get('selected_ids')))
			{
				if (strstr(UrL::get('selected_ids'),','))
				{
					$just_edited_id['just_edited_ids']=explode(',',UrL::get('selected_ids'));
				}
				else
				{
					$just_edited_id['just_edited_ids']=array('0'=>UrL::get('selected_ids'));
				}
			}
		}
		$this->parse_layout('list',$just_edited_id+
			array(
				'items'=>$items,
			)
		);
	}
}
?>