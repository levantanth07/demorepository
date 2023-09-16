<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="assets/vissale/chat/css/chat.css">

<div
        id="overlay-loading"
        class="well well-lg bs-loading-container"
        bs-loading-overlay
        bs-loading-overlay-reference-id="init-data"
        bs-loading-overlay-delay="5000">
</div>

<div id="conversations" ng-app="vissale">
    <div class="container" ng-controller="ChatController as chat">
        <div class="row row-cvs d-flex">
            <div class="cvs-user col-lg-3 pt-15">
                <div class="filter">
                    <div class="row">
                        <div class="search col-lg-6 mb-10">
                            <form action="">
                                <input type="text" name="1" id="1" class="form-control" placeholder="Tìm kiếm">
                                <button><i class="fa fa-search"></i></button>
                            </form>
                        </div>
                        <div class="page col-lg-6 mb-10">
                            <select class="form-control" ng-model="data.selectedPageId" ng-change="changePage()">
                                <option value="page_id">Tất cả page</option>
                                <option ng-repeat="page in data.pages"
                                        ng-if="isRegisteredPage(page)"
                                        value="{{page.page_id}}">{{page.page_name}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="btn-gr d-flex justify-content-between">
                        <div class="btn-group ">
                          <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Dữ liệu <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu">
                            <li><a ng-click="changeConversationTypeFilter('all')" href="javascript:;">Tất cả</a></li>
                            <li>
                                <a ng-click="changeConversationTypeFilter(1)" href="javascript:;">
                                    <i ng-if="filterConversationType==1" class="text-primary fa fa-check"></i> Comment
                                </a>
                            </li>
                            <li>
                                <a ng-click="changeConversationTypeFilter(0)" href="javascript:;">
                                    <i ng-if="filterConversationType==0" class="text-primary fa fa-check"></i> Inbox
                                </a>
                            </li>
                          </ul>
                        </div>
                        <div class="btn-group">
                          <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Trạng thái <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu">
                            <li><a ng-click="changeIsReadConversationFilter('all')" href="javascript:;">Tất cả</a></li>
                            <li><a ng-click="changeIsReadConversationFilter(false)" href="javascript:;">
                                    <i ng-if="readFilterOption == false" class="text-primary fa fa-check"></i> Chưa đọc</a></li>
                            <li><a ng-click="changeIsReadConversationFilter(true)" href="javascript:;">
                                    <i ng-if="readFilterOption == true" class="text-primary fa fa-check"></i> Đã đọc
                                </a>
                            </li>
                          </ul>
                        </div>
                        <div class="btn-group">
                          <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Đơn hàng <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu">
                            <li><a ng-click="changeConversationHasOrderFilter('all')" href="javascript:;">Tất cả</a></li>
                            <li><a ng-click="changeConversationHasOrderFilter(true)" href="javascript:;">
                                    <i ng-if="filterConversationHasOrder == true" class="text-primary fa fa-check"></i>Có đơn hàng</a></li>
                            <li><a ng-click="changeConversationHasOrderFilter(false)" href="javascript:;">
                                    <i ng-if="filterConversationHasOrder == false" class="text-primary fa fa-check"></i>Chưa có đơn hàng</a></li>
                          </ul>
                        </div>
                    </div>
                </div>
                <!-- list of conversations -->
                <div id="user-list" class="bor bor-item mb-20">
                    <div
                            id="overlay-loading-refresh-conversations"
                            class="well well-lg bs-loading-container"
                            style="
						position: absolute;
						width: 100%;
						height: 100%;
						z-index: 9999;
						top: 0;
						left: 0;
						opacity: .8;"
                            bs-loading-overlay
                            bs-loading-overlay-reference-id="get-conversations-spinner"
                            bs-loading-overlay-delay="1000">
                    </div>
                    <div class="item d-flex"
                         ng-repeat="(index, conversation) in data.conversations | orderBy:'-last_conversation_time'"
                         ng-if="filterConversation(conversation)&&filterConversationByRead(conversation)&&setConversationTypeFilter(conversation)&&setConversationHasOrderFilter(conversation)"
                         ng-click="selectConversation(conversation)"
                         ng-class="{'active' : isActiveConversation(conversation), 'Unread' : !conversation.is_read}"
                    >
                        <div class="img mr-10">
                            <img ng-src="https://graph.facebook.com/{{conversation.fb_user_id}}/picture?type=normal" alt="avatar">
                        </div>
                        <div class="ct">
                            <div class="name">{{conversation.fb_user_name}}</div>
                            <div class="txt">{{conversation.first_content}}</div>
                        </div>
                        <div class="time ml-auto">
                            <div class="t">{{momentFormatFromUnixTime(conversation.last_conversation_time, format)}}</div>
                        </div>
                    </div>
                </div>

                <div class="count text-center">
                    Bạn có tất cả {{data.conversations.length}} bài hội thoại
                </div>
            </div>
            <div class="cvs-content d-flex flex-column col-lg-12">
                <div ng-if="data.selectedConversation" class="user-info d-flex  mb-20">
                    <div class="img d-flex align-content-center">
                        <a target="_blank" ng-href="https://facebook.com/{{data.selectedConversation.fb_user_id}}">
                            <img ng-src="https://graph.facebook.com/{{data.selectedConversation.fb_user_id}}/picture?type=normal" alt="name">
                        </a>
                    </div>
                    <div class="ct">
                        <div class="name">
                            <a target="_blank" ng-href="https://facebook.com/{{data.selectedConversation.fb_user_id}}">{{data.selectedConversation.fb_user_name}}</a>
                        </div>
                        <div class="address" ng-if="data.selectedConversation.comment_id">
                            <i class="fa fa-comments" aria-hidden="true"></i>
                            <a target="_blank" ng-href="https://facebook.com/{{data.selectedConversation.comment_id}}">Link Comment</a>
                        </div>
                        <div ng-if="data.selectedConversation.fb_post_id" class="phone">
                            <i class="fa fa-wpexplorer" aria-hidden="true"></i>
                            <a target="_blank" ng-href="https://facebook.com/{{data.selectedConversation.fb_post_id}}">Link Post</a> </div>
                        <div class="note">
                            <i class="fa fa-sticky-note" aria-hidden="true"></i>
                            Page <a target="_blank" ng-href="https://facebook.com/{{data.activeConversationPage.page_id}}">{{data.activeConversationPage.page_name}}</a>
                        </div>
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
                            <p ng-if="message.content&&message.content!=message.attachments&&message.content!=message.attachment"
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
            <div class="cvs-right col-lg-4" style="visibility: hidden;">
                <ul class="nav nav-tabs d-flex">
                  <li class="active"><a data-toggle="tab" href="#info">Thông tin</a></li>
                  <li ><a data-toggle="tab" href="#order">Tạo đơn</a></li>
                </ul>
                <div class="tab-content">
                    <div id="info" class="tab-pane fade in active ">
                        <div class=" row">
                            <div class="col-xs-6"><i class="fa fa-user"></i> Tên:</div>
                            <div class="col-xs-6"><a href="">Châu du dan</a></div>
                        </div>
                        <div class=" row">
                            <div class="col-xs-6"><i class="fa fa-envelope"></i> Email:</div>
                            <div class="col-xs-6"><a href="">chaududan@gamil.com</a></div>
                        </div>
                        <div class=" row">
                            <div class="col-xs-6"><i class="fa fa-phone"></i> Số điện thoại:</div>
                            <div class="col-xs-6"><a href="">01234656</a></div>
                        </div>
                        <div class=" row">
                            <div class="col-xs-6"><i class="fa fa-bandcamp"></i> Địa chỉ:</div>
                            <div class="col-xs-6"><a href="">Thanh xuân, Hà nội</a></div>
                        </div>
                        <div class=" row">
                            <div class="col-xs-6"><i class="fa fa-industry"></i> Tỉnh/TP:</div>
                            <div class="col-xs-6"><a href="">Hà nội</a></div>
                        </div>
                        <div class=" row">
                            <div class="col-xs-6"><i class="fa fa-user"></i> Quận/Huyện:</div>
                            <div class="col-xs-6"><a href="">Thanh xuân</a></div>
                        </div>
                        <div class=" row">
                            <div class="col-xs-6"><i class="fa fa-birthday-cake"></i> Sinh nhật:</div>
                            <div class="col-xs-6"><a href="">05-1-1992</a></div>
                        </div>

                    </div>
                  <div id="order" class="tab-pane fade ">
                        <div class="form-order">
                            <form>
                                  <div class="row row-form-order">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Tên</label>
                                                <input type="text" name="12" id="12" class="form-control" placeholder="Tên">
                                              </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Số điện thoại</label>
                                                <input type="text" name="13" id="13" class="form-control" placeholder="Email">
                                              </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>Địa chỉ</label>
                                                <input type="mail" name="14" id="14" class="form-control" placeholder="Địa chỉ">
                                              </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Tỉnh/TP</label>
                                                <input type="text" name='144' id=144 class="form-control" placeholder="Tỉnh/TP">
                                              </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Quận/Huyện</label>
                                                <input type="text" name="15" id="25" class="form-control" placeholder="Quận/Huyện">
                                              </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Ngày sinh</label>
                                                <input type="date" name="16" id="26" class="form-control" placeholder="Quận/Huyện">
                                              </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="mail" name="17" id="27" class="form-control" placeholder="Email">
                                              </div>
                                        </div>
                                  </div>
                                  <div class="products">
                                        <div class="tit lead">Sản phẩm</div>
                                        <div class="item d-flex">
                                            <div class="img">
                                                <img src="https://static.hotdeal.vn/images/1399/1399146/280x280/314235-bo-do-lot-nu-ren-cao-cap-elegance.jpg" alt="">
                                            </div>
                                            <div class="ct">
                                                <div class="form-group">
                                                    <input type="text" name="18" id="28" class="form-control" placeholder="Tên">
                                                    <textarea name="" placeholder="Ghi chú"></textarea>
                                                </div>
                                            </div>
                                            <div class="price ml-auto">
                                                <div class="form-group">
                                                    <input type="text" name="19" id="29" class="form-control" placeholder="Giá">
                                                    <input type="text" name="30" id="40" class="form-control" placeholder="Số lượng">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item d-flex">
                                            <div class="img">
                                                <img src="http://img1.baza.vn/upload/products-var-m3YiSzA5/z8wmtkR2.JPG?v=635852746548290298" alt="">
                                            </div>
                                            <div class="ct">
                                                <div class="form-group">
                                                    <input type="text" name="31" id="41" class="form-control" placeholder="Tên">
                                                    <textarea name="" placeholder="Ghi chú"></textarea>
                                                </div>
                                            </div>
                                            <div class="price ml-auto">
                                                <div class="form-group">
                                                    <input type="text" name="32" id="32" class="form-control" placeholder="Giá">
                                                    <input type="text" name="33" id="33" class="form-control" placeholder="Số lượng">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="result">
                                        <div class="item d-flex">
                                            <span>Tổng tiền hàng:</span>
                                            <span class="ml-auto">1.000.000 đ</span>
                                        </div>
                                        <div class="item d-flex">
                                            <span>Giảm giá:</span>
                                            <span class="ml-auto">100.000 đ</span>
                                        </div>
                                        <div class="item d-flex">
                                            <span>Thu khác:</span>
                                            <span class="ml-auto">100.000 đ</span>
                                        </div>
                                        <div class="item d-flex">
                                            <span>Khách cần trả:</span>
                                            <span class="ml-auto">800.000 đ</span>
                                        </div>
                                    </div>
                                    <div class="bot">
                                        <div class="stt">
                                            <select name="34" id="34" class="form-control">
                                              <option>Trạng thái đơn hàng</option>
                                              <option>2</option>
                                              <option>3</option>
                                              <option>4</option>
                                              <option>5</option>
                                            </select>
                                        </div>
                                        <button><i class="fa fa-save"></i> Lưu</button>
                                    </div>
                            </form>
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

        $('#conversations').css('visibility', 'visible');
        console.log('init layout');
    }
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
<script type="text/javascript" src="assets/vissale/chat/js/chatController.js"></script>