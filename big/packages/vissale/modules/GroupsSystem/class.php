<?php
class GroupsSystem extends Module{

    public static $permissions;

    function __construct($row){
        Module::Module($row);
        require_once 'db.php';

        self::$permissions = array(
            'xembaocao'     => 'Xem báo cáo',
            'tracuudonhang' => 'Tra cứu đơn hàng',
            'tracuunhansu'  => 'Tra cứu nhân sự',
            'xuatexcel'     => 'Xuất excel'
        );

        if(isset($arr[Url::get('page')]) and Url::get('page')!='portal_category'){
            $_REQUEST['type'] = $arr[Url::get('page')];
        }elseif(Url::get('page')!='portal_category'){
            $_REQUEST['type'] = 'NEWS';
        }elseif(Url::get('page')=='menu'){
            $_REQUEST['type'] = '';
        }
        $this->redirect_parameters = array('type');
        if(User::can_view(false,ANY_CATEGORY)){
            switch(URL::get('cmd')){
            case 'update_status':
                if(Url::get('status')){
                    if(is_array(URL::get('selected_ids')) and sizeof(URL::get('selected_ids'))>0){
                        $ids = URL::get('selected_ids');
                        foreach($ids as $key=>$val){
                            $this->update_status(Url::get('status'),$val);  
                        }
                        $this->export_cache();
                    }   
                }
                //exit();
                Url::redirect_current();
                break;
            case 'convert':
                $this->convert();
                exit();
                break;
            case 'get_name_id':
                require_once 'packages/core/includes/utils/vn_code.php';
                $name = trim(Url::get('name'));
                $name_id = convert_utf8_to_url_rewrite($name);
                echo $name_id;
                exit();
                break;
            case 'get_user_id':
                $this->get_user_id();
                exit();
                break;
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
            case 'view':
                $this->view_cmd();
                break;
            case 'move_up':
            case 'move_down':
                $this->move_cmd();
                break;

            case 'insert_or_update_rank':
                $this->insert_or_update_rank();
                exit;

            case 'delete_rank':
                $this->delete_rank();
                exit;
            default:
                $this->list_cmd();
                break;
            }
        }else{
            URL::access_denied();
        }
    }
    function get_user_id(){
        if($account_id = Url::get('account_id') and $row=DB::fetch('select id,username from users where username="'.$account_id.'"')){
            echo '{"id":"'.$row['id'].'"}';
        }else{
            echo '{"id":"NOT_EXISTED"}';
        }
    }
    function update_status($status,$id){
        DB::update('groups_system',array('status'=>$status),'id='.$id);
    }
    function delete_file(){
        if(Url::get('link') and file_exists(Url::get('link')) and User::can_delete(false,ANY_CATEGORY)){
            @unlink(Url::get('link'));
        }
        echo '<script>window.close();</script>';
    }
    function add_cmd(){
        if(User::can_add(false,ANY_CATEGORY)){
            require_once 'forms/edit.php';
            $this->add_form(new EditGroupsSystemForm());
        }else{
            Url::redirect_current();
        }
    }
    function delete_cmd()
    {
        $systemIDs = URL::get('selected_ids');

        if(!is_array($systemIDs) || !$systemIDs || !User::can_delete(false,ANY_CATEGORY)){
            Form::render_error('Bad Request !');
        }

        $msg = [
            'DELETE' => [],
            'DELETE_SUCCESS' => [],
            Systems::IS_PARENT => [],
            Systems::GROUPS_ARE_STILL_ACTIVE => [],
        ];
        foreach($systemIDs as $systemID){
        	$system = DB::fetch('SELECT `name`,`icon_url` FROM `groups_system` WHERE `id` = ' . $systemID);
            if(!$system){
                continue;
            }

            $flag = Systems::canRemoveSystemID($systemID);
            if($flag !== true){
                $msg[$flag][] = $system['name'];
                continue;
            }

            if(Systems::removeSystem($systemID) !== true){
                $msg['DELETE'][] = $system['name'];
                continue;
            }

            $msg['DELETE_SUCCESS'][] = $system['name'];

            if(file_exists($system['icon_url'])){
                @unlink($system['icon_url']);
            }
        }

        $messages = [];
        if(!empty($msg[Systems::IS_PARENT])){
            $systemsName = implode('</b>,<b>', $msg[Systems::IS_PARENT]);
            $messages[] = sprintf('Các hệ thống không xóa được do chứa hệ thống con: <b>%s</b>', $systemsName);
        }

        if(!empty($msg[Systems::GROUPS_ARE_STILL_ACTIVE])){
            $systemsName = implode('</b>,<b>', $msg[Systems::GROUPS_ARE_STILL_ACTIVE]);
            $messages[] = sprintf('Các hệ thống không xóa được do chứa các Hộ kinh doanh đang hoạt động: <b>%s</b>', $systemsName);
        }  

        if(!empty($msg['DELETE'])){
            $systemsName = implode('</b>,<b>', $msg['DELETE']);
            $messages[] = sprintf('Xóa hệ thống <b>%s</b> thất bại !', $systemsName);
        }       

        if(!empty($msg['DELETE_SUCCESS'])){
            $messages[] = 'Xóa thành công các hệ thống: <b>' . implode('</b>,<b>', $msg['DELETE_SUCCESS']) . '</b>';
        }

        if(!empty($messages)){
            Form::set_flash_message('DELETE_SYSTEM', implode('<br>', $messages));
        }

        Url::redirect_current();         
    }
    function edit_cmd(){
        // and User::can_edit(false,$category['structure_id'])
        if(Url::get('id') and $category=DB::fetch('select id,structure_id from `groups_system` where id='.intval(Url::get('id')))){
            require_once 'forms/edit.php';
            $this->add_form(new EditGroupsSystemForm());
        }else{
            Url::redirect_current();
        }
    }
    function list_cmd(){
        require_once 'forms/list.php';
        $this->add_form(new ListGroupsSystemForm());
    }
    function view_cmd(){
        if(User::can_view_detail(false,ANY_CATEGORY) and Url::check('id') and DB::exists_id('category',$_REQUEST['id'])){
            require_once 'forms/detail.php';
            $this->add_form(new GroupsSystemForm());
        }else{
            Url::redirect_current();
        }
    }
    function move_cmd(){
        if(User::can_edit(false,ANY_CATEGORY)and Url::check('id')and $category=DB::exists_id('category',$_REQUEST['id'])){
            if($category['structure_id']!=ID_ROOT){
                require_once 'packages/core/includes/system/si_database.php';
                si_move_position('category',' and portal_id="'.PORTAL_ID.'"');
            }
            Url::redirect_current();
        }else{
            Url::redirect_current();
        }
    }
    function convert($parent_id=false){
        //echo ID_ROOT;die;
        mysql_query("SET NAMES UTF8");
        //header("Content-Type: text/html; charset=utf-8");
        require_once 'packages/core/includes/system/si_database.php';
        require_once 'packages/core/includes/utils/vn_code.php';
        $sql = '
            select
                ocd.category_id as id,ocd.*,oc.*
            from
                oc_category_description as ocd
                inner join oc_category AS oc ON oc.category_id = ocd.category_id
            where
                '.($parent_id?'parent_id='.$parent_id.'':'1=1').'
            order by
                oc.parent_id, oc.sort_order
        ';
        $old_categories = DB::fetch_all($sql);
        foreach($old_categories as $key=>$val){
            $utf8 = $val['name']; // file must be UTF-8 encoded
            $name = utf8_encode($utf8);
            $new_row = array('id'=>$key,'name_1'=>$name,'type'=>'PRODUCT','status'=>'SHOW','portal_id'=>PORTAL_ID);
            $name_id = convert_utf8_to_url_rewrite($val['name']);
            $same = false;
            $new_row+=array('name_id'=>$name_id);
            $new_row['time'] = time();
            if($val['parent_id']){
                if(!DB::exists('select id from category where id='.$key.'')){
                    $this->id = DB::insert('category', $new_row+array('structure_id'=>si_child('category',structure_id('category',$val['parent_id']),'')));
                }
            }
            else{
                if(!DB::exists('select id from category where id='.$key.'')){   
                    $this->id = DB::insert('category', $new_row+array('structure_id'=>si_child('category',ID_ROOT,'')));
                }
            }
            if($old = DB::fetch('select id,name_id from category where name_id="'.$name_id.'" and type="PRODUCT"')){
                $same = true;
            }
            if($same){
                DB::update_id('category',array('name_id'=>$name_id.'_'.$this->id),$this->id);
            }
            $this->convert($key);
        }
    }


