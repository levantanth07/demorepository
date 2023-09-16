<!-- adminLTE App -->
<script src="assets/vissale/content/admin/plugins/slimscroll/jquery.slimscroll.min.js?v=22012019"></script>
<script src="assets/vissale/content/admin/dist/js/app.min.js?v=22012019"></script>
<!-- 12345 -->
<link href="assets/vissale/content/Site.css" rel="stylesheet" type="text/css" />
<link href="assets/vissale/content/prettify.css" rel="stylesheet" type="text/css" />
<!-- Theme style -->
<link href="assets/vissale/content/admin/dist/css/AdminLTE.min.css?v=10072019" rel="stylesheet" type="text/css" />
<!-- adminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
<link href="assets/vissale/content/admin/dist/css/skins/_all-skins.min.css?v=10072019" rel="stylesheet" type="text/css" />
<link href="assets/vissale/content/admin/dist/css/skins/my_custom.css?v=03012020" rel="stylesheet" type="text/css" />
<!-- iCheck -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
<link rel="stylesheet" href="assets/vissale/bs-datetime/bootstrap-datetimepicker.min.css" />
<script type="text/javascript" src="assets/vissale/bs-datetime/moment.js"></script>
<script type="text/javascript" src="assets/vissale/bs-datetime/bootstrap-datetimepicker.min.js"></script>
<link href="assets/vissale/css/dxr.axd.css" rel="stylesheet" type="text/css" />
<link href="assets/vissale/css/style.css?v=20211214" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="introjs/intro.js"></script>
<link href="introjs/introjs.css" rel="stylesheet" />

<?php if (Session::get('callio_payload')) { ?>
<!-- <script type="text/javascript" src="assets/lib/callio/jssip.min.js"></script> -->
<?php } ?>

