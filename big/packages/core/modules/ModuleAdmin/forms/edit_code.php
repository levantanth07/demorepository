<?php
class EditModuleCodeAdminForm extends Form
{
	function __construct()
	{
		Form::Form('EditModuleCodeAdminForm');
		$this->add('id',new IDType(true,'object_not_exists','module'));
		//$this->add('code',new TextType(true,'invalid_name',0,2550000));
		//$this->add('layout',new TextType(true,'invalid_name',0,2550000));
		$this->link_css('assets/default/css/tabs/tabpane.css');
	}
	function on_submit()
	{
		if($this->check() and URL::get('confirm_edit'))
		{
			$id = $_REQUEST['id'];
			$row = DB::select('module',$id);
			if(URL::get('files'))
			{
				foreach($_REQUEST['files'] as $name=>$content)
				{
					$f = fopen($row['path'].$name,'w+');
					fwrite($f,$content);
					fclose($f);
				}
			}
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
				Url::redirect_current(array('cmd','id','package_id'=>$row['package_id'],'just_edited_id'=>$id));
	  		}
		}
	}
	function draw()
	{
		if($row=DB::select('module',URL::sget('id')))
		{
			$files = array(
				'class'=>array('name'=>'class','files'=>array()),
				'forms'=>array('name'=>'forms','files'=>array()),
				'layouts'=>array('name'=>'layouts','files'=>array())
			);
			$i = 0;
			$dir = opendir($row['path']);
			while($file = readdir($dir))
			{
				if(strpos($file,'.php'))
				{
					$i++;
					$files['class']['files'][$file] = array('id'=>$i, 'name'=>str_replace('.php','',$file),'path'=>$file,'content'=>$this->get_file_contents($row['path'].$file));
				}
			}
			if($dir = @opendir($row['path'].'layouts'))
			{
				while($file = readdir($dir))
				{
					if(strpos($file,'.php'))
					{
						$i++;
						$files['layouts']['files'][$file] = array('id'=>$i, 'name'=>'l/'.str_replace('.php','',$file),'path'=>'layouts/'.$file,'content'=>$this->get_file_contents($row['path'].'layouts/'.$file));
					}
				}
			}
			if($dir = @opendir($row['path'].'forms'))
			{
				while($file = readdir($dir))
				{
					if(strpos($file,'.php'))
					{
						$i++;
						$files['forms']['files'][$file] = array('id'=>$i, 'name'=>'f/'.str_replace('.php','',$file),'path'=>'forms/'.$file,'content'=>$this->get_file_contents($row['path'].'forms/'.$file));
					}
				}
			}
			$this->parse_layout('edit_code',$row+array('dirs'=>$files));
		}
	}
	function get_file_contents($file_name)
	{
		return str_replace(array('<','>'),array('&lt;','&gt;'),str_replace('&','&amp;',file_get_contents($file_name)));
	}
}
?>