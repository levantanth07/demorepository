<?php
class EditLanguageForm extends Form
{
	function __construct()
	{
		Form::Form('EditLanguageForm');
		if(URL::get('cmd')=='edit')
		{
			$this->add('id',new IDType(true,'object_not_exists','language'));
		}
		$this->add('code',new TextType(false,'invalid_code',0,255));
		$this->add('name',new TextType(true,'invalid_name',0,255));
		$this->link_css('assets/default/css/cms.css');
	}
	function on_submit()
	{
		require_once 'packages/core/includes/utils/upload_file.php';
		$dir = 'default/icon/';
		$new_row  = array();
		if(URL::get('cmd')=='edit' and $row = DB::fetch('select * from language where id='.Url::get('id')))
		{
			if(Url::get('delete_icon_url')=='0')
			{
				DB::update_id('language',array('icon_url'=>''),Url::get('id'));
				@unlink($row['icon_url']);
			}
		}
		if($_FILES['icon_url']['tmp_name']!='')
		{
			update_upload_file('icon_url',$dir);
			$new_row +=array('icon_url');
		}
		if($this->check() and URL::get('confirm_edit'))
		{
			$new_row +=
				array(
					'id','code', 'name','active'=>Url::get('active')?1:0,'default'=>Url::get('default')?1:0
				);
			if(URL::get('cmd')=='edit')
			{
				$id = $_REQUEST['id'];
				DB::update_id('language', $new_row,$id);
			}
			else
			{
				require_once 'packages/core/includes/system/si_database.php';
				$id = DB::insert('language', $new_row);
				Language::update_portal_language();
			}
			save_log($id);
			Url::redirect_current(array(
	)+array('just_edited_id'=>$id));
		}
	}
	function draw()
	{
		if(URL::get('cmd')=='edit' and $row=DB::fetch('select * from language where id='.$_REQUEST['id']))
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
		$this->parse_layout('edit',
			($edit_mode?$row:array())+
			array(
			)
		);
	}
}
?>