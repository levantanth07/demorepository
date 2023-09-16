<?php
/******************************
COPY RIGHT BY Catbeloved - Framework
WRITTEN BY catbeloved
******************************/
	function paging($totalitem, $itemperpage, $numpageshow=10,$smart=false,$page_name='page_no',$params=array(),$page_label='Trang'){
		$st = '';
		$new_row=array();
		if($params and is_array($params)){
			foreach($params  as $key=>$value){
				if(Url::get($value)!=''){
					$new_row[$value]=Url::get($value);
				}
			}
		}
		$totalpage = ceil($totalitem/$itemperpage);
		if ($totalpage<2){
			return;
		}
		$currentpage=page_no($page_name);
		$currentpage=round($currentpage);
		if($currentpage<=0 ||$currentpage>$totalpage){
			$currentpage=1;
		}
		if($currentpage>($numpageshow/2)){
			$startpage = $currentpage-floor($numpageshow/2);
			if($totalpage-$startpage<$numpageshow){
				$startpage=$totalpage-$numpageshow+1;
			}
		}else{
			$startpage=1;
		}
		if($startpage<1){
			$startpage=1;
		}

		//Trang hien thoi
		$st .= '<ul class="pagination">';//<span style="padding:5px;">'.$page_label.' </span>
		//Link den trang truoc
		if($currentpage>$startpage){
			$st .= '<li><a href = "'.Url::build_current($new_row+array($page_name=>$currentpage-1),$smart).'" >';
			$st .= '&laquo;';
			$st .= '</a></li>';
		}
		//Danh sach cac trang
		$st .= '';
		if($startpage>1){
			$st .= '<li><a href= "'.Url::build_current($new_row+array($page_name=>'1'),$smart).' ">1</a></li>';
			if($startpage>2){
				$st .= '...</a></li>';//
			}
		}
		for($i=$startpage; $i<=$startpage+$numpageshow-1&&$i<=$totalpage; $i++){
			if($i!=$startpage){
				$st .= '';//
			}
			if($i==$currentpage){
				if($i>1){
					$st .='';
				}
				$st .= '<li class="active"><a href="#" onclick="return false">'.$i.'</a></li>';
			}else{
				if($i>1){
					$st .='';
				}
				$st .= '<li><a href= "'.Url::build_current($new_row+array($page_name=>$i),$smart).' ">'.$i.'</a></li>';
			}
		}
		if($i==$totalpage){
			$st .= '<li><a href= "'.Url::build_current($new_row+array($page_name=>$totalpage),$smart).' ">'.$totalpage.'</a></li>';//
		}
		else
		if($i<$totalpage){
			$st .= '<li><a>...</a></li><li><a href= "'.Url::build_current($new_row+array($page_name=>$totalpage),$smart).' ">'.$totalpage.'</a></li>';//
		}
		$st .= '';
		//Trang sau
		if($currentpage<$totalpage){
			$st .= '<li><a href = "'.Url::build_current($new_row+array($page_name=>$currentpage+1),$smart).'">';
			$st .= '&raquo;';
			$st .= '</a></li>';
		}
		$st .= '</ul>';
		return $st;
	}	
	function pagingV2($totalitem, $itemperpage, $numpageshow=10,$smart=false,$page_name='page_no',$params=array(),$page_label='Trang'){
		$st = '';
		$new_row=array();
		if($params and is_array($params)){
			foreach($params  as $key=>$value){
				if($value){
					$new_row[$key] = $value;
				}
			}
		}
		$totalpage = ceil($totalitem/$itemperpage);
		if ($totalpage<2){
			return;
		}
		$currentpage=page_no($page_name);
		$currentpage=round($currentpage);
		if($currentpage<=0 ||$currentpage>$totalpage){
			$currentpage=1;
		}
		if($currentpage>($numpageshow/2)){
			$startpage = $currentpage-floor($numpageshow/2);
			if($totalpage-$startpage<$numpageshow){
				$startpage=$totalpage-$numpageshow+1;
			}
		}else{
			$startpage=1;
		}
		if($startpage<1){
			$startpage=1;
		}

		//Trang hien thoi
		$st .= '<ul class="pagination">';//<span style="padding:5px;">'.$page_label.' </span>
		//Link den trang truoc
		if($currentpage>$startpage){
			$st .= '<li><a href = "'.Url::build_current($new_row+array($page_name=>$currentpage-1),$smart).'" >';
			$st .= '&laquo;';
			$st .= '</a></li>';
		}
		//Danh sach cac trang
		$st .= '';
		if($startpage>1){
			$st .= '<li><a href= "'.Url::build_current($new_row+array($page_name=>'1'),$smart).' ">1</a></li>';
			if($startpage>2){
				$st .= '...</a></li>';//
			}
		}
		for($i=$startpage; $i<=$startpage+$numpageshow-1&&$i<=$totalpage; $i++){
			if($i!=$startpage){
				$st .= '';//
			}
			if($i==$currentpage){
				if($i>1){
					$st .='';
				}
				$st .= '<li class="active"><a href="#" onclick="return false">'.$i.'</a></li>';
			}else{
				if($i>1){
					$st .='';
				}
				$st .= '<li><a href= "'.Url::build_current($new_row+array($page_name=>$i),$smart).' ">'.$i.'</a></li>';
			}
		}
		if($i==$totalpage){
			$st .= '<li><a href= "'.Url::build_current($new_row+array($page_name=>$totalpage),$smart).' ">'.$totalpage.'</a></li>';//
		}
		else
		if($i<$totalpage){
			$st .= '<li><a>...</a></li><li><a href= "'.Url::build_current($new_row+array($page_name=>$totalpage),$smart).' ">'.$totalpage.'</a></li>';//
		}
		$st .= '';
		//Trang sau
		if($currentpage<$totalpage){
			$st .= '<li><a href = "'.Url::build_current($new_row+array($page_name=>$currentpage+1),$smart).'">';
			$st .= '&raquo;';
			$st .= '</a></li>';
		}
		$st .= '</ul>';
		return $st;
	}
	function order_page_ajax($totalitem,$itemperpage,$reference = '',$numpageshow = 5,$page_name = 'page_no',$page_label = ''){
		$ref = '';
		if($reference){
			if(is_array($reference)){
				foreach($reference  as $key=>$value){
					if(Url::get($key)){
						$ref.='&'.$key.'='.Url::get($key);
					}
				}
			}else{
				$ref = '&'.$reference;
			}
		}
		$st = '';
		$totalpage = ceil($totalitem/$itemperpage);
		if ($totalpage<2){
			return $st;
		}
		$st .= '<ul class="pagination">';
		$currentpage=page_no($page_name);
		if($currentpage<=0 ||$currentpage>$totalpage){
			$currentpage=1;
		}
		$st .= '<li>'.$page_label.' </li>';

		$startpage = $currentpage - floor($numpageshow/2);
		if($startpage < 1) {
			$startpage  = 1;
		}
		$endpage = $startpage+ $numpageshow-1;
		if($endpage > $totalpage){
			$endpage = $totalpage;
			if(($endpage -$numpageshow) > 1){
				$startpage = $endpage -$numpageshow+1;
			}
		}
		if($startpage == 2){ $startpage = 1; }
		if($endpage == ($totalpage-1)){ $endpage = $totalpage; }
		if($currentpage > $startpage){
			$st.= '<li><span alt="Trang trước" class="page-ajax-preview" onclick=\'ReloadList('.($currentpage-1).')\'>Trước</span></li>';
		}else{
			$st.= '<li><span alt="Trang trước" class="page-ajax-preview-block">Trang trước</span></li>';
		}
		if($startpage > 2){
			$st.= '<li><span id="1" onclick=\'ReloadList(1)\' class="page-ajax-normal">1</span></li><li><span>....</span></li>';
		}
		for($i = $startpage; $i<= $endpage; $i++){
			if($i==$currentpage){
				$st.= '<li><span id="'.$i.'" onclick=\'ReloadList('.$i.')\' class="page-ajax-active">'.$i.'</span>';
			}else{
				$st.= '<li><span id="'.$i.'" onclick=\'ReloadList('.$i.')\' class="page-ajax-normal">'.$i.'</span>';
			}
		}
		if($endpage < ($totalpage - 1)){
			$st.= '<li><span>....</span></li><li><span id="'.$totalpage.'" onclick=\'ReloadList('.$totalpage.')\' class="page-ajax-normal">'.$totalpage.'</span>';
		}
		if($currentpage < $endpage){
			$st.= '<li><span alt="Trang sau" class="page-ajax-next" onclick=\'ReloadList('.($currentpage+1).')\'>Sau</span></li>';
		}else{
			$st.= '<li><span alt="Trang sau" class="page-ajax-next-block">Trang sau<li>';
		}
		$st.='</ul>';
		return $st;
	}
	function order_page_prev_next_ajax($totalitem,$itemperpage,$reference = '',$numpageshow = 5,$page_name = 'page_no',$page_label = ''){
        $currentpage=page_no($page_name);
        if($currentpage<=0){
            $currentpage=1;
        }
        if ($currentpage === 1) {
            if (is_numeric($totalitem)) {
                if ($totalitem < $itemperpage) {
                    return '';
                }
            }
        }

		$st = '<ul class="pagination">';
        if ($currentpage === 1) {
            $st.= '';
//            $st.= '<li><span alt="Trang trước" class="page-ajax-preview-block">Trang trước</span></li>';
        } else {
            $st.= '<li><span alt="Trang trước" class="page-ajax-preview" onclick=\'ReloadList('.($currentpage-1).')\'>Trước</span></li>';
        }
        $st.= '<li><span alt="Trang sau" class="page-ajax-nextblock">'.$currentpage.'</span></li>';
        if (is_numeric($totalitem)) {
            if ($totalitem >= $itemperpage) {
                $st.= '<li><span alt="Trang sau" class="page-ajax-next" onclick=\'ReloadList('.($currentpage+1).')\'>Sau</span></li>';
            }
        } else {
            $st.= '<li><span alt="Trang sau" class="page-ajax-next" onclick=\'ReloadList('.($currentpage+1).')\'>Sau</span></li>';
        }

		$st.='</ul>';
		return $st;
	}
	function page_ajax($totalitem,$itemperpage,$reference = '',$numpageshow = 5,$page_name = 'page_no',$page_label = ''){
		$ref = '';
		if($reference){
			if(is_array($reference)){
				foreach($reference  as $key=>$value){
					if(Url::get($key)){
						$ref.='&'.$key.'='.Url::get($key);
					}
				}
			}else{
				$ref = '&'.$reference;
			}
		}
		$st = '';
		$totalpage = ceil($totalitem/$itemperpage);
		if ($totalpage<2){
			return $st;
		}
		$st .= '<ul class="pagination">';
		$currentpage=page_no($page_name);
		if($currentpage<=0 ||$currentpage>$totalpage){
			$currentpage=1;
		}
		$st .= '<li>'.$page_label.' </li>';

		$startpage = $currentpage - floor($numpageshow/2);
		if($startpage < 1) {
			$startpage  = 1;
		}
		$endpage = $startpage+ $numpageshow-1;
		if($endpage > $totalpage){
			$endpage = $totalpage;
			if(($endpage -$numpageshow) > 1){
				$startpage = $endpage -$numpageshow+1;
			}
		}
		if($startpage == 2){ $startpage = 1; }
		if($endpage == ($totalpage-1)){ $endpage = $totalpage; }
		if($currentpage > $startpage){
			$st.= '<li><span alt="Trang trước" class="page-ajax-preview" onclick=\'load_ajax("page_no='.($currentpage-1).$ref.'",'.Module::$current->data['id'].')\'>Trước</span></li>';
		}else{
			$st.= '<li><span alt="Trang trước" class="page-ajax-preview-block">Trang trước</span></li>';
		}
		if($startpage > 2){
			$st.= '<li><span id="1" onclick=\'load_ajax("page_no=1'.$ref.'",'.Module::$current->data['id'].')\' class="page-ajax-normal">1</span></li><li><span>....</span></li>';
		}
		for($i = $startpage; $i<= $endpage; $i++){
			if($i==$currentpage){
				$st.= '<li><span id="'.$i.'" onclick=\'load_ajax("page_no='.$i.$ref.'",'.Module::$current->data['id'].')\' class="page-ajax-active">'.$i.'</span>';
			}else{
				$st.= '<li><span id="'.$i.'" onclick=\'load_ajax("page_no='.$i.$ref.'",'.Module::$current->data['id'].')\' class="page-ajax-normal">'.$i.'</span>';
			}
		}
		if($endpage < ($totalpage - 1)){
			$st.= '<li><span>....</span></li><li><span id="'.$totalpage.'" onclick=\'load_ajax("page_no='.$totalpage.$ref.'",'.Module::$current->data['id'].')\' class="page-ajax-normal">'.$totalpage.'</span>';
		}
		if($currentpage < $endpage){
			$st.= '<li><span alt="Trang sau" class="page-ajax-next" onclick=\'load_ajax("page_no='.($currentpage+1).$ref.'",'.Module::$current->data['id'].')\'>Sau</span></li>';
		}else{
			$st.= '<li><span alt="Trang sau" class="page-ajax-next-block">Trang sau<li>';
		}
		$st.='</ul>';
		return $st;
	}
	function page_no($page_name='page_no'){
		if(Url::get($page_name) and Url::get($page_name)>0){
			return intval(Url::get($page_name));
		}else{
			return 1;
		}

	}
?>
