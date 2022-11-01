'use strict';

class BoxberryDelivery
{
    constructor(params)
    {
        this.params = params;

        this.isPvzDelivery = false;
        this.checkedDelivery = document.querySelector('[name="DELIVERY_ID"]:checked');
        if (this.checkedDelivery && this.params.pvzDelivery && this.params.pvzDelivery.indexOf(this.checkedDelivery.value) >= 0) {
            this.isPvzDelivery = true;
        }

        this.addressField = document.querySelector(`[data-property-code=${this.params.addressPropCode}]`);

        this.init();
    }

    async post(data)
    {
        await fetch('/bitrix/js/up.boxberrydelivery/ajax.php', {
            method: 'POST',
            body: new URLSearchParams(data)
        });
    }

    init()
    {
        let buttonWrap, button;

        buttonWrap = document.getElementById(this.params.selectButtonWrap);

        if (!this.isPvzDelivery || !this.addressField || !buttonWrap) {
            return;
        }

        this.addressField.setAttribute('readonly', 'readonly');

        button = document.createElement('a');
        button.href = 'javascript:void(0)';
        button.addEventListener('click', Tool.proxy(function () {
            if (!window.boxberry) {
                return;
            }
            boxberry.checkLocation(1);
            boxberry.open(Tool.proxy(this.delivery, this), ...this.params.selectButtonParams);
        }, this));
        button.innerHTML = this.params.selectButtonContent;
        buttonWrap.appendChild(button);
    }

    delivery(result)
    {
        if (typeof result !== undefined) {
            this.post({save_pvz_id: result.id});
            this.addressField.value = `Boxberry: ${result.address} #${result.id}`;
            document.dispatchEvent(new CustomEvent('input:reload'));
            this.addressField.dispatchEvent(new Event('change', {bubbles: true}));
        }
    }
}
