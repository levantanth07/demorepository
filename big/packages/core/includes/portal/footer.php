<?php if (Session::get('user_id') != 'guest'): ?>
    <style>
        .content-box-support {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            text-align: center;
            position: relative;
        }
        #box-support .content-box-support {
            margin-bottom: 10px;
        }
        .box-add-note {
            position: fixed;
            bottom: 5px;
            font-size: 18px;
            color: rgb(255, 255, 255);
            right: 55px;
            background: rgb(33, 150, 243);
            z-index: 9993;
        }
        .social i {
            line-height: 45px;
            color: #ffffff;
        }
        .content-box-support > a{
            display: block;
        }
        .social-tooltip {
            margin-bottom: 0px;
            font-size: 14px;
        }
        .social-tooltip:after {
            content: "";
            top: 9px;
            right: -7px;
            position: absolute;
            border-top: 7px solid rgba(0, 0, 0, 0);
            border-bottom: 7px solid rgba(0, 0, 0, 0);
            border-left: 7px solid rgba(0,0,0,.5);
        }
        a.btn-add-note {
            color: rgb(255, 255, 255);
            display: block;
            width: 100%;
            height: 100%;
            line-height: 60px;
        }
        a.btn-add-note i {
            line-height: 45px;
        }
        #box-add-note {
            position: fixed;
            height: 100%;
            width: 369px;
            box-shadow: 0 0 5px 0 rgba(0,0,0,0.25);
            background: rgba(255,255,255,.98);
            overflow: hidden;
            transition: opacity .5s ease;
            box-sizing: border-box;
            display: none;
            z-index: 9994;
        }
        #box-add-note.open {
            left: auto;
            right: 0;
            display: block;
            top: 0;
        }
        .box-note-content, .box-note-ajax {
            padding: 0 20px;
        }
        .box-note-title {
            box-shadow: 0 1px 0 0 rgba(0,0,0,0.03);
            border-bottom: 1px solid rgb(230, 230, 231);
            margin-bottom: 15px;
            padding: 15px 20px;
            text-align: center;
            font-size: 16px;
            font-weight: 600;
        }
        .close-note {
            float: right;
            font-size: 23px;
            font-weight: normal;
            color: rgb(153, 153, 153);
            position: relative;
            top: -5px;
        }
        .box-note-footer {
            margin-top: 15px;
        }
        #modalFeedBack .modal-header {
            background: rgb(60, 141, 188);
            color: rgb(255, 255, 255);
            text-align: center;
        }
        #modalFeedBack .modal-header h4 {
            font-size: 16px;
            font-weight: bold;
        }
        @media screen and (min-width: 768px) {
            #modalFeedBack .modal-dialog {
                width: 600px;
            }
            .ocupload-dialog .modal-dialog {
                width: 400px;
            }
        }
    </style>

    <!-- Modal -->
    <div class="modal fade" id="ResetPassPeriodic" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form role="form">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        Nhằm nâng cao vấn đề bảo mật tài khoản, quý khách vui lòng đổi mật khẩu để sử dụng mật khẩu được tốt hơn. Xin trân trọng cảm ơn
                    </div>
                    <div class="modal-footer">
                        <?php if(URL::not('trang-ca-nhan', ['cmd'=>'change_pass'])) : ?>
                        <button type="submit" class="btn btn-default" data-dismiss="modal" aria-label="Close">Bỏ qua</button>
                        <?php else : ?>
                        <button type="submit" class="btn btn-default" data-dismiss="modal" aria-label="Close" id="ResetPassPeriodicCloseBtn">Thoát</button>
                        <?php endif;?>
                        <button type="submit" class="btn btn-primary" data-dismiss="modal" aria-label="Đổi mật khẩu">Đổi mật khẩu</button>
                    </div>
                 </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function(){
            const showModalResetPassPeriodic = () => $('#ResetPassPeriodic').modal('show')
            const hideModalResetPassPeriodic = () => $('#ResetPassPeriodic').modal('hide')
            // Tự mở popup nếu chưa close
            if(<?=isset($_COOKIE['ResetPassPeriodic']) && $_COOKIE['ResetPassPeriodic'] == Portal::POPUP_SHOWING ? 'true' : 'false'?>){
                showModalResetPassPeriodic();
            }

            $('#ResetPassPeriodic').on('hidden.bs.modal', () => {
                $.cookie('ResetPassPeriodic', <?=Portal::POPUP_CLOSED?>)
            })

            $('#ResetPassPeriodicCloseBtn').click(function(){
                window.location = 'index062019.php?page=sign_out';
            });
        })
    </script>


    <div id="box-support" class="collapse" style="position: fixed; right: 30px; bottom: 50px;">
        <div class="content-box-support" style="">
            <div class="fb-message">
                <svg  xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="currentColor" class="bi bi-messenger" viewBox="0 0 16 16">
                    <path d="M0 7.76C0 3.301 3.493 0 8 0s8 3.301 8 7.76-3.493 7.76-8 7.76c-.81 0-1.586-.107-2.316-.307a.639.639 0 0 0-.427.03l-1.588.702a.64.64 0 0 1-.898-.566l-.044-1.423a.639.639 0 0 0-.215-.456C.956 12.108 0 10.092 0 7.76zm5.546-1.459-2.35 3.728c-.225.358.214.761.551.506l2.525-1.916a.48.48 0 0 1 .578-.002l1.869 1.402a1.2 1.2 0 0 0 1.735-.32l2.35-3.728c.226-.358-.214-.761-.551-.506L9.728 7.381a.48.48 0 0 1-.578.002L7.281 5.98a1.2 1.2 0 0 0-1.735.32z"/>
                </svg>
            </div>
            <!-- Messenger Plugin chat Code -->
            <div id="fb-root"></div>
            <!-- Your Plugin chat code -->
            <div id="fb-customer-chat" class="fb-customerchat">
            </div>
            <p class="social-tooltip" style="background-color: rgba(0, 0, 0, 0.5);right: 55px;bottom: 10px;color: white;padding: 5px 10px;min-width: 140px;max-width: 200px;border-radius: 5px;position: absolute;">Chat với chúng tôi</p>
        </div>
        <div class="content-box-support" style="background-color: rgb(92, 184, 92)">
            <a href="tel:<?= Portal::get_setting('hot_line') ?>" class="" >
                <span class="social"><i class="fa fa-phone"></i></span>
            </a>
            <p class="social-tooltip" style="background-color: rgba(0, 0, 0, 0.5);right: 55px;bottom: 2px;color: white;padding: 5px 10px;min-width: 140px;max-width: 200px;border-radius: 5px;position: absolute;">HOTLINE: <?= Portal::get_setting('hot_line');?></p>
        </div>
        <div class="content-box-support" style="background-color: rgb(60, 141, 188)">
            <a href="javascript:void(0)" id="b-add-feedback" class="b-add-feedback">
                <span class="social"><i class="fa fa-heart"></i></span>
            </a>
            <p class="social-tooltip" style="background-color: rgba(0, 0, 0, 0.5);right: 55px;bottom: 10px;color: white;padding: 5px 10px;min-width: 140px;max-width: 200px;border-radius: 5px;position: absolute;">Góp ý</p>
        </div>
        <div class="content-box-support" style="background-color: rgb(243, 156, 18)">
            <a href="javascript:void(0)" class="btn-add-note">
                <span class="social"><i class="fa fa-book"></i></span>
            </a>
            <p class="social-tooltip" style="background-color: rgba(0, 0, 0, 0.5);right: 55px;bottom: 10px;color: white;padding: 5px 10px;min-width: 140px;max-width: 200px;border-radius: 5px;position: absolute;">Thêm ghi chú</p>
        </div>
    </div>
    <div class="box-add-note content-box-support" data-toggle="collapse" data-target="#box-support" title="Bạn cần hỗ trợ" <?=Url::get('window')?'hidden':'';?> ">
    <a href="javascript:void(0)" class="btn-support-more">
        <span class="social"><i class="fa fa-comments"></i></span>
        <span class="social hidden"><i class="fa fa-times"></i></span>
    </a>
    <p class="social-tooltip" style="background-color: rgba(0, 0, 0, 0.5);right: 55px;bottom: 10px;color: white;padding: 5px 10px;min-width: 140px;max-width: 200px;border-radius: 5px;position: absolute;">Bạn cần hỗ trợ?</p>
    </div>

    <div id="box-add-note" class="">
        <div class="box-note-title">
            <span>Thêm ghi chú</span>
            <a aria-hidden="true" href="javascript:void(0)" class="close-note btn-close-note">&times;</a>
        </div>
        <div class="box-note-ajax text-center hidden">Đang thêm ghi chú...</div>
        <form action="" id="frmNoteAdd" method="POST">
            <div class="box-note-content">
                <div class="form-group">
                    <label for="">Tiêu đề</label>
                    <input type="text" id="ntitle" class="form-control">
                </div>
                <div class="form-group">
                    <label for="">Nội dung ghi chú (*)</label>
                    <textarea name="note" id="cknote" cols="30" rows="10" class="form-control" required></textarea>
                </div>
                <div class="box-note-footer text-right">
                    <a href="/?page=notes" target="_blank" class="btn btn-success float-left"><i class="fa fa-book"></i> Xem tất cả</a>
                    <a href="javascript:void(0)" class="btn btn-default btn-close-note">Đóng lại</a>
                    <input type="submit" class="btn-save-note btn btn-primary" value="Lưu lại">
                </div>
            </div>
        </form>
    </div>
