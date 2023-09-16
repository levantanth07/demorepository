<?php
class PageLayout
{
	var $regions=false;
	var $layout = false;
	var $name = '';
	function __construct($id, $layout=false)
	{
		//$this->data=DB::select('layout', $id);
		if($layout)
		{
			$this->layout = $layout;
		}
		else
		{
			$this->layout = file_get_contents($id);//$this->data['content'];
		}
	}
	function get_file_name()
	{
		return 'cache/layouts/'.$this->name.'.php';
	}
	function get_regions()
	{
		if($this->regions===false)
		{
			$this->regions = array();
			$text= $this->layout;
/*			if($this->layout)
			{
				$text= $this->layout;
			}
			else
			{
				$text = $this->data['content'];
			}*/
			while(($pos=strpos($text,'[[|'))!==false)
			{
				$text = substr($text, $pos+3,  strlen($text)-$pos-3);
				if(preg_match('/([^\|]*)/',$text, $match))
				{
					if(isset($match[1]))
					{
						$this->regions[$match[1]]=$match[1];
					}
					if(($pos = strpos($text,'|]]',0))!==false)
					{
						$text = substr($text, $pos+3,  strlen($text)-$pos-3);
					}
				}
				else
				{
					break;
				}
			}
		}
		return $this->regions;
	}
	function get_next_position($table, $block_table, $id, $region, $extra='')
	{
		//if(in_array($region,$this->get_regions()))
		{
			$position = 1;
			if(DB::query('
				select max(position) as amax
				from '.$block_table.'
				where region="'.DB::escape($region).'"
					and '.$table.'_id="'.DB::escape($id).'"
					'.$extra.'
				'))
			{
				if($row=DB::fetch())
				{
					$position = $row['amax']+1;
				}
			}
			return $position;
		}
	}
	function move($table,$block_table, $id, $dir, $extra = '')
	{
		$block=DB::exists_id($block_table,$id);
		if($dir=='up')
		{
			$move[0]='<';
			$move[1]='desc';
		}
		else
		{
			$move[0]='>';
			$move[1]='asc';
		}
		if(DB::query('
			select *
			from '.$block_table.'
			where region="'.DB::escape($block['region']).'"
				and '.$table.'_id="'.DB::escape($block[$table.'_id']).'"
				and position'.$move[0].$block['position'].'
				'.$extra.'
			order by position '.$move[1]))
		{
			if($row=DB::fetch())
			{
				DB::update($block_table,array('position'=>$block['position']),'`id`='.$row['id']);
				DB::update($block_table,array('position'=>$row['position']),'`id`='.$block['id']);
			}
		}
	}
	static function get_module_regions($module)
	{
		$dir = opendir($module['path'].'layouts');
		$regions = array();
		while($file=readdir($dir))
		{
			if(is_file($module['path'].'layouts/'.$file) and preg_match_all('/\[\[--([^\-\]]+)--\]\]/i', file_get_contents($module['path'].'layouts/'.$file), $patterns))
			{
				foreach($patterns[1] as $pattern)
				{
					$regions[] = $pattern;
				}
			}
		}
		return $regions;
	}
}
?>