<style>
#footer{color:#FFF;padding:10px;}
#footer h3{font-size:14px;}
.item,.item a{color:#fff;}
.white-light{
    padding:5px;
    color:#999;
}
</style>
<?php
  $notifications = [[=notifications=]];
  $schedules = [[=schedules=]];
  $quyen_mkt = check_user_privilege('MARKETING');
  $quyen_admin_mkt = check_user_privilege('ADMIN_MARKETING');
?>
<div class="modal fade" id="paymentHelp" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="color:#f00;">Hướng dẫn thanh toán</h4>
            </div>
            <div class="modal-body">
                <p>Tài khoản của quý khách sẽ hết hạn vào ngày <span style="color:#F00;font-size:20px;">[[|expired_date|]]</span>.</p>
                <p>Để gia hạn, quý khách vui lòng thực hiện liên hệ qua số điện thoại 03.9557.9557</p>
                <hr>
                <div class="text-center"><h4>Cám ơn quý khách!</h4></div>
                <br clear="all">
                <div class='modal-footer'>
                    <div class="pull-right">
                        <span id="prepairingExpiredBtn" class="btn btn-danger" , data-dismiss="modal" aria-label="Close">Đóng</span>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<header class="main-header position-sticky" <?=Url::get('window')?'hidden':'';?> ">
    <!-- Logo -->
    <a href="<?=Url::build('admin_orders');?>" class="logo hidden-xs hidden-sm hidden-md" tabindex="-1">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini">
            <img src="assets/standard/images/tuha_logo.png?v=03122021" style="height:50px" />
        </span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">
            <img src="assets/standard/images/tuha_logo.png?v=03122021" style="height:50px" />
        </span>
    </a>
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="javascript:void(0);" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!--IF:cond(Url::get('page')!='admin_orders')-->
        <div class="pull-left hidden-xs hidden-sm hidden-md" style="padding: 8px 0px 0px 10px;width: 150px">
            <input type="text" placeholder="Tìm số điện thoại" class="form-control" onchange="window.location='<?=Url::build('admin_orders');?>&admin_orders&keyword='+this.value;">
        </div>
        <!--/IF:cond-->
        <div class="navbar-custom-menu <?=Url::get('window')?'hidden':'';?>">
            <ul class="nav navbar-nav navbar-right">
                <?php if(Menu::$new_user){?>
                <li>
                    <a class="label label-info" data-toggle="tooltip" data-placement="bottom" title="Khi bạn đã sử dụng được phần mềm, phần hiển thị hướng dẫn cho người mới bắt đầu sẽ tắt đi.">
                        Tôi đã biết sử dụng: <input type="checkbox" onclick="updateUserStatus(this);" value="1">
                    </a>
                </li>
                <?php }?>
                <!--IF:cond([[=system_group_id=]]==4 and ($quyen_admin_mkt or $quyen_mkt))-->
                <li>
                    <a class="text-danger" href="<?=Url::build('admin_group_info');?>">Bạn vui lòng chỉnh lại API kết nối về zoma.shopal.vn</a>
                </li>
                <!--/IF:cond-->
                <!--IF:admin_group_cond(Session::get('admin_group'))-->
                <li class="hidden-xs">
                    <a href="<?=Url::build('trang-gioi-thieu')?>" title="Trang giới thiệu tổng quan quy trình bán hàng online">
                        <i class="fa fa-lightbulb-o"></i> Giới thiệu
                    </a>
                </li>
                <!--/IF:admin_group_cond-->
                <li class="dropdown small hidden-xs">
                    <a class="text-success" data-toggle="modal" data-target="#paymentHelp"><i class="fa fa-money"></i> HD thanh toán</a>
                </li>
                <li class="dropdown hidden-xs">
                    <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                        <i class="fa fa-phone"></i>
                        <span class="count">Hỗ trợ</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" onclick="return false;"><strong><i class="fa fa-ambulance"></i> Chăm sóc khách hàng</strong></a>
                        </li>
                        <li>
                            <a href="http://get.teamviewer.com/phanmembanhang365" target="_blank">
                                <i class="fa fa-download"></i> Tải về TeamViewer
                            </a>
                        </li>
                        <li>
                            <a href="https://ultraviewer.net/vi/download.html" target="_blank">
                                <i class="fa fa-download"></i> Tải về UltraViewer
                            </a>
                        </li>
                        <li>
                            <a href="https://drive.google.com/drive/folders/16zRDcd2AAn6TPKQhUx9gGk9BZVQ3o5xV" target="_blank">
                                <i class="fa fa-download"></i> Drivers cho các thiết bị
                            </a>
                        </li>
                        <li>
                            <a href="https://drive.google.com/uc?export=download&amp;id=0B0kuvBxLBrKiVVZmNWJMLXQwUjA" target="_blank">
                                <i class="fa fa-download"></i> Tải phần mềm hỗ trợ cân điện tử
                            </a>
                        </li>
                        <li>
                            <a href="https://drive.google.com/drive/folders/1G-zUX1QzaQc_4CC8a5hekp0v5VAG6ZcH" target="_blank">
                                <i class="fa fa-print"></i> Phần mềm hỗ trợ in Tự động, Báo chế biến...
                            </a>
                        </li>
                        <li>
                            <a href="tel:<?php echo Portal::get_setting('hot_line');?>">
                                <i class="fa fa-phone-square"></i> Hỗ trợ 24/7 : <?php echo Portal::get_setting('hot_line');?>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown notifications-menu">
                    <a href="#" class="dropdown-toggle dropwdown-parent" data-toggle="dropdown" aria-expanded="true">
                        _<i class="fa fa-bell-o"></i>_
                        <span class="label label-<?= [[=total_notification=]]?'warning':'default'?>"><?= [[=total_notification=]] ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if (!empty($notifications)){ ?>
                        <li class="header">Bạn có <?= [[=total_notification=]] ?> thông báo mới.</li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu" id="notification-scroll">
                                <?php
                                foreach ($notifications as $notification):
                                $link = '';
                                $class_active = $notification['is_read'] != 1 ? 'noti-active' : "";
                                if ($notification['notificationable_type'] == 1):
                                $link = Url::build('admin_orders').'&cmd=shipping_history&id=' . $notification['notificationable_id'];
                                ?>
                                <li>
                                    <a href="<?= $link ?>" class="<?= $class_active ?>">
                                        <?= $notification['content'] ?>
                                        <div><b><?= date('d/m/Y H:i:s', strtotime($notification['created_at'])) ?></b></div>
                                    </a>
                                </li>
                                <?php else: ?>
                                <li>
                                    <div class="<?= $class_active ?>">
                                        <div><b><?= $notification['title'] ?></b></div>
                                        <?= $notification['content'] ?>
                                        <div><b><?= date('d/m/Y H:i:s', strtotime($notification['created_at'])) ?></b></div>
                                    </div>
                                </li>
                                <?php endif; ?>
                        <?php endforeach; ?>
                            </ul>
                        </li>
                        <?php }else{ ?>
                            <li class="header">Chưa có thông báo mới.</li>
                        <?php }//endif; ?>
                        <li class="footer"><a href="<?=Url::build('notifications');?>" target="_blank"><i class="fa fa-chevron-right"></i> Xem tất cả</a></li>
                    </ul>
                </li>
                <li class="dropdown notifications-menu">
                    <a href="#" title="Lịch hẹn" class="dropdown-toggle dropwdown-parent" data-toggle="dropdown" aria-expanded="true">
                        _<i class="fa fa-calendar"></i>_
                        <span class="label label-<?= [[=total_schedule=]]?'warning':'default'?>"><?= [[=total_schedule=]] ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <?php
                        foreach ($schedules as $schedule):
                        $link = Url::build('customer',[ 'cid' => ($schedule['customer_id']), 'idLichHen' => $schedule['id'], 'do' => 'view' ] ).'#lichhen';
                        $class_active = "";
                        ?>
                        <li>
                            <a href="<?= $link ?>" class="<?= $class_active ?>">
                                <?= $schedule['note'] ?>
                                <div><i class="fa fa-clock-o"></i> <b><?= date('d/m/Y H:i\'', ($schedule['appointed_time'])) ?></b></div>
                            </a>
                        </li>
                        <?php endforeach; ?>
                        <li class="footer text-center">
                            <a href="<?=Url::build('lich-hen');?>" target="_blank">
                                <i class="fa fa-chevron-right"></i>
                                Xem tất cả
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                      <img src="[[|avatar_url|]]" class="user-image" alt="[[|account_id|]]" onerror="this.src='assets/standard/images/no_avatar.png'">
                      *<i class="fa fa-chevron-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="[[|avatar_url|]]" class="img-circle" alt="[[|account_id|]]" onerror="this.src='assets/standard/images/no_avatar.png'">
                            <p>
                                [[|full_name|]]
                                <small>
                                    [[|account_id|]]
                                    <!--IF:cond____([[=rated_point=]]>0)-->
                                    <span class="badge badge-warning">[[|rated_point|]] <i class="fa fa-star"></i> ([[|rated_quantity|]])</span>
                                    <!--/IF:cond____-->
                                </small>
                            </p>
                        </li>
                      <!-- Menu Body -->
                      <li class="user-body">
                        <div class="row">
                          <div class="col-xs-12 text-center">
                            <a class="btn btn-default btn-sm" <?=Session::get('admin_group')?'href="'.Url::build('admin_group_info').'" title="Vào tùy chỉnh SHOP: '.[[=group_name=]].'"':''?>>
                               <i class="fa fa-building-o"></i> [[|group_name|]] (ID: <?php echo Session::get('group_id');?>)
                            </a>
                          </div>
                          <hr>
                           <!--IF:phone_store_cond([[=phone_store_name=]])-->
                            <div class="text-center">
                                <a href="<?php echo Url::build('admin_orders');?>">
                                    Kho số: <strong>[[|phone_store_name|]]</strong>
                                </a>
                            </div>
                            <!--/IF:phone_store_cond-->
                        </div>
                        <div class="row">
                          <div class="col-xs-6 text-left small text-warning">
                            <i class="fa fa-clock-o"></i> Hạn: [[|expired_date|]]
                          </div>
                          <div class="col-xs-6 text-right small">
                            <a target="_blank" href="https://work.tuha.vn/dang-nhap.html?token=[[|likeworking_login_url|]]">
                                <i class="fa fa-check-square-o"></i> Chấm công
                            </a>
                          </div>
                        </div>
                        <!-- /.row -->
                      </li>
                      <!-- Menu Footer-->
                      <li class="user-footer">
                        <div class="pull-left">
                          <a href="trang-ca-nhan/" class="btn btn-default btn-flat">Trang cá nhân</a>
                        </div>
                        <div class="pull-right">
                          <a href="#" id="logoutBtn" class="btn btn-danger btn-flat"><i class="fa fa-sign-out fa-fw"></i> Thoát</a>
                        </div>
                      </li>
                    </ul>
                  </li>
                <li style="padding:5px 15px 0px 0px;"><canvas id="canvas" width="40" height="40"></canvas></li>
            </ul>
        </div>
    </nav>
</header>

<?php if (Session::get('callio_payload')) { ?>
<script type="text/javascript">
    var CALLIO_API = { baseUrl: 'https://client.callio.vn', token: '' }, CALLIO_LoadStart = new Date();
    var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
    $(document).ready(() => {
        //Kiểm tra có được sử dụng mic không
        navigator.mediaDevices.getUserMedia({audio: true}).then(() => {
            $.ajax({
                url : "https://clientapi.phonenet.io/auth/login",
                type : "post",
                data : {
                     email : '<?= Session::get('callio_payload')['email'] ?>',
                     password : 'abc@12345'
                },
                success : function (result){
                    CALLIO_API.token = result.token;
                    s1.async = true;
                    s1.src = CALLIO_API.baseUrl + '/widget-embed.js';
                    s1.charset = 'UTF-8';
                    s0.parentNode.insertBefore(s1, s0);
                }
            });
            $('.callio-call-customer').click(function(e){
                $("#box-call-callio").show();
                $("#end").show();
                phonenumber = this.dataset.phonenumber;
                phonename = phonenumber;
                if (this.dataset.phonename) {
                    phonename = this.dataset.phonename;
                }
                if (phonenumber != '') {
                    CALLIO_API.makeCall(phonenumber);
                }
            });
        }).catch(() => {
            //K có mic
            alert('Máy tính không có mic hoặc không được cấp quyền dùng mic!');
        });
    });
</script>
<!--
<div id="box-call-callio" style="background: white; padding: 10px; border-bottom: 3px solid #3C8DBC;">
    <div class="text-right">
        <span id="phone-number-incoming"></span>
        <span id="status"></span>
        <button id="answer" style="background: #00a65a; border: #008d4c; color: white; display: none">Trả lời</button>
        <button id="end" style="background: #dd4b39; border: #d73925; color: white; display: none">Dừng</button>
    </div>

    <script>
        $("#box-call-callio").hide();
        $("#answer").hide();
        $("#end").hide();
        var nhacChuong = new Audio('assets/default/audio/nhacchuong.mp3');
        if (typeof nhacChuong.loop == 'boolean') {
            nhacChuong.loop = true;
        } else {
            nhacChuong.addEventListener('ended', function() {
                this.currentTime = 0;
                this.play();
            }, false);
        }

        let host = '<?= Session::get('callio_payload')['host'] ?>';
        let ext = '<?= Session::get('callio_payload')['ext'] ?>';
        let password = '<?= Session::get('callio_payload')['password'] ?>';
        let contact_uri = 'sip:' + ext + '@' + Math.random().toString(36).substring(2, 15) + '.invalid;transport=ws';
        let phonename = null;
        let phonenumber = null;
        let phone = null;
        let call = null;

        let callOptions = {
            mediaConstraints: {
                audio: true,
                video: false
            },
            pcConfig: {
                iceServers: [
                    {
                        urls: [
                            'stun:stun-vn-01.phonenet.io',
                            'stun:stun.l.google.com:19302'
                        ]
                    },
                ],
            }
        };

        $(document).ready(() => {
            //Kiểm tra có được sử dụng mic không
            navigator.mediaDevices.getUserMedia({audio: true}).then(() => {
                //Kết nối webrtc tới phonenet
                phone = new JsSIP.UA({
                    sockets: [new JsSIP.WebSocketInterface('wss://' + host + ':7443')],
                    uri: 'sip:' + ext + '@' + host,
                    realm: host,
                    password: password,
                    contact_uri: contact_uri
                });
                phone.on('connected', (e) => {
                    //Kết nối thành công
                    console.log('Phone registered');
                    $('#status').text('Điện thoại sẵn sàng');
                    //Thực hiện gọi
                    $('.callio-call-customer').click(function(e){
                    	$("#box-call-callio").show();
                    	$("#end").show();
                        phonenumber = this.dataset.phonenumber;
                        phonename = phonenumber;
                        if (this.dataset.phonename) {
                    		phonename = this.dataset.phonename;
                    	}
                        if (phonenumber != '') {
                            phone.call(phonenumber, callOptions);
                            call.connection.onaddstream = function (e) {
                                const remoteAudio = document.createElement('audio');
                                remoteAudio.srcObject = e.stream;
                                remoteAudio.play();
                            };
                            $('#end').click(() => {
                            	if(call){
                                    call.terminate();
                                }
                                setTimeout(function(){ $("#box-call-callio").hide(); }, 2000);
                                $("#answer").hide();
                    	        $("#end").hide();
                            });
                            $('#status').text('Đang gọi');
                        } else {
                            alert('Mời nhập số');
                        }
                    })
                });
                phone.on('disconnected', () => {
                    //Mất kết nối, thông thường nó sẽ tự kết mối lại
                    $('#status').text('Mất kết nối');
                    console.log('Phone disconnected');
                });
                phone.start();
                phone.on("newRTCSession", (data) => {
                    //Có cuộc gọi mới
                    if(call){
                        data.session.terminate();
                        return;
                    }
                    // console.log(data);
                    call = data.session;
                    if (call.direction === "incoming") {
                    	phonenumber = call._request.from._display_name;
                    	phonename = phonenumber;

                    	$("#box-call-callio").show();
                    	$("#answer").show();
                        $('#status').text('Bấm trả lời để nhận');
                        $('#answer').click(() => {
                        	if (nhacChuong.played) {
                        		nhacChuong.pause();
                        	}
                        	$("#answer").hide();
                        	$('#status').text('Đang kết nối');

                            call.answer(callOptions);
                            call.connection.onaddstream = function (e) {
                                const remoteAudio = document.createElement('audio');
                                remoteAudio.srcObject = e.stream;
                                remoteAudio.play();
                            };

                            $('#end').click(() => {
                            	if (nhacChuong.played) {
                                    nhacChuong.pause();
                                }
                                call.terminate();
                            });
                        });
                    }

                    $('#phone-number-incoming').text('Số khách ' + phonename + ' |');
                    call.on("icecandidate", (e) => {
                        setTimeout(() => {
                            e.ready();
                        }, 10000);
                    });
                    call.on("progress", function () {
                        //Đổ chuông
                        // if (nhacChuong.paused) {
                        //     nhacChuong.play();
                        // }
                        $('#status').text('Đang đổ chuông');
                        console.log('progress');
                    });
                    call.on("accepted", function () {
                        //Đầu khách trả lời
                        if (nhacChuong.played) {
                            nhacChuong.pause();
                        }
                        $('#status').text('Đang đàm thoại');
                        $("#end").show();
                        console.log('accepted');
                    });
                    call.on("confirmed", function () {
                        //Kết nối ok
                        if (nhacChuong.played) {
                            nhacChuong.pause();
                        }
                        $('#status').text('Đang đàm thoại');
                        $("#end").show();
                        console.log('confirmed');
                    });
                    call.on("ended", function () {
                        //Cuộc gọi dừng
                        if (nhacChuong.played) {
                            nhacChuong.pause();
                        }
                        $('#status').text('Đã kết thúc cuộc gọi');
                        console.log('ended');
                        call = null;
                        setTimeout(function(){ $("#box-call-callio").hide(); }, 2000);
                        $("#answer").hide();
                    	$("#end").hide();
                    });
                    call.on("failed", function (e) {
                        //Thất bại
                        if (nhacChuong.played) {
                            nhacChuong.pause();
                        }
                        $('#status').text('Cuộc gọi thất bại');
                        console.log('failed');
                        console.log(e);
                        call = null;
                        setTimeout(function(){ $("#box-call-callio").hide(); }, 2000);
                        $("#answer").hide();
                        $("#end").hide();
                    });
                    // console.log(this.call);
                });
            }).catch(() => {
                //K có mic
                alert('Máy tính không có mic hoặc không được cấp quyền dùng mic!');
            });
        });

    </script>
</div>
-->
<?php } ?>

<?php if (Session::get('voip24h_payload')) { ?>
<div id="sipClient" style="position: fixed; bottom: 0px; right: 0px; width: 300px; z-index: 9994; display: none">
    <span onclick="document.getElementById('sipClient').style.display = 'none'" style="cursor: pointer; position: absolute; top: 0px; right: 20px">Đóng X</span>
    <iframe src="/voip24h/phone.html?v=2" id="voip24h_ifm" width="300" height="500" style="border:none;"></iframe>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
	const frameEle = document.getElementById('voip24h_ifm');
	var elements = document.getElementsByClassName("voip24h-call-customer");
    var myFunction = function() {
        let phonenumber = this.getAttribute("data-phonenumber");
        // if (this.hasAttribute("data-phonename")) {
        // 	phonenumber += '|' + this.getAttribute("data-phonename");
        // }
        frameEle.contentWindow.postMessage(phonenumber, '*');
        document.getElementById('sipClient').style.display = 'block';
    };
    for (var i = 0; i < elements.length; i++) {
        elements[i].addEventListener('click', myFunction, false);
    }
});
$(function(){
    var user = {
        "User":"<?= Session::get('voip24h_payload')['line'] ?>",
        "Pass":"<?= Session::get('voip24h_payload')['password'] ?>",
        "Realm":"<?= Session::get('voip24h_payload')['domain'] ?>",
        "Display":"<?= Session::get('voip24h_payload')['line'] ?>",
        "WSServer":"<?= Session::get('voip24h_payload')['server'] ?>"
    };
    localStorage.setItem('SIPCreds', JSON.stringify(user));
});
</script>
<?php } ?>

