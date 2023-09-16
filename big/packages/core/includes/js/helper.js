const JSHELPER = (function () {
    const render = function () {
        /**
         * Builds tag attributes.
         *     {name: 'aaa', class: ['bbb', 'fff'], id: 'ccc'}
         * =>  name="aaa" class="bbb fff" id="ccc"
         *
         * @param      {<type>}  attrs   The attributes
         * @return     {<type>}  The tag attributes.
         */
        function buildTagAttrs(attrs) {
            return Object.keys(attrs)
                .map(function (propName) {
                    switch (true) {
                        case Array.isArray(attrs[propName]):
                            return `${propName}="` + attrs[propName].join(' ') + `"`;

                        case typeof attrs[propName] === 'function':
                            return `${propName}="` + attrs[propName]() + `"`;

                        case typeof attrs[propName] === 'boolean':
                            return attrs[propName] ? `${propName}` : '';

                        default:
                            return `${propName}="` + attrs[propName].toString() + '"';
                    }
                })
                .join(' ')
                .trim();
        }

        /**
         * Builds a raw tag.
         * Tạo một tag ở dạng text
         *
         * @param      {<type>}   tagName  The tag name
         * @param      {<type>}   attrs    The attributes
         * @param      {<type>}   html     The html
         * @return     {boolean}  The raw tag.
         */
        function buildRawTag(tagName, attrs, html) {
            const attrStr = buildTagAttrs(attrs);
            return `<${tagName}${attrStr ? ' ' + attrStr : ''}>${html}</${tagName}>`;
        }

        /**
         * Builds a tag.
         * Tạo một tag là instance của Element DOM
         *
         * @param      {<type>}  tagName  The tag name
         * @param      {<type>}  attrs    The attributes
         * @param      {<type>}  html     The html
         * @return     {<type>}  The tag.
         */
        function buildTag(tagName, attrs, html) {
            return buildElement(buildRawTag(tagName, attrs, html));
        }

        /**
         * Builds an element.
         *
         * @param      {<type>}  textHtml  The text html
         * @return     {<type>}  The element.
         */
        function buildElement(textHtml) {
            const __div__ = document.createElement(`div`);
            __div__.innerHTML = textHtml;

            return __div__.firstChild;
        }

        return {
            _mountHTML: '',

            select: function (_origin) {
                /**
                 * Cách sử dụng cơ bản JSHELPER.render.select(options).mount('#mountpoint');
                 * Trong đó: options là mảng hoặc object được định dạng 
                 * Object: {value1: 'html1 ', value2: 'html2 ', }
                 *      <option value="value1">html1</option>
                 *      <option value="value2">html2</option>
                 *      ...
                 *      
                 * Array: ['html1', 'html2', ...] 
                 *      <option value="0">html1</option>
                 *      <option value="1">html2</option>
                 *      ...
                 *
                 * Ngoài cách dùng trên còn có thể sử dụng cặp key value như sau 
                 * key: {prop: valprop, prop1: valprop1, html: 'val html'} 
                 *      <option value="key" prop="valprop" prop1="valprop1">val html</option>
                 *  
                 *  Tổng quát có thể xem 
                 * _origin = {
                    data: {
                        1: html, => <option value="1">html</option>
                        2: {                => <option value="2" selected="true" disabled="true">1111</option>
                            selected: true, => khi khai báo là object thì nó sẽ ghi đè thuộc tính ở optionsAttrs
                            disabled: true,
                            html: '1111', => thông thường build 1 item option đơn giản ta sẽ khai báo là    value: html => <option value="value">html</option>
                                          => Tuy nhiên nếu muốn thêm thuộc tính cho nó ta sẽ sử dụng kiểu   value: {html: 'gia tri html', prop1: 'value prop'}
                                          => <option value="value" prop1="value prop">gia tri html</option>
                        },
                        key: function(data, key){return 'dfsdf'} => tham số đầu tiên mà data object, tham số thứ 2 là key hiện tại 
                    },
                    selected: value, => Chỉ ra giá trị nào sẽ được selected, nó sẽ bị ghi đè bởi thẻ opotion khai báo selected  
                    selectAttrs: {}, => các thuộc tính cho thẻ select
                    optionsAttrs: {} => các thuộc tính mặc điịnh cho thẻ option, sẽ bị ghi đè bởi khai báo riêng của thẻ option
                 */

                /**
                 * Sets the selected.
                 *
                 * @param      {<type>}  _attrs  The attributes
                 * @return     {<type>}  { description_of_the_return_value }
                 */
                function setSelected(_attrs) {
                    // option hiện tại sẽ được selected nếu tồn tại giá trị trong mảng toàn cục
                    if (Array.isArray(origin.selected)) {
                        _attrs.selected = origin.selected.includes(_attrs.value);
                    }

                    // option hiện tại sẽ được selected nếu giá trị bằng giá trị toàn cục
                    else {
                        _attrs.selected = origin.selected === _attrs.value;
                    }

                    return _attrs;
                }

                const origin = _origin;
                const HTML_COLUMN = origin.HTML_COLUMN || 'html';
                const _data = origin.data;

                origin.selected = Array.isArray(origin.selected) && origin.selected.map(String);
                const optionTags = [];

                for (const [key, value] of Object.entries(_data)) {
                    let // điền thông tin thuộc tính toàn cục vào danh sách thuộc tính sẽ áp dụng cho nội
                        // bộ option
                        _attrs = { ...origin.optionsAttrs },
                        // html của option
                        _html = '';

                    switch (typeof value) {
                        case 'number':
                        case 'string':
                            _attrs.value = key;
                            setSelected(_attrs);
                            _html = value;
                            break;

                        case 'function':
                            // nếu html của option là 1 hàm thì invoke hàm
                            _html = value(_data, key);
                            _attrs.value = key;
                            setSelected(_attrs);
                            break;

                        default:
                            _attrs.value = key;
                            // Trích xuất html khỏi danh sách thuộc tính
                            _html = value[HTML_COLUMN] || '';
                            delete value[HTML_COLUMN];

                            // Nếu selected được khai báo cục bộ thì sử dụng khai báo cục bộ
                            if (value.hasOwnProperty('selected')) {
                                _attrs.selected = !!value.selected;
                                delete value.selected;
                            } else {
                                setSelected(_attrs);
                            }

                            // Ghi đè thuộc tính toàn cục được áp dụng cho option hiện tại
                            Object.assign(_attrs, value);
                    }

                    optionTags.push(buildRawTag('option', _attrs, _html));
                }

                this._mountHTML = buildTag('select', origin.selectAttrs, optionTags.join(''));

                return this;
            },

            /**
             * Gắn kết component vào DOM
             *
             * @param      {<type>}  el      { parameter_description }
             * @return     {<type>}  { description_of_the_return_value }
             */
            mount: function (el, beforeMount, mounted) {
                const mountPoint = document.querySelector(el);
                if (mountPoint) {
                    typeof beforeMount === 'function' && beforeMount(this._mountHTML);
                    mountPoint.replaceWith(this._mountHTML);
                    typeof mounted === 'function' && mounted(this._mountHTML);

                    return this._mountHTML;
                }

                console.log(`mount point "${el}" not found`);
            },
        };
    };

    return {
        render: new render(),
    };
})();
