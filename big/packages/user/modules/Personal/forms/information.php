<?php
class PersonalInformationForm extends Form
{
    protected $map;
    function __construct()
    {
        Form::Form('personal');
        $this->add('full_name',new TextType(true,'invalid_full_name',0,50));
        $this->add('address',new TextType(false,'invalid_address',0,200));
        //$this->add('phone',new PhoneType(false,'invalid_phone_number'));
        $this->add('birth_date',new DateType(false,false,0,32));
        $this->add('email',new EmailType(false,'email_invalid'));
        $this->add('vaccination_count',new IntType(false,'vaccination_count'));
        $this->add('vaccination_status',new IntType(false,'vaccination_status'));
        $this->link_js('packages/core/includes/js/jquery/datepicker.js');
        $this->link_css('assets/default/css/jquery/datepicker.css');
        
    }
    function on_submit()
    {   
        if($this->check() && $this->validateUploadImage('image_url'))
        {    
            $full_name = DB::escape(Url::get('full_name'));
            $account_id = Session::get('user_id');
            $row = array(
                'full_name'=>$full_name
                ,'address'
                ,'birth_date'=>Url::get('birth_date')?Date_Time::to_sql_date(Url::get('birth_date')):'0000-00-00'
                ,'identity_card'
                ,'gender'
                ,'zone_id'
                ,'fax'
                ,'skype'
                ,'email'
                ,'website'
                    ,'note1'
                    ,'note2'
                    ,'prefix_post_code'
            );
            DB::update('party',$row,' user_id = "'.$account_id.'"');
            DB::update('users',['name'=>$full_name,'address'],' username = "'.$account_id.'"');
            
            $this->upsertVaccination();
            $this->updateUserAvatar();
            $this->set_flash_message('update', 'success');
        }
    }

    /**
     * Cập nhật ảnh avatar
     *
     * @return     bool  ( description_of_the_return_value )
     */
    private function updateUserAvatar(){
        require_once 'packages/core/includes/utils/ftp.php';
        require_once 'packages/core/includes/utils/upload_file.php';
        
        $dir = 'upload/default/groups/'.Session::get('group_id');
        $username = Session::get('user_id');
        $image_url = FTP::upload_file('image_url', $dir, true,'content', 'IMAGE', false, true);
        
        return $image_url && DB::update('party',['image_url' => $image_url],' user_id = "'.$username.'"');
    }

    /**
     * { function_description }
     */
    private function upsertVaccination()
    {   
        if(!$userID = $_SESSION['user_data']['user_id'] ?? 0){
            return;
        }

        if($saved = $this->getVaccination($userID)){
            return $this->logUpdateVaccination(
                $this->updateVaccination($userID, $saved),
                $saved,
                Session::get('user_id')
            );
        }

        return $this->logInsertVaccination(
            $this->insertVaccination($userID),
            Session::get('user_id')
        );
    }

    /**
     * Gets the vaccination infomation.
     *
     * @param      int     $userID  The user id
     *
     * @return     array  The vaccination infomation.
     */
    private function getVaccination(int $userID)
    {
        return DB::fetch('SELECT `user_id`, `count`, `status`, `note` FROM vaccination WHERE `user_id` = ' . $userID);
    }

    /**
     * Cập nhật thông tin tiêm chủng
     *
     * @param      int     $userID  The user id
     * @param      array   $saved   The saved
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function updateVaccination(int $userID, array $saved)
    {   
        $updateFields = [
            'count'      => URL::getUInt('vaccination_count'),
            'status'     => URL::getUInt('vaccination_status'),
            'note'       => URL::getString('vaccination_note'),
            'updated_by' => $userID,
            'updated_at' => date('Y-m-d h:i:s')
        ];

        return DB::update('vaccination', $updateFields, 'user_id=' . $userID) ? $updateFields : false;
    }

    /**
     * Thêm thông tin tiêm chủng
     *
     * @param      int     $userID  The user id
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function insertVaccination(int $userID)
    {
        $insertFields = [
            'user_id'    => $userID,
            'count'      => URL::getUInt('vaccination_count'),
            'status'     => URL::getUInt('vaccination_status'),
            'note'       => URL::getString('vaccination_note'),
            'created_by' => $userID,
            'created_at' => date('Y-m-d h:i:s')
        ];

        return DB::insert('vaccination', $insertFields) ? $insertFields : false;
    }

    /**
     * Gets the vaccination count.
     *
     * @param      int   $ID     { parameter_description }
     */
    private function getVaccinationCount(int $ID)
    {
        return Personal::getVaccinationCountFields()[$ID] ?? 'unknow';
    }

    /**
     * Gets the vaccination status.
     *
     * @param      int    $ID     { parameter_description }
     *
     * @return     array  The vaccination status.
     */
    private function getVaccinationStatus(int $ID)
    {
        return Personal::getVaccinationStatusFields()[$ID] ?? 'unknow';
    }

