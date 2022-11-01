'use strict';

class PhoneAuth
{
    ids = {
        wrap: '',

        message: '',
        counter: '',
        repeat: '',

        country: '',
        phone: '',
        code: '',

        backUrl: '',
        numberView: '',

        phoneBlock: '',
        codeBlock: '',
        formBlock: '',
        successBlock: '',
        repeatBlock: '',

        phoneButton: '',
        formButton: '',
        changeButton: '',
        codeButton: '',
        resetButton: '',
    };

    params = {
        sessId: '',
        currentUrl: '',
        errorCallback: function (message) {},
    };

    timer = null;
    timerEnable = true;
    timerCounter = 0;

    constructor(params, ids)
    {
        this.ids = ids;
        this.params = params;

        this.timer = setInterval(Tool.proxy(this.timerAction, this), 1001);
        this.wrap = document.getElementById(this.ids.wrap);

        if (!this.wrap) {
            return;
        }

        this.wrap.addEventListener('click', Tool.proxy(this.onClick, this));
        this.resetState();
    }

    makeStateElementById(id)
    {
        let element = document.getElementById(id);
        if (element) {
            element.display = function (set) {
                if (!!set) {
                    this.removeAttribute('style');
                } else {
                    this.style.display = 'none';
                }
            };
            element.readonly = function (set) {
                if (!!set) {
                    this.setAttribute('readonly', 'readonly')
                } else {
                    this.removeAttribute('readonly')
                }
            };
        }
        return element;
    }

    timerAction()
    {
        let message, counter, repeat;

        if (!this.timerEnable) {
            return;
        }

        this.timerCounter--;

        message = this.makeStateElementById(this.ids.message);
        counter = this.makeStateElementById(this.ids.counter);
        repeat = this.makeStateElementById(this.ids.repeat);

        if (!message || !counter || !repeat) {
            return;
        }

        if (this.timerCounter <= 0) {
            repeat.display(true);
            message.display(false);
            this.timerEnable = false;
        } else {
            counter.innerHTML = this.timerCounter.toString();
            repeat.display(false);
            message.display(true);
        }
    }

    onClick(event)
    {
        let element;

        element = Tool.closest(event.target, `#${this.ids.resetButton}`);
        if (element) {
            event.preventDefault();
            this.reload(Tool.setParamToLink(this.params.currentUrl, 'change_number', 'Y'));
            return;
        }

        element = Tool.closest(event.target, `#${this.ids.formButton}`);
        if (element) {
            event.preventDefault();
            this.reload(this.params.currentUrl, this.ids.formBlock);
            return;
        }

        element = Tool.closest(event.target, `#${this.ids.phoneButton}`);
        if (element) {
            if (element.disabled) {
                return;
            }
            event.preventDefault();
            this.getCode();
            return;
        }

        element = Tool.closest(event.target, `#${this.ids.changeButton}`);
        if (element) {
            event.preventDefault();
            this.resetState();
            return;
        }

        element = Tool.closest(event.target, `#${this.ids.repeat}`);
        if (element) {
            event.preventDefault();
            this.getCode();
            return;
        }

        element = Tool.closest(event.target, `#${this.ids.codeButton}`);
        if (element) {
            if (element.disabled) {
                return;
            }
            event.preventDefault();
            this.confirmCode();
            return;
        }
    }

    getCode()
    {
        let data, phone, country;

        country = document.getElementById(this.ids.country);
        phone = document.getElementById(this.ids.phone);
        if (!phone) {
            return;
        }

        data = new FormData();
        data.append('country', country.value);
        data.append('phone', phone.value);
        this.send('confirmCode', data);
    }

    confirmCode()
    {
        let data, phone, code, country;

        country = document.getElementById(this.ids.country);
        phone = document.getElementById(this.ids.phone);
        code = document.getElementById(this.ids.code);
        if (!phone || !code) {
            return;
        }

        data = new FormData();
        data.append('country', country.value);
        data.append('phone', phone.value);
        data.append('code', code.value);
        this.send('confirmCode', data);
    }

