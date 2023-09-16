<?php
class SupportOnlineForm extends Form
{
	function SupportOnlineForm()
	{
		Form::Form('SupportOnlineForm');
	}
	function draw()
	{
		$this->map = array();
		if(Portal::language()==2)
		{
			$this->map['yahoo'] = explode(',',Portal::get_setting('support_online_en'));
		}else{
			$this->map['yahoo'] = explode(',',Portal::get_setting('support_online'));
		}
		if($this->map['yahoo'] and count($this->map['yahoo'])>0){
			foreach($this->map['yahoo'] as $key=>$value){
				if(strpos($value,':')){
					$this->map['yahoo'][$key+1] = explode(':',$value);
				}else{
					$this->map['yahoo'][$key+1] = array('1'=>'','2'=>$value);
				}
				$this->map['yahoo'][$key+1]['status'] = $this->StatusYahoo($this->map['yahoo'][$key+1][1]);
			}
			$this->map['skype'] = explode(',',Portal::get_setting('support_skype'));
		}else{
			$this->map['skype'] = explode(',',Portal::get_setting('support_skype'));
		}
		if($this->map['skype'] and count($this->map['skype'])>0){
			foreach($this->map['skype'] as $key=>$value){
				if(strpos($value,':')){
					$this->map['skype'][$key+1] = explode(':',$value);
				}else{
					$this->map['skype'][$key+1] = array('1'=>'','2'=>$value);
				}
				$this->map['skype'][$key+1]['status'] = $this->StatusYahoo($this->map['skype'][$key+1][1]);
			}
		}
		$this->parse_layout('list',$this->map);
        //System::debug($this->map['yahoo']);
	}
	function StatusYahoo($user)
	{
		/*$file = @file_get_contents('http://opi.yahoo.com/online?u='.$user.'&m=t');
		if(strpos($file, "NOT ONLINE"))
		{
			return 0;
		}
		return true;*/
	}
}
?>