    /**
     * Logs an insert vaccination.
     *
     * @param      array   $insertResults  The insert results
     * @param      string  $userName       The user name
     */
    private function logInsertVaccination($insertResults, string $userName)
    {
        if(Session::is_set('debuger_id')) return;
        
        if(!is_array($insertResults)){
            return;
        }

        $logs = ['Thêm thông tin tiêm chủng tài khoản "<strong>'.$userName.'</strong>":<br>'];
        
        $logs[] = sprintf(
            '<div><strong>Số mũi</strong> "<span style="color:#1966ff">%s</span>"</div>', 
            $this->getVaccinationCount($insertResults['count'])
        );

        $logs[] = sprintf(
            '<div><strong>Trạng thái sức khỏe</strong> "<span style="color:#1966ff">%s</span>"</div>', 
            $this->getVaccinationStatus($insertResults['status'])
        );

        $logs[] = sprintf(
            '<div><strong>Ghi chú</strong> "<span style="color:#1966ff">%s</span>"</div>', 
            $insertResults['note']
        );

        System::account_log(0, implode('', $logs),MODULE_USERADMIN);
    }

    /**
     * Logs an update vaccination.
     *
     * @param      array   $updateFields  The update fields
     * @param      array   $saved         The saved
     * @param      string  $userName      The user name
     */
    private function logUpdateVaccination($updateFields, array $saved, string $userName)
    {
        if(Session::is_set('debuger_id')) return;
        
        if(!is_array($updateFields)){
            return;
        }

        $logs = ['Cập nhật thông tin tiêm chủng tài khoản "<strong>'.$userName.'</strong>":<br>'];
        $fields = ['count' => 'Số mũi', 'status' => 'Trạng thái sức khỏe', 'note' => 'Ghi chú'];

        foreach($fields as $fieldName => $fieldTxt){
        
            if($saved[$fieldName] == $updateFields[$fieldName]){
                continue;
            }

            switch($fieldName){
                case 'count':
                    $old = $this->getVaccinationCount($saved['count']);
                    $new = $this->getVaccinationCount($updateFields['count']);
                    break;

                case 'status':
                    $old = $this->getVaccinationStatus($saved['status']);
                    $new = $this->getVaccinationStatus($updateFields['status']);
                    break;

                default: 
                    $old = $saved[$fieldName];
                    $new = $updateFields[$fieldName];
            }

            $logs[] = sprintf(
                '<div><strong>%s</strong> từ "<span style="color:#1966ff">%s</span>" => "<span style="color:#ff641c">%s</span>"</div>',
                $fieldTxt,
                $old,
                $new
            );
        }

        if(count($logs) > 1){
            System::account_log(0, implode('', $logs),MODULE_USERADMIN);
        }
    }

    function draw()
    {
        $sql = '
            SELECT
                party.*
                ,users.phone
                ,users.identity_card
                ,`account`.last_online_time
                ,`account`.`create_date` as create_date
                ,users.id as user_id_
                ,ag.name as account_group
                ,account.group_id
                ,account.id as account_id
                ,account.admin_group
                ,vaccination.count as vaccination_count
                ,vaccination.status as vaccination_status
                ,vaccination.note as vaccination_note
            FROM
                `account`
                JOIN party on party.user_id=account.id
                JOIN users on users.username=account.id
                LEFT JOIN vaccination on users.id=vaccination.user_id
                LEFT JOIN account_group ag on ag.id=account.account_group_id
            WHERE
                `account`.id="'.Session::get('user_id').'"
                 and party.type="USER"';
        $row = array();
        $this->map['total_confirmed'] = 0;
        $this->map['total_unconfirmed'] = 0;
        $this->map['total_successed'] = 0;
        $this->map['admin_group'] = false;
        if($row = DB::fetch($sql)){
            $this->map['admin_group'] = $row['admin_group'];
            $this->map['account_group'] = $row['account_group'];
            $this->map['vaccination_count'] = $row['vaccination_count'];
            $this->map['vaccination_status'] = $row['vaccination_status'];
            $this->map['vaccination_note'] = $row['vaccination_note'];

            $row['birth_date'] = Date_Time::to_common_date($row['birth_date']);
            foreach($row as $key=>$value)
            {
                if(is_string($value) and !isset($_REQUEST[$key]))
                {
                    $_REQUEST[$key] = $value;
                }
            }
        }

        

        //require_once 'cache/tables/zone.cache.php';
        $zone = DB::fetch_all('select id,name from zone where '.IDStructure::direct_child_cond(ID_ROOT).' order by structure_id');
        require_once 'packages/user/modules/UserAdmin/db.php';
        $this->map['roles_activities'] = UserAdminDB::get_roles_of_the_user($row['account_id'],$row['group_id'],true);
        $this->map['account_group_admins'] = UserAdminDB::get_account_groups_of_user($row['user_id_']);
        $this->parse_layout('information',$this->map+array(
            'gender_list'=>array('0' => 'Chưa xác định', '1'=>'Nam','2'=>'Nữ')
            ,'zone_id_list'=>MiString::get_list($zone)
        ));
    }
}
?>
