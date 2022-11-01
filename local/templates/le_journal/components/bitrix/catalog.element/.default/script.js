'use strict';

class CatalogElement
{
    params = {};
    elements = {};

    constructor(params, elements)
    {
        this.params = params;
        this.elements = elements;
        this.isOffers = params.isHaveOffers;
        this.priceWrap = document.getElementById(elements.price);
        this.priceOldWrap = document.getElementById(elements.priceOld);
        this.buyButton = document.getElementById(elements.buyButton);
        this.subscribeButton = document.getElementById(elements.subscribeButton);
        this.setWrap = document.getElementById(elements.setWrap);
        this.selectedSize = document.getElementById(elements.selectedSize);
        this.availableWrap = document.getElementById(elements.availableWrap);
        this.availableMobileWrap = document.getElementById(elements.availableMobileWrap);
        this.oneClickButton = document.getElementById(elements.oneClickButton);

        if (this.isOffers) {
            this.currentOffer = parseInt(this.params.selectedOffer);
            this.offersCount = this.params.offersCount;
            this.offers = this.params.offers;
            this.matrix = {};

            this.offerWrap = document.getElementById(elements.offers);
            if (this.offerWrap) {
                this.offerWrap.addEventListener('click', Tool.proxy(this.onOfferClick, this));
                this.offerValues = this.offerWrap.querySelectorAll('[data-value]');

                this.modalOfferValues = document.querySelectorAll('[data-modal-property][data-modal-value]')
                if (this.modalOfferValues) {
                    for (let i = 0; i < this.modalOfferValues.length; i++) {
                        this.modalOfferValues[i].addEventListener('click', Tool.proxy(this.onModalOfferClick, this))
                    }
                }

                this.selectOffer(
                    this.offers[this.currentOffer].selectedTree.property,
                    this.offers[this.currentOffer].selectedTree.value
                );
            }
        } else {
            axios({
                url: '/local/ajax/ga4_item.php',
                method: 'get',
                params: {
                    id: this.params.id,
                    siteId: this.params.siteId,
                },
                data: {},
                timeout: 0,
                responseType: 'json',
            }).then((response) => {
                response = response.data;
                if (response.success) {
                    ga4('view_item', {items: [response.data]});
                }
            });
        }

        if (this.buyButton) {
            this.buyButton.addEventListener('click', Tool.proxy(this.eventAddToBasket, this));
            document.addEventListener('custom_site_data_update', Tool.proxy(this.updateBuyButton, this));
            this.updateBuyButton();
        }

        if (this.setWrap) {
            this.setWrap.addEventListener('click', Tool.proxy(function (event) {
                let button = Tool.closest(event.target, '[data-set-add-to-basket]');
                if (button) {
                    this.loadSet(button.dataset.setAddToBasket);
                }
            }, this));

            document.addEventListener('custom_element_change_offer', Tool.proxy(function () {
                this.loadSet(false, true);
            }, this));

            document.addEventListener('custom_element_set_offer_change', Tool.proxy(function () {
                this.loadSet(false);
            }, this));

            document.addEventListener('custom_basket_reload_complete', Tool.proxy(function () {
                this.loadSet(false);
            }, this));

            this.loadSet(false, true);
        }

        this.sendViewedCounter();
    }

    loadSet(addSetToBasket, ignoreSelected)
    {
        let params = {}, props, i, item;
        item = this.isOffers ? this.offers[this.currentOffer] : this.params;

        if (!item.available) {
            return;
        }

        showPreloader(this.setWrap);

        params.id = this.params.id;
        params.siteId = this.params.siteId;

        if (this.isOffers && !!ignoreSelected) {
            params.parent_element = this.params.id;
            params.parent_offer_selected = this.offers[this.currentOffer].id;
        }

        props = this.setWrap.querySelectorAll('[data-offer-selected]');
        if (props) {
            for (i = 0; i < props.length; i++) {
                params[props[i].getAttribute('name')] = props[i].value;
            }
        }

        if (parseInt(addSetToBasket) > 0) {
            params.add_to_basket = parseInt(addSetToBasket);
        }

        axios({
            url: '/local/ajax/element_set.php',
            method: 'post',
            params: params,
            data: {},
            timeout: 0,
            responseType: 'text',
        }).then(Tool.proxy(function (response) {
            this.loadSetHandler(response.data);
            if (params.add_to_basket) {
                document.dispatchEvent(new CustomEvent('custom_add_to_basket'));
            }
            document.dispatchEvent(new CustomEvent('scroll:update'));
        }, this));
    }

