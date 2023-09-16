<?php
class FaqListForm extends Form
{
	function FaqListForm()
	{
		Form::Form('FaqListForm');
	}
	function draw()
	{
		$this->map = array();
		$cond = 'news.type="FAQ" and news.status!="HIDE" and news.portal_id="'.PORTAL_ID.'"';
		if(Url::sget('page')=='trang-chu' or !Url::sget('page'))
		{
			$cond .= ' and news.status="HOME"';
		}
		$this->map['category_name'] = Portal::language('FAQ');
		if(Url::get('category_id') and $category = DB::select_id('category',intval(Url::sget('category_id'))))
		{
			$this->map['category_name'] = $category['name_'.Portal::language()];
			$cond.= ' and '.IDStructure::child_cond($category['structure_id']);
		}
		$item_per_page = 20;
		require_once'packages/core/includes/utils/paging.php';
		$count = FaqListDB::get_total_item($cond);
		$this->map['paging'] = paging($count['acount'],$item_per_page,3,false,'page_no',array('category_id'),Portal::language('page'));
		$this->map['faqs'] = FaqListDB::get_faq($cond,$item_per_page);
		$this->parse_layout('list',$this->map);
	}
}
?>