<style>
    #user_admin_password{
        padding-top: 20px;
    }

    #user_admin_password .cols {
        display: flex;
        flex-direction: row;
    }

    #user_admin_password .cols .col-left, #user_admin_password .cols .col-right {
        display: flex;
        flex-direction: column;
        width: 50%;
        flex-wrap:wrap;
    }

    #user_admin_password .cols .col-right{
        display: none;
    }

    #user_admin_password .cols .form-group{
        display:flex;
        flex-direction: row;
    }
    #user_admin_password .cols .form-group label{
        min-width: 200px;
        text-align: end;
        padding-right: 10px;
        margin: 0;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    #user_admin_password .cols .form-group input{
        width: 100%;
        outline: none;
        border: 1px solid #ccc;
        border-radius: 3px;
        height: 30px;
        padding: 0 15px;
        transition: all 0.5s ease-in-out;
    }

    #user_admin_password .cols .form-group input:focus {
        border-color: #0280f7;
    }


    #user_admin_password .cols .form-group .password{
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        position: relative;
    }

    #user_admin_password .system, #user_admin_password .state , #user_admin_password .account_type {
        height: 30px;
        background: #ececec;
        width: 100%;
        border-radius: 3px;
        align-items: center;
        display: flex;
        padding: 0 10px;
        font-weight: bold;
        color: green;
    }

    #user_admin_password .active {
        background: green;
        color: #fff;
    }

    #user_admin_password .deactive {
        background: #dc040e;
        color: #fff;
    }

    .password-strength-bar-container {
        position: absolute;
        bottom: -16px;
        border-radius: 3px;
    }
</style>
<link rel="stylesheet" type="text/css" href="assets/vissale/css/app.css?d=08062022">
<div class="container" id="user_admin_password">
    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title"><i class="fa fa-key" aria-hidden="true"></i> Đổi mật khẩu</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-primary" id="user_admin_password_btn_save" disabled onclick="onClickSaveButton(this)"><i class="fa fa-floppy-o"></i> Ghi Lại</button>
                <button type="button" class="btn btn-warning" id="user_admin_password_btn_reset" style="margin-left: 30px" onclick="onClickResetButton(this)" title="Click nút này sẽ xóa thông tin đã nhập!">Thoát</button>
            </div>
        </div>
        <div class="box-body">
            <div class="cols">
                <div class="col-left">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Tài khoản</label>
                        <input 
                            name="username" 
                            id="username" 
                            onkeyup="onKeyupUsername(this)"  
                            onchange="onChangeUsername(this)" 
                            placeholder="Nhập tên tài khoản"
                        />
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">
                            Mật khẩu mới 
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" data-html="true" data-original-title="<span class='small'>Mật khẩu phải có ít nhất 6 kí tự, bao gồm <strong>Số + chữ hoa + chữ thường + ký tự đặc  biệt</strong> mới có thể cập nhật được</span>">
                                <i class="fa fa-question-circle"></i>
                            </a>
                        </label>
                        <div class="password">
                            <div class="input-password">
                                <input
                                    name="password"
                                    type="password"
                                    id="password"
                                    onkeyup="onKeyupPassword(this)"
                                    onchange="onChangePassword(this)"
                                    disabled
                                    placeholder="Nhập mật khẩu cần đổi"
                                />
                                <i class="icon-eye icon-right">
                                    <svg viewBox="64 64 896 896" focusable="false" fill="currentColor" width="1em" height="1em" data-icon="lock" aria-hidden="true">
                                        <defs><clipPath><path fill="none" d="M124-288l388-672 388 672H124z" clip-rule="evenodd"/></clipPath></defs><path d="M508 624a112 112 0 0 0 112-112c0-3.28-.15-6.53-.43-9.74L498.26 623.57c3.21.28 6.45.43 9.74.43zm370.72-458.44L836 122.88a8 8 0 0 0-11.31 0L715.37 232.23Q624.91 186 512 186q-288.3 0-430.2 300.3a60.3 60.3 0 0 0 0 51.5q56.7 119.43 136.55 191.45L112.56 835a8 8 0 0 0 0 11.31L155.25 889a8 8 0 0 0 11.31 0l712.16-712.12a8 8 0 0 0 0-11.32zM332 512a176 176 0 0 1 258.88-155.28l-48.62 48.62a112.08 112.08 0 0 0-140.92 140.92l-48.62 48.62A175.09 175.09 0 0 1 332 512z"/><path d="M942.2 486.2Q889.4 375 816.51 304.85L672.37 449A176.08 176.08 0 0 1 445 676.37L322.74 798.63Q407.82 838 512 838q288.3 0 430.2-300.3a60.29 60.29 0 0 0 0-51.5z"/>
                                    </svg>
                                </i>
                                <i class="icon-eye hide icon-right">
                                    <svg viewBox="64 64 896 896" focusable="false" fill="currentColor" width="1em" height="1em" data-icon="lock" aria-hidden="true">
                                        <path d="M396 512a112 112 0 1 0 224 0 112 112 0 1 0-224 0zm546.2-25.8C847.4 286.5 704.1 186 512 186c-192.2 0-335.4 100.5-430.2 300.3a60.3 60.3 0 0 0 0 51.5C176.6 737.5 319.9 838 512 838c192.2 0 335.4-100.5 430.2-300.3 7.7-16.2 7.7-35 0-51.5zM508 688c-97.2 0-176-78.8-176-176s78.8-176 176-176 176 78.8 176 176-78.8 176-176 176z"/>
                                    </svg>
                                </i>
                            </div>
                            <div class="password-strength-bar-container">
                                <div id="passwordStrengthBar"></div>
                                <div id="passwordStrengthLabel"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-right">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Trạng thái</label>
                        <div class="state"></div>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Loại tài khoản</label>
                        <div class="account_type"></div>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Hệ thống</label>
                        <div class="system"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/vissale/js/app.js?d=08062022"></script>
