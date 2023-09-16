<?php
class ZoneAdmin extends Module
{
    protected $_provinceInfo = array(
        'viettel' => array('name', 'id', 'code'),
        'ghtk' => array('name'),
        'ems' => array('code'),
        'vnpost' => array('code'),
        'ghn' => array('id', 'code'),
        'best' => array('name'),
        'jt' => array('code')
    );

    protected $_districtInfo = array(
        'viettel' => array('name', 'id', 'code'),
        'ghtk' => array('name'),
        'ems' => array('code'),
        'vnpost' => array('code'),
        'ghn' => array('id', 'pid', 'code', 'type', 'stype'),
        'best' => array('name'),
        'jt' => array('code')
    );

    protected $_wardInfo = array(
        'viettel' => array('name', 'id'),
        'ghtk' => array('name'),
        'ems' => array('code'),
        'vnpost' => array('code'),
        'ghn' => array('did', 'code'),
        'best' => array('name'),
        'jt' => array('code')
    );
    protected $arrBrand = array(
        'viettel' => 'Viettel Post',
        'ghtk' => 'Giao hàng tiết kiệm',
        'ems' => 'EMS',
        'vnpost' => 'Bưu điện Việt Nam',
        'ghn' => 'Giao hàng nhanh',
        'best' => 'Best Inc',
        'jt' => 'J&T'
    );

