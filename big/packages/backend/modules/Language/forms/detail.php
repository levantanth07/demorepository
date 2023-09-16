<?php
class LanguageForm extends Form
{
	function __construct()
	{
		Form::Form("LanguageForm");
		$this->add('id',new IDType(true,'object_not_exists','language'));
		$this->link_css(Portal::template('core').'/css/category.css');
	}
	function on_submit()
	{
		if($this->check() and URL::get('confirm') and $_REQUEST['id']!=1)
		{
			$this->delete($this,$_REQUEST['id']);
			Language::update_portal_language();
			Url::redirect_current();
		}
	}
	function draw()
	{
		DB::query('
			select
				`language`.id
				,`language`.`code` ,`language`.`name`
			from
			 	`language`
			where
				`language`.id = "'.URL::sget('id').'"');
		if($row = DB::fetch())
		{
		}
		$this->parse_layout('detail',$row);
	}
	function delete(&$form,$id)
	{
		$row = DB::select('language',$id);
		@unlink('cache/language_'.$id.'.php');
		DB::delete_id('language', $id);
	}
}
?>