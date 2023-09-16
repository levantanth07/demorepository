<?php
    $title = 'Khai báo kho';System::set_page_title($title);
    $owner = is_group_owner();
    $idWarehouse = Url::get('id');
    $isEdit = Url::get('cmd');
    $email = [[=item=]];
?>
<div class="container"><br>
    <div class="box box-info">
        <form name="EditTourForm" method="post">
            <div class="box-header">
                <h3 class="box-title"><?php echo $email['title']??'Thêm kho mới'; ?> </h3>
                <div class="box-tools">
                    <?php if(Session::get('admin_group')){?><input name="save" type="submit" value="Lưu" class="btn btn-primary"><?php }?>
                    <?php if(Session::get('admin_group')){?><a href="<?php echo Url::build_current();?>"  class="btn btn-danger">Quay lại</a><?php }?>
                </div>
            </div>
            <div class="box-body">
                <?php if(Form::$current->is_error()){?><div><br><?php echo Form::$current->error_messages();?></div><?php }?>
                <br />
                <table class="table">
                    <tr>
                        <td>Tên kho(*):</td>
                        <td>
                            <?php if ($idWarehouse == 1 && $isEdit === 'edit'): ?>
                                <input name="name" type="text" id="name" class="form-control" readonly="">
                            <?php else: ?>
                                <input name="name" type="text" id="name" class="form-control">
                            <?php endif; ?>
                            
                        </td>
                        <?php if ($owner): ?>
                            <td>Nhập Email gửi file In đơn:</td>
                            <td><input name="email" type="email" id="email" class="form-control" value="<?php echo $email['email'] ?>"></td>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <td><label for="is_default">Kho chính (kho bán hàng):</label></td>
                        <td><input name="is_default" type="checkbox" id="is_default"></td>
                        <script type="text/javascript">
                            <?php if(Url::get('is_default')){?>
                            getId('is_default').checked = true;
                            <?php }?>
                        </script>
                    </tr>
                </table>
            </div>
        </form>
    </div>
</div>