<?php
class Counter
{
	static $delay = 360;//durantion time (second) user online
	static function get_ip_location($ip)
	{
		/*$sql = '
			SELECT
				ip2nation.country as country_code,
				ip2nationcountries.country,ip2nation.flag_url
			FROM
				ip2nation
				inner join ip2nationcountries on ip2nationcountries.code = ip2nation.country
			WHERE
				ip2nation.ip < INET_ATON("'.$ip.'")
			ORDER BY 
				ip2nation.ip DESC 
			LIMIT 0,1';
		$country = DB::fetch($sql);
		return $country;*/
		return '';
	}
	static function check_session(){
		$ip = Counter::get_ip_location($_SERVER['REMOTE_ADDR']);
		$current_page = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']; 
		$partten = "/[[\\\\]+|\"]+/i";
		$current_page =  preg_replace($partten,'',$current_page);
		/*if(!$session = DB::fetch('select * from visit where session_id ="'.session_id().'" and portal_id="'.PORTAL_ID.'"')){
			$visit = array(
				'country'=>$ip['country'],
				'ip'=>$_SERVER['REMOTE_ADDR'],
				'client'=>$_SERVER['HTTP_USER_AGENT'],
				'time'=>time(),
				'session_id'=>session_id(),
				'user_id'=>Session::get('user_id'),
				'portal_id'=>PORTAL_ID
			);
			$id = DB::insert('visit',$visit);
		}elseif(count($session)>0 and isset($session['session_id'])){
			$id = $session['id'];
			DB::update_id('visit',array('time'=>time(),'user_id'=>Session::get('user_id')),$id);
			if(!$page_view = DB::fetch('select * from visit_page where visit_id="'.$session['id'].'" and page ="'.$current_page.'"')){
				$page = array(
					'page'=>$current_page,
					'visit_id'=>$id
				);
				DB::insert('visit_page',$page);
			}	
		}*/
	}
	static function count_visitor($para='1',$portal_id = false){
		return 0;
		/*switch($para){
			case 'd': 	
				$cond = 'from_unixtime(time,"%d/%m/%Y") = "'.date('d/m/Y').'"';
				break;
			case 'm': 
				$cond = 'from_unixtime(time,"%m/%Y") = "'.date('m/Y').'"';
				break;
			case 'y':
				$cond = 'from_unixtime(time,"%Y") = "'.date('Y').'"';
				break;
			case 'n':
				$cond = 'time > '.(time()-Counter::$delay);
				break;
			default : 
				$cond = $para;
				break;
		}if(!$portal_id){
			//$cond .= ' and visit.portal_id = "'.PORTAL_ID.'"';
		}else{
			//$cond .= ' and visit.portal_id = "#'.$portal_id.'"';
		}
		return $counter = DB::fetch('
			select
				count(*) as acount
			from
				visit						
			where
				'.$cond.'
		','acount');*/
		
	}
	static function user_online($portal_id = false){
		if(!$portal_id){
			$portal_id = substr(PORTAL_ID,1);
		}	
		/*$items = DB::fetch_all('
			select
				visit.*
			from
				visit
			where
				visit.time>='.(time()-Counter::$delay).'
				and portal_id="#'.$portal_id.'"
			order by
				visit.time desc');
		foreach($items  as $key=>$value){
			$value['pages']  = DB::fetch_all('
				select 
					id,page
				from 
					visit_page
				where
					visit_id ='.$key.'
				order by 
					id DESC		
					');
			$items[$key] = $value;
		}*/
		return array();//$items ;
	}
	static function count_page_view($para = '1',$portal_id = false){
		/*switch($para){
			case 'd': 	
				$cond = 'from_unixtime(time,"%d/%m/%Y") = "'.date('d/m/Y').'"';
				break;
			case 'm': 
				$cond = 'from_unixtime(time,"%m/%Y") = "'.date('m/Y').'"';
				break;
			case 'Y':
				$cond = 'from_unixtime(time,"%Y") = "'.date('Y').'"';
				break;
			default : 
				$cond = $para;
				break;
		}if(!$portal_id){
			//$cond .= ' and visit.portal_id = "'.PORTAL_ID.'"';
		}else{
			//$cond .= ' and visit.portal_id = "#'.$portal_id.'"';
		}
		/*return DB::fetch_all('
			select
				id,page,count(*) as count
			from
				visit_page
				inner join visit on visit_page.visit_id = visit.id
			where
				'.$cond.'
			group by
				page
		');*/
		return 0;
	}
}