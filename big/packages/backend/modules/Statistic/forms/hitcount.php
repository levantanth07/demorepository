<?php
class HitcountForm extends Form
{
	function HitcountForm()
	{
		Form::Form('HitcountForm');
	}	
	function draw()
	{		
		$cond = '1 and news.type="NEWS" and news.status <> "HIDE"';
		if(Url::get('search_user_id')){
			$cond.= ' and news.user_id="'.Url::get('search_user_id').'"';
		}
		if(Url::get('date_from')){
			$cond.= ' and news.time>="'.Date_Time::to_time(Url::get('date_from')).'"';
		}
		if(Url::get('date_to')){
			$cond.= ' and news.time<"'.(Date_Time::to_time(Url::get('date_to')) + 24*3600).'"';
		}
		require_once 'packages/core/includes/utils/paging.php';
		$total_hitcount = StatisticDB::get_total_hitcount($cond);
		$count = StatisticDB::GetTotal($cond);
		$item_per_page = 50;
		$paging = paging($count['acount'],$item_per_page,10,false,'page_no',array('cmd'));
		$items = StatisticDB::GetItems($cond,'news.hitcount DESC',$item_per_page);
		$i = 1;
		foreach($items as $key=>$value)
		{
			$value['indexs'] = $i++;
			$items[$key] = $value;
		}
		$this->parse_layout('hitcount',array(
			'items'=>$items,
			'paging'=>$paging,
			'total'=>$count['acount'],
			'total_hitcount'=>$total_hitcount,
			'search_user_id_list'=>array(Portal::language('select_user'))+String::get_list(StatisticDB::get_user())
		));
	}
}
?>