<?php
class FAQDetailForm extends Form
{
	function FAQDetailForm()
	{
		Form::Form('FAQDetailForm');
		$this->link_css(Portal::template('longvu').'/css/news.css');
	}
	function on_submit(){
	}
	function draw()
	{
		$cond = 'and news.portal_id="'.PORTAL_ID.'" and  news.status!="HIDE"  and news.type="FAQ"';
		$item = array('id'=>'0','category_id'=>'0');
		$this->map = array();
		$mode = false;
		require_once 'packages/core/includes/utils/format_text.php';
		if($item = FAQDetail::$item)
		{
			$mode = true;
			DB::update_hit_count('news',$item['id']);
			$this->map['item_related'] = FAQDetailDB::get_items('news.portal_id="'.PORTAL_ID.'" and news.type="FAQ" and news.status!="HIDE" and news.parent_id='.$item['parent_id'].' and news.id<>'.$item['id']);
			$category = DB::fetch('select id, name_'.Portal::language().' as category_name,name_id from category where id = '.$item['category_id']);
			$this->map['category_name'] = $category['category_name'];
			$this->map['category_name_id'] = $category['name_id'];
		}
		$this->parse_layout('list',$mode?$item+$this->map:$this->map);
	}
}
?>
