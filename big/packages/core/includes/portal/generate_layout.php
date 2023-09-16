<?php
class GenerateLayout
{
	var $source;
	function __construct($source)
	{
		$this->source = $source;
		$this->counter = 0;
	}
	function synchronize()
	{
		$text = $this->source;
		$text=strtr($text,array(
			'MAP[\''=>'$this->map[\'',
			'%5B%5B|'=>'[[|',
			'|%5D%5D'=>'|]]',
			'%5B%5B%7C'=>'[[|',
			'%7C%5D%5D'=>'|]]'
			));
		if(preg_match_all('/\[\[\-\-(\w+)\-\-\]\]/',$text, $patterns))
		{
			foreach($patterns[1] as $region)
			{
				$text = str_replace('[[--'.$region.'--]]','<?php Module::get_sub_regions("'.$region.'");?>', $text);
			}
		}
		return preg_replace(array(
			'/<textarea name="([^\[\"]*)\[\[\|([^\|]+)\|\]\]"([^\>]+)>([^\[\"]*)\[\[\|([^\|]+)\|\]\]<\/textarea>/i',
			'/<textarea name="([^\"]+)"([^\>]+)>([^\[\"]*)\[\[\|([^\|]+)\|\]\]<\/textarea>/i',
			'/<textarea name="([^\[\"]*)\[\[\|([^\|]+)\|\]\]"([^\>]+)>([^\<]*)<\/textarea>/i',
			'/<textarea name="([^\"]+)"([^\>]+)>([^\<]*)<\/textarea>/i',
			'/<input name="([^\[\"]*)\[\[\|([^\|]+)\|\]\]" type="([^\"]+)" value="\[\[\|([^\|]+)\|\]\]"([^\>]*)>/i', 
			'/<input name="([^\"]+)" type="([^\"]+)" id="([^\"])+" value="([^\[\"]*)\[\[\|([^\|]+)\|\]\]">/i',
			'/<input name="([^\"]+)" type="([^\"]+)" id="([^\"])+" value="([^\"]*)">/i',
			'/<input name="([^\[\"]*)\[\[\|([^\|]+)\|\]\]" type="([^\"]+)"([^\>]+)>/i',
			'/<input name="([^\"\|\[]+)" type="([^\"]+)"([^\>\|\[]+)>/i',
			'/\[\[\.([^\.\]]+)\.\]\]/'
			), 
			array(
			'<textarea  name="\\1[[|\\2|]]"\\3><?php echo MiString::html_normalize(URL::get(\'\\1\'.[[=\\2=]],\'\\4\'.[[=\\5=]]));?></textarea>',
			'<textarea  name="\\1"\\2><?php echo MiString::html_normalize(URL::get(\'\\1\',\'\\3\'.[[=\\4=]]));?></textarea>',
			'<textarea  name="\\1[[|\\2|]]"\\3><?php echo MiString::html_normalize(URL::get(\'\\1\'.[[=\\2=]],\'\\4\'));?></textarea>',
			'<textarea  name="\\1"\\2><?php echo MiString::html_normalize(URL::get(\'\\1\',\'\\3\'));?></textarea>',
			'<input  name="\\1[[|\\2|]]" value="<?php echo MiString::html_normalize(URL::get(\'\\1\'.[[=\\2=]],[[=\\4=]]));?>"\\5 type ="\\3">', 
			'<input  name="\\1" type ="\\2" id="\\3" value="<?php echo MiString::html_normalize(URL::get(\'\\1\',\'\\4\'.[[=\\5=]]));?>">', 
			'<input  name="\\1" type ="\\2" id="\\3" value="<?php echo MiString::html_normalize(URL::get(\'\\1\',\'\\4\'));?>">',
			'<input  name="\\1[[|\\2|]]"\\4 type ="\\3" value="<?php echo MiString::html_normalize(URL::get(\'\\1\'.[[=\\2=]]));?>">',
			'<input  name="\\1"\\3 type ="\\2" value="<?php echo MiString::html_normalize(URL::get(\'\\1\'));?>">',
			'<?php echo Portal::language(\'\\1\');?>'
			), 
			$text);
	}

