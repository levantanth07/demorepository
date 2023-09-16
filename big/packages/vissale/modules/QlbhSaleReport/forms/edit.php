<?php
class EditQlbhSaleReportForm extends Form
{
	function __construct()
	{
		Form::Form("EditQlbhSaleReportForm");
		$this->link_css('skins/default/css/cms.css');
		//$this->add('category_id',new IDType(true,'miss_category','airport'));
		$this->add('name',new TextType(true,'miss_product_name',0,255));
		//$this->add('price',new FloatType(true,'invalid_price',0,100000));
	}
	function on_submit(){
		if($this->check()){
			$array = array(
				'name',
				'price'=>System::calculate_number(Url::get('price')),
				'org_id'=>Session::get('org_id'),
				'unit',
				'online'=>Url::get('online')?1:0
			);
			if(Url::get('cmd')=='edit'){
				$id = Url::iget('id');
				DB::update('qlbh_product',$array,'id = '.Url::iget('id'));
			}else{
				$sql = '
				SELECT
					id,position
				FROM
					qlbh_product
				WHERE
					position =
					(SELECT
						MAX(position) as position
					from
						qlbh_product
					WHERE
						qlbh_product.org_id = '.Session::get('org_id').')
				';
				$position = 1;
				if($next = DB::fetch($sql)){
					$position =  $next['position']+1;
				}	
				$id = DB::insert('qlbh_product',$array+array('position'=>$position));
			}
			save_log($id);
			Url::redirect_current(array('just_edited_id'=>$id));
		}
	}
	function draw()
	{
		$this->map = array();
		$item = QlbhSaleReport::$item;
		if($item){
			$item['price'] = System::display_number($item['price'],false,false);
			foreach($item as $key=>$value){
				if(!isset($_REQUEST[$key])){
					$_REQUEST[$key] = $value;
				}
			}
		}
		$this->map['title'] = (Url::get('cmd')=='add')?Portal::language('Add_service'):Portal::language('edit_service');
		$this->parse_layout('edit',$this->map);
	}
}
?>