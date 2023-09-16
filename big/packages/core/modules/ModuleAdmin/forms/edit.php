<?php
class EditModuleAdminForm extends Form
{
	function __construct()
	{
		Form::Form('EditModuleAdminForm');
		if(URL::get('cmd')=='edit')
		{
			$this->add('id',new IDType(true,'object_not_exists','module'));
		}
		$this->add('name',new UniqueType('Trùng tên module','module','name'));
		$this->add('package_id',new IDType(true,'invalid_package_id','package'));
		$this->add('type',new TextType(false,'invalid_type',0,20));
		$languages = DB::fetch_all('select id,name,icon_url from language where active=1');
		foreach($languages as $language)
		{
			$this->add('title_'.$language['id'],new TextType(false,'invalid_title',0,2000));
			$this->add('description_'.$language['id'],new TextType(false,'invalid_description',0,200000));
		}
		$this->add('module_table.table',new TextType(true,'invalid_table',0,255));
	}
	function on_submit()
	{
		$image_url = '';
		if(URL::get('cmd')=='edit')
		{
			$row = DB::select('module',$_REQUEST['id']);
			$image_url = $row['image_url'];
		}
		if($this->check() and URL::get('confirm_edit'))
		{
			$extra = array();
			$languages = DB::select_all('language','id=1');
			foreach($languages as $language)
			{
				$extra=$extra+array('title_'.$language['id']=>Url::get('title_'.$language['id'],1));
				$extra=$extra+array('description_'.$language['id']=>Url::get('description_'.$language['id'],1));
			}
			require_once 'packages/core/includes/portal/package.php';
			if(!empty($_FILES['image_url'])){
				$new_path = 'images/module/'.$_FILES['image_url']['name'];
				if(move_uploaded_file($_FILES['image_url']['tmp_name'],$new_path)){
					$image_url = $new_path;
				}
			}
			$new_row = $extra+
				array(
					'package_id', 'image_url'=>$image_url,
					'name', 'type', 'use_dblclick'=>Url::get('use_dblclick')?Url::get('use_dblclick'):0
					, 'update_setting_code', 'create_block_code', 'destroy_block_code',
					'path'=>get_package_path(URL::get('package_id')).'modules/'.URL::get('name').'/'
				)+((URL::get('type')=='PLUGIN')?array('action'=>URL::get('action'),'action_module_id'=>URL::get('action_module_id')):array())+((URL::get('type')=='WRAPPER')?array('action_module_id'=>URL::get('action_module_id')):array());
			if(Url::get('package_id') and $parent=IDStructure::parent(DB::structure_id('package',intval(Url::get('package_id')))) and $package=DB::fetch('select name as id from package where structure_id ="'.$parent.'"') and $package['id']=='tcv')
			{
				$name_package=DB::fetch('select name as id from package where id ="'.URL::get('package_id').'"');
				$new_row['path']='packages/tcv/'.$name_package['id'].'/'.URL::get('name').'/';
			}
			if(URL::get('cmd')=='edit')
			{
				$id = $_REQUEST['id'];
				DB::update_id('module', $new_row,$id);
				require_once 'packages/core/includes/portal/update_page.php';
				$pages = DB::fetch_all('select page_id as id from block where module_id="'.$id.'"');
				foreach($pages as $page_id=>$page)
				{
					update_page($page_id);
				}
			}
			else
			{
				require_once 'packages/core/includes/system/si_database.php';
				$id = DB::insert('module', $new_row);
				if(URL::get('type') == 'WRAPPER' and $module = DB::select('module',URL::get('action_module_id')))
				{
					DB::query('
						INSERT INTO
							module_setting(`id`, `name`, `description`, `module_id`, `type`, `style`, `default_value`, `value_list`, `edit_condition`, `view_condition`, `extend`, `group_name`, `position`, `meta`, `group_column`, `update_code`)
						SELECT
							REPLACE(`id`,"'.$module['id'].'","'.$id.'"),`name`, `description`, "'.$id.'", `type`, `style`, `default_value`, `value_list`, `edit_condition`, `view_condition`, `extend`, `group_name`, `position`, `meta`, `group_column`, `update_code`
						FROM
							module_setting
						WHERE
							module_id = "'.$module['id'].'"
					');
				}
			}
			if(URl::get('deleted_ids'))
			{
				foreach(URl::get('deleted_ids') as $delete_id)
				{
					DB::delete_id('module_table',$delete_id);
				}
			}
			if(isset($_REQUEST['mi_module_table']))
			{
				foreach($_REQUEST['mi_module_table'] as $key=>$record)
				{
					$empty = true;
					foreach($record as $record_value)
					{
						if($record_value)
						{
							$empty = false;
						}
					}
					if(!$empty)
					{
						$record['module_id'] = $id;
						if($record['id'])
						{
							DB::update('module_table',$record,'id='.$record['id']);
						}
						else
						{
							unset($record['id']);
							DB::insert('module_table',$record);
						}
					}
				}
			}
		//	die;
			if(URL::get('cmd')=='edit')
			{
				Url::redirect_current(array('package_id'=>URL::get('package_id'), 'type', 'name'=>isset($_GET['name'])?$_GET['name']:'', 'just_edited_id'=>$id));
	  		}
			else
			{
				Url::redirect_current(array('package_id'=>URL::get('package_id'), 'type', 'name'=>isset($_GET['name'])?$_GET['name']:'','id'=>$id,'cmd'=>(URL::get('type')=='HTML')?'edit_html':((URL::get('type')=='content')?'edit_content':'list'),'just_edited_id'=>$id));
			}
		}
	}
	function draw()
	{
		$languages = DB::fetch_all('select id,name,icon_url from language where active=1');
		if(URL::get('cmd')=='edit' and $row=DB::select('module',URL::sget('id')))
		{
			foreach($row as $key=>$value)
			{
				if(is_string($value) and !isset($_POST[$key]))
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
		if(!isset($_REQUEST['mi_module_table']) and $edit_mode)
		{
			DB::query('
				select
					`module_table`.id
					,`module_table`.`table`
				from
					`module_table`
				where
					`module_table`.module_id="'.URL::sget('id').'"'
			);
			$mi_module_table = DB::fetch_all();
			$_REQUEST['mi_module_table'] = $mi_module_table;
		}
		DB::query('select
			id, `package`.`name` as name
			from `package`
			'
		);
		$package_id_list = MiString::get_list(DB::fetch_all());
		$this->parse_layout('edit',
			($edit_mode?$row:array('type'=>URL::get('type')))+
			array(
			'languages'=>$languages,
			'type_list'=>array(''=>'NORMAL','SERVICE'=>'SERVICE','content'=>'content','HTML'=>'HTML','PLUGIN'=>'PLUGIN','WRAPPER'=>'WRAPPER'),
			'action_list'=>array(''=>'','ONLOAD'=>'ONLOAD','ONENDLOAD'=>'ONENDLOAD','ONDRAW'=>'ONDRAW','ONENDDRAW'=>'ONENDDRAW','ONSUBMIT'=>'ONSUBMIT','ONINSERT'=>'ONINSERT','ONEDIT'=>'ONEDIT','ONDELETE'=>'ONDELETE','ONCREATE'=>'ONCREATE','ONDESTROY'=>'ONDESTROY'),
			'action_module_id_list'=>MiString::get_list(DB::fetch_all('select id, name from module order by name')),
			'package_id_list'=>$package_id_list,
			'using_pages'=>DB::fetch_all('
				SELECT
					page.id, page.name
				FROM
					page
					INNER JOIN block ON block.page_id = page.id
				WHERE
					module_id="'.URL::sget('id').'"
				ORDER BY
					page.name
			')
			)
		);
	}
}
?>