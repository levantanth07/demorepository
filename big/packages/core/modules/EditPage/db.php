<?php
class EditPageDB
{
	static function update_page_layout($layout, $page_id)
	{
		DB::update('page',array('layout'=>$layout),'id="'.$page_id.'"');
	}
	static function update_block($region,$position,$container_id,$block_id)
	{
		DB::update('block', array('region'=>$region, 'position'=>$position,'container_id'=>$container_id),'id="'.$block_id.'"');
	}
	static function increment_all_after_block_position($page_id,$region,$position,$container_id)
	{
		DB::query('
			update block
			set position = position+1
			where
				page_id='.$page_id.'
				and region="'.$region.'"
				and position>'.$position.'
				and container_id='.$container_id.'
		');
	}
	static function select_all_block_in_container($container_id)
	{
		return DB::fetch_all('
			SELECT
				*
			FROM
				block
			WHERE
				container_id = "'.$container_id.'"
		');
	}
}
?>