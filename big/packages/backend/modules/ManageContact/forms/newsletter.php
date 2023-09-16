<?php
class ManageNewsletterForm extends Form
{
	function ManageNewsletterForm()
	{
		Form::Form('ManageNewsletterForm');
		$this->link_css('skins/default/css/cms.css');
	}
	function on_submit()
	{
	}
	function draw()
	{
		$this->map['content'] = '';
		require_once 'packages/core/includes/utils/paging.php';
		if(Url::get('act')=='newsletter'){
			$emails = ManageContactDB::get_email(1000);
			$i=0;
			foreach($emails as $key=>$value){
				$this->map['content'] .= (($i>0)?';':'').$value['email'];
				$i++;
			}
		}
		$this->parse_layout('newsletter',$this->map);
	}
}
?>