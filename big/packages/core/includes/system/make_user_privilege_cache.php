<?php
function make_user_privilege_cache($id)
{
	DB::query('
		SELECT
			CONCAT(privilege_module.module_id,"_",account_privilege.portal_id) as id,
			privilege_module.module_id,
			account_privilege.portal_id,
			IF(sum(privilege_module.`view`)>0,1,0) as `view`,
			IF(sum(privilege_module.`view_detail`)>0,1,0) as `view_detail`,
			IF(sum(privilege_module.`add`)>0,1,0) as `add`,
			IF(sum(privilege_module.`edit`)>0,1,0) as `edit`,
			IF(sum(privilege_module.`delete`)>0,1,0) as `delete`,
			IF(sum(privilege_module.`special`)>0,1,0) as `special`,
			IF(sum(privilege_module.`admin`)>0,1,0) as `admin`,
			IF(sum(privilege_module.`reserve`)>0,1,0) as `reserve`
		FROM
			account_privilege
			INNER JOIN `privilege_module` ON account_privilege.privilege_id=privilege_module.privilege_id
		WHERE
			category_id = 0
			AND (account_privilege.account_id="'.$id.'" or account_privilege.account_id="guest")
		GROUP BY
			privilege_module.module_id, account_privilege.portal_id
	');
	$user_actions = DB::fetch_all();
	$actions = array();
	foreach($user_actions as $user_action)
	{
		if($byte_cache = bindec($user_action['view'].$user_action['view_detail'].$user_action['add'].$user_action['edit'].$user_action['delete'].$user_action['special'].$user_action['reserve'].$user_action['admin']))
		{
			$actions[$user_action['portal_id']][$user_action['module_id']][0]=$byte_cache;
		}
	}
	DB::query('
		SELECT
			CONCAT(privilege_module.module_id,"_",account_privilege.category_id,"_",account_privilege.portal_id) as id,
			privilege_module.module_id,
			account_privilege.portal_id,
			function.structure_id,
			IF(sum(privilege_module.`view`)>0,1,0) as `view`,
			IF(sum(privilege_module.`view_detail`)>0,1,0) as `view_detail`,
			IF(sum(privilege_module.`add`)>0,1,0) as `add`,
			IF(sum(privilege_module.`edit`)>0,1,0) as `edit`,
			IF(sum(privilege_module.`delete`)>0,1,0) as `delete`,
			IF(sum(privilege_module.`special`)>0,1,0) as `special`,
			IF(sum(privilege_module.`admin`)>0,1,0) as `admin`,
			IF(sum(privilege_module.`reserve`)>0,1,0) as `reserve`
		FROM
			privilege_module
			INNER JOIN `account_privilege` ON account_privilege.privilege_id=privilege_module.privilege_id AND (account_privilege.account_id="'.$id.'" or account_privilege.account_id="guest")
			INNER JOIN function ON account_privilege.category_id = function.id
		WHERE
			category_id <> 0
		GROUP BY
			privilege_module.module_id, account_privilege.category_id, account_privilege.portal_id
	');
	$user_actions = DB::fetch_all();
	foreach($user_actions as $user_action)
	{
		if($byte_cache = bindec($user_action['view'].$user_action['view_detail'].$user_action['add'].$user_action['edit'].$user_action['delete'].$user_action['special'].$user_action['reserve'].$user_action['admin']))
		{
			$actions[$user_action['portal_id']][$user_action['module_id']][$user_action['structure_id']]=$byte_cache;
		}
	}
	DB::query('
		SELECT
			account_privilege.id,
			privilege_module.module_id,
			account_privilege.portal_id,
			IF(sum(privilege_module.`view`)>0,1,0) AS `view`,
			IF(sum(privilege_module.`view_detail`)>0,1,0) AS `view_detail`,
			IF(sum(privilege_module.`add`)>0,1,0) AS `add`,
			IF(sum(privilege_module.`edit`)>0,1,0) AS `edit`,
			IF(sum(privilege_module.`delete`)>0,1,0) AS `delete`,
			IF(sum(privilege_module.`special`)>0,1,0) AS `special`,
			IF(sum(privilege_module.`admin`)>0,1,0) AS `admin`,
			IF(sum(privilege_module.`reserve`)>0,1,0) AS `reserve`
		FROM
			privilege_module
			INNER JOIN `account_privilege` ON account_privilege.privilege_id=privilege_module.privilege_id
			INNER JOIN account_related ON (account_related.parent_id=account_privilege.account_id AND account_related.child_id="'.$id.'")
		WHERE
			category_id=0
		GROUP BY
			privilege_module.module_id, account_privilege.category_id, account_privilege.portal_id
	');
	$group_actions = DB::fetch_all();
	foreach($group_actions as $group_action)
	{
		$actions[$group_action['portal_id']][$group_action['module_id']][0]=
		(isset($actions[$group_action['portal_id']][$group_action['module_id']])?$actions[$group_action['portal_id']][$group_action['module_id']]:0) | bindec($group_action['view'].$group_action['view_detail'].$group_action['add'].$group_action['edit'].$group_action['delete'].$group_action['special'].$group_action['reserve'].$group_action['admin']);
	}
	/*DB::query('
		SELECT
			privilege_module.module_id AS id,
			account_privilege.portal_id,
			IF(sum(privilege_module.`view`)>0,1,0) AS `view`,
			IF(sum(privilege_module.`view_detail`)>0,1,0) AS `view_detail`,
			IF(sum(privilege_module.`add`)>0,1,0) AS `add`,
			IF(sum(privilege_module.`edit`)>0,1,0) AS `edit`,
			IF(sum(privilege_module.`delete`)>0,1,0) AS `delete`,
			IF(sum(privilege_module.`special`)>0,1,0) AS `special`,
			IF(sum(privilege_module.`admin`)>0,1,0) AS `admin`,
			IF(sum(privilege_module.`reserve`)>0,1,0) AS `reserve`
		FROM
			privilege_module
			INNER JOIN `account_privilege` ON account_privilege.privilege_id=privilege_module.privilege_id
		WHERE
			account_id =  "'.$group_id.'"
			OR account_id="'.$user_group_id.'"
		GROUP BY
			privilege_module.module_id, account_privilege.portal_id
	');
	$group_actions = DB::fetch_all();
	foreach($group_actions as $group_action)
	{
		$actions[$group_action['portal_id']][$group_action['id']]=
		(isset($actions[$group_action['portal_id']][$group_action['id']])?$actions[$group_action['portal_id']][$group_action['id']]:0) | bindec($group_action['view'].$group_action['view_detail'].$group_action['add'].$group_action['edit'].$group_action['delete'].$group_action['special'].$group_action['reserve'].$group_action['admin']);
	}*/
	$groups = DB::fetch_all('
		SELECT
			parent_id as id
		FROM
			account_related
		WHERE
			child_id="'.$id.'"
	');
	$code = '$this->groups='.var_export($groups,true).';'.
		'$this->actions='.var_export($actions,true).';';
	DB::update('account',array('cache_privilege'=>$code),'id="'.$id.'"');
	return $code;
}
?>