<?php
class GenerateSlide
{
	var $rows = array();
	var $options = array();
	function GenerateSlide($rows,$opt)
	{
		$this->rows = $rows;
		$this->options = $opt;
	}
	function link_css($file_name)
	{
		if(strpos(Portal::$extra_header,'<link rel="stylesheet" href="'.$file_name.'" type="text/css" />')===false)
		{
			return '<link rel="stylesheet" href="'.$file_name.'" type="text/css" />';
		}
	}
	function link_js($file_name)
	{
		if(strpos(Portal::$extra_header,'<script type="text/javascript" src="'.$file_name.'"></script>')===false)
		{
			return '<script type="text/javascript" src="'.$file_name.'"></script>';
		}
	}
	function require_lib()
	{
		$lib = '';
		$lib .= GenerateSlide::link_css(Portal::template_css(substr(PORTAL_ID,1)).'css/slide/'.$this->options['effect'].'.css');
		$lib .= GenerateSlide::link_js(Portal::template_js().'/jquery/'.$this->options['effect'].'.js');
		return $lib;
	}
	function generate_html()
	{
		$html = '';
		$file = 'packages/backend/modules/MediaAdmin/lib/'.$this->options['effect'].'.php';
		if(file_exists($file))
		{
			require_once 'packages/core/includes/portal/generate_layout.php';
			$current = current($this->rows);
			$generate_layout = new GenerateLayout('<?php $this->map +='.var_export($current,true).';$this->map[\'items\'] = '.var_export($this->rows,true).'; ?>'.file_get_contents($file));
			$html .= $generate_layout->generate_text($generate_layout->synchronize());
		}
		return ($html);
	}
	function generate_slise()
	{
		$html = '';
		if($this->rows and sizeof($this->rows)>0)
		{
			$html .= GenerateSlide::require_lib();
			$html .= GenerateSlide::generate_html();
		}
		return  $html;
	}
}
?>
