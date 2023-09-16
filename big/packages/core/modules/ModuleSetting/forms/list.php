<?php
class ListModuleSettingForm extends Form
{
	function ListModuleSettingForm()
	{
		Form::Form('ListModuleSettingForm');
	}
	function on_submit()
	{
		if(URL::get('confirm'))
		{
			foreach(URL::get('selected_ids') as $id)
			{
			}
			require_once 'detail.php';
			foreach(URL::get('selected_ids') as $id)
			{
				ModuleSettingForm::delete($this,$id);
				if($this->is_error())
				{
					return;
				}
			}
			Url::redirect_current(array('module_id'=>isset($_GET['module_id'])?$_GET['module_id']:'',
	));
		}
	}
	function draw()
	{
		$cond = '
				1 '
				.((URL::get('module_id') or 1)?'
					and `module_setting`.module_id = "'.URL::get('module_id').'"
				':'')
			.((URL::get('cmd')=='delete' and is_array(URL::get('selected_ids')))?' and `module_setting`.id in ("'.join(URL::get('selected_ids'),'","').'")':'')
		;
		if (Url::get('item_per_page'))
		{
			$item_per_page = Url::get('item_per_page',100);
		}else
		{
			$item_per_page = Module::$current->get_setting('item_per_page',100);
		}
		DB::query('
			select count(*) as acount
			from
				`module_setting`
				left outer join `module` on `module`.id=`module_setting`.`module_id`
			where '.$cond.'
			'.((false         or URL::get('order_by')=='module_setting.group_name' or URL::get('order_by')=='module_setting.position'     )?'order by '.URL::get('order_by').(URL::get('order_dir')?' '.URL::get('order_dir'):''):'').'
			limit 0,1
		');
		$count = DB::fetch();
		require_once 'packages/core/includes/utils/paging.php';
		$paging = paging($count['acount'],$item_per_page,4);
		DB::query('
			select
				`module_setting`.id, module_setting.module_id, module.name as module_name
				,`module_setting`.`name` ,`module_setting`.`description` ,`module_setting`.`type` ,`module_setting`.`default_value` ,`module_setting`.`value_list` ,`module_setting`.`edit_condition` ,`module_setting`.`view_condition` ,`module_setting`.`extend` ,`module_setting`.`group_name` ,`module_setting`.`position` ,`module_setting`.`meta` ,`module_setting`.`group_column` ,`module_setting`.`update_code`
			from
			 	`module_setting`
				left outer join `module` on `module`.id=`module_setting`.`module_id`
			where '.$cond.'
			'.(URL::get('order_by')?'order by '.URL::get('order_by').(URL::get('order_dir')?' '.URL::get('order_dir'):''):' order by module_setting.id').'
			limit '.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
		$items = DB::fetch_all();
		foreach($items as $key=>$item)
		{
			$items[$key]['id'] = str_replace($items[$key]['module_id'].'_','',$items[$key]['id']);
			$defintition = array('TEXT',' INT',' FLOAT',' EMAIL',' COLOR',' FONT_FAMILY','FONT_SIZE','FONT_WEIGHT','TEXTAREA',' TABLE',' SELECT',' DATE',' DATETIME',' CHECKBOX',' RADIO',' FILE',' IMAGE',' YESNO');
			if(isset($defintition[$items[$key]['type']]))
			{
				$items[$key]['type'] = $defintition[$items[$key]['type']];
			}
			else
			{
				$items[$key]['type'] = '';
			}       $items[$key]['position']=System::display_number($item['position']);  $items[$key]['group_column']=System::display_number($item['group_column']);
		}
		DB::query('select
			id, `module`.`name` as name
			from `module`
			order by name
			'
		);
		$module_id_list = array(''=>'')+String::get_list(DB::fetch_all());
		$i=1;
		foreach ($items as $key=>$value)
		{
			$items[$key]['i']=$i++;
		}
		$just_edited_id['just_edited_ids'] = array();
		if (UrL::get('selected_ids'))
		{
			if(is_string(UrL::get('selected_ids')))
			{
				if (strstr(UrL::get('selected_ids'),','))
				{
					$just_edited_id['just_edited_ids']=explode(',',UrL::get('selected_ids'));
				}
				else
				{
					$just_edited_id['just_edited_ids']=array('0'=>UrL::get('selected_ids'));
				}
			}
		}
		$this->parse_layout('list',$just_edited_id+
			array(
				'items'=>$items,
				'paging'=>$paging,
				'total_page'=>$count['acount'],
				'module_id_list' => $module_id_list,
				'module_id' => URL::get('module_id',''),
			)
		);
	}
}
?>