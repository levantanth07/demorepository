<?php
class EditPartnerAdminForm extends Form
{
	function __construct()
	{
		Form::Form('EditPartnerAdminForm');
		$this->add('link',new TextType(true,'lỗi nhập link',0,2000));//validate
		$this->link_css('assets/default/css/tabs/tabpane.css');
	}

	function save_image($file,$id){
		require_once 'packages/core/includes/utils/upload_file.php';
		$dir = substr(PORTAL_ID,1).'/partner';
		update_upload_file('small_thumb_url',$dir,'IMAGE',false,false,false);
		$row = array();

		///////////////////////////////////////////////////////

		if(Url::get('small_thumb_url')!=''){
			$row['small_thumb_url'] =Url::get('small_thumb_url');
		}
		DB::update_id('partner',$row,$id);
	}

	function save_item()
	{
		$rows = array();
		$languages = DB::select_all('language');
		require_once 'packages/core/includes/utils/vn_code.php';
		require_once 'packages/core/includes/utils/search.php';
		$rows += array(
			'status',
			'link'=>Url::get('link'),
			'name'=>Url::get('name')
//			'link2'=>Url::get('link2')
			);
			if(Url::get('position')=='')
			{
				$position = DB::fetch('select max(position)+1 as id from partner');
				$rows['position'] = $position['id'];
			}
			else
			{
				$rows['position'] = Url::get('position');
			}
//			$name_id = '';
//			if(!DB::fetch('select name_id from partner where name_id="'.$name_id.'"'))
//			{
//				$rows+=array('name_id'=>$name_id);
//			}
//			else
//			{
//				$this->error('link','duplicate_name');
//				if(Url::get('id') and Url::get('cmd')=='edit')
//				{
//					$rows+=array('name_id'=>$name_id);
//				}
//				else
//				{
//					$this->error('link','duplicate_name');
//				}
//			}
		return ($rows);
	}
	function on_submit()
	{
		if($this->check())
		{
			$rows = $this->save_item();
			if(!$this->is_error())
			{
				if(Url::get('cmd')=='edit' and $item = DB::exists_id('partner',Url::get('id')))
				{
					$id = intval(Url::get('id'));
					DB::update_id('partner',$rows,$id);
				}
				else
				{
					$id = DB::insert('partner',$rows);
				}
				$this->save_image($_FILES,$id);
				save_log($id);
				if($id)
				{
					echo '<script>if(confirm("'.Portal::language('update_success_are_you_continous').'")){location="'.Url::build_current(array('cmd'=>'add')).'";}else{location="'.Url::build_current(array('cmd'=>'list','just_edited_id'=>$id)).'";}</script>';
				}
			}
		}
	}
	function draw()
	{
		require_once 'cache/config/status.php';
		require_once Portal::template_js('core').'/tinymce/init_tinyMCE.php';
		$languages = DB::select_all('language');
		if(Url::get('cmd')=='edit' and Url::get('id') and $news = DB::exists_id('partner',intval(Url::get('id'))))
		{
			foreach($news as $key=>$value)
			{
				if(is_string($value) and !isset($_REQUEST[$key]))
				{
					$_REQUEST[$key] = $value;
				}
			}
		}
		$this->parse_layout('edit',array(
			'status_list'=>$status,
			'languages'=>$languages
		));
	}
}
?>
