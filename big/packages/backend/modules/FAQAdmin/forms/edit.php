<?php
class EditFAQAdminForm extends Form
{
	function EditFAQAdminForm()
	{
		Form::Form('EditFAQAdminForm');
		$languages = DB::select_all('language');
		foreach($languages as $language)
		{
			$this->add('name_'.$language['id'],new TextType(true,'invalid_name_'.$language['id'],0,2000));
		}
		$this->link_css('assets/default/css/cms.css');
		$this->link_css('assets/default/css/tabs/tabpane.css');
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
			'status'
			,'type'=>'FAQ'
			,'tags'
			,'user_id'=>Session::get('user_id')
			,'portal_id'=>PORTAL_ID
			);
			if(Url::get('position')=='')
			{
				$position = DB::fetch('select max(position)+1 as id from news where type="FAQ"');
				$rows['position'] = $position['id'];
			}
			else
			{
				$rows['position'] = Url::get('position');
			}
			$name_id = convert_utf8_to_url_rewrite($rows['name_1']);
			if(!DB::fetch('select name_id from news where name_id="'.$name_id.'" and portal_id="'.PORTAL_ID.'" and news.type="FAQ"'))
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
					$rows += array('time'=>time());
					$id = DB::insert('news',$rows);
				}
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
		if(Url::get('cmd')=='edit' and Url::get('id') and $news = DB::exists_id('news',intval(Url::get('id'))))
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
