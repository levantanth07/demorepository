<?php

require_once ROOT_PATH . 'packages/core/includes/common/SystemsTree.php';

class EditGroupsSystemForm extends Form{
	function __construct(){
		Form::Form('EditGroupsSystemForm');
		if(URL::get('cmd')=='edit'){
			$this->add('id',new IDType(true,'object_not_exists','groups_system'));
		}
		//$this->add('parent_id',new IDType(true,'invalid_category','groups_system'));
		$this->add('name',new TextType(true,'invalid_name',0,255));
		if(Url::get('name_id')){
			$this->add('name_id',new UniqueType('duplicated','groups_system','name_id'));
		}
		$this->add('description',new TextType(false,'invalid_description',0,200000));
		$this->link_js('assets/standard/js/multiple.select.js');
		$this->link_css('assets/standard/css/multiple-select.css');
		$this->link_css('assets/default/css/cms.css');
	}
	function on_submit(){
		require_once 'packages/core/includes/utils/upload_file.php';
		update_upload_file('icon_url',str_replace('#','',PORTAL_ID).'/category');
		update_upload_file('image_url',str_replace('#','',PORTAL_ID).'/category');
		if($this->check() and URL::get('confirm_edit')){
			if(URL::get('cmd')=='edit'){
				$this->old_value = DB::select('groups_system','id="'.addslashes($_REQUEST['id']).'"');
				if(Url::get('delete_icon_url')=='0'){
					@unlink($this->old_value['icon_url']);
					DB::update_id('groups_system',array('icon_url'=>''),$_REQUEST['id']);
				}
				if(Url::get('delete_image_url')=='0'){
					@unlink($this->old_value['image_url']);
					DB::update_id('groups_system',array('image_url'=>''),$_REQUEST['id']);
				}
			}
			$this->save_item();
			//exit();
			if(!$this->is_error()){
				Url::js_redirect(true,'Dữ liệu đã được cập nhật',array('cmd','id'));
			}
		}
	}
	function draw(){
		$languages = DB::fetch_all('select id,name,icon_url from language where active=1');
		$this->init_edit_mode();
		$this->get_parents();
		$categories = GroupsSystemDB::check_categories($this->parents);
		require_once('packages/core/includes/utils/category.php');
		combobox_indent($categories);
		$parent_id = (($this->edit_mode?si_parent_id('groups_system',$this->init_value['structure_id'],''):""));
		$_REQUEST['parent_id'] = $parent_id;
		$role_options = '<option value="">Chọn vai trò</option><option value="1">Chủ tịch</option><option value="2">Phó chủ tịch</option>';
		$permissions_options = '';
		if (GroupsSystem::$permissions) {
			foreach (GroupsSystem::$permissions as $key => $value) {
				$permissions_options .= '<option value="'.$key.'">'.$value.'</option>';
			}
		}

		$ID = URL::get('id') ? URL::get('id') : 1;
		$selectBox = SystemsTree::selectBox(
			null, 
			[	
				'selected' => $ID, 
				'selectedType' => SystemsTree::SELECTED_PARENT,
				'props' => [
					'name' =>"parent_id",
					'id' =>"parent_id",
					'class' =>"js-example-placeholder-single js-states form-control"
				]
			]);

		$this->parse_layout('edit',
			($this->edit_mode?$this->init_value:array())+
			array(
			'languages'=>$languages,
			'selectbox'=> $selectBox,
			'parent_id'=>$parent_id,
			'role_options'=>$role_options,
			'permissions_options'=>$permissions_options,
			'permissions'=>json_encode(array_keys(GroupsSystem::$permissions)),
			'groups'=>isset($this->init_value['id'])?GroupsSystemDB::get_groups($this->init_value['id']):array(),
			// 'ranks' => json_encode($this->get_rank($this->init_value['id'], get_user_id()))
			)
		);
	}
	
	// Lấy ra danh sách các rank được thiết lập cho group cha, 
    // kết quả được sắp xếp theo revenue_min 
    static function get_rank($system_group_id, $user_id){
        $sql = '
                SELECT 
                    groups_system_rank.id,groups_system_rank.revenue_min,groups_system_rank.rank_name
                FROM 
                    groups_system_rank
                    INNER JOIN users ON users.id=groups_system_rank.user_id
                    INNER JOIN groups_system ON groups_system_rank.group_system_id = groups_system.id
                WHERE
                    groups_system_rank.group_system_id = '.$system_group_id.' AND
                    users.id = '.$user_id.'
                ORDER BY revenue_min DESC 
            ';

            return DB::fetch_all($sql);
    }

