<?php
/******************************
COPY RIGHT BY Catbeloved - Framework
******************************/

//Cac ham thao tac voi IDStructure
//Tra ve structure_id cua ban ghi co ma so $id trong bang $table
//$table: Ten bang can tim
//$id: id can tim

// Tra ve ban ghi trong bang $table co $structure_id
function si_select($table,$structure_id){
	return DB::select($table,'structure_id='.$structure_id);
}
// Tra ve id cua ban ghi trong bang $table co $structure_id
function si_id($table,$structure_id){
	$row=DB::select($table,'structure_id='.$structure_id);
	return $row['id'];
}
// Tra ve id cua ban ghi trong bang $table co $structure_id
function structure_id($table,$id){
	$row=DB::select($table,'id="'.$id.'"');
	return $row['structure_id'];
}	
//Tra ve id cua ban ghi co $structure_id trong bang $table
//Khong nen lam dung ham nay 
//$table: Ten bang can tim
//$structure_id: structure_id can tim
function si_parent($table,$id, $extra=''){
	$sql = 'SELECT * FROM '.$table.' WHERE structure_id = '.(($id != ID_ROOT)?IDStructure::parent($id):ID_ROOT).$extra;
	DB::query($sql);
	$row = DB::fetch();
	return $row;
}
function si_parent_id($table,$id, $extra=''){
	if($id == ID_ROOT){
		return 1;
	}else{
		require_once 'packages/core/includes/system/si_database.php';
		$row = si_parent($table,$id, $extra);
		return $row['id'];
	}
}
//Tra ve $structure_id con con` trong tiep theo cua bang
//$table: ten bang can tim
//$structure_id: can tinh 
//$extra_cond: Dieu kien bo sung trong cau lenh select