    loadSetHandler(html)
    {
        this.setWrap.innerHTML = html;
        Tool.evalScripts(this.setWrap);
        hidePreloader(this.setWrap);
    }

    updateOfferItem()
    {
        if (Tool.isEmpty(this.offers[this.currentOffer])) {
            return;
        }

        let offer;

        offer = this.offers[this.currentOffer];
        this.priceWrap.innerHTML = offer.price;
        this.priceOldWrap.innerHTML = offer.priceOld;

        if (offer.available) {
            this.priceWrap.removeAttribute('style');
            this.priceOldWrap.removeAttribute('style');
        } else {
            this.priceWrap.style.display = 'none';
            this.priceOldWrap.style.display = 'none';
        }

        this.availableWrap.innerHTML = offer.available ? this.params.messageAvailable : this.params.messageNotAvailable;
        this.availableMobileWrap.innerHTML = offer.available ? this.params.messageAvailable : this.params.messageNotAvailable;

        document.dispatchEvent(new CustomEvent('gallery:set', {detail: offer.id}));

        this.updateBuyButton();
        this.sendViewedCounter();
    }

    updateBuyButton()
    {
        let item;
        item = this.isOffers ? this.offers[this.currentOffer] : this.params;

        if (SiteManager.inBasket(item.id)) {
            this.buyButton.innerHTML = this.params.inBasketMessage;
            this.buyButton.classList.add('in-basket');
        } else {
            this.buyButton.innerHTML = this.params.addBasketMessage;
            this.buyButton.classList.remove('in-basket');
        }

        if (item.canBuy) {
            this.buyButton.removeAttribute('style');
            this.oneClickButton.removeAttribute('style');
            this.subscribeButton.style.display = 'none';
        } else if (item.canSubscribe) {
            this.subscribeButton.removeAttribute('style');
            this.oneClickButton.style.display = 'none';
        } else {
            this.oneClickButton.style.display = 'none';
            this.subscribeButton.style.display = 'none';
            this.buyButton.removeAttribute('style');
            this.buyButton.innerHTML = this.params.notCanBuyMessage;
        }
    }

    eventAddToBasket(event)
    {
        let item, data, recommend;

        item = this.isOffers ? this.offers[this.currentOffer] : this.params;

        event.preventDefault();
        event.stopPropagation();

        if (this.buyButton.classList.contains('in-basket')) {
            document.dispatchEvent(new CustomEvent('modal:load', {
                detail: {
                    url: `/local/ajax/basket.php?siteId=${this.params.siteId}`,
                    tab: 'basket',
                }
            }));
            return;
        }

        if (!item.canBuy) {
            recommend = document.getElementById('elementRecommendedWrap');
            if (recommend) {
                scrollToNode(recommend);
            }
            return;
        }

        data = new FormData();
        data.append(this.params.actionVariableName, 'ADD2BASKET');
        data.append(this.params.quantityVariableName, '1');
        data.append(this.params.idVariableName, item.id);

        axios({
            url: this.params.currentPageUrl,
            method: 'post',
            params: {ajax_basket: 'Y'},
            data: data,
            timeout: 0,
            responseType: 'json',
        }).then(Tool.proxy(function (response) {
            this.addToBasketPostHandler(response.data);
        }, this));

        axios({
            url: '/local/ajax/ga4_item.php',
            method: 'get',
            params: {
                id: item.id,
                siteId: this.params.siteId,
            },
            data: {},
            timeout: 0,
            responseType: 'json',
        }).then((response) => {
            response = response.data;
            if (response.success) {
                ga4('add_to_cart', {items: [response.data]});
            }
        });
    }

