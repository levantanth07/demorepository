<?php
class EditMediaAdminForm extends Form{
	function EditMediaAdminForm(){
		Form::Form('EditMediaAdminForm');
		$languages = DB::fetch_all('select id,name,icon_url from language where active=1');
		foreach($languages as $language){
			$this->add('name_'.$language['id'],new TextType(true,'invalid_name_'.$language['id'],0,2000));
		}
		$this->add('category_id',new TextType(true,'invalid_category',0,2000));
		$this->link_css('assets/default/css/cms.css');
	}
	function save_item(){
		$rows = array();
		$languages = DB::fetch_all('select id,name,icon_url from language where active=1');
		foreach($languages as $language){
			$rows += array('name_'.$language['id']=>Url::get('name_'.$language['id'],1));
			$rows += array('description_'.$language['id']=>Url::get('description_'.$language['id'],1));
			
			$rows += array('seo_title_'.$language['id'].''=>Url::get('seo_title_'.$language['id'].''));
			$rows += array('seo_keywords_'.$language['id'].''=>Url::get('seo_keywords_'.$language['id'].''));
			$rows += array('seo_description_'.$language['id'].''=>Url::get('seo_description_'.$language['id'].''));
		}
		require_once 'packages/core/includes/utils/search.php';
		require_once 'packages/core/includes/utils/vn_code.php';
		$rows += array(
			'status'
			,'type'=>Url::get('type')
			,'tags'
			,'url'
			,'embed'
			,'category_id'
			,'hitcount'
			,'position'
			,'user_id'=>Session::get('user_id')
			,'portal_id'=>PORTAL_ID
		);
		foreach($languages as $language){
			$name_id = convert_utf8_to_url_rewrite($rows['name_'.$language['id']]);
			if(Url::get('id') and !DB::fetch('select name_id_'.$language['id'].' from media where name_id_'.$language['id'].'="'.$name_id.'" and id!='.intval(Url::get('id')))){
				$rows+=array('name_id_'.$language['id']=>$name_id);
			}else{
				$rows+=array('name_id_'.$language['id']=>$name_id.'_'.date('i-h',time()));
			}
		}
		return ($rows);
	}
	function save_image($file,$id){
		require_once 'packages/core/includes/utils/upload_file.php';
		$dir = substr(PORTAL_ID,1).'/media/'.date('dmY');
		update_upload_file('image_url',$dir);
		//update_upload_file('image_url', $dir,'IMAGE',false,281,218,true);
		update_upload_file('small_thumb_url', $dir,'IMAGE',false,281,218,true);
		if(isset($_REQUEST['image_url']) and $_REQUEST['image_url']){
			create_thumb($_REQUEST['image_url'],str_replace('image_url','small_thumb_url',$_REQUEST['image_url']),281,218, true);
		}
		
		$row = array();
		if(Url::get('image_url')!=''){
			$row +=array('image_url');
		}
		DB::update_id('media',$row,$id);
		//Upload nhieu anh
		$a = multi_upload_file('image_url_detail',$dir,'IMAGE');
        if(!empty($a)){
			foreach ($a as $key => $value){
				$b=$value['value'];
				//System::debug($value);
				 $id2=DB::insert('media_image',array(
						 'media_id'=>$id
						,'image_url'=>$b
						,'time' => time()
				));
					if(Url::get('image_url_detail')!=''){
						$row2 = array_merge($row,array('image_url'=>$b));
						//$row2 = array_merge($row,array('small_thumb_url'=>$value['small_thumb_url']));
					}
					DB::update_id('media_image',$row2,$id);
			}
		}
		//System::debug($row); exit();
		//DB::update_id('product_image',$row,$id);
	}
	function on_submit(){
		if($this->check()){
			$rows = $this->save_item();
			if(Url::get('cmd')=='edit' and $item = DB::exists_id('media',Url::get('id'))){
				$id = intval(Url::get('id'));
				$rows += array('last_time_update'=>time());
				DB::update_id('media',$rows,$id);
			}
			else{
				$rows += array('time'=>time());
				$id = DB::insert('media',$rows);
			}
			$this->save_image($_FILES,$id);
			//exit();
			//save_log($id);
			if($id){
				echo '<script>if(confirm("'.Portal::language('update_success_are_you_continous').'")){location="'.Url::build_current(array('cmd'=>'add')).'";}else{location="'.Url::build_current(array('cmd'=>'list','just_edited_id'=>$id)).'";}</script>';
			}
		}
	}
	function draw(){
		require_once 'cache/config/status.php';
		$languages = DB::fetch_all('select id,name,icon_url from language where active=1');
		if(Url::get('cmd')=='edit' and Url::get('id') and $news = DB::exists_id('media',intval(Url::get('id')))){
			foreach($news as $key=>$value){
				if(is_string($value) and !isset($_REQUEST[$key])){
					$_REQUEST[$key] = $value;
				}
			}
			//$_REQUEST['product_id'] = $news['item_id'];
		}
		$this->parse_layout('edit',array(
			'status_list'=>array(""=>"--/--")+$status,
			'languages'=>$languages,
			'category_id_list'=>array(""=>"--/--")+String::get_list(MediaAdminDB::get_category()),
			'product_id_list'=>array(""=>"--/--")+String::get_list(MediaAdminDB::get_product()),
		));
	}
}
?>
