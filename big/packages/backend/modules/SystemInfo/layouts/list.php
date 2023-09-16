<div class="container">
    <br>
    <div class="box">
        <div class="box-header">
            <h3>Thông tin hệ thống</h3>
        </div>
        <div class="box-body">
            <div id="SystemInfo" align="center">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#system_info"><span>[[.system_info.]]</span></a></li>
                    <li><a data-toggle="tab" href="#PHP_core"><span>[[.PHP_core.]]</span></a></li>
                    <li><a data-toggle="tab" href="#PHP_Variables"><span>PHP Variables</span></a></li>
                    <li><a data-toggle="tab" href="#apache2handler"><span>[[.apache2handler.]]</span></a></li>
                    <li><a data-toggle="tab" href="#Apache_environment"><span>[[.Apache_environment.]]</span></a></li>
                    <li><a data-toggle="tab" href="#gd"><span>[[.Graph_driver.]]</span></a></li>
                    <li><a data-toggle="tab" href="#mysql"><span>[[.mysql.]]</span></a></li>
                    <li><a data-toggle="tab" href="#session"><span>[[.session.]]</span></a></li>
                </ul>
                <div class="tab-content">
                    <div id="system_info" class="tab-pane fade in active">
                        <table class="table table-bordered table-striped">
                            <tr >
                                <td width="27%" align="left">PHP Version</td>
                                <td width="73%" align="left"><?php echo phpversion();?></td>
                            </tr>
                            <tr>
                                <td align="left">Zend Version</td>
                                <td align="left"><?php echo zend_version();?></td>
                            </tr>
                            <tr >
                                <td align="left">Client Browser</td>
                                <td align="left"><?php echo $_SERVER['HTTP_USER_AGENT'];?></td>
                            </tr>
                            <tr >
                                <td align="left">Server Name</td>
                                <td align="left"><?php echo $_SERVER['SERVER_NAME'];?></td>
                            </tr>
                            <tr >
                                <td align="left">Mysql Server Info</td>
                                <td align="left"><?php echo DB::get_server_info();?></td>
                            </tr>
                            <tr >
                                <td align="left">GD2 Library</td>
                                <td align="left"><?php $gd2 = gd_info();echo $gd2['GD Version'];?></td>
                            </tr>
                            <tr >
                                <td align="left">Server IP</td>
                                <td align="left"><?php echo gethostbyname($_SERVER['HTTP_HOST']);?></td>
                            </tr>
                            <tr >
                                <td align="left">Client IP</td>
                                <td align="left"><?php echo gethostbyname($_SERVER['REMOTE_ADDR']);?></td>
                            </tr>
                        </table>
                    </div>
                    <div id="PHP_core" class="tab-pane fade">
                        <table class="table table-bordered table-striped">
                            <?php foreach($this->map['system_info']['Core'] as $key=>$value){?>
                                <tr >
                                    <td width="27%" align="left"><?php echo $key;?></td>
                                    <td width="73%" align="left"><?php if(is_array($value)){System::debug($value);}else{echo $value;};?></td>
                                </tr>
                            <?php }?>
                        </table>
                    </div>
                    <div id="PHP_Variables" class="tab-pane fade">
                        <table class="table table-bordered table-striped">
                            <?php foreach($this->map['system_info']['PHP Variables'] as $key=>$value){?>
                                <tr >
                                    <td width="27%" align="left"><?php echo $key;?></td>
                                    <td width="73%" align="left"><?php echo $value;?></td>
                                </tr>
                            <?php }?>
                        </table>
                    </div>
                    <div id="apache2handler" class="tab-pane fade">
                        <table class="table table-bordered table-striped">
                            <?php foreach($this->map['system_info']['apache2handler'] as $key=>$value){?>
                                <tr >
                                    <td width="27%" align="left"><?php echo $key;?></td>
                                    <td width="73%" align="left"><?php echo $value;?></td>
                                </tr>
                            <?php }?>
                        </table>
                    </div>
                    <div id="Apache_environment" class="tab-pane fade">
                        <table class="table table-bordered table-striped">
                            <?php foreach($this->map['system_info']['Apache Environment'] as $key=>$value){?>
                                <tr >
                                    <td width="27%" align="left"><?php echo $key;?></td>
                                    <td width="73%" align="left"><?php echo $value;?></td>
                                </tr>
                            <?php }?>
                        </table>
                    </div>
                    <div id="gd" class="tab-pane fade">
                        <table class="table table-bordered table-striped">
                            <?php foreach($this->map['system_info']['gd'] as $key=>$value){?>
                                <tr >
                                    <td width="27%" align="left"><?php echo $key;?></td>
                                    <td width="73%" align="left"><?php echo $value;?></td>
                                </tr>
                            <?php }?>
                        </table>
                    </div>
                    <div id="mysql" class="tab-pane fade">
                        <table class="table table-bordered table-striped">
                            <?php foreach($this->map['system_info']['mysqli'] as $key=>$value){?>
                                <tr >
                                    <td width="27%" align="left"><?php echo $key;?></td>
                                    <td width="73%" align="left"><?php echo $value;?></td>
                                </tr>
                            <?php }?>
                        </table>
                    </div>
                    <div id="session" class="tab-pane fade">
                        <table class="table table-bordered table-striped">
                            <?php foreach($this->map['system_info']['session'] as $key=>$value){?>
                                <tr >
                                    <td width="27%" align="left"><?php echo $key;?></td>
                                    <td width="73%" align="left"><?php echo $value;?></td>
                                </tr>
                            <?php }?>
                        </table>
                    </div>
                </div>
            </div>
            <h3>Run Query</h3>
            <a name="query"></a>
            <div class="row">
                <div class="col-xs-8">
                    <textarea name="sql" id="sql" class="form-control t-text-area" rows="10" placeholder="input your SQL" style="font-size:14px;color:#008c21"></textarea>
                </div>
                <div class="col-xs-4">
                    <button type="button" class="btn btn-lg btn-success" onclick="runQuery();">Run</button>
                    <hr>
                    <div class="small italic text-warning">
                        Nghĩ kỹ trước khi ấn nhé!
                    </div>
                    <div class="box box-default box-solid">
                        <div class="box-header">
                            Mẫu query
                        </div>
                        <div class="box-body">
                            <button type="button" class="btn btn-default btn-sm" onclick="$('#sql').val('SELECT * FROM TABLE_NAME WHERE 1=1')">SELECT</button>
                            <button type="button" class="btn btn-default btn-sm" onclick="$('#sql').val('UPDAET `TABLE_NAME` SET FIELD_NAME=\' \' WHERE 1=1')">UPDATE</button>
                            <button type="button" class="btn btn-default btn-sm" onclick="$('#sql').val('INSERT INTO `TABLE_NAME` (`id`) VALUES (\'\')')">INSERT</button>
                            <button type="button" class="btn btn-default btn-sm" onclick="$('#sql').val('DELETE FROM `TABLE_NAME` WHERE 1=1');">DELETE</button>
                        </div>
                        <div class="box-footer">
                            <a href="https://www.regextester.com/" target="_blank">Test Regular Expression</a>
                        </div>
                    </div>
                </div>
            </div>
            <h4>Kết quả query</h4>
            <div id="queryResult" style="background: #fff;overflow: auto;"></div>
        </div>
    </div>
</div>
<script>
    function runQuery(){
        $.ajax({
            type: "POST",
            url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
            data : {
                'do':'run_query',
                "sql" : $('#sql').val()
            },
            beforeSend: function(){
                $('#queryResult').html('Đang xử lý...');
            },
            success: function(data) {
                $('#queryResult').html(data);
            },
            complete: function() {

            }
        });
    }
</script>