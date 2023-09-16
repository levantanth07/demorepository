<style>
img{border:none;outline:none;max-width:100%}#mdlGioiThieu .modal-lg{margin-top:0;margin-bottom:0;top:50%;transform:translateY(-50%)!important}#mdlGioiThieu .modal-body{padding:0}#mdlGioiThieu label{color:#fff;font-weight:700;margin:10px 0 5px;line-height:19px}#mdlGioiThieu .btn-submit-custom{background:linear-gradient(to right,#00DDE6,#00ADE6);width:250px;color:#fff}#mdlGioiThieu input{min-height:20px!important;height:30px;border-radius:5px;width:100%}#mdlGioiThieu .btn-close-modal{position:absolute;color:orange;font-size:2em;font-weight:700;border:none!important;background:transparent}#mdlGioiThieu img.logo-tuha{width:45px;height:45px;position:absolute;top:5px;right:35px}@media only screen and (min-width: 768px){#mdlGioiThieu .box{display:flex;flex-direction:column;background:url(/assets/standard/images/popup_mbl.jpg);background-size:cover;height:auto}#mdlGioiThieu .box-header{display:block}#mdlGioiThieu .btn-close-modal{width:auto;height:fit-content;top: 5px;right:10px}#mdlGioiThieu .modal-body{padding:0}#mdlGioiThieu .box-left,#mdlGioiThieu .box-right{width:100%;display:flex;flex-direction:column;padding:20px}#mdlGioiThieu .box-right{justify-content:flex-end;padding-top:0}}@media only screen and (min-width: 992px){#mdlGioiThieu .box-header{display:none}#mdlGioiThieu .btn-close-modal{top:0;right:5px}#mdlGioiThieu .box{background:url(/assets/standard/images/popup_nen.jpg);background-size:cover;background-position:50% 50%;position:relative;display:flex;flex-direction:row;padding-top:140px;padding-bottom:55px}#mdlGioiThieu .box-left,#mdlGioiThieu .box-right{width:50%;display:flex;flex-direction:column;padding:0 20px}#mdlGioiThieu .box-left{padding-left:20px;padding-right:40px}#mdlGioiThieu .box-right{justify-content:flex-end;margin-left:-20px;padding:0}#mdlGioiThieu .modal-lg,#mdlGioiThieu .modal-xl{width:800px}}@media only screen and (max-width: 768px){#mdlGioiThieu .box{display:flex;flex-direction:column;background:url(/assets/standard/images/popup_mbl.jpg);background-size:cover;height:auto}#mdlGioiThieu .box-header{display:block}#mdlGioiThieu .btn-close-modal{width:auto;height:fit-content;top:5px;right:10px}#mdlGioiThieu .modal-body{padding:0}#mdlGioiThieu .box-left,#mdlGioiThieu .box-right{width:100%;display:flex;flex-direction:column;padding:20px}#mdlGioiThieu .box-right{justify-content:flex-end;padding-top:0}}.gift{margin:auto;display:block}
</style>
<!--modal gioi thieu khach hang-->
<div class="modal fade" id="mdlGioiThieu" role="dialog" style="font-size: 13px;">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <form id="frmGioiThieu" name="frmGioiThieu" method="post">
                    <div class="box">
                        <button type="button" class="btn-close-modal" data-dismiss="modal">&times;</button>
                        <div class="box-left" id="popup-form-gt">
                            <div class="box-header">
                                <p style="color: white;font-weight: bold;font-size: 15px;text-align: center;padding-top: 15px;">
                                    Chương trình
                                    <br>
                                    "KHÁCH HÀNG GIỚI THIỆU KHÁCH HÀNG"
                                </p>
                                <img class="gift" src="assets/standard/images/30tr.png">
                                <img class="logo-tuha" src="assets/standard/images/logo_tuha.png">
                            </div>

                            <div class="form-group-sm">
                                <label>Tên người sử dụng / Shop ID <span>*</span></label>
                                <input class="form-control" id="NguoiGioiThieu" name="NguoiGioiThieu">
                            </div>

                            <div class="form-group-sm">
                                <label>SĐT người giới thiệu <span>*</span></label>
                                <input class="form-control" id="SDTNguoiGioiThieu" name="SDTNguoiGioiThieu"
                                       type="text" onkeyup="check(this)">
                            </div>
                            <div class="form-group-sm">
                                <label>Tên người được giới thiệu <span>*</span></label>
                                <input class="form-control" id="NguoiDuocGioiThieu" name="NguoiDuocGioiThieu"
                                       type="text">
                            </div>
                            <div class="form-group-sm">
                                <label>SĐT được giới thiệu <span>*</span></label>
                                <input class="form-control" id="SDTNguoiDuocGioiThieu" name="SDTNguoiDuocGioiThieu"
                                       type="text" onkeyup="check(this)">
                            </div>

                        </div>
                        <div class="box-right">
                            <button type="submit" name="submit" id="submit_gtkh" class="btn btn-lg btn-submit-custom">
                                Giới thiệu ngay
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
<!--end modal gioi thieu khach hang-->