<!-- Left menu -->
<aside class="main-sidebar left-side" <?=Url::get('window')?'hidden':'';?> ">
   <!-- sidebar: style can be found in sidebar.less -->
   <section class="sidebar">
       <ul class="sidebar-menu" data-widget="tree">
         <li class="treeview">
            <a href="#">
              <i class="fa fa-dashboard"></i><span class="menu-title">Dashboard</span>
            </a>
             <ul class="treeview-menu">
                 <li>
                     <a href="<?=Url::build('dashboard');?>">
                         <i class="fa fa-angle-right pull-left"></i> <span>Xem Dashboard</span>
                     </a>
                 </li>
             </ul>
         </li>
         <li class="treeview">
            <a href="#"><i class="glyphicon glyphicon-shopping-cart"></i><span class="menu-title">Bán hàng</span><i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
                <li>
                    <a href="<?=Url::build('admin_orders');?>"><i class="fa fa-angle-right pull-left"></i> <span>Quản lý đơn hàng</span></a>
                </li>
                <!--IF:admin_group_cond(Session::get('admin_group'))-->
                <!--IF:cond(is_system_user() && check_system_user_permission('xuatexcel'))-->
               <li>
                  <a href="<?=Url::build('admin_orders',['cmd'=>'export_excel_system']);?>"><i class="fa fa-angle-right pull-left"></i> <span>Xuất excel theo HT</span></a>
               </li>
               <!--/IF:cond-->
               <li>
                  <a href="<?=Url::build('admin_status');?>"><i class="fa fa-angle-right pull-left"></i> <span>Quản lý Trạng thái</span></a>
               </li>
               <li>
                  <a href="<?=Url::build('admin_shipping_service');?>"><i class="fa fa-angle-right pull-left"></i> <span>Quản lý Hình thức giao hàng</span></a>
               </li>
               <li>
                  <a href="<?=Url::build('admin_source');?>"><i class="fa fa-angle-right pull-left"></i> <span>Quản lý nguồn đơn hàng</span></a>
               </li>
                <!--IF:admin_system_sources(allowAddAdminSource())-->
               <li>
                  <a href="<?=Url::build('admin_system_sources');?>"><i class="fa fa-angle-right pull-left"></i> <span>Quản lý nguồn marketing</span></a>
               </li>
                <!--/IF:admin_system_sources-->
               <li>
                  <a href="<?=Url::build('admin_group_info', ['do' => 'manager_columns_export_excel']);?>"><i class="fa fa-angle-right pull-left"></i> <span>Quản lý cột xuất excel</span></a>
               </li>
               <li>
                  <a href="<?=Url::build('print-templates');?>"><i class="fa fa-angle-right pull-left"></i> <span>Quản lý mẫu in</span></a>
               </li>
                <li>
                    <a href="<?=Url::build('admin-shipping-address');?>"><i class="fa fa-angle-right pull-left"></i> <span>Địa chỉ lấy hàng</span></a>
                </li>
                <!--/IF:admin_group_cond-->
                <!--IF:vandon_cond(Menu::$quyen_van_don)-->
                <li>
                    <a href="<?=Url::build('admin_orders&cmd=manager-shipping');?>"><i class="fa fa-angle-right pull-left"></i> <span>Đơn vận chuyển</span></a>
                </li>
                <li>
                    <a href="<?=Url::build('admin_orders',['cmd'=>'shipping-processing-v3']);?>"><i class="fa fa-angle-right pull-left"></i> <span>Tình trạng đơn vận chuyển</span></a>
                </li>
                <!--/IF:vandon_cond-->
                <!--IF:kt_cond(Menu::$quyen_ke_toan)-->
                <li>
                    <a href="<?=Url::build('import_ma_buu_dien');?>"><i class="fa fa-angle-right pull-left"></i> <span>Import mã bưu điện</span></a>
                </li>
                <!--/IF:kt_cond-->
                <!--IF:kt_cond(check_system_user_permission('tracuudonhang'))-->
                <li>
                    <a href="<?=Url::build('tracking-orders');?>"><i class="fa fa-angle-right pull-left"></i> <span>Tra cứu đơn hàng</span></a>
                </li>
                <!--/IF:kt_cond-->
            </ul>
         </li>
           <!--IF:IF:customer_cskh_cond((check_user_privilege('CUSTOMER') and Session::get('account_type') != TONG_CONG_TY and !Session::get('master_group_id')) or check_user_privilege('CSKH'))-->
           <li class="treeview">
               <a href="#"><i class="fa fa-users"></i><span class="menu-title">Khách hàng</span><i class="fa fa-angle-left pull-right"></i></a>
               <ul class="treeview-menu">
               <!--IF:customer_cond(check_user_privilege('CUSTOMER') and Session::get('account_type') != TONG_CONG_TY and !Session::get('master_group_id'))-->
                   <li>
                       <a href="<?=Url::build('customer');?>"><i class="fa fa-angle-right pull-left"></i> <span>Danh sách khách hàng</span></a>
                   </li>
                   <li>
                       <a href="<?=Url::build('customer-group');?>"><i class="fa fa-angle-right pull-left"></i> <span>Nhóm khách hàng</span></a>
                   </li>
                   <li>
                       <a href="<?=Url::build('lich-hen',['cmd'=>'today']);?>"><i class="fa fa-angle-right pull-left"></i> <span>Lịch hẹn hôm nay</span></a>
                    </li>
                    <li>
                       <a href="<?=Url::build('lich-hen');?>"><i class="fa fa-angle-right pull-left"></i> <span>Lịch hẹn</span></a>
                    </li>
                    <!--/IF:customer_cond-->
                    <li>
                       <a href="<?=Url::build('customer',['do'=>'redirect', 'destination' => "https://palion.vn/sso?ref_url=big.shopal.vn"]);?>" target="_blank"><i class="fa fa-angle-right pull-left"></i> <span>Palion CRM</span></a>
                    </li>
               </ul>
           </li>
           <!--/IF:IF:customer_cskh_cond-->
         <!--IF:mkt_cond(Session::get('admin_group') or $quyen_mkt or $quyen_admin_mkt)-->
         <li class="treeview">
            <a href="#">
            <i class="fa fa-facebook-square"></i><span class="menu-title">Facebook</span>
            <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="https://big-pages.shopal.vn/" target="_blank"><i class="fa fa-angle-right pull-left"></i> <span>QLBH pages</span></a>
               </li>
                <!--IF:mkt_cond_($quyen_admin_mkt)-->
               <li>
                  <a href="<?=Url::build('admin_fb_post');?>"><i class="fa fa-angle-right pull-left"></i> <span>Gán Fan Page cho MKT </span></a>
               </li>
                <li>
                    <a href="<?=Url::build('fb_setting');?>"><i class="fa fa-angle-right pull-left"></i> <span>Đồng bộ Pages</span></a>
                </li>
                <!--/IF:mkt_cond_-->
            </ul>
         </li>
         <!--/IF:mtk_cond-->
         <!--IF:admin_group_cond(Session::get('admin_group'))-->
         <li class="treeview">

            <a href="#">
                <i class="fa fa-th-large"></i><span class="menu-title">Sản phẩm</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <?php if([[=can_access_master_product_page=]]):?>
                <li>
                  <a href="<?=Url::build('product_admin', ['do' => 'master_product']);?>">
                    <span><i class="fa fa-barcode"></i> Sản phẩm hệ thống</span>
                  </a>
                </li>
                <?php endif;?>
                <li>
                  <a href="<?=Url::build('product_admin');?>">
                    <span><i class="fa fa-barcode"></i> DS sản phẩm</span>
                    <span class="badge bg-gray">[[|product_total|]]</span>
                  </a>
               </li>
               <li>
                  <a href="<?=Url::build('admin_products');?>">
                    <span><i class="fa fa-barcode"></i> DS sản phẩm (sửa nhanh)</span>
                    <span class="badge bg-gray">[[|product_total|]]</span>
                  </a>
               </li>
                <!--IF:admin_system_bundles(checkRoleAddBundle())-->
               <li>
                  <a href="<?=Url::build('admin_system_bundles');?>">
                    <span><i class="fa fa-barcode"></i> DS nhóm SP hệ thống cấp 1</span>
                  </a>
               </li>
               <li>
                  <a href="<?=Url::build('admin_system_bundles') . '&lv=2';?>">
                    <span><i class="fa fa-barcode"></i> DS nhóm SP hệ thống cấp 2</span>
                  </a>
               </li>
                <!--/IF:admin_system_bundles-->
               <li>
                  <a href="<?=Url::build('admin_bundles');?>">
                  <span><i class="fa fa-angle-right pull-left"></i> Phân loại</span></a>
               </li>
               <li>
                  <a href="<?=Url::build('admin_units');?>">
                  <span><i class="fa fa-angle-right pull-left"></i> Đơn vị</span></a>
               </li>
            </ul>
         </li>
         <!--/IF:admin_group_cond-->
         <li class="treeview" onclick="window.location='<?=Url::build('report');?>';">
            <a href="<?=Url::build('report');?>">
                <i class="fa fa-bar-chart-o"></i> <span class="menu-title">Xem báo cáo</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
         </li>
         <!--IF:admin_group_cond(User::is_admin() or Session::get('admin_group') or $quyen_mkt or $quyen_admin_mkt)-->
         <li class="treeview">
            <a href="#">
            <i class="fa fa-user"></i><span class="menu-title">Hồ sơ</span>
            <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
               <!--IF:admin_group_cond_(Session::get('admin_group') or $quyen_admin_mkt)-->
               <li>
                  <a href="<?=Url::build('admin_group_info');?>">
                  <span><i class="fa fa-angle-right pull-left"></i> Tùy chỉnh SHOP</span></a>
               </li>
               <!--/IF:admin_group_cond_-->
               <!--IF:admin_group_cond_(Session::get('account_type')==3 or User::is_admin())-->
               <li>
                  <a href="<?=Url::build('user_admin&cmd=shop');?>">
                  <span><i class="fa fa-angle-right pull-left"></i> Các SHOP thuộc hệ thống</span></a>
               </li>
               <!--/IF:admin_group_cond_-->
               <!--IF:admin_group_cond_(Session::get('admin_group'))-->
               <li>
                  <a href="trang-ca-nhan/">
                  <span><i class="fa fa-angle-right pull-left"></i> Trang cá nhân</span></a>
               </li>
               <!--/IF:admin_group_cond_-->
               <!--IF:admin_group_cond_(User::is_admin())-->
               <li>
                  <a href="<?=Url::build('user_admin', ['cmd' => 'password']);?>">
                    <span><i class="fa fa-angle-right pull-left"></i> Thay đổi mật khẩu</span>
                  </a>
               </li>
               <!--/IF:admin_group_cond_-->
               <!--IF:admin_group_cond_(Session::get('admin_group') or User::is_admin())-->
               <li>
                  <a href="<?=Url::build('user_admin');?>">
                    <span><i class="fa fa-angle-right pull-left"></i> Quản trị người dùng</span>
                    <span class="badge bg-gray">[[|user_total|]]</span>
                  </a>
               </li>
               <!--/IF:admin_group_cond_-->
               <!--IF:admin_group_cond_(Session::get('admin_group'))-->
               <li>
                  <a href="<?=Url::build('admin_account_group');?>">
                  <span><i class="fa fa-angle-right pull-left"></i> Khai báo đội/nhóm</span></a>
               </li>
               <!--/IF:admin_group_cond_-->
               <!--IF:admin_group_cond_(Session::get('admin_group') or User::is_admin())-->
               <li>
                  <a href="<?=Url::build('admin_roles');?>">
                  <span><i class="fa fa-angle-right pull-left"></i> Quản lý quyền</span></a>
               </li>
               <!--/IF:admin_group_cond_-->
               <!--IF:admin_group_cond_($quyen_mkt or $quyen_admin_mkt)-->
               <li>
                  <a href="<?=Url::build('adv_money');?>">
                  <span><i class="fa fa-angle-right pull-left"></i> Khai báo tiền quảng cáo</span></a>
               </li>
               <!--/IF:admin_group_cond_-->

                <!--IF:admin_group_cond_(Session::get('admin_group'))-->
                <li>
                    <a href="<?=Url::build('log',['type'=>'EXPORT_EXCEL']);?>">
                        <span><i class="fa fa-angle-right pull-left"></i> Lịch sử xuất excel</span></a>
                </li>
                <li>
                    <a href="<?=Url::build('log',['type'=>'SEND_EMAIL_TO_CARRIER']);?>">
                        <span><i class="fa fa-angle-right pull-left"></i> Lịch sử gửi mail NVC</span></a>
                </li>
                <li>
                    <a href="<?=Url::build('log',['type'=>'SEND_EMAIL_TO_WAREHOUSE']);?>">
                        <span><i class="fa fa-angle-right pull-left"></i> Lịch sử gửi mail Kho</span></a>
                </li>
                <li>
                    <a href="<?=Url::build('log',['type'=>'UPDATE_SHOP_SETTING']);?>">
                        <span><i class="fa fa-angle-right pull-left"></i> Lịch sử cập nhật tùy chỉnh shop</span></a>
                </li>
                 <?php if(User::is_admin()): ?>
                <li>
                    <a href="<?=Url::build('log',['do'=>'log_order']);?>">
                        <span><i class="fa fa-angle-right pull-left"></i> Lịch sử đơn hàng</span></a>
                </li>
                <?php endif; ?>
                <li>
                    <a href="<?=Url::build('log',['type'=>'PRINT']);?>">
                        <span><i class="fa fa-angle-right pull-left"></i> Lịch sử in đơn hàng</span></a>
                </li>
                <li>
                    <a href="<?=Url::build('log',['type'=>'DELETE']);?>">
                        <span><i class="fa fa-angle-right pull-left"></i> Lịch sử xoá đơn hàng</span></a>
                </li>
                <li>
                    <a href="<?=Url::build('login-history');?>">
                        <span><i class="fa fa-angle-right pull-left"></i> Lịch sử đăng nhập</span></a>
                </li>
                <li>
                    <a href="<?=Url::build('account-log');?>">
                        <span><i class="fa fa-angle-right pull-left"></i> Lịch sử chỉnh sửa tài khoản</span></a>
                </li>
                <!--/IF:admin_group_cond_-->
            </ul>
         </li>
         <!--/IF:admin_group_cond-->
         <!--IF:admin_kho_cond(check_user_privilege('ADMIN_KHO') )-->
         <li class="treeview">
            <a href="#">
                <i class="glyphicon glyphicon-home"></i><span class="menu-title">Kho</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
              <li>
                  <a href="<?=Url::build('qlbh_nhap_kho&cmd=add&type=IMPORT');?>">
                  <span><i class="fa fa-angle-right pull-left"></i> Nhập kho</span></a>
               </li>
               <li>
                  <a href="<?=Url::build('qlbh_xuat_kho&cmd=add&type=EXPORT');?>">
                  <span><i class="fa fa-angle-right pull-left"></i> Xuất kho</span></a>
               </li>
              <li>
                  <a href="<?=Url::build('qlbh_nhap_kho&type=IMPORT');?>">
                  <span><i class="fa fa-angle-right pull-left"></i> Danh sách phiếu nhập kho</span></a>
               </li>
              <li>
                  <a href="<?=Url::build('qlbh_xuat_kho&type=EXPORT');?>">
                  <span><i class="fa fa-angle-right pull-left"></i> Danh sách phiếu xuất kho</span></a>
               </li>
               <li>
                  <a href="<?=Url::build('qlbh_warehouse');?>">
                  <span><i class="fa fa-angle-right pull-left"></i> Khai báo kho</span></a>
               </li>
               <li>
                  <a href="<?=Url::build('admin_products')?>">
                  <span><i class="fa fa-angle-right pull-left"></i> Khai báo sản phẩm</span></a>
               </li>
               <li>
                  <a href="<?=Url::build('qlbh_supplier');?>">
                  <span><i class="fa fa-angle-right pull-left"></i> Khai báo nhà cung cấp</span></a>
               </li>
                <li>
                    <a href="<?=Url::build('qlbh_stock_report');?>">
                        <span><i class="fa fa-angle-right pull-left"></i> Báo cáo nhập xuất tồn</span></a>
                </li>
                <li>
                    <a href="<?=Url::build('qlbh_stock_report&do=store_card');?>">
                        <span><i class="fa fa-angle-right pull-left"></i> Thẻ kho</span></a>
                </li>
            </ul>
         </li>
         <!--/IF:admin_kho_cond-->
         <?php if( Session::get('group_id') == GROUP_ID_AN_NINH_SHOP || check_system_user_permission('tracuunhansu')) : ?>
         <li class="treeview">
            <a href="#">
            <i class="fa fa-shield"></i><span class="menu-title">Bảo mật</span>
            <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <?php if(check_system_user_permission('tracuunhansu') || (is_group_owner() && Session::get('group_id') == GROUP_ID_AN_NINH_SHOP) || (Session::get('admin_group') && Session::get('group_id') == GROUP_ID_AN_NINH_SHOP) ) : ?>
                    <li>
                      <a href="<?=Url::build('admin_group_info&do=list');?>">
                      <span><i class="fa fa-angle-right pull-left"></i>Danh sách</span></a>
                    </li>
                    <li>
                      <a href="<?=Url::build('admin_group_info&do=list_story');?>">
                      <span><i class="fa fa-angle-right pull-left"></i> Log lịch sử thao tác</span></a>
                    </li>
               <?php endif; ?>
               <?php if(Session::get('group_id') == GROUP_ID_AN_NINH_SHOP && !is_group_owner() && !Session::get('admin_group')) : ?>
                    <li>
                      <a href="<?=Url::build('admin_group_info&do=list');?>">
                      <span><i class="fa fa-angle-right pull-left"></i> Danh sách</span></a>
                    </li>
               <?php endif; ?>
            </ul>
         </li>
         <?php endif; ?>
         <!--IF:admin_cs_cond(check_user_privilege('CS'))-->
         <li class="treeview">
            <a href="#">
                <i class="fa fa-star-half-o"></i><span class="menu-title">Dịch vụ khách hàng</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
                <li>
                    <a href="<?=Url::build('admin_orders',['cmd'=>'care_list']);?>">
                    <span><i class="fa fa-angle-right pull-left"></i> Màn hình chính</span></a>
                </li>
                <li>
                    <a href="<?=Url::build('rating-question-template');?>">
                    <span><i class="fa fa-angle-right pull-left"></i> Danh sách câu hỏi mẫu</span></a>
                </li>
                <li>
                    <a href="<?=Url::build('bao-cao-danh-gia-sale-cskh');?>">
                    <span><i class="fa fa-angle-right pull-left"></i> Báo cáo đánh giá sale và cskh</span></a>
                </li>
            </ul>
         </li>
         <!--/IF:admin_cs_cond-->
         <li class="treeview hidden">
           <div class="target-column">
            <a href="<?=Url::build('revenue_target');?>" class="in" title="Doanh thu: [[|current_target_amount|]]đ">
              <div class="total" style="height: [[|target_height|]]px;">
              [[|target_percent|]]%
              </div>
            </a>
           </div>
         </li>
         <!--<li class="treeview">
            <a href="#">
            <i class="glyphicon glyphicon-question-sign" aria-hidden="true"></i><span class="menu-title">Hướng dẫn sử dụng</span>
            <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class="treeview-menu">
               <li>
                  <a href="https://drive.google.com/drive/folders/0B5eL3q_WZ7zzRjV1T01ONDh4SDg" target="_blank">
                  <span>HD sử dụng phần mềm</span></a>
               </li>
            </ul>
         </li>-->
         <!--IF:admin_group_cond([[=tuha_administrator=]] or [[=tuha_content_admin=]])-->
          <li class="treeview">
              <a href="#">
                  <i class="glyphicon glyphicon-cog" aria-hidden="true"></i>
                  <span class="menu-title">Tính năng quản trị</span>
                  <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                  <li>
                      <a href="/quantri/"><i class="fa fa-file-text pull-left"></i> &nbsp;<span>Quản lý bài viết</span></a>
                  </li>
                  <li>
                      <a href="<?=Url::build('news_category');?>"><i class="fa fa-file pull-left"></i> &nbsp;<span>Quản lý danh mục</span></a>
                  </li>
                  <li>
                      <a href="<?=Url::build('partner_admin');?>"><i class="fa fa-users pull-left"></i> &nbsp;<span>Quản lý đối tác</span></a>
                  </li>
                  <!--IF:admin_group_cond_([[=tuha_administrator=]])-->
                  <li>
                      <a href="<?=Url::build('setting&cmd=seo');?>"><i class="fa fa-search pull-left"></i> &nbsp;<span>Cấu hình SEO</span></a>
                  </li>
                    <!--IF:OVERLOAD_FEATURES(defined('OVERLOAD_FEATURES') && in_array(Session::get('user_id'), OVERLOAD_FEATURE_MANAGER))-->
                    <li>
                        <a href="<?=Url::build('setting',['cmd'=>'overload_feature_manager']);?>">
                            <i class="fa fa-angle-right pull-left"></i> &nbsp;<span>Bật tắt tính năng</span>
                        </a>
                    </li>
                    <!--/IF:OVERLOAD_FEATURES-->
                  <li>
                      <a href="<?=Url::build('groups_system');?>"><i class="fa fa-angle-right pull-left"></i> &nbsp;<span>Hệ thống cộng đồng</span></a>
                  </li>
                  <li>
                      <a href="<?=Url::build('admin-shop&status=1');?>"><i class="fa fa-angle-right pull-left"></i> &nbsp;<span>Shop đăng ký dùng thử</span></a>
                  </li>
                  <li>
                      <a href="<?=Url::build('admin-shop');?>"><i class="fa fa-angle-right pull-left"></i> &nbsp;<span>Danh sách shop</span></a>
                  </li>
                  <li>
                      <a href="<?=Url::build('admin-shop&cmd=manager-packages');?>"><i class="fa fa-angle-right pull-left"></i> &nbsp;<span>Danh sách gói</span></a>
                  </li>
                  <li>
                      <a href="<?=Url::build('setting');?>">
                          <i class="fa fa-angle-right pull-left"></i> <span>Cấu hình hệ thống</span>
                      </a>
                  </li>
                  <li>
                      <a href="<?=Url::build('zone_admin');?>">
                          <i class="fa fa-angle-right pull-left"></i> <span>Quản lý tỉnh thành</span>
                      </a>
                  </li>
                  <li>
                      <a href="<?=Url::build('admin-notifications');?>">
                          <i class="fa fa-angle-right pull-left"></i> <span>Quản lý thông báo</span>
                      </a>
                  </li>
                  <!--/IF:admin_group_cond_-->
                    <!--IF:admin_debug_cond(in_array(Session::get('user_id'), SYSTEM_DEBUG_ACCOUNTS))-->
                    <li>
                        <a href="<?=Url::build('dang-nhap&cmd=debug');?>">
                            <i class="fa fa-users pull-left"></i> <span>Đăng nhập tài khoản khác</span>
                        </a>
                    </li>
                  <!--/IF:admin_debug_cond-->
                  <!--IF:admin_group_cond_(Session::get('user_id') == 'admin')-->
                  <li>
                      <a href="/pull.php" target="_blank">
                          <i class="fa fa-angle-right pull-left"></i> <span>GIT - Push to Master</span>
                      </a>
                  </li>
                  <!--/IF:admin_group_cond_-->
              </ul>
          </li>
         <!--/IF:admin_group_cond-->
        <?php if(Session::get('admin_group')  || is_group_owner() || check_user_privilege('KE_TOAN')  || check_user_privilege('admin_ketoan')):?>
        <li class="treeview" onclick="window.location='<?=Url::build('admin_group_info', ['do' => 'cost_declaration', 'act' => 'list']);?>';">
            <a href="<?=Url::build('admin_group_info', ['do' => 'cost_declaration', 'act' => 'list']);?>">
                <i class="fa fa-edit"></i> <span class="menu-title">Khai báo tỷ lệ doanh thu</span>
                <i class="fa fa-angle-right pull-right"></i>
            </a>
        </li>
        <?php endif;?>
      </ul>
   </section>
   <!-- /.sidebar -->
