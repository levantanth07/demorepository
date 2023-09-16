

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
    .alert-warning-custom {
        color: rgb(138, 109, 59) !important;
        background-color: rgb(252, 248, 227) !important;
        border-color: rgb(138, 109, 59);
        margin-top: 10px;
    }
</style>
<?php
    $print_types = [[=print_types=]];
    $type = [[=type=]];
    $constants = [[=constants=]];
    $page_variables = [[=page_variables=]];
    $template = [[=template=]];
    $is_default = [[=is_default=]];
    $print_groups = [[=print_groups=]];
    $paper_sizes = [[=paper_sizes=]];
    $type_id = [[=type_id=]];
    $href_current = Url::build('print-templates', [
        'type' => Url::get('type')
    ]);
    $template_id = [[=template_id=]];
?>

<div id="page">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('report')?>">Báo cáo</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
            <li class="breadcrumb-item">Setup mẫu in - </li>
            <li class="pull-right">
                <div class="pull-right">

                </div>
            </li>
        </ol>
    </nav>

    <section class="content-header">
        <h1 class="page-title"><i class="fa fa-print"></i> <?= [[=title=]] ?></h1>
    </section>
    <section class="content">
        <div id="content">
            <input type="hidden" id="href_current" value="<?= $href_current ?>">
            <div class="box box-solid">
                <form action="" method="POST" id="frmSaveTemplate">
                    <input type="hidden" name="type_id" value="<?= $type_id ?>">
                    <input type="hidden" name="template_id" value="<?= $template_id ?>">
                <div class="box-body">
                    <?php if (!empty($paper_sizes)): ?>
                    <section id="box-search" class="row">
                        <div class="form-group col-md-6">
                            <label for="" class="col-xs-3 control-label">Chọn khổ giấy</label>
                            <div class="col-xs-9">
                                <select  name="paper_size" id="paper_size" class="form-control">
                                    <?php
                                    foreach ($paper_sizes as $k => $size):
                                        $selected = ($k == Url::get('paper_size')) ? 'selected' : "";
                                    ?>
                                        <option value="<?= $k ?>" <?= $selected ?>><?= $size['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <section id="list-tabs">
                                <ul class="nav nav-pills">
                                    <?php
                                    foreach ($print_types as $k => $p_type):
                                    $p_active = ($k == $type) ? 'active' : "";
                                    ?>
                                    <li role="presentation" class="<?= $p_active ?>"><a href="/index062019.php?page=<?= DataFilter::removeXSSinHtml(Url::get('page')) ?>&type=<?= $type ?>"><?= $p_type['name'] ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </section>
                        </div>
                    </section>
                    <?php endif; ?>
                    <section id="print-wrap">
                        <div class="row">
                            <section class="col-md-6">
                                <textarea name="data" id="ckeditor" class="ckeditor" cols="30" rows="10"><?= $template ?></textarea>
                                <div class="form-group text-right" style="margin-top: 5px">
                                    <a href="#" data-toggle="modal" data-target="#returnDefaultTemplateModal" class="btn btn-default pull-left" id="btn-default-template">
                                        <i class="fa fa-arrow-left"></i> Quay về dùng mẫu in mặc định
                                    </a>
                                    <label for="is_default"><input type="checkbox" name="is_default" value="1" id="is_default" <?= ($is_default == 1) ? "checked" : "" ?> /> Đặt làm mặc định</label>
                                    <?php if (User::is_admin()): ?>
                                        <label for="is_system"><input type="checkbox" name="is_system" value="1" id="is_system" /> Đặt làm mẫu in</label>
                                    <?php endif; ?>
                                    <button class="btn btn-success btn-save" type="submit">
                                        <i class="fa fa-floppy-o"></i> Lưu cài đặt
                                    </button>
                                </div>
                            </section>
                            <section class="col-md-6">
                                <div class="panel panel-default panel-preview">
                                    <div class="panel-heading">
                                        <h3 class="panel-heading-title text-center">Xem trước mẫu in</h3>
                                    </div>
                                    <div class="panel-body"></div>
                                </div>
                                <div class="alert alert-warning alert-warning-custom">
                                    <div><b>Chú ý:</b></div>
                                    <div>- Màn hình bên trái là nội dung mẫu in quý khách có thể thay đổi (Theo khổ giấy cần in: Khổ A4|A5, Khổ A4|A5 (khổ ngang), Mẫu 8 đơn hàng 1 trang (Khổ dọc), Mẫu 6 đơn hàng 1 trang (Khổ ngang)).</div>
                                    <div>- Màn hình bên phải là màn hình xem trước mẫu in sau khi thay đổi.</div>
                                    <div>- Nút <b>"Đăt làm mặc định"</b>, nếu quý khách muốn sử dụng mẫu in này làm mặc định chọn khi in.</div>
                                    <div>- Sau khi chỉnh sửa hoàn tất, quý khách nhấn <b>"Lưu cài đặt"</b> để lưu lại nội dung đã chỉnh sửa.</div>
                                    <div><b>Giải thích thuật ngữ:</b></div>
                                    <div>- <b>"Từ khóa cho mẫu in"</b>: Là tất cả các từ khóa quý khách có thể sử dụng cho mẫu in này. Từ khóa của mẫu in được xác định bên trong ký tự {__ __}, ví dụ: {__MA_DH__} là mã đơn hàng.</div>
                                    <div>- Khi quý khách sử dụng từ khóa này, hệ thống sẽ thay thế từ khóa đó bằng dữ liệu đã được xác định của đơn hàng. Ví dụ: trên màn hình từ khóa <b>"{__MA_DH__}"</b> sẽ được thay thế bằng mã đơn hàng <b>4193531</b> khi in.</div>
                                    <div>- Quý khách có thể lựa chọn nhiều từ khóa mẫu in, bằng cách click vào biểu tượng <b>"Từ khóa cho mẫu in"</b>.</div>
                                    <div><b>Lưu ý:</b></div>
                                    <div>- Nội dung xem trước mẫu in chỉ là một nội dung mẫu, không phải là một đơn hàng cụ thể.</div>
                                </div>
                            </section>
                        </div>
                    </section>
                </div>
                </form>
            </div>
        </div>
    </section>
</div>

<div id="returnDefaultTemplateModal" class="modal fade">
    <div class="modal-dialog" style="width: 600px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Xác nhận</h4></div>
            <div class="modal-body">
                <div class="alert alert-warning">Bạn có chắc chắn muốn dùng mẫu in mặc định của hệ thống?
                    <p>(Các chỉnh sửa hiện tại của mẫu in này sẽ bị xóa và dùng mẫu mặc định của hệ thống)</p>
                </div>
            </div>
            <div class="modal-footer">
                <button data-id="" id="confirmReturnDefaulTemplate" type="button" class="btn btn-primary">Đồng ý</button>
                <button type="button" class="btn btn-default reload" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script id="template-modal" type="text/html">
    <div class="form-horizontal consignment-selector clearfix">
        <div class="row">
            <div class="col-md-12">
            <?php foreach ($constants as $k => $constant): ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= $print_groups[$k] ?></h3>
                    </div>
                    <div class="panel-body">
                        <?php foreach($constant['variables'] as $ki => $item): ?>
                        <?php $loop = !empty($item['is_interval']) ? 1 : 2 ?>
                            <div class="col-md-6 rowItemKeyword">
                                <div class="row">
                                    <div class="col-xs-4"><?= $item['name'] ?></div>
                                    <div class="col-xs-6"><span><?= $ki ?></span></div>
                                    <div class="col-xs-2"><a href="javascript:" data-loop=<?= $loop ?> data-key="<?= str_replace(['{', '}'], '', $ki) ?>" class="btn btn-default btn-xs btn-select-key"><i class="fa fa-check"></i> Chọn</a></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
</script>
<!-- <script src="//cdn.ckeditor.com/4.11.2/full/ckeditor.js"></script> -->
<!-- <script src="https://cdn.ckeditor.com/4.11.2/standard/ckeditor.js"></script> -->
<script>
    var pageVariable = <?= $page_variables ?>;
    var href_current = $('#href_current').val()
    var pageFunctions = {
        showPreviewTemplate: function(template) {
            $('.panel-preview .panel-body').html(this.replaceTemplate(template));
        },
        replaceTemplate: function(template) {
            if (!template) {
                return;
            }

            var i, j, keyword;
            if (typeof pageVariable.consignmentExamples != 'undefined' && typeof pageVariable.consignmentExamples.nomalItems != 'undefined') {
                for (i in pageVariable.consignmentExamples.nomalItems) {
                    template = this.replaceAll(template, '{' + i + '}', pageVariable.consignmentExamples.nomalItems[i])
                }
            }

            var tempDoc = $('<div/>').html(template);
            var hasIntervalConsignment = false;
            var tableInterval = null;
            var trStart = false;
            if (typeof pageVariable.consignmentExamples != 'undefined' && typeof pageVariable.consignmentExamples.intervalItems != 'undefined') {
                for (j in pageVariable.consignmentExamples.intervalItems) {
                    for (keyword in pageVariable.consignmentExamples.intervalItems[j]) {
                        if (tempDoc.find(":contains('" + keyword + "')").length) {
                            tempDoc.find(":contains('" + keyword + "')").each(function() {
                                if (!tableInterval) {
                                    tableInterval = $(this).closest('table');
                                }
                            });

                            hasIntervalConsignment = true;
                            break;
                        }
                    }
                }
            }

            if (hasIntervalConsignment) {
                tableInterval = null;
                trStart = false;
                if (typeof pageVariable.consignmentExamples != 'undefined' && typeof pageVariable.consignmentExamples.intervalItems != 'undefined') {
                    for (j in pageVariable.consignmentExamples.intervalItems) {
                        for (keyword in pageVariable.consignmentExamples.intervalItems[j]) {
                            tempDoc.find(":contains('" + keyword + "')").filter(function() {
                                return ($(this).clone().children().remove().end().filter(":contains('" + keyword + "')").length > 0)
                            }).each(function() {
                                if (!tableInterval) {
                                    tableInterval = $(this).closest('table');
                                }

                                var trIndex = tableInterval.find('tbody tr').index($(this).closest('tr'));
                                if (trIndex != -1) {
                                    if (trStart === false || trStart == -1 || trStart > trIndex) {
                                        trStart = trIndex;
                                    }
                                }
                            });
                        }
                    }
                }

                console.log(trStart)
                intervalHtml = '';
                tableInterval.find('tbody tr').each(function() {
                    if (tableInterval.find('tbody tr').index($(this)) == trStart) {
                        intervalHtml += $('<div/>').append($(this)).html();
                    }
                });
                var replaceInterval = '';
                for (i in pageVariable.consignmentExamples.intervalItems) {
                    var temp = intervalHtml;
                    for (var key in pageVariable.consignmentExamples.intervalItems[i]) {
                        temp = this.replaceAll(temp, '{' + key + '}', pageVariable.consignmentExamples.intervalItems[i][key]);
                    }

                    replaceInterval += temp;
                }
                
                tableInterval.find('tbody').append(replaceInterval);
                template = tempDoc.html();
            }

            return template
        },
        replaceAll: function(str, find, replace) {
            return str.replace(new RegExp(find, 'g'), replace);
        }
    }

    $(function() {
        CKEDITOR.replace('ckeditor', {
            height: 400,
            toolbar: [{
                name: 'styles',
                items: ['Font', 'FontSize', 'Format']
            }, {
                name: 'colors',
                items: ['TextColor', 'BGColor']
            }, {
                name: 'basicstyles',
                items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']
            }, {
                name: 'insert',
                items: ['Table', 'HorizontalRule', 'SpecialChar', 'PageBreak']
            }, {
                name: 'paragraph',
                items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language']
            }, {
                name: 'document',
                items: ['Source', '-', 'Maximize', 'Templateconsignment']
            }],
            removeButtons: 'NewPage,Save,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Language,Flash,Smiley,Iframe,ShowBlocks,About,Blockquote,Find,Replace,Cut,Copy,Paste,Link,Unlink,Anchor,Redo,Undo',
            autoUpdateElement: false,
            extraPlugins: 'quicktable,tableresize,templateconsignment',
            qtRows: 10,
            qtColumns: 10,
            qtBorder: '0',
            qtWidth: '100%',
            qtStyle: {
                'border-collapse': 'collapse'
            },
            qtClass: '',
            qtCellPadding: '0',
            qtCellSpacing: '0',
            qtPreviewBorder: '',
            qtPreviewSize: '4px',
            qtPreviewBackground: '#c8def4'
            // toolbar: []
        });

        CKEDITOR.instances['ckeditor'].on('contentDom', function() {
            CKEDITOR.instances['ckeditor'].on('change', function() {
                CKEDITOR.instances['ckeditor'].updateElement();
                pageFunctions.showPreviewTemplate(CKEDITOR.instances['ckeditor'].getData());
            });
        });

        pageFunctions.showPreviewTemplate(CKEDITOR.instances['ckeditor'].getData())

        $('#paper_size').change(function() {
            var paper_size = $(this).val()
            var new_href = href_current + "&paper_size=" + paper_size
            window.location.href = new_href
        })

        $('#confirmReturnDefaulTemplate').click(function() {
            CKEDITOR.instances['ckeditor'].setData(pageVariable.templates)
            CKEDITOR.instances['ckeditor'].updateElement();

            $('#frmSaveTemplate').submit();
        })
    })
</script>