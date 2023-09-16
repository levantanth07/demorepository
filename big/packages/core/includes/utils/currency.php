<?php
// doc gia tien tieng viet
	function currency_to_text($currency)
	{
		$unit = array(1=>'',2=>'nghìn',3=> 'triệu',4=> 'tỉ',5=>'nghìn',6=>'triệu',7=>'tỉ');
		$text = '';
		$i = 1;
		while((strlen($currency)-3*$i) > -3)
		{
			if((strlen($currency)-3*$i) < 0)
			{
				$number_digits_miss = abs(strlen($currency)-3*$i);
			}else $number_digits_miss = 0;
			$temp = substr($currency,strlen($currency)-3*$i+$number_digits_miss,3-$number_digits_miss);
			if(($t = read_currency($temp)) != '')
			{
				$text = $t.' '.$unit[$i].' '.$text;
			}
			$i++;
		}
		return ucfirst($text);
	}
	// doc 3 so
	function read_currency($number)
	{
		$text = '';
		$text_number = array(
						0=>'không',
						1=>'một',
						2=>'hai',
						3=>'ba',
						4=>'bốn',
						5=>'năm',
						6=>'sáu',
						7=>'bảy',
						8=>'tám',
						9=>'chín'
		);
		$temp[3] = $temp[2] = $temp[1] = '';
		for($i=1;$i<=strlen($number);$i++)
		{
			if(($digital[$i] = substr($number,strlen($number)-$i,1))!='')
			{
				$temp[$i] =  $text_number[$digital[$i]];
				if($i==3) $temp[$i] .= ' trăm ';
				if($i==2){
					if($digital[$i] == 0)
					{
						if($digital[1]!=0) $temp[$i] = ' linh ';
						else $temp[$i]  = '';
					}
					elseif($digital[$i] == 1)
					{
						$temp[$i] = suffix($digital[$i]);
					}else
					{
						if($digital[1]==0){
							$temp[$i] .= suffix($digital[$i]);
						}else $temp[$i] .=' ';
					}
				}
				if($i==1 and $digital[$i]==0) $temp[$i] = '';
			}
		}
		if(isset($digital[2]))
		{
			if($digital[1]==1) $temp[1] = ' mốt ';
		}
		if(isset($digital[1]) and isset($digital[2]) and isset($digital[3]) and ($digital[1] == 0) and ($digital[2] == 0) and ($digital[3] == 0))
		{
			$temp[3] = $temp[2] = $temp[1] = '';
		}
		//if($number==562) echo $temp[3].$temp[2].$temp[1];
		return $text.$temp[3].$temp[2].$temp[1];
	}
	function suffix($number)
	{
		if($number==1) return ' mười '; // muoi
		else return ' mươi '; // muoif
	}
// ket thuc doc gia tien tieng viet
?>