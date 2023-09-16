<?php
class RecycleBinDB
{
	function get_items()
	{
		return get_files_in_dir('backup/recycle bin/','file','',true);
	}
}
?>