</aside>
<!-- content right -->
<script type="text/javascript" src="packages/core/includes/js/jquery/jquery.cookie.js"></script>
<script>
    //xu ly menu nho vi tri
    $.AdminLTESidebarTweak = {};
    $.AdminLTESidebarTweak.options = {
        EnableRemember: true,
        NoTransitionAfterReload: false
        //Removes the transition after page reload.
    };
    ////
    $(function () {
        "use strict";
        $("body").on("collapsed.pushMenu", function(){
            if($.AdminLTESidebarTweak.options.EnableRemember){
                document.cookie = "toggleState=closed";
            }
        });
        $("body").on("expanded.pushMenu", function(){
            if($.AdminLTESidebarTweak.options.EnableRemember){
                document.cookie = "toggleState=opened";
            }
        });
        if($.AdminLTESidebarTweak.options.EnableRemember){
            var re = new RegExp('toggleState' + "=([^;]+)");
            var value = re.exec(document.cookie);
            var toggleState = (value != null) ? unescape(value[1]) : null;
            if(toggleState == 'closed'){
                if($.AdminLTESidebarTweak.options.NoTransitionAfterReload){
                    $("body").addClass('sidebar-collapse hold-transition').delay(100).queue(function(){
                        $(this).removeClass('hold-transition');
                    });
                }else{
                    $("body").addClass('sidebar-collapse');
                }
            }
        }
    });
    //
    var update_notification = true;
    var current_url = window.location.href;
    var page_noti_menu = 1;
    var has_data_menu = true;
    var scroll_to_bottom_menu = false;
    $(document).ready(function(){
        <!--IF:cond(Menu::$quyen_sale and (Url::get('cmd')!='edit' and Url::get('cmd')!='add'))-->
        // sau 30 phút thì báo một lần
        getUnassignOrders();
        setInterval(function(){
            getUnassignOrders();
        },1000*60*30);
        <!--/IF:cond-->
        <!--IF:cond(Menu::$new_user)-->
        setTimeout(function(){
            introJs().setOptions({'prevLabel': '<','nextLabel': '>','skipLabel':'Bỏ qua','doneLabel':'Xong'}).start();
        },1000);
        <!--/IF:cond-->
        $('.dropwdown-parent').click(function() {
          if (update_notification) {
            update_notification = false;
            $.ajax({
                url: current_url,
                type: 'POST',
                data: {
                  action: 'update_notification'
                },
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                      $('.notifications-menu .dropwdown-parent span').text(0)
                    } else {
                      update_notification = true
                    }
                },
                error: function() {
                    update_notification = true
                }
            })
          }
        })

        $('#notification-scroll').on('scroll', function() {
            if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
                if (!scroll_to_bottom_menu) {
                  scroll_to_bottom_menu = true
                  page_noti_menu += 1
                  if (has_data_menu) {
                    $.ajax({
                        url: current_url,
                        data : {
                            action: 'get_notifications',
                            page: page_noti_menu
                        },
                        dataType: 'json',
                        success: function(data) {
                            console.log(data)
                            if (data.success) {
                                if (data.html != '') {
                                    $('#notification-scroll').append(data.html)
                                } else {
                                    has_data_menu = false
                                }
                            }
                        },
                        complete: function() {
                            setTimeout(function() {
                                scroll_to_bottom_menu = false
                            }, 100)
                        }
                    });
                  }
                }
            }
        })

        <?php
        if(Session::get('group_id') and !User::can_admin(MODULE_USERADMIN,ANY_CATEGORY)){?>
            jQuery('#group_id').val(<?php echo Session::get('group_id');?>);
        <?php }
        ?>
        jQuery('#logoutBtn').click(function(){
            window.location = '<?=Url::build('sign_out')?>';
            //var win = window.open('https://admin.tuha.vn/logout?close=1','','width=1,height=1,bottom=1');
            //win.close();
            return false;
        });
        <!--IF:ep_cond(!User::is_admin() and [[=expired=]])-->
        $("#expiredModal").modal({
            backdrop: 'static',
            keyboard: false
        });
        jQuery('#expLogoutBtn').click(function(){
            window.location = '<?=Url::build('sign_out')?>';
            //var win = window.open('https://admin.tuha.vn/logout?close=1','','width=1,height=1,bottom=1');
            //win.close();
            return false;
        });
        <!--/IF:ep_cond-->
        <!--IF:ep_cond(!User::is_admin() and [[=prepairing_expired=]])-->
        var expired_cookie = $.cookie($('.expired-modal-check').attr('name'));
        if (expired_cookie && expired_cookie == "true") {
            $(this).prop('checked', expired_cookie);
            console.log('checked checkbox');
        }
        else{
            $("#prepairingExpiredModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            console.log('uncheck checkbox');
        }
        $(".expired-modal-check").change(function() {
            $.cookie($(this).attr("name"), $(this).prop('checked'), {
                path: '/',
                expires: 1
            });
        });
        <!--/IF:ep_cond-->
        <!--IF:notify_cond(!User::is_admin() and [[=notify=]])-->
        var notify_cookie = $.cookie($('.notify-modal-check').attr('name'));
        if (notify_cookie && notify_cookie == "true") {
            $(this).prop('checked', notify_cookie);
            //console.log('checked checkbox');
        }
        else{
            $("#NotifyModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            //console.log('uncheck checkbox');
        }
        $(".notify-modal-check").change(function() {
            $.cookie($(this).attr("name"), $(this).prop('checked'), {
                path: '/',
                expires: 1
            });
        });
        <!--/IF:notify_cond-->
    });
