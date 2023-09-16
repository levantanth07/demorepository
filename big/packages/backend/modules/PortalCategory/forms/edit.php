<?php
class EditPortalCategoryForm extends Form{
	function __construct(){
		Form::Form('EditPortalCategoryForm');
		if(URL::get('cmd')=='edit'){
			$this->add('id',new IDType(true,'object_not_exists','category'));
		}
		$this->add('type',new IDType(true,'invalid_type','type'));
		//$this->add('parent_id',new IDType(true,'invalid_category','category'));
		$languages = DB::fetch_all('select id,name,icon_url from language where active=1');
		foreach($languages as $key=>$val){
		$this->add('name_'.$key,new TextType(true,'invalid_name_'.$key,0,255));
		if(Url::get('name_id_'.$key)){
			$this->add('name_id_'.$key,new UniqueType('duplicated_'.$val['name'].'','category','name_id_'.$key));
		}
		$this->add('description_'.$key,new TextType(false,'invalid_description_'.$key,0,200000));
		}
		$this->link_css('assets/default/css/cms.css');
	}
	function on_submit(){
		require_once 'packages/core/includes/utils/upload_file.php';
		update_upload_file('icon_url',str_replace('#','',PORTAL_ID).'/category');
		update_upload_file('image_url',str_replace('#','',PORTAL_ID).'/category');
		if($this->check() and URL::get('confirm_edit')){
			if(URL::get('cmd')=='edit'){
				$this->old_value = DB::select('category','id="'.addslashes($_REQUEST['id']).'"');
				if(Url::get('delete_icon_url')=='0'){
					@unlink($this->old_value['icon_url']);
					DB::update_id('category',array('icon_url'=>''),$_REQUEST['id']);
				}
				if(Url::get('delete_image_url')=='0'){
					@unlink($this->old_value['image_url']);
					DB::update_id('category',array('image_url'=>''),$_REQUEST['id']);
				}
			}
			$this->save_item();
			//exit();
			if(!$this->is_error()){
				Url::redirect_current(Module::$current->redirect_parameters+array('just_edited_id'=>$this->id));
			}
		}
	}
	function draw(){
		$languages = DB::fetch_all('select id,name,icon_url from language where active=1');
		$this->init_edit_mode();
		$this->get_parents();
		$this->init_database_field_select();
		require_once 'cache/config/status.php';
		if(Url::get('page') == 'menu'){
			$status = array('MENU'=>'Menu');
			$_REQUEST['status'] = 'MENU';
		}
		$categories = PortalCategoryDB::check_categories($this->parents);
		require_once('packages/core/includes/utils/category.php');
		combobox_indent($categories);
		$parent_id = (($this->edit_mode?si_parent_id('category',$this->init_value['structure_id'],' and portal_id="'.PORTAL_ID.'" '.(Url::get('type')?' and type="'.Url::get('type').'"':'').''):""));
		if($parent_id){
            $_REQUEST['parent_id'] = $parent_id;
        }
		$this->parse_layout('edit',
			($this->edit_mode?$this->init_value:array())+
			array(
			'languages'=>$languages,
			'parent_id_list'=>MiString::get_list($categories),
			'parent_id'=>$parent_id,
			'type_list'=>$this->type_list,
			'status_list'=>$status
			)
		);
	}
	function save_item(){
			$extra = array();
			$languages = DB::fetch_all('select id,name,icon_url from language where active=1');
			foreach($languages as $language){
				$extra=$extra+array('name_'.$language['id']=>Url::get('name_'.$language['id'],1));
				$extra=$extra+array('show_home'=>Url::get('show_home')?Url::get('show_home'):'0');
				$extra=$extra+array('brief_'.$language['id']=>Url::get('brief_'.$language['id'],1));
				$extra=$extra+array('description_'.$language['id']=>Url::get('description_'.$language['id'],1));
			}
			$new_row = $extra+
			array('type','status', 'url', 'portal_id'=>PORTAL_ID);
			if(Url::get('icon_url')!=''){
				$new_row['icon_url'] =Url::get('icon_url');
			}
			if(Url::get('image_url')!=''){
				$new_row['image_url'] =Url::get('image_url');
			}
			require_once 'packages/core/includes/utils/vn_code.php';
			if(Url::get('cmd') == 'edit'){
				foreach($languages as $language){
					$name_id =trim(Url::get('name_id_'.$language['id']));
					$new_row+=array('name_id_'.$language['id']=>$name_id);
				}
				$same = false;
			}else{
				foreach($languages as $language){
					$name_id = convert_utf8_to_url_rewrite($new_row['name_'.$language['id']]);
					$new_row+=array('name_id_'.$language['id']=>$name_id?$name_id:'');
				}
				$same = false;
			}
			foreach($languages as $language){
				if($old = DB::fetch('select id,name_id_'.$language['id'].' from category where name_id_1="'.$name_id.'" and type="'.Url::get('type').'" and portal_id="'.PORTAL_ID.'"')){
					$same = true;
					$old_id = $old['id'];
				}
			}
		if(!$this->is_error()){
			if(URL::get('cmd')=='edit'){
				$this->id = $_REQUEST['id'];
				$new_row['last_time_update'] = time();
				if($same and $old_id!=$this->id){
					$new_row['name_id_1'] .= '_'.$this->id;
				}
				//System::debug($new_row); exit();
				DB::update_id('category', $new_row,$this->id);
				if($this->old_value['structure_id']!=ID_ROOT){
					if (Url::iget('parent_id')){
						$parent = DB::select('category','id='.Url::iget('parent_id').'');
						if($parent['structure_id']==$this->old_value['structure_id']){
							//$this->error('id','invalid_parent');
						}else{
							require_once 'packages/core/includes/system/si_database.php';
							$extra_cond = Url::get('type')?' and type="'.Url::get('type').'"':'';
							if(!si_move('category',$this->old_value['structure_id'],$parent['structure_id'],$extra_cond)){
								$this->error('id','invalid_parent');
							}
						}
					}
				}
			}else{
				require_once 'packages/core/includes/system/si_database.php';
				$new_row['time'] = time();
				if(isset($_REQUEST['parent_id'])){
					$this->id = DB::insert('category', $new_row+array('structure_id'=>si_child('category',structure_id('category',$_REQUEST['parent_id']),' and portal_id="'.PORTAL_ID.'"')));
				}else{
					$this->id = DB::insert('category', $new_row+array('structure_id'=>ID_ROOT));
				}
				if($same){
					DB::update_id('category',array('name_id_1'=>$new_row['name_id_1'].'_'.$this->id),$this->id);
				}
			}
			save_log($this->id);
		}
	}
	function init_edit_mode(){
		if(URL::get('cmd')=='edit' and $this->init_value=DB::select('category','id='.intval(URL::sget('id')).'')){
			foreach($this->init_value as $key=>$value){
				if(is_string($value) and !isset($_REQUEST[$key])){
					$_REQUEST[$key] = $value;
				}
			}
			$this->edit_mode = true;
		}else{
			$this->edit_mode = false;
		}
	}
	function get_parents(){
		require_once 'packages/core/includes/system/si_database.php';
		if(Url::get('type') == 'ALL' or !Url::get('type')){
			$extra_cond = '';
		}else{
			$extra_cond = ' and (category.type="'.Url::get('type').'")';
		}
		$sql = '
			select
				id,
				structure_id
				,name_'.Portal::language().' as name
			from
			 	`category`
			where
				portal_id="'.PORTAL_ID.'"'.$extra_cond.'
			order by
				structure_id
		';
		$this->parents = DB::fetch_all($sql);
	}
	function init_database_field_select(){
		if(Url::get('type') == 'ALL'){
			$extra_cond = ' and (type.id!="")';
		}else{
			$extra_cond = ($type=Url::get('type'))?' and (type.id="'.Url::get('type').'")':'';
		}
		if($types = DB::fetch_all('select
					`type`.id,
					`type`.`title_'.Portal::language().'` as name
				from
					`type`
				where
					portal_id="'.PORTAL_ID.'"'.$extra_cond
			)){
				$this->type_list = MiString::get_list($types);
			}else{
				$sql = 'select
					`type`.id,
					`type`.`title_'.Portal::language().'` as name
				from
					`type`
				where
					portal_id=""'.$extra_cond;
				$this->type_list = MiString::get_list(DB::fetch_all($sql));
			}
	}
}
?>