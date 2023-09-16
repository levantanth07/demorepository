<?php
class ImportMaBuuDien extends Module{
	function __construct($row){
		Module::Module($row);
		require_once 'db.php';
		Portal::$document_title = 'Import Excel Bưu điện';
		require_once 'packages/vissale/lib/php/vissale.php';
		if(User::is_login() and Session::get('group_id') and check_user_privilege('KE_TOAN') ){
            switch(Url::get('cmd')){
				case 'download_excel_fail':
                    return $this->download_excel_fail();
					break;
                case 'download_excel_status_fail':
                    return $this->download_excel_status_fail();
                    break;
                case 'download_excel_null_mdb':
                    return $this->download_excel_null_mdb();
                    break;
                case 'download_excel_null_mdh':
                    return $this->download_excel_null_mdh();
                    break;
				default:
					$this->list_cmd();
			}
		}
		
		else{
			Url::access_denied();
		}
	}
	function list_cmd(){
		require_once 'forms/list.php';
		$this->add_form(new ListImportMaBuuDienForm());
	}

    private function download_excel_fail()
    {
        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator("TUHA")
            ->setLastModifiedBy("TUHA")
            ->setTitle("Mã bưu điện")
            ->setSubject("Dòng lỗi");

        $rows = Session::get('mbd_import_excel_fail_rows');
        $columns = array_slice(Session::get('mbd_import_excel_fail_header'), 1,8);// 8 = so luong cot o file excel sample
        $errorCells = Session::get('mbd_import_excel_fail_cells');
        // var_dump(Session::get('mbd_import_excel_fail_header')); die;
        $rows = array_map(function ($array_item){
            return  array_slice($array_item, 1, 8); // 8 = so luong cot o file excel sample
        } ,$rows);

		$data = array_merge(array($columns),  $rows);

        $sheet = $spreadsheet->getActiveSheet()->fromArray($data);

        foreach ($errorCells as $rowIdx => $cellErrors) {
            foreach ($cellErrors as $cellIdx) {
                if($cellIdx){
                    $sheet->getStyleByColumnAndRow($cellIdx, $rowIdx+1)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('ffff7103');   
                }
                
            }
        }

        $objWriter = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        if (ob_get_contents()) {
            ob_end_clean();
        };
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="danh-sach-ma-buu-dien-import-khong-thanh-cong-' . $_SESSION['user_id'] . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter->save('php://output');

        exit;
    }
     private function download_excel_status_fail()
    {
        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator("TUHA")
            ->setLastModifiedBy("TUHA")
            ->setTitle("Mã bưu điện")
            ->setSubject("Dòng lỗi import chuyển trạng thái");

        $dataStatus = Session::get('mbd_import_excel_fail_status_data');
        $dataStatus = array_map(function ($array_item){
            return  array_slice($array_item, 1, 9);
        } ,$dataStatus);
        $header = array_slice(Session::get('mbd_import_excel_fail_status_header'),1,9);
        $items = array_merge(array($header),  $dataStatus);

        $sheet = $spreadsheet->getActiveSheet()->fromArray($items);
        $objWriter = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        if (ob_get_contents()) {
            ob_end_clean();
        };
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="danh-sach-loi-chuyen-trang-thai' . $_SESSION['user_id'] . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter->save('php://output');

        exit;
    }
    private function download_excel_null_mdb()
    {
        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator("TUHA")
            ->setLastModifiedBy("TUHA")
            ->setTitle("Mã bưu điện")
            ->setSubject("Dòng lỗi import chuyển trạng thái");
        $dataMBD = Session::get('mbd_import_excel_fail_mdb_data');
        $dataMBD = array_map(function ($array_item){
            return  array_slice($array_item, 1, 9);
        } ,$dataMBD);
        $header = array_slice(Session::get('mbd_import_excel_fail_status_header'),1,9);
        $items = array_merge(array($header),  $dataMBD);

        $sheet = $spreadsheet->getActiveSheet()->fromArray($items);
        $objWriter = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        if (ob_get_contents()) {
            ob_end_clean();
        };
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="danh-sach-chua-nhap-ma-buu-dien' . $_SESSION['user_id'] . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter->save('php://output');

        exit;
    }
    private function download_excel_null_mdh()
    {
        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator("BIG")
            ->setLastModifiedBy("BIG")
            ->setTitle("Mã bưu điện")
            ->setSubject("Dòng lỗi import chuyển trạng thái");
        $dataMDH = Session::get('mbd_import_excel_fail_mdh_data');
        $dataMDH = array_map(function ($array_item){
            return  array_slice($array_item, 1, 9);
        } ,$dataMDH);
        $header = array_slice(Session::get('mbd_import_excel_fail_status_header'),1,9);
        $items = array_merge(array($header),  $dataMDH);

        $sheet = $spreadsheet->getActiveSheet()->fromArray($items);
        $objWriter = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        if (ob_get_contents()) {
            ob_end_clean();
        };
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="danh-sach-chua-nhap-ma-don-hang' . $_SESSION['user_id'] . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter->save('php://output');

        exit;
    }
}
?>
