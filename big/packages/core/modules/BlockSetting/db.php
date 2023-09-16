<?php
class BlockSettingDB
{
	static function update_block_name()
	{
		DB::update('block',array('name'=>URL::get('name')),'id="'.URL::get('block_id').'"');
	}
	static function update_block_setting($value,$block_id,$setting_id)
	{
		if(DB::select('block_setting','block_id="'.addslashes($block_id).'" and setting_id="'.addslashes($setting_id).'"'))
		{
			DB::update('block_setting',array('value'=>$value),'block_id="'.addslashes($block_id).'" and setting_id="'.addslashes($setting_id).'"');
		}
		else
		{
			DB::insert('block_setting',
				array(
					'value'=>$value,
					'block_id'=>$block_id,
					'setting_id'=>$setting_id
				)
			);
		}
	}
	static function insert_block_setting($value,$block_id,$setting_id)
	{
		DB::insert('block_setting',
			array(
				'value'=>$value,
				'block_id'=>$block_id,
				'setting_id'=>$setting_id
			)
		);
	}
	static function image_url_is_still_use_by_other_setting($image_url,$block_id, $setting_id)
	{
		return DB::select('block_setting','value="'.$image_url.'" and (block_id<>"'.$block_id.'" or setting_id<>"'.$setting_id.'")');
	}
	static function select_template($template_id)
	{
		return DB::select('template','id="'.addslashes($template_id).'"');
	}
	static function select_block_setting($block_id,$setting_id)
	{
		return DB::select('block_setting','setting_id="'.$setting_id.'" and block_id="'.$block_id.'"');
	}
	static function select_similar_blocks($block)
	{
		return DB::fetch_all('
			SELECT
				block.id, CONCAT(page.params,"(",block.position,") of ",page.name," ",block.region) as name
			FROM
				block
				inner join page on page_id=page.id
			WHERE
				module_id="'.$block['module_id'].'"
				AND block.id<>"'.$block['id'].'"
			ORDER BY
				page.params,
				page.name,
				block.region,
				block.position
		');
	}
	static function select_all_module_setting($module_id)
	{
		return DB::fetch_all('
			SELECT
				`module_setting`.*
			FROM
				`module_setting`
			WHERE
				module_id='.$module_id.'
			ORDER BY
				position,module_setting.name
		');
	}
	static function select_all_block_setting($block_id)
	{
		return DB::fetch_all('
			SELECT
				`block_setting`.setting_id as id
				,`block_setting`.`value`
			FROM
				`block_setting`
			WHERE
				block_id="'.$block_id.'"
		');
	}
}
?>