<?php
class EditAdvertismentAdminForm extends Form{
	function EditAdvertismentAdminForm(){
		Form::Form('EditAdvertismentAdminForm');
		$languages = DB::fetch_all('select id,name,icon_url from language where active=1');
		foreach($languages as $language){
			$this->add('name_'.$language['id'],new TextType(true,'invalid_name_'.$language['id'],0,2000));
		}
		//$this->add('url',new TextType(true,'invalid_url',0,2000));
		//$this->add('image_url',new TextType(true,'invalid_image_url',0,2000));
		$this->link_css('assets/default/css/cms.css');
	}
	function save_item(){
		$rows = array();
		$languages = DB::fetch_all('select id,name,icon_url from language where active=1');
		foreach($languages as $language){
			$rows += array('name_'.$language['id']=>Url::get('name_'.$language['id'],1));
			$rows += array('description_'.$language['id']=>Url::get('description_'.$language['id'],1));
		}
		$rows += array(
			'status'
			,'type'=>'ADVERTISMENT'
			,'url'
			,'width'
			,'height'
			,'user_id'=>Session::get('user_id')
			,'portal_id'=>PORTAL_ID
			);
			require_once 'packages/core/includes/utils/vn_code.php';
			$name_id = convert_utf8_to_url_rewrite($rows['name_1']);
			if(Url::get('id') and !DB::fetch('select name_id_1 from media where name_id_1="'.$name_id.'" and id!='.intval(Url::get('id')))){
				$rows+=array('name_id_1'=>$name_id);
			}else{
				$rows+=array('name_id_1'=>$name_id.'_'.date('i-h',time()));
			}
			$name_id = convert_utf8_to_url_rewrite($rows['name_2']);
			if(Url::get('id') and !DB::fetch('select name_id_2 from media where name_id_2="'.$name_id.'" and id!='.intval(Url::get('id')))){
				$rows+=array('name_id_2'=>$name_id);
			}else{
				$rows+=array('name_id_2'=>$name_id.'_'.date('i-h',time()));
			}
		return ($rows);
	}
	function save_image($file,$id){
		require_once 'packages/core/includes/utils/upload_file.php';
		$dir = substr(PORTAL_ID,1).'/media/';
		update_upload_file('image_url',$dir);
		$row = array();
		if(Url::get('image_url')!=''){
			$row['image_url'] =Url::get('image_url');
		}
		DB::update_id('media',$row,$id);
	}
	function on_submit(){
		if($this->check()){
			$rows = $this->save_item();
			if(Url::get('cmd')=='edit' and $item = DB::exists_id('media',Url::get('id'))){
				$id = intval(Url::get('id'));
				$rows += array('last_time_update'=>time());
				DB::update_id('media',$rows,$id);
			}else{
				$rows += array('time'=>time());
				$id = DB::insert('media',$rows);
			}
			$this->save_image($_FILES,$id);	
			save_log($id);
			if($id){
				echo '<script>if(confirm("'.Portal::language('update_success_are_you_continous').'")){location="'.Url::build_current(array('cmd'=>'add')).'";}else{location="'.Url::build_current(array('cmd'=>'list','just_edited_id'=>$id)).'";}</script>';
			}
		}
	}
	function draw(){
		require_once 'cache/config/status.php';
		require_once Portal::template_js('core').'/tinymce/init_tinyMCE.php';
		$languages = DB::fetch_all('select id,name,icon_url from language where active=1');
		if(Url::get('cmd')=='edit' and Url::get('id') and $news = DB::exists_id('media',intval(Url::get('id')))){
			foreach($news as $key=>$value){
				if(is_string($value) and !isset($_REQUEST[$key])){
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
