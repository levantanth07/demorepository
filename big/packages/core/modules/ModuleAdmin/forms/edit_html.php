<?php
class EditModuleHTMLAdminForm extends Form
{
	function __construct()
	{
		Form::Form('EditModuleHTMLAdminForm');
		$this->add('id',new IDType(true,'object_not_exists','module'));
		$this->add('layout',new TextType(true,'invalid_name',0,2550000));
		$this->link_css(Portal::template('core').'/css/tabs/tabpane.css');
	}
	function on_submit()
	{
		if($this->check() and URL::get('confirm_edit'))
		{
			$id = $_REQUEST['id'];
			$row = DB::select('module',$_REQUEST['id']);
			$new_row =
				array(
					'layout'
				);
			$id = $_REQUEST['id'];
			DB::update_id('module', $new_row,$id);
			require_once 'packages/core/includes/portal/update_page.php';
			$pages = DB::fetch_all('select page_id as id from block where module_id="'.$id.'"');
			foreach($pages as $page_id=>$page)
			{
				update_page($page_id);
			}
			if(URL::get('href'))
			{
				URL::redirect_url('?'.URL::get('href'));
			}
			else
			{
				Url::redirect_current(array('package_id'=>$row['package_id'],'just_edited_id'=>$id));
	  		}
		}
	}
	function draw()
	{
		if($row=DB::select('module',URL::sget('id')))
		{
			$this->parse_layout('edit_html',$row);
		}
	}
}
?>