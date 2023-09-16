<?php
class EditCrmCustomerForm extends Form
{
    protected $map;

    public function __construct()
    {
        Form::Form('EditCrmCustomerForm');
        $this->link_js('assets/vissale/js/lodash.min.js');
        $this->link_js('packages/core/includes/js/multi_items.js');
//        if (URL::get('do') == 'edit') {
//            $this->add('id', new IDType(true, 'object_not_exists', 'crm_customer'));
//        }
        $this->add('name', new TextType(true, 'Bạn vui lòng nhập tên khách hàng', 0, 255));
        $this->add('job_title', new TextType(false, 'invalid_job_title', 0, 255));
        $this->add('birth_date', new DateType(false, 'invalid_birth_date'));
        $this->add('email', new EmailType(false, 'invalid_email'));
        // $this->add('mobile', new PhoneType(true, 'Di động', 6, 11));
        $this->add('weight', new FloatType(false, 'Cân nặng', 0, 1000));
        $this->add('career', new TextType(false, 'Nghề nghiệp', 2, 100));
        $this->add('address', new TextType(false, 'invalid_address', 0, 255));
        $this->add('description', new TextType(false, 'invalid_description', 0, 20000));
        $this->add('crm_group_id', new TextType(true, 'Nhóm phân loại', 0, 255));
        $this->add('zone_id', new IDType(true, 'Tỉnh / thành phố', 'zone'));
        $this->add('crm_customer_card.name', new TextType(false, 'Lỗi nhập tên thẻ', 0, 255));
        $this->add('crm_customer_share.group_id', new TextType(false, 'Lỗi cơ sở được chia sẻ', 0, 255));

        //
        $this->map = array();
        $this->init();
    }
    public function on_submit()
    {
        $type = 1;// ca nhan
        if (Url::get('org') == 1) {
            $type = 2;// to chuc
        }
        $group_id = Session::get('group_id');
        $cid = DB::escape(Url::get('cid'));
        $queryCustomerCond = "id=$cid and group_id=$group_id";
        $creator_id = get_user_id();

        if ($this->check()) {
            $name = Url::post('name');
            $name = mb_convert_case($name, MB_CASE_TITLE, "UTF-8");
            $array = array(
                'zone_id' => Url::post('zone_id') ? DB::escape(Url::get('zone_id')) : '0',
                'job_title' => DB::escape(Url::post('job_title')),
                'name' =>   DB::escape($name),
                'weight' => Url::post('weight')? DB::escape(Url::get('weight')):'0',
                'career' => DB::escape(Url::post('career')),
                'website' => DB::escape(Url::post('website')),
                'birth_date' => URL::post('birth_date') ? Date_Time::to_sql_date((URL::post('birth_date'))) : '0000-00-00',
                'email' => DB::escape(Url::post('email')),
                'mobile' => CrmCustomerDB::convertVietnamMobileNo( trim(Url::sget('mobile')) ),
                'address' => DB::escape(Url::post('address')),
                'description' => DB::escape(Url::post('description')),
                'parent_id' => Url::get('parent_id') ? DB::escape(Url::get('parent_id')) : '0',
                'gender' => Url::get('gender') ? DB::escape(Url::get('gender')) : '0',
                'crm_group_id' => DB::escape(Url::post('crm_group_id')),
                'type' => $type,
                'source_id' => Url::get('source_id') ? DB::escape(Url::get('source_id')) : '0',
                'user_id' => Url::get('user_id') ? DB::escape(Url::get('user_id')) : '0',
                'contact_id' => Url::get('contact_id') ? DB::escape(Url::get('contact_id')) : '0',
                'bank_name' => DB::escape(Url::post('bank_name')),
                'bank_account_number' => DB::escape(Url::post('bank_account_number')),
                'bank_account_name' =>  DB::escape(Url::post('bank_account_name')),
                'warning_note' => DB::escape(Url::post('warning_note')),
            );//'status_id' => (int)Url::post('status_id'),
            $master_group_id = Session::get('master_group_id');
            $account_type = Session::get('account_type');
            if ($account_type == 3) {
                $cond = ' and (crm_customer.group_id = ' . $group_id . ' or crm_customer.master_group_id = ' . $group_id . ')';
            } else {
                if ($master_group_id) {
                    $cond = ' and (crm_customer.group_id = ' . $group_id . ' or (crm_customer.master_group_id = ' . $master_group_id . '))';// and crm_customer.shared
                } else {
                    $cond = ' and crm_customer.group_id = ' . $group_id . '';
                }
            }
            if (URL::get('do') == 'edit' and $row = DB::select('crm_customer', $queryCustomerCond)) {

                if ($array['mobile'] and $customer = DB::fetch('select id,name from crm_customer where mobile="' . $array['mobile'] . '" and id<>"' . $row['id'] . '" and group_id=' . $group_id)) {
                    $this->error('mobile', 'Số điện thoại này đã được sử dụng bởi khách hàng' . $customer['name'] . '', false);
                    return;
                }

                $id = $row['id'];
                /////
                $text = array(
                    'job_title' => 'Chức vụ',
                    'weight' => 'Cân nặng',
                    'career' => 'Nghề nghiệp',
                    'name' => 'Tên',
                    'website' => 'Website',
                    'birth_date' => 'Ngày sinh',
                    'email' => 'email',
                    'phone' => 'Điện thoại',
                    'mobile' => 'Di động',
                    'address' => 'Địa chỉ',
                    'description' => 'Ghi chú chung',
                    'crm_group_id' => 'Phân loại',
                    'source_id' => "Nguồn",
                    'user_id' => "Nhân viên phụ trách",
                    'contact_id' => 'Người liên hệ',
                    'bank_name' => 'Tên ngân hàng',
                    'bank_account_number' => 'Số tài khoản ngân hàng',
                    'bank_account_name' => 'Tên tài khoản ngân hàng',
                    'warning_note' => 'Ghi chú cảnh báo',
                    'status' => 'Phân loại',
                    'gender' => 'Giới tính',
                    'zone_id' => 'Tỉnh / thành phố',
                );
                $old_array =
                array(
                    'job_title' => $row['job_title'],
                    'name' => $row['name'],
                    'website' => $row['website'],
                    'birth_date' => ($row['birth_date'] == '') ? '0000-00-00' : $row['birth_date'],
                    'email' => $row['email'],
                    'phone' => $row['phone'],
                    'weight' => $row['weight'],
                    'career' => $row['career'],
                    'mobile' => $row['mobile'],
                    'address' => $row['address'],
                    'description' => $row['description'],
                    'crm_group_id' => $row['crm_group_id'],
                    'source_id' => $row['source_id'],
                    'user_id' => $row['user_id'],
                    'contact_id' => $row['contact_id'],
                    'bank_name' => $row['bank_name'],
                    'bank_account_number' => $row['bank_account_number'],
                    'bank_account_name' => $row['bank_account_name'],
                    'warning_note' => $row['warning_note'],
                    'zone_id' => $row['zone_id'],
                    'gender' => $row['gender'],
                );//'status_id' => $row['status_id']
                $users = AdminOrdersDB::get_users(false, false, true);
                $sql = '
			select
				zone_provinces_v2.province_id as id, `zone_provinces_v2`.province_name as name
			from
				`zone_provinces_v2`
			where
				1=1
			order by
				zone_provinces_v2.province_name
		';
                $zones = DB::fetch_all($sql);
                $arrIdToText = array(
                    'crm_group_id' => array('' => 'Chưa chọn') + MiString::get_list(CrmCustomerDB::get_customer_groups()),
                    'source_id' => array('' => 'Chưa chọn') + MiString::get_list(AdminOrdersDB::get_source()),
                    'user_id' => array('' => 'Chưa chọn') + MiString::get_list($users, 'full_name'),
                    'zone_id' => array('' => 'Chưa chọn') + MiString::get_list($zones),
                    'gender' => array('' => 'Chưa xác định', '1' => 'Nam', '2' => 'Nữ'),
                );
                

                unset($array['mobile']);
                DB::update('crm_customer', $array, $queryCustomerCond);
                //$array['status'] = $this->map['status_id_list'][$array['status_id']];
                //$old_array['status'] = $this->map['status_id_list'][$old_array['status_id']];
                $message = "đã sửa khách hàng: <br>" . System::generate_log_message($old_array, $array, $text,$arrIdToText);
                System::log('EDIT', "customer_id_{$row['id']}", $message, "customer_id_={$row['id']}");
                /////
            } else {
                // $customer = CrmCustomerDB::get_duplicated_phone($array['mobile'],$group_id);
                // if ($array['mobile'] && $customer) {
                //     $this->error('mobile', "SĐT {$array['mobile']} đã sử dụng bởi: {$customer['name']},
                //                                     Vui lòng liên hệ chi nhánh <strong>{$customer['group_name']}</strong>,
                //                                     Hoặc tổng để được chia sẻ thông tin KH.", false);
                //     return;
                // } else {
                //     $array += array('group_id' => Session::get('group_id'), 'time' => time(), 'creator_id' => $creator_id);
                //     $id = DB::insert('crm_customer', $array);
                //     System::log('ADD', "crm_customer_id_{$id}",'thêm mới customer');
                // }
            }
            ///
            require_once 'packages/core/includes/utils/upload_file.php';
            $dir = 'default/groups/'.Session::get('group_id').'/customer/';
            update_upload_file('image_url',$dir, 'IMAGE',false,200,200,true);
            if(Url::get('image_url')!='')
            {   
                $row = DB::fetch('select id,image_url from crm_customer where id='.$id);
                @unlink($row['image_url']);
                DB::update_id('crm_customer',array('image_url'),$id);
            }
            ///
            CrmCustomerDB::update_card($id);
            CrmCustomerDB::update_share($id);
            // CrmCustomerDB::update_contact($id);
            Url::js_redirect('customer', 'Dữ liệu đã cập nhật', array('cid', 'do'=>'view','branch_id'));
        }
    }
    public function draw()
    {
        $group_id = Session::get('group_id');
        $this->map['no_id'] = '';
        $cid = DB::escape(Url::get('cid'));
        $queryCustomerCond = "crm_customer.id=$cid and crm_customer.group_id=$group_id";
        if (URL::get('do') == 'edit' and $row = DB::select('crm_customer', $queryCustomerCond)) {
            $row['contact_name'] = $row['contact_id'] ? DB::fetch('select id,name from crm_customer where id=' . $row['contact_id'], 'name') : '';
            $row['creator'] = $row['creator_id'] ? DB::fetch('select id,name from users where id=' . $row['creator_id'], 'name') : '';
            $this->map['no_id'] = $row['id'];
            if ($row['birth_date'] != '0000-00-00') {
                $row['birth_date'] = Date_Time::to_common_date($row['birth_date']);
            } else {
                $row['birth_date'] = '';
            }
            // $orderId = DB::escape(Url::get('orderId'));
            // $sql = "SELECT id,customer_group FROM orders_extra WHERE order_id = $orderId";
            // $query = DB::fetch($sql);
            // if (empty($row['crm_group_id'])) {
            //     $row['crm_group_id'] = $query['customer_group'];
            // }
            foreach ($row as $key => $value) {
                if (is_string($value) and !isset($_REQUEST[$key])) {
                    $_REQUEST[$key] = $value;
                }
            }
            $edit_mode = true;
            $this->map['logs'] = System::get_logs(false, "customer_id_={$row['id']}");
        } else {
            $edit_mode = false;
        }
        if (!isset($_REQUEST['mi_card'])) {
            $_REQUEST['mi_card'] = CrmCustomerDB::get_card(Url::sget('cid') ? $row['id'] : false);
        }
        if (!isset($_REQUEST['mi_shared_group'])) {
            $_REQUEST['mi_shared_group'] = CrmCustomerDB::get_share(Url::sget('cid') ? $row['id'] : false);
        }
        $customer_groups = CrmCustomerDB::get_customer_groups();
        $group_id_list = array('' => 'Chọn') + MiString::get_list($customer_groups);
        $sql = '
			select
				zone_provinces_v2.province_id as id, `zone_provinces_v2`.province_name as name
			from
				`zone_provinces_v2`
			where
				1=1
			order by
				zone_provinces_v2.province_name
		';
        $zones = DB::fetch_all($sql);
        $zone_id_list = array('' => 'Chọn') + MiString::get_list($zones);
        $this->map += ($edit_mode ? $row : array()) + array(
            'crm_group_id_list' => $group_id_list,
            'zone_id_list' => $zone_id_list,
        );

        $this->parse_layout('edit', $this->map);
        $this->parse_layout('script',$this->map);
    }

    public function init()
    {
        $this->map['orders'] = CrmCustomerDB::get_orders(Url::iget('id'));
        $this->map['source_id_list'] = array('Chọn') + MiString::get_list(AdminOrdersDB::get_source());
        $users = AdminOrdersDB::get_users(false, false, true);
        $this->map['user_id_list'] = array('' => 'Chọn') + MiString::get_list($users, 'full_name');
        $this->map['gender_list'] = array('0' => 'Chưa xác định', '1' => 'Nam', '2' => 'Nữ');
        $branches = CrmCustomerDB::get_other_groups();
        $branch_options = '<option value="">Chọn cơ sở</option>';
        foreach($branches as $key=>$val){
            $branch_options .= '<option value="'.$key.'">'.$val['name'].'</option>';
        }
        $this->map['branch_options'] = $branch_options;
        $this->map['customer_statuses']     = CrmCustomerDB::get_all_statuses();
        //$this->map['status_id_list']  = [''=>'Chọn phân loại'] + MiString::get_list($this->map['customer_statuses'], 'name');
    }
}