</script>
<!--IF:ep_cond(!User::is_admin() and [[=expired=]])-->
<div class="modal fade" id="expiredModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="color:#f00;">Thông báo hết hạn tài khoản</h4>
            </div>
            <div class="modal-body">
                <p>Tài khoản của quý khách sẽ hết hạn vào ngày <span style="color:#F00;font-size:20px;">[[|expired_date|]]</span>.</p>
                <p>Để gia hạn, quý khách vui lòng thực hiện liên hệ qua số điện thoại 03.9557.9557</p>
                <hr>
                <div class="text-center"><h4>Cám ơn quý khách!</h4></div>
                <br clear="all">
                <div class='modal-footer'>
                  <div class="pull-right"><p><a href="#" class="btn btn-danger" id="expLogoutBtn">Đóng</a></p></div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--/IF:ep_cond-->
<!--IF:ep_cond(!User::is_admin() and [[=prepairing_expired=]])-->
<div class="modal fade" id="prepairingExpiredModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="color:#f00;">Thông báo sắp hết hạn tài khoản</h4>
            </div>
            <div class="modal-body">
                <p>Tài khoản của quý khách sẽ hết hạn vào ngày <span style="color:#F00;font-size:20px;">[[|expired_date|]]</span>.</p>
                <p>Để gia hạn, quý khách vui lòng thực hiện liên hệ qua số điện thoại 03.9557.9557</p>
                <hr>
                <div class="text-center"><h4>Cám ơn quý khách!</h4></div>
                <br clear="all">
                <div class='modal-footer'>
                  <div class="pull-right">
                      <label><input class="expired-modal-check" name="prepairingExpiredModalCheck" type="checkbox"> Không hiển thị lần sau</label>
                      <span id="prepairingExpiredBtn" class="btn btn-danger" , data-dismiss="modal" aria-label="Close">Đóng</span>
                  </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--/IF:ep_cond-->

