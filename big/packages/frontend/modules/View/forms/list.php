<?php
class ViewForm extends Form
{
	function ViewForm()
	{
		Form::Form('ViewForm');
	}
	function draw()
	{
		$this->map = array();
		$cond = '1';
		$this->map['total_visit'] = Counter::count_visitor()+500;
		$this->map['length'] = strlen($this->map['total_visit']);
		$sub_str = '';
		if($this->map['length']<=6)
		{
			$int = 6-intval($this->map['length']);
			for($i=1;$i<=$int;$i++)
			{
				$sub_str.= '0';
			}
		}
		$this->map['total_visit']= $sub_str.$this->map['total_visit'];
		for($i=0;$i<=5;$i++){
			$this->map['visit'][$i+1] = substr($this->map['total_visit'],$i,1);
		}
		// user online
		$this->map['user_online'] = Counter::count_visitor('n');
		$this->map['leng_user_online'] = strlen($this->map['user_online']);
		for($i=0;$i<=$this->map['leng_user_online']-1;$i++){
			$this->map['total_online'][$i+1] = substr($this->map['user_online'],$i,1);
		}
		//System::debug($this->map['total_online']);
		$this->parse_layout('list',$this->map);
	}
}
?>
