<style>
    body {
        height: auto;
    }
    .content {
        min-height: 250px;
        padding: 15px;
        margin-right: auto;
        margin-left: auto;
        padding-left: 15px;
        padding-right: 15px;
    }
    .box {
        position: relative;
        border-radius: 3px;
        background: rgb(255, 255, 255);
        border-top: 3px solid rgb(210, 214, 222);
        margin-bottom: 20px;
        width: 100%;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    }
    .box.box-solid {
        border-top: 0;
    }
    .box-body {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
        border-bottom-right-radius: 3px;
        border-bottom-left-radius: 3px;
        padding: 10px;
    }
    .donhang-search-form {
        margin-bottom: 15px;
    }
    #list-status {
        border-bottom: 1px solid rgb(204, 204, 204);
        margin-bottom: 5px;
    }
    .btn-default .badge {
        color: rgb(255, 255, 255);
        background-color: rgb(51, 51, 51);
    }
    .page-bottom {
        border-top: 1px solid rgb(241, 241, 241);
        padding-top: 10px;
    }
    .float-right {
        float: right
    }
    ul.list-group li:nth-child(2n) {
        background: rgb(245, 245, 245)
    }
    .timeline>li>.timeline-item>.timeline-header {
        font-size: 13px;
    }
    .timeline:before {
        background: rgb(221, 221, 221);
    }
    a.btn-abs {
        position: absolute;
        width: 100%;
        height: 100%;
        z-index: 99;
        left: 0;
        top: 0;
        right: 0;
    }
    ul.timeline li:hover .timeline-item {
        background: rgb(217, 237, 247)
    }
    h3.panel-heading-title {
        margin: 0px;
    }
    .cke_button__templateconsignment_label {
        display: inline;
    }
    .rowItemKeyword {
        margin-bottom: 5px;
    }
    .panel-heading {
        padding: 10px 15px;
    }
    #list-tabs {
        margin-bottom: 10px;
    }
    .float-left {
        float: left;
    }
</style>
<?php
    $old_templates = [[=old_templates=]];
?>

<div id="page">
    <section class="content-header">
        <h1 class="page-title"><i class="fa fa-print"></i> <?= [[=title=]] ?></h1>
    </section>
    <section class="content">
        <div id="content">
            <div class="box box-solid">
                <form action="" method="POST" id="frmSaveTemplate">
                    <div class="box-body">
                        <div class="clearfix" style="border-bottom: 1px dotted #ccc">
                            <h3 class="float-left">Chọn shop để cập nhật</h3>
                            <div class="float-right">
                                <a href="index062019.php?page=<?= DataFilter::removeXSSinHtml($_GET['page']) ?>" class="btn btn-default">Quay lại</a>
                                <input type="submit" value="Cập nhật" class="btn btn-warning" />
                            </div>
                        </div>
                        <div><label><input type="checkbox" value="1" id="cball" checked> Chọn tất cả</label></div>
                    <?php if (!empty($old_templates)): ?>
                        <div class="row">
                            <?php foreach ($old_templates as $template): ?>
                                <div class="col-md-3">
                                    <div class="checkbox">
                                        <label><input type="checkbox" name="group_id[]" value="<?= $template['id'] ?>" checked><?= $template['name'] ?> (<?= $template['id'] ?>)</label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<script>
    $(function() {
        $("#cball").click(function(){
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
    })
</script>