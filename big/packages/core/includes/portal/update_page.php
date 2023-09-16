<?php
function update_page($id)
{
	if($page=DB::select('page',$id))
	{
		if($page['params'])
		{
			$full_name = $page['name'].'.'.$page['params'];
		}
		else
		{
			$full_name = $page['name'];
		}
	}
	else
	{
		return;
	}
	$cache_file = ROOT_PATH.'cache/page_layouts/'.$full_name.'.cache.php';
	if(file_exists($cache_file))
	{
		unlink($cache_file);
	}
}
?>