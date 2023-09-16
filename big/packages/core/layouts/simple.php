<?php if(URL::get('page') === 'dang-nhap'): ?>
    [[|center|]]
<?php else: ?>
<div id="wrapper" class="wrapper">
    [[|banner|]]
    <div class="content-wrapper">
        <?php if(Session::get('chup_anh_nhan_vien') == 1) : ?>
                <?php
                    $startTime = Session::get('start_time_login');
                    $currentTime = time();
                    $time = $currentTime - $startTime;
                    $time = round($time / 60);
                ?>
                <?php if(Session::get('user_login')) : ?>
                    <div class="my_camera" style="display: none;"></div>
                    <div id="my_result"></div>
                <?php elseif(TIME_TAKE_PHOTO && !Session::get('user_login')) : ?>
                    <script type="text/javascript">
                        function timeLoadPage(){
                            location.reload();
                        }
                        var time = <?php echo TIME_TAKE_PHOTO ?>;
                        var timeLoad = time*60*1000 + 5000;
                        setInterval(timeLoadPage, timeLoad);
                    </script>
                <?php endif; ?>
                <?php if($time>=TIME_TAKE_PHOTO && !Session::get('user_login')) : ?>
                    <div class="my_camera" style="display: none;"></div>
                    <div id="my_result"></div>
                    <script>
                        Webcam.set({
                            width: 320,
                            height: 240,
                            image_format: 'jpeg',
                            jpeg_quality: 90
                         });
                        Webcam.attach('.my_camera');
                        function b64toBlob(b64Data, contentType, sliceSize) {
                            contentType = contentType || '';
                            sliceSize = sliceSize || 512;
                            var byteCharacters = atob(b64Data);
                            var byteArrays = [];
                            for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
                                var slice = byteCharacters.slice(offset, offset + sliceSize);
                                var byteNumbers = new Array(slice.length);
                                for (var i = 0; i < slice.length; i++) {
                                    byteNumbers[i] = slice.charCodeAt(i);
                                }
                                var byteArray = new Uint8Array(byteNumbers);
                                byteArrays.push(byteArray);
                            }
                            var blob = new Blob(byteArrays, {type: contentType});
                            return blob;
                        }
                        
                        function takePhoto() {
                            Webcam.snap( function(data_uri) {
                                var host_name = location.hostname;
                                var host_protocol = location.protocol;
                                var url = host_protocol+'//'+host_name;
                                var formData = new FormData();
                                var image = data_uri.split(";");
                                var contentType = image[0].split(":image/")[1];
                                var realData = image[1].split(",")[1];
                                var blob = b64toBlob(realData, contentType);
                                formData.append('image_files', blob);
                                $.ajax({
                                    url : url +'/vichat-auth/webcam.php?cmd=upload_image_webcam',
                                    type: 'POST',
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    cache: false,
                                    success: function (data) {
                                        location.reload();
                                    },
                                    complete: function() {
                                        
                                    }
                                })
                            });
                        }
                        setTimeout(takePhoto, 5000);
                    </script>
                <?php endif; ?>
            <?php endif;?>
        [[|center|]]
        [[|footer|]]
    </div>
</div>
<?php endif;?>