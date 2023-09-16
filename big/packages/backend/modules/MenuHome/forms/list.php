<?php
class MenuHomeForm extends Form
{
	function __construct()
	{
		Form::Form('MenuHomeForm');
		$this->link_css('assets/default/css/menu.css');
	}
	function draw()
	{
		require 'cache/tables/function.cache.php';
		$categories = MiString::array2tree($categories,'child');
        foreach($categories as $key=>$value){
            $categories[$key]['name']= $categories[$key]['name_'.Portal::language()];
            if (isset($categories[$key]['child'])){
                foreach ($categories[$key]['child'] as $k1=>$v1){
                    $categories[$key]['child'][$k1]['name']= $categories[$key]['child'][$k1]['name_'.Portal::language()];
                    if ($v1['status']=="HIDE"){
                        unset($categories[$key]['child'][$k1]);
                    }
                }
            }
    		if($value['status']=="HIDE"){
    			unset($categories[$key]['child']);
    		}
        }
		$category_id = intval(Url::get('category_id'));
		if(isset($categories[$category_id]))
		{
			$this->map['name'] = $categories[$category_id]['name_'.Portal::language()];
			$this->map['child'] = $categories[$category_id]['child']; //DB::fetch_all('select id, name_'.Portal::language().' as name_1,url, icon_url, structure_id from function where status<>\'HIDE\' and '.IDStructure::child_cond($categories[$category_id]['structure_id']).' and structure_id <> \''.$categories[$category_id]['structure_id'].'\'  order by structure_id');
			$this->parse_layout('list',$this->map);
		}
	}
}
?>
