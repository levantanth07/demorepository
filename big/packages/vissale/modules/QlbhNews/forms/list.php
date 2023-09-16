<?php
class QlbhNewsForm extends Form
{
	function __construct()
	{
		Form::Form('QlbhNewsForm');
	}
	function draw()
	{
		$this->map = array();
    $item_per_page = 5;
		if(Url::get('name_id') and $row = DB::fetch('SELECT id,name_id,name_'.Portal::language().' AS name FROM category WHERE name_id ="'.Url::get('name_id').'"')){
			$category_id = $row['id'];
			$this->map['category_name'] = $row['name'];
			$this->map['category_name_id'] = $row['name_id'];
		}else{
			$category_id = 216;
			$this->map['category_name'] = 'Tin tức';
			$this->map['category_name_id'] = '';
		}
		if(Url::iget('category_id') and $row = DB::fetch('SELECT id,name_id,name_'.Portal::language().' AS name FROM category WHERE id ="'.Url::iget('category_id').'"')){
			$category_id = $row['id'];
			$this->map['category_name'] = $row['name'];
			$this->map['category_name_id'] = $row['name_id'];
		}
		$cond='type="NEWS" and portal_id="'.PORTAL_ID.'" and '.IDStructure::direct_child_cond( DB::structure_id('category',216));
		$this->map['categories'] = QlbhNewsDB::get_categories($cond);
		$layout = 'list';
		require_once'packages/core/includes/utils/paging.php';
    //Truy van cac tin tuc con-slide-tabs
		$cond='news.type="NEWS" and news.publish=1 and news.status="HOT" and '.IDStructure::child_cond( DB::structure_id('category',$category_id));
		$this->map['news'] = QlbhNewsDB::get_news($cond,$item_per_page);
		//System::debug($this->map['news']);
		//Truy van tat ca cac tin tuc
		if(Url::get('page')=='our-partners'){
			$category_id = 586;
			$layout = 'partner';
		}
		$item_per_page = 20;
		$cond='
			news.type="NEWS" 
			and news.publish=1 
			and (news.status!="HIDE")
			'.(Url::get('keyword')?' AND (news.name_1 like "%'.Url::get('keyword').'%" or news.name_3 like "%'.Url::get('keyword').'%" or news.description_1 like "%'.Url::get('keyword').'%" or news.description_3 like "%'.Url::get('keyword').'%")':'').'
			'.(Url::get('tag')?' AND (news.tags like "%'.Url::get('tag').'%")':'').'
			and '.IDStructure::child_cond( DB::structure_id('category',$category_id))
		;
		$count = QlbhNewsDB::get_total_item($cond);
		$this->map['paging'] = paging($count['acount'],$item_per_page,5,REWRITE,'page_no',array('name_id'),'page');
		$this->map['news_all'] =QlbhNewsDB::get_total_news($cond,$item_per_page);
    $this->parse_layout($layout,$this->map);
		//System::debug($this->map['news']);
	}
}
?>