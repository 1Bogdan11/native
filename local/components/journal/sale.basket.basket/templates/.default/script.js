'use strict';

class Basket {
    wrap = null;
    wrapId = '';
    params = {};
    selectors = {};
    quantityTimer = null;
    quantityTimeout = 500;

    constructor(wrapId, params, selectors)
    {
        this.wrapId = wrapId;
        this.params = params;
        this.selectors = selectors;
        this.wrap = document.getElementById(this.wrapId);

        this.wrap.addEventListener('click', Tool.proxy(function (event) {
            if (Tool.closest(event.target, this.selectors.basketRemove)) {
                return this.removeEvent(event);
            } else if (Tool.closest(event.target, this.selectors.basketQuantityMinus)) {
                return this.quantityMinus(event);
            } else if (Tool.closest(event.target, this.selectors.basketQuantityPlus)) {
                return this.quantityPlus(event);
            } else if (Tool.closest(event.target, this.selectors.basketRemoveAll)) {
                return this.removeAll(event);
            }
        }, this));
        this.wrap.addEventListener('change', Tool.proxy(function (event) {
            if (Tool.closest(event.target, this.selectors.basketOffer)) {
                return this.setOfferEvent(event);
            } else if (Tool.closest(event.target, this.selectors.basketPromoCode)) {
                return this.addPromoCodeEvent(event);
            }
        }, this));
        this.wrap.setAttribute("data-inited", true)
        document.addEventListener('custom_basket_reload', Tool.proxy(this.loadBasket, this));
    }

    addPromoCodeEvent(event)
    {
        let input = document.querySelector(this.selectors.basketPromoCode);
        showPreloader(this.wrap);
        axios({
            url: '/bitrix/services/main/ajax.php?action=its:maxma.api.coupon.set',
            method: 'post',
            params: {
                SITE_ID: this.params.site,
                sessid: this.params.sessid,
                coupon: input.value,
            },
            data: {},
            timeout: 0,
        }).then(Tool.proxy(function () {
            this.loadBasket(false);
        }, this));
    }

    removeEvent(event) {
        let itemId;
        event.preventDefault();
        itemId = Tool.closest(event.target, '[data-item]').getAttribute('data-item');
        this.sendAction('DELETE_' + itemId, 'Y');
    }

    quantityMinus(event) {
        let count, current, itemId;
        current = Tool.closest(event.target, this.selectors.basketQuantity)
            .querySelector(this.selectors.basketQuantityCount);
        count = parseInt(current.innerHTML);
        itemId = Tool.closest(event.target, '[data-item]').getAttribute('data-item');
        count--;
        current.innerHTML = count;

        if (count) {
            clearTimeout(this.quantityTimer);
            this.quantityTimer = setTimeout(Tool.proxy(function () {
                this.sendAction('QUANTITY_' + itemId, count);
            }, this), this.quantityTimeout);
        } else {
            this.removeEvent(event);
        }
    }

    quantityPlus(event) {
        let count, current, itemId;
        current = Tool.closest(event.target, this.selectors.basketQuantity)
            .querySelector(this.selectors.basketQuantityCount);
        count = parseInt(current.innerHTML);
        itemId = Tool.closest(event.target, '[data-item]').getAttribute('data-item');
        count++;
        current.innerHTML = count;
        clearTimeout(this.quantityTimer);
        this.quantityTimer = setTimeout(Tool.proxy(function () {
            this.sendAction('QUANTITY_' + itemId, count);
        }, this), this.quantityTimeout);
    }

    loadBasket(loader) {
        if (!!loader) {
            showPreloader(this.wrap);
        }
        axios({
            url: this.params.currentPage,
            method: 'post',
            params: {ajax_basket: 'Y'},
            data: {},
            timeout: 0,
            responseType: 'text',
        }).then(Tool.proxy(function (response) {
            hidePreloader(this.wrap);
            this.loadBasketHandler(response.data);
        }, this));
    }

    loadBasketHandler(response) {
        let parser, html;
        parser = new DOMParser();
        html = parser.parseFromString(response, 'text/html');
        this.wrap.innerHTML = html.getElementById(this.wrapId).innerHTML;
        document.dispatchEvent(
            new CustomEvent(
                'custom_basket_reload_complete',
                {detail: {wrap: this.wrapId}}
            )
        );
        document.dispatchEvent(new CustomEvent("select:update"));
    }

    sendAction(parameter, value) {
        let data;
        data = new FormData();
        data.append('lastAppliedDiscounts', true);
        data.append('site_id', this.params.site);
        data.append('sessid', this.params.sessid);

        //особенный случай для вызова нашего метода по очистки корзины
        if (parameter == 'removeall') {
            data.append(this.params.actionVariable, 'removeall'); //так работате метод remove
        } else if (typeof parameter !== 'undefined') {
            data.append(this.params.actionVariable, 'recalculateAjax');
            data.append('basket[' + parameter + ']', value);
        }

        showPreloader(this.wrap);
        axios({
            url: this.params.url,
            method: 'post',
            params: {},
            data: data,
            timeout: 0,
            responseType: 'json',
        }).then(Tool.proxy(function (response) {
            if (response.data.BASKET_DATA.WARNING_MESSAGE.length > 0) {
                console.log(response.data.BASKET_DATA.WARNING_MESSAGE.join('\n'));
                showMessage({
                    title: this.params.messageError,
                    text: response.data.BASKET_DATA.WARNING_MESSAGE.join('\n'),
                    time: 10000,
                });
            }
            this.loadBasket(false);
        }, this));
    };

    setOfferEvent(event) { //тут работа через select

        event.preventDefault();
        if (!event.target.value) {
            return;
        }
        let optionSelected = event.target.selectedOptions[0];
        let itemId = event.target.getAttribute('data-item');
        let propCode = optionSelected.getAttribute('data-property');
        let offerId = optionSelected.getAttribute('data-offer');
        let value = event.target.value;
        let data = {};

        if (value && value.length) {
            data['props[' + propCode + ']'] = value;
            this.sendActionD7('select_item', itemId, data);
        } else {
            data.basketOfferId = offerId;
            this.sendActionD7('select_item_without_props', itemId, data);
        }
    };

    sendActionD7(action, itemId, data) {

        data.basketItemId = itemId;
        data.sessid = this.params.sessid;
        data.site_id = this.params.site;
        data.action_var = this.params.actionVariable;

        data[this.params.actionVariable] = action;

        showPreloader(this.wrap);

        axios({
            url: this.params.url,
            method: 'post',
            params: {},
            data: new URLSearchParams(data).toString(),
            timeout: 0,
            responseType: 'json',
        }).then(Tool.proxy(function (response) {
            if (response.data.BASKET_DATA.WARNING_MESSAGE.length > 0) {
                console.error(response.data.BASKET_DATA.WARNING_MESSAGE.join('\n'));
                showMessage({
                    title: this.params.messageError,
                    text: response.data.BASKET_DATA.WARNING_MESSAGE.join('\n'),
                    time: 10000,
                });

            } else {
                this.loadBasket(false);
            }
        }, this));
    };

    removeAll(event) {
        event.preventDefault();
        this.sendAction('removeall', '');
    }
}
