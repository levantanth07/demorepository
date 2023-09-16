<?php
class Statistic extends Module{
	function Statistic($row){
		if(User::can_view(false,ANY_CATEGORY)){
			Module::Module($row);
			require_once 'db.php';
			switch(Url::get('cmd')){
				case 'adv':
					require_once 'forms/adv.php';
					$this->add_form(new AdvStatisticForm());
					break;
				case 'hitcount':
					require_once 'forms/hitcount.php';
					$this->add_form(new HitcountForm());
					break;
				case 'user':
					require_once 'forms/user.php';
					$this->add_form(new UserStatisticForm());
					break;
				case 'report':
					require_once 'forms/report.php';
					$this->add_form(new ReportForm());		
					break;		
				default:
					require_once 'forms/list.php';
					$this->add_form(new StatisticForm());
					break;
			}
		}else{
			Url::access_denied();
		}
	}
}
?>