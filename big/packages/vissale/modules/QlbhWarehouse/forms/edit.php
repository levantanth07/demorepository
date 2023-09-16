<?php
class EditQlbhWarehouseForm extends Form
{
	protected $map;
	function __construct()
	{
		Form::Form('EditQlbhWarehouseForm');
		if(URL::get('cmd')=='edit')
		{
			$this->add('id',new IDType(true,'ID không tồn tại','qlbh_warehouse'));
		}
		$this->add('name',new TextType(true,'Lỗi nhập tên',0,255)); 
	}
	function on_submit()
	{
		if($this->check()) {
			if(URL::get('cmd')=='edit'){
				$this->old_value = DB::select('qlbh_warehouse','id='.Url::iget('id').'');
			}
			$this->save_item();
			Url::js_redirect(true);
		}
	}	
	function draw()
	{
		$this->init_edit_mode();
		$this->get_parents();
		if ($this->init_value['group_id'] == 0) {
			$email = DB::fetch('select id,group_id,name,email,user_id from qlbh_warehouse where kho_tong_shop = 1 and  group_id = '.Session::get('group_id').'');
			if ($email) {
				$this->init_value['email'] = $email['email'];
			}
		}
		if ($this->edit_mode) {
			$arrayName = array(
				'parent_id_list'=>MiString::get_list(QlbhWarehouseDB::check_categories($this->parents)),
				'parent_id'=>($this->edit_mode?si_parent_id('qlbh_warehouse',$this->init_value['structure_id']):1),
				'title'=>Url::get('cmd')=='edit'?'Sửa thông tin kho':'Thêm kho mới'
			);
			$data = $this->init_value + $arrayName;
			$this->map['item'] = $data;
		}
		$this->parse_layout('edit',$this->map);
	}
	function save_item()
	{
		$new_row = array('name','zone_id'=>'0');
		$new_row['is_default'] = Url::check('is_default')?1:0;
		$new_row['email'] = Url::get('email');
		if ($new_row['email']) {
			$email = filter_var($new_row['email'], FILTER_SANITIZE_EMAIL);
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			   $this->error('email','Email không đúng định dạng');
			}
		}
		if(URL::get('cmd')=='edit' and $row=DB::fetch('select id,group_id,name from qlbh_warehouse where id= '.Url::iget('id').'') and (User::is_admin() or Session::get('admin_group')))
		{
			$this->id = Url::iget('id');
			if($row['group_id']==0 && Url::get('name') !== $row['name']){
				$this->error('name','Không được phép sửa tên kho tổng');
			}else{
				if (!is_group_owner()) {
					unset($new_row['email']);
				}
				if ($row['group_id']==0) {
					$email = DB::fetch('select id,group_id,name,email,user_id from qlbh_warehouse where kho_tong_shop = 1 and  group_id = '.Session::get('group_id').'');
					if (!$email) {
						require_once 'packages/core/includes/system/si_database.php';
						$insertRow['name'] = 'Kho Tổng Của Shop';
						$insertRow['group_id'] = Session::get('group_id');
						$insertRow['user_id'] = Session::get('user_id');
						$insertRow['email'] = $new_row['email'];
						$insertRow['structure_id'] = si_child('qlbh_warehouse',ID_ROOT);
						$insertRow['is_default'] = 0;
						$insertRow['kho_tong_shop'] = 1;
						$insertRow['zone_id'] = 0;
						DB::insert('qlbh_warehouse',$insertRow);
					} else{
						if ($email['email'] !== $new_row['email']) {
							DB::update_id('qlbh_warehouse', array('email'=>$new_row['email']),$email['id']);
						}
					}
				}
				if ($row['group_id']!=0) {
					DB::update_id('qlbh_warehouse', $new_row,$this->id);
				}
				if($this->old_value['structure_id']!=ID_ROOT)
				{
					if (Url::check(array('parent_id')))
					{
						$parent = DB::select('qlbh_warehouse', URL::iget('parent_id'));
						if($parent['structure_id']==$this->old_value['structure_id'])
						{
							$this->error('id','Kho không được trực thuộc chính nó');
						}
						else
						{
							require_once 'packages/core/includes/system/si_database.php';
							if(!si_move('qlbh_warehouse',$this->old_value['structure_id'],$parent['structure_id']))
							{
								$this->error('id','Lỗi danh mục cha');
							}
						}
					}
				}
			}
		}
		else
		{
			require_once 'packages/core/includes/system/si_database.php';
			$new_row += array('group_id'=>Session::get('group_id'));
			$this->id = DB::insert('qlbh_warehouse', $new_row+
                array('user_id'=>Session::get('user_id'),
                    'structure_id'=>si_child('qlbh_warehouse',ID_ROOT))
            );
		}
	}
	function init_edit_mode()
	{
		if(URL::get('cmd')=='edit' and $this->init_value= DB::fetch('select * from qlbh_warehouse where id='.URL::iget('id').''))
		{		
			foreach($this->init_value as $key=>$value)
			{
				if(!isset($_REQUEST[$key]))
				{
					$_REQUEST[$key] = $value;
				}
			}
			$this->edit_mode = true;
		}
		else
		{
			$this->edit_mode = false;
		}
	}
	function get_parents()
	{
		require_once 'packages/core/includes/system/si_database.php';
		$sql = '
			select 
				id,
				structure_id,
				name
			from 
			 	qlbh_warehouse
			order by 
				structure_id
		';
		$this->parents = DB::fetch_all($sql,false);		
	}
}
?>