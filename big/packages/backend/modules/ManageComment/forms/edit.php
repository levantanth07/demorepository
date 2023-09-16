<?php
class EditManageCommentForm extends Form
{
	function EditManageCommentForm()
	{
		Form::Form('EditManageCommentForm');
		$this->add('title',new TextType(true,'Lỗi nhập tiêu đề',0,2000));
		$this->link_css('assets/default/css/cms.css');
		$this->link_css('assets/default/css/tabs/tabpane.css');
	}
	function save_item()
	{
		$rows = array('title','content','email','phone','full_name');
		require_once 'packages/core/includes/utils/vn_code.php';
		require_once 'packages/core/includes/utils/search.php';
		$rows += array(
		'publish'=>Url::get('publish')?1:0
		,'type'=>'COMMENT'
		,'user_id'=>Session::get('user_id')
		);
		if(Url::get('position')=='')
		{
			$position = DB::fetch('select max(position)+1 as id from comment where type="FAQ"');
			$rows['position'] = $position['id'];
		}
		else
		{
			$rows['position'] = Url::get('position');
		}
		$name_id = convert_utf8_to_url_rewrite(Url::get('title'));
		if(!DB::fetch('select name_id from comment where name_id="'.$name_id.'" and comment.type="COMMENT"'))
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
				$this->error('title','duplicate_name');
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
				if(Url::get('cmd')=='edit' and $item = DB::exists_id('comment',Url::get('id')))
				{
					$id = intval(Url::get('id'));
					DB::update_id('comment',$rows,$id);
				}
				else
				{
					$rows += array('time'=>time());
					$id = DB::insert('comment',$rows);
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
		if(Url::get('cmd')=='edit' and Url::get('id') and $comment = DB::exists_id('comment',intval(Url::get('id'))))
		{
			foreach($comment as $key=>$value)
			{
				if(is_string($value) and !isset($_REQUEST[$key]))
				{
					$_REQUEST[$key] = $value;
				}
			}
		}
		$this->parse_layout('edit');
	}
}
?>
