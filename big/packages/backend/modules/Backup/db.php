<?php
class BackupDB
{
	function get_all_tables()
	{
		return DB::fetch_all_array('SHOW TABLE STATUS');
	}
	function get_fields($table)
	{
		return DB::fetch_all_array('DESC '.$table);
	}
}
?>
