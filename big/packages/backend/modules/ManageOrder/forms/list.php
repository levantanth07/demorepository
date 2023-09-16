<?php
class ManageOrderForm extends Form
{
	function ManageOrderForm()
	{
		Form::Form('ManageOrderForm');
		$this->add('full_name',new TextType(true,'full_name',0,255));
		$this->add('email',new TextType(true,'email',0,255));
	}
	function on_submit(){
		if(Url::get('export_excel')){
			$items = ManageOrderDB::get_order_detail();	
			$this->export_excel($items,Url::get('excel_opt'));
		}
	}
	function draw()
	{
		$this->map = array();
		$this->map['total'] = 0;
		$orders = ManageOrderDB::get_order_detail();
		foreach($orders as $value){
			$this->map['total'] += $value['total'];
		}
		$this->map['total'] = System::display_number($this->map['total']);
		$this->map['orders'] = $orders;
		$countries = DB::fetch_all('select * from country order by name');
		$this->map += array(
				'nationality_id_list'=>array(''=>Portal::language('select')) + String::get_list($countries),
				'gender_list'=>array(''=>Portal::language('select')) + array('1'=>Portal::language('male'),'0'=>Portal::language('female'))
		);
		$this->parse_layout('list',$this->map);
	}
	function export_excel($items,$colomns){
		define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
		/** Include PHPExcel */
		require_once 'packages/core/includes/utils/PHPExcel/Classes/PHPExcel.php';
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Khoand")
								 ->setLastModifiedBy("Khoand")
								 ->setTitle("Email List")
								 ->setSubject("Email List")
								 ->setDescription("Email List")
								 ->setKeywords("office PHPExcel php")
								 ->setCategory("Test result file");
		// set value for header
		$i=1;
		$letter_arr = array(
			1=>'A',
			2=>'B',
			3=>'C',
			4=>'D',
			5=>'E',
			6=>'F',
			7=>'G',																		
			8=>'H',
			9=>'I',
			10=>'J',									
		);
		$objWorkSheet = $objPHPExcel->setActiveSheetIndex(0);
		foreach($colomns as $value){
			$objWorkSheet->setCellValue(''.$letter_arr[$i].'1', Portal::language($value));
			$objWorkSheet->getColumnDimension($letter_arr[$i])->setWidth(20);
			$i++;
		}
		$i=2;
		foreach($items as $key=>$value){
			// Add some data
			$objWorkSheet = $objPHPExcel->setActiveSheetIndex();
			$j = 1;
			foreach($colomns as $v){
				$objWorkSheet->setCellValue(($letter_arr[$j].$i), $value[$v]);
				$j++;
			}
			$i++;
		}
		// Rename worksheet
			//echo date('H:i:s') , " Rename worksheet" , EOL;
			//$objPHPExcel->getActiveSheet()->setTitle('Simple');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		
		
		// Save Excel 2007 file
	//	echo date('H:i:s') , " Write to Excel2007 format" , EOL;
		$callStartTime = microtime(true);
		//echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$subfix = 'donhang';
		$file = $subfix.'_'.'emails.xlsx';
		$objWriter->save($file);
		$callEndTime = microtime(true);
		$callTime = $callEndTime - $callStartTime;
		echo 'Export thành công. Tải file tại đây: <a href="'.$file.'"><strong>Tải file</strong></a>';
		exit();
	}
}
?>