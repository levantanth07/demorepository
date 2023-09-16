<table cellpadding="0" cellspacing="0" width="100%" <?php if($table_css = Module::get_setting('table_css')){echo 'style="'.$table_css.'"';}?>><tr valign="top">
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
		}
		else
		if(!$customize)
		{
			$width = round(100/sizeof($columns)).'%';
		}
		else
		{
			$width = '';
		}
		$style='';
		if($width)
		{
			if(preg_match('/\(([^\)]+)\)/',$width,$patterns))
			{
				$style=' style="'.$patterns[1].'"';
			}
			$width = ' width="'.preg_replace('/\([^\)]+\)/','',$width).'"';
		}
		echo '<td '.$width.' align="left"'.$style.'>';
		Module::get_sub_regions($column);
		echo '</td>';
	}
}
?>
</tr></table>