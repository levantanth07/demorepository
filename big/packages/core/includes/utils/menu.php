<?php
/******************************
COPY RIGHT BY NYN PORTAL - TCV
WRITTEN BY vuonggialong
******************************/
function array2tree(&$items,$items_name)
{
	$parents = array();
	$show_items = array();
	foreach($items as $item)
	{
		if($item['level']<=1)
		{
			$show_items[$item['id']] = $item+array($items_name=>array());
			$parents[1] = $item['id'];
		}
		else
		{
			$st = '';
			for($i=$item['level']-1;$i>=1;$i--)
			{
				if(isset($parents[$i]))
				{
					$st = '['.$parents[$i].'][\''.$items_name.'\']'.$st;
				}
			}
			eval('$show_items'.$st.'['.$item['id'].'] = array(\'id\'=>$item[\'id\'],\'href\'=>$item[\'href\'],\'image_url\'=>isset($item[\'image_url\'])?$item[\'image_url\']:\'\',\'title\'=>$item[\'title\'],$items_name=>array());');
		}
	}
	return $show_items;
}
?>