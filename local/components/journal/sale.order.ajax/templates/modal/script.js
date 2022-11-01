'use strict';

class ModalOrder
{
    form = null;
    formId = '';
    params = {};

    disable = false;

    constructor(formId, params)
    {
        this.formId = formId;
        this.params = params;
        this.form = document.getElementById(this.formId);

        this.form.addEventListener('change', Tool.proxy(function (event) {
            let button = Tool.closest(event.target, '[data-order-reload]');
            if (button) {
                event.preventDefault();
                event.stopPropagation();
                this.reload(button.dataset.orderReload);
            }
            let selector = Tool.closest(event.target, '[data-go-select]');
            if (selector) {
                document.dispatchEvent(new CustomEvent("ordering:changeBefore"));
                this.send(false, selector.value);
            }
        }, this));

        this.form.addEventListener('click', Tool.proxy(function (event) {
            let button = Tool.closest(event.target, '[data-order-submit]');
            if (button) {
                event.preventDefault();
                event.stopPropagation();
                this.submit(button.dataset.orderSubmit, true);
            }
        }, this));

        this.form.addEventListener('click', Tool.proxy(function (event) {
            let button = Tool.closest(event.target, '[data-order-go]');
            if (button){
                event.preventDefault();
                event.stopPropagation();
                const requiredFields = document.querySelectorAll(`[data-order-step-field="${button.dataset.orderStep}"]`)
                const emptyFields = [];
                let validate;
                requiredFields.forEach((field) => {
                    validate = field.querySelector(".validate");
                    if(!validate || (validate.classList.contains("is-filled") && !validate.classList.contains("is-error"))){
                        emptyFields.push(field);
                    }
                    else{
                        field.classList.add("is-invalid")
                        setTimeout(() => {
                            field.classList.remove("is-invalid")
                        }, 500)
                    }
                })
                if(requiredFields.length === emptyFields.length){
                    this.send(false, button.dataset.orderGo);
                    document.dispatchEvent(new CustomEvent("ordering:changeBefore"));
                }
            }
        }, this));
    }

    reload(step, animate)
    {
        this.send(false, step, animate);
    }

    submit(step, animate)
    {
        this.send(true, step, animate);
    }

    send(confirm, step, animate)
    {
        let data;

        if (this.disable) {
            return;
        }

        if (animate)
            document.dispatchEvent(new CustomEvent('ordering:changeBefore'));
        else
            document.dispatchEvent(new CustomEvent('ordering:reloadBefore'));

        this.disable = true;

        data = new FormData(this.form);
        data.append('profile_change', 'N');
        data.append('order_ajax', 'Y');
        data.append('json', 'Y');

        if (!!confirm) {
            data.append('confirmorder', 'Y');
        }

        if (typeof step === 'string' && step.trim().length > 0) {
            data.append('step', step.trim());
        }

        axios({
            url: this.params.currentPage,
            method: 'post',
            params: {},
            data: data,
            timeout: 0,
            responseType: 'text',
        }).then(Tool.proxy(function (response) {
            setTimeout(() => {
                this.sendHandler(response.data);
                document.dispatchEvent(new CustomEvent("ordering:changeAfter", { detail: response.data }));
            }, 500)
            
        }, this));
    }

    sendHandler(response, animate)
    {
        
        let json, parser, html;
        try {
            json = typeof response !== 'object' ? JSON.parse(response) : response;
            if (json.error) {
                this.disable = false;
            } else if (json.redirect) {
                document.dispatchEvent(new CustomEvent('modal:close', {
                    detail: {
                        name: 'ordering-modal',
                    }
                }));
                document.dispatchEvent(new CustomEvent('modal:load', {
                    detail: {
                        url: json.redirect,
                    }
                }));
                this.disable = false;
            }
        } catch (e) {
            this.disable = false;

            parser = new DOMParser();
            html = parser.parseFromString(response, 'text/html');
            this.form.innerHTML = html.getElementById(this.formId).innerHTML;
            Tool.evalScripts(this.form);
        }

        if (!animate)
            document.dispatchEvent(new CustomEvent('ordering:reloadAfter'));
    }
}