	function save_item(){
			$extra = array();
			$extra=$extra+array('name'=>Url::get('name'));
			$extra=$extra+array('description'=>Url::get('description'));
			$new_row = $extra;
			if(Url::get('icon_url')!=''){
				$new_row['icon_url'] =Url::get('icon_url');
			}
			if(Url::get('image_url')!=''){
				$new_row['image_url'] =Url::get('image_url');
			}
			require_once 'packages/core/includes/utils/vn_code.php';
			if(Url::get('cmd') == 'edit'){
				$name_id =trim(Url::get('name_id'));
				$new_row+=array('name_id'=>$name_id);
				$same = false;
			}else{
				$name_id = convert_utf8_to_url_rewrite($new_row['name']);
				$new_row+=array('name_id'=>$name_id);
				$same = false;
			}
			if($old = DB::fetch('select id,name_id'.' from `groups_system` where name_id="'.$name_id.'"')){
				$same = true;
				$old_id = $old['id'];
			}
		if(!$this->is_error()){
			if(URL::get('cmd')=='edit'){
				$this->id = $_REQUEST['id'];
				$new_row['last_time_update'] = time();
				if($same and $old_id!=$this->id){
					$new_row['name_id'] .= '_'.$this->id;
				}
				//System::debug($new_row); exit();
				DB::update_id('groups_system', $new_row,$this->id);
				if($this->old_value['structure_id']!=ID_ROOT){
					if (Url::iget('parent_id')){
						$parent = DB::select('groups_system','id='.Url::iget('parent_id').'');
						if($parent['id']!=$this->old_value['id']){
							try{
								Systems::moveSystem($this->old_value['id'], $parent['id']);
							}catch(Throwable $e){
								$this->error('move_system', "Đã có lỗi xảy ra. Vui lòng thử lại sau.", false);

							}
						}
					}
				}
			}else{
				require_once 'packages/core/includes/system/si_database.php';
				$new_row['time'] = time();
				if(isset($_REQUEST['parent_id'])){
					$new_row['structure_id'] = Systems::getAvailbleNextIDStructure(structure_id('groups_system',$_REQUEST['parent_id']));
			        if($new_row['structure_id'] === false){
			            return $this->error('add', 'Hệ thống chuyển đến đã đầy !', false);
			        }
					$this->id = DB::insert('groups_system', $new_row);
				}else{
					$this->id = DB::insert('groups_system', $new_row+array('structure_id'=>ID_ROOT));
				}
				if($same){
					DB::update_id('groups_system',array('name_id'=>$new_row['name_id'].'_'.$this->id),$this->id);
				}
			}
			$this->update_user_id($this->id);
			save_log($this->id);
		}
	}
	function get_user_id($system_group_id){
		$sql = '
				select 
					groups_system_account.id,
					groups_system_account.user_id,
					groups_system_account.role,
					groups_system_account.permissions,
					users.username as account_id
				from 
					groups_system_account
					INNER JOIN users ON users.id=groups_system_account.user_id
				WHERE
					groups_system_account.system_group_id = '.$system_group_id.'
				order by 
					groups_system_account.id
			';
			$mi_account = DB::fetch_all($sql);
			$_REQUEST['mi_account'] = $mi_account;
	}
	function update_user_id($system_group_id){
		if(isset($_REQUEST['mi_account'])){
			foreach($_REQUEST['mi_account'] as $key=>$record){
				if($record['id']=='(auto)'){
					$record['id']=0;
				}
				unset($record['account_id']);
				$record['permissions'] = implode(',', $record['permissions']);
				if($record['id'] and DB::exists_id('groups_system_account',$record['id'])){
					DB::update('groups_system_account',$record,'id='.$record['id']);
				}else{
					unset($record['id']);
					$record['system_group_id'] = $system_group_id;
					$record['id'] = DB::insert('groups_system_account',$record);
				}
				/////
			}
			if (isset($ids) and sizeof($ids)){
				$_REQUEST['selected_ids'].=','.join(',',$ids);
			}
		}
		if(URL::get('deleted_ids')){
			$ids = explode(',',URL::get('deleted_ids'));
			foreach($ids as $id){
				DB::delete_id('groups_system_account',$id);
			}
		}
	}
	function init_edit_mode(){
		if(URL::get('cmd')=='edit' and $this->init_value=DB::select('groups_system','id='.intval(URL::sget('id')).'')){
			$this->get_user_id($this->init_value['id']);
			foreach($this->init_value as $key=>$value){
				if(!isset($_REQUEST[$key])){
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
		$sql = '
			select
				id,
				structure_id
				,name
			from
			 	`groups_system`
			where
				1=1
			order by
				structure_id
		';
		$this->parents = DB::fetch_all($sql);
	}
}
?>
