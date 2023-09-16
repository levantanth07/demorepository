<?php
class SystemInfo extends Module
{
	function __construct($row)
	{
		if(User::can_admin(MODULE_SYSTEMINFO,ANY_CATEGORY))
		{
			switch (Url::get('do')){
                case 'run_query':
                    if($sql=Url::get('sql')){
                        $this->run_query($sql);
                    }else{
                        echo json_encode(['error'=>1,'error_message'=>'No query']);
                    }
                    die();
                    break;
                default:
                    Module::Module($row);
                    require_once 'forms/list.php';
                    $this->add_form(new SystemInfoForm());
                    break;
            }
		}
		else
		{
			Url::access_denied();
		}
	}
	function run_query($sql){
        $sql = preg_replace('/[\s|\n|\t]+/',' ',$sql);
        $sql = strtolower($sql);
	    $r = '';
	    if(preg_match('/^SELECT/i',$sql)){
	        preg_match_all('/^select (.*) from ([a-z0-9_]+)([^(limit)$]*)(limit)*([0-9, ]*)/',$sql,$matchs);
	        if(!isset($matchs[4][0]) or $matchs[4][0]!='limit'){
                $sql .= ' LIMIT 0,10';
            }
	        //System::debug($matchs);
	        if(isset($matchs[2][0])){
	           $table = $matchs[2][0];
                $items = DB::fetch_all($sql);
                $fieldnames = $this->get_coloumn($table);
                $r .= '<table class="table table-striped table-bordered">';
                $r .= '<tr><th>#</th>';
                foreach($items as $key=>$val) {
                    foreach($fieldnames as $k=>$v){
                        if(!isset($val[$v])){
                            unset($fieldnames[$k]);
                        }
                    }
                }
                foreach($fieldnames as $k=>$v){
                    $r .= '<th>'.$v.'</th>';
                }
                $r .= '</tr>';
                $i=0;
                foreach($items as $key=>$val) {
                    $i++;
                    $r .= '<tr>';
                    $r .= '<td>'.$i.'</td>';
                    foreach($fieldnames as $k=>$v){
                        if(isset($val[$v])){
                            $r .= '<td>'.$val[$v].'</td>';
                        }
                    }
                    $r .= '</tr>';
                }
                $r .= '</table>';
            }
	        echo $r;
        }else{
            DB::query($sql);
            System::debug(DB::$db_result);
        }
    }
    function get_coloumn($table) {
        DB::query("SHOW COLUMNS FROM ". $table);
        $qr = DB::$db_result;
        if ($qr){
            while ($row = mysqli_fetch_assoc($qr)) {
                $fieldnames[] = $row['Field'];
            }
        }
        return $fieldnames;
    }
}
?>