<script type="text/javascript">
    jQuery(document).ready(function(e) {
        jQuery('#user_id').focus();
        $('.ls-modal').on('click', function(e){
            window.open($(this).attr('href'),'register','width=600,height=600,top=0,right=0');
            e.preventDefault();
        });
    });
</script>

<script type="text/javascript">
    function check(el){
        el.value = el.value.replace(/\D/g, '');
    }
    //$('#mdlGioiThieu').modal();
    $('#frmGioiThieu').on('submit', function(e){
        e.preventDefault();
        var txtTenShop = $('input[name="NguoiGioiThieu"]').val();
        var txtSDTNguoiGioiThieu = $('input[name="SDTNguoiGioiThieu"]').val();
        var txtTenNguoiDuocGioiThieu = $('input[name="NguoiDuocGioiThieu"]').val();
        var txtSDTNguoiDuocGioiThieu = $('input[name="SDTNguoiDuocGioiThieu"]').val();
        var isMobile = false; //initiate as false
    // device detection
        if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
            || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) {
            isMobile = true;
        }
        if(txtTenShop.trim().length == 0){
            alert('Bạn chưa điền "Tên người sử dụng / tên Shop Id"!');
            txtTenShop.focus;
            return false;
        } else if(txtSDTNguoiGioiThieu.trim().length == 0){
            alert('Bạn chưa điền "SDT người giới thiệu"!');
            txtSDTNguoiGioiThieu.focus;
            return false;
        } else if(txtTenNguoiDuocGioiThieu.trim().length == 0){
            alert('Bạn chưa điền "Tên người được giới thiệu"!');
            txtTenNguoiDuocGioiThieu.focus;
            return false;
        } else if(txtSDTNguoiDuocGioiThieu.trim().length == 0){
            alert('Bạn chưa điền "SDT được giới thiệu"!');
            txtSDTNguoiDuocGioiThieu.focus;
            return false;
        }
        var vnf_regex = /^((09|03|07|08|05|02)+([0-9]{8,9})\b)$/g;
        if (vnf_regex.test(txtSDTNguoiGioiThieu) == false) {
            alert('Số điện thoại người giới thiệu không đúng định dạng!');
            txtSDTNguoiGioiThieu.focus;
            return false;
        }

        var vnf_regex1 = /^((09|03|07|08|05|02)+([0-9]{8,9})\b)$/g;
        if (vnf_regex1.test(txtSDTNguoiDuocGioiThieu) == false) {
            alert('Số điện thoại người được giới thiệu không đúng định dạng!');
            txtSDTNguoiDuocGioiThieu.focus;
            return false;
        }

        if (txtSDTNguoiGioiThieu == txtSDTNguoiDuocGioiThieu) {
            alert('Số điện thoại người giới thiệu và người được giới thiệu không được trùng nhau!');
            txtSDTNguoiDuocGioiThieu.focus;
            return false;
        }
  
        $('button[id="submit_gtkh"]').attr('disabled', true);
        $('button[id="submit_gtkh"]').text('Vui lòng đợi...');
        var form = document.forms['frmGioiThieu'];
        $.ajax({
            url: "https://script.google.com/macros/s/AKfycbxAR0hW6XVHq9GXd3wbG1OrBDJuMSJwR1C2JtQXr0V5HQfnjiGcaq6Ef-QXpfzrhWqq/exec",
            method: "POST",
            dataType: "json",
            data: {
                'NguoiGioiThieu': txtTenShop,
                'SDTNguoiGioiThieu': txtSDTNguoiGioiThieu,
                'NguoiDuocGioiThieu': txtTenNguoiDuocGioiThieu,
                'SDTNguoiDuocGioiThieu': txtSDTNguoiDuocGioiThieu,
            },
            success: function (data) {
                alert('Giới thiệu khách hàng thành công!');
                $('#mdlGioiThieu').modal('hide');
                // $('button[name="submit"]').attr('disabled', false);
                $('button[name="submit"]').text('Giới thiệu ngay');

            }
        });

    });
</script>