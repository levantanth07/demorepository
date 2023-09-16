<?php
class FTP 
{
//Media FTP account
    static $ftp_connect_id = false;             // connection id of this ftp server
    static $ftp_result = false;
    static $home = '';

    // thư mục con upload, hằng này đang được dùng ở module
    // -- packages\user\modules\UserAdmin
    const SUB_DIR_STORAGE = '/original/11_2020';


    function __construct($ftp_server, $ftp_port, $ftp_user, $ftp_password)
    {
        FTP::$home = '/var/www/media.tuha.vn/htdocs/';
        FTP::$ftp_connect_id = ftp_connect($ftp_server,$ftp_port);
        if(FTP::$ftp_connect_id)
        {
            $ftp_result = ftp_login(FTP::$ftp_connect_id,$ftp_user,$ftp_password);
            if(isset(FTP::$ftp_connect_id) and FTP::$ftp_connect_id)
            {
                if(!$ftp_result)
                {
                    ftp_close(FTP::$ftp_connect_id);
                    FTP::$ftp_connect_id = $ftp_result;
                }else{
                    ftp_pwd(FTP::$ftp_connect_id);
                    ftp_pasv(FTP::$ftp_connect_id, true);
                    //FTP::$home = ftp_pwd(FTP::$ftp_connect_id);
                }
            }
        }
        if(!FTP::$ftp_connect_id)
        {
            //die('Error: Could not connect to the ftp server!');
            return false;
        }
        return FTP::$ftp_connect_id;
    }
    public static function home()
    {
        ftp_chdir(FTP::$ftp_connect_id,FTP::$home);
    }

