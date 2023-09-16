<?php
class MakeSlideMediaAdminForm extends Form
{
	function MakeSlideMediaAdminForm()
	{
		Form::Form('MakeSlideMediaAdminForm');
		$languages = DB::select_all('language');
		foreach($languages as $language)
		{
			$this->add('name_'.$language['id'],new TextType(true,'invalid_name_'.$language['id'],0,2000));
		}
		$this->link_css('assets/default/css/cms.css');
	}
	function make_slide($options)
	{
		$html = '';
		if($items = MediaAdminDB::get_slide_by_cond($options['item_id']))
		{
			require_once 'packages/backend/includes/php/generate_slide.php';
			$slides = new GenerateSlide($items,$options);
			$html .= $slides->generate_slise();
		}
		return $html;
	}
	function on_submit()
	{
		if($this->check())
		{
			if(Url::get('selected_ids') and count(Url::get('selected_ids'))>0)
			{
				$row = array();
				$languages = DB::select_all('language');
				foreach($languages as $language)
				{
					$row['name_'.$language['id']] = Url::get('name_'.$language['id']);
				}
				$row += array(
					'option'=>Url::get('option')
					,'effect'=>Url::get('effect')
					,'user_id'=>Session::get('user_id')
					,'portal_id'=>PORTAL_ID
					,'item_id'=>implode(',',Url::get('selected_ids'))
				);
				$html = $this->make_slide($row);
				$row += array('html'=>$html);
				if(Url::get('id') and $slide = DB::exists_id('slide',intval(Url::get('id'))))
				{
					$row['last_time_update'] = time();
					DB::update_id('slide',$row,$slide['id']);
					$id = $slide['id'];
				}
				else
				{
					$row['time'] = time();
					$id = DB::insert('slide',$row);
				}
				save_log($id);
			}
			Url::redirect_current(array('cmd'=>'view_slide','slide_id'=>$id));
		}
	}
	function draw()
	{
		$cond = '1 and media.type="'.Url::get('type').'" and media.status!="HIDE"';
		$cond.=$this->get_condition();
		$item_per_page = MediaAdminDB::get_total_item($cond)?MediaAdminDB::get_total_item($cond):1;
		require_once 'packages/core/includes/utils/paging.php';
		require_once 'cache/config/effect.php';
		$paging = paging($item_per_page,$item_per_page,10,false,'page_no',array('cmd','type','category_id'));
		$items = MediaAdminDB::get_items($cond,'media.id DESC',$item_per_page);
		if(Url::get('id') and $row = DB::exists_id('slide',intval(Url::get('id'))))
		{
			foreach($row as $key=>$value)
			{
				$_REQUEST[$key] = $value;
			}
			if($item_select = MediaAdminDB::get_slide_by_cond($row['item_id']))
			{
				foreach($item_select as $id=>$item)
				{
					if(isset($items[$id]))
					{
						$items[$id]['item_id'] = $id;
					}
				}
			}
		}
		$languages = DB::select_all('language');
		$this->parse_layout('slide',array(
			'items'=>$items,
			'languages'=>$languages,
			'category_id_list'=>String::get_list(MediaAdminDB::get_category()),
			'effect_list'=>$effect
		));
	}
	function get_condition()
	{
		$cond = '';
		if(Url::get('category_id') and DB::exists_id('category',intval(Url::sget('category_id'))))
		{
			$cond.= ' and '.IDStructure::child_cond(DB::structure_id('category',intval(Url::sget('category_id'))));
		}
		return $cond;
	}
}
?>
