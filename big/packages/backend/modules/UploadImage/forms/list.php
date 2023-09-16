<?php
class UploadImageForm extends Form
{
	function UploadImageForm()
	{
		Form::Form('UploadImageForm');
		$this->link_css('assets/default/css/global.css');
		$this->link_css('assets/admin/css/style.css');
		$this->link_css('assets/default/css/cms.css');
		$this->link_js('assets/admin/scripts/jquery-1.7.1.js');
		$this->link_js('assets/admin/scripts/swfupload/swfupload.js');
		$this->link_js('assets/admin/scripts/handlers.js');		
		if(User::is_admin()){
			//DB::update_id('product',array('checked_info'=>1),Session::get('product_id'));
		}
	}
	function on_submit()
	{
		if(Url::iget('product_id') and $product = DB::fetch('select * from product where product.id='.Url::iget('product_id'))){
			/*if(isset($_FILES['Filedata']))
			{
				$dir = 'product/'.$product['account_id'];
				require_once 'packages/core/includes/utils/upload_file.php';
				if($images_url = FTP::upload_multi_file('Filedata',$dir,true))
				{
					$max_position = DB::fetch('select id, max(position) as position from product_image where product_id = '.$product['id'].' group by product_id');
					$pos = intval($max_position['position'])+1;
					foreach($images_url as $value)
					{
						DB::insert('product_image',array('name'=>'','image_url'=>$value['image_url'],'thumb_url'=>$value['thumb_url'],'small_thumb_url'=>$value['small_thumb_url'],'product_id'=>$product['id'],'position'=>$pos));
						$pos++;
					}
				}
			}*/
			if($title = Url::get('title'))
			{
				foreach($title as $key=>$value)
				{
					DB::update('product_image',array('name'=>$value),'product_id = '.$product['id'].' and id='.$key);
				}
			}
			if(!User::can_admin(false,ANY_CATEGORY)){
				$checked_info = 0;
			}else{
				$checked_info = 1;
			}
			//DB::update('product',array('checked_info'=>$checked_info),'id = '.Session::get('product_id').'');
			save_log(1);
			Url::redirect_current(array('product_id','cmd'=>'success'));
		}else
		{
			$this->error('upload_error','Cõ lỗi xảy ra trong quá trình tải ảnh');
		}
	}
	function draw()
	{
		if($id = Url::iget('product_id') and $product=DB::exists_id('product',$id))
		{
			require_once 'cache/config/notice.php';
			$sql = '
				SELECT
					product_image.*
				FROM
					product_image
					inner join product on product.id = product_image.product_id
				WHERE
					product.id = '.$id.'
				ORDER BY
					position
			';
			$this->map['images'] = DB::fetch_all($sql);
			$current = current($this->map['images']);
			$this->map['image'] = $current['image_url'];
			$max = 20;
			$haved_upload = count($this->map['images']);
			$this->map['limit'] = $max - $haved_upload;
			$this->map['product_id'] = $id;
			$this->map['notice'] = strtr($notice['product_image_notice'],
											array('[[=limit=]]'=>'<b style="color:blue">'.$max.'</b>',
											'[[=numbers=]]'=>'<b style="color:blue">'.$haved_upload.'</b>'));
			$this->parse_layout('list',$this->map);
		}else{
			echo '<h3 class="alrert alert-default">Quý bạn vui lòng thêm thư viện ảnh sản phẩm sau khi đã cập nhật đầy đủ thông tin sản phẩm và ghi lại.</h3>';
		}
	}
}
?>