	function __construct($row)
	{
		Module::Module($row);
		require_once 'db.php';
		// if(User::can_view(false,ANY_CATEGORY))
		if(User::can_view(false,ANY_CATEGORY))
		{
			switch(URL::get('cmd'))
			{
                case 'v2':
                    $this->list_cmd_v2();
                    break;
                case 'sync_v2':
                    $this->sync_zone_jt();
                    break;
                case 'get_form_info_v2':
                    echo $this->get_form_info_v2(URL::get('type'), URL::get('id'), URL::get('brand'));
                    die;
                    break;
                case 'update_info_v2':
                    echo $this->update_info_v2(URL::get('info_type'), URL::get('info_id'), URL::get('brand'));
                    die;
                    break;
                case 'update_district_name':
                    $this->update_district_name(Url::get('value'),Url::get('zone_id'));
                    die;
                    break;
                case 'update_ems_province':
                    $this->update_ems_province(Url::get('value'),Url::get('zone_id'));
                    break;
                case 'update_ems_district':
                    $this->update_ems_district(Url::get('value'),Url::get('zone_id'));
                    break;
                case 'update_ems_ward':
                    $this->update_ems_ward(Url::get('value'),Url::get('zone_id'));
                    break;
			    case 'update_name':
					$this->update_name(Url::get('value'),Url::get('zone_id'));
					die;
					break;
				case 'cache':
					$this->export_cache();
					break;
				case 'get_zone_id':
					$this->get_zone(intval(Url::get('zone_id')));
					exit();
				case 'delete':
					$this->delete_cmd();
					break;
				case 'edit':
					$this->edit_cmd();
					break;
				case 'unlink':
					$this->delete_file();
				case 'add':
					$this->add_cmd();
					break;
                case 'edit_district':
                    $this->add_district();
                    break;
                case 'add_district':
                    $this->add_district();
                    break;
                case 'edit_ward':
                    $this->add_ward();
                    break;
                case 'add_ward':
                    $this->add_ward();
                    break;
                case 'add_ward_v2':
                    $this->add_ward_v2();
                    break;
                case 'delete_ward':
                    if(Url::iget('id') and $zone_ward=DB::fetch('select id from zone_wards where id='.intval(Url::get('id'))))
                    {
                        DB::delete('zone_wards','id='.$zone_ward['id']);
                        Url::js_redirect(true,'Xóa thành công!',['district_id']);
                    }
                    break;
				case 'add_all':
					$this->add_all_cmd();
					break;
				case 'view':
					$this->view_cmd();
					break;
				case 'move_up':
				case 'move_down':
					$this->move_cmd();
					break;
				default:
					$this->list_cmd();
					break;
			}
		}
		else
		{
			URL::access_denied();
		}
	}
	function get_zone($id)
	{
		if(Url::get('zone_id') and $zone=DB::select_id('zone_provinces',intval(Url::get('zone_id')))){
			$region_cond = '(area.area_type_id in (0,4,6)) and area.zone_id='.$zone['id'];
		}
		$zone['zoom'] = $this->zoom($zone['structure_id']);
		echo 'var lat='.$zone['lat'].'; var long='.$zone['long'].'; var zoom='.$zone['zoom'].'; var arr = '.String::array2js(String::get_list(ZoneAdminDB::get_regions($region_cond))).';';
	}
	function export_cache()
	{
		$zones = DB::fetch_all('
			SELECT
				id,name,name_id,
				structure_id
			FROM
				zone
			WHERE
				portal_id="'.PORTAL_ID.'" 
				and '.IDstructure::direct_child_cond(ID_ROOT).'
				and status != "HIDE"
			ORDER BY
				structure_id'
		);
		$temp_zones = $zones;
		foreach($zones as $key=>$value)
		{
			$zones[$key]['countries'] = $this->get_zone_child($value['structure_id']);
		}
		foreach($temp_zones as $key=>$value)
		{
			$temp_zones[$key]['zone_provinces'] = $this->get_zone_child($value['structure_id'],' LIMIT 0,10');
		}
		$items = DB::fetch_all('select * from zone where type = 3 and status != "HIDE" order by name');
		$this->export_file('cache/tables/cities.cache.php','cities',$items);
		$items_ = DB::fetch_all('select id,name,name_id,structure_id from zone where type = 3 or type = 4 and status != "HIDE" order by structure_id');
		$this->export_file('cache/tables/zones.cache.php','zones',$items_);
		$this->export_file('cache/tables/destination_favorites.cache.php','destination_favorites',$temp_zones);
		Url::redirect_current();
	}
	function delete_file()
	{
		if(Url::get('link') and file_exists(Url::get('link')) and User::can_delete(false,ANY_CATEGORY))
		{
			@unlink(Url::get('link'));
		}
		echo '<script>window.close();</script>';
	}
	function add_cmd()
	{
		if(User::can_add(false,ANY_CATEGORY))
		{
			require_once 'forms/edit.php';
			$this->add_form(new EditZoneAdminForm());
		}
		else
		{
			Url::redirect_current();
		}
	}
    function add_district()
    {
        if(User::can_add(false,ANY_CATEGORY))
        {
            require_once 'forms/edit_district.php';
            $this->add_form(new EditZoneAdminForm());
        }
        else
        {
            Url::redirect_current();
        }
    }
    function add_ward()
    {
        if(User::can_add(false,ANY_CATEGORY))
        {
            require_once 'forms/edit_ward.php';
            $this->add_form(new EditZoneAdminForm());
        }
        else
        {
            Url::redirect_current();
        }
    }
    function add_ward_v2()
    {
        if(User::can_add(false,ANY_CATEGORY))
        {
            require_once 'forms_v2/edit_ward_v2.php';
            $this->add_form(new EditZoneAdminFormV2());
        }
        else
        {
            Url::redirect_current();
        }
    }

	function add_all_cmd()
	{
		if(User::can_add(false,ANY_CATEGORY))
		{
			require_once 'forms/add.php';
			$this->add_form(new EditZoneAdminForm());
		}
		else
		{
			Url::redirect_current();
		}
	}