    addToBasketPostHandler(response)
    {
        if (response.STATUS === 'OK') {
            const basketOpen = () => {
                document.dispatchEvent(new CustomEvent('modal:load', {
                    detail: {
                        url: `/local/ajax/basket.php?siteId=${this.params.siteId}`,
                        tab: 'basket',
                    }
                }));
            }

            document.dispatchEvent(new CustomEvent('custom_add_to_basket'));
            showMessage({
                title: this.params.actionBuyHeadMessage,
                text: this.params.actionBuyTextMessage.replace('#NAME#', this.params.name),
                time: 10000,
                html: `<button class="button-bordered" data-action="close">${this.params.goToBasketMessage}</button>`,
                clickEvent: basketOpen,
                params: {
                    href: this.params.basketUrl,
                    text: this.params.actionBuyLinkMessage,
                },
            });
        }
    }

    onOfferClick(event)
    {
        let property, value;

        if (!event.target.matches('[data-value]')) {
            return;
        }

        value = event.target;
        property = Tool.closest(value, '[data-property]');
        this.selectOffer(
            property.getAttribute('data-property'),
            value.getAttribute('data-value')
        );

        event.stopPropagation();
    }

    onModalOfferClick(event)
    {
        if (!event.target.matches('[data-modal-property][data-modal-value]')) {
            return;
        }
        this.selectOffer(
            event.target.getAttribute('data-modal-property'),
            event.target.getAttribute('data-modal-value')
        );

        event.stopPropagation();
    }

