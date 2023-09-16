<?php
define('ID_BASE', 100.0);//So ban ghi toi da o cung mot cap thuoc mot goc
define('ID_MAX_LEVEL', 9);//So level toi da
define('ID_ROOT', "1000000000000000000");//ID goc
define('ID_MAX', "1999999999999999999");//ID lớn nhất
define('ID_MAX_CHILD', 99);// Số lượng ID tối đa mỗi cấp
//Tap hop cac ham thao tac voi cac bang co ID co cau truc cay
class IDStructure
{
	static function have_child($table,$structure_id, $extra_cond='', $database=false)
	{
		return DB::select($table,IDStructure::child_cond($structure_id, true).$extra_cond);
	}
	//Tra ve structure_id cha cua $structure_id
	//$structure_id: structure_id can tinh
 	static function parent($structure_id,$level_parent=false)
	{
		if($structure_id==ID_ROOT) return false;
		$level=IDStructure::level($structure_id);
		//$structure_id=number_format($structure_id,0,'','').'';
		if($level_parent===false) $level_parent = $level-1;
		if($level_parent >= $level)	return false;
		while($level>$level_parent){
			$structure_id[$level*2-1]='0';
			$structure_id[$level*2]='0';
			$level--;
		}
		return $structure_id;//number_format($structure_id,0,'','');
	}
	//Tra ve level cua $structure_id
	//$structure_id: structure_id can tinh
	static function level($structure_id)
	{
		$level = 0;
		if($structure_id>=ID_ROOT)
		{
			$i = 0;
			$st = '_'.$structure_id;
			while(substr($st,$level*2,2)!='00')
			{
				$level++;
				if($level>100){
				    break;
                }
			}
			$level--;
		}
		return $level;
	}
	//Tra ve structure_id ke sau cua $structure_id
	//$structure_id: structure_id can tinh
	static function next($structure_id)
	{
	    $r = $structure_id+intval(pow(ID_BASE,ID_MAX_LEVEL - IDStructure::level($structure_id)));
		return $r;
	}
	//Kiem tra $structure_id co phai la con cua $parent_id khong
	//$structure_id: structure_id con
	//$parent_id: structure_id cha
	static function is_child($structure_id, $parent_id)
	{
		return $structure_id > $parent_id and $structure_id < IDStructure::next($parent_id);
	}
	//Tra ve dieu kien de truy van ra duong dan cua idstruture, tu con den cha
	static function path_cond($structure_id)
	{
		$path = $structure_id;
		while($structure_id=IDStructure::parent($structure_id))
		{
			$path .= ','.$structure_id;
		}
		return '(FIND_IN_SET(`structure_id`,"0,'.$path.'")>0)';
	}
	//Tra ve bieu thuc dieu kien truy van tat ca con cua $id
	//$structure_id: can tinh dieu kien
	//$except_me: co loai tru chinh $structure_id nay khong
	static function child_cond($structure_id, $except_me = false,$extra = '')
	{
		if($except_me)
		{
			return '('.$extra.'`structure_id` > '.$structure_id.' and '.$extra.'`structure_id` < '.IDStructure::next($structure_id).')';
		}
		else
		{
			return '('.$extra.'`structure_id` >= '.$structure_id.' and '.$extra.'`structure_id` < '.IDStructure::next($structure_id).')';
		}
	}
	//Tra ve bieu thuc dieu kien truy van tat ca con truc tiep cua $id (truc tiep nghia la co level = level ($structure_id)-1)
	//$structure_id: can tinh dieu kien
	static function direct_child_cond($structure_id, $child_level=1)
	{
		$level = IDStructure::level($structure_id);
		$child_offset = pow(ID_BASE, ID_MAX_LEVEL-($level+$child_level));
		return '('.IDStructure::child_cond($structure_id, true).' and (`structure_id` % '.$child_offset.'=0)) ';
	}
}