<!--IF:notify_cond(!User::is_admin() and [[=notify=]])-->
<div class="modal fade" id="NotifyModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="color:#f00;">Thông báo lịch bảo trì mềm QLBH </h4>
            </div>
            <div class="modal-body">
                <div class="text-center">PHẦN MỀM QLBH<BR> SẼ BẮT ĐẦU BẢO TRÌ VÀO LÚC <STRONG>23H00</STRONG> NGÀY 23/12/2018 ĐẾN <STRONG>01H00</STRONG> SÁNG NGÀY 24/12/2018</div>
                <hr>
                <div class="text-center">
                    <h4>QUÝ KHÁCH VUI LÒNG KHÔNG CẬP NHẬP DỮ LIỆU LÊN PHẦN MỀM NHẰM TRÁNH SAI SÓT KHI PHẦN MỀM ĐANG BẢO TRÌ.</h4>
                    <h4>XIN LỖI QUÝ KHÁCH VÌ SỰ BẤT TIỆN.</h4>
                    <h4>Cám ơn quý khách!</h4>
                </div>
                <br clear="all">
                <div class='modal-footer'>
                  <div class="pull-right">
                      <label><input class="notify-modal-check" name="NotifyModalCheck" type="checkbox"> Không hiển thị lần sau</label>
                      <span class="btn btn-danger" , data-dismiss="modal" aria-label="Close">Đóng</span>
                  </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!--/IF:notify_cond-->
