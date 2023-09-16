<?php
class PageAdminForm extends Form
{
	function __construct()
	{
		Form::Form("PageAdminForm");
		$this->add('id',new IDType(true,'object_not_exists','page'));
		$this->link_css(Portal::template('core').'/css/category.css');
	}
	function on_submit()
	{
		if($this->check() and URL::get('confirm'))
		{
			$this->delete($this,$_REQUEST['id']);
			Url::redirect_current(array('portal_id','name','package_id'=>isset($_GET['package_id'])?$_GET['package_id']:''));
		}
	}
	function draw()
	{
		$languages = DB::select_all('language');
		DB::query('
			select
				`page`.id
				,`page`.`name` ,`page`.`cachable` ,`page`.`cache_param` ,`page`.`params`
				,`page`.title_'.Portal::language().' as title
				,`page`.description_'.Portal::language().' as description
				,`package`.`name` as package_id
			from
			 	`page`
				left outer join `package` on `package`.id=`page`.package_id
			where
				`page`.id = "'.URL::sget('id').'"');
		if($row = DB::fetch())
		{
		}
		$languages = DB::select_all('language');
		DB::query('
			select module.* from module inner join block on module_id=module.id where 1 and page_id = '.$row['id'].'
		');
		$row['module_related_fields'] = DB::fetch_all();
		$this->parse_layout('detail',$row+array('languages'=>$languages));
	}
	function delete(&$form,$id)
	{
		$row = DB::select('page',$id);
		$blocks = DB::select_all('block','page_id='.$id);
		foreach ($blocks as $key=>$value)
		{
			DB::delete('block_setting', 'block_id='.$value['id']);
		}
		DB::delete('block', 'page_id='.$id);
		DB::delete_id('page', $id);
	}
}
?>