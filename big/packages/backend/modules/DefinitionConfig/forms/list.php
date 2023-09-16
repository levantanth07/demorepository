<?php
class ListDefinitionConfigForm extends Form
{
	function ListDefinitionConfigForm()
	{
		Form::Form('ListDefinitionConfigForm');
		$this->link_css('assets/default/css/cms.css');
		$this->link_js(Portal::template_js('core').'multi_items.js');
	}
	function on_submit()
	{
		if($config = $_REQUEST['config'])
		{
			foreach($config as $key=>$value)
			{
				$config[$value['code']] = $value;
				unset($config[$key]);
			}
			require_once 'packages/core/includes/utils/xml.php';
			sort($config);
			XML::create_xml('cache/config_template',$config);
		}
		Url::redirect_current();
	}
	function draw()
	{
		if(is_file('cache/config_template.xml'))
		{
			require_once 'packages/core/includes/utils/xml.php';
			$config = XML::fetch_all('cache/config_template.xml');
			//$total = count($config);
			//$item_per_page = 2;
			//require_once 'packages/core/includes/utils/paging.php';
			$_REQUEST['config'] = $config;//array_slice($config,(page_no()-1)*$item_per_page,$item_per_page);
			//$_REQUEST['config'] = $config;
			//$this->map['paging'] = paging($total,$item_per_page);
		}
		$this->map['type_list'] = array(
									'1'=>array('id'=>'text'),
									'2'=>array('id'=>'textarea'),
									'3'=>array('id'=>'select'),
									'4'=>array('id'=>'checkbox'),
									'5'=>array('id'=>'radio'),
									'6'=>array('id'=>'image'),
									'7'=>array('id'=>'file')
								);
		$this->parse_layout('list',$this->map);
	}
	function CutArray($arr,$pos,$length)
	{
	}
}
?>