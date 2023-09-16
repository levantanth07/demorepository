<?php
class EditAdminAccountGroupForm extends Form{
	function __construct(){
		Form::Form('EditAdminAccountGroupForm');
		$this->add('account_group.name',new TextType(true,'Chưa nhập tên',0,255));
        $this->link_js('assets/standard/js/multiple.select.js');
        $this->link_css('assets/standard/css/multiple-select.css');
	}
	function on_submit(){
		if($this->check() and URL::get('confirm_edit') and !Url::get('search')){
			require_once 'packages/core/includes/utils/vn_code.php';
			require_once "packages/vissale/modules/AdminOrders/db.php";
			$users = AdminOrdersDB::get_users(false,false,true);

			if(isset($_REQUEST['mi_account_group'])){
				foreach($_REQUEST['mi_account_group'] as $key=>$record){
					// Validate User
					if(!isset($users[$record['admin_user_id']])){
						$record['admin_user_id'] = 0;
					}

					$record['group_id'] = Session::get('group_id');
					if($record['id']=='(auto)'){
						$record['id']=false;
					}
                    $record['admin_user_id'] = intval($record['admin_user_id']);
					if($record['id']){
						DB::update('account_group',$record,'id='.$record['id']);
					}else{
						unset($record['id']);
						$record['id'] = DB::insert('account_group',$record);
					}
					/////
				}
				if (isset($ids) and sizeof($ids)){
					$_REQUEST['selected_ids'].=','.join(',',$ids);
				}
			}
			if(URL::get('deleted_ids')){
				$ids = URL::getSafeIDs('deleted_ids');
				foreach($ids as $id){
					DB::delete('account_group', 'id=' . $id . ' AND group_id = ' . Session::get('group_id'));
				}
			}
			//update_mi_upload_file();
			Url::js_redirect(true);
		}
	}	
	function draw(){
        if(Url::get('page')=='admin_account_group1'){
            echo 'Quản lý nhóm';
            ini_set('display_errors',1);
        }
		$this->map = array();
		$paging = '';
		$cond = '
			account_group.group_id = '.Session::get('group_id').'
				'.(Url::get('keyword')?' AND account_group.name LIKE "%'. DB::escape(Url::get('keyword')).'%"':'').'
			';		
		//if(!isset($_REQUEST['mi_account_group']))
		{
			$item_per_page = 200;
			DB::query('
				select 
					count(distinct account_group.id) as acount
				from 
					account_group
				where 
					'.$cond.'
			');
			$count = DB::fetch();
			$this->map['total'] = $count['acount'];			
			require_once 'packages/core/includes/utils/paging.php';
			$paging = paging($count['acount'],$item_per_page);
			$sql = '
				select 
					account_group.*
				from 
					account_group
				WHERE
					'.$cond.'
				GROUP BY
					account_group.id
				order by 
					account_group.id  DESC
				LIMIT
					'.((page_no()-1)*$item_per_page).','.$item_per_page.'
			';
			$mi_account_group = DB::fetch_all($sql);
			$_REQUEST['mi_account_group'] = $mi_account_group;
		}
		$this->map['paging'] = $paging;
	    require_once "packages/vissale/modules/AdminOrders/db.php";
	    $admin_user_ids_options = '<option value="">Chọn tài khoản</option><option value="0">->Không chọn</option>';
	    $users = AdminOrdersDB::get_users(false,false,true);
	    foreach($users as $key=>$val){
            $admin_user_ids_options .= '<option value="'.$key.'">'.$val['full_name'].'</option>';
        }
        $this->map['admin_user_ids_options'] = $admin_user_ids_options;
		$this->parse_layout('edit',$this->map);
	}
}
?>