<?php endif; ?>
<?php
$pages_disable_enter = ['admin_orders','user_admin','adv_money','customer','admin-shipping-address','product_admin','admin_products','qlbh_nhap_kho','trang-ca-nhan'];
$current_page= Url::get('page');
if (in_array($current_page,$pages_disable_enter)): ?>
    <script>
        $(document).ready(function(){
            //////////
            $('body').on('keydown', 'input, select, textarea', function(e) {
                if(e.currentTarget.id.match(/note/)){
                    return;
                }

                var self = $(this),
                    form = self.parents('form:eq(0)'),
                    submit = (self.attr('type') == 'submit' || self.attr('type') == 'button'),
                    focusable,
                    next;

                if (e.keyCode == 13 && !submit) {
                    if (event.target.classList.contains('allow-enter')) {
                        return;
                    }

                    focusable = form.find('input,a,select,button,textarea').filter(':visible:not([readonly]):not([disabled])');
                    next = focusable.eq(focusable.index(this)+1);

                    if (next.length) {
                        next.focus();
                    } else {
                        form.submit();
                    }

                    return false;
                }
            });
        });
    </script>
<?php endif; ?>
<?php if (Session::get('user_id') != 'guest'): ?>
    <script src="/assets/vissale/js/bootstrap-notify.min.js"></script>
    <link rel="stylesheet" href="/assets/vissale/bootstrap-dialog/bootstrap-dialog.min.css">
    <script src="/assets/vissale/bootstrap-dialog/bootstrap-dialog.min.js"></script>
    <script src="/assets/standard/ckeditor/ckeditor.js"></script>
    <script src="/assets/standard/ckeditor/plugins/ocupload/plugin.js"></script>
    <style>
        #box-support .fb_dialog iframe{
            bottom: 226px !important;
            right: 19px !important;
        }

        #box-support .fb_dialog {
           opacity: 0.01;;
        }
        .fb-message {
            background-color: #ffff;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 44px;
            cursor: pointer;
        }
        .fb-message svg {
            color: #0A7CFF;
        }
    </style>
    <script>
        var chatbox = document.getElementById('fb-customer-chat');
        chatbox.setAttribute("page_id", "1149509425191035");
        chatbox.setAttribute("attribution", "biz_inbox");
    </script>

    <!-- Your SDK code -->
    <?php if (!System::is_local()) { ?>
    <script>
        window.fbAsyncInit = function() {
            FB.init({
                xfbml            : true,
                version          : 'v13.0'
            });
        };

        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = 'https://connect.facebook.net/vi_VN/sdk/xfbml.customerchat.js';
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>
    <?php } ?>
    <script>
        $(function() {
            $('.btn-support-more').click(function() {
                $(this).find('.social').toggleClass('hidden');
            })
            //////////
            var editorNote = CKEDITOR.replace('cknote', {
                height: 250,
                toolbar: [{
                    name: 'colors',
                    items: ['TextColor', 'BGColor']
                }, {
                    name: 'basicstyles',
                    items: ['Bold', 'Italic', 'Underline', '-', 'RemoveFormat']
                }, {
                    name: 'paragraph',
                    items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
                }]
            });
            CKEDITOR.config.enterMode = CKEDITOR.ENTER_DIV
            editorNote.on("required", function(e) {
                editorNote.showNotification( 'Nội dung ghi chú bắt buộc phải nhập.', 'warning' );
                e.cancel();
            })

            /*var editorFeedBack = CKEDITOR.replace('feedback', {
                height: 200,
                toolbar: [{
                    name: 'colors',
                    items: ['TextColor', 'BGColor']
                }, {
                    name: 'basicstyles',
                    items: ['Bold', 'Italic', 'Underline', '-', 'RemoveFormat']
                }, {
                    name: 'paragraph',
                    items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
                }, {
                    name: 'images',
                    items: ['OcUpload', 'Maximize']
                }],
                extraPlugins: 'ocupload'
                // filebrowserUploadUrl: '/upload'
            });
            CKEDITOR.config.enterMode = CKEDITOR.ENTER_DIV
            editorFeedBack.on("required", function(e) {
                editorFeedBack.showNotification( 'Nội dung ghi chú bắt buộc phải nhập.', 'warning' );
                e.cancel();
            })*/

            $('.btn-close-note').click(function() {
                $('#cknote').val("")
                CKEDITOR.instances['cknote'].setData("")
                $('.cke_notifications_area').remove()
                $('#box-add-note').removeClass("open")
            })

            /*$('.b-add-feedback').click(function() {
                $('#feedback').val("")
            })*/

            $(".btn-add-note").click(function() {
                $("#box-add-note").addClass("open")
            })

            function checkAllowSubmitted() {
                let allow_submit = false;
                if (typeof Storage !== "undefined") {
                    if(localStorage.getItem('time-submitted') === null) {
                        allow_submit = true;
                        localStorage.setItem('time-submitted', new Date().getTime());
                    } else {
                        var time_last_submitted = localStorage.getItem('time-submitted');
                        var time_now = new Date().getTime()
                        var result = Math.abs(time_now - time_last_submitted) / 1000;
                        var minutes = Math.floor(result / 60) % 60;
                        if (minutes >= 10) {
                            allow_submit = true
                        }
                    }
                }

                return allow_submit
            }

            $('#frmNoteAdd').submit(function(e) {
                e.preventDefault()
                var original_url = window.location.origin
                var content = CKEDITOR.instances['cknote'].getData()
                if (content) {
                    $(".box-note-ajax").removeClass("hidden")
                    $(".box-note-content").addClass("hidden")
                    var ntitle = $(this).find('#ntitle').val()
                    $.ajax({
                        type: "POST",
                        url: original_url + "/index062019.php?page=notes&cmd=add_note",
                        data: {
                            content: content,
                            title: ntitle
                        },
                        dataType: "json",
                        success: function(data) {
                            if (!data.success) {
                                alert("Có lỗi xảy ra. Bạn vui lòng thử lại sau.")
                            }

                            $.notify({
                                // options
                                message: 'Thêm mới ghi chú thành công. Click nút thêm ghi chú để xem tất cả ghi chú'
                            },{
                                // settings
                                type: 'success',
                                timer: 15000
                            });
                        },
                        complete: function() {
                            $('.btn-close-note').trigger('click')
                            $(".box-note-ajax").addClass("hidden")
                            $(".box-note-content").removeClass("hidden")
                            CKEDITOR.instances['cknote'].setData('')
                        }
                    })
                }
            })

            $('#frmFeedBackAdd').submit(function(e) {
                e.preventDefault()
                if (!checkAllowSubmitted()) {
                    $.notify({
                        message: 'Bạn chỉ có thể góp ý sau lần góp ý gần nhất 10 phút.'
                    },{
                        type: 'success',
                        timer: 15000
                    });
                    $('#modalFeedBack').modal('hide')

                    return;
                }

                var original_url = window.location.origin
                // var content = CKEDITOR.instances['cknote'].getData()
                var content = CKEDITOR.instances['feedback'].getData()
                if (content) {
                    $(".box-feedback-ajax").removeClass("hidden")
                    $(".box-feedback-content").addClass("hidden")
                    $.ajax({
                        type: "POST",
                        url: original_url + "/index062019.php?page=feedback&do=add_feedback",
                        data: {
                            content: content
                        },
                        dataType: "json",
                        success: function(data) {
                            if (!data.success) {
                                alert("Có lỗi xảy ra. Bạn vui lòng thử lại sau.")
                            }

                            $.notify({
                                // options
                                message: 'Cảm ơn những góp ý của bạn. Những góp ý chân thành này sẽ giúp chúng tôi ngày càng hoàn thiện hơn để mang lại những sản phẩm có chất lượng tốt nhất đến khách hàng. Trân trọng!'
                            },{
                                // settings
                                type: 'success',
                                timer: 15000
                            });
                        },
                        complete: function() {
                            $('#modalFeedBack').modal('hide')
                            $(".box-feedback-ajax").addClass("hidden")
                            $(".box-feedback-content").removeClass("hidden")
                            CKEDITOR.instances['feedback'].setData('')
                            if (typeof Storage !== "undefined") {
                                localStorage.setItem('time-submitted', new Date().getTime());
                            }
                        }
                    })
                }
            })
        })
    </script>

