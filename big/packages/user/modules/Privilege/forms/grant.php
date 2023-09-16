<?php
class GrantPrivilegeForm extends Form
{
	function __construct()
	{
		Form::Form('GrantPrivilegeForm');
		$this->add('id',new IDType(true,'invalid_privilege_id','privilege'));
		$this->link_css('assets/default/css/cms.css');
		$this->link_js('packages/core/includes/js/multi_items.js');
	}
	function on_submit()
	{
		if($this->check())
		{
			$is_error = false;
			if(isset($_REQUEST['mi_privilege_module']))
			{
				foreach($_REQUEST['mi_privilege_module'] as $key=>$record)
				{
					$_REQUEST['mi_privilege_module'][$key]['privilege_id'] = URL::sget('id');
					if(!($_REQUEST['mi_privilege_module'][$key]['module_id'] = DB::fetch('select id from module where name="'.$record['module_name'].'"','id')))
					{
						$this->error('module_name_'.$key,'Unknow module "<b>'.$record['module_name'].'</b>"');
						$is_error = true;
					}
				}
				if(!$is_error)
				{
					if(URL::get('deleted_ids'))
					{
						$deleted_ids = explode(',',URL::get('deleted_ids'));
						foreach($deleted_ids as $module_name)
						{
							DB::delete('privilege_module','module_id="'.DB::fetch('select id from module where name="'.$module_name.'"','id').'" and privilege_id="'.URL::sget('id').'"');
						}
					}
					$ids = array();
					foreach($_REQUEST['mi_privilege_module'] as $key=>$record)
					{
						unset($record['module_name']);
						unset($record['id']);
						$record['privilege_id'] = URL::get('id');
						$record['view'] = isset($record['view'])?1:0;
						$record['view_detail'] = isset($record['view_detail'])?1:0;
						$record['add'] = isset($record['add'])?1:0;
						$record['edit'] = isset($record['edit'])?1:0;
						$record['delete'] = isset($record['delete'])?1:0;
						$record['special'] = isset($record['special'])?1:0;
						$record['reserve'] = isset($record['reserve'])?1:0;
						$record['admin'] = isset($record['admin'])?1:0;
						if($record['view'] == 0 and $record['view_detail'] == 0 and $record['add'] == 0
							and $record['edit'] == 0 and $record['delete'] == 0 and $record['special'] == 0
							and $record['reserve'] == 0 and $record['admin'] == 0)
						{
							DB::delete('privilege_module','module_id="'.$record['module_id'].'" and privilege_id="'.$record['privilege_id'].'"');
						}
						else
						{
							if($privilege_module=DB::select('privilege_module','module_id="'.$record['module_id'].'" and privilege_id="'.$record['privilege_id'].'"'))
							{
								DB::update('privilege_module',$record,'id="'.$privilege_module['id'].'"');
								$ids[] = $privilege_module['id'];
							}
							else
							{
								$ids[] = DB::insert('privilege_module',$record);
							}
						}
					}
					$_REQUEST['selected_ids']=join(',',$ids); //huan them
				}
			}
			else
			{
				if(URL::get('deleted_ids'))
				{
					$deleted_ids = explode(',',URL::get('deleted_ids'));
					foreach($deleted_ids as $module_name)
					{
						DB::delete('privilege_module','module_id="'.DB::fetch('select id from module where name="'.$module_name.'"','id').'" and privilege_id="'.URL::get('id').'"');
					}
				}
			}
			if(!$is_error)
			{
				DB::update('account',array('cache_privilege'=>''),1);
				Url::redirect_current();
			}
		}
	}
	function draw()
	{
		$privilege_id_list = MiString::get_list(DB::fetch_all('
			select
				privilege.id,
				privilege.title_1 as name
			from
				privilege
			order by name'));
		$db_items = DB::select_all('module',false,'name');
		$module_id_options = '';
		foreach($db_items as $item)
		{
			$module_id_options .= '<option value="'.$item['id'].'">'.$item['name'].'</option>';
		}
		$privilege_id = URL::get('id',1);
		if($privilege = DB::select('privilege',$privilege_id))
		{
			$modules = DB::fetch_all('
				select
					module.name as id, module.name as module_name,`edit`,`add`,`delete`,`view`,view_detail,`special`,`reserve`,`admin`
				from
					module
					inner join package on package_id=package.id
					left join privilege_module on module.id=module_id and privilege_id="'.URL::get('id').'"
				where
					'.IDStructure::child_cond(DB::fetch('select structure_id from package where id='.$privilege['package_id'],'structure_id')).'
					or module_id IS NOT NULL
				order by
					privilege_module.id DESC ,module.name
			');
			if(isset($_REQUEST['mi_privilege_module']))
			{
				foreach($_REQUEST['mi_privilege_module'] as $record)
				{
					if(isset($modules[$record['module_name']]))
					{
						unset($modules[$record['module_name']]);
					}
				}
			}
			foreach($modules as $module_id=>$module)
			{
				$_REQUEST['mi_privilege_module'][]=$module;
			}
		}
		$_REQUEST['id']=$privilege_id;
		$this->parse_layout('grant',
			array(
				'id_list'=>$privilege_id_list,
				'module_id_options' => $module_id_options,
			)
		);
	}
}
?>