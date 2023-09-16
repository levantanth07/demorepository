<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require ROOT_PATH.'vendor/autoload.php';

class Timer{
	var $starttime = 0;
	function start_timer(){
			$mtime = microtime();
			$mtime = explode (' ', $mtime);
			$mtime = $mtime[1] + $mtime[0];
			$this->starttime = $mtime;
	}
	function get_timer(){
		$mtime = microtime();
		$mtime = explode (' ', $mtime);
		$mtime = $mtime[1] + $mtime[0];
		return number_format($mtime-$this->starttime,4);
	}
}
class System{
	static $false = false;
    static function is_local(){
        return is_local();
    }
    static function send_mail($from,$to,$subject,$content,$cc=false){
        $mail = new PHPMailer(true);
        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'noreply.tuha@gmail.com';                     // SMTP username
            $mail->Password   = 'talavodich@123';                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
            $mail->Port       = 587;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom($from, 'QLBH - '.date('H:i d/m/Y'));
            $mail->addAddress($to);     // Add a recipient
            //$mail->addAddress('ellen@example.com');               // Name is optional
            //$mail->addReplyTo('info@example.com', 'Information');
            if($cc){
                $mail->addCC($cc);
            }
            //$mail->addBCC('bcc@example.com');

            // Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $content;
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            $mail->send();
            return true;
        } catch (Exception $e) {
			echo "Lỗi gửi email.";
            die;
        }
    }
	static function send_mail1($from,$to,$subject,$content,$attachment=array()){

	}
	static function halt(){
		Session::end();
		DB::close();
		exit();
	}
    static function get_logs($module_id=0,$parameter=false){
        $module_id = is_object(Module::$current)?Module::block_id():$module_id;
        $sql = 'select id,title,description,time,user_id from log where module_id='.$module_id.' '.($parameter?' and parameter="'.$parameter.'"':'').' order by time desc limit 0,100';
        return DB::fetch_all($sql);
    }
	static function account_log($type=0, $content,$module_id=0){
        if ( defined('LOG_V2') && !empty(LOG_V2)) {
            $groupName = DB::fetch('SELECT id,name FROM groups WHERE id=' . Session::get('group_id'));
            if(@require_once(ROOT_PATH.'packages/vissale/lib/php/log.php')) {
                $dataLog = array(
                    'log_type' => (int)$type,
                    'content' => $content,
                    'time' => time(),
                    'account_id' => Session::get('user_id'),
                    'group_id' => (int)Session::get('group_id'),
                    'group_name' => $groupName['name'],
                    'module_id' => (int)$module_id,
                    'ip' => System::get_client_ip_env()
                );
                storeAccountLog($dataLog);
            }
        } else {
            $data = array(
                'log_type'=>$type,
                'content'=>$content,
                'time'=>time(),
                'account_id'=>Session::get('user_id'),
                'group_id'=>Session::get('group_id'),
                'module_id'=>$module_id,
                'ip'=>System::get_client_ip_env()
            );
            //DB::insert('account_log', $data);
            LogHandler::sendSqlToQueue($data);
        }
	}
	static function log($type, $title='', $description = '', $parameter = '', $note = '', $user_id = false, $arrPatchData = array()){
		if(Session::is_set('debuger_id')) return;
        $arrData = array(
            'type'=>$type,
            'module_id'=>is_object(Module::$current)?Module::block_id():0,
            'title'=>$title,
            'description'=>$description,
            'parameter'=>$parameter,
            'note'=>$note,
            'time'=>time(),
            'user_id'=> $user_id ? $user_id : (is_object(User::$current) ? User::id() : 0),
            'portal_id'=>PORTAL_ID,
            'group_id'=>Session::get('group_id'),
            'ip'=>System::get_client_ip_env()
        );
        if(count($arrPatchData) > 0){
            foreach($arrPatchData as $keyPatchData => $rowPatchData){
                $arrData[$keyPatchData] = $rowPatchData;
            }
        }
		DB::insert('log', $arrData);
	}
    static function logHT($type, $title='', $description = '', $parameter = '', $note = '', $user_id = false, $arrPatchData = array()){
        if(Session::is_set('debuger_id')) return;
        $arrData = array(
            'type'=>$type,
            'module_id'=>is_object(Module::$current)?Module::block_id():0,
            'title'=>$title,
            'description'=>$description,
            'parameter'=>$parameter,
            'note'=>$note,
            'time'=>time(),
            'user_id'=> $user_id ? $user_id : (is_object(User::$current) ? User::id() : 0),
            'portal_id'=>PORTAL_ID,
            'group_id'=>-1,
            'ip'=>System::get_client_ip_env()
        );
        if(count($arrPatchData) > 0){
            foreach($arrPatchData as $keyPatchData => $rowPatchData){
                $arrData[$keyPatchData] = $rowPatchData;
            }
        }
        DB::insert('log', $arrData);
    }
	static function set_page_title($title){
		echo '<script type="text/javascript">document.title=\''.str_replace('\'','&quot;',$title).'\';</script>';
	}
	static function set_page_description($description){
		echo '<script type="text/javascript">document.description=\''.str_replace('\'','&quot;',$description).'\';</script>';
	}
	static function add_meta_tag($tags){
		global $meta_tags;
		if(isset($meta_tags)){
	 		$meta_tags.=$tags;
		}else{
			$meta_tags=$tags;
		}
	}
	static function display_number($num,$discount = false){
		$num = (float)$num;
		if($discount){
			$num = $num - $num*SYSTEM_DISCOUNT/100;
		}
		if($num==round($num)){
			return number_format($num,0);
		}else{
			return number_format($num,2);
		}
	}
	static function display_number_report($num){
		$num = $num?$num:0;
		return number_format($num,2);
	}
	static function calculate_number($num,$discount = false){
		$num = str_replace(',','',$num);
		if($discount){
			$num = $num - $num*SYSTEM_DISCOUNT/100;
			$num = System::display_number(ceil($num/1000)*1000);
		}	
		return $num;
	}
	static function debug($array){
		echo '<pre style="background: #edef8b;margin:20px;">';
		print_r($array);
		echo '</pre>';
	}
	static function ip_number($ipv4){
			$ips = explode('.',$ipv4);
			return $ips[0] * 16777216 + $ips[1] * 65536 + $ips[2] * 256 + $ips[3];
	}		
	static function display_vnd_number($num,$coma = true,$discount = false){//display_vnd_number\(([^\,]+),([^\,]+),([^\,]+)\)
		$exchange_rate = Session::get('site_exchange_vnd');
		if($discount){
			$num = $num - $num*SYSTEM_DISCOUNT/100;
		}
		if($coma){
			return System::display_number(ceil(($num*$exchange_rate)/1000)*1000);
		}else{
			return ceil(($num*$exchange_rate)/1000)*1000;
		}
	}
    static function kksort (&$array, $key) {
        array_multisort(array_map(function($element) {
            return $element['total'];
        }, $array), SORT_DESC, $array);
    }
	static function sksort(&$array, $subfield,$sort_ascending='ASC'){
		$sortarray = array();
		if(is_array($array) and !empty($array)){
            foreach ($array as $key => $row)
            {
                $sortarray[$key] = $row[$subfield];
            }
            if($sort_ascending=='ASC'){
                array_multisort($sortarray,SORT_ASC, $array);
            }else{
                array_multisort($sortarray,SORT_DESC, $array);
            }
            if(is_array($array)){
                $array = array_combine(range(1, count($array)), array_values($array));
            }
        }
	}
	static function sksort_old($array, $subkey="id", $sort_ascending=false) {
		$temp_array = array();
		if (count($array))
			$temp_array[key($array)] = array_shift($array);

		foreach($array as $key => $val){
			$offset = 0;
			$found = false;
			foreach($temp_array as $tmp_key => $tmp_val)
			{
				if(!$found and strtolower($val[$subkey]) > strtolower($tmp_val[$subkey]))
				{
					$temp_array = array_merge(    (array)array_slice($temp_array,0,$offset),
							array($key => $val),
							array_slice($temp_array,$offset)
					);
					$found = true;
				}
				$offset++;
			}
			if(!$found) $temp_array = array_merge($temp_array, array($key => $val));
		}

		if ($sort_ascending) $array = array_reverse($temp_array);

		else $array = $temp_array;
	}
	static function check_user_agent(){//check moible or pc browser
		return false;
		/*if(Url::get('web_skin')){
			Session::set('web_skin',true);
		}
		if(Session::is_set('web_skin')){// truong hop ep dung` giao dien web
			return false;
		}
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
			return true;
		}else{
			return false;
		}*/
	}
	static function get_client_ip_env() {
        $ipAddress = (isset($_SERVER["HTTP_CF_CONNECTING_IP"]) and $_SERVER["HTTP_CF_CONNECTING_IP"])?$_SERVER["HTTP_CF_CONNECTING_IP"]:'';
        if($ipAddress){
            return $ipAddress;
        }
	    $ipaddress = '';
	    if (getenv('HTTP_CLIENT_IP'))
	        $ipaddress = getenv('HTTP_CLIENT_IP');
	    else if(getenv('HTTP_X_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	    else if(getenv('HTTP_X_FORWARDED'))
	        $ipaddress = getenv('HTTP_X_FORWARDED');
	    else if(getenv('HTTP_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_FORWARDED_FOR');
	    else if(getenv('HTTP_FORWARDED'))
	        $ipaddress = getenv('HTTP_FORWARDED');
	    else if(getenv('REMOTE_ADDR'))
	        $ipaddress = getenv('REMOTE_ADDR');
	    else
	        $ipaddress = 'UNKNOWN';
	 
	    return $ipaddress;
	}
    static function generate_log_message($old_data, $new_data, $map_msg,$arrIdToText = array())
    {
        $html = '';
        foreach ($map_msg as $key => $message) {
            if (isset($old_data[$key]) and isset($new_data[$key]) and $old_data[$key] != $new_data[$key]) {
                $oldText = $old_data[$key];
                if(isset($arrIdToText[$key])){
                    if(empty($oldText)){
                        $oldText = '';
                    }
                    $oldText = $arrIdToText[$key][$oldText];
                }
                $newText = $new_data[$key];
                if(isset($arrIdToText[$key])){
                    if(empty($newText)){
                        $newText = '';
                    }
                    $newText = $arrIdToText[$key][$newText];
                }
                $html .= "<strong>{$message}</strong>: $oldText <strong class='text-red'>=></strong> $newText <br>";
            }
        }
        return $html;
    }
    static function get_base_url()
    {
        return System::getProtocol() .'://' . $_SERVER['HTTP_HOST'];
    }

    /**
     * Gets the protocol.
     *
     * @return     <type>  The protocol.
     */
    public static function getProtocol() 
    {
		return defined('PROTOCOL') ? PROTOCOL : 'http';
	}
} //  end class system
class MiString{
	static function create_tags1($string,$page){
		$result = '';
		$arr = explode(',',$string);
		$i = 0;
		foreach($arr as $key=>$value){
			$value = trim($value);
			$result .= (($i>0)?', ':'').'<a class="badge" target="_blank" href="'.$page.'/tags/'.str_replace('+','-',urlencode($value)).'">'.$value.'</a>';
			$i++;
		}
		return $result;
	}
	static function create_tags($string,$page){
		$result = '';
		if($string){		
			$arr = explode(',',$string);
			$i = 0;
			foreach($arr as $key=>$value){
				$value = trim($value);
				$result .= (($i>0)?', ':'').'<a class="badge" target="_blank" href="'.$page.'?tags='.$value.'" title="'.$value.'">'.$value.'</a>';
				$i++;
			}
		}
		return $result;
	}
	static function array2suggest($array,$multi=true){
		$st = '[';
		$i = 0;
		$size_of_array = sizeof($array);
		foreach($array as $key=>$values){
			if($multi){
				$st.='{';
				/*if(is_array($values)){
					$f = true;
					foreach($values as $key=>$value){
						$st .= $key.':"'.MiString::string2js($value).'",';
					}
				}*/
				if(isset($values['name'])){
					$st.='name:"'.MiString::string2js($values['name']).'",to:"'.$key.'", id:"'.$key.'"';
				}else{
					$st.='name:"'.$key.'",to:"'.$key.'", id:"'.$key.'"';
				}
				$i++;
				if($i==$size_of_array){
					$st.='}';
				}else{
					$st.='},';
				}
			}else{
				if($i==0){
					$st.='"'.$key.'"';
				}else{
					$st.=',"'.$key.'"';
				}
				$i++;
			}
		}
		$st.= ']';
		return $st;
	}
	static function str_multi_language($vn,$en=false){
		if(Portal::language()==1){
			return $vn;
		}
		else
		if(Portal::language()==2){
			return ($en!=false)?$en:$vn;
		}
		else
		if(Portal::language()==3){
			return ($en!=false)?$en:$vn;
		}
		else
		if(Portal::language()==4){
			return ($en!=false)?$en:$vn;
		}else{
			return ($en!=false)?$en:$vn;
		}
	}
	static function language_field_list($name){
		$languages = DB::select_all('language');
		$st = '';
		foreach($languages as $language){
			if($st){
				$st .= ',';
			}
			$st .= $name.'_'.$language['id'];
		}
		return $st;
	}
	static function display_sort_title($str,$number,$word=true){
		if($word){// truong hop cat theo tu
			$c = str_word_count($str);
			$array1=array($c);
			$new_str='';
			if($number < $c){
				$array1 = explode(" ",$str);
				$i=0;
				while($i<sizeof($array1)){
					if($i<$number){
						$new_str.=$array1[$i].' ';
					}
					$i++;
				}
				return $new_str.'...';
			}else{
				return $str;
			}
		}else{// cat theo ky tu
			$len = strlen($str);
			if($len > $number){
				$new_str = substr($str,0,$number);
				return $new_str.'...';
			}else{
				return $str;
			}
		}
	}
	static function html_normalize($st){
		return str_replace(array('"','<'),array('&quot;','&lt;'),$st);
	}
	static function string2js($st){
		return strtr($st, array('\''=>'\\\'','\\'=>'\\\\','\n'=>'',chr(10)=>'\\
',chr(13)=>''));
	}
	static function array2js($array){
		$st = '{';
		foreach($array as $key=>$value){
			if($st!='{'){
				$st.='
,';
			}
			$st.='\''.MiString::string2js($key).'\':';
			if(is_array($value)){
				$st .= MiString::array2js($value);
			}else{
				$st .= '\''.MiString::string2js($value).'\'';
			}
		}
		return $st.'
}';
	}
	static function array2tree(&$items,$items_name){
		//$structure_ids = array(ID_ROOT=>1);
		$show_items = array();
		$min = -1;
		foreach($items as $item){
			if($min==-1){
				$min = IDStructure::level($item['structure_id']);
			}
			$structure_ids[number_format($item['structure_id'],0,'','')] = $item['id'];
			//echo number_format($item['structure_id'],0,'','').'<br>';
			if(IDStructure::level($item['structure_id'])<=$min){
				$show_items[$item['id']] = $item+(isset($item['childs'])?array():array($items_name=>array()));
			}else{
				$st = '';
				$parent = $item['structure_id'];
				while(($level=IDStructure::level($parent = IDStructure::parent($parent)))>=$min and $parent and isset($structure_ids[number_format($parent,0,'','')])){
					$st = '['.$structure_ids[number_format($parent,0,'','')].'][\''.$items_name.'\']'.$st;
				}
				//echo number_format($parent,0,'','').' '.$st.'<br>';
				if($level<$min or $level==0){
					//echo '$show_items'.$st.'['.$item['id'].']<br>';
					eval('$show_items'.$st.'['.$item['id'].'] = $item+array($items_name=>array());');
				}
			}
		}
		return $show_items;
	}
//convert to vnnumeric
	static function convert_to_vnnumeric($st){
		//$temp = str_replace('.','',$st);
		return str_replace(',','',$st);
	}
//convert string to number
	static function to_number($st,$count=0){
		$temp = substr($st,$count);
		$n = 0;
		for($i=0;$i<strlen($temp);$i++){
			$n = $n*10 + $temp[$i];
		}
		return $n;
	}
	static function get_list($items, $field_name=false,$indent=false){
		$item_list = array();
		if(is_array($items) and !empty($items)){
            foreach($items as $item){
                if(!$field_name){
                    $field_name=isset($item['name'])?'name':(isset($item['title'])?'title':(isset($item['name_'.Portal::language()])?'name_'.Portal::language():(isset($item['title_'.Portal::language()])?'title_'.Portal::language():'id')));
                }
                if(isset($item['structure_id'])){
                    $level = 0;//IDStructure::level($item['structure_id']);
                    for($i=0;$i<$level;$i++){
                        $item[$field_name] = ($indent?$indent:"").$item[$field_name];
                    }
                }
                $item_list[$item['id']]=isset($item[$field_name])?$item[$field_name]:'';
            }
        }
		return $item_list;
	}
	static function system_md5($string){
		return md5(CATBE.$string);
	}
}
class Date_Time{
    static function get_age($year){
        $year = $year?$year:0;
        $age = intval(date('Y')) - $year;
        return ($age>0 and $year)?$age:'';
    }
	static function to_sql_date($date){
		$a = explode('/',$date);
		if(sizeof($a)==3 and is_numeric($a[1]) and is_numeric($a[2]) and is_numeric($a[0]) and checkdate($a[1],$a[0],$a[2])){
			return ($a[2].'-'.$a[1].'-'.$a[0]);
		}else{
			return false;
		}
	}
	static function to_common_date($date){
		$a = explode('-',$date);
		if(sizeof($a)==3 and $a[0]!='0000'){
			return ($a[2].'/'.$a[1].'/'.$a[0]);
		}else{
			return false;
		}
	}
	// format 01/01/2006
	static function to_time($date){
		if(preg_match('/(\d+)\/(\d+)\/(\d+)\s*(\d+)\:(\d+)/',$date,$patterns)){
			return strtotime($patterns[2].'/'.$patterns[1].'/'.$patterns[3])+$patterns[4]*3600+$patterns[5]*60;
		}

		$a = explode('/',$date);
		if(sizeof($a)==3 and is_numeric($a[1]) and is_numeric($a[2]) and is_numeric($a[0]) and checkdate($a[1],$a[0],$a[2])){
			return strtotime($a[1].'/'.$a[0].'/'.$a[2]);
		}
		
        return false;
	}
	static function display_date($time){
		$time=date('d/m/Y',$time);
		return $time;
	}
	static function daily($time){
		$daily=(getdate($time));
		return $daily['weekday'];
	}
	static function count_day($first_date,$second_date){
		$offset = $second_date-$first_date;
		return floor($offset/60/60/24);
	}
	static function get_last_day_of_month($month,$year){
		$last_day = strtotime($year.'-' . $month.'-01');
		return date("t", $last_day);
	}
}
class EleFunc {
    static function cUrlPost($url, $data_string = array()) {
        $host = $_SERVER['HTTP_HOST'];
        $data_string = json_encode($data_string);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: ' . $host,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }

    static function callioPost($url, $data_string = array()) {
        $token = CALLIO_AGENCY_TOKEN;
        $data_string = json_encode($data_string);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Token: ' . $token,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }
    static function callioGet($url, $data_string = array()) {
        $token = CALLIO_AGENCY_TOKEN;
        $data_string = json_encode($data_string);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Token: ' . $token,
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }
}
