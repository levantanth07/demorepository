<?php
class FetchTemplateDB
{
	static function get_category($type='NEWS')
	{
		$sql='
		SELECT
			`category`.id
			,`category`.`name_'.Portal::language().'` as name
			,`category`.structure_id ,status
		FROM
			`category`
		WHERE
			portal_id="'.PORTAL_ID.'" and category.status<>"HIDE"
			and type="'.$type.'"
		ORDER BY
			 structure_id';
		return DB::fetch_all($sql);
	}
	function cut_string($content,$pattern_start,$pattern_end)
	{
		if($start=strpos($content,$pattern_start)+strlen($pattern_start) and $finish=strpos($content,$pattern_end,$start))
		{
			return substr($content,$start,$finish-$start);
		}
		return false;
	}
}
?>
