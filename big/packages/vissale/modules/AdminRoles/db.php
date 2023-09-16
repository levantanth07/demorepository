<?php
class AdminRolesDB{
    static function get_total_item($cond){
        return DB::fetch(
            'select
				count(*) as acount
			from
				roles
			where
				'.$cond.'
				'
            ,'acount');
    }
    static function get_items($cond,$item_per_page){
        $sql = '
            SELECT
              roles.*
            FROM
              roles
            WHERE
              '.$cond.'
            ORDER BY
              roles.name
            LIMIT
                '.((page_no()-1)*$item_per_page).','.$item_per_page.'
        ';
        $items = DB::fetch_all($sql);
        foreach($items as $k => $v){
          $items[$k]['roles']=rtrim(AdminRolesDB::get_roles($cond.' and roles_to_privilege.role_id="'.$v['id'].'" ',true),', ');
          $items[$k]['role_status']=AdminRolesDB::get_status($v['id'],true);
        }
        return $items;
    }
    static function get_roles($cond,$str = false){
      $sql = '
            SELECT
              roles_to_privilege.id
              ,roles.group_id
              ,roles_to_privilege.privilege_code
              ,roles_to_privilege.role_id
              ,roles_activities.name as role_name
            FROM
              roles_to_privilege
              LEFT JOIN roles ON roles_to_privilege.role_id = roles.id
              INNER JOIN roles_activities ON roles_activities.code = roles_to_privilege.privilege_code
            WHERE
              '.$cond.'
            ORDER BY
              roles_to_privilege.id
        ';
        $items = DB::fetch_all($sql);
        // $items = [];
        // foreach ($results as $key => $value) {
        //   if (!in_array($value['privilege_code'], ['BUNGDON_NHOM','BUNGDON','BUNGDON2'])) {
        //       $items[$key] = $value;
        //   }
        // }
        if($str==false){
          return $items;
        }
        else{
          $str = '';
          foreach($items as $key => $val){
            $str.=$val['role_name'].', ';
          }
          return $str;
        }
    }
    static function get_statuses($arr = false){
      $group_id = Session::get('group_id');
      $master_group_id = Session::get('master_group_id');
      if(Session::get('account_type')==3){//khoand edited in 30/09/2018
        $cond = ' (groups.id='.$group_id.')';
      }elseif($master_group_id){
        $cond = ' (groups.id = '.$master_group_id.')';
      }else{
        $cond = ' groups.id='.$group_id.'';
      }
      $sql = '
        SELECT 
          statuses.id,
          CONCAT(statuses.level,".",statuses.name) AS name,
          statuses.color,
          0 as can_view,
          0 as can_edit
        FROM 
          `statuses`
          inner join `groups` on groups.id = statuses.group_id
        WHERE 
          '.$cond.' OR is_system=1
        ORDER BY
          statuses.level
        ';
      $items = DB::fetch_all($sql);
      if($arr == false){
        return $items;
      }
      else{
        foreach ($items as $key => $value) {
            $items[$key]['can_view'] = 0;
            $items[$key]['can_edit'] = 0;
            if(isset($arr[$key])){
                $items[$key]['can_view']=1;
                if(isset($arr[$key]['can_edit']) and $arr[$key]['can_edit']==1){
                    $items[$key]['can_edit']=1;
                }
            }
        }
        return $items;
      }
        
    }
    static function get_status($role_id,$str = false){
        $sql = '
            SELECT
              statuses.id
              ,statuses.name
              ,statuses.color
              ,roles_statuses.role_id
              ,roles_statuses.can_edit
            FROM
              statuses
              INNER JOIN roles_statuses ON statuses.id = roles_statuses.status_id
            WHERE
              role_id ='.DB::escape($role_id).'
            ORDER BY
              statuses.name
        ';
        $items = DB::fetch_all($sql);
        if($str == false){
          return $items;
        }
        else{
          $str = '';
          foreach($items as $key => $val){
            $str.='<div style="margin-right:5px;margin-bottom:2px;border:1px solid #ccc;display:inline-block;padding:2px 3px;" ><span style="display:inline-block;width:10px;height:10px;border-radius:3px;background-color:'.$val['color'].'"></span> '.$val['name'].' '.($val['can_edit']?' <span style="font-size:10px;padding:2px;background-color:#7fffd4;border:1px solid #CCC;">Xem + Sửa</span>':'<span style="border:1px solid #CCC;font-size:10px;padding:2px;background:#faebd7">Xem</span>').'</div>';
          }
          return $str;
        }
    }

}
?>