    public static function upload_file($field, $dir = 'upload/default', $thumbnail = false, $data_type = 'content', $type = 'IMAGE', $variant = false,$original = false)
    {
        set_time_limit(0);
        require_once 'packages/core/includes/utils/vn_code.php';
        if ($_FILES || $variant) {
            if ($type == 'IMAGE') {
                $type_file = 'pre|image/jpeg|image/png|image/gif|image/ico';
            } elseif ($type == 'FILE') {
                $type_file = Portal::get_setting('type_file_upload', 'doc|docx|flv|swf|jpg|jpeg|gif|png|swf|flv|mp3|wmv|wav');
            }
            $max_upload_file_size = 1 * 1024 * 1024;
            /*$upload_path = FTP::make_folder($dir . '/original/' . date('m_Y'));
            if ($thumbnail) {
                $thumb_path = FTP::make_folder($dir . '/thumb/' . date('m_Y'));
            }*/
            

            // check nguồn để nối chuỗi thư mục
            if($original == false){
                $upload_path = $dir . self::SUB_DIR_STORAGE;
                $thumb_path = $dir . '/thumb/11_2020';
            }
            else{
                $upload_path = $thumb_path = $dir;
            }

            self::make_folder($upload_path);
            self::make_folder($thumb_path);

            $new_name = array();
            if (!$variant) {
                $file_resource = $_FILES[$field];
                if ($file_resource and $file_resource['name'] and $file_resource['tmp_name']) {
                    $file_name = convert_utf8_to_latin($file_resource['name']);
                    $tmp_name = $file_resource['tmp_name'];
                    $file_type = $file_resource['type'];
                }
            } else {
                $file_name = convert_utf8_to_latin($variant['name']);
                $tmp_name = $variant['tmp_name'];
                $file_type = $variant['type'];
            } 
            if (!empty($file_name) and strpos($type_file, $file_type) == true and filesize($tmp_name) < $max_upload_file_size) {
                $file_name = preg_replace("/[^\w\.\-]/", '', $file_name);

                // lấy đuôi đuôi file
                $typefile = pathinfo($file_name, PATHINFO_EXTENSION);

                $file_name = str_replace(array("'",".$typefile"), "", $file_name);
                $file_name = $file_name.'_'.random_int(1000000,99999999).'.'.$typefile;

                if (FTP::f_exists($upload_path . '/' . $file_name)) {
                    $file_name = time() . '_' . $file_name;
                }
                $new_name['image_url'] = $upload_path . '/' . $file_name;
                if ($thumbnail) {
                    $new_name['thumb_url'] = $thumb_path . '/' . $file_name;
                }
                FTP::home();
                $returnValue = ftp_put(FTP::$ftp_connect_id, $upload_path . '/' . $file_name, $tmp_name, FTP_BINARY);
                if ($returnValue != FTP_FINISHED) {
                    return false;
                } else {
                    $new_name['image_url'] = 'https://media.tuha.vn/' . $new_name['image_url'];
                }
                return $new_name['image_url'];
            } else {
                FTP::home();
                return false;
            }
        }
        return false;
    }
    public static function upload_webcam_file($field,$groupId, $userId,$flag = false){
        set_time_limit(0);
        if ($flag == false) {
            require_once 'packages/core/includes/utils/vn_code.php';
        } else {
            require_once ROOT_PATH.'packages/core/includes/utils/vn_code.php';
        }
        if ($_FILES) {
            $type_file = 'pre|image/jpeg|image/png|image/gif|image/ico';
            $max_upload_file_size = 1 * 1024 * 1024;
            $upload_path = "upload/login_user_photos";
            //$upload_path = "upload/login_user_photos/".date('Y_m')."/".date('d');
            $new_name = array();
            $file_resource = $_FILES[$field];
            if ($file_resource and $file_resource['name'] and $file_resource['tmp_name']) {
                $file_name = $file_resource['name'];
                $tmp_name = $file_resource['tmp_name'];
                $file_type = $file_resource['type'];
            }
            if (!empty($file_name) and strpos($type_file, $file_type) == true and filesize($tmp_name) < $max_upload_file_size) {
                $file_name = date('H_i_s').'.'.$file_type;
                if(!FTP::f_exists($upload_path . '/' . $groupId)){
                    $upload_path = $upload_path . '/' . $groupId;
                    if(!FTP::make_folder($upload_path)){
                        return false;
                    }
                }
                if(!FTP::f_exists($upload_path . '/' . date('Y_m'))){
                    $upload_path = $upload_path . '/' . date('Y_m');
                    if(!FTP::make_folder($upload_path)){
                        return false;
                    }
                }
                if(!FTP::f_exists($upload_path . '/' . date('d'))){
                    $upload_path = $upload_path . '/' . date('d');
                    if(!FTP::make_folder($upload_path)){
                        return false;
                    }
                }
                if(!FTP::f_exists($upload_path . '/' . $userId)){
                    $upload_path = $upload_path . '/' . $userId;
                    if(!FTP::make_folder($upload_path)){
                        return false;
                    }
                }
                if (FTP::f_exists($upload_path . '/' . $file_name)) {
                    $file_name = time() . '_' . $file_name;
                }
                $new_name['image_url'] = $upload_path . '/' . $file_name;
                if(!FTP::make_folder($upload_path)){
                    return false;
                }
                FTP::home();
                $returnValue = ftp_put(FTP::$ftp_connect_id, $upload_path . '/' . $file_name, $tmp_name, FTP_BINARY);
                if ($returnValue != FTP_FINISHED) {
                    return false;
                } else {
                    $new_name['image_url'] = 'https://media.tuha.vn/' . $new_name['image_url'];
                }
                return $new_name['image_url'];
            } else {
                FTP::home();
                return false;
            }
        }
        return false;
    }
    function upload_multi_file($field,$dir='default',$thumbnail=false,$data_type='content',$type='IMAGE')
    {
        set_time_limit(0);
        if($_FILES)
        {
            if(isset($_FILES[$field]))
            {
                if($type=='IMAGE')
                {
                    $type_file = 'pre|image/jpeg|image/png|image/gif|image/ico';
                }
                elseif($type=='FILE')
                {
                    $type_file = Portal::get_setting('type_file_upload','doc|docx|flv|swf|jpg|jpeg|gif|png|swf|flv|mp3|wmv|wav');
                }
                $max_upload_file_size = 20*1024*1024;
                $upload_path = FTP::make_folder($dir.'/original/'.date('m_Y'));
                if($thumbnail)
                {
                    $thumb_path = FTP::make_folder($dir.'/thumb/'.date('m_Y'));
                    $small_thumb_path = FTP::make_folder($dir.'/small_thumb/'.date('m_Y'));
                }
                $new_name = array();
                require_once 'packages/core/includes/utils/vn_code.php';
                foreach($_FILES[$field]['name'] as $i=>$name)
                {
                    $file_name = '';
                    if($name and $_FILES[$field]['tmp_name'][$i])
                    {
                        $file_name = $name;
                        if(FTP::f_exists($upload_path.'/'.$file_name))
                        {
                            $file_name = time().'_'.$name;
                        }
                        $tmp_name = $_FILES[$field]['tmp_name'][$i];
                        $file_type = $_FILES[$field]['type'][$i];
                        //echo $folder;exit();
                        //echo $upload_path;exit();
                        if($file_name and strpos($type_file,$file_type)==true and filesize($tmp_name)< $max_upload_file_size)
                        {
                            $file_name = convert_utf8_to_latin($file_name);
                            $new_name[$i+1]['image_url'] = $upload_path.'/'.$file_name;
                            if($thumbnail)
                            {
                                $new_name[$i+1]['thumb_url'] = $thumb_path.'/'.$file_name;
                                $new_name[$i+1]['small_thumb_url'] = $small_thumb_path.'/'.$file_name;                          
                            }
                            FTP::home();
                            $returnValue = ftp_put(FTP::$ftp_connect_id, $upload_path.'/'.$file_name, $tmp_name, FTP_BINARY);
                            //upload thumb
                            $size_upload = 0;
                            if ($returnValue != FTP_FINISHED) 
                            {
                                $new_name[$i+1]['image_url'] = '';
                                return false;               
                            }
                            else
                            {
                                $new_name[$i+1]['image_url'] = 'https://media.tuha.vn/'.$new_name[$i+1]['image_url'];
                            }
                            if($thumbnail)
                            {                           
                                require_once 'packages/core/includes/utils/create_thumb.php';
                                $image = new Imagethumb($tmp_name,true);
                                // ---------- Create thumb --------------------
                                $image->getThumb('upload/temp/'.$file_name,250,150);
                                $return_thumb = ftp_put(FTP::$ftp_connect_id, $thumb_path.'/'.$file_name, 'upload/temp/'.$file_name, FTP_BINARY);
                                if($return_thumb != FTP_FINISHED)
                                {
                                    $new_name[$i+1]['thumb_url'] = '';  
                                }
                                else
                                {
                                    $new_name[$i+1]['thumb_url'] = 'https://media.tuha.vn/'.$new_name[$i+1]['thumb_url'];
                                }
                                @unlink('upload/'.$file_name);
                                //----------------- Create small_thumb --------------                               
                                $image->getThumb('upload/temp/'.$file_name,90,90);
                                $return_small_thumb = ftp_put(FTP::$ftp_connect_id, $small_thumb_path.'/'.$file_name, 'upload/temp/'.$file_name, FTP_BINARY);
                                if($return_small_thumb != FTP_FINISHED)
                                {
                                    $new_name[$i+1]['small_thumb_url'] = '';
                                }
                                else
                                {
                                    $new_name[$i+1]['small_thumb_url'] = 'https://media.tuha.vn/'.$new_name[$i+1]['small_thumb_url'];
                                }
                                @unlink('upload/'.$file_name);
                            }
                        }
                        else
                        {
                            $new_name[$i+1]['image_url'] = '';
                            $new_name[$i+1]['thumb_url'] ='';
                            $new_name[$i+1]['small_thumb_url'] = '';                            
                            FTP::home();
                            return false;
                        }
                    }
                }
                return $_REQUEST[$field] = $new_name;               
            }           
            
        }
    }
    function delete_file($file)
    {
        FTP::home();
        if(ftp_size(FTP::$ftp_connect_id,$file))
        {
            return @ftp_delete(FTP::$ftp_connect_id,$file);
        }
        return false;
    }
    function ftp_mksubdirs($ftpcon,$ftpbasedir,$ftpath){
        @ftp_chdir($ftpcon, $ftpbasedir); // /var/www/uploads
        $parts = explode('/',$ftpath); // 2013/06/11/username
        foreach($parts as $part){
            if(!@ftp_chdir($ftpcon, $part)){
                ftp_mkdir($ftpcon, $part);
                ftp_chdir($ftpcon, $part);
                //ftp_chmod($ftpcon, 0777, $part);
            }
        }
    }
    public static function make_folder($path,$permissions = NULL){
        FTP::home();
        @ftp_mkdir(FTP::$ftp_connect_id,$path);//2019
        return $path;
    }
    function rename($old_file, $new_file, $move = FALSE)
    {
        $result = @ftp_rename(FTP::$ftp_connect_id, $old_file, $new_file);

        if ($result === FALSE)
        {
            return FALSE;
        }

        return TRUE;
    
    }
    function delete_folder($filepath)
    {
        $filepath = preg_replace("/(.+?)\/*$/", "\\1/",  $filepath);

        $list = FTP::list_files($filepath);

        if ($list !== FALSE AND count($list) > 0)
        {
            foreach ($list as $item)
            {
                // If we can't delete the item it's probaly a folder so
                // we'll recursively call delete_dir()
                if ( ! @ftp_delete(FTP::$ftp_connect_id, $item))
                {
                    FTP::delete_folder($item);
                }
            }
        }

        $result = @ftp_rmdir(FTP::$ftp_connect_id, $filepath);

        if ($result === FALSE)
        {
            return FALSE;
        }

        return TRUE;
    }
    function list_files($path = '.')
    {
        return ftp_nlist(FTP::$ftp_connect_id, $path);
    }
    
