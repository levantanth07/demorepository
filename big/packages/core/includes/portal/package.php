<?php
$GLOBALS['packages'] = DB::select_all('package',false,'structure_id');
global $packages;
$temp_package = array();
foreach($packages as $package)
{
	$temp_package[$package['structure_id']] = $package;
}
foreach($packages as $key=>$package)
{
	if($package['structure_id'] == ID_ROOT)
	{
		$packages[$key]['path'] = '';
	}
	else
	{
		$packages[$key]['path'] = $packages[$temp_package[IDStructure::parent($package['structure_id'])]['id']]['path'].'packages/'.$package['name'].'/';
	}
}
function get_package_path($package_id)
{
	global $packages;
	return $packages[$package_id]['path'];
}
?>