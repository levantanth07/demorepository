<style>
    .img-print-template img{
        max-width: 100%;
    }
</style>
<div class="container">
    <fieldset id="toolbar">
        <div id="toolbar-title">
            Quản lý bệnh lý
            <span>[ <?php if(Url::get('cmd')=='add'){echo 'Thêm mới';} if(Url::get('cmd')=='edit')
                {echo 'Sửa';}?> ]</span>
        </div>
        <div id="toolbar-content" align="right">
            <table align="right" style="height: 58px;">
                <tbody>
                <tr>
                    <td align="center">
                        <a class="btn btn-primary" onclick="EditCrmCustomerPathology.submit();">
                            <i class="glyphicon glyphicon-floppy-disk"></i> Ghi lại </a>
                    </td>
                    <td align="center">
                        <a class="btn btn-default" href="<?php echo Url::build('customer',array('do'=>'view', 'cid' => [[=cid=]]));?>#benhly">
                            <i class="glyphicon glyphicon-log-out"></i> Quay lại </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </fieldset>
    <br clear="all"/>
    <fieldset id="add_receive_form" class="row">
        <div class="col-md-8">
            <?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
            <div class="panel panel-info">
                <div class="panel-heading">
                    <strong>bệnh lý:</strong>
                </div>
                <div class="panel-body">
                    <form name="EditCrmCustomerPathology" id="EditCrmCustomerPathology" method="post" enctype="multipart/form-data">
                        <input name="customer_id" type="hidden" id="customer_id" class="form-control">
                        <div class="form-group">
                            <label for="note_id">Mã bệnh lý</label>
                            <input name="note_id" type="text" id="note_id" class="form-control" readonly placeholder="(auto)">
                        </div>
                        <div class="form-group">
                            <label for="received_full_name">Tên khách hàng</label>
                            <input name="customer_name" type="text" id="customer_name" readonly class="form-control" placeholder="Tên khách hàng">
                        </div>
                        <div class="form-group">
                            <label for="name">Tên bệnh</label>
                            <input name="name" type="text" id="name" class="form-control" placeholder="Tên bệnh">
                        </div>
                        <div class="form-group">
                            <label for="note">Tình trạng bệnh:</label>
                            <textarea name="note" type="text" id="note" class="form-control" rows="5"
                                      placeholder="Tình trạng bệnh bệnh"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="created_time">Thời Gian tạo</label>
                            <input name="created_time" type="text" id="created_time" readonly class="form-control">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><i class="glyphicon glyphicon-time"></i> Lịch sử</strong>
                </div>
                <div class="panel-body" style="max-height: 865px;overflow-y: auto">
                    <ul class="list-group">
                        <!--LIST:logs-->
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-12"><i class="fa fa-clock-o"></i>
                                        <?php echo date("d/m/Y H:i'", [[=logs.time=]]); ?>
                                    </div>
                                    <div class="col-md-12">
                                        <strong>[[|logs.user_id|]]</strong>
                                        <span class="small">[[|logs.description|]]</span>
                                    </div>
                                </div>
                            </li>
                        <!--/LIST:logs-->
                    </ul>
                </div>
            </div>
        </div>
    </fieldset>
</div>
<script>
    jQuery(document).ready(function(){
        // jQuery('#created_time').datetimepicker({
        //     inline: true,
        //     sideBySide: true,
        //     format: 'YYYY-MM-DD HH:mm:ss',
        //     locale : 'vi'
        // });
    });
</script>