    resetState()
    {
        let phoneBlock, codeBlock, repeatBlock;

        phoneBlock = this.makeStateElementById(this.ids.phoneBlock);
        codeBlock = this.makeStateElementById(this.ids.codeBlock);
        repeatBlock = this.makeStateElementById(this.ids.repeatBlock);

        if (!phoneBlock || !codeBlock) {
            return;
        }

        phoneBlock.display(true);
        codeBlock.display(false);

        if (repeatBlock) {
            repeatBlock.display(false);
        }
    }

    reload(url, formId)
    {
        let data;
        data = formId ? new FormData(document.getElementById(formId)) : {};
        showPreloader(this.wrap);
        axios({
            url: url,
            method: 'post',
            params: {redirect_from_json: 'Y'},
            data: data,
            timeout: 0,
            responseType: 'text',
        }).then(Tool.proxy(function (response) {
            hidePreloader(this.wrap);
            this.reloadHandler(response.data);
        }, this));
    }

    reloadHandler(response)
    {
        let backUrl,
            phoneBlock, codeBlock, formBlock, successBlock, repeatBlock,
            parser, html, wrap;

        if (typeof response === 'object') {
            backUrl = document.getElementById(this.ids.backUrl);
            if (backUrl) {
                backUrl.setAttribute('href', response.redirect);
            }

            successBlock = this.makeStateElementById(this.ids.successBlock);
            if (successBlock) {
                successBlock.display(true);
            }

            phoneBlock = this.makeStateElementById(this.ids.phoneBlock);
            if (phoneBlock) {
                phoneBlock.display(false);
            }

            codeBlock = this.makeStateElementById(this.ids.codeBlock);
            if (codeBlock) {
                codeBlock.display(false);
            }

            formBlock = this.makeStateElementById(this.ids.formBlock);
            if (formBlock) {
                formBlock.display(false);
            }

            repeatBlock = this.makeStateElementById(this.ids.repeatBlock);
            if (repeatBlock) {
                repeatBlock.display(false);
            }
            return;
        }

        parser = new DOMParser();
        html = parser.parseFromString(response, 'text/html');
        wrap = html.getElementById(this.ids.wrap);

        if (wrap) {
            this.wrap.innerHTML = wrap.innerHTML;
            Tool.evalScripts(this.wrap);
            document.dispatchEvent(
                new CustomEvent('custom_phone_auth_ajax_loaded', {detail: {wrap: this.ids.wrap}})
            )
        }
    }

    send(action, data)
    {
        showPreloader(this.wrap);
        axios({
            url: '/bitrix/services/main/ajax.php',
            method: 'post',
            params: {
                c: 'its.agency:phone.auth',
                action: action,
                mode: 'class',
                sessid: this.params.sessId,
            },
            data: data,
            timeout: 0,
            responseType: 'json',
        }).then(Tool.proxy(function (response) {
            hidePreloader(this.wrap);
            if (this.params.devMode) {
                alert(JSON.stringify(response.data.data));
            }
            this.sendHandler(response.data.data);
        }, this));
    }

    sendHandler(response)
    {
        let numberView, phone, phoneBlock, codeBlock, repeatBlock;

        this.timerCounter = parseInt(response.timeout);
        this.timerEnable = true;

        if (response.state === 'error') {
            this.params.errorCallback(response.message);
        } else if (response.state === 'confirm') {
            this.reload(this.params.currentUrl);
        } else if (response.state === 'code') {
            numberView = document.getElementById(this.ids.numberView);
            phone = document.getElementById(this.ids.phone);
            phoneBlock = this.makeStateElementById(this.ids.phoneBlock);
            codeBlock = this.makeStateElementById(this.ids.codeBlock);
            repeatBlock = this.makeStateElementById(this.ids.codeBlock);

            if (numberView && phone && phoneBlock && codeBlock) {
                numberView.innerHTML = phone.value;
                phoneBlock.display(false);
                codeBlock.display(true);

                if (repeatBlock) {
                    repeatBlock.display(true);
                }
            }
        }
    }
}
