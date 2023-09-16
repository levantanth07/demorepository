<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="assets/vissale/chat/css/chat.css">
<script type="application/javascript">
    function removeElementsByClass(elementClass){
        let list = document.getElementsByClassName(elementClass);
        for(let i = list.length - 1; 0 <= i; i--)
            if(list[i] && list[i].parentElement)
                list[i].parentElement.removeChild(list[i]);
    }
    removeElementsByClass("main-header");
    removeElementsByClass("main-sidebar");
    removeElementsByClass("navbar-static-top");

    const selectedConversationID = [[|conversation_id|]];
</script>
<style type="text/css">
    #wrapper{
        background-color: #ecf0f5;
        overflow: hidden;
    }
    .sidebar-mini.sidebar-collapse .content-wrapper,
    .sidebar-mini.sidebar-collapse .right-side,
    .sidebar-mini.sidebar-collapse .main-footer{
        margin: 0 !important;
    }
    #conversations .cvs-content .user-info{
        padding-top: 0;
        padding-bottom: 0;
    }
    #conversations .cvs-content{
        width: 100%;
    }
    .mt-auto.im-content{margin-top: 0;}
</style>
<div
    id="overlay-loading"
    class="well well-lg bs-loading-container"
    bs-loading-overlay
    bs-loading-overlay-reference-id="init-data"
    bs-loading-overlay-delay="2000">
</div>

<div id="conversations" ng-app="vissale">
    <div class="container" ng-controller="ChatOrderController as chat">
        <div class="row row-cvs d-flex">

            <div class="cvs-content d-flex flex-column col-lg-12">
                <div class="user-info d-flex  mb-20">
                    <div class="img d-flex align-content-center">
                        <img ng-src="https://graph.facebook.com/{{data.selectedConversation.fb_user_id}}/picture?type=normal" alt="name">
                    </div>
                    <div class="ct">
                        <div class="name">{{data.selectedConversation.fb_user_name}}</div>
                        <div class="address"><i class="fa fa-address-card"></i> Link post:
                            <a target="_blank" ng-href="https://facebook.com/{{data.selectedConversation.fb_post_id}}">Link Post</a>
                        </div>
                        <div class="phone"><i class="fa fa-phone"></i> Link comment:
                            <a target="_blank" ng-href="https://facebook.com/{{data.selectedConversation.comment_id}}">Link Comment</a>
                        </div>
                        <div class="note"><i class="fa fa-notes-medical"></i> chú thích</div>
                    </div>
                </div>

                <div class="cvs" id="list-messages">
                    <div
                            id="overlay-loading-refresh-messages"
                            class="well well-lg bs-loading-container"
                            bs-loading-overlay
                            bs-loading-overlay-reference-id="get-conversation-messages-spinner"
                            bs-loading-overlay-delay="2000">
                    </div>

                    <!-- list of conversation's messages -->
                    <div class="item item-l d-flex"
                         ng-repeat="(index, message) in data.messages | orderBy:'user_created'"
                         ng-class="{'flex-row-reverse' : message.isPageMessage}"
                    >
                        <div class="img">
                            <img ng-src="https://graph.facebook.com/{{message.fb_user_id}}/picture?type=normal" alt="">
                        </div>
                        <div class="txt d-flex">
                            <p ng-if="message.content && message.content!=message.attachments&&message.content!=message.attachment"
                               ng-bind-html="trustHtml(filterMessage(message.content))"></p>

                            <p ng-if="message.attachments"
                               class="message-history-images"
                               ng-bind-html="filterAttachments(message.attachments)"></p>

                            <p ng-if="message.attachment"
                               class="message-history-images"
                               ng-bind-html="filterAttachments(message.attachment)"></p>

                            <i class="d-flex align-items-end">{{momentFormatFromTimeString(message.created)}}</i>
                        </div>
                    </div>
                </div>
                <div class="mt-auto im-content">
                    <div class="bor">
                        <div>
                            <textarea ng-model="messageContent" class="form-control" placeholder="Viết tin nhắn tại đây" ></textarea>
                        </div>
                        <div class="reply-tools d-flex">
                            <div class="c-img">
                                <a class="tooltips" href="javascript:;"
                                   data-container="body"
                                   data-placement="bottom"
                                   data-original-title="Gửi ảnh"
                                   ngf-select="upload($file)"
                                   ngf-max-size="5MB"
                                   ngf-pattern="'image/*'"
                                   ngf-accept="'image/*'"
                                >
                                    <i class="fa fa-camera"></i>
                                </a>
                            </div>
                            <div class="c-message-tpl">
                                <div class="dropdown">
                                    <i class="fa fa-comment dropdown-toggle" data-toggle="dropdown"></i>
                                    <ul class="dropdown-menu dropup" aria-labelledby="dropdownMenu1">
                                        <li class="d-flex tit">
                                            <span class="">Mẫu trả lời nhanh</span>
                                            <a href="" class="ml-auto">Cài đặt</a>
                                        </li>
                                        <li ng-click="getQuickReply('Chào cả họ nhà bạn :D')"><span class="key">/demo</span>
                                            <span class="message">Chào bạn, shop giúp gì được cho bạn :D</span>
                                        </li>
                                        <li ng-click="getQuickReply('Vui lòng để lại số điện thoạiV để shop liên hệ hỗ trợ')"><span class="key">/dem2</span>
                                            <span class="message">Vui lòng để lại số điện thoạiV để shop liên hệ hỗ trợ</span></li>
                                        <li ng-click="getQuickReply('Địa chỉ nhà bạn ở đâu ?')"><span class="key">/dem2</span>
                                            <span class="message">Địa chỉ nhà bạn ở đâu ?</span></li>
                                        <li ng-click="getQuickReply('Bạn chắc chắn sẽ đặt hàng đúng không ạ')"><span class="key">/dem2</span>
                                            <span class="message">Bạn chắc chắn sẽ đặt hàng đúng không ạ ?</span></li>
                                    </ul>
                                </div>
                            </div>
                            <button ng-click="sendMessage()" class="ml-auto send-message btn btn-primary"> Gửi tin nhắn</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    window.group_id = [[|group_id|]];

    function initChatLayout() {

        $('.dropup').css('top', '-' + $('.dropup').css('height'));
        if($(".row-cvs").height() < (screen.height-200-51)){
            $(".row-cvs").height(screen.height-135-51);
        }

        if(($('.cvs-user .filter').height() + $('.cvs-user .bor-item').height())>(screen.height-60-51)){
            var height_cvs_content = screen.height - $('.cvs-user .filter').height()-240-51;
            $('.cvs-user .bor-item').css('overflow-y','scroll');
            $('.cvs-user .bor-item').css('max-height',height_cvs_content);
        }
        if(($('.cvs-content .user-info').height() + $('.cvs-content .cvs').height() + $('.cvs-content .im-content').height())>(screen.height)){
            var height_cvs_content = screen.height - $('.cvs-content .user-info').height() - $('.cvs-content .im-content').height()-200-51;
            $('.cvs-content .cvs').css('overflow-y','scroll');
            $('.cvs-content .cvs').css('max-height',height_cvs_content);
        }
        $('.reply-tools .fa-camera').click(function(){
            $(this).next().trigger('click');
        });

        $('#user-list').slimScroll({
            height: screen.height-375 + 'px',
            railVisible: true,
            alwaysVisible: true,
            allowPageScroll: true,
            start : 'top'
        });

        /*$('#list-messages').slimScroll({
            height: screen.height-425 + 'px',
            railVisible: true,
            alwaysVisible: true,
            allowPageScroll: true,
            start : 'top'
        });*/

        $('#conversations').css('visibility', 'visible');
        console.log('init layout');
    }

    $(document).ready(function(){

    });
