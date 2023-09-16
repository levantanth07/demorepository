<?php
class UserDashboardForm extends Form{
	function __construct(){
		Form::Form('UserDashboardForm');
		$this->link_js('packages/core/includes/js/jquery/datepicker.js');
		$this->link_css('assets/default/css/jquery/datepicker.css');
		$this->link_css('assets/default/css/cms.css');
	}
	function draw(){
		$cond = '1 and news.portal_id="'.PORTAL_ID.'" and news.status!="HIDE"';
		$user = DB::fetch_all('select distinct user_id as id ,user_id as name from news where '.$cond);
		if(!Url::get('date_from') and !Url::get('date_to')){
			$cond .= ' and FROM_UNIXTIME(news.time,"%d/%m/%Y")="'.date('d/m/Y',time()).'"';
		}
		if(Url::get('date_from')){
			$cond .= ' and FROM_UNIXTIME(news.time,"%d/%m/%Y")>="'.Url::get('date_from').'"';
		}
		if(Url::get('date_to')){
			$cond .= ' and FROM_UNIXTIME(news.time,"%d/%m/%Y")<="'.Url::get('date_to').'"';
		}
		if(Url::get('user_id')){
			$cond.=' and news.user_id="'.Url::sget('user_id').'"';
		}
		$news = DB::fetch_all('
			select
				news.id,
				news.name_'.Portal::language().' as name,
				category.name_'.Portal::language().' as category_name,
				FROM_UNIXTIME(news.time,"%d/%m/%Y") as time,
				news.user_id
			from
				news
				left outer join news_category on news_category.news_id = news.id
				left outer join category on category.id = news_category.category_id
			where
				'.$cond.'
			order by
				id DESC
		');
		$this->parse_layout('user',array(
			'items'=>$news,
			'total'=>count($news),
			'user_id_list'=>String::get_list($user)
		));
	}
}
?>