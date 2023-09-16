<?php
class SurveyAdminDB{
	static function get_parent()
	{
		return DB::fetch_all('
			SELECT
				id,
				name_'.Portal::language().' as name
			FROM
				survey
			WHERE
				portal_id = "'.PORTAL_ID.'"
				and is_parent = 1
		');
	}
}
?>