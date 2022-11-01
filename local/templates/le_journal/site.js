'use strict';

class Site
{
    params = {};
    data = {};

    constructor(params)
    {
        this.params = params;
        this.data = {};

        this.loadData();

        document.addEventListener('custom_add_to_basket', Tool.proxy(this.loadData, this));
        document.addEventListener('custom_basket_reload_complete', Tool.proxy(this.loadData, this));
        document.addEventListener('click', Tool.proxy(function (event) {
            let item, productId;
            item = Tool.closest(event.target, '[data-favorite-item]');
            if (item) {
                event.preventDefault();
                event.stopPropagation();
                productId = parseInt(item.getAttribute('data-favorite-item'));
                let ga4event = this.inFavorite(productId) ? 'remove_from_wishlist' : 'add_to_wishlist';
                this.toggleFavorite(productId, item.getAttribute('data-favorite-item-name'));
                axios({
                    url: '/local/ajax/ga4_item.php',
                    method: 'get',
                    params: {
                        id: productId,
                        siteId: this.params.siteId,
                    },
                    data: {},
                    timeout: 0,
                    responseType: 'json',
                }).then(Tool.proxy(function (response) {
                    response = response.data;
                    if (response.success) {
                        ga4(
                            ga4event,
                            {items: [response.data]}
                        );
                    }
                }, this));
            }

            item = Tool.closest(event.target, '[data-select-item]');
            if (item) {
                axios({
                    url: '/local/ajax/ga4_item.php',
                    method: 'get',
                    params: {
                        id: parseInt(item.getAttribute('data-select-item')),
                        siteId: this.params.siteId,
                    },
                    data: {},
                    timeout: 0,
                    responseType: 'json',
                }).then((response) => {
                    response = response.data;
                    if (response.success) {
                        ga4('select_item', {items: [response.data]});
                    }
                });
            }
        }, this));

        document.addEventListener('custom_site_data_update', Tool.proxy(this.favoriteUpdate, this));
        document.addEventListener('custom_block_ajax_loaded', Tool.proxy(this.favoriteUpdate, this));
        document.addEventListener('custom_basket_reload_complete', Tool.proxy(this.favoriteUpdate, this));
        document.addEventListener('custom_favorite_loaded', Tool.proxy(this.favoriteUpdate, this));
        document.addEventListener('afterModalLoad', Tool.proxy(this.favoriteUpdate, this));
    }

    loadData()
    {
        axios({
            url: '/bitrix/services/main/ajax.php',
            method: 'post',
            params: {
                c: 'journal:site.data.ajax',
                action: 'summary',
                mode: 'ajax',
                SITE_ID: this.params.siteId,
                sessid: this.params.sessId,
            },
            data: {},
            timeout: 0,
            responseType: 'json',
        }).then(Tool.proxy(this.loadDataHandler, this));
    }

    toggleFavorite(productId, productName)
    {
        axios({
            url: '/bitrix/services/main/ajax.php',
            method: 'post',
            params: {
                c: 'journal:site.data.ajax',
                action: !this.inFavorite(productId) ? 'addToFavorite' : 'removeFromFavorite',
                mode: 'ajax',
                SITE_ID: this.params.siteId,
                sessid: this.params.sessId,
                productId: productId,
            },
            data: {},
            timeout: 0,
            responseType: 'json',
        }).then(Tool.proxy(function (response) {
            this.loadDataHandler(response);
            if (this.inFavorite(productId) && productName) {
                const favoriteOpen = () => {
                    document.dispatchEvent(new CustomEvent('modal:load', {
                        detail: {
                            url: `/local/ajax/basket.php?siteId=${this.params.siteId}`,
                            tab: 'favorites',
                        }
                    }));
                }
                showMessage({
                    title: this.params.goToFavoriteHeadMessage,
                    text: this.params.goToFavoriteTextMessage.replace('#NAME#', productName),
                    time: 10000,
                    html: `<button class="button-bordered" data-action="close">${this.params.goToFavoriteButtonMessage}</button>`,
                    clickEvent: favoriteOpen,
                });
            }
        }, this));
    }

    loadDataHandler(response)
    {
        if (typeof response.data.data !== 'object') {
            return;
        }

        this.data = response.data.data;
        document.dispatchEvent(new CustomEvent(
            'custom_site_data_update',
            {detail: {object: this}}
        ));

        this.updateBasketInfo();
        this.updateFavoriteInfo();
    }

    updateBasketInfo()
    {
        let counters = document.querySelectorAll('.jsBasketCount'),
            count = this.data.basket.items ? this.data.basket.items.length : 0,
            i, status;

        if (!counters) {
            return;
        }

        for (i = 0; i < counters.length; i++) {
            counters[i].innerHTML = count;
            status = Tool.closest(counters[i], '.js-basket-status');
            if (status) {
                status.dispatchEvent(new CustomEvent('basketStatus:update', {detail: {count: count}}));
            }
        }

    }

    updateFavoriteInfo()
    {
        let counters = document.querySelectorAll('.jsFavoriteCount'),
            count = this.data.favorite.items ? this.data.favorite.items.length : 0,
            i;

        if (!counters) {
            return;
        }

        for (i = 0; i < counters.length; i++) {
            counters[i].innerHTML = count;
            if (count > 0) {
                counters[i].removeAttribute('style');
            } else {
                counters[i].style.display = 'none';
            }
        }
    }

    favoriteUpdate = function (event) {
        let wrap, items, item, i, productId;
        if (typeof event === 'object' && !!event.detail && event.detail.wrap) {
            wrap = document.getElementById(event.detail.wrap);
        }
        if (!wrap) {
            wrap = document;
        }

        items = wrap.querySelectorAll('[data-favorite-item]');
        if (!items) {
            return true;
        }

        for (i = 0; i < items.length; i++) {
            item = items[i];
            productId = parseInt(item.getAttribute('data-favorite-item'));
            if (this.inFavorite(productId)) {
                item.classList.add('is-active');
            } else {
                item.classList.remove('is-active');
            }
        }
    }

    inBasket(productId)
    {
        if (typeof this.data.basket === 'object') {
            return Tool.inArray(parseInt(productId), this.data.basket.items);
        }
        return false;
    }

    inFavorite(productId)
    {
        if (typeof this.data.favorite === 'object') {
            return Tool.inArray(parseInt(productId), this.data.favorite.items);
        }
        return false;
    }
}
