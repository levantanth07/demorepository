<?php
class CloneLib
{
	static function clone_portal($old_portal, $new_name, $overwrite_setting = true)
	{
		$from = str_replace('#','',$old_portal['portal_id']);
		$to = str_replace('#','',$new_name);
		if(!DB::select('account','id="#'.$to.'"'))
		{
			DB::insert('account',array(
				'id' => '#'.$to,
				'type' => 'PORTAL',
				'is_active' =>1,
				'create_date' => date('Y-m-d',time())
			));
		}
		unset($old_portal['id']);
		$old_portal['portal_id'] = '#'.$to;
		$old_portal['user_id'] = Session::get('user_id');
		$old_portal['time'] = time();
		if($party = DB::select('party','type="PORTAL" and portal_id="#'.$to.'"'))
		{
			DB::update('party',array('image_url'=>$old_portal['image_url']),'id="'.$party['id'].'"');
		}
		else
		{
			DB::insert('party',$old_portal);
		}
		$pages = DB::fetch_all('
			SELECT
				*
			FROM
				page
			WHERE
				params="portal='.$from.'"
				and package_id<>278
		');
		$old_pages = DB::fetch_all('
			SELECT
				*
			FROM
				page
			WHERE
				params="portal='.$to.'"
		');
		if($old_pages)
		{
			foreach($old_pages as $page_)
			{
				CloneLib::unclone_page($page_);
			}
		}
		//System::debug($old_pages);exit();
		foreach($pages as $page)
		{
			//CloneLib::un_clone_page($page);
			$new_params = str_replace('portal='.$from,'portal='.$to,$page['params']);
			if(!DB::select('page','name="'.$page['name'].'" and params="'.$new_params.'"'))
			{
				CloneLib::clone_page($page,$page['name'],$new_params);
			}
		}
		if($overwrite_setting)
		{
			CloneLib::copy_account_setting($from, $to);
		}
		//CloneLib::copy_menu($from, $to);
		CloneLib::copy_category($from, $to);
		CloneLib::copy_type($from, $to);
		CloneLib::copy_folder('resources/'.$from,'resources/'.$to);
		CloneLib::copy_folder('packages/enterprises/packages/example/assets/default/images/'.$from,'packages/enterprises/packages/example/assets/default/images/'.$to);
		//CloneLib::copy_service($from, $to);
	}
	static function copy_menu($from,$to)
	{
		if($menus = DB::select_all('menu','portal_id="#'.$from.'"'))
		{
			foreach($menus as $id=>$menu)
			{
				unset($menu['id']);
				$menu['portal_id'] = '#'.$to;
				$new_id = DB::insert('menu',$menu);
				DB::query('
					INSERT INTO
						menu_item(`menu_id`, `title_1`, `tooltip_1`, `title_2`, `tooltip_2`, `href`, `params`, `structure_id`, `image_url`, `condition`, `position`, `level`)
					SELECT
						"'.$new_id.'", `title_1`, `tooltip_1`, `title_2`, `tooltip_2`, `href`, `params`, `structure_id`, `image_url`, `condition`, `position`, `level`
					FROM
						menu_item
					WHERE
						menu_id="'.$id.'"
				');
				DB::query('
					UPDATE
						block_setting,block,page
					SET
						block_setting.value="'.$new_id.'"
					WHERE
						block_id=block.id
						and page_id=page.id
						and page.params="portal='.$to.'"
						and setting_id="5333_menu_id"
						and block_setting.value="'.$id.'"
				');
			}
		}
	}
	static function copy_type($from,$to)
	{
		DB::query('
			INSERT INTO
				portal_type(`type`, `portal_id`, `brief`, `template_id`)
			SELECT
				`type`, "#'.$to.'", `brief`, `template_id`
			FROM
				portal_type
			WHERE
				portal_id="#'.$from.'"
		');
	}
	static function update_setting($from,$to, $setting)
	{
		if($value = DB::fetch('
			SELECT
				value
			FROM
				account_setting
			WHERE
				account_id="#'.$from.'"
				and setting_id = "'.$setting.'"
		','value'))
		{
			DB::update('account_setting',array('value'=>$value),'account_id="#'.$to.'" and setting_id = "'.$setting.'"');
		}
	}
	static function copy_category($from,$to)
	{
		if(DB::select('category','portal_id="#'.$to.'"'))
		{
			DB::query('delete from category where portal_id="#'.$to.'"');
		}
		DB::query('
			INSERT INTO
				category(`is_visible`, `name_1`, `name_3`, `name_2`, `description_1`, `description_3`, `description_2`, `type`, `structure_id`, `image_url`, `icon_url`, `url`, `admin_url`, `total_item`, `template_id`, `portal_id`, `original_id`, `status`)
			SELECT
				`is_visible`, `name_1`, `name_3`, `name_2`, `description_1`, `description_3`, `description_2`, `type`, `structure_id`, `image_url`, `icon_url`, `url`, `admin_url`, `total_item`, `template_id`, "#'.$to.'", `original_id`, `status`
			FROM
				category
			WHERE
				portal_id="#'.$from.'"
		');
		//CloneLib::update_navigation($from,$to);
	}
	static function update_navigation($from,$to)
	{
		$categories = DB::fetch_all('
			SELECT
				block_setting.*, category.structure_id
			FROM
				block_setting
				INNER JOIN block on block.id = block_id
				INNER JOIN page on page.id = block.page_id
				INNER JOIN category on category.id = block_setting.value
			WHERE
				page.params="portal='.$from.'"
				and setting_id = "5333_category_id"
		');
		foreach($categories as $category)
		{
			if($new_category_id = DB::fetch('
				SELECT
					id
				FROM
					category
				WHERE
					structure_id="'.$category['structure_id'].'"
					and portal_id="#'.$to.'"
			','id'))
			{
				DB::query('
					UPDATE
						block_setting, block, page
					SET
						block_setting.value = "'.$new_category_id.'"
					WHERE
						block.id = block_id
						and page.id = block.page_id
						and page.params="portal='.$to.'"
						and setting_id = "5333_category_id"
						and block.value = "'.$category['value'].'"
				');
			}
		}
	}
	static function clone_page($page, $new_name, $new_params)
	{
		$old_page_id = $page['id'];
		$page['name'] =  DB::escape(DataFilter::removeXSSinHtml($new_name));
		$page['params'] = $new_params;
		$page['cachable'] = 0;
        $page['is_use_sapi'] = 0;

		unset($page['id']);
		if($new_page_id=DB::insert('page', $page))
		{
			if($blocks = DB::fetch_all('select * from block where page_id='.$old_page_id.' order by container_id'))
			{
				$match_blocks = array();
				foreach($blocks as $old_block_id=>$block)
				{
					if($block['container_id'] and isset($match_blocks[$block['container_id']]))
					{
						$block['container_id'] = $match_blocks[$block['container_id']];
					}
					unset($block['id']);
					$block['page_id'] = $new_page_id;
					if($new_block_id=DB::insert('block',$block))
					{
						$match_blocks[$old_block_id] = $new_block_id;
						DB::query('insert block_setting(block_id, value, setting_id) select '.$new_block_id.',value, setting_id from block_setting where block_id='.$old_block_id);
					}
				}
			}
			return true;
		}
	}
	static function copy_account_setting($from, $to)
	{
		if(DB::fetch('select id,value from account_setting where account_id="#'.$to.'"'))
		{
			DB::query('delete from account_setting where account_id="#'.$to.'"');
		}
		DB::query('
			insert into
				account_setting(account_id, value, setting_id)
			select
				"#'.$to.'",
				value,
				setting_id
			from
				account_setting
			where
				account_id="#'.$from.'"
		');
		$settings = DB::fetch_all('
			select
				account_setting.id,account_setting.value
			from
				account_setting
				inner join setting on setting.id = account_setting.setting_id
			where
				account_setting.account_id="#'.$to.'" and setting.type="IMAGE"'
		);
		foreach($settings as $key=>$value)
		{
			DB::update('account_setting',array('value'=>str_replace('resources/'.$from,'resources/'.$to,$value['value'])),'id='.$key);
		}
	}
	static function copy_service($from, $to)
	{
		DB::query('
			insert into
				service_lease(`service_id`, `portal_id`, `start_date`, `end_date`, `price`, `total`, `note`, `status`)
			select
				`service_id`,
				"#'.$to.'", `start_date`, `end_date`, `price`, `total`, `note`, `status`
			from
				service_lease
			where
				portal_id="#'.$from.'"
		');
	}
	function copy_folder($dir_name,$copy_to)
	{
		if(!is_dir($copy_to))
		{
			mkdir($copy_to);
			if(is_dir($dir_name))
			{
				$dir_handle = opendir($dir_name);
				while($file = readdir($dir_handle))
				{
					if ($file != "." && $file != ".." && $file!='items' && $file!='upload')
					{
						if (!is_dir($dir_name."/".$file))
						{
							copy($dir_name."/".$file,$copy_to."/".$file);
						}
						else
						{
							CloneLib::copy_folder($dir_name."/".$file,$copy_to."/".$file);
						}
					}
				}
			}
		}
	}
	static function unclone_portal($id)
	{
		$id=str_replace('#','',$id);
		$pages = DB::fetch_all('
			SELECT
				*
			FROM
				page
			WHERE
				params="portal='.$id.'"
		');
		foreach($pages as $page)
		{
			CloneLib::unclone_page($page);
		}
		CloneLib::unclone_service($id);
		CloneLib::unclone_type($id);
		CloneLib::unclone_menu($id);
	}
	static function unclone_page($page)
	{
		if($blocks = DB::fetch_all('select * from block where page_id='.$page['id']))
		{
			foreach($blocks as $block_id=>$block)
			{
				DB::delete('block_setting','block_id='.$block_id);
				DB::delete('block','id='.$block_id);
			}
		}
		DB::delete('page','id='.$page['id']);
	}
	static function unclone_service($id)
	{
		DB::delete('service_lease','portal_id="#'.$id.'"');
	}
	static function unclone_type($id)
	{
		DB::delete('portal_type','portal_id="#'.$id.'"');
	}
	static function unclone_menu($id)
	{
		DB::query('
			DELETE FROM
				menu_item
			USING
				menu,menu_item
			WHERE
				menu.id=menu_id
				and portal_id="#'.$id.'"
		');
		DB::delete('menu','portal_id="#'.$id.'"');
	}
}
?>