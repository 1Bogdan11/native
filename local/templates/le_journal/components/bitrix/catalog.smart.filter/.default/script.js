'use strict';

class CatalogSmartFilter
{
    constructor(url, form, params, callback)
    {
        this.url = url;
        this.form = form;
        this.selector = params.selector;
        this.prefix = params.prefix;
        this.callback = callback;
        this.timeout = 500;
        this.timer = null;
        this.sef = false;
        this.delUrl = '?del_filter=Y';
        this.apply = document.getElementById(params.applyButton);

        if (params.SEF_SET_FILTER_URL) {
            this.sef = true;
            if (this.apply) {
                this.apply.setAttribute('data-url', params.SEF_SET_FILTER_URL);
            }
        }

        if (params.SEF_DEL_FILTER_URL) {
            this.delUrl = params.SEF_DEL_FILTER_URL;
        }
    }

    clear()
    {
        if (typeof this.callback === 'function') {
            this.callback(this.delUrl);
        }
    };

    change(input)
    {
        if (!!this.timer) {
            clearTimeout(this.timer);
        }
        this.timer = setTimeout(Tool.proxy(function () {
            this.reload(input);
        }, this), this.timeout);
    };

    reload(input)
    {
        let values, k, sections, elements;

        values = [{name: 'ajax', value: 'y'}];

        if (typeof this.selector === 'string') {
            elements = document.querySelectorAll(this.selector);
        } else {
            elements = document.querySelectorAll('#' + this.form + ' input');
        }

        if (elements) {
            this.gatherInputsValues(values, elements);
        }

        if (this.sef) {
            if (this.apply) {
                this.apply.setAttribute('disabled', 'disabled');
                this.apply.classList.add('disabled');
            }
        }

        sections = [];
        for (k = 0; k < values.length; k++) {
            if (values[k].name === 'sections[]') {
                sections.push('sections[]=' + values[k].value);
            }
        }

        axios({
            url: this.url,
            method: 'post',
            params: this.getValuesForPost(values),
            data: {},
            timeout: 0,
            responseType: 'json',
        }).then(Tool.proxy(function (response) {
            this.postHandler(response.data, sections.join('&'));
        }, this));
    };

    postHandler(result, additional)
    {
        let i, filterUrl;

        if (!!result && !!result.ITEMS) {
            for (i in result.ITEMS) {
                if (result.ITEMS.hasOwnProperty(i)) {
                    this.updateItem(result.ITEMS[i]);
                }
            }

            filterUrl = result.FILTER_URL;

            if (!!additional) {
                filterUrl = filterUrl + (filterUrl.indexOf('?') >= 0 ? '&' : '?') + additional;
            }

            if (this.apply) {
                this.apply.setAttribute('data-url', filterUrl);
            }

            if (typeof this.callback === 'function') {
                /*
                    result.FILTER_URL
                    result.ELEMENT_COUNT
                */
                this.callback(filterUrl, result);
            }
        }

        if (this.sef) {
            if (this.apply) {
                this.apply.removeAttribute('disabled');
                this.apply.classList.remove('disabled');
            }
        }
    };

    updateItem(items)
    {
        let i, item, input, count;

        if (items.VALUES) {
            for (i in items.VALUES) {
                if (items.VALUES.hasOwnProperty(i)) {
                    item = items.VALUES[i];

                    if (this.prefix) {
                        item.CONTROL_ID = this.prefix + item.CONTROL_ID;
                    }

                    input = document.getElementById(item.CONTROL_ID);
                    count = document.getElementById(item.CONTROL_ID + '_count');

                    if (input) {
                        if (item.DISABLED) {
                            // disable input
                            input.classList.add('disabled');
                        } else {
                            // enable input
                            input.classList.remove('disabled');
                        }
                    }

                    if (count){
                        if (item.hasOwnProperty('ELEMENT_COUNT')) {
                            count.innerHTML = item.ELEMENT_COUNT;
                        }
                    }


                }
            }
        }
    };

    gatherInputsValues(values, elements)
    {
        let i, j, element, name;

        if (elements) {
            for (i = 0; i < elements.length; i++) {
                element = elements[i];
                if (element.disabled || !element.type) {
                    continue;
                }

                name = !!this.prefix ? element.name.replace(this.prefix, '') : element.name;

                switch (element.type.toLowerCase()) {
                    case 'text':
                    case 'number':
                    case 'textarea':
                    case 'password':
                    case 'hidden':
                    case 'select-one':
                        if (element.value.length) {
                            values[values.length] = {name: name, value: element.value};
                        }
                        break;
                    case 'radio':
                    case 'checkbox':
                        if (element.checked) {
                            values[values.length] = {name: name, value: element.value};
                        }
                        break;
                    case 'select-multiple':
                        for (j = 0; j < element.options.length; j++) {
                            if (element.options[j].selected) {
                                values[values.length] = {name: name, value: element.options[j].value};
                            }
                        }
                        break;
                    default:
                        break;
                }
            }
        }
    };

    getValuesForPost(values)
    {
        let post = {};
        let current = post;
        let i = 0;

        while (i < values.length) {
            let p = values[i].name.indexOf('[');
            if (p === -1) {
                current[values[i].name] = values[i].value;
                current = post;
                i++;
            } else {
                let name = values[i].name.substring(0, p);
                let rest = values[i].name.substring(p + 1);
                if (!current[name]) {
                    current[name] = [];
                }
                let pp = rest.indexOf(']');
                if (pp === -1) {
                    // Error - not balanced brackets
                    current = post;
                    i++;
                } else if (pp === 0) {
                    // No index specified - so take the next integer
                    current = current[name];
                    values[i].name = '' + current.length;
                } else {
                    // Now index name becomes and name and we go deeper into the array
                    current = current[name];
                    values[i].name = rest.substring(0, pp) + rest.substring(pp + 1);
                }
            }
        }
        return post;
    };
}
