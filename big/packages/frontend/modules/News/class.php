<?php
class News extends Module
{
	function __construct($row)
	{
		Module::Module($row);
		require_once 'db.php';
		//require_once '';
		$this->update_seo();
		require_once 'forms/list.php';
		$this->add_form(new NewsForm());
        switch(Url::get('cmd'))
			{
			     case 'change_new':
								$this->return_change();
								break;
                 default:
                 break;
		    }
	}
	function return_change(){
		$cond='news.type="NEWS" and news.publish=1 and news.status!="HIDE" and category.name_id="'.Url::get('category_name_id').'"';
	    $news =NewsDB::get_news($cond);
			$vitri=1;
			$ketqua='';
			$chuoiketqua2='';
			foreach ($news as $key=>$value){
							if (file_exists($news[$key]['image_url'])){$image_url=$news[$key]['image_url'];}else{$image_url='assets/default/images/no_image.gif';}
					//Lay tin dau tien
					if ($vitri==1){
							$ketqua='
							<li class="img-1"><a href="'.Url::build('xem-trang-tin',array('name_id'=>$news[$key]['name_id']),1).'"><img class="img-top-1" src="'
							.$image_url
							.'"></a></li>
			<li class="name-top-1-bg"></li>
			<li class="name-top-1"><a id="href-main" href="'.Url::build('xem-trang-tin',array('name_id'=>$news[$key]['name_id']),1).'">'.MiString::display_sort_title( strip_tags($news[$key]['name']),10).'</a></li>';
			/*$ketqua.='|'.$news[$key]['brief'];*/
					}
					//lay cac tin tiep theo
							$chuoiketqua2.='
			<div id="warrap-content-right-news" class="anh-'.$news[$key]['id'].'" img="'.$news[$key]['image_url'].'" onmousemove="">
			<li><img class="icon-muiten" src="../../../../../assets/hotel/images/icon-muiten.png" /></li>
						<li class="brif-content-child"><a idanh="'.$news[$key]['id'].'" class="txt'.$news[$key]['id'].'" href="'.Url::build('xem-trang-tin',array('name_id'=>$news[$key]['name_id']),1).'">'.MiString::display_sort_title(strip_tags($news[$key]['name']),9).'</a></li>
			</div>';
			}
			//System::debug($news);
			//if (Portal::language()==1){$view_all='Xem tất cả';}else{$view_all='View all';}
			//$xemtatca='<div class="detail"><span><a href="#">>>'.$view_all.'</a></span></div>';
	}
	function update_seo(){
		if(Url::get('category_name_id') and $item = DB::fetch('SELECT id,name_id_'.Portal::language().' as name_id,name_'.Portal::language().' as name,brief_'.Portal::language().' as brief from category WHERE name_id_'.Portal::language().'="'.Url::get('category_name_id').'"')){
			Portal::$document_title = $item['name'];
			Portal::$meta_description = strip_tags($item['brief']);
		}
	}
}
?>