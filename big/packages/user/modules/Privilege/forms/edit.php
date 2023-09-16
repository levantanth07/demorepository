<?php
class EditPrivilegeForm extends Form
{
	function __construct()
	{
		Form::Form('EditPrivilegeForm');
		if(URL::get('cmd')=='edit')
		{
			$this->add('id',new IDType(true,'object_not_exists','privilege'));
		}
		$languages = DB::select_all('language');
		foreach($languages as $language)
		{
			$this->add('title_'.$language['id'],new TextType(true,'invalid_title',0,255));
			$this->add('description_'.$language['id'],new TextType(false,'invalid_description',0,200000));
		}
		$this->add('code',new UniqueType('Mã này đã tồn tại','privilege','code'));
		$this->add('package_id',new IDType(true,'invalid_package_id','package'));
		$this->link_css('assets/default/css/tabs/tabpane.css');
		$this->link_js('assets/default/css/tabs/tabpane.js');
		$this->link_css('assets/default/css/cms.css');
	}
	function on_submit()
	{
		if(URL::get('cmd')=='edit')
		{
			$row = DB::select('privilege',$_REQUEST['id']);
		}
		if($this->check() and URL::get('confirm_edit'))
		{
			$new_row =
				array(
					'package_id',
					'status',
					'category_id',
						'code',
						'portal_id'=>PORTAL_ID
				);
			$languages = DB::select_all('language');
			foreach($languages as $language)
			{
				$new_row = $new_row + array('title_'.$language['id']=>URL::get('title_'.$language['id']),'description_'.$language['id']=>URL::get('description_'.$language['id']));
			}
			if(URL::get('cmd')=='edit')
			{
				$id = $_REQUEST['id'];
				DB::update_id('privilege', $new_row,$id);
			}
			else
			{
				$id = DB::insert('privilege', $new_row);
			}
			if(URl::get('deleted_ids'))
			{
				foreach(URl::get('deleted_ids') as $delete_id)
				{
					DB::delete_id('account_privilege',$delete_id);
				}
			}
			require_once 'packages/core/includes/system/update_privilege.php';
			make_privilege_cache();
			Url::js_redirect(true,'Cập nhật thành công',array('package_id'=>isset($_GET['package_id'])?$_GET['package_id']:'','cmd'=>'grant','id'=>$id));
		}
	}
	function draw()
	{
		if(URL::get('cmd')=='edit' and $row=DB::select('privilege','id="'.URL::sget('id').'"'))
		{
			foreach($row as $key=>$value)
			{
				if(!isset($_POST[$key]))
				{
					$_REQUEST[$key] = $value;
				}
			}
			$edit_mode = true;
		}
		else
		{
			$edit_mode = false;
		}
		$db_items = DB::fetch_all('select id from account order by id');
		$group_id_options = '';
		foreach($db_items as $item)
		{
			$group_id_options .= '<option value="'.$item['id'].'">'.$item['id'].'</option>';
		}
		if(!isset($_REQUEST['mi_group_privilege']) and $edit_mode)
		{
			DB::query('
				select
					`account_privilege`.id
					,`account_privilege`.`parameters`
					,`account_privilege`.`account_id`
				from
					`account_privilege`
				where
					`account_privilege`.privilege_id="'.URL::sget('id').'"
				limit 0,50
			'
			);
			$mi_group_privilege = DB::fetch_all();
			$_REQUEST['mi_group_privilege'] = $mi_group_privilege;
		}
		DB::query('
			select
				id,
				`package`.`name`,
				structure_id
			from
				`package`
			order by structure_id
			'
		);
		require_once 'cache/config/status.php';
		require 'cache/tables/function.cache.php';
		$package_id_list = MiString::get_list(DB::fetch_all());
		$this->parse_layout('edit',
			($edit_mode?$row:array())+
			array(
			'languages'=>DB::select_all('language'),
			'package_id_list'=>$package_id_list,
			'group_id_options' => $group_id_options,
			'status_list'=>$status,
			'category_id_list'=>array('0'=>Portal::language('all'))+MiString::get_list($categories)
			)
		);
	}
}
?>
