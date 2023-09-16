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
    $shipping_statuses = [[=shipping_statuses=]];
    $orderInfo = [[=orderInfo=]];
    $products = [[=products=]];
    $shipping_info = [[=shipping_info=]];
    $shipping_costs_config = [[=shipping_costs_config=]];
    $prepaid = [[=prepaid=]];
    $order_carrier_logs = [[=order_carrier_logs=]];
    $status_current = '';
    $shipping_fee = 0;
?>
<div class="box">
    <div class="panel panel-default">
        <div class="panel-heading clear-fix">
            <div class="heading-title float-left">[[|title|]]</div>
            <div class="float-right">
            </div>
        </div>
        <div class="panel-body">
            <div id="stepper" class="">
                <?php if (!empty($order_carrier_logs)): ?>
                    <?php foreach ($order_carrier_logs as $log): ?>
                        <?php if (isset($log['deliver_logs']['icon'])): ?>
                            <?php if ($log['deliver_logs']['icon']): ?>
                                <div class="stepper__step stepper__step--finish">
                                    <div class="stepper__step-icon stepper__step-icon--finish">
                                        <i class="<?= $log['deliver_logs']['icon'] ?>"></i>
                                    </div>
                                    <div class="stepper__step-text"><?= $log['deliver_logs']['name'] ?></div>
                                    <div class="stepper__step-date"><?= ($log['deliver_logs']['create_at']) ? date('d-m-Y H:i:s', $log['deliver_logs']['create_at']) : '' ?></div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="stepper__line">
                    <div class="stepper__line-background" style="background: rgb(224, 224, 224);"></div>
                    <div class="stepper__line-foreground" style="width: calc((100% - 140px) * 1); background: rgb(45, 194, 88);"></div>
                </div>
            </div>

            <?php if (!empty($shipping_info)): ?>
                <div class="order-detail-page__delivery__container-wrapper">
                    <div class="shopee-border-delivery"></div>
                    <div class="order-detail-page__delivery__container">
                        <div class="order-detail-page__delivery__header">
                            <div class="order-detail-page__delivery__header__title">giao hàng</div>
                            <div class="order-detail-page__delivery__header__tracking-info"><?= $shipping_info['carrier']['name'] ?> | Mã Vận Đơn. <b><?= $shipping_info['order']['billcode'] ?></b></div>
                        </div>
                        <div class="order-detail-page__delivery__content">
                            <div class="order-detail-page__delivery__shipping-address__container">
                                <div class="order-detail-page__delivery__shipping-address">
                                    <!-- <div class="order-detail-page__delivery__shipping-address__shipping-name">Hoàn Trịnh</div> -->
                                    <div class="order-detail-page__delivery__shipping-address__detail"><?= $shipping_info['detail']['receiver']['address'] ?></div>
                                </div>
                            </div>
                            <div class="order-detail-page__delivery__logistic-info">
                                <?php if (!empty($order_carrier_logs)): ?>
                                    <?php $i = 1; ?>
                                    <?php foreach ($order_carrier_logs as $log): ?>
                                        <?php
                                        $log_current_class = ($i == 1) ? 'highlighted' : 'not-highligh';
                                        $log_last_class = ($i == count($order_carrier_logs)) ? 'last' : 'not-last';
                                        ?>
                                        <div class="order-detail-page__delivery__logistic-info__item-wrapper">
                                            <div class="order-detail-page__delivery__logistic-info__bullet-connector order-detail-page__delivery__logistic-info__bullet-connector--<?= $log_last_class ?>"></div>
                                            <div class="order-detail-page__delivery__logistic-info__item">
                                                <div class="order-detail-page__delivery__logistic-info__item__bullet order-detail-page__delivery__logistic-info__item__bullet--<?= $log_current_class ?>"></div>
                                                <div class="order-detail-page__delivery__logistic-info__item__time order-detail-page__delivery__logistic-info__item__time--<?= $log_current_class ?>"><?= ($log['deliver_logs']['create_at']) ? date('d-m-Y H:i:s', $log['deliver_logs']['create_at']) : '' ?></div>
                                                <div class="order-detail-page__delivery__logistic-info__item__description order-detail-page__delivery__logistic-info__item__description--<?= $log_current_class ?>"><?= $log['deliver_logs']['content'] ?></div>
                                            </div>
                                        </div>
                                        <?php $i++; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="shopee-border-delivery"></div>
                </div>
            <?php endif; ?>

            <div class="order-detail-main-content-wrapper">
                <div class="text-right"><label for="">ID ĐƠN HÀNG: <?= Url::get('id') ?></label></div>
                <?php if (!empty($products)): ?>
                    <table class="table table-bordered">
                        <?php $total_price = 0; ?>
                        <?php foreach ($products as $product): ?>
                            <?php
                            $img_product = '/assets/standard/images/vissale_logo.png';
                            $product_price = str_replace(',', '', $product['product_price']);
                            $total_price += $product_price * $product['qty'];
                            $strProperties = '';
                            ?>
                            <tr>
                                <td width="150">
                                    <a href="javascript:void(0)"><img src="<?= $img_product ?>" width="80" alt=""></a>
                                </td>
                                <td>
                                    <div><a href="javascript:void(0)"><?= $product['product_name']. $strProperties ?></a></div>
                                    <div>x<?= $product['qty'] ?></div>
                                </td>
                                <td class="tb-middle text-right" width="200">
                                    <div><b><?= number_format($product_price * $product['qty']) ?> đ</b></div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="2" class="text-right">Thành tiền</td>
                            <td class="text-right"><b><?= number_format($orderInfo['price']); ?> đ</b></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right">Giảm giá</td>
                            <td class="text-right"><b><?= number_format($orderInfo['discount_price']); ?> đ</b></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right">Phí vận chuyển</td>
                            <td class="text-right"><b><?= number_format($orderInfo['shipping_price']); ?> đ</b></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right">Phụ thu</td>
                            <td class="text-right"><b><?= number_format($orderInfo['other_price']); ?> đ</b></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right">Khai giá</td>
                            <td class="text-right"><?= number_format($orderInfo['insurance_value']) ?> đ</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right">Còn phải trả</td>
                            <td class="text-right"><b><?= number_format($orderInfo['total_price']) ?> đ</b></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right">Thanh toán trước</td>
                            <td class="text-right"><b><?= number_format($prepaid); ?> đ</b></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right">Còn nợ</td>
                            <td class="text-right"><b class="total-price"><?= number_format($orderInfo['total_price'] - $prepaid); ?> đ</b></td>
                        </tr>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

