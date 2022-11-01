'use strict';

class SubscribeElement {
    url = null;
    sessId = null;
    button = null;
    productId = 0;
    loc = [];
    params = [];
    subscribed = [];

    constructor(params) {
        this.params = params;
        this.loc = this.params.message;
        this.button = document.getElementById(this.params.buttonId);
        this.url = this.params.url;
        this.productId = this.params.productId;

        this.form = document.getElementById(this.params.formId);
        this.formWrap = document.getElementById(this.params.formWrapId);
        this.formModalId = this.params.formModalId;

        if (this.form) {
            this.form.addEventListener(
                'submit',
                this.proxy(this.onSubscribeFormSubmit, this)
            );
        }


        if (this.button) {
            this.button.addEventListener(
                'click',
                this.proxy(this.subscribe, this)
            );
        }

    }

    proxy(func, context) {
        return function () {
            return func.apply(context, arguments);
        };
    }

    setSubscribed(subscribed) {
        this.subscribed = subscribed;
    }

    init(sessId) {
        this.sessId = sessId;
        this.checkState();

        document.addEventListener(
            'custom_element_change_offer',
            this.proxy(this.onChangeOffer, this)
        );
    }

    onChangeOffer(event) {
        this.productId = parseInt(event.detail.id);
        this.checkState();
    }

    checkState() {
        if (!this.button || !this.form) {
            return;
        }

        if (this.isSubscribed()) {
            this.button.innerHTML = this.loc.subscribed;
            this.button.classList.add('is-added');
            this.form.classList.add('is-subscribed');
        } else {
            this.button.innerHTML = this.loc.subscribe;
            this.button.classList.remove('is-added');
            this.form.classList.remove('is-subscribed');
        }
    }

    isSubscribed() {
        return !!this.subscribed[this.productId];
    }

    subscribe(data, handler) {
        let inputsExtended;
        let fExtendedInputOk = true;


        if (this.isSubscribed()) {
            this.unsubscribe();
            return;
        }

        if (!(data instanceof FormData)) {
            data = new FormData();
            data.append('subscribe', 'Y');
        }

        data.append('sessid', this.sessId);
        data.append('itemId', this.productId);
        data.append('siteId', this.params.siteId);
        data.append('landingId', 0);

        inputsExtended = this.form.querySelectorAll('input[data-extended="true"]');
        for (let i = 0; i < inputsExtended.length; i++) {
            if (inputsExtended[i].value == ''){
                fExtendedInputOk = false;
            }
            data.append(
                inputsExtended[i].name,
                inputsExtended[i].value
            );
        }

        if (!fExtendedInputOk){
            return;//если не заполнено хоть одно расширенное св-во, то выход
        }

        //значение подписаться на расылку или нет
        let subscribe_emailing_list  = this.form.querySelector('input[name="subscribe_emailing_list"]');
        if (subscribe_emailing_list.checked) {
            data.append('subscribe_emailing_list', 'Y');
        }
        else{
            data.append('subscribe_emailing_list', 'N');
        }

        axios({
            url: this.url,
            method: 'post',
            params: {},
            data: data,
            timeout: 0,
            responseType: 'json',
        }).then(this.proxy(function (response) {
            if (typeof handler === 'function') {
                handler(response.data);
            } else {
                this.subscribeHandler(response.data);
            }
        }, this));
    }

    onSubscribeFormSubmit(event) {
        let data, inputs, i;

        inputs = this.formWrap.querySelectorAll('input');
        if (!inputs) {
            return;
        }

        data = new FormData();
        for (i = 0; i < inputs.length; i++) {
            data.append(
                'contact[' + inputs[i].name + '][user]',
                inputs[i].value
            );
        }
        data.append('manyContact', 'N');
        data.append('contactFormSubmit', 'Y');

        this.subscribe(data, this.proxy(this.onSubscribeFormSubmitHandler, this));

        event.preventDefault();
        event.stopPropagation();
    }

    onSubscribeFormSubmitHandler(response) {
        if (!response.error) {
            //modals.modalDisactivate(document.querySelector('.' + this.formModalId));
        }

        this.subscribeHandler(response);
    }

    subscribeHandler(response) {
        if (response.success) {
            this.subscribed[this.productId] = true;
            this.checkState();
        } else if (response.contactFormSubmit) {
            this.makeSubscribeForm(response);
            //callModal(document.querySelector('.' + this.formModalId)); //osman
        } else if (response.error) {
            if (response.hasOwnProperty('setButton')) {
                this.subscribed[this.productId] = true;
            }
            if (response.hasOwnProperty('typeName')) {
                response.message = response.message.replace('USER_CONTACT', response.typeName);
            }
            //initMessageModal(this.loc.error, response.message); //osman
        }
    }

    makeSubscribeForm(response) {
        let key, input, label;
        let labelNode, divNode;
        if (!response.hasOwnProperty('contactTypeData')) {
            return;
        }

        this.formWrap.innerHTML = '';
        for (key in response.contactTypeData) {
            label = response.contactTypeData[key].contactLable;
            input = document.createElement('input');
            input.setAttribute('name', key);
            input.setAttribute('type', label === 'Email' ? 'email' : 'text');
            //input.setAttribute('placeholder', label);
            input.setAttribute('required', '');

            //создадим labelHtml
            labelNode = document.createElement('label');
            labelNode.classList.add('input__label');
            labelNode.innerHTML = label;

            divNode = document.createElement('div');
            divNode.classList.add('input__bar');

            this.formWrap.append(input);
            this.formWrap.append(labelNode);
            this.formWrap.append(divNode);
        }
    }

    unsubscribe() {
        //initMessageModal(this.loc.error, this.loc.alreadySubscribed); //osman
    }
}
