<?php
class ListZoneAdminForm extends Form
{
	function __construct()
	{
		Form::Form('ListZoneAdminForm');
	}
	function on_submit()
	{
		if(Url::get('cmd')=='delete' and Url::get('selected_ids')){
			$this->deleted_selected_ids();
		}
	}
	function draw()
	{
        $map = [];
		$this->get_just_edited_id();
		$this->get_select_condition();
        $map['province_name'] = '';
        $map['district_name'] = '';
        $map['province_id'] = 0;
		if($province_id=Url::get('province_id') and $zone = DB::fetch('select province_id,province_name from zone_provinces where province_id='.$province_id)){
			$this->cond = 'zone_districts.province_id='.$province_id;
            $map['province_name']  = $zone['province_name'];
			$check_flag = false;
		}elseif($district_id=Url::get('district_id') and $zone = DB::fetch('select zone_districts.district_id,zone_districts.district_name from zone_districts where district_id='.$district_id)){
            $this->cond = 'zone_wards.district_id='.$district_id;
            $map['district_name']  = $zone['district_name'];
            $province_id = DB::fetch('select province_id from zone_districts where district_id='.$district_id,'province_id');
            $province = DB::fetch('select province_id,province_name from zone_provinces where province_id='.$province_id);
            $map['province_name'] = $province['province_name'];
            $map['province_id'] = $province['province_id'];
            $check_flag = false;
        } else{
			$check_flag = true;
		}
		$this->get_items();
		$items = $this->items;
		//require_once 'cache/tables/countries.cache.php';
		$cities = DB::fetch_all('select zone_provinces.province_id as id,zone_provinces.province_name as name from zone_provinces where 1=1 order by zone_provinces.province_name');

		$this->parse_layout('list',$this->just_edited_id+
			$map + array(
				'items'=>$items,
				'check_flag'=>$check_flag,
				'province_id_list'=>[''=>'Tất cả tỉnh thành']+MiString::get_list($cities,'name',' ')
			)
		);
	}
	function get_just_edited_id()
	{
		$this->just_edited_id['just_edited_ids'] = array();
		if (UrL::get('selected_ids'))
		{
			if(is_string(UrL::get('selected_ids')))
			{
				if (strstr(UrL::get('selected_ids'),','))
				{
					$this->just_edited_id['just_edited_ids']=explode(',',UrL::get('selected_ids'));
				}
				else
				{
					$this->just_edited_id['just_edited_ids']=array('0'=>UrL::get('selected_ids'));
				}
			}
		}
	}
	function deleted_selected_ids()
	{
		require_once 'detail.php';
		foreach(URL::get('selected_ids') as $id)
		{
			if($id and $category=DB::exists_id('zone_provinces',$id) and User::can_edit(false,$category['structure_id']))
			{
				save_recycle_bin('zone_provinces',$category);
				DB::delete_id('zone_provinces',$id);
				@unlink($category['image_url']);
				save_log($id);
			}	
			if($this->is_error())
			{
				return;
			}
		}
		Url::redirect_current(array('countries'));
	}
	function get_items()
	{

	    if($province_id = Url::get('province_id')){
            $sql = '
                select 
                    zone_districts.district_id as id,
                    zone_districts.*
                from 
                    zone_districts
                where
                     '.$this->cond.'
                order by
                    zone_districts.district_name
            ';
        }elseif($district_id = Url::get('district_id')){
            $sql = '
                select 
                    zone_wards.*
                from 
                    zone_wards
                where
                     '.$this->cond.'
                order by
                    zone_wards.ward_name
            ';
        } else{
            $sql = '
                select 
                    zone_provinces.province_id as id,
                    zone_provinces.*
                from 
                    zone_provinces
                where
                     '.$this->cond.'
                order by
                    zone_provinces.province_name
            ';
        }
		$this->items = DB::fetch_all($sql);
	}
	function get_select_condition()
	{
		$this->cond = '1=1'.((URL::get('cmd')=='delete' and is_array(URL::get('selected_ids')))?' and zone_provinces.province_id in ("'.join(URL::get('selected_ids'),'","').'")':'');
	}
	
}
?>