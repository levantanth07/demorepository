<?php
/******************************
COPY RIGHT BY NYN PORTAL - TCV
WRITTEN BY vuonggialong
******************************/
	function number_array($from, $to, $name=false)
	{
		$array = array();
		for($i = $from; $i<=$to; $i++)
		{
			if($name)
			{
				$array[]=array($name=>(strlen($i)<2)?'0'.$i:$i);
			}
			else
			{
				$array[$i] = $i;
			}
		}
		return $array;
	}
	function get_time_parameter($param, $default, &$end)
	{
		$number = URL::get($param, $default);
		$end = $number;
		if(is_numeric($number))
		{
			return $number;
		}
		$numbers = explode('-',$number);
		if(is_numeric($numbers[0]))
		{
			if($numbers[1])
			{
				$end = $numbers[1];
			}
			else
			{
				$end = $numbers[0];
			}
			return $numbers[0];
		}
		return $default;
	}
	function get_time_parameters($day=false, $end_day=false)
	{
		$years = number_array(2000, 2008, 'year');
		$start = get_time_parameter('year', date('Y'), $end);
		foreach($years as $key=>$year)
		{
			$years[$key]['selected'] = ($year['year']>=$start and $year['year']<=$end)?'_selected':'';
		}
		$months = number_array(1, 12, 'month');
		$start = get_time_parameter('month', date('m'), $end);
		foreach($months as $key=>$month)
		{
			$months[$key]['selected'] = ($month['month']>=$start and $month['month']<=$end)?'_selected':'';
		}
		$days = number_array(1, 31, 'day');
		if(!$day)
		{
			$start = get_time_parameter('day', date('d'), $end);
		}
		else
		{
			$start = $day;
			$end = $end_day;
		}
		foreach($days as $key=>$day)
		{
			$days[$key]['selected'] = ($day['day']>=$start and $day['day']<=$end)?'_selected':'';
		}
		return array('years'=>$years,'months'=>$months,'days'=>$days);
	}
	function get_time_range(&$obj)
	{
		$obj->from_year =1990;
		$obj->to_year =date('Y',time());
		$obj->from_month=1;
		$obj->to_month=1;
		$obj->from_day=1;
		$obj->to_day=31;
		if(URL::get('from_year'))
		{
			$obj->from_year = Url::get('from_year');
		}
		if(URL::get('to_year'))
		{
			$obj->to_year = Url::get('to_year');
		}
		if(Url::get('from_month'))
		{
			$obj->from_month = Url::get('from_month');
		}
		if(Url::get('to_month'))
		{
			$obj->to_month = Url::get('to_month');
		}
		if(URL::get('from_day'))
		{
			$obj->from_day = URL::get('from_day');
		}
		if(URL::get('to_day'))
		{
			$obj->to_day = URL::get('to_day');
		}
		$_REQUEST['day'] = $obj->from_day.'-'.$obj->to_day;
		$_REQUEST['month'] = $obj->from_month.'-'.$obj->to_month;
		$_REQUEST['year'] = $obj->from_year.'-'.$obj->to_year;
	}
?>