	function delete_cmd()
	{
		if(is_array(URL::get('selected_ids')) and sizeof(URL::get('selected_ids'))>0 and User::can_delete(false,ANY_CATEGORY))
		{
			if(sizeof(URL::get('selected_ids'))>1)
			{
				require_once 'forms/list.php';
				$this->add_form(new ListZoneAdminForm());
			}
			else
			{
				$ids = URL::get('selected_ids');
				$_REQUEST['id'] = $ids[0];
				require_once 'forms/detail.php';
				$this->add_form(new ZoneAdminForm());
			}
		}
		else
		if(User::can_delete(false,ANY_CATEGORY) and Url::check('id') and DB::exists_id('zone_provinces',$_REQUEST['id']))
		{
			require_once 'forms/detail.php';
			$this->add_form(new ZoneAdminForm());
		}
		else
		{
			Url::redirect_current();
		}
	}
	function edit_cmd()
	{
		if(Url::get('id') and $category=DB::fetch('select id,structure_id from zone_provinces where id='.intval(Url::get('id'))) and User::can_edit(false,$category['structure_id']))
		{
			require_once 'forms/edit.php';
			$this->add_form(new EditZoneAdminForm());
		}
		else
		{
			Url::redirect_current();
		}
	}
	function list_cmd()
	{
		require_once 'forms/list.php';
		$this->add_form(new ListZoneAdminForm());
	}
	function list_cmd_v2()
	{
		require_once 'forms_v2/list.php';
		$this->add_form(new ListZoneAdminFormV2());
	}
	function view_cmd()
	{
		if(User::can_view_detail(false,ANY_CATEGORY) and Url::check('id') and DB::exists_id('zone_provinces',$_REQUEST['id']))
		{
			require_once 'forms/detail.php';
			$this->add_form(new ZoneAdminForm());
		}
		else
		{
			Url::redirect_current();
		}
	}
	function move_cmd()
	{
		if(User::can_edit(false,ANY_CATEGORY)and Url::check('id')and $category=DB::exists_id('zone_provinces',$_REQUEST['id']))
		{
			if($category['structure_id']!=ID_ROOT)
			{
				require_once 'packages/core/includes/system/si_database.php';
				si_move_position('zone_provinces',' and 1');
			}
			Url::redirect_current(array('countries'));
		}
		else
		{
			Url::redirect_current(array('countries'));
		}
	}
	function zoom($structure_id){
		$level = IDStructure::level($structure_id);
		switch ($level){
			case 1:
				$zoom = 2; break;
			case 2:
				$zoom = 5; break;
			case 3:
				$zoom = 11; break;
			case 4:
				$zoom = 12; break;
			default:
				$zoom = 11; break;
		}
		return $zoom;
	}
	function get_zone_child($structure_id,$limit='',$cond=''){
		return DB::fetch_all('
			SELECT
				*
			FROM
				zone
			WHERE
				portal_id="'.PORTAL_ID.'" and '.IDstructure::direct_child_cond($structure_id).' '.$cond.'
			ORDER BY 
				structure_id
			'.$limit.'
		');
	}
	function export_file($path,$file_name,$content){
		$content = '<?php $'.$file_name.' = '.var_export($content,true).';?>';
		$handler = fopen($path,'w+');
		fwrite($handler,$content);
		fclose($handler);
	}
	function update_name($value,$id){
		if($value and $id){
			DB::update('zone_provinces',array('viettel_province_name'=>trim($value)),'province_id='.$id);
			echo $value;
		}
	}
	function update_ems_province($value,$id) {
		if($value and $id){
			DB::update('zone_provinces',array('ems_provice_code'=>trim($value)),'province_id='.$id);
			echo $value;
		}
	}
	function update_ems_district($value,$id) {
		if($value and $id){
			DB::update('zone_districts',array('ems_district_code'=>trim($value)),'district_id='.$id);
			echo $value;
		}
	}
	function update_ems_ward($value,$id) {
		if($value and $id){
			DB::update('zone_wards',array('ems_ward_code'=>trim($value)),'id='.$id);
			echo $value;
		}
	}
    function update_district_name($value,$id) {
        if($value and $id){
            DB::update('zone_districts',array('viettel_district_name'=>trim($value)),'district_id='.$id);
            echo $value;
        }
    }
    function sync_zone_provinces_v2() {
        $sql = 'select * from zone_provinces';
        $provinces = DB::fetch_all_key($sql, 'province_id');
        if (!empty($provinces)) {
            foreach ($provinces as $id => $province) {
                $payload = array(
                    'province_id' => $id,
                    'province_name' => $province['province_name'],
                    'province_info' => json_encode(array(
                        'viettel' => array(
                            'name' => $province['viettel_province_name']
                        ),
                        'ghtk' => array(
                            'name' => $province['province_name']
                        ),
                        'ems' => array(
                            'code' => $province['ems_provice_code']
                        ),
                        'vnpost' => array(
                            'name' => $province['province_name']
                        ),
                        'ghn' => array(
                            'name' => $province['province_name']
                        ),
                        'best' => array(
                            'name' => $province['province_name']
                        ),
                        'jt' => array(
                            'name' => $province['province_name']
                        )
                    ), JSON_UNESCAPED_UNICODE)
                );
                DB::insert('zone_provinces_v2', $payload);
            }
        }
        echo 'Done';
    }
    function sync_zone_districts_v2() {
        $sql = 'select * from zone_districts';
        $districts = DB::fetch_all_key($sql, 'district_id');
        if (!empty($districts)) {
            foreach ($districts as $id => $district) {
                $payload = array(
                    'district_id' => $id,
                    'province_id' => $district['province_id'],
                    'district_name' => $district['district_name'],
                    'district_info' => json_encode(array(
                        'viettel' => array(
                            'name' => $district['viettel_district_name']
                        ),
                        'ghtk' => array(
                            'name' => $district['district_name']
                        ),
                        'ems' => array(
                            'code' => $district['ems_district_code']
                        ),
                        'vnpost' => array(
                            'name' => $district['district_name']
                        ),
                        'ghn' => array(
                            'name' => $district['district_name']
                        ),
                        'best' => array(
                            'name' => $district['district_name']
                        ),
                        'jt' => array(
                            'name' => $district['district_name']
                        )
                    ), JSON_UNESCAPED_UNICODE)
                );
                DB::insert('zone_districts_v2', $payload);
            }
        }
        echo 'Done';
    }
    function sync_zone_wards_v2() {
        $sql = 'select * from zone_wards';
        $districts = DB::fetch_all_key('select * from zone_districts', 'district_id');
        $wards = DB::fetch_all_key($sql, 'id');
        if (!empty($wards)) {
            foreach ($wards as $id => $ward) {
                $payload = array(
                    'ward_id' => $id,
                    'province_id' => $districts[$ward['district_id']]['province_id'],
                    'district_id' => $ward['district_id'],
                    'ward_name' => $ward['ward_name'],
                    'ward_info' => json_encode(array(
                        'viettel' => array(
                            'name' => $ward['viettel_ward_name']
                        ),
                        'ghtk' => array(
                            'name' => $ward['ward_name']
                        ),
                        'ems' => array(
                            'code' => $ward['ems_ward_code']
                        ),
                        'vnpost' => array(
                            'name' => $ward['ward_name']
                        ),
                        'ghn' => array(
                            'name' => $ward['ward_name']
                        ),
                        'best' => array(
                            'name' => $ward['ward_name']
                        ),
                        'jt' => array(
                            'name' => $ward['ward_name']
                        )
                    ), JSON_UNESCAPED_UNICODE)
                );
                DB::insert('zone_wards_v2', $payload);
            }
        }
        echo 'Done';
    }
    function sync_zone_jt()
    {
    	$arrProvince = array();
    	$arrDistrict = array();
    	$arrWard = array();
        $path = "upload/zone/json/jnt.json";
        $jsonData = json_decode(file_get_contents($path), true);
        foreach ($jsonData as $key => $item) {
//            if ($key < 11000) continue;
//            if ($key > 3000) break;
            $pName = normalizer_normalize($item['Province']);
            $pId = $item['Prov_id'];
            $dName = normalizer_normalize($item['District']);
            $dId = $item['District_id'];
            $wName = normalizer_normalize($item['Area']);
            $wId = $item['Area_id'];

            $province=DB::fetch('select * from zone_provinces_v2 where province_name="'.$pName.'"');
            if (!in_array($pId, $arrProvince)) {
                if ($province) {
                    $province_info = json_decode($province['province_info'], true);
                    if (!is_numeric($province_info['jt']['code'])) {
                        $province_info['jt'] = array('code' => $pId);
                        $province_info = json_encode($province_info, JSON_UNESCAPED_UNICODE);
                        $query = "UPDATE `zone_provinces_v2`
						SET `province_info` = '".$province_info."', `province_name` = '".$pName."'
						WHERE `province_id` = " . $province['province_id'];
                        DB::query($query);
                        $arrProvince[] = $pId;
					}
                }
			}

            $district=DB::fetch('select * from zone_districts_v2 where province_id = '.$province['province_id'].' and district_name="'.$dName.'"');
            if (!in_array($dId.'-'.$pId, $arrDistrict)) {
                if ($district) {
                    $district_info = json_decode($district['district_info'], true);
                    if (!is_numeric($district_info['jt']['code'])) {
                        $district_info['jt'] = array('code' => $dId);
                        $district_info = json_encode($district_info, JSON_UNESCAPED_UNICODE);

                        $query = "UPDATE `zone_districts_v2`
						SET `district_info` = '" . str_replace("'", "\'", $district_info) . "', `district_name` = '" . str_replace("'", "\'", $dName) . "'
						WHERE `district_id` = " . $district['district_id'];
                        DB::query($query);
                        $arrDistrict[] = $dId . '-' . $pId;
                    }
                }
			}

            if (!in_array($wId.'-'.$dId.'-'.$pId, $arrWard)) {
                $ward=DB::fetch('select * from zone_wards_v2 where province_id = '.$province['province_id'].' and district_id = '.$district['district_id'].' and ward_name="'.$wName.'"');
                if ($ward) {
                    $ward_info = json_decode($ward['ward_info'], true);
                    if (!is_numeric($ward_info['jt']['code'])) {
                        $ward_info['jt'] = array('code' => $wId);
                        $ward_info = json_encode($ward_info, JSON_UNESCAPED_UNICODE);
                        $query = "UPDATE `zone_wards_v2`
						SET `ward_info` = '" . str_replace("'", "\'", $ward_info) . "', `ward_name` = '" . str_replace("'", "\'", $wName) . "'
						WHERE `ward_id` = " . $ward['ward_id'];
                        DB::query($query);
                        $arrWard[] = $wId . '-' . $dId . '-' . $pId;
                    }
                }
			}
        }
        var_dump($arrProvince);echo '<br><br>';
        var_dump($arrDistrict);echo '<br><br>';
        var_dump($arrWard);
        die;
    }

    function get_form_info_v2($type='', $id='', $brand='') {
        $arrBrand = array(
            'viettel' => 'Viettel Post',
            'ghtk' => 'Giao hàng tiết kiệm',
            'ems' => 'EMS',
            'vnpost' => 'Bưu điện Việt Nam',
            'ghn' => 'Giao hàng nhanh',
            'best' => 'Best Inc',
            'jt' => 'J&T'
        );
        $arrInfo = array(
            'name' => 'Tên địa danh:',
            'code' => 'Mã địa danh:',
            'id' => 'ID địa danh:',
            'pid' => 'PID địa danh:',
            'did' => 'DID địa danh:',
            'type' => 'Type địa danh:',
            'stype' => 'Stype địa danh:'
        );
	    $html = '<h5>Địa danh không tồn tại!</h5>';
	    if ($type === 'province') {
            $province=DB::fetch('select * from zone_provinces_v2 where province_id='.$id);
            if ($province) {
                $html = '<form class="form-info"><h4 style="margin-bottom: 20px">' . $arrBrand[$brand] . '</h4>';
                $province_info = json_decode($province['province_info'], true);
                $infos = $this->_provinceInfo[$brand];
                foreach ($infos as $info) {
                    $html .= '
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">'.$arrInfo[$info].'</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="'.$info.'" value="'.$province_info[$brand][$info].'">
                            </div>
                        </div>';
                }
                $html .= '<input type="hidden" name="brand" id="info_brand" value="'.$brand.'">
                    <input type="hidden" name="info_type" id="info_type" value="'.$type.'">
                    <input type="hidden" name="info_id" id="info_id" value="'.$id.'">
                    <div class="form-group row">
                        <label class="col-sm-8 col-form-label text-right" id="result" style="line-height: 30px; color: #0dbd00"></label>
                        <div class="col-sm-4 text-right">
                          <button type="submit" class="btn btn-primary">Cập nhật</button>
                        </div>
                    </div>
                </form>';
            }
        }
	    if ($type === 'district') {
            $district=DB::fetch('select * from zone_districts_v2 where district_id='.$id);
            if ($district) {
                $html = '<form class="form-info"><h4 style="margin-bottom: 20px">' . $arrBrand[$brand] . '</h4>';
                $district_info = json_decode($district['district_info'], true);
                $infos = $this->_districtInfo[$brand];
                foreach ($infos as $info) {
                    $html .= '
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">'.$arrInfo[$info].'</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="'.$info.'" value="'.$district_info[$brand][$info].'">
                            </div>
                        </div>';
                }
                $html .= '<input type="hidden" name="brand" id="info_brand" value="'.$brand.'">
                    <input type="hidden" name="info_type" id="info_type" value="'.$type.'">
                    <input type="hidden" name="info_id" id="info_id" value="'.$id.'">
                    <div class="form-group row">
                        <label class="col-sm-8 col-form-label text-right" id="result" style="line-height: 30px; color: #0dbd00"></label>
                        <div class="col-sm-4 text-right">
                          <button type="submit" class="btn btn-primary">Cập nhật</button>
                        </div>
                    </div>
                </form>';
            }
        }
	    if ($type === 'ward') {
            $ward=DB::fetch('select * from zone_wards_v2 where ward_id='.$id);
            if ($ward) {
                $html = '<form class="form-info"><h4 style="margin-bottom: 20px">' . $arrBrand[$brand] . '</h4>';
                $ward_info = json_decode($ward['ward_info'], true);
                $infos = $this->_wardInfo[$brand];
                foreach ($infos as $info) {
                    $html .= '
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">'.$arrInfo[$info].'</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="'.$info.'" value="'.$ward_info[$brand][$info].'">
                            </div>
                        </div>';
                }
                $html .= '<input type="hidden" name="brand" id="info_brand" value="'.$brand.'">
                    <input type="hidden" name="info_type" id="info_type" value="'.$type.'">
                    <input type="hidden" name="info_id" id="info_id" value="'.$id.'">
                    <div class="form-group row">
                        <label class="col-sm-8 col-form-label text-right" id="result" style="line-height: 30px; color: #0dbd00"></label>
                        <div class="col-sm-4 text-right">
                          <button type="submit" class="btn btn-primary">Cập nhật</button>
                        </div>
                    </div>
                </form>';
            }
        }
        return '<div class="modal">'.$html.'
        <script>
            $(document).ready(function(){
                $("form").on("submit", function(event){
                    event.preventDefault();
                    let brand = "";
                    let type = "";
                    let id = "";
                    let arrData = $(this).serializeArray();
                    arrData.forEach(function(item) {
                        if (item["name"] == "brand") {
                            brand = item["value"];
                        }
                        if (item["name"] == "info_type") {
                            type = item["value"];
                        }
                        if (item["name"] == "info_id") {
                            id = item["value"];
                        }
                    })
                    let formValues= "page=zone_admin&cmd=update_info_v2&" + $(this).serialize();
                    $.post("index062019.php", formValues, function(data){
                        // Display the returned data in browser
                        if (data == "Cập nhật thất bại!") {
                            $("#result").html(data);
                        } else {
                            $("#" + type + "-" + id + "-" + brand).html(data);
                        }
                    });
                });
            });
        </script></div>';
    }

    function update_info_v2($type, $id, $brand) {
        if ($type === 'province') {
            $province=DB::fetch('select * from zone_provinces_v2 where province_id='.$id);
            if ($province) {
                $infos = $this->_provinceInfo[$brand];
                if (!empty($infos)) {
                    $html = '<b>'.$this->arrBrand[$brand].'</b>';
                    $data = array();
                    $province_info = json_decode($province['province_info'], true);
                    foreach ($infos as $info) {
                        $data[$info] = Url::get($info);
                        $html .= '<br>' . $info . ': ' . Url::get($info);
                    }
                    $province_info[$brand] = $data;
                    $province_info = json_encode($province_info, JSON_UNESCAPED_UNICODE);
                    $province_info = str_replace("'", "", $province_info);
                    $query = "UPDATE `zone_provinces_v2`
                    SET `province_info` = '".$province_info."'
                    WHERE `province_id` = " . $id;
                    DB::query($query);

                    return $html;
                } else {
                    return 'Cập nhật thất bại!';
                }
            } else {
                return 'Cập nhật thất bại!';
            }
        }
        if ($type === 'district') {
            $district=DB::fetch('select * from zone_districts_v2 where district_id='.$id);
            if ($district) {
                $infos = $this->_districtInfo[$brand];
                if (!empty($infos)) {
                    $html = '<b>'.$this->arrBrand[$brand].'</b>';
                    $data = array();
                    $district_info = json_decode($district['district_info'], true);
                    foreach ($infos as $info) {
                        $data[$info] = Url::get($info);
                        $html .= '<br>' . $info . ': ' . Url::get($info);
                    }
                    $district_info[$brand] = $data;
                    $district_info = json_encode($district_info, JSON_UNESCAPED_UNICODE);
                    $district_info = str_replace("'", "", $district_info);
                    $query = "UPDATE `zone_districts_v2`
                    SET `district_info` = '".$district_info."'
                    WHERE `district_id` = " . $id;
                    DB::query($query);

                    return $html;
                } else {
                    return 'Cập nhật thất bại!';
                }
            } else {
                return 'Cập nhật thất bại!';
            }
        }
        if ($type === 'ward') {
            $ward=DB::fetch('select * from zone_wards_v2 where ward_id='.$id);
            if ($ward) {
                $infos = $this->_wardInfo[$brand];
                if (!empty($infos)) {
                    $html = '<b>'.$this->arrBrand[$brand].'</b>';
                    $data = array();
                    $ward_info = json_decode($ward['ward_info'], true);
                    foreach ($infos as $info) {
                        $data[$info] = Url::get($info);
                        $html .= '<br>' . $info . ': ' . Url::get($info);
                    }
                    $ward_info[$brand] = $data;
                    $ward_info = json_encode($ward_info, JSON_UNESCAPED_UNICODE);
                    $ward_info = str_replace("'", "", $ward_info);
                    $query = "UPDATE `zone_wards_v2`
                    SET `ward_info` = '".$ward_info."'
                    WHERE `ward_id` = " . $id;
                    DB::query($query);

                    return $html;
                } else {
                    return 'Cập nhật thất bại!';
                }
            } else {
                return 'Cập nhật thất bại!';
            }
        }
    }
}
?>
