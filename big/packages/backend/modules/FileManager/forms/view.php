<?php
class FileManagerForm extends Form
{
	function FileManagerForm()
	{
		Form::Form('FileManagerForm');
		$this->link_css(Portal::template('').'css/file_manager.css');
		$this->link_js('assets/admin/scripts/jquery-1.7.1.js');		
		$this->link_js(Portal::template_js('core').'jquery/jquery.rightClick.js');
		$this->link_js('packages/core/includes/js/tinymce/jscripts/tiny_mce/tiny_mce_popup.js');
		$this->link_js('packages/core/includes/js/tinymce/jscripts/tiny_mce/tiny_mce_popup.js');
	}
	function on_submit()
	{
		if($cmd = Url::get('cmd'))
		{
			$current = urldecode(Url::get('currentFolder'));
			switch($cmd)
			{
				case 'create_folder' :
					$this->CreateFolder();
					break;
				case 'delete':
					$this->Delete();
					break;
				case 'rename':
					$this->Rename();
					break;
				case 'back':
					Session::set('currentFolder',substr($current,0,strrpos($current,'/')));
					break;
				case 'upload':
					$this->upload();
					break;
				case 'view':
					$selected = Url::get('selectedFolder');
					Session::set('currentFolder',$current.'/'.$selected);
					break;
			}
		}
		//Url::redirect_current();
	}
	function draw()
	{
		if(!Session::is_set('currentFolder'))
		{
			Session::set('currentFolder','');
		}
		require_once 'packages/backend/includes/php/file_manager_config.php';
		$dir = $this->RelativePath();
		$item['current'] = Session::get('currentFolder');// default ''
		$list = array();
		if( is_dir($dir) ) {
			$files = scandir($dir);
			$i = 0;
			natcasesort($files);
			if( count($files) > 2 ) {
				foreach( $files as $file ) {
					if( file_exists($dir .'/'. $file) && $file != '.' && $file != '..' && $file != 'Thumbs.db' && is_dir($dir .'/'. $file) ) {
						$i++;
						$list[$i]['name'] = $this->name($file);
						$list[$i]['type'] = 'folder';
						$list[$i]['path'] = $dir .'/'. $file;
					}
				}
				foreach( $files as $file ) {
					if( file_exists($dir .'/'. $file) && $file != '.' && $file != '..' && $file != 'Thumbs.db' && !is_dir($dir .'/'. $file) ) {
						$ext = preg_replace('/^.*\./', '', $file);
						$i++;
						$list[$i]['name'] = $this->name($file);
						$list[$i]['type'] = 'file';
						$list[$i]['ext'] = $this->FileIcon($ext,$IMAGE);
						$list[$i]['src'] = $dir .'/'. $file;
					}
				}
			}
		}else
		{
			mkdir($dir);
		}
		$this->parse_layout('view',$item+array('list'=>$list));
	}
	function FileIcon($file_type,$IMAGE)
	{
		foreach($IMAGE as $key=>$value)
		{
			if(strpos(strtolower($value),strtolower($file_type))!==false)
			{
				return $key;
			}
		}
		return 'file';
	}
	function name($file)
	{
		return $file;
	}
	function CreateFolder()
	{
		if($name = Url::get('folder_name'))
		{
			$dir = $this->RelativePath();
			if(!is_dir( $dir .'/'. $name))
			{
				mkdir($dir .'/'. $name);
			}
		}
	}
	function Delete()
	{
		$dir = $this->RelativePath();
		$dir .= '/'.urldecode(Url::get('selectedFolder'));
		$type = Url::get('type');
		if($type=='folder')
		{
			require_once 'packages/core/includes/utils/dir.php';
			empty_all_dir($dir,true);
		}elseif($type=='file')
		{
			if(is_file($dir))
			{
				unlink($dir);
			}
		}
	}
	function Rename()
	{
		$dir = $this->RelativePath();
		$old_name = urldecode(Url::get('selectedFolder'));
		$new_name = urldecode(Url::get('new_name'));
		$type = Url::get('type');
		if(is_dir($dir . '/' . $old_name) or is_file($dir . '/' . $old_name))
		{
			rename( $dir . '/' . $old_name, $dir . '/' . $new_name);
		}
	}
	function RelativePath()
	{
		return $Folder = 'upload/'.substr(PORTAL_ID,1). Session::get('currentFolder');
	}
	function upload()
	{
		require_once 'packages/core/includes/utils/upload_file.php';
		$dir = $this->RelativePath();
		//update_upload_file('upload',substr($dir,strpos($dir,'/')+1,(strlen($dir)-strpos($dir,'/')-1)),'IMAGE',false,false,false,true);
		multi_upload_file('upload',substr($dir,strpos($dir,'/')+1,(strlen($dir)-strpos($dir,'/')-1)),'IMAGE');
	}
}
?>