    selectOffer(property, value)
    {
        let castOffer, isUpdate, matrix, p, i, k, v, x, modalProperty;

        property = parseInt(property);
        value = parseInt(value);

        if (property < 1) {
            return 0;
        }

        for (p = 0; p < this.offerValues.length; p++) {
            this.offerValues[p].checked = false;
            this.offerValues[p].classList.remove('is-active');
            Tool.closest(this.offerValues[p], '[data-offer-item-wrap]').classList.add('is-disabled');
        }

        if (this.modalOfferValues) {
            for (x = 0; x < this.modalOfferValues.length; x++) {
                this.modalOfferValues[x].checked = false;
                this.modalOfferValues[x].classList.remove('is-active');
                Tool.closest(this.modalOfferValues[x], '[data-offer-item-wrap]').classList.add('is-disabled');
            }
        }

        castOffer = this.castOffer(property, value);
        isUpdate = this.currentOffer !== castOffer;
        this.currentOffer = castOffer;
        matrix = this.getCanBuyMatrixByPropertyId(property);

        for (i in matrix) {
            if (matrix.hasOwnProperty(i)) {
                if (matrix[i].canBuy === true) {
                    Tool.closest(
                        this.offerWrap.querySelector('[data-property="' + property + '"]').querySelector('[data-value="' + i + '"]'),
                        '[data-offer-item-wrap]'
                    ).classList.remove('is-disabled')

                    modalProperty = document.querySelector(`[data-modal-property="${property}"][data-modal-value="${i}"]`);
                    if (modalProperty) {
                        Tool.closest(modalProperty, '[data-offer-item-wrap]').classList.remove('is-disabled')
                    }
                }

                if (parseInt(i) !== value) {
                    continue;
                }

                for (k in matrix[i]) {
                    if (matrix[i].hasOwnProperty(k)) {
                        for (v in matrix[i][k]) {
                            if (matrix[i][k].hasOwnProperty(v)) {
                                if (Tool.inArray(true, matrix[i][k][v])) {
                                    Tool.closest(
                                        this.offerWrap.querySelector('[data-property="' + k + '"]').querySelector('[data-value="' + v + '"]'),
                                        '[data-offer-item-wrap]'
                                    ).classList.remove('is-disabled')

                                    modalProperty = document.querySelector(`[data-modal-property="${k}"][data-modal-value="${v}"]`);
                                    if (modalProperty) {
                                        Tool.closest(modalProperty, '[data-offer-item-wrap]').classList.remove('is-disabled')
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (isUpdate) {
            this.updateOfferItem();
        }

        axios({
            url: '/local/ajax/ga4_item.php',
            method: 'get',
            params: {
                id: this.offers[this.currentOffer].id,
                siteId: this.params.siteId,
            },
            data: {},
            timeout: 0,
            responseType: 'json',
        }).then((response) => {
            response = response.data;
            if (response.success) {
                ga4('view_item', {items: [response.data]});
            }
        });

        if (this.isOffers) {
            let oneClickOfferInput = document.getElementById('jsCurrentOneClickOffer');
            if (oneClickOfferInput) {
                oneClickOfferInput.value = this.offers[this.currentOffer].id;
            }
        }

        document.dispatchEvent(
            new CustomEvent(
                'custom_element_change_offer',
                {detail: {id: this.offers[this.currentOffer].id}}
            )
        );

    }

    castOffer(property, value)
    {
        let last, lastIndex, index, temp, i, k, checkedOffer, modalProperty;

        last = 0;
        lastIndex = 0;

        for (i = 0; i < parseInt(this.offersCount); i++) {
            index = 1;

            if (Tool.isEmpty(this.offers[i].tree)) {
                continue;
            }

            for (k in this.offers[this.currentOffer].tree) {
                if (this.offers[this.currentOffer].tree.hasOwnProperty(k)) {
                    temp = parseInt(this.offers[this.currentOffer].tree[k]);
                    k = parseInt(k);

                    if (k === property && parseInt(this.offers[i].tree[k]) === value) {
                        index += 100;
                        continue;
                    }

                    if (k !== property && parseInt(this.offers[i].tree[k]) === temp) {
                        index++;
                    }
                }
            }

            if (index > lastIndex) {
                lastIndex = index;
                last = i;
            }
        }

        for (i in this.offers[last].tree) {
            if (this.offers[last].tree.hasOwnProperty(i)) {
                checkedOffer = this.offerWrap.querySelector('[data-property="' + i + '"]')
                    .querySelector('[data-value="' + this.offers[last].tree[i] + '"]');
                checkedOffer.classList.add('is-active');
                checkedOffer.checked = true;

                if (checkedOffer.dataset.sizeName && this.selectedSize) {
                    this.selectedSize.innerHTML = checkedOffer.dataset.sizeName;
                }

                modalProperty = document.querySelector(`[data-modal-property="${i}"][data-modal-value="${this.offers[last].tree[i]}"]`);
                if (modalProperty) {
                    modalProperty.classList.add('is-active');
                    modalProperty.checked = true;
                }
            }
        }

        return last;
    }

    getCanBuyMatrixByPropertyId(property)
    {
        let offer, value, i, k;

        property = parseInt(property);

        if (!Tool.isEmpty(this.matrix[property])) {
            return this.matrix[property];
        }

        for (i = 0; i < parseInt(this.offersCount); i++) {
            offer = this.offers[i];

            if (Tool.isEmpty(offer.tree) || !offer.tree.hasOwnProperty(property)) {
                continue;
            }

            value = parseInt(offer.tree[property]);

            for (k in offer.tree) {
                if (offer.tree.hasOwnProperty(k)) {
                    Tool.makeObjectChain(
                        this.matrix,
                        [property, value, k, parseInt(offer.tree[k])]
                    );

                    this.matrix[property][value][k][parseInt(offer.tree[k])][i] = offer.canBuy || offer.canSubscribe;

                    if (offer.canBuy) {
                        this.matrix[property][value]['canBuy'] = true;
                    }
                }
            }
        }

        return this.matrix[property];
    }

    sendViewedCounter() {
        axios({
            url: '/bitrix/components/bitrix/catalog.element/ajax.php',
            method: 'post',
            params: {
                AJAX: 'Y',
                SITE_ID: this.params.siteId,
                PRODUCT_ID: this.isOffers ?this.offers[this.currentOffer].id : this.params.id,
                PARENT_ID: this.params.id
            },
            data: {},
            timeout: 0,
        });
    }
}
