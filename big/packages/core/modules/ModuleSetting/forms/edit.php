<?php
class EditModuleSettingForm extends Form
{
	function EditModuleSettingForm()
	{
		Form::Form('EditModuleSettingForm');
		/*if(URL::get('cmd')=='edit')
		{
			$this->add('id',new IDType(true,'object_not_exists','module_setting'));
		}*/
		$this->add('name',new TextType(true,'invalid_name',0,255));
		//$this->add('name',new UniqueType('duplicate_name','module_setting','name'));
		$this->add('description',new TextType(false,'invalid_description',0,200000));
		//$this->add('type',new SelectType(false,'invalid_type',array('TEXT'=>'TEXT','INT'=>'INT','FLOAT'=>'FLOAT','EMAIL'=>'EMAIL','COLOR'=>'COLOR','FONT_FAMILY'=>'FONT_FAMILY','FONT_SIZE'=>'FONT_SIZE','FONT_WEIGHT'=>'FONT_WEIGHT','TEXTAREA'=>'TEXTAREA','RICH_EDITOR'=>'RICH_EDITOR','TABLE'=>'TABLE','SELECT'=>'SELECT','DATE'=>'DATE','DATETIME'=>'DATETIME','CHECKBOX'=>'CHECKBOX','RADIO'=>'RADIO','FILE'=>'FILE','IMAGE'=>'IMAGE','YESNO'=>'YESNO')));
		$this->add('default_value',new TextType(false,'invalid_default_value',0,200000));
		$this->add('value_list',new TextType(false,'invalid_value_list',0,200000));
		$this->add('edit_condition',new TextType(false,'invalid_edit_condition',0,200000));
		$this->add('view_condition',new TextType(false,'invalid_view_condition',0,200000));
		$this->add('extend',new TextType(false,'invalid_extend',0,200000));
		$this->add('group_name',new TextType(false,'invalid_group_name',0,255));
		$this->add('position',new IntType(false,'invalid_position','0','100000000000'));
		$this->add('meta',new TextType(false,'invalid_meta',0,200000));
		$this->add('group_column',new IntType(false,'invalid_group_column','0','100000000000'));
		$this->add('update_code',new TextType(false,'invalid_update_code',0,200000));
		$this->add('module_id',new IDType(true,'invalid_module_id','module'));
	}
	function on_submit()
	{
		if(URL::get('cmd')=='edit')
		{
			$row = DB::select('module_setting',$_GET['id']);
		}
		if($this->check() and URL::get('confirm_edit'))
		{
			$new_row =
				array(
					'module_id', 'name', 'description', 'type', 'default_value', 'value_list', 'style','edit_condition', 'view_condition', 'extend', 'group_name', 'position'=>URL::get('position'), 'meta', 'group_column', 'update_code',
				);
			$new_row['id'] = $_REQUEST['module_id'].'_'.$_REQUEST['id'];
			if((URL::get('cmd')!='edit' or $new_row['id']==$_GET['id']) or !DB::select('module_setting','id="'.$new_row['id'].'"'))
			{
				if(URL::get('cmd')=='edit')
				{
					$id = $_REQUEST['module_id'].'_'.$_REQUEST['id'];
					if($_REQUEST['id'] != $_GET['id'])
					{
						DB::update('block_setting',array('setting_id'=>$id),'setting_id="'.$_REQUEST['module_id'].'_'.$_GET['id'].'"');
					}
					DB::update_id('module_setting', $new_row,$row['id']);
				}
				else
				{
					require_once 'packages/core/includes/system/si_database.php';
					$id = DB::insert('module_setting', $new_row);
				}
				$new_row['position'] ++;
				Url::redirect_current($new_row+array('just_edited_id'=>$id));
			}
		}
	}
	function draw()
	{
		if(URL::get('cmd')=='edit' and $row=DB::select('module_setting','id="'.$_GET['id'].'"'))
		{
			$row['id'] = str_replace($row['module_id'].'_','',URL::get('id',$row['id']));
			$_REQUEST['id'] = $row['id'];
			foreach($row as $key=>$value)
			{
				if(is_string($value) and !isset($_REQUEST[$key]))
				{
					$_REQUEST[$key] = $value;
				}
			}
			$edit_mode = true;
		}
		else
		{
			$edit_mode = false;
			$_REQUEST['edit_condition'] = URL::get('edit_condition','User::is_admin()');
			$_REQUEST['view_condition'] = URL::get('view_condition','User::is_admin()');
		}
		DB::query('select
				id, `module`.`name` as name
				from `module`
				'
			);
		$module_id_list = array(''=>'')+String::get_list(DB::fetch_all());
		$this->parse_layout('edit',
			($edit_mode?$row:array())+
			array(
				'module_id_list'=>$module_id_list,
				'style_list'=>array(0=>'Linebreak', 1=>'Inline', 2=>'Very large information'),
			)
		);
	}
}
?>