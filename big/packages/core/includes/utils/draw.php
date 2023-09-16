<?php
class Draw{
	function int_to_words($x){
		$nwords = array(    "kh&#244;ng", "m&#7897;t", "hai", "ba", "b&#7889;n", "n&#259;m", "s&#225;u", "b&#7843;y",
                      "t&#225;m", "ch&#237;n", "m&#432;&#7901;i", "m&#432;&#7901;i m&#7897;t", "m&#432;&#7901;i hai", "m&#432;&#7901;i ba",
                      "m&#432;&#7901;i b&#7889;n", "m&#432;&#7901;i l&#259;m", "m&#432;&#7901;i s&#225;u", "m&#432;&#7901;i b&#7843;y", "m&#432;&#7901;i t&#225;m",
                      "m&#432;&#7901;i ch&#237;n", "hai m&#432;&#417;i", 30 => "ba m&#432;&#417;i", 40 => "b&#7889;n m&#432;&#417;i",
                      50 => "n&#259;m m&#432;&#417;i", 60 => "S&#250;a m&#432;&#417;i", 70 => "b&#7843;y m&#432;&#417;i", 80 => "t&#225;m m&#432;&#417;i",
                      90 => "ch&#237;n m&#432;&#417;i" );
		if(!is_numeric($x)){
		  	$w = '#';
		}else{
			  if($x < 0){
				  $w = 'minus ';
				  $x = -$x;
			  }else{
				  $w = '';
			  }
			  if($x < 21){
				  $w .= $nwords[$x];
			  }else if($x < 100){
				  $w .= $nwords[10 * floor($x/10)];
				  $r = fmod($x, 10);
				  if($r > 0){
					  $w .= ' '. $nwords[$r];
				  }
			  } else if($x < 1000){
				  $w .= $nwords[floor($x/100)] .' tr&#259;m';
				  $r = fmod($x, 100);
				  if($r > 0){
					  $w .= '  '. Draw::int_to_words($r);
				  }
			  } else if($x < 1000000){
				  $w .= Draw::int_to_words(floor($x/1000)) .' ng&#224;n';
				  $r = fmod($x, 1000);
				  if($r > 0){
					  $w .= ' ';
					  if($r < 100){
						  $w .= ' ';
					  }
					  $w .= Draw::int_to_words($r);
				  }
			  } else {
				  $w .= Draw::int_to_words(floor($x/1000000)) .' tri&#7879;u';
				  $r = fmod($x, 1000000);
				  if($r > 0){
					  $w .= ' ';
					  if($r < 100){
						  $word .= ' ';
					  }
					  $w .= Draw::int_to_words($r);
				  }
			  }
		}
		return $w;
	}
	function fieldset_begin(){
		echo '<div style="width:100%;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
	<td width="1px" valign="bottom"><span style="border-top:1px solid #CCCCCC;height:1px;display:block;width:1px;font-size:0px;background-color:#CCCCCC;"></span></td>
	<td width="1px" valign="bottom"><span style="border-top:2px solid #CCCCCC;height:2px;display:block;width:1px;font-size:0px;"></span></td>
	<td width="1px" valign="bottom"><span style="border-top:2px solid #CCCCCC;height:3px;display:block;width:1px;font-size:0px;"></span></td>
	<td style="border-top:1px solid  #CCCCCC;line-height:4px;">&nbsp;</td>
	<td width="1px" valign="bottom"><span style="border-top:2px solid #CCCCCC;height:3px;display:block;width:1px;font-size:0px;"></span></td>
	<td width="1px" valign="bottom"><span style="border-top:2px solid #CCCCCC;height:2px;display:block;width:1px;font-size:0px;"></span></td>
	<td width="1px" valign="bottom"><span style="border-top:1px solid #CCCCCC;height:1px;display:block;width:1px;font-size:0px;background-color:#CCCCCC;"></span></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border:0px;border-left:1px solid #CCCCCC;border-right:1px solid #CCCCCC;">
  <tr>
    <td style="padding:5px;height:200px;" valign="top">';
	}
	function fieldset_end(){
		echo '</td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
	<td width="1px" valign="bottom"><span style="border-top:2px solid #CCCCCC;height:3px;display:block;width:1px;font-size:0px;"></span></td>
	<td width="1px" valign="bottom"><span style="border-top:2px solid #CCCCCC;height:2px;display:block;width:1px;font-size:0px;"></span></td>
	<td width="1px" valign="bottom"><span style="border-top:2px solid #CCCCCC;height:1px;display:block;width:1px;font-size:0px;"></span></td>
	<td style="border-bottom:1px solid  #CCCCCC;line-height:4px;">&nbsp;</td>
	<td width="1px" valign="bottom"><span style="border-top:2px solid #CCCCCC;height:1px;display:block;width:1px;font-size:0px;"></span></td>
	<td width="1px" valign="bottom"><span style="border-top:2px solid #CCCCCC;height:2px;display:block;width:1px;font-size:0px;"></span></td>
	<td width="1px" valign="bottom"><span style="border-top:2px solid #CCCCCC;height:3px;display:block;width:1px;font-size:0px;"></span></td>
  </tr>
</table></div>';
	}
	//Ve tr co hieu ung khi chuot di vao
	static function hover($color='#EAF1FB'){
		echo ' onmouseover="tr_color=this.style.backgroundColor;this.style.backgroundColor=\''.$color.'\'" onmouseout="if(typeof(tr_color)!=\'undefined\'){this.style.backgroundColor=tr_color;}" ';
	}
	function button($title,$url=false,$tooltip=false,$onsubmit=false,$form_name=false, $target=false, $font_color="#143B80"){
		if($onsubmit){
			$action = ($url?' name="'.$url.'"':'').' onclick="this.disable=true;"';
		}
		else{
			if($url){
				$action = 'onclick="'.($target==false?'location=\''.$url.'\';':'window.open(\''.$url.'\';)').'"';
			}
			else{
				$action = 'onclick="history.go(-1)"';
			}
		}
		if(strlen($title)<10){
			$action.=' style="width:80px" ';
		}
		echo '<input class="button" type="'.($onsubmit?'submit':'button').'" '.($tooltip?'title="'.str_replace($tooltip,'"','&quot;').'"':'').$action.' value="'.$title.'">';
	}
	static function begin_round_table(){
		echo '<div class="body">';
		echo '<div class="main">';
	}
	static function end_round_table(){
		echo '</div>';
		echo '</div>';
	}
	static function title_label($name,$label){
		if(Url::get('order_by')==$name){
			echo '<a style="color:#333333;" href="'.URL::build_current(array('category_id','page_no','order_by'=>$name,'dir'=>Url::get('dir')=='desc'?'asc':'desc')).'">'.$label;
			if(Url::get('dir')=='desc'){
				echo ' <span class="label label-default">Tăng</span>';
			}
			else{
				echo ' <span class="label label-default">Giảm</span>';
			}
		}
		else{
			echo '<a style="color:#333333;" href="'.URL::build_current(array('category_id','page_no','order_by'=>$name,'dir'=>'asc')).'">'.$label;
		}
		echo '</a>';
	}
}
function draw_select($array_value, $name=false,$id=false, $style=false, $onchange, $selected_option=false){
	if ($id){
		$id = 'id="'.$id.'"';
	}
	if ($style){
		$style = 'class="'.$style.'"';
	}
	if (isset($_REQUEST[$name]) && $_REQUEST[$name]){
		$selected_option = $_REQUEST[$name];
	}
	if ($name){
		$name = 'name="'.$name.'"';
	}
	if ($onchange){
		$onchange = 'onchange="'.$onchange.'"';
	}
	echo '<select '.$name.' '.$id.' '.$style.' '.$onchange.'>';
	foreach ($array_value as $key=>$value){
		if ($selected_option && ($key == $selected_option)){
			$option = '<option value="'.$key.'" selected="true">'.$value.'</option>';
		}else{
			$option = '<option value="'.$key.'">'.$value.'</option>';
		}
		echo $option;
	}
	echo '</select>';
}
?>