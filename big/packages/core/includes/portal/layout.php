<?php
class Layout
{
	var $name;
	var $map;
	var $text = false;
	var $counter = 0;
	function __construct($name, $map=array(), $text=false)
	{
		$this->name = $name;
		$this->map = $map;
		if($text)
		{
			$this->source = $text;
		}
		else
		{
			$file_name = Module::$current->data['module']['path'].'layouts/'.$this->name.'.php';
			if($file = fopen($file_name,'r'))
			{
				$this->source = fread($file,filesize($file_name));
				fclose($file);
			}
		}
	}
	function show()
	{
		$this->generate();
		require 'cache/modules/'.Module::$current->data['module']['name'].'/'.$this->name.'.cache.php';
	}
	function pure_show()
	{
		require_once 'packages/core/includes/portal/generate_layout.php';
		$generate_layout = new GenerateLayout($this->name);
		$generate_layout->source = $this->source;
		$generate_layout->map = $this->map;
		eval('?>'.$generate_layout->generate_text($generate_layout->synchronize()).' <?php ');
	}
	function generate()
	{
		$file_name = Module::$current->data['module']['path'].'layouts/'.$this->name.'.php';
		$dir = 'cache/modules/'.Module::$current->data['module']['name'];
		if(!is_dir($dir))
		{
			mkdir($dir);
		}
		$cache_file_name = $dir.'/'.$this->name.'.cache.php';
		if(!file_exists($cache_file_name) or (($cache_time=filemtime($cache_file_name)) and (filemtime($cache_file_name)<filemtime($file_name))))
		{
			require_once 'packages/core/includes/portal/generate_layout.php';
			$generate_layout = new GenerateLayout($this->name);
			$generate_layout->source = $this->source;
			$generate_layout->map = $this->map;
			if($text = $generate_layout->generate_text($generate_layout->synchronize()))
			{
				$file = fopen($cache_file_name,'w+');
				fwrite($file,$text);
				fclose($file);
			}
		}
	}
}
?>