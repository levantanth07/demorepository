<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="content-language" content="vi" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="CACHE-CONTROL" content="NO-CACHE" />
    <meta name="keywords" content="<?php echo (Portal::$meta_keywords)?''.strip_tags(Portal::$meta_keywords):Portal::get_setting('website_keywords_'.Portal::language());?>" />
    <meta name="description" content="<?php echo Portal::$meta_description?Portal::$meta_description:Portal::get_setting('website_description_'.Portal::language());?>" />
    <meta name="ROBOTS" content="ALL" />
    <meta name="author" content="<?php echo Portal::get_setting('site_name_'.Portal::language());?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php if(
        Url::get('page') == 'dang-nhap'
        or !Url::get('page')
        or Url::get('page') == 'trang-tin'
        or Url::get('page') == 'xem-trang-tin'
    ){?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php }?>
    <meta name="google-site-verification" content="CEy_vMWra0OSoRvJg8ZbntQPe0__bHk5XBoTWoUT6fU" />
    <meta property="fb:app_id" content="208527286162605"/>
    <title><?php echo (Portal::$document_title ? Portal::$document_title : Portal::get_setting(PREFIX.'site_title_'.Portal::language()))?></title>
    <base href="<?php echo System::get_base_url(); ?>" />
    <meta property="og:image" content="<?php echo Portal::$image_url?Portal::$image_url:'';?>" />
    <meta property="og:title" content="<?php echo (Portal::$document_title?(Portal::$document_title):Portal::get_setting('site_title_'.Portal::language()));?>" />
    <meta property="og:description" content="<?php echo Portal::$meta_description?Portal::$meta_description:Portal::get_setting('website_description_'.Portal::language());?>" />
    <meta property="og:url" content="<?php echo DataFilter::removeXSSinHtml('https://'.$_SERVER['HTTP_HOST'].''.$_SERVER['REQUEST_URI']);?>" />
    <link rel="image_src" href="<?php echo Portal::$image_url?Portal::$image_url:'';?>" />
    <link rel="shortcut icon" href="<?php echo Portal::get_setting('site_icon');?>" />
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-M2TWMB553M"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-M2TWMB553M');
    </script>
    <script type="text/javascript"> query_string = "?<?php echo urlencode($_SERVER['QUERY_STRING']);?>"; PORTAL_ID = "<?php echo substr(PORTAL_ID,1);?>";</script>
    <script type="text/javascript" src="assets/standard/js/jquery.js"></script>
    <?php echo Portal::$extra_header;?>
    <?php echo ($_SERVER['HTTP_HOST']=='tuha.vn' or $_SERVER['HTTP_HOST']=='big.shopal.vn' or $_SERVER['HTTP_HOST']=='balancer.tuha.vn')?Portal::get_setting('google_analytics'):'';?>
    <link rel="stylesheet" href="assets/vissale/lib/AdminLTE/pace/pace.min.css">
    <script type="application/javascript" src="assets/vissale/lib/AdminLTE/pace/pace.min.js"></script>
    <script type="application/javascript">
        $(document).ready(function () {
            $(document).ajaxStart(function() { Pace.restart(); });
        });
    </script>
