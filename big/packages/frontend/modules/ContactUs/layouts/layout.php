<?php
$page = (Portal::language() == 1)?'lien-he':'contact-us';
?>
<section id="blog-detail" class="container">
    <div class="col-sm-12">
        <ol class="breadcrumb">
            <li><a href="" title="Trang chủ"><span class="glyphicon glyphicon-home" aria-hidden="true"></span></a></li>
            <li><a href="<?php echo $page.'/';?>">[[.contact_us.]]</a></li>
        </ol>
    </div>
	<div class="center">
      <h1>[[.contact_us.]]</h1>
  </div>
    <div class="col-sm-12">
        <div class="gmap-area">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6 text-center">
                        <div class="gmap">
                            <iframe style="border: 0;" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d7448.439450252362!2d105.8208766!3d21.0238925!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab704f418df7%3A0xbddd4993e1bb7a53!2zVMOyYSBuaMOgIEFydGV4LCAxNzIgTmfhu41jIEtow6FuaCwgR2nhuqNuZyBWw7UsIEJhIMSQw6xuaCwgSMOgIE7hu5lpLCBWaWV0bmFt!5e0!3m2!1sen!2s!4v1435935222976" frameborder="0"></iframe>
                        </div>
                    </div>
                    <div class="col-sm-6 map-content">
                        <ul class="row">
                            <li>
                                <address>
                                    <h5>[[.head_office.]]</h5>
                                    <p><?php echo Portal::get_setting('company_name_'.Portal::language());?><br>
                                        <?php echo Portal::get_setting('company_address_'.Portal::language());?></p>
                                    <p>[[.phone.]]: <?php echo Portal::get_setting('company_phone_bac');?> <br>
                                        Email: <?php echo Portal::get_setting('company_email');?></p>
                                </address>
                            </li>
                            <li class="hide">
                                <address>
                                    <h5>[[.brand_office.]]</h5>
                                    <p><?php echo Portal::get_setting('company_name_'.Portal::language());?><br><?php echo Portal::get_setting('company_address_nam_'.Portal::language());?></p>
                                    <p>[[.phone.]]: <?php echo Portal::get_setting('company_phone_nam');?> <br>
                                        Email: <?php echo Portal::get_setting('company_email_nam');?></p>
                                </address>
                            </li>
                            <li>
                                <p class="lead"><?php echo Portal::get_setting('contact_text');?></p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section id="contact-page">
      <div class="container">
          <div class="center">
              <h2>[[.send_contact_information.]]</h2>
              <p class="lead"></p>
          </div>
          <div class="row contact-wrap">
              <div class="status alert alert-success" style="display: none"></div>
              <form name="ContactUs" method="post" id="form">
                  <div class="col-sm-5 col-sm-offset-1">
                  		<div class="error"><?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?></div>
                      <div class="form-group">
                          <input name="full_name" type="text" id="full_name" maxlength="255" placeholder="[[.full_name.]]" class="form-control" title="[[.full_name.]]" required />
                      </div>
                      <div class="form-group">
                          <input name="email" type="email" id="email" class="form-control" required="required" placeholder="Email" title="[[.input_email.]]">
                      </div>
                      <div class="form-group">
                          <input name="phone" type="text" id="phone" maxlength="20" placeholder="[[.phone.]]" class="form-control" title="[[.input_phone.]]" required/>
                      </div>
                      <div class="form-group">
                          <input name="address" type="text" id="address" maxlength="255" placeholder="[[.address.]]" title="[[.input_address.]]" class="form-control" required/>
                      </div>
                      <div>
                        <label style="position: relative;">[[.captcha.]] *</label>
                         <?php include("captcha/captcha.php");
$_SESSION['captcha'] = simple_php_captcha();?><img src="<?php echo $_SESSION['captcha']['image_src'];?>" height="30" alt="Captcha"></a>
                          <input name="captcha" type="text" id="captcha" maxlength="5" placeholder="[[.captcha.]]" class="form-control" required />
                      </div>
                  </div>
                  <div class="col-sm-5">
                      <div class="form-group">
                          <label>[[.content.]] *</label>
                          <textarea name="content" id="content" required class="form-control" rows="11"></textarea>
                      </div>
                      <div class="form-group">
                          <button type="submit" name="submit" class="btn btn-primary btn-lg" required="required">[[.submit.]]</button>
                      </div>
                  </div>
              </form>
          </div><!--/.row-->
      </div><!--/.container-->
  </section><!--/#contact-page-->
</section>  