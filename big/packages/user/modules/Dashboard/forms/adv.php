<?php
class AdvDashboardForm extends Form{
	function __construct(){
		Form::Form('AdvDashboardForm');
		$this->link_css('assets/default/css/cms.css');
	}
	function draw(){
		require_once 'packages/core/includes/utils/paging.php';
		$items = DashboardDB::GetAdvItems();
		$this->parse_layout('adv',array(
			'items'=>$items,
			'total'=>count($items)
		));
	}
}
?>