<?php
$new_user = 0;
if(User::is_login() and !System::is_local()){?>
    <script type="application/ld+json">{
	"@context": "http://schema.org/",
  	"@type": "ProfessionalService",
"@id":"https://tuha.vn/",
	"url": "https://tuha.vn/",
	"logo": "https://media.tuha.vn/upload/default/content/08072019/logo-tuha.jpg",
    "image":"https://media.tuha.vn/upload/default/content/08072019/logo-tuha.jpg",
    "priceRange":"10$-100$",
	"hasMap": "https://goo.gl/maps/Lw9N39M2KJJNum9Z8",	
	"email": "hotro@palvietnam.vn",
    "founder": "Đăng Khoa",
  	"address": {
    	"@type": "PostalAddress",
    	"addressLocality": "Thanh Xuân",
        "addressCountry": "VIỆT NAM",
    	"addressRegion": "Hà Nội",
    	"postalCode":"100000",
    	"streetAddress": "ngõ 28 Ngụy Như Kon Tum, Nhân Chính, Thanh Xuân, Hà Nội 100000"
  	},
  	"description": "Phần mềm quản lý bán hàng online QLBH tự động giật đơn từ Facebook và các kênh khác, an toàn dữ liệu tuyệt đối, dễ dàng sử dụng ✅GỌI NGAY 03.9557.9557✅",
	"name": "Phần mềm bán hàng Online Tuha.vn ",
  	"telephone": "0395-579-557",
  	"openingHoursSpecification": [
  {
    "@type": "OpeningHoursSpecification",
    "dayOfWeek": [
      "Monday",
      "Tuesday",
      "Wednesday",
      "Thursday",
      "Friday",
      "Saturday",
      "Sunday"
    ],
    "opens": "08:30",
    "closes": "17:30"
  }
],
  	"geo": {
    	"@type": "GeoCoordinates",
   	"latitude": "20.9981612",
    	"longitude": "105.7973935"
 		}, 		
		
"potentialAction": {
"@type": "ReserveAction",
"target": {
"@type": "EntryPoint",
"urlTemplate": "https://tuha.vn/dang-nhap/",
"inLanguage": "vn",
"actionPlatform": [
"http://schema.org/DesktopWebPlatform",
"http://schema.org/IOSPlatform",
"http://schema.org/AndroidPlatform"
]
},
"result": {
"@type": "Reservation",
"name": "đăng kí"
}
},	
         
  		"sameAs" : [ "https://medium.com/@tuhapal",
			"https://twitter.com/tuhapal",
    	              "https://issuu.com/tuhapal",
                       "https://soundcloud.com/tuhapal",
                        "https://www.scoop.it/u/phan-mem-quan-ly-ban-hang-tuha",
			"https://www.reddit.com/user/tuhapal",
                        "https://ello.co/tuhapal",
			"https://www.plurk.com/tuhapal",
                        "https://www.viki.com/users/tuhapal/",
			"https://followus.com/tuhapal",
			"https://www.linkedin.com/in/phan-mem-ban-hang-tuha-32b0a5188/",
                	"https://myspace.com/tuhapal",
			"https://www.pinterest.com/tuhapal/",
			"https://www.ok.ru/tuhapal12",
			"https://www.facebook.com/tuha.vn/",
			"https://www.flickr.com/people/181702151@N07/",
			"https://imgur.com/user/tuhapal/about",
			"http://profile.hatena.ne.jp/tuhapal/profile",
			"https://vimeo.com/tuhapal",
			"https://www.diigo.com/user/tuhapal",
			"http://www.folkd.com/user/tuhapal",
			"https://tuhapal.tumblr.com/",
			"https://flipboard.com/@tuhapal",
			"https://www.behance.net/tuhapal",
			"https://www.instapaper.com/p/tuhapal",
			"https://mix.com/tuhapal",
			"https://getpocket.com/@tuhapal",
			"https://linkhay.com/u/tuhapal",
			"https://www.youtube.com/channel/UClRZSsHQtmic4H5hFoIFGzw/",
			"https://about.me/tuhapal"
		]
	}</script>
    <script type="application/ld+json">{"@context":"https:\/\/schema.org","@graph":[{"@context":"https:\/\/schema.org","@type":"SiteNavigationElement","id":"site-navigation","name":"Giới thiệu","url":"https://tuha.vn/bai-viet/gioi-thieu/phan-mem-quan-ly-ban-hang-online-tuha/"},{"@context":"https:\/\/schema.org","@type":"SiteNavigationElement","id":"site-navigation","name":"Báo giá","url":"https://tuha.vn/bao-gia-tuha"},{"@context":"https:\/\/schema.org","@type":"SiteNavigationElement","id":"site-navigation","name":"Tuyển dụng","url":"https://tuha.vn/bai-viet/tuyen-dung/"},{"@context":"https:\/\/schema.org","@type":"SiteNavigationElement","id":"site-navigation","name":"Hướng dẫn sử dụng","url":"https://tuha.vn/bai-viet/huong-dan-su-dung/"},{"@context":"https:\/\/schema.org","@type":"SiteNavigationElement","id":"site-navigation","name":"Câu chuyện thành công","url":"https://tuha.vn/bai-viet/cau-chuyen-thanh-cong/"},{"@context":"https:\/\/schema.org","@type":"SiteNavigationElement","id":"site-navigation","name":"Kinh nghiệm kinh doanh","url":"https://tuha.vn/bai-viet/kinh-nghiem-kinh-doanh/"},{"@context":"https:\/\/schema.org","@type":"SiteNavigationElement","id":"site-navigation","name":"Mẹo hay","url":"https://tuha.vn/bai-viet/meo-hay/"},{"@context":"https:\/\/schema.org","@type":"SiteNavigationElement","id":"site-navigation","name":"Tin tức về Tuha","url":"https://tuha.vn/bai-viet/tin-tuc-ve-tuha/"}]}</script>

    <!--TUHA CALLCENTER-->
    <style type=text/css>
        [data-notify="title"] {
            color: #FFF;
            display: block;
            font-weight: bold;
        }

        [data-notify="message"] {
            /* color: #000; */
            font-size: 80%;
        }

        .message span {
            display: block;
        }
    </style>
    <?php
        $new_user = (isset($_SESSION['user_data']['user_status']) and $_SESSION['user_data']['user_status']<=1)?1:0;
    ?>
    <?php } ?>

    <!--
    <script  type=text/javascript type="text/javascript" src="https://call-connector.tuha.vn:8009/dependencies/sails.io.js" autoconnect="true"></script>
    <script  type=text/javascript src="https://call-connector.tuha.vn:8009/bootstrap-notify-3.1.3/bootstrap-notify.min.js"></script>
    <script  type=text/javascript src="https://call-connector.tuha.vn:8009/js/lodash.js"></script>
    <script type=text/javascript>
        // ** Global Variables **//
        const shop_id = '<?php echo Session::get('group_id'); ?>';
    </script>
    <script type="text/javascript" src="assets/standard/js/tuha-cc.js"></script>
    -->

    <script src="/assets/standard/js/camera.js?v=18032022"></script>
    <style>
        .my_camera{
            width: 400px;
            height: 400px;
            border: 1px solid black;
        }
    </style>
</head>
<body class="skin-blue <?=$new_user?'wysihtml5-supported sidebar-mini':'wysihtml5-supported sidebar-mini sidebar-collapse hold-transition'?>">