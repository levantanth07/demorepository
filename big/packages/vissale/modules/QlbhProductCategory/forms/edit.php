<?php
class EditQlbhProductCategoryForm extends Form
{
	function __construct()
	{
		Form::Form('EditQlbhProductCategoryForm');
		if(URL::get('cmd')=='edit')
		{
			$this->add('id',new IDType(true,'object_not_exists','qlbh_product_category'));
		}
		$this->add('name',new TextType(true,'invalid_name',0,2000)); 
		$this->add('description',new TextType(false,'invalid_description',0,200000));
		$this->link_css('skins/default/css/tabs/tabpane.css');
		$this->link_js('skins/default/css/tabs/tabpane.js');
		$this->link_css('skins/default/css/cms.css');
	}
	function on_submit()
	{
		require_once 'packages/core/includes/utils/upload_file.php';
		update_upload_file('icon_url',str_replace('#','',PORTAL_ID).'/category');
		if($this->check() and URL::get('confirm_edit'))
		{
			if(URL::get('cmd')=='edit')
			{
				$this->old_value = DB::select('qlbh_product_category','id="'.addslashes($_REQUEST['id']).'"');
				if(Url::get('delete_icon_url')=='0')
				{
					@unlink($this->old_value['icon_url']);
					DB::update_id('qlbh_product_category',array('icon_url'=>''),$_REQUEST['id']);
				}	
			}
			$this->save_item();
			if(!$this->is_error())
			{
				Url::redirect_current(array('just_edited_id'=>$this->id));
			}	
		}
	}	
	function draw()
	{
		$languages = DB::select_all('language');
		$this->init_edit_mode();
		$this->get_parents();
		$this->init_database_field_select();
		require_once 'cache/config/status.php';
		require_once Portal::template_js('core').'/tinymce/init_tinyMCE.php';
		$this->parse_layout('edit',
			($this->edit_mode?$this->init_value:array())+
			array(
			'languages'=>$languages,
			'parent_id_list'=>MiString::get_list(QlbhProductCategoryDB::check_categories($this->parents)),
			'parent_id'=>($this->edit_mode?si_parent_id('qlbh_product_category',$this->init_value['structure_id'],''):1),
			'type_list'=>$this->type_list, 
			'status_list'=>$status
			)
		);
	}
	function save_item()
	{
		$extra = array();
		$extra=$extra+array('name'=>Url::get('name',1)); 
		$extra=$extra+array('brief'=>Url::get('brief',1)); 
		$extra=$extra+array('description'=>Url::get('description',1)); 
		$new_row = $extra+
		array('type'=>'PRODUCT','status', 'url');
		if(Url::get('icon_url')!='')
		{
			$new_row['icon_url'] =Url::get('icon_url');
		}
		require_once 'packages/core/includes/utils/vn_code.php';
		$name_id = convert_utf8_to_url_rewrite($new_row['name']); 			
		if(!DB::fetch('select name_id from qlbh_product_category where name_id="'.$name_id.'"'))
		{
			$new_row+=array('name_id'=>$name_id);
		}
		else
		{
			$new_row+=array('name_id'=>$name_id);
			/*if(Url::get('id') and Url::get('cmd')=='edit')
			{
				$new_row+=array('name_id'=>$name_id);
			}
			else
			{
				$this->error('name','duplicate_name');
			}*/
		}	
		if(!$this->is_error())
		{	
			if(URL::get('cmd')=='edit')
			{
				$this->id = $_REQUEST['id'];
				$new_row['last_time_update'] = time();
				DB::update_id('qlbh_product_category', $new_row,$this->id);
				if($this->old_value['structure_id']!=ID_ROOT)
				{
					if (Url::check(array('parent_id')))
					{
						$parent = DB::select('qlbh_product_category',$_REQUEST['parent_id']);
						if($parent['structure_id']==$this->old_value['structure_id'])
						{
							$this->error('id','invalid_parent');
						}
						else
						{
							require_once 'packages/core/includes/system/si_database.php';
							if(!si_move('qlbh_product_category',$this->old_value['structure_id'],$parent['structure_id']))
							{
								$this->error('id','invalid_parent');
							}
						}
					}
				}
			}
			else
			{
				require_once 'packages/core/includes/system/si_database.php';
				$new_row['time'] = time();
				if(isset($_REQUEST['parent_id']))
				{
					$this->id = DB::insert('qlbh_product_category', $new_row+array('structure_id'=>si_child('qlbh_product_category',structure_id('qlbh_product_category',$_REQUEST['parent_id']),'')));
				}
				else
				{
					$this->id = DB::insert('qlbh_product_category', $new_row+array('structure_id'=>ID_ROOT));		
				}				
			}
			save_log($this->id);
		}	
	}
	function init_edit_mode()
	{
		if(URL::get('cmd')=='edit' and $this->init_value=DB::select('qlbh_product_category','id='.intval(URL::sget('id')).''))
		{
			foreach($this->init_value as $key=>$value)
			{
				if(is_string($value) and !isset($_REQUEST[$key]))
				{
					$_REQUEST[$key] = $value;
				}
			}
			$this->edit_mode = true;
		}
		else
		{
			$this->edit_mode = false;
		}
	}
	function get_parents()
	{
		require_once 'packages/core/includes/system/si_database.php';
		$extra_cond = ' and qlbh_product_category.type="PRODUCT"';
		$sql = '
			select 
				id,
				structure_id
				,name
			from 
			 	`qlbh_product_category`
			where 
				1=1 '.$extra_cond.'
			order by 
				structure_id
		';
		$this->parents = DB::fetch_all($sql);
	}
	function init_database_field_select()
	{
		$extra_cond = '';
		if(Url::get('type') and Url::sget('page')!='portal_category')
		{
			$extra_cond = ' and (type.id="'.Url::get('type').'")';
		}	
		if($types = DB::fetch_all('select
					`type`.id,
					`type`.`title_'.Portal::language().'` as name
				from 
					`type`
				where
					1=1 '.$extra_cond
			))
			{
				$this->type_list = MiString::get_list($types); 
			}
			else
			{	
				$this->type_list = MiString::get_list(DB::fetch_all('select
					`type`.id,
					`type`.`title_'.Portal::language().'` as name
				from 
					`type`
				where
					1=1'.$extra_cond
				));		
			}	
	}	
}
?>