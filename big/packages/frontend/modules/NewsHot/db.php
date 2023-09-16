<?php
class NewsHotDB{
	function get_news($cond,$limit='0,1')
	{
		$sql ='
			SELECT
				id,
				name_'.Portal::language().' as name,
				brief_'.Portal::language().' as brief,
				description_'.Portal::language().' as description,
				image_url,
				small_thumb_url,
				name_id
			FROM
				news
			WHERE
				'.$cond.'
			ORDER BY
				position DESC, id DESC
			LIMIT
				'.$limit.'
		';
		return DB::fetch_all($sql);
	}
}
?>
