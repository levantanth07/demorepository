<style>
    .px-4 {
        padding-right: 1.5rem!important;
        padding-left: 1.5rem!important;
    }
    .mt-0 {
        margin-top: 0!important;
    }
    .mb-5 {
        margin-bottom: 4rem!important;
    }
    .py-3 {
        padding-top: 1rem!important;
        padding-bottom: 1rem!important;
    }
    .mt-3 {
        margin-top: 1rem!important;
    }
    .container{
        background: #f7f6f6;
        min-height: 600px;
        padding-top:50px;
    }
    .card {
        width: 100%;
        border: none;
        box-shadow: 5px 6px 6px 2px #e9ecef;
        border-radius: 12px;
        background:#fff;
        margin: auto;
    }

    .circle-image img {
        border: 6px solid #fff;
        border-radius: 100%;
        padding: 0px;
        position: relative;
        width: 100px;
        height: 100px;
        border-radius: 100%;
        z-index: 1;
        background: #e7d184;
        cursor: pointer
    }

    .name {
        margin-top: -21px;
        font-size: 18px
    }

    .fw-500 {
        font-weight: 500 !important
    }

    .start {
        color: green
    }

    .stop {
        color: red
    }

    .rate {
        border-bottom-right-radius: 12px;
        border-bottom-left-radius: 12px
    }

    .rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center
    }

    .rating>input {
        display: none
    }

    .rating>label {
        position: relative;
        width: 1em;
        font-size: 30px;
        font-weight: 300;
        color: #FFD600;
        cursor: pointer
    }
    .fa.fa-star{
        position: relative;
        width: 1em;
        font-size: 30px;
        font-weight: 300;
        color: #FFD600;
    }

    .rating>label::before {
        content: "\2605";
        position: absolute;
        opacity: 0
    }

    .rating>label:hover:before,
    .rating>label:hover~label:before {
        opacity: 1 !important
    }

    .rating>input:checked~label:before {
        opacity: 1
    }

    .rating:hover>input:checked~label:before {
        opacity: 0.4
    }
    .btn-warning {
        color: #212529;
        background-color: #ffc107;
        border-color: #ffc107;
    }
    .buttons {
        top: 36px;
        position: relative
    }

    .bg-success{
        background-color: #28a745!important;
    }
    h6.text-white {
        color: #fff!important;
        margin-top: 0;
        margin-bottom: .5rem !important;
        font-weight: bold !important;
        line-height: 1.2 !important;
        font-size: 16px !important;
    }
    .text-center {
        text-align: center!important;
    }
