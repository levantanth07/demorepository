<?php
class NewsDB
{
	static function get_news($cond, $item_per_page)
	{
		$items = DB::fetch_all('
			SELECT
				news.id
                ,news.name_id_' . Portal::language() . ' as name_id
				,news.publish
				,news.front_page
				,news.status
				,news.position
				,news.user_id
				,news.image_url
				,news.small_thumb_url
				,news.time
				,news.hitcount
				,news.name_' . Portal::language() . ' as name
				,news.brief_' . Portal::language() . ' as brief
				,news.description_' . Portal::language() . ' as description
				,category.name_id_' . Portal::language() . ' as category_name_id
				,category.name_' . Portal::language() . ' as category_name
				,category.structure_id
			FROM
				news
				INNER JOIN news_category on news_category.news_id = news.id
				INNER JOIN category on category.id = news_category.category_id
			WHERE
				' . $cond . '
			ORDER BY news.position DESC,news.id DESC
				LIMIT 0,15
		');
		foreach ($items as $key => $value) {
			$items[$key]['brief'] = MiString::display_sort_title(strip_tags($value['brief']), 40);
		}
		return ($items);
	}

	static function get_total_news($cond, $item_per_page, $order = 'news.position DESC,news.id DESC')
	{
		$sql = '
			SELECT
				news.id
                ,news.name_id_' . Portal::language() . ' as name_id
				,news.publish
				,news.front_page
				,news.status
				,news.position
				,news.user_id
				,news.image_url
				,news.small_thumb_url
				,news.time
				,news.hitcount
				,news.name_' . Portal::language() . ' as name
				,news.brief_' . Portal::language() . ' as brief
				,news.description_' . Portal::language() . ' as description
				,category.name_' . Portal::language() . ' as category_name
				,category.structure_id
				,category.name_id_' . Portal::language() . ' as category_name_id
			FROM
				news
				INNER JOIN news_category on news_category.news_id = news.id
				INNER JOIN category on category.id = news_category.category_id
			WHERE
				' . $cond . '
				AND news.portal_id="' . PORTAL_ID . '"
			ORDER BY
				' . $order . '
			LIMIT
				' . ((page_no() - 1) * $item_per_page) . ',' . $item_per_page . '
		';
		
		$sql = self::formatQuery($sql);
		$items = DB::fetch_all($sql);
		foreach ($items as $key => $value) {
			/*$img_src = $value['small_thumb_url'];
            $new_img_src = str_replace(['https://node4.tuha.vn/','https://node5.tuha.vn/','https://tuha.vn/'],'',$img_src);
            if(!file_get_contents($img_src)){
                if(file_get_contents('https://node4.tuha.vn/'.$new_img_src)){
                    $new_img_src = 'https://node4.tuha.vn/'.$new_img_src;
                }else{
                    $new_img_src = 'https://node5.tuha.vn/'.$new_img_src;
                }
            }
            $items[$key]['small_thumb_url'] = $new_img_src;*/
			$items[$key]['brief'] = MiString::display_sort_title(strip_tags($value['brief']), 40);
		}
		return ($items);
	}

	static function get_total_item($cond)
	{
		$query = '
			SELECT
				count(*) as acount
			FROM
				news
				INNER JOIN news_category on news_category.news_id = news.id
				INNER JOIN category on category.id = news_category.category_id
			WHERE
				' . $cond . '
		';

		$query = self::formatQuery($query);
		return DB::fetch($query);
	}

	static function get_category($cond)
	{
		$sql = '
			SELECT
				id,
				name_' . Portal::language() . ' as name,
				name_id_' . Portal::language() . ' as name_id,
				url,
				structure_id,
				image_url
			FROM
				category
			WHERE
				' . $cond . '
			ORDER BY
				structure_id
			';

		$items = DB::fetch_all($sql);
		foreach ($items as $key => $value) {
			$items[$key]['items'] = DB::fetch_all('
				SELECT
					id,
					name_' . Portal::language() . ' as name,
					name_id_' . Portal::language() . ' as name_id,
					url,
					structure_id
				FROM
					category
				WHERE
					 ' . IDStructure::child_cond($value['structure_id']) . ' and id!=' . $value['id'] . '
				ORDER BY
					position DESC, id ASC
			');
		} //end foreach

		return $items;
	}

	static function get_news_category_with_structure($category_id)
	{
		//danh muc tin tuc
		$cond = 'type="NEWS" and portal_id="' . PORTAL_ID . '" and ' . IDStructure::direct_child_cond(DB::structure_id('category', $category_id));
		return self::get_category($cond);
	}

	static function get_category_by_slug(string $slug): array
	{
		$portalLang = Portal::language();
		$query = "
			SELECT
				id,
				name_id_$portalLang as name_id,
				name_$portalLang AS name
			FROM category
			WHERE name_id_$portalLang = '$slug'";

		return DB::fetch($query);
	}

	/**
	 * string function
	 *
	 * @param string $query
	 * @return string
	 */
	static function formatQuery(string $query): string
	{
		return trim(preg_replace('!\s+!', ' ', $query));
	}

	static function getRelatedNews(int $category_id)
	{
		//tin lien quan
		$cond = 'news.type="NEWS" and news.publish=1 and news.time <= '
			. strtotime(date('Y-m-d'))
			. ' and news.status="SHOW" and '
			. IDStructure::child_cond(DB::structure_id('category', $category_id));

		return self::get_total_news($cond, 5, 'news.hitcount DESC');
	}
}
