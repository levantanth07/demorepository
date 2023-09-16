<?php
class EditZoneAdminForm extends Form
{
	function __construct()
	{
		Form::Form('EditZoneAdminForm');
		$this->add('district_name',new TextType(true,'Lỗi nhập tên',0,225));
        $this->add('district_code',new TextType(true,'Lỗi nhập mã',0,225));

        $this->add('viettel_district_name',new TextType(true,'Lỗi nhập tên Viettel',0,225));
        $this->add('ems_district_code',new IntType(false,'Lỗi nhập mã ems (Phải là số)'));
	}
	function on_submit()
	{
		if($this->check() and URL::get('confirm_edit'))
		{
			if($id = Url::iget('district_id') and URL::get('cmd')=='edit_district')
			{
				$this->old_value = DB::select('zone_districts','district_id='.$id.'');
                $this->id = $this->old_value['district_id'];
			}
			$this->save_item();
			if(!$this->is_error())
			{
				Url::js_redirect(true,'Cập nhật thành công',['province_id']);
			}
		}
	}	
	function draw()
	{
		$this->init_edit_mode();
		$this->map = array(
            'district_type_list'=>['Quận'=>'Quận','Huyện'=>'Huyện','Thành phố'=>'Thành phố','Thị Xã'=>'Thị Xã','Xã'=>'Xã']
        );
		$this->map['province_name'] = '';
        if($province_id = Url::iget('province_id')){
            $this->map['province_name'] = DB::fetch('select zone_provinces.province_name from zone_provinces where province_id='.$province_id,'province_name');
        }
		$this->parse_layout('edit_district',
			($this->edit_mode?$this->init_value:array())+ $this->map
		);
	}
	function save_item()
	{
        $new_row = array(
                'district_code','district_name',
                'viettel_district_name','viettel_district_code',
                'ems_district_code'=>Url::iget('ems_district_code')?Url::iget('ems_district_code'):'0',
                'province_id',
                'district_type');
		if(!$this->is_error()) {
			if(URL::get('cmd')=='edit_district')
			{
				DB::update('zone_districts', $new_row,'district_id='.$this->id);

            }
			else
			{
                $this->id = DB::insert('zone_districts', $new_row);
			}
			save_log($this->id);
		}	
	}
	function init_edit_mode()
	{
		if(URL::get('cmd')=='edit_district' and $this->init_value=DB::select('zone_districts','district_id='.URL::iget('district_id').''))
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