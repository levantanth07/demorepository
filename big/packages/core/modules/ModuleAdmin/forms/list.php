<?php
class ListModuleAdminForm extends Form{
	function __construct(){
		Form::Form('ListModuleAdminForm');
	}
	function on_submit(){
		if(URL::get('confirm')){
			foreach(URL::get('selected_ids') as $id){
			}
			require_once 'detail.php';
			foreach(URL::get('selected_ids') as $id){
				ModuleAdminForm::delete($this,$id);
				if($this->is_error()){
					return;
				}
			}
			Url::redirect_current(array('package_id'=>isset($_GET['package_id'])?$_GET['package_id']:'',
	'name'=>isset($_GET['name'])?$_GET['name']:'',
	  ));
		}
	}
	function draw(){
		$languages = DB::select_all('language');
		$cond = ' 1 '
			.(!URL::get('type')?' and (module.type="" or module.type="SERVICE")':' and module.type="'.URL::get('type').'"')
			.(URL::get('package_id')?'
					and '.IDStructure::child_cond(DB::fetch('select structure_id from `package` where id="'. URL::get('package_id',1).'"','structure_id'),false,'package.').' ':'')
			.(URL::get('name')?' and `module`.`name` LIKE "%'.URL::get('name').'%"':'')
			.((URL::get('cmd')=='delete' and is_array(URL::get('selected_ids')))?' and `module`.id in ('.join(URL::get('selected_ids'),',').')':'')
		;
		$item_per_page = Module::$current->get_setting('item_per_page',50);
		DB::query('
			select count(*) as acount
			from
				`module`
				left outer join `package` on `package`.id=`module`.`package_id`
			where '.$cond.'
			'.(URL::get('order_by')?'order by '.URL::get('order_by').(URL::get('order_dir')?' '.URL::get('order_dir'):''):'').'
			limit 0,1
		');
		$count = DB::fetch();
		require_once 'packages/core/includes/utils/paging.php';
		$paging = paging($count['acount'],$item_per_page);
		DB::query('
			select
				`module`.id
				,`module`.`name` ,`module`.`use_dblclick`
				,`module`.title_'.Portal::language().' as title ,`module`.description_'.Portal::language().' as description 				,`package`.`name` as package_id
			from
			 	`module`
				left outer join `package` on `package`.id=`module`.`package_id`
			where 
				'.$cond.'
			'.(URL::get('order_by')?'order by '.URL::get('order_by').(URL::get('order_dir')?' '.URL::get('order_dir'):''):' order by module.id desc').'
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
		foreach ($items as $key=>$value){
			$items[$key]['i']=$i++;
			if (Url::check('page_id')){
				$items[$key]['href']=Url::build('edit_page',array('module_id'=>$value['id'],'id'=>$_REQUEST['page_id'],'region','after','replace','href','container_id'));
			}else{
				$items[$key]['href']=Url::build_current(array('cmd'=>'edit','package_id','name','id'=>$value['id']));
			}
			$items[$key]['page_name']=DB::fetch('select page.name from block inner join page on page.id=block.page_id where module_id="'.$items[$key]['id'].'"','name');
		}
		$just_edited_id['just_edited_ids'] = array();
		if (UrL::get('selected_ids')){
			if(is_string(UrL::get('selected_ids'))){
				if (strstr(UrL::get('selected_ids'),',')){
					$just_edited_id['just_edited_ids']=explode(',',UrL::get('selected_ids'));
				}else{
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