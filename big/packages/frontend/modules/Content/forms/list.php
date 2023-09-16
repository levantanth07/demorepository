<?php
class ContentForm extends Form
{
	function ContentForm()
	{
		Form::Form('ContentForm');
		$this->link_css(Portal::template('anshop').'/css/news.css');
	}
	function draw()
	{
		$this->map = array();
		if($name_id = Url::get('name_id') and $category = ContentDB::get_category('category.name_id="'.$name_id.'" and category.portal_id="'.PORTAL_ID.'"')){
			$cond = 'news.portal_id="'.PORTAL_ID.'" and '.IDStructure::child_cond($category['structure_id']);
			$item = ContentDB::get_item($cond);
			if($item){
				DB::update_hit_count('news',$item['id']);
				$this->news_other($item);
			}else{
				$item['name'] = false;
			}
		}elseif(($name_id = Url::get('name_id')) and $item = ContentDB::get_item('news.name_id="'.$name_id.'" and news.portal_id="'.PORTAL_ID.'"')){
			DB::update_hit_count('news',$item['id']);
			$this->news_other($item);
		}else{
			$item['name'] = false;
		}
		require_once 'packages/core/includes/utils/format_text.php';
		$this->parse_layout('list',$item+$this->map);
	}
	function news_other($item){
		$this->map['item_related'] = ContentDB::get_items('news.portal_id="'.PORTAL_ID.'" and '.IDStructure::child_cond(DB::structure_id('category',$item['category_id'])).' and news.time<='.$item['time'].' and news.status!="HIDE" and news.id<>'.$item['id']);
		$this->map['item_newer'] = ContentDB::get_items('news.portal_id="'.PORTAL_ID.'" and '.IDStructure::child_cond(DB::structure_id('category',$item['category_id'])).' and news.time>='.$item['time'].' and news.status!="HIDE" and news.id<>'.$item['id']);
	}
}
?>
