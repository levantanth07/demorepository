<?php
class AdvStatisticForm extends Form{
	function AdvStatisticForm(){
		Form::Form('AdvStatisticForm');
		$this->link_css('assets/default/css/cms.css');
	}
	function draw(){
		require_once 'packages/core/includes/utils/paging.php';
		$items = StatisticDB::GetAdvItems();
		$this->parse_layout('adv',array(
			'items'=>$items,
			'total'=>count($items)
		));
	}
}
?>