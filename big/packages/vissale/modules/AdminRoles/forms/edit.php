<?php
class EditAdminRolesForm extends Form{
	function __construct(){
		Form::Form('EditAdminRolesForm');
		$this->add('name',new TextType(true,'Chưa nhập tên',0,255));
	}
	function on_submit(){
		if($this->check()  and !Url::get('search')){
			require_once 'packages/core/includes/utils/vn_code.php';
			$rows = array('name');

			$id = Url::getUInt('id');
			$item = DB::fetch('SELECT * FROM roles WHERE group_id = ' . Session::get('group_id') . ' AND id = ' . $id);
			if(Url::get('cmd')=='edit' && $item){
				$rows += array('modified'=>date('Y-m-d H:i:s'));
				DB::update_id('roles',$rows,$id);
			}else{
				$rows += array('created'=>date('Y-m-d H:i:s'),'group_id'=>Session::get('group_id'));
				$id = DB::insert('roles',$rows);
			}
			/////
			$roles_activities = DB::select_all('roles_activities',false,'position DESC');
			foreach ($roles_activities as $key => $value) {
				if(Url::get($value['code'])){
					if(!DB::exists('select id from roles_to_privilege where role_id='.$id.' AND privilege_code="'.$value['code'].'"')){
						DB::insert('roles_to_privilege',array('role_id'=>$id,'privilege_code'=>$value['code']));
					}
				}
				else{
					DB::delete('roles_to_privilege','role_id='.$id.' AND privilege_code="'.$value['code'].'"');
				}
			}
			$role_id = $id;
			if(isset($_REQUEST['status'])){
				//DB::delete('roles_statuses','role_id="'.$role_id.'" ');
				foreach($_REQUEST['status'] as $key=>$val){
                    $status_id = $key;
					$arr = array(
						'role_id' => $role_id,
						'status_id' => $status_id,
						'created' => date('Y-m-d H:i:s')
					);
					if($val==0){
                        DB::delete('roles_statuses','role_id="'.$role_id.'" and status_id='.$status_id);
                    }else if($val==2){
                        $arr['can_edit'] = 1;
                        if($row=DB::exists('select id from roles_statuses where role_id='.$role_id.' and status_id='.$status_id)){
                            DB::update('roles_statuses',$arr,'id='.$row['id']);
                        }else{
                            DB::insert('roles_statuses',$arr);
                        }
                    }else{
                        $arr['can_edit'] = 0;
                        if($row=DB::exists('select id from roles_statuses where role_id='.$role_id.' and status_id='.$status_id)){
                            DB::update('roles_statuses',$arr,'id='.$row['id']);
                        }else{
                            DB::insert('roles_statuses',$arr);
                        }
                    }
				}
			}
			Url::js_redirect(true);
		}
	}	
	function draw(){
		if(!DB::exists('select id from roles_activities where code="BUNGDON_NHOM"')){
			DB::query('INSERT INTO `roles_activities` (`code`, `name`, `position`) VALUES ("BUNGDON_NHOM", "Bung đơn nhóm", "4")');
		}
		$this->map = array();
		$roles_activities = DB::select_all('roles_activities',false,'position ASC');
		$roleActivities = [];
		foreach ($roles_activities as $key => $value) {
			if(!in_array($value['code'], $this->arrayQuyenGiamSat())){
				$roleActivities[$key] = $value;
			}
		}
		$this->map['roles_activities'] = $roleActivities;
		$news = DB::fetch('SELECT * FROM roles WHERE group_id = ' . Session::get('group_id') . ' AND id = ' . Url::getUInt('id'));
		if(Url::get('cmd')=='edit' && $news){
			foreach($news as $key=>$value){
				if(is_string($value) and !isset($_REQUEST[$key])){
					$_REQUEST[$key] = $value;
				}
			}
			$cond = ' roles.group_id='.Session::get('group_id').' and roles_to_privilege.role_id="'.$news['id'].'"';
			$roles = AdminRolesDB::get_roles($cond);
			$array = array();
			foreach ($roles as $key => $value) {
				$array[$key]=$value['privilege_code'];
			}
			$this->map['atv_privilege_code'] = $array;
			$arr = array();
			foreach (AdminRolesDB::get_status($news['id']) as $key => $value) {
				$arr[$key]['id']=$value['id'];
                $arr[$key]['can_edit']=$value['can_edit'];
			}
			$this->map['status']=AdminRolesDB::get_statuses($arr);
		}
		else{
			$this->map['status']=AdminRolesDB::get_statuses();
			$this->map['atv_privilege_code'] = array();
		}
		
		$this->parse_layout('edit',$this->map);
	}

	function arrayQuyenGiamSat()
	{
		return [
			'QUYEN_GIAM_SAT'
		];
	}
}
?>