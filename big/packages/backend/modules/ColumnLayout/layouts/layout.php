<table cellspacing="0"><tr valign="top">
<?php
if($columns = Module::get_setting('columns'))
{
	$customize = strpos($columns,':');
	$columns = explode(',',$columns);
	foreach($columns as $column)
	{
		if($pos=strpos($column,':'))
		{
			$width = substr($column,$pos+1);
			$column = substr($column,0,$pos);
			$width = ' width="'.$width.'"';
		}
		else if(!$customize)
		{
			$width = ' width="'.round(100/sizeof($columns)).'%"';
		}
		else
		{
			$width = '';
		}
		echo '<td'.$width.'>[[--'.$column.'--]]</td>';
	}
}
?>
</tr></table>