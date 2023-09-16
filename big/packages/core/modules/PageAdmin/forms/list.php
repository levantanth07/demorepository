<?php
class ListPageAdminForm extends Form{
	function __construct(){
		Form::Form('ListPageAdminForm');
	}
	function on_submit(){
		if(URL::get('confirm')){
			require_once 'detail.php';
			foreach(URL::get('selected_ids') as $id){
				PageAdminForm::delete($this,$id);
				if($this->is_error()){
					return;
				}
			}
			Url::redirect_current(array('portal_id','package_id'=>isset($_GET['package_id'])?$_GET['package_id']:'',
	'name'=>isset($_GET['name'])?$_GET['name']:'',
	  ));
		}
	}
	function draw(){
		$languages = DB::select_all('language');
		$cond = ' 1 '
				.(URL::get('package_id')?'
					and '.IDStructure::child_cond(DB::fetch('select structure_id from `package` where id="'. URL::get('package_id',15).'"','structure_id'),false,'package.').'
				':'')
				.(URL::get('name')?' and `page`.`name` LIKE "%'.URL::get('name').'%"':'')
			.((URL::get('cmd')=='delete' and is_array(URL::get('selected_ids')))?' and `page`.id in ('.join(URL::get('selected_ids'),',').')':'')
			.(URL::get('portal_id')?
				((URL::get('portal_id')=='default')?' and params not like "portal=%"':
				' and params like "portal='.URL::get('portal_id').'%"'):'')
		;
		$item_per_page = Module::$current->get_setting('item_per_page',50);
		$sql = '
			select
				count(distinct page.id) as total
			from
				`page`
				left outer join `package` on `package`.id=`page`.`package_id`
			where '.$cond.'
		';
		$count = DB::fetch($sql,'total');
		require_once 'packages/core/includes/utils/paging.php';
		$paging = paging($count,$item_per_page);
		DB::query('
			select
				`page`.id
				,`page`.`name` ,`page`.`read_only` ,`page`.`show` ,`page`.`cachable` ,`page`.`cache_param` ,`page`.`params`
				,`page`.title_'.Portal::language().' as title ,`page`.description_'.Portal::language().' as description
				,`package`.`name` as package_id
				,count(page.name)-1 as is_sibling
			from
			 	`page`
				left outer join `package` on `package`.id=`page`.`package_id`
			where '.$cond.'
			group by page.name
			'.(URL::get('order_by')?'order by '.URL::get('order_by').(URL::get('order_dir')?' '.URL::get('order_dir'):''):'order by page.id desc').'
			limit '.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
		$items = DB::fetch_all();
		foreach($items as $id=>$item){
			if($item['is_sibling']){
				$items[$id]['href'] = Url::build_current(array('cmd'=>'list_sibling','package_id','name'=>$item['name']));
			}
			else{
				$items[$id]['href'] = Url::build('edit_page',array('id'=>$item['id']));
			}
		}
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
		foreach ($items as $key=>$value){
			$items[$key]['i']=$i++;
		}
		$just_edited_id['just_edited_ids'] = array();
		if (UrL::get('selected_ids')){
			if(is_string(UrL::get('selected_ids'))){
				if (strstr(UrL::get('selected_ids'),',')){
					$just_edited_id['just_edited_ids']=explode(',',UrL::get('selected_ids'));
				}
				else{
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