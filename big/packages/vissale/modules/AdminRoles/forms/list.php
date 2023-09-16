<?php
class ListAdminRolesForm extends Form{
	function __construct(){
		Form::Form('ListAdminRolesForm');
	}
	function delete(){
		if(isset($_REQUEST['selected_ids']) and count($_REQUEST['selected_ids'])>0){
			foreach($_REQUEST['selected_ids'] as $key){
				if($item = DB::select('roles','id='.DB::escape($key).' and group_id='.Session::get('group_id'))){
					DB::delete('roles_statuses','role_id='.intval($key));
					DB::delete('users_roles','role_id='.intval($key));
					DB::delete_id('roles',intval($key));
				}
			}
		}
		Url::js_redirect(true,'Bạn đã xoá thành công');
	}
	function on_submit(){
		switch(Url::get('cmd')){
			case 'delete':
				$this->delete();
				break;
		}
	}
	function draw(){
		$cond = 'roles.group_id='.Session::get('group_id').'';
		require_once 'packages/core/includes/utils/paging.php';
		$item_per_page = 20;//Url::get('item_per_page',50);
		$total = AdminRolesDB::get_total_item($cond);
		$paging = paging($total,$item_per_page,10,false,'page_no');
		$items = AdminRolesDB::get_items($cond,$item_per_page);
		$this->parse_layout('list',array(
			'items'=>$items,
			'paging'=>$paging,
			'total'=>$total
		));
	}
	function get_just_edited_id(){
		$this->just_edited_id['just_edited_ids'] = array();
		if (UrL::get('selected_ids')){
			if(is_string(UrL::get('selected_ids'))){
				if (strstr(UrL::get('selected_ids'),',')){
					$this->just_edited_id['just_edited_ids']=explode(',',UrL::get('selected_ids'));
				}
				else{
					$this->just_edited_id['just_edited_ids']=array('0'=>UrL::get('selected_ids'));
				}
			}
		}
	}
}
?>