    private function delete_rank(){
        $response = new stdClass;
        $response->status = 'DELETE_ERROR';

        if(($data = Url::get('id'))<1){
            $response->status = 'DATA_INVALID';
            return $this->response($response);
        }

        // lay user id cua nguoi dung hien tai
        $user_id         = get_user_id();
        if(!$user_id) {
            $response->status = 'USER_NOT_FOUND';
            return $this->response($response);
        }

        // lay system_group id cha ma nguoi dung dang quan li
        $system_group_id = DB::fetch('select id,system_group_id from groups_system_account where user_id=' . $user_id . ' limit 0,1', 'system_group_id');
        if(!$system_group_id) {
            $response->status = 'SYSTEM_GROUP_NOT_FOUND';
            return $this->response($response);
        }

        // Cap nhat neu can cap nhat
        $sql_format = 'DELETE FROM `groups_system_rank` WHERE `user_id` = %d AND `group_system_id` = %d AND id = %d';
        $sql = sprintf($sql_format, $user_id, $system_group_id, $data);
        DB::query($sql);

        if(DB::$db_result){
            $response->status = 'DELETE_SUCCESS';
        }

        $response->ranks = $this->get_rank($system_group_id, $user_id);
        $this->response($response);
    }

    private function insert_or_update_rank(){
        $response = new stdClass;
        $response->status = 'INSERT_ERROR';

        if(!($data = json_decode(Url::get('data')))){
            $response->status = 'DATA_INVALID';
            return $this->response($response);
        }

        // lay user id cua nguoi dung hien tai
        $user_id         = get_user_id();
        if(!$user_id) {
            $response->status = 'USER_NOT_FOUND';
            return $this->response($response);
        }

        // lay system_group id cha ma nguoi dung dang quan li
        $system_group_id = DB::fetch('select id,system_group_id from groups_system_account where user_id=' . $user_id . ' limit 0,1', 'system_group_id');
        if(!$system_group_id) {
            $response->status = 'SYSTEM_GROUP_NOT_FOUND';
            return $this->response($response);
        }

        // Cap nhat neu can cap nhat
        $sql_format = 'UPDATE `groups_system_rank` SET `rank_name`="%s", `revenue_min`=%d 
        WHERE `user_id` = %d 
        AND `group_system_id` = %d AND id = %d';
        if(count($data->update)){
            foreach ($data->update as $key => $item) {
                $revenue_min = preg_replace('#[^\d\.]+#', '', $item->revenue_min);
                $revenue_min = trim($revenue_min);
                $sql = sprintf($sql_format, DB::escape($item->rank_name), $revenue_min, $user_id, $system_group_id, $item->id);
                DB::query($sql);
                $response->status = DB::$db_result ? 'UPDATE_SUCCESS' : 'UPDATE_ERROR'; 
            }
        }

        // insert neu co du lieu insert
        if(count($data->insert)){
            $inserts = [];
            foreach ($data->insert as $item) {
                if(!$item->rank_name || !$item->revenue_min)
                    continue;

                $revenue_min = preg_replace('#[^\d\.]+#', '', $item->revenue_min);
                $revenue_min = trim($revenue_min);

                $inserts[] = sprintf('(%d, %d, "%s", %d)', $user_id, $system_group_id, DB::escape($item->rank_name), $revenue_min);
            }

            $sql = sprintf('INSERT INTO `groups_system_rank` (`user_id`, `group_system_id`, `rank_name`, `revenue_min`) VALUES %s', implode(',', $inserts));
            DB::query($sql);

            if(DB::$db_result){
                $response->status = 'INSERT_SUCCESS';
            }
        }

        $response->ranks = $this->get_rank($system_group_id, $user_id);
        $this->response($response);
    }

    // Lấy ra danh sách các rank được thiết lập cho group cha, 
    // kết quả được sắp xếp theo revenue_min 
    function get_rank($system_group_id, $user_id){
        $sql = '
                SELECT 
                    groups_system_rank.*
                FROM 
                    groups_system_rank
                    INNER JOIN users ON users.id=groups_system_rank.user_id
                    INNER JOIN groups_system ON groups_system_rank.group_system_id = groups_system.id
                WHERE
                    groups_system_rank.group_system_id = '.$system_group_id.' AND
                    users.id = '.$user_id.'
                ORDER BY revenue_min DESC 
            ';

            return DB::fetch_all($sql);
    }

    private function response($object){
        header('Content-Type: application/json');
        exit(json_encode($object));
    }
}
?>