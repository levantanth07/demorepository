<?php
class ListPrivilegeForm extends Form
{
	function __construct()
	{
		Form::Form('ListPrivilegeForm');
		$this->link_css('assets/default/css/cms.css');
	}
	function on_submit()
	{
		if(URL::get('cmd')=='delete')
		{
			require_once 'detail.php';
			foreach(URL::get('selected_ids') as $id)
			{
				PrivilegeForm::delete($this,$id);
				if($this->is_error())
				{
					return;
				}
			}
			require_once 'packages/core/includes/system/update_privilege.php';
			make_privilege_cache();
			Url::redirect_current(array('package_id'=>isset($_GET['package_id'])?$_GET['package_id']:'',));
		}
	}
	function draw()
	{
		$cond = '1 '
				.(URL::get('package_id')?'
					and '.IDStructure::child_cond(DB::fetch('select structure_id from `package` where id="'. URL::get('package_id',1).'"','structure_id'),false,'package.').'
				':'')
		;
		$item_per_page = Module::$current->get_setting('item_per_page',50);
		DB::query('
			select
				count(*) as acount
			from
				`privilege`
				left outer join `package` on `package`.id=`privilege`.`package_id`
				left outer join function on function.id = privilege.category_id
			where '.$cond.'
			'.(URL::get('order_by')?'order by '.URL::get('order_by').(URL::get('order_dir')?' '.URL::get('order_dir'):''):'').'
			limit 0,1
		');
		$count = DB::fetch();
		require_once 'packages/core/includes/utils/paging.php';
		$paging = paging($count['acount'],$item_per_page);
		DB::query('
			select
				`privilege`.id
				,privilege.title_'.Portal::language().' as title
				,`privilege`.`description_'.Portal::language().'` as  description
				,`function`.name_'.Portal::language().' as function_name
				,`package`.`name` as package_id
				,privilege.code
			from
			 	`privilege`
				left outer join `package` on `package`.id=`privilege`.`package_id`
				left outer join `function` on `function`.id = privilege.category_id
			where
				'.$cond.'
			'.(URL::get('order_by')?'order by '.URL::get('order_by').(URL::get('order_dir')?' '.URL::get('order_dir'):''):' order by id DESC').'
			limit '.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
		$items = DB::fetch_all();
		DB::query('
			select
				id,name
				,structure_id
			from
				`package`
			order by structure_id');
		$packages = DB::fetch_all();
		require_once 'packages/core/includes/utils/category.php';
		category_indent($packages);
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
				'paging'=>$paging,
				'packages'=>$packages,
			)
		);
	}
}
?>