<div class="modal fade" id="upateModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="color:#f00;">Nội dung cập nhật phần mềm </h4>
            </div>
            <div class="modal-body" style="height: 450px; overflow-y: auto;">
                <ul class="list-group">
                    <li class="list-group-item">
                        Ngày cập nhật 03/12/2018: Cho phép xóa vĩnh viễn đơn hàng với tài khoản Owner (Có quyền admin shop và là tài khoản khởi tạo shop)
                    </li>
                    <li class="list-group-item">Ngày cập nhật 26/11/2018: Cập nhật tính năng cho phép chọn marketing với
                        đơn Up Sale.
                    </li>
                    <li class="list-group-item">Ngày cập nhật 08/10/2018: Cập nhật tính năng hủy chia đơn hàng cho tài
                        khoản admin shop.
                    </li>
                    <li class="list-group-item">Ngày cập nhật 05/10/2018: Cập nhật tính năng chia đơn tự động.</li>
                    <li class="list-group-item">Ngày cập nhật 03/10/2018: Thêm
                        <a href="<?=Url::build('dashboard&do=kho_so');?>">Báo cáo kho số marketing</a>.
                    </li>
                    <li class="list-group-item">Ngày cập nhật 03/10/2018: Thêm <a
                                href="<?=Url::build('dashboard&do=kho_so&sale=1');?>">Báo cáo kho số sale</a>.
                    </li>
                    <li class="list-group-item">Ngày cập nhật 02/10/2018: Thêm <a href="<?=Url::build('dashboard&do=not_action');?>">báo
                            cáo đơn hàng <strong>chưa xử lý</strong></a>.
                    </li>
                    <li class="list-group-item">Ngày cập nhật 02/10/2018: Thêm tổng số <strong>chưa được chia</strong>
                        vào báo <a href="<?=Url::build('on_page_report');?>">cáo trực page</a>.
                    </li>
                    <li class="list-group-item">Ngày cập nhật 17/09/2018: Khi bạn tải App <strong>TUHA Boss</strong>,
                        thông báo đơn hàng thành công sẽ được gửi ngay lập tức vào app khi có cập nhật đơn hàng.
                    </li>
                    <li class="list-group-item">Ngày cập nhật 17/09/2018: Hiệu chỉnh lại tỷ lệ chốt, số được chia</li>
                    <li class="list-group-item">Ngày cập nhật 15/09/2018: Yêu cầu mật khẩu phải có độ phức tạp cao</li>
                    <li class="list-group-item">Ngày cập nhật 15/09/2018: Thêm tùy chọn tìm kiếm theo <strong>ngày
                            chia</strong>(ngày gán) đơn
                    </li>
                    <li class="list-group-item">Ngày cập nhật 13/09/2018: Cho phép ghi lại tài khoản cuối cùng sửa
                        <strong>Tùy chỉnh cột</strong> vào lúc nào
                    </li>
                    <li class="list-group-item">Ngày cập nhật 12/09/2018: Chỉ cho phép 01 tài khoản đăng nhập tại một
                        thời điểm. Khi đăng nhập vào thì tài khoản đang sử dụng sẽ bị đẩy ra
                    </li>
                </ul>
                <br clear="all">
            </div>
            <div class='modal-footer'>
                  <div class="pull-right">
                      <span class="btn btn-danger" , data-dismiss="modal" aria-label="Close">Đóng</span>
                  </div>
                </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
  $notification_popup = [[=notification_popup=]];
  if (!empty($notification_popup)) {
    $title_popup = !empty($notification_popup['title']) ? $notification_popup['title'] : "Thông báo";
?>
<div class="modal fade" id="notificationModal" role="dialog">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><?= $title_popup ?></h4>
          </div>
          <div class="modal-body">
              <?= $notification_popup['content'] ?>
              <br clear="all">
          </div>
          <div class='modal-footer'>
                <div class="pull-left">
                  <input type="checkbox" id="no-show-popup" value="1" data-notification-id="<?= $notification_popup['id'] ?>"> <label for="no-show-popup">Không hiển thị lần sau</label>
                </div>
                <div class="pull-right">
                    <span class="btn btn-default" data-dismiss="modal" aria-label="Close">Đóng</span>
                </div>
              </div>
      </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
  $('#notificationModal').modal("show");
  $('#no-show-popup').change(function() {
    $(this).attr('disabled', true)
    var self = $(this)
    if ($(this).is(":checked")) {
      let notification_id = $(this).attr('data-notification-id')
      $.ajax({
          url: current_url,
          data : {
              action: 'not_display_popup',
              notification_id: notification_id
          },
          type: 'POST',
          dataType: 'json',
          success: function(data) {
              console.log(data)
              if (!data.success) {
                  self.removeAttr('disabled')
              }
          },
          error: function() {
              self.removeAttr('disabled')
          }
      });
    }
  })
</script>
<?php
  }
