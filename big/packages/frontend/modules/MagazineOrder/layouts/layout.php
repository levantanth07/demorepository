<div id="col-md-6">
<div id="content_module">
<div class="col_left contact">
      <div class="title">
       <h2><p>Đặt tạp chí thường kỳ</p><span class="vart"></span></h2>
    </div>
      <div class="m-contact dattapchi">
          <?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
          <form name="MagazineOrder" method="post" id="form">
              <div class="success" style="display: none;">Contact form submitted!<br>
              <strong>We will be in touch soon.</strong> </div>
              <fieldset>
              <label class="form-label">Họ và tên</label>
              <input name="full_name" type="text" id="full_name" maxlength="255" />
              <br class="clear"/>
              <label class="form-label">Giới tính</label>
              <select name="gender" id="gender"></select>
              <br class="clear"/>
              <label class="form-label">Địa chỉ</label>
              <input name="address" type="text" id="address" maxlength="255" />
              <br class="clear"/>
              <label class="form-label">Quốc gia</label>
              <div>Việt Nam</div>
              <select name="country_id" id="country_id" style="display:none;"></select>
              <br class="clear"/>
              <label class="form-label">Điện thoại</label>
              <input name="phone" type="text" id="phone" maxlength="20" />
              <br class="clear"/>
              <label class="form-label">E-mail</label>
              <input name="email" type="text" id="email" maxlength="100"/>
              <br class="clear"/>
              <label class="form-label" style="width:400px">Phát hành: 1 Tháng 1 số (Giá: 22.000đ)</label>
							<div>
              		<table width="100%" border="1" cellspacing="0" cellpadding="5" bordercolor="#CCCCCC">
                    <tr bgcolor="#EFEFEF">
                      <th width="40%" align="left">Thời gian đặt</th>
                      <th width="20%" align="center">Giảm</th>
                      <th width="30%" align="center">Chỉ còn</th>
                      <th align="center">Chọn</th>
                    </tr>
                    <tr>
                      <td>6 tháng</td>
                      <td align="center">5%</td>
                      <td align="right">125.400đ</td>
                      <td align="center"><input name="select_package" type="radio" value="125400" style="width:auto;" /></td>
                    </tr>
                    <tr>
                      <td>1 Năm</td>
                      <td align="center">10%</td>
                      <td align="right">237.600đ</td>
                      <td align="center"><input name="select_package" type="radio" value="237600" style="width:auto;" /></td>
                    </tr>
                  </table>
              </div>
              </fieldset>
              <br class="clear"/>
              <fieldset>
              <legend>Thanh toán chuyển khoản <span>(Miễn phí vận chuyển trên toàn quốc!)</span></legend>
              <div>
              	<table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center" class="bank-transfer"><input type="submit" name="bank_transfer" value="Đặt ấn phẩm" class="btn" /></td>
                  <!--<td align="center" class="online-payment"><input src="assets/tgtt/images/online-payment.png" type="image" name="online_payment" value="Thanh toán trực tuyến" /><br /><span>Thanh toán trực tuyến</span></td> -->
                </tr>
              </table>
              </div>
              <br class="clear"/>
              </fieldset>
          </form>
      </div><!--End .m-contact-->
      </div><!--End .contact-->
  </div><!--End .container-->
</div><!--End .container-fix-->
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#full_name').focus();
	jQuery('#form').validate({
		rules: {
			full_name:{
				required: true
			},
			gender: {
				required: true
			},
			address: {
				required: true
			},
			country_id: {
				required: true
			},
			phone: {
				required: true
			},
			email: {
				required: true,
				email: true
			},
			select_package: {
				required:true
			}
		},
		messages: {
			full_name:{
				required: 'Yêu cầu phải nhập',
				minlength: 'Nhập tối thiểu 3 ký tự'
			},
			gender:{
				required: 'Yêu cầu phải nhập',
			},
			address:{
				required: 'Yêu cầu phải nhập'
			},
			country_id: {
				required: 'Yêu cầu phải nhập'
			},
			phone: {
				required: 'Yêu cầu phải nhập',
			},
			email: {
				required: 'Yêu cầu phải nhập',
				email: 'Phải nhập đúng định dạng email'
			},
			select_package: {
				required:'Bạn chọn hình thức đặt tạp chí'
			}
		}
	});
});
</script>