	function get_text_variable($text)
	{
		if(!$text)
		{
			return;
		}
		$vars = explode('.',$text);
		$item_text = '$this->map';
		$i=1;
		foreach($vars as $var)
		{
			if($i<sizeof($vars))
			{
				$item_text .= '[\''.$var.'\'][\'current\']';
			}
			else
			{
				return $item_text.'[\''.$var.'\']';
			}
			$i++;
		}
		return;
	} 
	function generate_text($text)
	{
		$result='';
		
		while(($pos=strpos($text,'<!--LIST:'))!==false)
		{
			$pos2 = strpos($text,'-->',$pos+9);
			$var = substr($text, $pos+9,  $pos2-$pos-9);
			if($items = $this->get_text_variable($var))
			{
				if($pos3 = strpos($text,'<!--/LIST:'.$var.'-->'))
				{
					$result .= substr($text, 0,  $pos);
					$item_text = substr($text, $pos2+3,  $pos3-$pos2-3);
	
					$this->counter++;
					$result.='<?php
					if(isset('.$items.') and is_array('.$items.'))
					{
						foreach('.$items.' as $key'.$this->counter.'=>&$item'.$this->counter.')
						{
							if($key'.$this->counter.'!==\'current\')
							{
								'.$items.'[\'current\'] = &$item'.$this->counter.';?>'.
								$this->generate_text($item_text).'
							
						<?php
							}
						}
					unset('.$items.'[\'current\']);
					} ?>';
					$text = substr($text, $pos3+strlen($var)+13,  strlen($text)-strlen($var)-$pos3-13);
				}
				else
				{
					$result .= substr($text, 0,  $pos+9);
					$text = substr($text, $pos+9,  strlen($text)-$pos-9);
				}
			}
			else
			{
				$result .= substr($text, 0,  $pos+9);
				$text = substr($text, $pos+9,  strlen($text)-$pos-9);
			}
		}
		
		$text=$result.$text;

		//Update <form>
		/*if(isset(Form::$current) and is_object(Form::$current))
		{
			$make_onsubmit = '<script type="text/javascript">//make_onsubmit([';
			$first = true;
			if(isset(Form::$current->inputs) and is_array(Form::$current->inputs))
			{
				foreach(Form::$current->inputs as $name=>$inputs)
				{
					if(!strpos($name,'.'))
					{
						foreach($inputs as $input)
						{
							if($first)
							{
								$first = false;
							}
							else
							{
								$make_onsubmit .=',';
							}
							$make_onsubmit.=$input->to_js_data($name, Portal::language($input->message));
						}
					}
				}
			}
			$make_onsubmit.='],'.Form::$current->count.');</script>';
		}
		else*/
		{
			$make_onsubmit='';
		}
		$text = str_replace('</form>',
			'<input type="hidden" name="form_block_id" value="<?php echo isset(Module::$current->data)?Module::$current->data[\'id\']:\'\';?>" />
			</form >
			'.$make_onsubmit.'
			',$text);
		$result = '';
		while(($pos=strpos($text,'[[|'))!==false)
		{
			if($pos2 = strpos($text,'|',$pos+3))
			{
				$var = substr($text, $pos+3,  $pos2-$pos-3);
				if($item = $this->get_text_variable($var))
				{
					$result .= substr($text, 0,  $pos).'<?php echo '.$item.';?>';
					$text = substr($text, $pos2+3,  strlen($text)-$pos2-3);
				}
				else
				{
					$result .= substr($text, 0,  $pos+3);
					$text = substr($text, $pos+3,  strlen($text)-$pos-3);
				}
			}
			else
			{
				$result .= substr($text, 0,  $pos+3);
				$text = substr($text, $pos+3,  strlen($text)-$pos-3);
			}
		}
		$text = $result.$text;
		$result = '';
		while(($pos=strpos($text,'{{|'))!==false)
		{
			if($pos2 = strpos($text,'|',$pos+3))
			{
				$var = substr($text, $pos+3,  $pos2-$pos-3);
				$result .= substr($text, 0,  $pos).'<?php echo Module::get_setting(\''.$var.'\');?>';
				$text = substr($text, $pos2+3,  strlen($text)-$pos2-3);
			}
			else
			{
				$result .= substr($text, 0,  $pos+3);
				$text = substr($text, $pos+3,  strlen($text)-$pos-3);
			}
		}
		$text = $result.$text;
		$result = '';
		while(($pos=strpos($text,'{{='))!==false)
		{
			if($pos2 = strpos($text,'=',$pos+3))
			{
				$var = substr($text, $pos+3,  $pos2-$pos-3);
				$result .= substr($text, 0,  $pos).' Module::get_setting(\''.$var.'\')';
				$text = substr($text, $pos2+3,  strlen($text)-$pos2-3);
			}
			else
			{
				$result .= substr($text, 0,  $pos+3);
				$text = substr($text, $pos+3,  strlen($text)-$pos-3);
			}
		}
		$text = $result.$text;
		
		$result = '';
		while(($pos=strpos($text,'[[='))!==false)
		{
			if($pos2 = strpos($text,'=',$pos+3))
			{
				$var = substr($text, $pos+3,  $pos2-$pos-3);
				if($item = $this->get_text_variable($var))
				{
					$result .= substr($text, 0,  $pos).$item;
					$text = substr($text, $pos2+3,  strlen($text)-$pos2-3);
				}
				else
				{
					$result .= substr($text, 0,  $pos+3);
					$text = substr($text, $pos+3,  strlen($text)-$pos-3);
				}
			}
			else
			{
				$result .= substr($text, 0,  $pos+3);
				$text = substr($text, $pos+3,  strlen($text)-$pos-3);
			}
		}
		$text = $result.$text;
		$result = '';
		
		while(($pos=strpos($text,'<select name="'))!==false)
		{
			if($pos2 = strpos($text,'"',$pos+15))
			{
				$var = substr($text, $pos+14,  $pos2-$pos-14);
				if($items = $this->get_text_variable($var.'_list'))
				{
					
					if(!($cur_item = $this->get_text_variable($var)))
					{
						$cur_item = URL::get($var);
					}
					$p = $pos + 14;
					do
					{
						$p = strpos($text, '>',$p+1);
					}
					while ($p<strlen($text) and $text[$p-1]=='?');
					
					$result .= strtr(substr($text, 0,  $p+1),array('<select'=>'<select '));
					$result .= '<?php
					if(isset('.$items.'))
					{
						foreach('.$items.' as $key=>$value)
						{
							if($key>1000000000){
							    echo \'<option value="\'.$key.\'"\';
								echo \'>\'.$value.\'</option>\';
								/*echo \'<optgroup label=" - - \'.$value.\'">\';*/
							}else{
								echo \'<option value="\'.$key.\'"\';
								echo \'>\'.$value.\'</option>\';
							}
							
						}
					}
					?><?php if(isset($_REQUEST["'.$var.'"])){?><script type="text/javascript">getId(\''.$var.'\').value = "<?php echo addslashes(URL::get(\''.$var.'\',isset($this->map[\''.$var.'\'])?$this->map[\''.$var.'\']:\'\'));?>";</script><?php }?>
	';
					$p2 = strpos($text,'</select>',$p+1);
					$result.=preg_replace(
						array(
						'/<option value="([^\[\"]*)\[\[\|([^\|]+)\|\]\]">/i',
						'/<option value="([^\"]+)">/i'),
						array(
						'<option value="\\1[[|\\2|]]" <?php 
						if(\'\\1\'.[[=\\2=]] == URL::get(\''.strtr($var,'|','=').'\','.$cur_item.'))
						{
							echo \' selected\';
						}?>>',
						'<option value="\\1"  <?php 
						if(\'\\1\' == URL::get(\''.strtr($var,'|','=').'\','.$cur_item.'))
						{
							echo \' selected\';
						}?>>'),
						substr($text, $p+1,  $p2-$p-1)).'</select>';
					
					$text = substr($text, $p2+9,  strlen($text)-$p2-9);
				}
				else
				{
					if($pos2 = strpos($text,'</select>',$pos))
					{
						$result .= substr($text, 0,  $pos2+9);
						$text = substr($text, $pos2+9,  strlen($text)-$pos2-9);
					}
					else
					{
						$result .= substr($text, 0,  $pos+14);
						$text = substr($text, $pos+14,  strlen($text)-$pos-14);
					}
				}
			}
			else
			{
				if($pos2 = strpos($text,'</select>'))
				{
					$result .= substr($text, 0,  $pos2+9);
					$text = substr($text, $pos2+9,  strlen($text)-$pos2-9);
				}
				else
				{
					$result .= substr($text, 0,  $pos+14);
					$text = substr($text, $pos+14,  strlen($text)-$pos-14);
				}
			}
		}
	
		$text = $result.$text;
		$result = '';
		while(($pos=strpos($text,'<!--IF:'))!==false)
		{
			$pos2 = strpos($text,'(',$pos+7);
			$name = substr($text, $pos+7,  $pos2-$pos-7);
			if($pos3 = strpos($text,'<!--/IF:'.$name.'-->') and $pos4 = strpos($text,'-->',$pos2))
			{
				$result .= substr($text, 0,  $pos);
				$result .= '<?php 
				if('.substr($text, $pos2,  $pos4-$pos2).')
				{?>';
				$result .= $this->generate_text(strtr(substr($text, $pos4+3,  $pos3-$pos4-3),array('<!--ELSE-->'=>' <?php }else{ ?>'))).'
				<?php
				}
				?>';
				$text = substr($text, $pos3+strlen('<!--/IF:'.$name.'-->'), strlen($text)-$pos3-strlen('<!--/IF:'.$name.'-->'));
			}
			else
			{
				$result .= substr($text, 0,  $pos+7);
				$text = substr($text, $pos+7, strlen($text)-$pos-7);
			}
		}
		return $result.$text;
	}
	
}
?>