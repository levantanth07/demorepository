<?php
class PortalCategory extends Module{
	function __construct($row){
		Module::Module($row);
		require_once 'db.php';
		$arr = array('news_category'=>'NEWS','product_category'=>'PRODUCT','photo_category'=>'PHOTO','video_category'=>'VIDEO','portal_category'=>'ALL');
		if(isset($arr[Url::get('page')]) and Url::get('page')!='portal_category'){
			$_REQUEST['type'] = $arr[Url::get('page')];
		}elseif(Url::get('page')!='portal_category'){
			$_REQUEST['type'] = 'NEWS';
		}elseif(Url::get('page')=='menu'){
			$_REQUEST['type'] = '';
		}
		$this->redirect_parameters = array('type');
		if(User::can_view(false,ANY_CATEGORY)){
			switch(URL::get('cmd')){
			case 'update_status':
				if(Url::get('status')){
					if(is_array(URL::get('selected_ids')) and sizeof(URL::get('selected_ids'))>0){
						$ids = URL::get('selected_ids');
						foreach($ids as $key=>$val){
							$this->update_status(Url::get('status'),$val);	
						}
						$this->export_cache();
					}	
				}
				//exit();
				Url::redirect_current();
				break;
			case 'convert':
				$this->convert();
				exit();
				break;
			case 'get_name_id':
				require_once 'packages/core/includes/utils/vn_code.php';
				$name = trim(Url::get('name'));
				$name_id = convert_utf8_to_url_rewrite($name);
				echo $name_id;
				exit();
				break;
			case 'export_cache':
				$this->export_cache();
				break;
			case 'delete':
				$this->delete_cmd();
				break;
			case 'edit':
				$this->edit_cmd();
				break;
			case 'unlink':
				$this->delete_file();
			case 'add':
				$this->add_cmd();
				break;
			case 'view':
				$this->view_cmd();
				break;
			case 'move_up':
			case 'move_down':
				$this->move_cmd();
				break;
			default:
				$this->list_cmd();
				break;
			}
		}else{
			URL::access_denied();
		}
	}
	function update_status($status,$id){
		DB::update('category',array('status'=>$status),'id='.$id);
	}
	function export_cache(){
		if(User::can_view(false,ANY_CATEGORY)){
			$languages = DB::fetch_all('select id,name,icon_url from language where active=1');
			foreach($languages as $key=>$val){	
				$this->export($key);
			}
			Url::redirect_current();
		}
	}
	// tao cache file voi category va zone
	function export($language_id=1){
		require_once 'packages/core/includes/utils/category.php';
		$categogies_ = $categogies = PortalCategoryDB::get_categories($language_id,ID_ROOT,'and category.status="MENU"');
		$categogies = convert_item_cat_to_ul($categogies,true,$language_id);
		$path = 'cache/tables/menu_'.$language_id.'.cache.php';
		$hand = fopen($path,'w+');
		fwrite($hand,'<?php $categogies = '.var_export($categogies,true).';?>');
		fclose($hand);
	}
	function delete_file(){
		if(Url::get('link') and file_exists(Url::get('link')) and User::can_delete(false,ANY_CATEGORY)){
			@unlink(Url::get('link'));
		}
		echo '<script>window.close();</script>';
	}
	function add_cmd(){
		if(User::can_add(false,ANY_CATEGORY)){
			require_once 'forms/edit.php';
			$this->add_form(new EditPortalCategoryForm());
		}else{
			Url::redirect_current();
		}
	}
	function delete_cmd(){
		if(is_array(URL::get('selected_ids')) and sizeof(URL::get('selected_ids'))>0 and User::can_delete(false,ANY_CATEGORY)){
			if(sizeof(URL::get('selected_ids'))>1){
				require_once 'forms/list.php';
				$this->add_form(new ListPortalCategoryForm());
			}else{
				$ids = URL::get('selected_ids');
				$_REQUEST['id'] = $ids[0];
				require_once 'forms/detail.php';
				$this->add_form(new PortalCategoryForm());
			}
		}
		else
		if(User::can_delete(false,ANY_CATEGORY) and Url::check('id') and DB::exists_id('category',$_REQUEST['id'])){
			require_once 'forms/detail.php';
			$this->add_form(new PortalCategoryForm());
		}else{
			Url::redirect_current();
		}
	}
	function edit_cmd(){
		// and User::can_edit(false,$category['structure_id'])
		if(Url::get('id') and $category=DB::fetch('select id,structure_id from category where id='.intval(Url::get('id')))){
			require_once 'forms/edit.php';
			$this->add_form(new EditPortalCategoryForm());
		}else{
			Url::redirect_current();
		}
	}
	function list_cmd(){
		require_once 'forms/list.php';
		$this->add_form(new ListPortalCategoryForm());
	}
	function view_cmd(){
		if(User::can_view_detail(false,ANY_CATEGORY) and Url::check('id') and DB::exists_id('category',$_REQUEST['id'])){
			require_once 'forms/detail.php';
			$this->add_form(new PortalCategoryForm());
		}else{
			Url::redirect_current();
		}
	}
	function move_cmd(){
		if(User::can_edit(false,ANY_CATEGORY)and Url::check('id')and $category=DB::exists_id('category',$_REQUEST['id'])){
			if($category['structure_id']!=ID_ROOT){
				require_once 'packages/core/includes/system/si_database.php';
				si_move_position('category',' and portal_id="'.PORTAL_ID.'"');
			}
			Url::redirect_current();
		}else{
			Url::redirect_current();
		}
	}
	function convert($parent_id=false){
		//echo ID_ROOT;die;
		mysql_query("SET NAMES UTF8");
		//header("Content-Type: text/html; charset=utf-8");
		require_once 'packages/core/includes/system/si_database.php';
		require_once 'packages/core/includes/utils/vn_code.php';
		$sql = '
			select
				ocd.category_id as id,ocd.*,oc.*
			from
				oc_category_description as ocd
				inner join oc_category AS oc ON oc.category_id = ocd.category_id
			where
				'.($parent_id?'parent_id='.$parent_id.'':'1=1').'
			order by
				oc.parent_id, oc.sort_order
		';
		$old_categories = DB::fetch_all($sql);
		foreach($old_categories as $key=>$val){
			$utf8 = $val['name']; // file must be UTF-8 encoded
			$name = utf8_encode($utf8);
			$new_row = array('id'=>$key,'name_1'=>$name,'type'=>'PRODUCT','status'=>'SHOW','portal_id'=>PORTAL_ID);
			$name_id = convert_utf8_to_url_rewrite($val['name']);
			$same = false;
			$new_row+=array('name_id'=>$name_id);
			$new_row['time'] = time();
			if($val['parent_id']){
				if(!DB::exists('select id from category where id='.$key.'')){
					$this->id = DB::insert('category', $new_row+array('structure_id'=>si_child('category',structure_id('category',$val['parent_id']),'')));
				}
			}
			else{
				if(!DB::exists('select id from category where id='.$key.'')){	
					$this->id = DB::insert('category', $new_row+array('structure_id'=>si_child('category',ID_ROOT,'')));
				}
			}
			if($old = DB::fetch('select id,name_id from category where name_id="'.$name_id.'" and type="PRODUCT"')){
				$same = true;
			}
			if($same){
				DB::update_id('category',array('name_id'=>$name_id.'_'.$this->id),$this->id);
			}
			$this->convert($key);
		}
	}
}
?>