</style>
<div class="container">
    <form name="CareDetailForm" method="post" autocomplete="off" role="presentation">
        <div class="box box-info">
            <div class="box-header with-border">
                <h4 class="box-title"> <i class="fa fa-cogs"></i> Đánh giá chất lượng SALE / CSKH</h4>
                <div class="box-tools pull-right">
                    <div class="pull-left"> <button class="btn btn-warning rating-submit"><i class="fa fa-check-square"></i> Lưu </button> </div>
                    <a href="#" onclick="if(confirm('Bạn có chắc chắn muốn đóng không?')){window.close();} return false;" class="btn btn-default"><i class="fa fa-times-circle"></i> Đóng</a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                        <div class="col-md-5">
                            <div class="card text-center mb-5">
                                <?php if(Form::$current->is_error())
                                {
                                    ?>
                                    <div><?php echo Form::$current->error_messages();?></div>
                                    <?php
                                }
                                ?>
                                <div class="circle-image">
                                    <img src="[[|staff_avatar|]]" onerror="this.src='assets/standard/images/no_avatar.webp'" width="90">
                                </div>
                                <span class="dot"></span> <span class="name mb-1 fw-500">Nhân viên: [[|staff_name|]]</span>
                                <div class="location mt-4">

                                </div>
                                <div class="rate bg-success py-3 mt-3">
                                    <h6 class="mb-0 text-white">Khách hàng đánh giá</h6>
                                    <!--IF:cond([[=rating_point=]])-->
                                    [[|rating_point|]]
                                    <!--ELSE-->
                                    <div class="rating">
                                        <input type="radio" name="rating" value="5" id="5"><label for="5">☆</label>
                                        <input type="radio" name="rating" value="4" id="4"><label for="4">☆</label>
                                        <input type="radio" name="rating" value="3" id="3"><label for="3">☆</label>
                                        <input type="radio" name="rating" value="2" id="2"><label for="2">☆</label>
                                        <input type="radio" name="rating" value="1" id="1"><label for="1">☆</label>
                                    </div>
                                    <!--/IF:cond-->
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Góp ý cải thiện dịch vụ
                                </div>
                                <div class="panel-body">
                                    <!--IF:cond([[=rating_point=]])-->
                                    <div class="mb-1 alert alert-info">
                                        [[|feedback|]]
                                    </div>
                                    <!--LIST:rating_template_ids-->
                                    <ul>
                                        <li>
                                            [[|rating_template_ids.content|]]
                                        </li>
                                    </ul>
                                    <!--/LIST:rating_template_ids-->
                                    <!--ELSE-->
                                    <div class="mb-1">
                                        <textarea name="feedback" id="feedback" class="form-control" placeholder="Nội dung khách phản hồi"></textarea>
                                    </div>
                                    <div id="RatingTemplates">

                                    </div>
                                    <!--/IF:cond-->
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    Nhân viên CS
                                </div>
                                <div class="panel-body">
                                    <div class="mb-1">
                                        <select name="cs_status_id" id="cs_status_id" class="form-control"></select>
                                    </div>
                                    <div class="mb-1">
                                            <textarea name="cs_note" id="cs_note" rows="10" class="form-control" placeholder="Nhân viên CS ghi chú"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="box box-default">
                                <div class="box-header">
                                    <h3 class="title">Thông tin chi tiết</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <span class="d-block"><i class="fa fa-file-o"></i> <small class="text-truncate ml-2">Mã đơn hàng: [[|id|]]</small> </span>
                                            <span class="d-block"><i class="fa fa-user"></i> <small class="text-truncate ml-2">Khách hàng: [[|customer_name|]]</small> </span>
                                            <span class="d-block"><i class="fa fa-phone-square"></i> <small class="text-truncate ml-2">SĐT: [[|mobile|]]</small> </span>
                                            <span><i class="fa fa-map-marker stop mt-2"></i> <small class="text-truncate ml-2">Địa chỉ: [[|address|]], [[|city|]]</small> </span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Ghi chú:</strong>
                                            <div class="text-muted well well-sm no-shadow">
                                                [[|note1|]]
                                                [[|note2|]]
                                            </div>
                                        </div>
                                    </div>
                                    <h4>Sản phẩm</h4>
                                    <table class="table table-striped">
                                        <tr>
                                            <th>STT</th>
                                            <th>Tên sản phẩm</th>
                                            <th>Số lượng</th>
                                        </tr>
                                        <?php $i=0?>
                                        <!--LIST:products-->
                                        <tr>
                                            <td><?=++$i?></td>
                                            <td>[[|products.name|]]</td>
                                            <td>[[|products.qty|]]</td>
                                        </tr>
                                        <!--/LIST:products-->
                                    </table>
                                </div>
                            </div>
                            <div class="box box-default box-solid">
                                <div class="box-header">
                                    <div class="title">Nội dung câu hỏi mẫu để hỏi khách hàng</div>
                                </div>
                                <div class="box-body">
                                    <table class="table table-striped">
                                        <?php $i=0?>
                                        <!--LIST:question_templates-->
                                        <tr>
                                            <td>[[|question_templates.content|]]</td>
                                        </tr>
                                        <!--/LIST:question_templates-->
                                    </table>
                                </div>
                            </div>
                        </div>
                </div>
                <input name="order_id" type="hidden" id="order_id">
            </div>
        </div>
    </form>
</div>
<script>
    $(document).ready(function(){
        $('input[name=rating]').click(function () {
            getRatingTemplate($(this).val());
        });
    });
    function getRatingTemplate(Point){
        $.ajax({
            url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
            data: {
                cmd: "get_rating_template",
                point: Point
            },
            beforeSend: function(){
                $('#loading').show()
            },
            success: function(data) {
                $('#RatingTemplates').html(data);
            },
            complete: function() {
                $('#loading').hide()
            }
        })
    }
</script>