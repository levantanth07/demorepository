<?php
class NewsDetail extends Module{
	static $item = false;
	function __construct($row){
		if(Url::get('cmd')=='check_ajax'){
			$this->check();
			exit();
		}
		Module::Module($row);
        require_once 'packages/vissale/lib/php/vissale.php';
		require_once 'db.php';
		$this->init();
		require_once 'forms/list.php';
		$this->add_form(new NewsDetailForm());
	}
	function init(){
		if($category_name_id = addslashes(Url::sget('category_name')) and $name_id = addslashes(Url::sget('name_id')) and $item = NewsDetailDB::get_item('news.name_id_'.Portal::language().'="'.$name_id.'" and category.name_id_'.Portal::language().'="'.$category_name_id.'" and news.portal_id="'.PORTAL_ID.'"')){
			$party = DB::fetch('select id,full_name,image_url from party where user_id="'.$item['user_id'].'"');
			$item['full_name'] = $party['full_name'];
			$item['avatar'] = $party['image_url'];
            $item['film_id'] = '';
            $item['film_name_id'] = '';
            $item['film_name'] = '';
            $item['film_pro_point'] = '';
            $item['film_cache_category'] = '';
			NewsDetail::$item = $item;
			$_REQUEST['category_id'] = $item['category_id'];
			Portal::$document_title = htmlentities($item['seo_title']?$item['seo_title']:$item['name']);
			Portal::$meta_keywords = str_replace(array(' ','"','!','\'','"','"'),', ',strip_tags(($item['name']?$item['seo_keywords']:$item['seo_keywords'])));
			Portal::$meta_description = htmlentities(strip_tags(($item['seo_description']?$item['seo_description']:$item['brief'])));
			if($item['image_url']){
				Portal::$image_url = 'http://'.$_SERVER['HTTP_HOST'].'/'.$item['image_url'];
			}else{
				if($item['small_thumb_url']){
					Portal::$image_url = 'http://'.$_SERVER['HTTP_HOST'].'/'.$item['small_thumb_url'];	
				}
			}
		}else{
			header('location:/404.php');
		}
	}
	function check(){
		if($comfirm_code = Url::get('verify_comfirm_code')){
			if($comfirm_code == Session::get('security_code')) echo 'true';
			else echo 'false';
		}
		exit();
	}
}
?>