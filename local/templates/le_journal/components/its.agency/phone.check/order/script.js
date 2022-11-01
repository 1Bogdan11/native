'use strict';

class OrderPhoneCheck
{
    ids = {
        message: '',
        counter: '',
        repeat: '',

        country: '',
        phone: '',
        code: '',

        changeButton: '',
        phoneButton: '',
        codeButton: '',

        phoneBlock: '',
        codeBlock: '',
        repeatBlock: '',
    };

    params = {
        devMode: false,
        sessId: '',
        sendCode: false,
        confirmPhone: false,
        checkedPhoneClass: '',
        errorCallback: function (message) {},
    };

    timer = null;
    timerEnable = true;
    timerCounter = 0;

    constructor(params, ids)
    {
        this.ids = ids;
        this.params = params;

        this.message = this.makeStateElementById(this.ids.message);
        this.counter = this.makeStateElementById(this.ids.counter);
        this.repeat = this.makeStateElementById(this.ids.repeat);

        if (this.message && this.counter && this.repeat) {
            this.timer = setInterval(Tool.proxy(this.timerAction, this), 1001);
        }

        this.country = this.makeStateElementById(this.ids.country);
        this.phone = this.makeStateElementById(this.ids.phone);
        this.code = this.makeStateElementById(this.ids.code);

        if (!this.phone || !this.code) {
            console.warn('Phone check: Phone or code input is not set!');
            return;
        }

        this.changeButton = this.makeStateElementById(this.ids.changeButton);
        this.phoneButton = this.makeStateElementById(this.ids.phoneButton);
        this.codeButton = this.makeStateElementById(this.ids.codeButton);

        if (!this.changeButton || !this.phoneButton || !this.codeButton) {
            console.warn('Phone check: Change, phone or code button is not set!');
            return;
        }

        this.phoneBlock = this.makeStateElementById(this.ids.phoneBlock);
        this.codeBlock = this.makeStateElementById(this.ids.codeBlock);
        this.repeatBlock = this.makeStateElementById(this.ids.repeatBlock);

        if (!this.phoneBlock || !this.codeBlock) {
            console.warn('Phone check: Phone or code block is not set!')
            return;
        }

        this.repeat.addEventListener('click', Tool.proxy(this.onClickPhoneButton, this));
        this.changeButton.addEventListener('click', Tool.proxy(this.onClickChangeButton, this));
        this.phoneButton.addEventListener('click', Tool.proxy(this.onClickPhoneButton, this));
        this.codeButton.addEventListener('click', Tool.proxy(this.onClickCodeButton, this));

        this.setBeginState();
    }

    setBeginState()
    {
        this.codeBlock.display(!!this.params.sendCode);
        if (this.repeatBlock) {
            this.repeatBlock.display(!!this.params.sendCode);
        }
        if (this.country) {
            this.country.readonly(!!this.params.sendCode || !!this.params.confirmPhone);
        }
        this.phone.readonly(!!this.params.sendCode || !!this.params.confirmPhone);
        this.phoneButton.display(!this.params.sendCode && !this.params.confirmPhone);
        this.changeButton.display(!!this.params.sendCode || !!this.params.confirmPhone);

        if (!!this.params.confirmPhone) {
            this.phone.classList.add(this.params.checkedPhoneClass);
        } else {
            this.phone.classList.remove(this.params.checkedPhoneClass);
        }

        if (!!this.params.confirmPhone) {
            this.showAdditionalFields();
        }
    }

    setChangeState()
    {
        this.codeBlock.display(false);
        if (this.repeatBlock) {
            this.repeatBlock.display(false);
        }
        if (this.country) {
            this.country.readonly(false);
        }
        this.phone.readonly(false);
        this.phoneButton.display(true);
        this.changeButton.display(false);

        this.phone.classList.remove(this.params.checkedPhoneClass);
    }

    setConfirmState()
    {
        this.codeBlock.display(false);
        if (this.repeatBlock) {
            this.repeatBlock.display(false);
        }
        if (this.country) {
            this.country.readonly(true);
        }
        this.phone.readonly(true);
        this.phoneButton.display(false);
        this.changeButton.display(true);

        this.phone.classList.add(this.params.checkedPhoneClass);

        this.showAdditionalFields();
    }

    setCodeState()
    {
        this.codeBlock.display(true);
        if (this.repeatBlock) {
            this.repeatBlock.display(true);
        }
        if (this.country) {
            this.country.readonly(true);
        }
        this.phone.readonly(true);
        this.phoneButton.display(false);
        this.changeButton.display(true);

        this.code.focus();
        this.phone.classList.remove(this.params.checkedPhoneClass);
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
                    this.closest(".js-input-phone").classList.add("is-readonly");
                } else {
                    this.removeAttribute('readonly')
                    this.closest(".js-input-phone").classList.remove("is-readonly");
                }
            };
        }
        return element;
    }

    timerAction()
    {
        if (!this.timerEnable) {
            return;
        }

        this.timerCounter--;

        if (this.timerCounter <= 0) {
            this.repeat.display(true);
            this.message.display(false);
            this.timerEnable = false;
        } else {
            this.counter.innerHTML = this.timerCounter.toString();
            this.repeat.display(false);
            this.message.display(true);
        }
    }

    getCode()
    {
        let data = new FormData();
        data.append('phone', this.phone.value);
        if (this.country) {
            data.append('country', this.country.value);
        }
        this.send('confirmCode', data);
    }

    confirmCode()
    {
        let data = new FormData();
        data.append('phone', this.phone.value);
        data.append('code', this.code.value);
        if (this.country) {
            data.append('country', this.country.value);
        }
        this.send('confirmCode', data);
    }

    send(action, data)
    {
        axios({
            url: '/bitrix/services/main/ajax.php',
            method: 'post',
            params: {
                c: 'its.agency:phone.check',
                action: action,
                mode: 'class',
                sessid: this.params.sessId,
            },
            data: data,
            timeout: 0,
            responseType: 'json',
        }).then(Tool.proxy(function (response) {
            if (this.params.devMode) {
                alert(JSON.stringify(response.data.data));
            }
            this.sendHandler(response.data.data);
        }, this));
    }

    sendHandler(response)
    {
        this.timerCounter = parseInt(response.timeout);
        if (this.timerCounter) {
            this.timerEnable = true;
        }

        if (response.state === 'error') {
            this.params.errorCallback(response.message);
        } else if (response.state === 'confirm') {
            this.setConfirmState();
        } else if (response.state === 'code') {
            this.setCodeState();
        }
    }

    onClickPhoneButton(event)
    {
        this.getCode();
    }

    onClickChangeButton(event)
    {
        this.setChangeState();
    }

    onClickCodeButton(event)
    {
        this.confirmCode();
    }

    showAdditionalFields()
    {
        let form, hidden, i;

        form = Tool.closest(this.phone, 'form');
        if (!form) {
            return;
        }

        hidden = form.querySelectorAll('[data-form-step]');
        if (hidden) {
            for (i = 0; i < hidden.length; i++) {
                hidden[i].classList.add('is-active');
            }
        }
    }
}
