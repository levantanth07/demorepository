<?php
class ModeratorDB{
	static function update_moderator($id, $user_id, $portal_id, $privilege_id, $category_id){
		if(!$category_id or DB::select('function','id="'.$category_id.'"')){
			if(!DB::fetch('select id,account_id from account_privilege where account_id="'.$user_id.'" and category_id="'.($category_id?$category_id:0).'" and portal_id="'.$portal_id.'" and privilege_id='.$privilege_id)){
				$row = array(
					'account_id'=>$user_id,
					'category_id'=>$category_id,
					'portal_id'=>$portal_id,
					'privilege_id'=>$privilege_id,
					'group_id'=>Session::get('group_id')
				);
				if($id and DB::select('account_privilege',$id)){
					DB::update('account_privilege',$row,'id="'.$id.'"');
				}else{
					DB::insert('account_privilege',$row);
				}
				DB::update('account',array('cache_privilege'=>''),'id="'.$user_id.'"');
			}
		}
	}
	static function have_privilege($privilege_id, $portal_id){
		if(User::is_admin()){
			return true;
		}
		if(!DB::select('account_privilege','account_id="'.Session::get('user_id').'" and portal_id="'.$portal_id.'"')){
			return $privilege_id==1;
		}
		return false;
	}
	static function get_portals(){
		if(User::is_admin()){
			return DB::fetch_all('
				select
					id,type
				from
					account
				where
					type="PORTAL"
				order by
					id
			');
		}
		else
		if(User::can_admin()){
			return array(PORTAL_ID=>array('id'=>PORTAL_ID));
		}else{
			return array();
		}
	}
	static function get_privileges(){
		if(User::is_admin()){
			return DB::fetch_all('
				select
					id,title_'.Portal::language().' as title
				from
					privilege
				order by
					id ASC
			');
		}
		else
		if(User::can_admin(false,ANY_CATEGORY)){
			return DB::fetch_all('
				select
					id,title_'.Portal::language().' as title
				from
					privilege
				where
					id in (1,2,3,9)');
		}else{
			return array();
		}
	}
	static function get_users(){
		return DB::fetch_all('
			SELECT
				account.id
			FROM
				account
			WHERE
				type="USER"
			ORDER
				 by id
		');
	}
	static function get_categories(){
		$portal_id=URL::get('portal_id')?addslashes(URL::get('portal_id')):str_replace('#','',PORTAL_ID);
		$categories=DB::fetch_all('
			SELECT
				`function`.id
				,`function`.name_'.Portal::language().' as name
				,`function`.structure_id
			FROM
				`function`
			WHERE
				1=1
			ORDER BY
				 structure_id'
		);
		foreach($categories as $key=>$value){
			if(!User::can_view(false,$value['structure_id'])){
				unset($categories[$key]);
			}
		}
		return $categories;
	}
	static function get_item_count($cond){
		return DB::fetch('
			select
				 count(*) as acount
			from
				`account_privilege`
			where '.$cond.'
				limit 0,1
		','acount');
	}
	static function get_items($cond, $item_per_page){
		return DB::fetch_all('
			SELECT
				`account_privilege`.id
				,`account_privilege`.account_id
				,account_privilege.portal_id
				,function.name_'.Portal::language().' as category_name
				,function.id as category_id
				,privilege.title_'.Portal::language().' as title
			FROM
			 	`account_privilege`
				inner join privilege on `account_privilege`.privilege_id=privilege.id
				left outer join function on `account_privilege`.category_id=function.id
			WHERE
				'.$cond.'
			'.(URL::get('order_by')?'order by '.URL::get('order_by').(URL::get('order_dir')?' '.URL::get('order_dir'):' order by portal_id'):'').'
			limit '.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
	}
}
?>