function si_next_child($table,$structure_id,$extra_cond = ''){
	//Lay tat cac cac id con truc tiep cua $id
	$level = IDStructure::level($structure_id);
	$child_offset = number_format(pow(ID_BASE, ID_MAX_LEVEL-($level+1)),0,'','');

	DB::query('select * from `'.$table.'` where '.IDStructure::direct_child_cond($structure_id).$extra_cond);
	$rows = DB::fetch_all();
	$new_id = number_format($structure_id + $child_offset,0,'','');
	for($i=1;$i<ID_BASE;$i++){
		$found = false;
		if($rows){
			foreach($rows as $row){
				if($row['structure_id']==$new_id){
					$found = true;
					break;
				}
			}
		}
		if(!$found){
			return $new_id;
		}
		$new_id = number_format($new_id + $child_offset,0,'','');
	}
	return $new_id;
}
//doi ban ghi tu cho $structure_id cu sang structure_id moi dong thoi doi luon cac ban ghi con
//$table: Ten bang can chuyen
//$structure_id: structure_id can chuyen
//$new_id: structure_id moi
//$extra_cond: Dieu kien bo sung trong cau lenh select
function si_change($table,$structure_id, $new_id, $extra_cond=''){
	if($structure_id != $new_id){
		// Tinh do dich chuyen theo cap
		$old_level=IDStructure::level($structure_id);
		$new_level=IDStructure::level($new_id);
		$mul = pow(ID_BASE, $new_level-$old_level);
        $oldRecords = DB::fetch_all_array("select * from `{$table}` where " . IDStructure::child_cond($structure_id).$extra_cond);
		$updated = DB::query('update `'.$table.'` set structure_id=floor((structure_id-'.$structure_id.')/'.$mul.')+'.$new_id.' where '.IDStructure::child_cond($structure_id).$extra_cond);
		// $new_id da thay the' $page_id

        if ($updated == true) {
            if (! empty($oldRecords)) {
                foreach ($oldRecords as $oldRecord) {
                    if (! empty($oldRecord['id'])) {
                        // Publish data on update a record event
                        CrmSync::publishEventOnUpdate($table, Session::get('group_id'), "`id` = {$oldRecord['id']}");
                    }
                }
            }
        }
	}
}
function si_move($table, $structure_id, $parent_id, $extra_cond=''){
	if(IDStructure::parent($structure_id)!=$parent_id){
		if(!IDStructure::is_child($parent_id, $structure_id)){
			require_once 'packages/core/includes/system/si_database.php';
			$new_id=si_next_child($table,$parent_id, $extra_cond);
			si_change($table,$structure_id, $new_id, $extra_cond);
			return $new_id;
		}else{
			return false;
		}
	}else{
		return $structure_id;
	}
}
function si_delete($table,$structure_id, $extra_cond=''){
	DB::delete($table,IDStructure::child_cond($structure_id).$extra_cond);
}
function si_have_child($table,$structure_id, $extra_cond=''){
	return DB::select($table,IDStructure::child_cond($structure_id, true).$extra_cond);
}
function si_have_child_id($table,$id, $extra_cond=''){
	require_once 'packages/core/includes/system/si_database.php';
	return si_have_child($table, DB::structure_id($table,$id),$extra_cond);
}
function clone_records($table,$change_fields, $cond='1'){
	if($result=@mysql_query('SHOW COLUMNS FROM `'.$table.'`')){
		$columns = array();
		while ($row = mysql_fetch_row($result)) {
			if($row[0]!='id'){
				if(isset($change_fields[$row[0]])){
					$columns['`'.$row[0].'`'] = '"'.$row[0].'"';
				}else{
					$columns['`'.$row[0].'`'] = $row[0];
				}
			}
		}
		DB::query('insert into `'.$table.'`('.implode(',',array_keys($columns)).') select '.implode(',',array_values($columns)).' from '.$table.' where '.$cond);
	}
}
function si_child($table,$structure_id,$extra_cond = '', $database=false){
	//Lay tat cac cac id con truc tiep cua $id
	$level = IDStructure::level($structure_id);
	$child_offset = number_format(pow(ID_BASE, ID_MAX_LEVEL-($level+1)),0,'','');
	$sql = 'select id,structure_id from `'.$table.'` where '.IDStructure::direct_child_cond($structure_id).$extra_cond;
	DB::query($sql);
	$rows = DB::fetch_all();
	$new_id = $structure_id + $child_offset;//number_format($structure_id + $child_offset,0,'','');
	for($i=1;$i<ID_BASE;$i++){
		$found = false;
		if($rows){
			foreach($rows as $row){
				if($row['structure_id']==$new_id){
					$found = true;
					break;
				}
			}
		}
		if(!$found){
			return $new_id;
		}
		$new_id = $new_id + $child_offset;//number_format($new_id + $child_offset,0,'','');
	}
	return $new_id;
}
function si_compress($structure_id){
	while($structure_id{strlen($structure_id)-1}=='0' and $structure_id{strlen($structure_id)-2}=='0'){
		$structure_id = substr($structure_id,0,strlen($structure_id)-2);
	}
	return $structure_id;
}
function si_decompress($structure_id){
	while(strlen($structure_id)<strlen(ID_ROOT)){
		$structure_id .= '00';
	}
	return $structure_id;
}
function si_move_position($table,$extra=''){
	$parent = si_parent($table,DB::structure_id($table,Url::iget('id')),$extra);
	$category=DB::exists_id($table,Url::iget('id'));
	if(Url::check(array('cmd'=>'move_up'))){
		$move[0]='<';
		$move[1]='desc';
	}else{
		$move[0]='>';
		$move[1]='asc';
	}
	if(DB::query('
		select 
			*
		from 
			`'.$table.'`
		where 
			'.IDStructure::direct_child_cond($parent['structure_id']).'
			and structure_id'.$move[0].$category['structure_id'].'
			'.$extra.'
		order by 
			structure_id '.$move[1])){
		if($row=DB::fetch()){
			$si =si_next_child($table,$parent['structure_id'],$extra);
			si_change($table,$category['structure_id'],$si,$extra);
			si_change($table,$row['structure_id'],$category['structure_id'],$extra);
			si_change($table,$si,$row['structure_id'],$extra);
		}
	}
}
?>