?>
<script>
    var canvas = document.getElementById("canvas");
    var ctx = canvas.getContext("2d");
    var radius = canvas.height / 2;
    ctx.translate(radius, radius);
    radius = radius * 0.90
    setInterval(drawClock, 1000);

    function drawClock() {
        drawFace(ctx, radius);
        drawNumbers(ctx, radius);
        drawTime(ctx, radius);
    }

    function drawFace(ctx, radius) {
        var grad;
        ctx.beginPath();
        ctx.arc(0, 0, radius, 0, 2*Math.PI);
        ctx.fillStyle = 'yellow';
        ctx.fill();
        grad = ctx.createRadialGradient(0,0,radius*0.95, 0,0,radius*1.05);
        grad.addColorStop(0, 'red');
        grad.addColorStop(0.5, 'white');
        grad.addColorStop(1, 'white');
        ctx.strokeStyle = grad;
        ctx.lineWidth = radius*0;
//  ctx.stroke();
        ctx.beginPath();
        ctx.arc(0, 0, radius*0.1, 0, 2*Math.PI);
        ctx.fillStyle = '#333';
        ctx.fill();
    }

    function drawNumbers(ctx, radius) {
        var ang;
        var num;
        ctx.font = radius*0.3 + "px arial";
        ctx.textBaseline="middle";
        ctx.textAlign="center";
        for(num = 1; num < 13; num++){
            ang = num * Math.PI / 6;
            ctx.rotate(ang);
            ctx.translate(0, -radius*0.85);
            ctx.rotate(-ang);
            ctx.fillText(num.toString(), 0, 0);
            ctx.rotate(ang);
            ctx.translate(0, radius*0.85);
            ctx.rotate(-ang);
        }
    }

    function drawTime(ctx, radius){
        var now = new Date();
        var hour = now.getHours();
        var minute = now.getMinutes();
        var second = now.getSeconds();
        //hour
        hour=hour%12;
        hour=(hour*Math.PI/6)+
            (minute*Math.PI/(6*60))+
            (second*Math.PI/(360*60));
        drawHand(ctx, hour, radius*0.5, radius*0.07);
        //minute
        minute=(minute*Math.PI/30)+(second*Math.PI/(30*60));
        drawHand(ctx, minute, radius*0.8, radius*0.07);
        // second
        second=(second*Math.PI/30);
        drawHand(ctx, second, radius*0.9, radius*0.02);
    }

    function drawHand(ctx, pos, length, width) {
        ctx.beginPath();
        ctx.lineWidth = width;
        ctx.lineCap = "round";
        ctx.moveTo(0,0);
        ctx.rotate(pos);
        ctx.lineTo(0, -length);
        ctx.stroke();
        ctx.rotate(-pos);
    }
    function updateUserStatus(obj){
        let status = $(obj).is(":checked");
        console.log(status);
        $.ajax({
            method: "POST",
            url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
            data : {
                'do':'update_user_status',
                'status':(status==true)?2:1
            },
            dataType: 'json',
            beforeSend: function(){
            },
            success: function(content){
                var r = content;
                console.log(r);
            }
        });
    }
    function getUnassignOrders(){
        $.ajax({
            method: "POST",
            url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
            data: {
              'do': 'get_unassign_orders'
            },
            success: function (data) {
                if(data.trim()){
                    $.notify(data,{
                        'type':'warning',
                        'showProgressbar':true,
                        placement: {
                            from: "bottom",
                            align: "right"
		                }
                    });
                }
            },
            error: function() {

            }
        });
    }
</script>
