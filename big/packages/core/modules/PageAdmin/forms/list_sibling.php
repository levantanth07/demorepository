<?php
class ListPageAdminSiblingForm extends Form
{
	function ListPageAdminSiblingForm()
	{
		Form::Form('ListPageAdminSiblingForm');
		$this->link_css(Portal::template('core').'/css/category.css');
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
				PageAdminForm::delete($this,$id);
				if($this->is_error())
				{
					return;
				}
			}
			Url::redirect_current(array('cmd'=>'list_sibling','package_id'=>isset($_GET['package_id'])?$_GET['package_id']:'',
	'name'=>isset($_GET['name'])?$_GET['name']:'',
	  ));
		}
	}
	function draw()
	{
		$languages = DB::select_all('language');
		$cond = 'page.name="'.URL::get('name').'"';
		$item_per_page = Module::$current->get_setting('item_per_page',50);
		DB::query('
			select count(*) as acount
			from
				`page`
				left outer join `package` on `package`.id=`page`.`package_id`
			where '.$cond.'
			'.(URL::get('order_by')?'order by '.URL::get('order_by').(URL::get('order_dir')?' '.URL::get('order_dir'):''):'order by params').'
			limit 0,1
		');
		$count = DB::fetch();
		require_once 'packages/core/includes/utils/paging.php';
		$paging = paging($count['acount'],$item_per_page);
		DB::query('
			select
				`page`.id
				,`page`.`name` ,`page`.`read_only` ,`page`.`show` ,`page`.`cachable` ,`page`.`cache_param` ,`page`.`params`
				,`page`.title_'.Portal::language().' as title ,`page`.description_'.Portal::language().' as description
				,`package`.`name` as package_id
			from
			 	`page`
				left outer join `package` on `package`.id=`page`.`package_id`
			where '.$cond.'
			'.(URL::get('order_by')?'order by '.URL::get('order_by').(URL::get('order_dir')?' '.URL::get('order_dir'):''):'order by params').'
			limit '.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
		$items = DB::fetch_all();
		DB::query('
			select
				id,name as name
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
		$this->parse_layout('list_sibling',
			array(
				'items'=>$items,
				'paging'=>$paging,
				'packages'=>$packages,
			)
		);
	}
}
?>