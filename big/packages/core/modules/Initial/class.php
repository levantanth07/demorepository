<?php
/******************************
COPY RIGHT BY NYN PORTAL - TCV
WRITTEN BY thedeath
******************************/
class Initial extends Module
{
	function Initial($row)
	{
		Module::Module($row);
		require_once 'db.php';
		if(User::can_admin())
		{
			require_once 'packages/core/includes/utils/xml.php';
			switch(Url::get('cmd'))
			{
				case 'empty':
				case 'restore':
					$this->restore();
					break;
				case 'make_temp':
					$this->make_temp();
					break;
				case 'copy_portal':
				default:
					require_once 'forms/list.php';
					$this->add_form(new InitialForm());
			}
		}
		else
		{
			Url::access_denied();
		}
	}
	function restore_db($file)
	{
		$path = 'backup/xml/'.$file.'.xml';
		if(file_exists($path))
		{
			$items = XML::fetch_all($path);
			if($items and count($items)>0 and $file!='account_setting')
			{
				foreach($items as $key=>$value)
				{
					DB::insert($file,$value);
				}
			}
			@unlink($path);
		}
	}
	function restore()
	{
		set_time_limit(0);
		Initial::restore_db('account_privilege');
		Initial::restore_db('account_setting');
		Initial::restore_db('advertisment');
		Initial::restore_db('category');
		Initial::restore_db('comment');
		Initial::restore_db('contact');
		Initial::restore_db('log');
		Initial::restore_db('manufacturer');
		Initial::restore_db('media');
		Initial::restore_db('news');
		Initial::restore_db('product');
		Initial::restore_db('slide');
		Initial::restore_db('survey');
		Initial::restore_db('survey_options');
		Initial::restore_db('page');
		Initial::restore_db('block');
		Initial::restore_db('block_setting');
		Url::redirect_current();
	}
	function make_temp()
	{
		set_time_limit(0);
		$account_privilege = DB::fetch_all('select * from account_privilege');
		XML::create_xml('backup/xml/account_privilege',$account_privilege);
		DB::query(' delete from `account_privilege` ');
		$account_setting = DB::fetch_all('select * from account_setting');
		XML::create_xml('backup/xml/account_setting',$account_setting);
		DB::query(' delete from  `account_setting` ');
		$advertisment = DB::fetch_all('select * from advertisment');
		XML::create_xml('backup/xml/advertisment',$advertisment);
		DB::query(' delete from  `advertisment` ');
		$category = DB::fetch_all('select * from `category` where structure_id!="'.ID_ROOT.'"');
		XML::create_xml('backup/xml/category',$category);
		DB::query(' delete from `category` where structure_id!="'.ID_ROOT.'"');
		$comment = DB::fetch_all('select * from comment');
		XML::create_xml('backup/xml/comment',$comment);
		DB::query(' delete from  `comment` ');
		$contact = DB::fetch_all('select * from contact');
		XML::create_xml('backup/xml/contact',$contact);
		DB::query(' delete from  `contact` ');
		$log = DB::fetch_all('select * from log');
		XML::create_xml('backup/xml/log',$log);
		DB::query(' delete from  `log` ');
		$manufacturer = DB::fetch_all('select * from manufacturer');
		XML::create_xml('backup/xml/manufacturer',$manufacturer);
		DB::query(' delete from  `manufacturer` ');
		$media = DB::fetch_all('select * from media');
		XML::create_xml('backup/xml/media',$media);
		DB::query(' delete from  `media` ');
		$news = DB::fetch_all('select * from news');
		XML::create_xml('backup/xml/news',$news);
		DB::query(' delete from  `news` ');
		$product = DB::fetch_all('select * from product');
		XML::create_xml('backup/xml/product',$product);
		DB::query(' delete from  `product` ');
		$slide = DB::fetch_all('select * from slide');
		XML::create_xml('backup/xml/slide',$slide);
		DB::query(' delete from  `slide` ');
		$survey = DB::fetch_all('select * from survey');
		XML::create_xml('backup/xml/survey',$survey);
		DB::query(' delete from  `survey` ');
		$survey_options = DB::fetch_all('select * from survey_options');
		XML::create_xml('backup/xml/survey_options',$survey_options);
		DB::query(' delete from  `survey_options` ');
		DB::query(' TRUNCATE TABLE `visit` ');
		DB::query(' TRUNCATE TABLE `visit_page` ');
		$page = DB::fetch_all('select * from page where params !=" " and name!="trang-chu" and name!="home" and name!="dang-nhap" and name!="sign_in" and name!="sign_out" and package_id=331');
		XML::create_xml('backup/xml/page',$page);
		foreach($page as $key=>$value)
		{
			$blocks = DB::fetch_all('select * from block where page_id="'.$value['id'].'"');
			XML::create_xml('backup/xml/block',$blocks);
			foreach($blocks as $id=>$block)
			{
				$block_setting = DB::fetch_all('select * from block_setting where block_id="'.$block['id'].'"');
				XML::create_xml('backup/xml/block_setting',$block_setting);
				DB::query('delete from block_setting where block_id="'.$block['id'].'"');
			}
			DB::query('delete from block where page_id="'.$value['id'].'"');
		}
		DB::query('delete from page where params !="" and name!="trang-chu" and name!="home" and name!="dang-nhap" and name!="sign_in" and name!="sign_out" and package_id=331');
		Url::redirect_current();
	}
}
?>