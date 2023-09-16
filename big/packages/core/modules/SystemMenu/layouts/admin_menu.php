<style type="text/css">
    .target-column{margin-top:5px;padding:5px;float: left;width: 100%;}
    .target-column .in{float: left;width:100%;height: 200px;border:2px solid #b8e4b0;border-radius: 10px;
        background: #d2ff52;
        background: -moz-linear-gradient(left, #d2ff52 0%, #91e842 62%, #91e842 100%);
        background: -webkit-gradient(left top, right top, color-stop(0%, #d2ff52), color-stop(62%, #91e842), color-stop(100%, #91e842));
        background: -webkit-linear-gradient(left, #d2ff52 0%, #91e842 62%, #91e842 100%);
        background: -o-linear-gradient(left, #d2ff52 0%, #91e842 62%, #91e842 100%);
        background: -ms-linear-gradient(left, #d2ff52 0%, #91e842 62%, #91e842 100%);
        background: linear-gradient(to right, #d2ff52 0%, #91e842 62%, #91e842 100%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#d2ff52', endColorstr='#91e842', GradientType=1 );
        overflow:hidden;}
    .target-column .in .total{
        text-align: center;
        color:#FFF;
        font-weight: bold;
        padding-top:80px;
        background: #5f6c82;
        width: 100%;
    }
</style>
<!-- 12345 -->
<link href="assets/vissale/content/Site.css" rel="stylesheet" type="text/css" />
<link href="assets/vissale/content/prettify.css" rel="stylesheet" type="text/css" />
<!-- Theme style -->
<link href="assets/vissale/content/admin/dist/css/AdminLTE.min.css?v=01122018" rel="stylesheet" type="text/css" />
<!-- adminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
<link href="assets/vissale/content/admin/dist/css/skins/_all-skins.min.css?v=01122018" rel="stylesheet" type="text/css" />
<link href="assets/vissale/content/admin/dist/css/skins/my_custom.css?v=01122018" rel="stylesheet" type="text/css" />
<!-- iCheck -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
<link rel="stylesheet" href="assets/vissale/bs-datetime/bootstrap-datetimepicker.min.css" />
<script type="text/javascript" src="assets/vissale/bs-datetime/moment.js"></script>
<script type="text/javascript" src="assets/vissale/bs-datetime/bootstrap-datetimepicker.min.js"></script>
<link href="assets/vissale/css/dxr.axd.css" rel="stylesheet" type="text/css" />
<link href="assets/vissale/css/style.css?v=20211026" rel="stylesheet" type="text/css" />
<!-- adminLTE App -->
<script src="assets/vissale/content/admin/dist/js/app.min.js" type="text/javascript"></script>
<!-- adminLTE for demo purposes -->
<script src="assets/vissale/content/admin/dist/js/demo.js" type="text/javascript"></script>
<header class="main-header">
    <!-- Logo -->
    <a href="Order" class="logo" style="background-color: #006600">
        <!-- mini logo for sidebar mini 50x50 pixels -->

                <span class="logo-mini">
                    <img src="https://tuha.vn/assets/standard/images/tuha_logo.png?v=03122021" style="height:45px" />
                </span>
        <!-- logo for regular state and mobile devices -->
                <span class="logo-lg">
                    <img src="https://tuha.vn/assets/standard/images/tuha_logo.png?v=03122021" style="height:45px" />
                </span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span class="hidden-xs"><?php echo Session::get('user_id'); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="trang-ca-nhan.html"><i class="fa fa-user fa-fw"></i> <?php echo Session::get('user_id'); ?></a>
                        </li>
                        <li><a href="?page=setting"><i class="fa fa-gear fa-fw"></i> Cấu hình</a>
                        </li>
                        <li class="divider"></li>
                        <li class="nav"><a href="<?php echo Url::build('sign_out',array()); ?>"><i class="fa fa-sign-out fa-fw"></i> Thoát</a>
                        </li>
                    </ul>
                    <script type="text/javascript">

                    </script>
                </li>
            </ul>
        </div>
    </nav>
</header>
<aside class="main-sidebar left-side">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <ul class="nav sidebar-menu">
            <li class="treeview">
                <a href="?page=dashboard"><i class="fa fa-dashboard"></i> <span class="menu-title">Dashboard</span></a>
            </li>
            <li class="treeview"><a href="?page=page"><i class="fa fa-globe"></i> <span class="menu-title">Pages</span></a></li>
            <li class="treeview"><a href="?page=module"><i class="fa fa-th-large"></i> <span class="menu-title">Modules</span></a></li>
            <li class="treeview"><a href="?page=package"><i class="fa fa-gift"></i> <span class="menu-title">Packages</span></a></li>
            <!--<li class="treeview"><a href="?page=function"><i class="fa fa-cogs"></i> <span class="menu-title">Functions</span></a></li>-->
    </section>
</aside>   