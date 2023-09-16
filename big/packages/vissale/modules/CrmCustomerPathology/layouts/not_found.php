<div class="container">
    <style>
        .img-print-template{
        }
        .img-print-template img{
            max-width: 100%;
        }
    </style>
    <fieldset id="toolbar">
        <div id="toolbar-title">
            Quản lý Thu-Chi
            <span>[ <?php if(Url::get('cmd')=='add'){echo 'Thêm mới';} if(Url::get('cmd')=='edit')
                {echo 'Sửa';}?> ]</span>
        </div>
        <div id="toolbar-content" align="right">
            <table align="right">
                <tbody>
                <tr>
                    <td id="toolbar-save"  align="center"><a onclick="EditCrmCustomerPathology.submit();"> <span title="Edit"> </span> Ghi lại </a> </td>
                    <td id="toolbar-back"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'list'));?>#"> <span title="New"> </span> Quay lại </a> </td>
                </tr>
                </tbody>
            </table>
        </div>
    </fieldset>
    <br clear="all"/>
    <fieldset id="add_receive_form">
        <?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
        <div class="panel panel-success">
            <div class="panel-heading">
                <strong>Lập Phiếu Chi</strong>
            </div>
            <div class="panel-body">
                Không tìm thấy kết quả
            </div>
        </div>
    </fieldset>
</div>

<script>
    jQuery(document).ready(function(){
        jQuery('#bill_date').datepicker({format:'dd/mm/yyyy',language:'vi'});
    });
</script>