<script>
    //su ly voi tong dai
    let user_type = <?php echo Session::get('account_type'); ?>;

    jQuery(document).ready(function () {
        console.log('Call Cloud system');
        //notify.close();

        let new_list = [];
        let notify_list = [];
        let displaying = [];

        function intervalCallCloud() {

            new_list = [];
            let jqxhr = jQuery.ajax({
                url: 'https://spa.tuha.vn/callcloud.php',
                headers : {

                }
            })
                .done(function(response) {
                    xmlDoc = jQuery.parseXML( response );
                    //console.log( "response", xmlDoc );
                    $xml = jQuery( xmlDoc );
                    $xml.find("num").each(function() {
                        let number = jQuery(this).text();
                        new_list.push({mobile:number});
                        //getCustomer(number);
                    });
                    validateNumbers();
                })
                .fail(function(error) {
                    console.log( "error", error );
                })
                .always(function() {
                    //console.info( "complete" );
                });
        }

        function validateNumbers() {
            //console.log('validateNumbers ...');
            let ended_calls = _.differenceBy(notify_list, new_list, 'mobile');
            //console.log('ended_calls', ended_calls);
            _.forEach(ended_calls, function(value, key) {
                let __index = _.findIndex(notify_list, value);
                let __dindex = _.findIndex(displaying, value);
                //console.log('__dindex', __dindex );
                displaying[__dindex].notify.close();
                displaying[__dindex].alert.close();
                notify_list.splice(__index, 1);
                displaying.splice(__dindex, 1);
            });

            let new_calls = _.differenceBy(new_list, notify_list, 'mobile');
            //console.log('new_calls', new_calls);
            _.forEach(new_calls, function(value, key) {
                notify_list.push(value);
                getCustomer(value.mobile);
            });


            ///
        }

        function getCustomer(number) {
            console.info('calling number', number);

            let current_mobile = number;
            jQuery.ajax({
                method: "POST",
                url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                data : {
                    'cmd':'callcloud',
                    'mobile':number
                },
                dataType: 'json',
                beforeSend: function(){
                    //console.info('sending ...');
                },
                success: function(content) {
                    let notify = $.notify({
                            // options
                        title: `Cuộc gọi đến: <strong>${current_mobile}</strong><br>`,
                        message: `<strong>${content.customer_name}</strong> ${content.customer_age}<br><br>
                                    Chi nhánh: ${content.group_name}<br>
                                    Nhóm: ${content.crm_group_name}<br>`,
                        url: content.url,
                        target: '_blank'

                    }, {
                            // settings
                        delay : 0,
                        type: 'danger',
                        placement: {
                            from: "bottom",
                            align: "left"
                        },
                    }
                    )   ;
                    let alertNotify =  notifyMe(current_mobile, content);
                    displaying.push({mobile:current_mobile, notify:notify, alert:alertNotify});
                    //console.log('displaying', displaying )
                }
            });
        }

        if(user_type === 3) setInterval(function(){ intervalCallCloud(); }, 2000);
    });

    function checkNotificationPermission(){

        console.log(Notification.permission);

        if (Notification.permission == 'granted') {
            return false;
        }

        Notification.requestPermission(function (permission) {
            if (permission == "denied") {
                return false;
            }
            var options = {
                body: "Xin cảm ơn bạn đã đăng ký nhận thông báo trên tuha.vn!",
                icon: 'https://tuha.vn/assets/standard/images/tuha_logo.png?v=03122021'
            };
            var notification = new Notification('tuha.vn thông báo:', options);
            setTimeout(notification.close.bind(notification), 5000);
        });
    }

    function notifyMe(current_mobile, content) {
        // Let's check if the browser supports notifications

        let message = `${content.customer_name} ${content.customer_age}`;

        if ( !("Notification" in window) ) {
            console.error("Trình duyệt này không hỗ trợ nhận thông báo tin nhắn.");
        }
        // Let's check whether notification permissions have already been granted
        else if (Notification.permission === "granted") {
            // If it's okay let's create a notification
            //var notification = new Notification("Hi there!");
            let options = {
                body: message,
                icon: 'https://tuha.vn/assets/standard/images/tuha_logo.png?v=03122021',
                requireInteraction: true
            };
            let notification = new Notification(`Calling: ${current_mobile}`, options);
            notification.onclick = function(event) {
                event.preventDefault(); // prevent the browser from focusing the Notification's tab
                window.open(content.url, '_blank');
                notification.close();
            };
            setTimeout(notification.close.bind(notification), 600000);
            return notification;
        }

        // Otherwise, we need to ask the user for permission
        else if (Notification.permission !== 'denied') {
            Notification.requestPermission(function (permission) {
                // If the user accepts, let's create a notification

                if (permission === "granted") {
                    var options = {
                        body: "Cảm ơn bạn đã đăng ký nhận tin nhắn tại tuha.vn!",
                        icon: 'https://tuha.vn/assets/standard/images/tuha_logo.png?v=03122021'
                    };
                    var notification = new Notification(username + ' thông báo:', options);
                    setTimeout(notification.close.bind(notification), 5000);
                }
            });
        }
        return null;
        // Finally, if the user has denied notifications and you
        // want to be respectful there is no need to bother them any more.
    }

    //notifyMe({});
    checkNotificationPermission();

</script>