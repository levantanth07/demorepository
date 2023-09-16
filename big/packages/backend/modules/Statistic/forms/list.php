<?php
class StatisticForm extends Form{
	function StatisticForm(){
		Form::Form('StatisticForm');
	}
	function get_date($cond){
		return DB::fetch_all('
			select
				count(*) as amount,
				FROM_UNIXTIME(time,"%d") as id
			from
				visit
			where
				portal_id = "'.PORTAL_ID.'"
				'.$cond.'
			group by
				FROM_UNIXTIME(time,"%d-%m-%Y")
			order by
				time ASC
		');
	}
	function draw(){
		$month = array(
			 '01'=>Portal::language('month_1')
			,'02'=>Portal::language('month_2')
			,'03'=>Portal::language('month_3')
			,'04'=>Portal::language('month_4')
			,'05'=>Portal::language('month_5')
			,'06'=>Portal::language('month_6')
			,'07'=>Portal::language('month_7')
			,'08'=>Portal::language('month_8')
			,'09'=>Portal::language('month_9')
			,'10'=>Portal::language('month_10')
			,'11'=>Portal::language('month_11')
			,'12'=>Portal::language('month_12')
			);
		$year = array();
		for($i=2000;$i<(date('Y')+10);$i++){
			$year[$i] = $i;
		}
		$total_page_view = Counter::count_visitor();
		$date_page_view = Counter::count_visitor('d');
		$month_page_view = Counter::count_visitor('m');
		$year_page_view = Counter::count_visitor('y');
		$user_online = Counter::count_visitor('n');
		$list = Counter::user_online();
		$cond  = ' and FROM_UNIXTIME(time,"%m-%Y") = "'.Url::get('month',date('m')).'-'.Url::get('year',date('Y')).'"';
		$data = $this->get_date($cond);
		$max = 0;
		foreach($data as $key=>$value){
			if($value['amount']>$max){
				$max = $value['amount'];
			}
		}
		$this->parse_layout('list',array(
			'total_page_view'=>$total_page_view,
			'date_page_view'=>$date_page_view,
			'month_page_view'=>$month_page_view,
			'year_page_view'=>$year_page_view,
			'user_online'=>$user_online,
			'list'=>$list,
			'data'=>$data,
			'month_list'=>$month,
			'year_list'=>$year,
			'max'=>$max
		));
	}
}
?>