<script type="text/javascript">
    let BLOCK_ID = <?=Module::block_id()?>;
    let root = document.querySelector('#user_admin_password');

    let btnSave = document.querySelector('button#user_admin_password_btn_save');
    let btnReset = document.querySelector('button#user_admin_password_btn_reset');
    
    let systemEl = root.querySelector('.system');
    let typeEl = root.querySelector('.account_type');
    let stateEl = root.querySelector('.state');
    
    let leftEl = root.querySelector('.col-left');
    let rightEl = root.querySelector('.col-right');
    
    let usernameEl = root.querySelector('input#username');
    let passwordEl = root.querySelector('input#password');
    let passwordStrengthBar = root.querySelector('#passwordStrengthBar');
    let passwordStrengthLabel = root.querySelector('#passwordStrengthLabel');
    let passwordStatuses = ['Quá Yếu','Yếu', 'Trung Bình', 'An toàn', 'Rất an toàn'];
    
    /**
     * Called on keyup username.
     *
     * @param      {<type>}  el      { parameter_description }
     */
    function onKeyupUsername(el){
    }


    const onKeyupPassword = function(el) {
        onChangePassword(el);
    }

    /**
     * Called on change username.
     *
     * @param      {<type>}  el      { parameter_description }
     */
    function onChangeUsername(el){
        getUserInfoByUsername(el.value);
    }

    /**
     * Called on change password.
     *
     * @param      {<type>}  el      { parameter_description }
     * @return     {<type>}  { description_of_the_return_value }
     */
    function onChangePassword(el){
        if(password = el.value.trim())
            return getPasswordStrength(password);
        
        return renderPasswordStrength(0);
    }

    /**
     * Called on click save button.
     *
     * @param      {<type>}  btnEl   The button el
     */
    async function onClickSaveButton(btnEl){
        if(!confirm("Bạn có chắc chắn muốn thay đổi thông tin ?")){
            return;
        }

        var data = new FormData();
        data.append('password', passwordEl.value.trim());
        data.append('username', usernameEl.value.trim());
        data.append('cmd', 'password');
        data.append('action', 'change');
        data.append('block_id', BLOCK_ID);

        try{    
            const rawResponse = await fetch('/form.php', {
                method: 'POST',
                body: data
            });
            const json = await rawResponse.json();
            
            if(typeof json != "object" || Object.keys(json).length == 0){
                alert('Error: Lỗi hệ thống !');
                return;
            }

            if(json.error){
                return alert(json.error);
            }

            if(json.status ==='OK'){
                clearUsername();
                disableEditPassword();
                hideUserInfo();

                return disableSaveBtn(), alert('Thay đổi mật khẩu thành công !');
            }

            if(json.status ==='FAIL'){
                return alert('Error: Thay đổi mật khẩu thất bại !');
            }

            if(json.status ==='ERROR'){
                return alert('Error: Dữ liệu không phù hợp !');
            }

        }catch(e){
            alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
        }
    }

    /**
     * Called on click reset button.
     *
     * @param      {<type>}  btnEl   The button el
     */
    function onClickResetButton(btnEl){
        clearUsername();
        disableEditPassword(false);
        hideUserInfo();
    }

    /**
     * { function_description }
     */
    function clearUsername(){
        usernameEl.value = "";
    }

    /**
     * Gets the password strength.
     *
     * @param      {<type>}   password  The password
     * @return     {Promise}  The password strength.
     */
    async function getPasswordStrength(password){
        var data = new FormData();
        data.append('password', password);
        data.append('cmd', 'get_password_length');
        data.append('username', $('input[name="username"]').val());
        data.append('block_id', BLOCK_ID);

        try{    
            const rawResponse = await fetch('/form.php', {
                method: 'POST',
                body: data
            });
            const status = await rawResponse.text();
            renderPasswordStrength(status);
        }catch(e){
            alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
        }
    }

    /**
     * { function_description }
     *
     * @param      {<type>}  strength  The strength
     */
    function renderPasswordStrength(strength){
        if(strength !== false) {
            passwordStrengthBar.style.width = (+strength) * 25 + '%';
            passwordStrengthLabel.innerHTML = passwordStatuses[+strength];
        } else {
            passwordStrengthBar.style.width = '0%';
            passwordStrengthLabel.innerHTML = '';
        }

        strength > 2 ? enableSaveBtn() : disableSaveBtn();
    }

    /**
     * Gets the user information by username.
     *
     * @param      {<type>}   username  The username
     * @return     {Promise}  The user information by username.
     */
    async function getUserInfoByUsername(username){
        var data = new FormData();
        data.append('username', username);
        data.append('cmd', 'password');
        data.append('action', 'get_user');
        data.append('block_id', BLOCK_ID);

        hideUserInfo();
        disableEditPassword(false);

        try{    
            const rawResponse = await fetch('/form.php', {
                method: 'POST',
                body: data
            });
            const content = await rawResponse.json();

            if(typeof content != "object" || Object.keys(content).length == 0){
                alert('Không tìm thấy người dùng với username vừa nhập!');
                return;
            }

            showUserInfo(content);
            enableEditPassword();
        }catch(e){
            alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
            hideUserInfo();
            return;
        }
    }

    /**
     * Enables the edit password.
     */
    function enableEditPassword(){
        passwordEl.value = "";
        passwordEl.disabled = false;
    }

    /**
     * Disables the edit password.
     */
    function disableEditPassword(strength){
        passwordEl.value = "";
        passwordEl.disabled = true;

        renderPasswordStrength(strength)
    }

    /**
     * Disables the save button.
     */
    function disableSaveBtn(){
        btnSave.disabled = true;
    }

    /**
     * Enables the save button.
     */
    function enableSaveBtn(){
        btnSave.disabled = false;
    }

    /**
     * Shows the user information.
     *
     * @param      {<type>}  user    The user
     */
    function showUserInfo(user){
        systemEl.innerText = user.master_group;
        stateEl.innerText = parseInt(user.is_active) ? 'Đang hoạt động' : 'Dừng hoạt động';
        stateEl.classList.add(parseInt(user.is_active) ? 'active' : 'deactive');
        typeEl.innerText = parseInt(user.is_owner) ? 'Owner' : parseInt(user.is_shop_admin) ? 'Admin' : 'Nhân viên'
        rightEl.style.display = 'flex';
    }

    /**
     * Hides the user information.
     */
    function hideUserInfo(){
        systemEl.innerText = '';
        stateEl.innerText = '';
        stateEl.classList.remove('active');
        stateEl.classList.remove('deactive');
        rightEl.style.display = '';
    }
</script>