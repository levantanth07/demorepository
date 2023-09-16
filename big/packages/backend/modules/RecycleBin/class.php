<?php
/******************************
COPY RIGHT BY NYN PORTAL - TCV
WRITTEN BY thedeath
******************************/
class RecycleBin extends Module
{
	function RecycleBin($row)
	{
		Module::Module($row);
		require_once 'db.php';
		require_once 'packages/core/includes/utils/dir.php';
		if(User::can_admin(false,ANY_CATEGORY))
		{
			switch(Url::get('cmd'))
			{
				case 'restore':
					$this->restore(Url::get('path'));
					break;
				default:
					require_once 'forms/list.php';
					$this->add_form(new ListRecycleBinForm());
					break;
			}
		}
		else
		{
			URL::access_denied();
		}
	}
	function restore($file)
	{
		$path = 'backup/recycle bin/';
		$sql = @file_get_contents($path.$file);
		if(DB::query($sql) and Url::get('table') and Url::get('id') and $row = DB::exists_id(Url::get('table'),Url::get('id')))
		{
			if(isset($row['image_url']) and $row['image_url']!='' and $name = substr($row['image_url'],strrpos($row['image_url'],'/')+1)  and file_exists($path.$name))
			{
				@copy($path.$name,$row['image_url']);
				@unlink($path.$name);
			}
			if(isset($row['small_thumb_url']) and $row['small_thumb_url']!='' and $name = substr($row['small_thumb_url'],strrpos($row['small_thumb_url'],'/')+1) and file_exists($path.$name))
			{
				@copy($path.$name,$row['small_thumb_url']);
				@unlink($path.$name);
			}
			if(isset($row['icon_url']) and $row['icon_url']!='' and $name = substr($row['icon_url'],strrpos($row['icon_url'],'/')+1)  and file_exists($path.$name))
			{
				@copy($path.$name,$row['icon_url']);
				@unlink($path.$name);
			}
			@unlink($path.$file);
		}
		Url::redirect_current();
	}
}
?>