<style type="text/css">
    .normal-text{font-style: normal; color: #333; font-weight: normal}
    .thumb-wrapper {display: flex; }
    .thumb {    width: 200px; min-height: 80px; overflow: hidden; border-radius: 3px; margin: 0 20px 0 0; justify-content: center; align-items: center; display: flex; box-shadow: 0 0 3px 0 #c1c1c1; background: #f3f3f3;}
    img {display: block; max-width: 100%; }

    .suggest{
        position: relative;
        display: none;
    }

    .suggest ul , .suggest ul li{
        display: flex;
        flex-direction: column;
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .suggest ul li{
        flex-direction: row;
        padding: 3px 10px;
    }

    .suggest ul{
        background: #fff;
        width: 100%;
        position: absolute;
        border-bottom: none;
        max-height: 145px;
        overflow-y: auto;
        border: 1px solid #ccc;
        border-radius: 3px;
        margin-top: 2px;
    }

    .suggest ul li:not(:last-child){
        /*border-bottom: 1px solid #ccc;*/
    }

    .suggest ul li{
        border-left: 3px solid #ccc;
        align-items:  center;
        font-size:  10px;
        color:  #ccc;
    }
    .suggest ul li b{
        font-size: 13px;
        color: #333;
        margin: 0 5px;
    }
    .suggest ul li img{
        width: 40px;
        height: 30px;
        margin:  0 5px;
   }
</style>
<script type="text/javascript">
    const MASTER_PRODUCTS = <?=json_encode($this->map['master_products'])?>;

    function hideSuggest(el){
        el.style.display = 'none';
    }

    function clearSuggest(el){
        el.innerHTML = '';
    }

    function showSuggest(el){
        el.style.display = 'block';
    }

    function suggest(selector, items, field)
    {
        let el = document.querySelector(selector);
        let suggest = document.querySelector('.suggest[for=' + field + ']');
        let suggestUl = document.querySelector('.suggest[for=' + field + '] > ul');

        if(!el || !items || !items.length || !suggest || !suggestUl ) return;

        $('body').click(function(e){
            hideSuggest(suggest);
        })

        let oldTerm = '';
        el.addEventListener('keyup', function(e){
            const term = this.value.trim();

            if(term === oldTerm){
                return;
            }

            oldTerm = term;

            // Ẩn suggest nếu từ khóa nhập vào nhỏ hơn 3 kí tự
            if(!term || term.length < 2){
                return hideSuggest(suggest);
            }

            // Lọc các sp khớp với từ đang nhập
            const matchItems = items.filter(item => {
                return item[field].match(new RegExp(term, 'i'))
            });

            // Ẩn gợi ý nếu không có sp nào match
            if(!matchItems.length) return hideSuggest(suggest);

            // cảnh báo trùng
            let isUnique = matchItems.reduce((isUnique, item) => {
                return isUnique *= (item[field].toLowerCase() === term.toLowerCase());
            }, 1);
            this.style['border-color'] = isUnique ? 'red' : '';


            // Hiển thị suggest
            suggestUl.innerHTML = matchItems.map(item => `<li onclick="event.stopPropagation()" name="suggest">
                                                 <img src="${item.image || '/assets/standard/images/no_image.png'}" />
                                                 • <b>${item.code}</b> • <b>${item[field]}</b></li>
                                                 `).join("\n");
            showSuggest(suggest);
        })
    }

    Number.prototype.format = function(n, x, s, c) {
        var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
            num = this.toFixed(Math.max(0, ~~n));

        return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
    };

    $('input[id="cost_price"]').val(($('input[id="cost_price"]').val() - 0).format(0, 3, ',', '.'))
    $('input[id="cost_price"]').keyup(function(){
        this.value = this.value.replace(/[^\d\.]/g, '')
        this.value = (this.value - 0).format(0, 3, ',', '.')
        // this.setSelectionRange(0,0);
    });

    $('#weight').on('change', function(e) {
	    $(e.target).val($(e.target).val().replace(/[^\d]/g, ''))
    });
    $('#weight').on('keypress', function(e) {
	    keys = ['0','1','2','3','4','5','6','7','8','9']
	    return keys.indexOf(event.key) > -1
    });

    suggest('input[id="name"]', MASTER_PRODUCTS, 'name')
    suggest('input[id="full_name"]', MASTER_PRODUCTS, 'full_name')
</script>