<link rel="stylesheet" href="/assets/vissale/lib/feedback/feedback.css">
<script src="/assets/vissale/lib/feedback/feedback.js"></script>
<script>
    $(function() {
        $.feedback({
            ajaxURL: window.location.origin + "/index062019.php?page=feedback&do=add_feedback",
            html2canvasURL: '/assets/vissale/lib/feedback/html2canvas.js'
        });
    })
    <?php if(Session::get('chup_anh_nhan_vien') == 1) : ?>
        navigator.getUserMedia = (navigator.getUserMedia ||
        navigator.webkitGetUserMedia ||
        navigator.mozGetUserMedia ||
        navigator.msGetUserMedia);

        if (navigator.getUserMedia) {
            navigator.getUserMedia({
              video: true
            },
            function(stream) {
                var result = stream.getVideoTracks().some(function(track) {
                    return track.enabled && track.readyState === 'live';
                });
                if (result) {
                    // alert('Webcam của bạn đang được bật !');
                } else {
                    alert('Not busy');
                }
            },
            function(e) {
              alert("Bạn vui lòng cho phép phần mềm sử dụng camera vì lý do an ninh");
              window.location = '<?=Url::build('sign_out')?>';
            });
        }
        <?php
            $startTime = Session::get('start_time_login');
            $currentTime = time();
            $time = $currentTime - $startTime;
            $time = round($time / 60);
        ?>

        <?php if(Session::get('user_login')) : ?>
            Webcam.set({
                width: 320,
                height: 240,
                image_format: 'jpeg',
                jpeg_quality: 90
             });
            Webcam.attach('.my_camera');
            function b64toBlob(b64Data, contentType, sliceSize) {
                contentType = contentType || '';
                sliceSize = sliceSize || 512;
                var byteCharacters = atob(b64Data);
                var byteArrays = [];
                for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
                    var slice = byteCharacters.slice(offset, offset + sliceSize);
                    var byteNumbers = new Array(slice.length);
                    for (var i = 0; i < slice.length; i++) {
                        byteNumbers[i] = slice.charCodeAt(i);
                    }
                    var byteArray = new Uint8Array(byteNumbers);
                    byteArrays.push(byteArray);
                }
                var blob = new Blob(byteArrays, {type: contentType});
                return blob;
            }
            
            function takePhoto() {
                Webcam.snap( function(data_uri) {
                    var host_name = location.hostname;
                    var host_protocol = location.protocol;
                    var url = host_protocol+'//'+host_name;
                    var formData = new FormData();
                    var image = data_uri.split(";");
                    var contentType = image[0].split(":image/")[1];
                    var realData = image[1].split(",")[1];
                    var blob = b64toBlob(realData, contentType);
                    formData.append('image_files', blob);
                    $.ajax({
                        url : url +'/vichat-auth/webcam.php?cmd=upload_image_webcam',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function (data) {
                            location.reload();
                        },
                        complete: function() {
                            
                        }
                    })
                    
                });
            }
        <?php elseif(TIME_TAKE_PHOTO && !Session::get('user_login')): ?>
            
        <?php endif; ?>

        <?php if(Session::get('user_login')) : ?>
            setTimeout(takePhoto, 5000);
        <?php elseif(TIME_TAKE_PHOTO && !Session::get('user_login')) : ?>

        <?php endif; ?>
    <?php endif; ?>
// end takePhoto
</script>
<?php endif; ?>
</body>
</html>