</script>


<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.10/lodash.min.js"></script>
<script type="text/javascript" src="assets/vissale/chat/js/config.js"></script>
<script type="text/javascript" src="https://admin.tuha.vn:8443/client.js"></script>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.10/angular.min.js"></script>
<script type="text/javascript" src="https://code.angularjs.org/1.6.10/angular-animate.min.js"></script>
<script type="text/javascript" src="https://code.angularjs.org/1.6.10/angular-sanitize.min.js"></script>
<script type="text/javascript" src="https://code.angularjs.org/1.6.10/angular-messages.min.js"></script>

<script src="assets/vissale/chat/js/angular/ui-bootstrap-tpls.js"></script>
<script src="assets/vissale/chat/js/angular/angular-loading-overlay.js"></script>
<script src="assets/vissale/chat/js/angular/angular-loading-overlay-spinjs.js"></script>
<script src="assets/vissale/chat/js/angular/angular-loading-overlay-http-interceptor.js"></script>

<script src="assets/vissale/chat/js/angular/ng-file-upload-shim.js"></script>
<script src="assets/vissale/chat/js/angular/ng-file-upload.js"></script>
<script src="assets/vissale/chat/js/moment-with-locales.min.js"></script>
<link rel="stylesheet" type="text/css" href="assets/vissale/chat/js/angular/angular-toastr/angular-toastr.min.css" />
<script type="text/javascript" src="assets/vissale/chat/js/angular/angular-toastr/angular-toastr.tpls.min.js"></script>
<script src="assets/vissale/chat/js/jquery.slimscroll.min.js"></script>

<script type="text/javascript" src="assets/vissale/chat/js/common.js"></script>
<script type="text/javascript" src="assets/vissale/chat/js/chat.js"></script>
<script type="text/javascript" src="assets/vissale/chat/js/chatOrderController.js"></script>