<?php
class NewsForm extends Form
{
	protected $map;
	function __construct()
	{
		Form::Form('NewsForm');
		//$this->link_css('assets/v3/css/pages/home.css');
		//$this->link_css('https://mobirise.com/bootstrap-template/news-template/assets/theme/css/style.css');
		//$this->link_css('https://mobirise.com/bootstrap-template/news-template/assets/mobirise/css/mbr-additional.css');
	}

	function on_submit()
	{
	}

	/**
	 * initMapData function
	 *
	 * @return void
	 */
	public function initMapData()
	{
		$this->map = [];
		$this->map['category'] = [];
		$this->map['category_name'] = 'Bài viết';
		$this->map['category_name_id'] = 'bai-viet';
		$this->map['tags'] = null;
		$this->map['keyword'] = null;
		$this->map['show_searchbox'] = false;
	}

	/**
	 * draw function
	 *
	 * @return void
	 */
	function draw()
	{
		require_once 'packages/core/includes/utils/paging.php';
		$this->initMapData();
		$category_id = 213;
		$this->map['category'] = NewsDB::get_news_category_with_structure($category_id);

		$category_slug = Url::get('category_name_id');
		$category = NewsDB::get_category_by_slug($category_slug);
		if ($category_slug && $category) {
			$this->renderPostsByCat($category);
		} else {
			$this->renderPosts($category_id);
		} //end if

		$this->parse_layout('list', $this->map);
	}

	/**
	 * renderPosts function
	 *
	 * @param integer $category_id
	 * @param integer $item_per_page
	 * @return void
	 */
	public function renderPosts(int $category_id, int  $item_per_page = 21)
	{
		$time = strtotime(date('Y-m-d'));
		$structure = IDStructure::direct_child_cond(DB::structure_id('category', $category_id));
		$cond = $this->generateDefaultCond($time, $structure);
		$cond .= $this->generateTagCond();
		$cond .= $this->generateKeywordCond();
		$count = NewsDB::get_total_item($cond);
		$this->map['news'] = NewsDB::get_total_news($cond, $item_per_page);
		$this->map['paging'] = paging($count['acount'], $item_per_page, 3, REWRITE, 'page_no', array('name_id', 'category_name_id'));
	}

	/**
	 * renderPostsByCat function
	 *
	 * @param array $category_info
	 * @param integer $cat_related_news
	 * @param integer $item_per_page
	 * @return void
	 */
	public function renderPostsByCat(
		array $category_info,
		int $cat_related_news = 213,
		int $item_per_page = 21
	) {
		$category_id = $category_info['id'];
		$this->map['category_name'] = $category_info['name'];
		$this->map['category_name_id'] = $category_info['name_id'];

		//Truy van tat ca cac tin tuc
		$time = strtotime(date('Y-m-d'));
		$structure = IDStructure::child_cond(DB::structure_id('category', $category_id));
		$cond = $this->generateDefaultCond($time, $structure);
		$cond .= $this->generateTagCond();
		$cond .= $this->generateKeywordCond();
		$count = NewsDB::get_total_item($cond);

		$this->map['paging'] = paging($count['acount'], $item_per_page, 3, REWRITE, 'page_no', array('name_id', 'category_name_id'));
		$this->map['news'] = NewsDB::get_total_news($cond, $item_per_page);
		$this->map['lastest_news'] = NewsDB::getRelatedNews($cat_related_news);
		$this->map['show_searchbox'] = true;
	}

	/**
	 * generateTagCond function
	 *
	 * @return string
	 */
	private function generateTagCond(): string
	{
		if (!$tags = DB::escape(Url::get('tags'))) {
			return '';
		} //end if

		$cond = " AND (news.tags LIKE '%$tags%')";
		$this->map['tags'] = MiString::create_tags($tags, 'bai-viet');
		return $cond;
	}

	/**
	 * generateKeywordCond function
	 *
	 * @return string
	 */
	private function generateKeywordCond(): string
	{
		$maxlength = 60;
		$keyword = self::xss_clean(Url::get('keyword'), $maxlength);
		if (!$keyword) {
			return '';
		} //end if

		$portal_language = Portal::language();
		$cond = "
			AND (
				news.name_$portal_language LIKE '%$keyword%'
				OR news.brief_$portal_language LIKE '%$keyword%'
				OR news.description_$portal_language LIKE '%$keyword%'
			)";

		$this->map['keyword'] = $keyword;
		return $cond;
	}

	/**
	 * generateDefaultCond function
	 *
	 * @param string $time
	 * @param string $structure
	 * @return string
	 */
	private function generateDefaultCond(string $time, string $structure): string
	{
		return "
			news.type='NEWS' 
			AND news.publish = 1 
			AND news.time <= $time
			AND news.status <> 'HIDE'
			AND $structure
		";
	}

	private function xss_clean($data, $limit = 0)
	{
		$data = DB::escape($data);
		if ($limit) {
			$data = substr($data, 0, $limit);
		} //end if

		// Fix &entity\n;
		$data = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $data);
		$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
		$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
		$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

		// Remove any attribute starting with "on" or xmlns
		$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

		// Remove javascript: and vbscript: protocols
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

		// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

		// Remove namespaced elements (we do not need them)
		$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

		do {
			// Remove really unwanted tags
			$old_data = $data;
			$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
		} while ($old_data !== $data);

		// we are done...
		return $data;
	}
}
