<style>
    body, html {
        font-size: 14px;
        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif !important;
    }
    #stepper {
        position: relative;
        -webkit-box-pack: justify;
        -webkit-justify-content: space-between;
        -moz-box-pack: justify;
        -ms-flex-pack: justify;
        justify-content: space-between;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        -webkit-flex-wrap: nowrap;
        -ms-flex-wrap: nowrap;
        flex-wrap: nowrap;
        display: -webkit-box;
        display: -webkit-flex;
        display: -moz-box;
        display: -ms-flexbox;
        display: flex;
    }
    .stepper__step {
        width: 140px;
        text-align: center;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        cursor: default;
        z-index: 1;
    }
    .stepper__step-icon {
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        display: -webkit-box;
        display: -webkit-flex;
        display: -moz-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -webkit-flex-direction: column;
        -moz-box-orient: vertical;
        -moz-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        -webkit-box-pack: center;
        -webkit-justify-content: center;
        -moz-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        -webkit-box-align: center;
        -webkit-align-items: center;
        -moz-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        position: relative;
        margin: auto;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        font-size: 1.875rem;
        border: 4px solid #e0e0e0;
        color: #e0e0e0;
        background-color: #fff;
        -webkit-transition: background-color .3s cubic-bezier(.4,0,.2,1) .7s,border-color .3s cubic-bezier(.4,0,.2,1) .7s,color .3s cubic-bezier(.4,0,.2,1) .7s;
        transition: background-color .3s cubic-bezier(.4,0,.2,1) .7s,border-color .3s cubic-bezier(.4,0,.2,1) .7s,color .3s cubic-bezier(.4,0,.2,1) .7s;
    }
    .stepper__step-icon--finish {
        border-color: #2dc258;
        color: #2dc258;
    }
    .stepper__step-text {
        text-transform: capitalize;
        color: rgba(0,0,0,.8);
        line-height: 1.25rem;
        margin: 1.25rem 0 .25rem;
    }
    .stepper__step-date {
        color: rgba(0,0,0,.26);
        height: .875rem;
    }
    .stepper__line {
        position: absolute;
        top: 29px;
        height: 4px;
        width: 100%;
    }
    .stepper__line-background, .stepper__line-foreground {
        position: absolute;
        width: -webkit-calc(100% - 140px);
        width: calc(100% - 140px);
        margin: 0 70px;
        height: 100%;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }
    .stepper__line-foreground {
        background: #2dc258;
        -webkit-transition: width 1s cubic-bezier(.4,0,.2,1);
        transition: width 1s cubic-bezier(.4,0,.2,1);
    }
    #toolbar-title {
        margin-top: 5px;
        color: #666;
        font-size: 22px;
        font-weight: bold;
        padding-left: 15px;
    }
    .heading-title {
        font-size: 21px;
        padding-top: 5px;
    }
    .alert-custom {
        padding: 10px 20px;
        margin-top: 15px;
    }
    .alert-custom-warning {
        background-color: #fffcf5;
        color: rgba(0,0,0,.54);
        border: 1px dotted rgba(0,0,0,.09);
    }
    .clear-fix:after {
        clear: both;
        display: block;
        content: ""
    }
    .order-detail-page__delivery__container-wrapper {
        margin-bottom: 20px;
        background: #fff;
        margin-top: 20px;
    }
    .shopee-border-delivery {
        height: .1875rem;
        width: 100%;
        background-position-x: -1.875rem;
        background-size: 7.25rem .1875rem;
        background-image: repeating-linear-gradient(45deg,#6fa6d6,#6fa6d6 33px,transparent 0,transparent 41px,#f18d9b 0,#f18d9b 74px,transparent 0,transparent 82px);
    }
    .order-detail-page__delivery__container {
        padding: 1rem .75rem 1.875rem;
    }
    .order-detail-page__delivery__header {
        display: -webkit-box;
        display: -webkit-flex;
        display: -moz-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-pack: justify;
        -webkit-justify-content: space-between;
        -moz-box-pack: justify;
        -ms-flex-pack: justify;
        justify-content: space-between;
        -webkit-box-align: end;
        -webkit-align-items: flex-end;
        -moz-box-align: end;
        -ms-flex-align: end;
        align-items: flex-end;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(0,0,0,.09);
    }
    .order-detail-page__delivery__header__title {
        color: rgba(0,0,0,.8);
        line-height: 1.5rem;
        margin-left: .5rem;
        text-transform: capitalize;
        font-size: 18px;
        font-weight: 600;
    }
    .order-detail-page__delivery__header__tracking-info {
        color: rgba(0,0,0,.54);
        line-height: .875rem;
        max-width: 50%;
        word-wrap: break-word;
    }
    .order-detail-page__delivery__content {
        padding: 1.625rem 1.25rem 0;
        display: -webkit-box;
        display: -webkit-flex;
        display: -moz-box;
        display: -ms-flexbox;
        display: flex;
    }
    .order-detail-page__delivery__shipping-address__container {
        width: 10rem;
        text-align: start;
        margin-right: 6%;
        padding-top: .625rem;
        -webkit-flex-shrink: 0;
        -ms-flex-negative: 0;
        flex-shrink: 0;
    }
    .order-detail-page__delivery__logistic-info__item__description--highlighted, .order-detail-page__delivery__shipping-address__shipping-name {
        color: rgba(0,0,0,.8);
    }
    .order-detail-page__delivery__shipping-address__shipping-name {
        line-height: 1.375rem;
        margin-bottom: .4375rem;
    }
    .order-detail-page__delivery__shipping-address__detail {
        color: rgba(0,0,0,.54);
    }
    .order-detail-page__delivery__shipping-address__detail {
        line-height: 1.3125rem;
    }
    .order-detail-page__delivery__logistic-info {
        padding: .625rem 0 1.25rem 2.5rem;
        border-left: 1px solid rgba(0,0,0,.09);
    }
    .order-detail-page__delivery__logistic-info__item-wrapper {
        position: relative;
    }
    .order-detail-page__delivery__logistic-info__bullet-connector {
        width: .0625rem;
        height: 100%;
        position: absolute;
        background: #d8d8d8;
        top: .6875rem;
        left: .3125rem;
    }
    .order-detail-page__delivery__logistic-info__item {
        color: rgba(0,0,0,.54);
    }
    .order-detail-page__delivery__logistic-info__item {
        line-height: 2rem;
        display: -webkit-box;
        display: -webkit-flex;
        display: -moz-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-flex-wrap: nowrap;
        -ms-flex-wrap: nowrap;
        flex-wrap: nowrap;
        -webkit-box-align: start;
        -webkit-align-items: flex-start;
        -moz-box-align: start;
        -ms-flex-align: start;
        align-items: flex-start;
    }
    .order-detail-page__delivery__logistic-info__item__bullet {
        width: .625rem;
        height: .625rem;
        background: #d8d8d8;
        margin-right: .5rem;
        margin-top: .6875rem;
        border-radius: 50%;
        z-index: 1;
    }
    .order-detail-page__delivery__logistic-info__item__bullet--highlighted {
        background: #ff5722;
    }
    .order-detail-page__delivery__logistic-info__item__bullet, .order-detail-page__delivery__logistic-info__item__time {
        -webkit-flex-shrink: 0;
        -ms-flex-negative: 0;
        flex-shrink: 0;
    }
    .order-detail-page__delivery__logistic-info__item__time {
        margin-right: 5px;
    }
    .order-detail-page__delivery__logistic-info__item__time--highlighted {
        color: rgba(0,0,0,.8);
    }
    .order-detail-page__delivery__logistic-info__item__bullet, .order-detail-page__delivery__logistic-info__item__time {
        -webkit-flex-shrink: 0;
        -ms-flex-negative: 0;
        flex-shrink: 0;
    }
    .order-detail-page__delivery__logistic-info__item__description--highlighted, .order-detail-page__delivery__shipping-address__shipping-name {
        color: rgba(0,0,0,.8);
    }
    .order-detail-page__delivery__logistic-info__bullet-connector--last {
        height: 0;
    }
    .text-line-through {
        color: #ccc;
        text-decoration: line-through;
    }
    .main-price {
        color: red;
    }
    td.tb-middle {
        vertical-align: middle !important;
    }
    .total-price {
        color: red;
        font-size: 25px;
    }
    .float-left {
        float: left
    }
    .float-right {
        float: right;
    }
</style>
<?php
    $items = [[=items=]];
?>
<div class="container" style="margin-top: 15px">
    <div class="panel panel-default">
        <div class="panel-heading clear-fix">
            <div class="heading-title float-left">[[|title|]]</div>
            <div class="float-right">
                <a href="index062019.php?page=<?= DataFilter::removeXSSinHtml($_GET['page']) ?>&cmd=manager-shipping" class="btn btn-info"><i class="fa fa-reply" aria-hidden="true"></i> Quay lại</a>
            </div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped sticky-enabled tableheader-processed sticky-table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Ngày phát</th>
                            <th>Trạng thái</th>
                            <th>Tại bưu cục</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($items)): ?>
                            <?php $items = array_reverse(end($items)); $i = 1; ?>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= $item->NgayPhat ?> <?= $item->GioPhat ?></td>
                                    <td><?= $item->TrangThai ?></td>
                                    <td><?= $item->TaiBuuCuc ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td class="text-center" colspan="4">Chưa có dữ liệu !</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

