<?php
class ListLanguageForm extends Form
{
	function __construct()
	{
		Form::Form('ListLanguageForm');
		$this->link_css('assets/default/css/category.css');
		$this->link_css('assets/default/css/cms.css');
	}
	function on_submit()
	{
		if(URL::get('confirm'))
		{
			require_once 'detail.php';
			foreach(URL::get('selected_ids') as $id)
			{
				if($item = DB::exists_id('language',$id))
				{
					save_recycle_bin('language',$item);
					LanguageForm::delete($this,$id);
					save_log($id);
				}
			}
			Url::redirect_current(array());
		}
	}
	function draw()
	{
		$cond = '
				1 '
			.((URL::get('cmd')=='delete' and is_array(URL::get('selected_ids')))?' and `language`.id in ('.join(URL::get('selected_ids'),',').')':'')
		;
		$item_per_page = Module::$current->get_setting('item_per_page',50);
		DB::query('
			select count(*) as acount
			from
				`language`
			where '.$cond.'
			'.(URL::get('order_by')?'order by '.URL::get('order_by').(URL::get('order_dir')?' '.URL::get('order_dir'):''):'').'
			limit 0,1
		');
		$count = DB::fetch();
		require_once 'packages/core/includes/utils/paging.php';
		$paging = paging($count['acount'],$item_per_page);
		DB::query('
			select
				`language`.id
				,`language`.`code` ,`language`.`name`
				,`language`.`icon_url`
				,`language`.`active`
				,`language`.`default`	
			from
			 	`language`
			where '.$cond.'
			'.(URL::get('order_by')?'order by '.URL::get('order_by').(URL::get('order_dir')?' '.URL::get('order_dir'):''):'').'
			limit '.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
		$items = DB::fetch_all();
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
			)
		);
	}
}
?>