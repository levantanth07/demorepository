<script type="text/javascript">
	var blockId = <?php echo Module::block_id(); ?>;
</script>
<link rel="stylesheet" type="text/css" href="assets/vissale/css/app.css?d=08062022">
<div class="container">
    <br>
    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title"><i class="fa fa-key" aria-hidden="true"></i> Đổi mật khẩu</h3>
            <div class="box-tools pull-right">
                <a class="btn btn-success" href="<?php echo Url::build_current();?>" title="Thông tin tài khoản"> <span></span>Thông tin tài khoản</a>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-xs-2 col-md-4"></div>
                <div class="col-xs-8 col-md-4" style="background: #fff;padding:20px;">
                    <?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
                    <form  method="post" name="ChangePassword" id="ChangePassword">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Mật khẩu đang sử dụng</label>
                                <div class="input-password">
                                <input name="old_password" type="password" id="old_password" class="form-control">
                                <i class="icon-eye icon-right">
                                    <svg viewBox="64 64 896 896" focusable="false" fill="currentColor" width="1em" height="1em" data-icon="lock" aria-hidden="true">
                                        <defs><clipPath><path fill="none" d="M124-288l388-672 388 672H124z" clip-rule="evenodd"/></clipPath></defs><path d="M508 624a112 112 0 0 0 112-112c0-3.28-.15-6.53-.43-9.74L498.26 623.57c3.21.28 6.45.43 9.74.43zm370.72-458.44L836 122.88a8 8 0 0 0-11.31 0L715.37 232.23Q624.91 186 512 186q-288.3 0-430.2 300.3a60.3 60.3 0 0 0 0 51.5q56.7 119.43 136.55 191.45L112.56 835a8 8 0 0 0 0 11.31L155.25 889a8 8 0 0 0 11.31 0l712.16-712.12a8 8 0 0 0 0-11.32zM332 512a176 176 0 0 1 258.88-155.28l-48.62 48.62a112.08 112.08 0 0 0-140.92 140.92l-48.62 48.62A175.09 175.09 0 0 1 332 512z"/><path d="M942.2 486.2Q889.4 375 816.51 304.85L672.37 449A176.08 176.08 0 0 1 445 676.37L322.74 798.63Q407.82 838 512 838q288.3 0 430.2-300.3a60.29 60.29 0 0 0 0-51.5z"/>
                                    </svg>
                                </i>
                                <i class="icon-eye hide icon-right">
                                    <svg viewBox="64 64 896 896" focusable="false" fill="currentColor" width="1em" height="1em" data-icon="lock" aria-hidden="true">
                                        <path d="M396 512a112 112 0 1 0 224 0 112 112 0 1 0-224 0zm546.2-25.8C847.4 286.5 704.1 186 512 186c-192.2 0-335.4 100.5-430.2 300.3a60.3 60.3 0 0 0 0 51.5C176.6 737.5 319.9 838 512 838c192.2 0 335.4-100.5 430.2-300.3 7.7-16.2 7.7-35 0-51.5zM508 688c-97.2 0-176-78.8-176-176s78.8-176 176-176 176 78.8 176 176-78.8 176-176 176z"/>
                                    </svg>
                                </i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">Mật khẩu mới</label>
                            <td width=63% align=left>
                                <div class="input-password">
                                    <input  name="new_password" type="password" id="new_password" class="form-control" onkeyup="getPasswordStrength(blockId,this.value);">
                                    <i class="icon-eye icon-right">
                                        <svg viewBox="64 64 896 896" focusable="false" fill="currentColor" width="1em" height="1em" data-icon="lock" aria-hidden="true">
                                            <defs><clipPath><path fill="none" d="M124-288l388-672 388 672H124z" clip-rule="evenodd"/></clipPath></defs><path d="M508 624a112 112 0 0 0 112-112c0-3.28-.15-6.53-.43-9.74L498.26 623.57c3.21.28 6.45.43 9.74.43zm370.72-458.44L836 122.88a8 8 0 0 0-11.31 0L715.37 232.23Q624.91 186 512 186q-288.3 0-430.2 300.3a60.3 60.3 0 0 0 0 51.5q56.7 119.43 136.55 191.45L112.56 835a8 8 0 0 0 0 11.31L155.25 889a8 8 0 0 0 11.31 0l712.16-712.12a8 8 0 0 0 0-11.32zM332 512a176 176 0 0 1 258.88-155.28l-48.62 48.62a112.08 112.08 0 0 0-140.92 140.92l-48.62 48.62A175.09 175.09 0 0 1 332 512z"/><path d="M942.2 486.2Q889.4 375 816.51 304.85L672.37 449A176.08 176.08 0 0 1 445 676.37L322.74 798.63Q407.82 838 512 838q288.3 0 430.2-300.3a60.29 60.29 0 0 0 0-51.5z"/>
                                        </svg>
                                    </i>
                                    <i class="icon-eye hide icon-right">
                                        <svg viewBox="64 64 896 896" focusable="false" fill="currentColor" width="1em" height="1em" data-icon="lock" aria-hidden="true">
                                            <path d="M396 512a112 112 0 1 0 224 0 112 112 0 1 0-224 0zm546.2-25.8C847.4 286.5 704.1 186 512 186c-192.2 0-335.4 100.5-430.2 300.3a60.3 60.3 0 0 0 0 51.5C176.6 737.5 319.9 838 512 838c192.2 0 335.4-100.5 430.2-300.3 7.7-16.2 7.7-35 0-51.5zM508 688c-97.2 0-176-78.8-176-176s78.8-176 176-176 176 78.8 176 176-78.8 176-176 176z"/>
                                        </svg>
                                    </i>
                                </div>
                                <div class="password-strength-bar-container">
                                    <div id="passwordStrengthBar"></div>
                                    <div id="passwordStrengthLabel"></div>
                                </div>
                            </td>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">Nhập lại mật khẩu mới</label>
                            <div class="input-password">
                                <input  name="retype_new_password" type="password" id="retype_new_password" class="form-control">
                                <i class="icon-eye icon-right">
                                    <svg viewBox="64 64 896 896" focusable="false" fill="currentColor" width="1em" height="1em" data-icon="lock" aria-hidden="true">
                                        <defs><clipPath><path fill="none" d="M124-288l388-672 388 672H124z" clip-rule="evenodd"/></clipPath></defs><path d="M508 624a112 112 0 0 0 112-112c0-3.28-.15-6.53-.43-9.74L498.26 623.57c3.21.28 6.45.43 9.74.43zm370.72-458.44L836 122.88a8 8 0 0 0-11.31 0L715.37 232.23Q624.91 186 512 186q-288.3 0-430.2 300.3a60.3 60.3 0 0 0 0 51.5q56.7 119.43 136.55 191.45L112.56 835a8 8 0 0 0 0 11.31L155.25 889a8 8 0 0 0 11.31 0l712.16-712.12a8 8 0 0 0 0-11.32zM332 512a176 176 0 0 1 258.88-155.28l-48.62 48.62a112.08 112.08 0 0 0-140.92 140.92l-48.62 48.62A175.09 175.09 0 0 1 332 512z"/><path d="M942.2 486.2Q889.4 375 816.51 304.85L672.37 449A176.08 176.08 0 0 1 445 676.37L322.74 798.63Q407.82 838 512 838q288.3 0 430.2-300.3a60.29 60.29 0 0 0 0-51.5z"/>
                                    </svg>
                                </i>
                                <i class="icon-eye hide icon-right">
                                    <svg viewBox="64 64 896 896" focusable="false" fill="currentColor" width="1em" height="1em" data-icon="lock" aria-hidden="true">
                                        <path d="M396 512a112 112 0 1 0 224 0 112 112 0 1 0-224 0zm546.2-25.8C847.4 286.5 704.1 186 512 186c-192.2 0-335.4 100.5-430.2 300.3a60.3 60.3 0 0 0 0 51.5C176.6 737.5 319.9 838 512 838c192.2 0 335.4-100.5 430.2-300.3 7.7-16.2 7.7-35 0-51.5zM508 688c-97.2 0-176-78.8-176-176s78.8-176 176-176 176 78.8 176 176-78.8 176-176 176z"/>
                                    </svg>
                                </i>
                            </div>
                        </div>
                        <span class="text-danger small">Mật khẩu phải có ít nhất 6 kí tự, bao gồm <strong>Số + chữ hoa + chữ thường + ký tự đặc  biệt</strong> mới có thể cập nhật được</span>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary" id="updateButton"> <i class="fa fa-floppy-o"></i> Cập nhật</button>
                        </div>
                    </form>
                </div>
                <div class="col-xs-2 col-md-4"></div>
            </div>
        </div>
    </div>
</div>
<script src="assets/vissale/js/app.js?d=08062022"></script>
<script type="text/javascript">
	jQuery(document).ready(function(){
		$(document).keypress(function(e) {
        if(e.which == 13) {
            return false;
        }
    });
	});

    const getPasswordStrength = _.debounce(_getPasswordStrength, 200);
    
	function _getPasswordStrength(blockId,password){
		jQuery.ajax({
            method: "POST",
            url: 'form.php',
            data : {
                'cmd':'get_password_length',
                'password':password,
                'username': "<?=Session::get('user_id')?>",
                block_id:blockId
            },
            beforeSend: function(){

            },
            success: function(content){
                var strength = parseInt(content) || 0;
                const labels = ['Quá yếu', 'Yếu', 'Trung bình', 'An toàn', 'Rất an toàn'];
                jQuery('#passwordStrengthBar').css({'width': 25 * strength + '%'});
                jQuery('#passwordStrengthLabel').html(labels[strength]);
                jQuery('#updateButton').attr('disabled', strength < 3);
            },
            error: function(){
                alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
            }
        });
	}
</script>