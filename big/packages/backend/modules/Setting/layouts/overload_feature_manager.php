<style type="text/css">
    .normal-text{font-style: normal; color: #333; font-weight: normal}
    .thumb-wrapper {display: flex; }
    .thumb {    width: 80px; height: 45px; overflow: hidden; border-radius: 3px; margin: 0 20px 0 0; justify-content: center; align-items: center; display: flex; box-shadow: 0 0 3px 0 #c1c1c1; background: #f3f3f3;} 
    #product-system-wrapper img {display: block; max-width: 100%; }
    .nav-tabs>li:first-child{ margin-left: 15px; }
    .pagenav {display: flex; align-items: center; }

    .pagenav b {margin: 0 10px; }
    .pagenav .pagination {    flex-grow: 1; justify-content: flex-end; display: flex;}
    td.history-edited i{color: #888}
    td.history-edited p{margin: 0; font-size: 14px}
</style>
<div id="product-system-wrapper" class="container-fluid" style="padding: 30px">
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <form method="POST" enctype="multipart/form-data" action="/<?=URL::build_current(['cmd' => 'overload_feature_manager'])?>" class="form-inline">
                        <strong style="line-height: 30px">Tắt tính năng cho đến </strong>
                        <div class="form-group mb-2">
                            <input class="form-control" name="time" value="<?=$this->map['time']?>">
                        </div>
                        <button class="btn btn-primary" value="" type="submit">SUBMIT</button>
                    </form>
                </div>
                <div class="panel-body form-inline">
                    <?php Form::draw_flash_message_success(OverloadFeatureManagerForm::FLASH_MESSAGE_KEY); ?>
                    <table>
                    <?php foreach($this->map['rows'] as $key => $row): ?>
                        <tr>
                            <td><label for="id-<?=$key?>">Tính năng: <?=$row['name']?></label></td>
                        </tr>
                    <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> 

<script type="text/javascript">
    const MODULE_ID = <?=Module::block_id()?>;
</script>