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
                    <td id="toolbar-save"  align="center"><a onclick="EditCustomerDebitReport.submit();"> <span title="Edit"> </span> Ghi lại </a> </td>
                    <td id="toolbar-back"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'list'));?>#"> <span title="New"> </span> Quay lại </a> </td>
                </tr>
                </tbody>
            </table>
        </div>
    </fieldset>
    <br clear="all"/>
    <fieldset id="add_bill_form">
        <?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
        <ul class="nav nav-tabs" role="nav">
            <li role="presentation" class="active">
                <a data-action='type_pay' href="#">Phiếu Chi</a>
            </li>
        </ul>
        <content id="type_receive" class="active">
            <form name="EditCustomerDebitReport" id="EditCustomerDebitReport" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="exampleInputEmail1">Receive</label>
                    <input type="text" class="form-control" name="receive" value="receive" placeholder="Receive">
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Password</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                </div>
                <div class="form-group">
                    <label for="exampleInputFile">File input</label>
                    <input type="file" id="exampleInputFile">
                    <p class="help-block">Example block-level help text here.</p>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox"> Check me out
                    </label>
                </div>
            </form>
        </content>
    </fieldset>
</div>
