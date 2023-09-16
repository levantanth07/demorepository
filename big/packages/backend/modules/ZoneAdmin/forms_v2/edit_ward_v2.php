<?php
class EditZoneAdminFormV2 extends Form
{
	function __construct()
	{
		Form::Form('EditZoneAdminFormV2');
		if(URL::get('cmd')=='edit_ward')
		{
			$this->add('id',new IDType(true,'object_not_exists','zone_wards'));
		}
		$this->add('ward_name',new TextType(true,'lỗi hập tên',0,225));
	}
	function on_submit()
	{
		if($this->check() and URL::get('confirm_edit'))
		{
			if(URL::get('cmd')=='edit_ward')
			{
				$this->old_value = DB::select('zone_wards','id="'.addslashes($_REQUEST['id']).'"');
                $this->id = $this->old_value['id'];
			}
			$this->save_item();
			if(!$this->is_error())
			{
				Url::js_redirect(true,'Cập nhật thành công',['district_id']);
			}
		}
		echo '1';die;
	}
	function draw()
	{
		$this->init_edit_mode();
		$this->map = array(
            'type_list'=>['Phường'=>'Phường','Thị trấn'=>'Thị trấn','Xã'=>'Xã']
        );
		$this->map['district_name'] = '';
        if($district_id = Url::iget('district_id')){
            $this->map['district_name'] = DB::fetch('select district_name from zone_districts_v2 where district_id='.$district_id,'district_name');
        }
		$this->parse_layout('edit_ward_v2',
			($this->edit_mode?$this->init_value:array())+ $this->map
		);
	}
	function save_item()
	{
        $new_row = array('ward_code','ward_name','code','district_id','type');
		if(!$this->is_error()) {
			if(URL::get('cmd')=='edit_ward')
			{
				DB::update_id('zone_wards', $new_row,$this->id);
			}
			else
			{
                $district_id = Url::iget('district_id');
                $ward_name = DB::escape(trim(Url::get('ward_name')));
                $ward_info = '{"viettel":{"id":0,"name":"'.$ward_name.'"},"ghtk":{"name":"'.$ward_name.'"},"ems":{"code":"0"},"vnpost":{"code":"0"},"ghn":{"did":"0","code":"0"},"best":{"name":"'.$ward_name.'"},"jt":{"code":"0"}}';
                $province_id = DB::fetch('select province_id from zone_districts_v2 where district_id='.$district_id,'province_id');
                $query = 'insert into `zone_wards_v2`(`province_id`,`district_id`,`ward_name`,`ward_info`) 
					values('.$province_id.','.$district_id.',\'' . DB::escape(trim(Url::get('ward_name'))) . '\',\''.$ward_info.'\')';
				if (DB::query($query)) {
                    $this->id = DB::insert_id();
                }
//                $this->id = DB::insert('zone_wards', $new_row);
			}
			save_log($this->id);
		}
	}
	function init_edit_mode()
	{
		if(URL::get('cmd')=='edit_ward' and $this->init_value=DB::select('zone_wards','id='.intval(URL::sget('id')).''))
		{
			foreach($this->init_value as $key=>$value)
			{
				if(is_string($value) and !isset($_REQUEST[$key]))
				{
					$_REQUEST[$key] = $value;
				}
			}
			$this->edit_mode = true;
		}
		else
		{
			$this->edit_mode = false;
		}
	}
}
?>
