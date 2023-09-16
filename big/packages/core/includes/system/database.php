<?php

require_once ROOT_PATH. '/packages/core/includes/common/Query.php';
require_once ROOT_PATH. '/packages/core/includes/common/QueryJoin.php';

require_once "crm_sync.php";

// MySqli - by Khoand - 11/12/2017
class DB{
    static $db_connect_id=false;				// connection id of this database
	static $db_result=false;				// current result of an query
	static $db_cache_tables = array();
	static $db_exists_db = array();
	static $db_select_all_db = array();
	static $db_num_queries = 0;
	static protected $db_name = '';
	static protected $dbport = '';
	static protected $sqlpassword;
	static protected $sqlserver;
	static protected $sqluser;
	static $stmt;
	// Debug
	var $num_queries = 0;		// number of queries was done

	function __construct($sqlserver, $sqluser, $sqlpassword, $dbname, $dbport){
        self::$sqlpassword = $sqlpassword;
        self::$sqlserver = $sqlserver;
        self::$sqluser = $sqluser;
        self::$db_name = $dbname;
        self::$dbport = $dbport;

		return self::get_connect_id();
	}
    static function get_server_info(){
        return self::$db_connect_id->server_info;
    }
	public static function get_connect_id(){

        if ( !empty(self::$db_connect_id) && mysqli_ping(self::$db_connect_id) ) {
            return self::$db_connect_id;
        }

        // Throw tất cả lỗi câu truy vấn mysql để handle
        if(defined('MYSQL_STRICT_MODE')) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        }

