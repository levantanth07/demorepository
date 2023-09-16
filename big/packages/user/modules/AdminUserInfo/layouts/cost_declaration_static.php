<style type="text/css">
    .error{ border-color: red; }
    [name="CostDeclaration"] span {
        display: block;
        font-size: 12px;
    }
    .pc-prefix:after {content: '%';position: absolute;top: 0;right: 0;height: 100%;line-height: 34px;padding: 0 8px;background: #d2d6de;border-top-right-radius: 3px;border-bottom-right-radius: 3px;}
    .pc-prefix {position: relative;}
</style>
<script type="text/javascript">
    $(function(){
        const CONFIRM_SAVE_MESSAGE        = 'Bạn chắc chắn muốn lưu ?';
        const CONFIRM_DELETE_ITEM_MESSAGE = 'Bạn chắc chắn muốn xóa khai báo này ?';
        const ALERT_INVALID_TIME_MESSAGE  = 'Vui lòng chọn thời gian từ tháng, năm hiện tại đến tương lai.';
        const MONTH_EL = $('select[name="month"]')
        const YEAR_EL = $('select[name="year"]')

        /**
         * Called on input type.
         */
        const onInputType = function()
        {
            let segments = this.value.replace(/[^\d\,\.]/g, '').match(/([\d\,]*)(\.)?(\d*)/);
            segments[1] = segments[1].replace(/[^\d]/g, '').split(/(?=(?:\d{3})+(?:\.|$))/g).join(',');
            this.value = segments[1] + (segments[2] || '') + (segments[3] || '')
        }

        /**
         * Called on submit form.
         *
         * @param      {<type>}  e       { parameter_description }
         * @return     {<type>}  { description_of_the_return_value }
         */
        const onSubmitForm = function(e)
        {
            try{
                // Cách viết này đảm bảo cả 2 hàm validate đều được chạy 
                // và nếu như có bất cứ kết quả false nào thì coi như fail  
                const bool = [isTimeValid(), isInputValid()].some(e => e == false);

                if(bool || !confirm(CONFIRM_SAVE_MESSAGE)){
                    return e.preventDefault();
                }
            }catch(err){
                e.preventDefault();
                console.log(err)
            }
        }

        /**
         * Called on delete button click.
         *
         * @param      {<type>}  e       { parameter_description }
         * @return     {<type>}  { description_of_the_return_value }
         */
        const onDeleteBtnClick = function(e)
        {
            if(!confirm(CONFIRM_DELETE_ITEM_MESSAGE)){
                return e.preventDefault();
            }
        }

        /**
         * Validate không cho phép thời gian nhỏ hơn năm tháng hiện tại
         *
         * @return     {boolean}  True if time valid, False otherwise.
         */
        const isTimeValid = function()
        {
            const currentDateObject = new Date();
            const year = YEAR_EL.val();
            const month = (MONTH_EL.val().length === 1 ? '0' : '') + MONTH_EL.val();
            
            const inputTime = year + month;
            const currentTime = currentDateObject.getFullYear() + '' + (currentDateObject.getMonth() + 1);

            if(inputTime - currentTime >= 0){
                MONTH_EL.removeClass('error')
                YEAR_EL.removeClass('error')

                return true;
            }
             
            alert(ALERT_INVALID_TIME_MESSAGE)
            MONTH_EL.addClass('error');
            YEAR_EL.addClass('error')
            
            return false;
        }

        /**
         * Validate không cho phép bỏ trống input
         *
         * @return     {boolean}  True if input valid, False otherwise.
         */
        const isInputValid = function()
        {   
            let bool = true;

            $('[name="CostDeclaration"] input.form-control').each(function(i, inputElement){
                const costValue = parseFloat(inputElement.value.trim());
                
                if(isNaN(costValue)){
                    return bool = false, inputElement.classList.add('error');
                }
                
                return inputElement.classList.remove('error');
            })

            return bool;
        }

        // Sự kiện bấm nút xóa 
        $('.btn-delete').on('click', onDeleteBtnClick);

        // Sự kiện nhập dữ liệu 
        $('[name="CostDeclaration"] input').on('input', onInputType)

        // Sự kiện submit form
        $('[name="CostDeclaration"]').on('submit', onSubmitForm)
    })
</script>