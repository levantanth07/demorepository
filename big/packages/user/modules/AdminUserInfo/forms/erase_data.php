<?php
class EraseDataForm extends Form
{
    protected $map;
    protected $admin_tuha;
    protected $admin_group;
    protected $admin_mkt;
    function __construct()
    {
        $this->admin_tuha = (User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY))?true:false;
        $this->admin_group = (Session::get('admin_group'))?true:false;
        $this->admin_mkt = check_user_privilege('ADMIN_marketing')?true:false;
        Form::Form('AdminUserInfo');
        if($this->admin_group or $this->admin_tuha) {
            $this->add('name', new TextType(true, 'invalid_full_name', 0, 50));
            $this->add('address', new TextType(false, 'invalid_address', 0, 200));
            $this->add('phone', new PhoneType(false, 'invalid_phone_number'));
            $this->add('email', new EmailType(false, 'email_invalid'));
        }
        $this->link_css('assets/default/css/cms.css');
        $this->link_js('packages/core/includes/js/jquery/datepicker.js');
        $this->link_css('assets/default/css/jquery/datepicker.css');
    }
    function on_submit()
    {
        if($month_before=Url::get('month_before')){
            $group_id = Session::get('group_id');
            $shop_created_date = DB::fetch('select created from `groups` where id='.$group_id,'created');
            if($shop_created_date){
                $shop_created_time = strtotime($shop_created_date);
                $current_time = strtotime(date('Y-m-d'));
                if($shop_created_time<$current_time-$month_before*30*24*3600){
                    $min_time = $current_time - $month_before*30*24*3600;
                    $min_date = (date('Y-m-d',$min_time));
                    AdminUserInfoDB::process_group_data($group_id,$min_date);
                }else{
                    Url::js_redirect(true,'Shop có thời gian tạo trong '.$month_before.' tháng gần đây nên không thực hiện được.',['do']);
                }
            }else{
                Url::js_redirect(true,'Shop không có ngày tạo',['do']);
            }
        }else{
            Url::js_redirect(true,'Vui lòng chọn số tháng gần nhất',['do']);
        }
    }
    function draw()
    {
        $this->map =  array();
        $account_type = Session::get('account_type');
        $this->map['show_full_name'] = get_group_options('show_full_name');
        $_REQUEST['show_full_name'] = $this->map['show_full_name'];
        if($this->admin_tuha){
            $group_id = Url::iget('group_id')?Url::iget('group_id'):Session::get('group_id');
        }else{
            if($account_type==3){
                $group_id = Url::iget('group_id')?Url::iget('group_id'):Session::get('group_id');
            }else{
                $group_id = Session::get('group_id');
            }
        }
        $sql = '
			SELECT
				`groups`.*
			FROM
				`groups`
			WHERE
				groups.id='.$group_id.'
				'.((!$this->admin_tuha and $account_type==3 and Url::iget('group_id'))?' and master_group_id='.Url::iget('group_id'):'').'
				';
        $row = array();
        if($row = DB::fetch($sql)){
            $row['created'] = date('d/m/Y',strtotime($row['created']));
            $row += AdminUserInfoDB::get_group_info($group_id);
            $this->map += $row;
        }
        $this->map['month_before_list'] = [
            3=>'Giữ lại 3 tháng gần nhất',
            6=>'Giữ lại 6 tháng gần nhất',
            9=>'Giữ lại 9 tháng gần nhất',
            12=>'Giữ lại 12 tháng gần nhất',
        ];
        $min_hour = AdminUserInfo::$allow_earse_time[0];
        $max_hour = AdminUserInfo::$allow_earse_time[1];
        $current_hour = intval(date('H'));
        if(($current_hour>=$min_hour and $current_hour<=23) or ($current_hour<=$max_hour and $current_hour>0)){
            $this->parse_layout('erase_data',$this->map);
        }else{
            echo '<div class="alert alert-warning">Bạn chỉ được sử dụng tính năng này vào lúc từ '.$min_hour.'h đến '.$max_hour.'h ngày hôm sau.</div>';
        }
    }
}
?>