<?php
class EditNewsAdminForm extends Form
{
	function EditNewsAdminForm()
	{
		Form::Form('EditNewsAdminForm');
		$languages = DB::select_all('language');
		foreach($languages as $language)
		{
			$this->add('name_'.$language['id'],new TextType(true,'invalid_name_'.$language['id'],0,2000));
		}
		$this->add('category_id',new TextType(true,'invalid_category_id',0,2000));
		$this->link_css('assets/default/css/cms.css');
		$this->link_css('assets/default/css/tabs/tabpane.css');
		$this->link_js('assets/default/css/tabs/tabpane.js');
        $this->link_css('assets/default/css/jquery/datepicker.css');
        $this->link_js('packages/core/includes/js/jquery/datepicker.js');
	}
	function save_item()
	{
		$rows = array();
		$languages = DB::select_all('language');
		foreach($languages as $language)
		{
			$rows += array('name_'.$language['id']=>Url::get('name_'.$language['id'],1));
			$rows += array('brief_'.$language['id']=>Url::get('brief_'.$language['id'],1));
			$rows += array('description_'.$language['id']=>Url::get('description_'.$language['id'],1));
		}
		require_once 'packages/core/includes/utils/vn_code.php';
		require_once 'packages/core/includes/utils/search.php';
		$rows['keywords']=extend_search_keywords(convert_utf8_to_telex($rows['name_1'].' '.$rows['brief_1']));
		$rows += array(
			'category_id'
			,'publish'=>Url::get('publish')==1?1:0
			,'hitcount'
			,'status'
			,'type'=>'NEWS'
			,'author'
			,'tags'
			,'portal_id'=>PORTAL_ID
            ,'type_text'=>Url::get('type_text')
            ,'published_date'=> Date_Time::to_time( Url::get('published_date') )
			);
			if(Url::get('position')=='')
			{
				$position = DB::fetch('select max(position)+1 as id from news where type="NEWS"');
				$rows['position'] = $position['id'];
			}
			else
			{
				$rows['position'] = Url::get('position');
			}
			$name_id = convert_utf8_to_url_rewrite($rows['name_1']);
			if(!DB::fetch('select name_id from news where name_id="'.$name_id.'" and portal_id="'.PORTAL_ID.'" and news.type="NEWS"'))
			{
				$rows+=array('name_id'=>$name_id);
			}
			else
			{
				if(Url::get('id') and Url::get('cmd')=='edit')
				{
					$rows+=array('name_id'=>$name_id);
				}
				else
				{
					$this->error('name','duplicate_name');
				}
			}
		return ($rows);
	}
	function save_image($file,$id)
	{
		require_once 'packages/core/includes/utils/upload_file.php';
		$dir = substr(PORTAL_ID,1).'/content';
		update_upload_file('small_thumb_url',$dir);
		update_upload_file('image_url',$dir);
		update_upload_file('file',$dir,'FILE');
		$row = array();
		if(Url::get('small_thumb_url')!='')
		{
			$row = array_merge($row,array('small_thumb_url'));
		}
		if(Url::get('image_url')!='')
		{
			$row = array_merge($row,array('image_url'));
		}
		if(Url::get('file')!='')
		{
			$row = array_merge($row,array('file'));
		}
		DB::update_id('news',$row,$id);
	}
	function on_submit()
	{
		if($this->check())
		{
			$rows = $this->save_item();
			if(!$this->is_error())
			{
				if(Url::get('cmd')=='edit' and $item = DB::exists_id('news',Url::get('id')))
				{
					$id = intval(Url::get('id'));
					$rows += array('last_time_update'=>time());
					DB::update_id('news',$rows,$id);
				}
				else
				{
					$rows += array('time'=>time(),'user_id'=>Session::get('user_id'));
					$id = DB::insert('news',$rows);
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
	   /////////////////////////////////////////////////////
       // NVQ
       // File cache loai van ban
       // 10/02/2012
       require_once 'cache/config/type_text.php';
		require_once Portal::template_js('core').'/tinymce/init_tinyMCE.php';
		require_once 'cache/config/status.php';
		$languages = DB::select_all('language');
		$arr = array('1'=>'YES','0'=>'NO');
		if(Url::get('cmd')=='edit' and Url::get('id') and $news = DB::exists_id('news',intval(Url::get('id'))))
		{
			foreach($news as $key=>$value)
			{
				if(is_string($value) and !isset($_REQUEST[$key]))
				{
                     if ($key=='published_date'){
    			         $_REQUEST[$key] = date("d/m/Y",$value);
    			     }else{
     					$_REQUEST[$key] = $value;
    			     }
				}
			}
		}
		$this->parse_layout('edit',array(
			'category_id_list'=>String::get_list(NewsAdminDB::get_category( IDStructure::direct_child_cond( DB::structure_id('category','523') ))),
			'status_list'=>$status,
			'languages'=>$languages,
			'show_image_list'=>$arr,
			'show_email_list'=>$arr,
			'show_print_list'=>$arr,
			'show_time_list'=>$arr,
			'show_author_list'=>$arr,
			'show_comment_list'=>$arr,
			'front_page_list'=>$arr,
            'type_text_list'=>$type_text
		));
	}
}
?>