    //-----------
    static function check_dir($dir)
    {
        return ftp_chdir(FTP::$ftp_connect_id,$dir);
    }
    function get_file($dir)
    {
        $files = array();
        $filetypes = array('-'=>'FILE', 'd'=>'DIR', 'l'=>'LINK');
        $month_data = array(
            'Jan'=>1,
            'Feb'=>2,
            'Mar'=>3,
            'Apr'=>4,
            'May'=>5,
            'Jun'=>6,
            'Jul'=>7,
            'Aug'=>8,
            'Sep'=>9,
            'Otc'=>10,
            'Nov'=>11,
            'Dec'=>12
        );
        $data = ftp_rawlist(FTP::$ftp_connect_id,$dir,true);
        $i = 0;
        if($data)
        {
            foreach($data as $line) {
                $i++;
                if(substr(strtolower($line), 0, 5) == 'total') continue; # first line, skip it
                preg_match('/'. str_repeat('([^\s]+)\s+', 7) .'([^\s]+) (.*)/', $line, $matches); # Here be Dragons
                list($permissions, $children, $owner, $group, $size, $month, $day, $time, $name) = array_slice($matches, 1);
                if (! in_array($permissions[0], array_keys($filetypes))) continue;
                $type = $filetypes[$permissions[0]];
                $date = date('d/m/y H:i', (strpos($time, ':') ? mktime(substr($time, 0, 2), substr($time, -2), 0, $month_data[$month], $day) : mktime(0,0,0,$month_data[$month], $day, $time) ) );
                $files[$i] = array(
                    'name'=>$name,
                    'type'=>$type,
                    'permissions'=>substr($permissions, 1),
                    'children'=>$children,
                    'owner'=>$owner,
                    'group'=>$group,
                    'size'=>$size,
                    'date'=>$date
                );
            }
        }
        return $files;
    }
    //---------------------------------
    public static function f_exists($file){
        $size = ftp_size(FTP::$ftp_connect_id,$file);
        if($size!=-1){
            return true;
        }else{
            return false;
        }
    }
    function close()
    {
        ftp_close(FTP::$ftp_connect_id);
    }
}
define('MEDIA_SERVER','123.31.43.35');
define('FTP_PORT','21');
define('FTP_USER','paldev');
define('FTP_PASSWORD','Abc!@#4512');
define('ROOT_UPLOAD_FOLDER','upload');
define('PATH','https://media.tuha.vn/');

if(defined('USE_FTP')){
    $ftp = new FTP(MEDIA_SERVER,FTP_PORT, FTP_USER, FTP_PASSWORD);
}
?>