        self::$db_connect_id = mysqli_connect(self::$sqlserver, self::$sqluser, self::$sqlpassword, self::$db_name,self::$dbport);
        if (!self::$db_connect_id) {
            die('Hệ thống đồng bộ và hiệu chỉnh lại cơ sở dữ liệu. Mong quý khách đợi trong ít phút....');
        }
        /* change character set to utf8 */
        mysqli_set_charset(self::$db_connect_id,"utf8mb4");
	    return self::$db_connect_id;
	}
    
	static function register_cache($table, $id_name='id', $order=' order by id asc'){
		DB::$db_cache_tables[$table]=array('id_name'=>$id_name, 'order'=>$order);
		if(!file_exists(ROOT_PATH.'cache/tables/'.$table.'.cache.php')){
			require_once 'packages/core/includes/system/make_table_cache.php';
			make_table_cache($table);
		}else{
			require_once ROOT_PATH.'cache/tables/'.$table.'.cache.php';
		}
	}
	static function count($table, $condition=false){
		return DB::fetch('select count(*) as total from `'.$table.'`'.($condition?' where '.$condition:''),'total');
	}
	//Lay ra mot ban ghi trong bang $table thoa man dieu kien $condition
	//Neu bang duoc cache thi lay tu cache, neu khong query tu CSDL
	static function select($table, $condition){
		if($result = DB::select_id($table, $condition)){
			return $result;
		}else{
			return DB::exists('select * from `'.$table.'` where '.$condition.' limit 0,1');
		}
	}
	static function select_id($table, $condition){
		if($condition and !preg_match('/[^a-zA-Z0-9_#-\.]/',$condition)){
			if(isset(DB::$db_cache_tables[$table])){
				$id=$condition;
				$cache_var = 'cache_'.$table;
				global $$cache_var;
				$cached = isset($$cache_var);
				if(!$cached){
					DB::refresh_cache($table);
				}
				$data = &$$cache_var;
				if(isset($data[$id])){
					return $data[$id];
				}
			}else{
				return DB::exists_id($table,$condition);
			}
		}else{
			return false;
		}
	}
	//Lay ra tat ca cac ban ghi trong bang $table thoa man dieu kien $condition sap xep theo thu tu $order
	//Neu bang duoc cache thi lay tu cache, neu khong query tu CSDL
	static function select_all($table, $condition=false, $order = false){
		if(isset(DB::$db_select_all_db[$table]) and !$order and !$condition){
			return DB::$db_select_all_db[$table];
		}elseif(isset($GLOBALS['cache_'.$table]) and !$order and !$condition){
			return $GLOBALS['cache_'.$table];
		}else{
			if($order){
				$order = ' order by '.$order;
			}
			if($condition){
				$condition = ' where '.$condition;
			}
			DB::query('select * from `'.$table.'` '.$condition.' '.$order);
			$rows = DB::fetch_all();
			if(sizeof($rows)<10){
				DB::$db_select_all_db[$table] = $rows;
			}
			return $rows;
		}
	}
	// function close
	// Close SQL connection
	// should be called at very end of all scripts
	// ------------------------------------------------------------------------------------------
	static function close(){
		if (self::get_connect_id() and self::get_connect_id()){
			if (isset(DB::$db_result) and DB::$db_result){
				@mysqli_free_result(DB::$db_result);
			}
			$result = mysqli_close(self::get_connect_id());
			return $result;
		}else{
			return false;
		}
	}
	// function query
	// Run an sql command
	// Parameters:
	//		$query:		the command to run
	// ------------------------------------------------------------------------------------------
	static function query($query, $params = []){
		//echo DB::$db_num_queries.'.'.$query.'<br>';
		// Clear old query result
		mysqli_query(self::get_connect_id(),"SET charset 'utf8mb4';");
		mysqli_query(self::get_connect_id(),"SET names 'utf8mb4';");
		DB::$db_result = false;
        DB::$stmt = false;

		if (empty($query)){
            return false;
        }if ($query) {
            if (is_array($params) && !empty($params)) {
                DB::$stmt = self::prepare($query, $params);
            } else {
                DB::$db_result = mysqli_query(self::get_connect_id(), $query);
            }

            DB::$db_num_queries++;
        }

        if (!DB::$stmt && !DB::$db_result) {
            self::logErrors($query, debug_backtrace());
        }

        return DB::$stmt ? DB::$stmt : DB::$db_result;

	}


    static function multi_query($query,  $params = []) {
        mysqli_query(self::get_connect_id(),"SET charset 'utf8mb4';");
        mysqli_query(self::get_connect_id(),"SET names 'utf8mb4';");
        DB::$db_result = false;
		DB::$stmt = false;
        if ($query) {
            if (is_array($params) && !empty($params)) {
                DB::$stmt = self::prepare($query, $params);
            } else {
                DB::$db_result = mysqli_multi_query(self::get_connect_id(), $query);
            }

            DB::$db_num_queries++;
        }

        if (!DB::$stmt && !DB::$db_result) {
            self::logErrors($query, debug_backtrace());
        }

        return DB::$stmt ? DB::$stmt : DB::$db_result;
    }
    /**
     * Logs errors.
     */
    private static function logErrors(string $sql, array $traces){
        if($logger = Logger::getLogger()){
            $logs = [];
            $logs[] = sprintf('%s - %s', $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
            $logs[] = self::get_connect_id()->error;
            $logs[] = "SQL:";
            $logs[] = $sql;
            foreach ($traces as $key => $trace) {
                $logs[] = sprintf(
                	"#%d %s:%d [%s%s%s]", 
                	++$key, 
                	$trace['file'] ?? '', 
                	$trace['line'] ?? '', 
                	$trace['class'] ?? '',
                	$trace['type'] ?? '',
                	$trace['function'] ?? ''
                );
            }

            $logger->error(implode("\n\r", $logs));
        }
    }
	//Tra ve ban ghi query tu CSDL bang lenh SQL $query neu co
	//Neu khong co tra ve false
	//$query: cau lenh SQL se thuc hien
	static function exists($query, $params = []){
		$result = DB::fetch($query, false, false, $params);
        return $result ? $result : false;
	}
	//Tra ve ban ghi trong bang $table co id la $id
	//Neu khong co tra ve false
	//$table: bang can truy van
	//$id: ma so ban ghi can lay
	static function exists_id($table,$id){
		if($id){

			if(!isset(DB::$db_exists_db[$table][$id])){
				DB::$db_exists_db[$table][$id]=DB::exists('select * from `'.$table.'` where id="'.DB::escape($id).'" limit 0,1');
			}
			return DB::$db_exists_db[$table][$id];
		}
	}
	static function insert($table, $values, $replace=false){
		if($replace){
			$query='replace';
		}else{
			$query='insert into';
		}
		$query.=' `'.$table.'`(';
		$i=0;
		if(is_array($values)){
			foreach($values as $key=>$value){
				if(($key===0) or is_numeric($key)){
					$key=$value;
				}
				if($key){
					if($i<>0){
						$query.=',';
					}
					$query.='`'.$key.'`';
					$i++;
				}
			}
			$query.=') values(';
			$i=0;
			foreach($values as $key=>$value){
				if(is_numeric($key) or $key===0){
					$value = DB::escape(Url::get($value));
				}
				if($i<>0){
					$query.=',';
				}
				if($value==='NULL'){
					$query.='NULL';
				}else{
					$query.='\''.DB::escape($value).'\'';
				}
				$i++;
			}
			$query.=')';
			/*if($table=='order_rating'){
                echo $query;exit();
            }*/
            //if(preg_match('#master_product#', $query)) th_debug($query);
			
			if(DB::query($query)){
				$id = DB::insert_id();
				if(isset(DB::$db_cache_tables[$table])){
					//DB::refresh_cache($table);
				}

                // Publish data on insert new record event
                if(!System::is_local()){
                	CrmSync::publishEventOnInsert($table, Session::get('group_id'), "`id` = {$id}");
                }
               

				return $id;
			}
		}
	}
	static function delete($table, $condition){
        $deleteRecord = null;
        if(in_array($table, DATA_DELETED_TRIGGER_TABLE)) {
            try {
                $deleteRecord = DB::fetch_all_array("select id from {$table} where {$condition}");
            } catch (Exception $exception) {
                // Ignores exception
            }
        }

		$query='delete from `'.$table.'` where '.$condition;
		//echo $query;
		if(DB::query($query)){
			if(isset(DB::$db_cache_tables[$table])){
				//DB::refresh_cache($table);
			}

            // Publish data on delete a record event
            if (! empty($deleteRecord)) {
            	if(!System::is_local()){
	                CrmSync::publishEventOnDelete($table, Session::get('group_id'), $deleteRecord);
	            }
            }

			return true;
		}
	}

	static function delete_id($table, $id){
		return DB::delete($table, 'id="'.DB::escape($id).'"');
	}

	static function update($table, $values, $condition){
		$query='update `'.$table.'` set ';
		$i=0;
		if($values){
			foreach($values as $key=>$value){
				if($key===0 or is_numeric($key)){
					$key=$value;
					$value=URL::get($value);
				}
				if($i<>0){
					$query.=',';
				}
				if($key){
					if($value==='NULL'){
						$query.='`'.$key.'`=NULL';
					}else{
						$query.='`'.DB::escape($key).'`=\''.DB::escape($value).'\'';
					}
					$i++;
				}
			}
			$query.=' where '.$condition;
			if(DB::query($query)){
				if(isset(DB::$db_cache_tables[$table])){
					DB::refresh_cache($table);
				}

                // Publish data on update a record event
                if(!System::is_local()){
                	CrmSync::publishEventOnUpdate($table, Session::get('group_id'), $condition);
                }
                

				return true;
			}
		}
	}
	static function refresh_cache(){
	}
	static function update_id($table, $values, $id){
		return DB::update($table, $values, 'id="'.DB::escape($id).'"');
	}
	static function num_rows($query_id = 0){
		if (!$query_id){
			$query_id = DB::$db_result;
		}
		if ($query_id){
			$result = mysqli_num_rows($query_id);
			return $result;
		}else{
			return false;
		}
	}
	static function affected_rows(){
		if (self::get_connect_id() and self::get_connect_id()){
			$result = mysqli_affected_rows(self::get_connect_id());
			return $result;
		}else{
			return [];
		}
	}
	static function fetch($sql = false, $field = false, $default = false,  $params = [])
	{	
		if(!$sql || !DB::query($sql, $params)) {
			return $field ? null : [];
		}

		if (self::$stmt) {
            $result = mysqli_stmt_get_result(DB::$stmt)->fetch_assoc();
        } elseif (DB::$db_result) {
            $result = DB::$db_result->fetch_assoc();
        } else {
        	$result = [];
        }

		return $result ? ($field ? $result[$field] : $result) : ($field ? false : []);
	}

	static function fetch_all($sql=false, $params = []){
		if($sql){
			DB::query($sql);
		}
		$result = [];

        if (self::$stmt) {
            $stmtResult = mysqli_stmt_get_result(DB::$stmt);
            while ($row = $stmtResult->fetch_assoc()) {
                $result[$row['id']] = $row;
            }
        } elseif (DB::$db_result) {
            while ($row = mysqli_fetch_assoc(DB::$db_result)) {
                $result[$row['id']] = $row;
            }
        }

        return $result;
	}

    static function fetch_all_key($sql=false,$field = ''){
        if($sql){
            DB::query($sql);
        }
        $query_id = DB::$db_result;
        if ($query_id){
            $result=array();
            if($field != ''){
                $arrKey = $field;
            }else{
                $arrKey = 'id';
            }
            while($row = mysqli_fetch_assoc($query_id)){
                $result[$row[$arrKey]] = $row;
            }
            return $result;
        }else{
            return [];
        }
    }

	static function fetch_all_array($sql=false, $params = []){
		if($sql){
			DB::query($sql, $params);
		}
		$result = [];

        if (self::$stmt) {
            $stmtResult = mysqli_stmt_get_result(DB::$stmt);
            while ($row = $stmtResult->fetch_assoc()) {
                $result[] = $row;
            }
        } elseif (DB::$db_result) {
            while ($row = mysqli_fetch_assoc(DB::$db_result)) {
                $result[] = $row;
            }
        }

        return $result;
	}

	/**
	 * Lấy danh sách giá trị của một cột
	 *
	 * @param      string  $sql         The sql
	 * @param      string  $columnName  The column name - Giá trị của cột muốn lấy
	 * @param      string  $key         The key - giá trị của cột được đặt làm khóa của mảng
	 *
	 * @return     array   All column.
	 */
	public static function fetch_all_column(string $sql, string $columnName = 'id', string $key = null)
	{
		$results = [];

		if(!DB::query($sql)){
			return $results;
		}

		while($row = mysqli_fetch_assoc(DB::$db_result)){
			if($key && isset($row[$key])){
	            $results[$row[$key]] = $row[$columnName] ?? null;
	        }

	        else{
	        	$results[] = $row[$columnName] ?? null;
	        }
        }

        return $results;
	}

	/**
	 * Lấy danh sách giá trị của nhiều cột
	 *
	 * @param      string  $sql         The sql
	 * @param      string  $columnName  The column name - danh sách của cột muốn lấy
	 * @param      string  $key         The key - giá trị của cột được đặt làm khóa của mảng
	 *
	 * @return     array   All column.
	 */
	public static function fetch_all_columns(string $sql, array $columnNames, string $key = null)
	{	
		if(!$columnNames){
			throw new Exception('DB::fetch_all_columns: Vui lòng cung cấp danh sách cột cần lấy !');
		}

		if(!DB::query($sql)){
			return [];
		}

		$results = [];
		while($row = mysqli_fetch_assoc(DB::$db_result)){
			$values = array_reduce($columnNames, function($res, $columnName) use($row) {
            	$res[$columnName] = $row[$columnName] ?? null;

            	return $res;
            }, []);

			if($key && isset($row[$key])){
	            $results[$row[$key]] = $values;
	        }else{
	        	$results[] = $values;
	        }
        }

        return $results;
	}

	static function insert_id(){
		if (self::get_connect_id()){
			$result = mysqli_insert_id(self::get_connect_id());
			return $result;
		}else{
			return false;
		}
	}
	static function free_result($query_id = 0){
		if (!$query_id){
			$query_id = DB::$db_result;
		}
		if ($query_id){
			@mysqli_free_result($query_id);
			return true;
		}else{
			return false;
		}
	}
	static function error(){
		$result['message'] = mysqli_error(self::get_connect_id());
		$result['code'] = mysqli_errno(self::get_connect_id());
		return $result;
	}
	static function escape($sql){
		return mysqli_real_escape_string(self::get_connect_id(),$sql ?? '');
	}

	static function escapeArr($data)
    {
        $datafomart = [];
        if(!empty($data) && is_array($data)){
            foreach ($data as $value) {
                $datafomart[] = DB::escape($value);
            }
        }
        return $datafomart;
    }

	static function escapeArray($array){
		return is_array($array) ? array_map("self::escape", $array): DB::escape($array);
	}

	
    static function escapeAssociativeArr($arr)
    {
        $return = [];
        if (is_array($arr)) {
            foreach ($arr as $key => $value) {
                $return[$key] = DB::escape($value);
            }
        }

        return $return;
    }

	static function num_queries(){
		return DB::$db_num_queries;
	}
	// tra ve structure_id cua $id
	static function structure_id($table,$id){
		$row=DB::select($table,'id='.DB::escape($id).'');
		return $row['structure_id'];
	}
	static function search_cond($table, $field){
		$cond_st = '';
		if(URL::get('search_by_'.$field)){
			$conds = explode('&',URL::get('search_by_'.$field));
			foreach($conds as $cond){
				if(preg_match('/[><=]/',URL::get('search_by_'.$field))){
					$cond_st .= ' and `'.$table.'`.`'.$field.'` '.$cond;
				}else{
					$cond_st .= ' and `'.$table.'`.`'.$field.'` LIKE "%'.$cond.'%"';
				}
			}
		}
		return $cond_st;
	}
	static function get_record_title($item){
		if(isset($item['name'])){
			return 'name';
		}elseif(isset($item['title'])){
			return 'title';
		}elseif(isset($item['name_'.Portal::language()])){
			return 'name_'.Portal::language();
		}elseif(isset($item['title_'.Portal::language()])){
			return 'title_'.Portal::language();
		}
	}
	static function update_hit_count($table,$id){
		//unset($_SESSION['item_visited']);
		if(Session::is_set('item_visited')){
			$items=array_flip(explode(',',Session::get('item_visited')));
		}else{
			$items=array();
		}
		if(!isset($items[$id]) and $item=DB::select_id($table,intval($id))){
			DB::update_id($table,array('hitcount'=>$item['hitcount']+1),intval($id));
			$items[$id]=$id;
			Session::set('item_visited', implode(',',array_keys($items)));
		}
	}
    static function check_query($ip,$sql=false){
        if(System::get_client_ip_env() == $ip){
            echo '<div class="alert alert-warning-custom">';
            echo 'Time: '.$timer = number_format(Portal::$page_gen_time->get_timer(),4);
            echo '<br>';
            echo 'Queries: '.$total_query = DB::num_queries();

            if($sql){
                echo '<hr>';
                System::debug($sql);
            }
            echo '</div>';
        }
    }

	static function prepare($sql, $params = null)
    {
        $stmt = mysqli_prepare(self::get_connect_id(), $sql);
        $types = '';

        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
        }

        if ($params) {
            $bindNames[] = $types;
            $paramCount = count($params);
            for ($i = 0; $i < $paramCount; $i++) {
                $bindName = 'bind' . $i;
                $$bindName = $params[$i];
                $bindNames[] = &$$bindName;
            }

            call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt], $bindNames));
        }

        mysqli_stmt_execute($stmt);

        return $stmt;
    }
}
require_once(ROOT_PATH.'cache/db/default.php');

$db = new DB(DB_HOST, DB_USER , DB_PASS, DB_NAME, DB_PORT);
Query::boot(DB::get_connect_id());
DB::register_cache('language');
