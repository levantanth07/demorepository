<?php
class PrivilegeForm extends Form
{
	function PrivilegeForm()
	{
		Form::Form("PrivilegeForm");
		$this->add('id',new IDType(true,'object_not_exists','privilege'));
		$this->link_css(Portal::template('core').'/css/crud.css');
	}
	function on_submit()
	{
		if($this->check() and URL::get('confirm'))
		{
			$this->delete($this,$_REQUEST['id']);
			require_once 'packages/core/includes/system/update_privilege.php';
			make_privilege_cache();
			Url::redirect_current(array('package_id'=>isset($_GET['package_id'])?$_GET['package_id']:'',
	));
		}
	}
	function draw()
	{
		DB::query('
			select
				`privilege`.id
				,`privilege`.`title_'.Portal::language().'` as title ,`privilege`.`description_'.Portal::language().'` as description
				,`package`.`name` as package_id
			from
			 	`privilege`
				left outer join `package` on `package`.id=`privilege`.package_id
			where
				`privilege`.id = "'.URL::sget('id').'"');
		if($row = DB::fetch())
		{
		}
		DB::query('
			select
				`account_privilege`.id
				,`account_privilege`.`parameters`
				,`account_id` as group_id_name
			from
				`account_privilege`
			where
				`account_privilege`.privilege_id="'.$_REQUEST['id'].'"
			'
		);
		$row['group_privilege_items'] = DB::fetch_all();
		$this->parse_layout('detail',$row);
	}
	function delete(&$form,$id)
	{
		DB::delete('account_privilege', 'privilege_id="'.$id.'"');
		DB::delete('privilege', 'id="'.$id.'"');
	}
}
?>
