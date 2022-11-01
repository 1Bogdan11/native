'use strict';

class SectionPage
{
    params = {};
    wrapId = '';
    wrap = null;

    pageNavigationClass = '';
    showMoreClass = '';
    elementListClass = '';

    constructor(params, selectors)
    {
        this.params = params;
        this.wrapId = selectors.wrapId;
        this.pageNavigationClass = selectors.pageNavigationClass;
        this.showMoreClass = selectors.showMoreClass;
        this.elementListClass = selectors.elementListClass;
        this.orderSelectClass = selectors.orderSelectClass;
        this.wrap = document.getElementById(this.wrapId);

        if (this.wrap) {
            this.wrap.addEventListener('click', Tool.proxy(this.showMoreClickHandler, this));
        }

        this.orders = document.querySelectorAll(`.${this.orderSelectClass}`);
        if (this.orders) {
            for (let i = 0; i < this.orders.length; i++) {
                this.orders[i].addEventListener('change', Tool.proxy(this.orderChangeHandler, this));
            }
        }

        document.addEventListener('custom_section_filter_apply', Tool.proxy(function (event) {
            this.reload(event.detail.url);
        }, this));
    }

    showMoreClickHandler(event)
    {
        let showMoreButton = Tool.closest(event.target, `.${this.showMoreClass}`)
        if (showMoreButton) {
            event.preventDefault();
            event.stopPropagation();
            this.reload(showMoreButton.href, true);
        }
    };

    orderChangeHandler(event)
    {
        if (event.target.tagName === 'SELECT' || event.target.tagName === 'select') {
            this.reload(event.target.value);
        } else {
            this.reload(event.target.getAttribute('data-link'));
        }
    };

    reload(url, more)
    {
        showPreloader(this.wrap);
        let data = new FormData();
        data.append('ajax_request', 'Y');
        axios({
            url: url,
            method: 'post',
            params: {},
            data: data,
            timeout: 0,
            responseType: 'text',
        }).then(Tool.proxy(function (response) {
            this.reloadHandler(response.data, more);
            hidePreloader(this.wrap);
            document.dispatchEvent(new CustomEvent('scroll:update'));
        }, this));
    };

    reloadHandler(response, more)
    {
        let parser, html, wrap, nav, elements, scripts, k;

        parser = new DOMParser();
        html = parser.parseFromString(response, 'text/html');
        wrap = html.getElementById(this.wrapId);

        if (!!more) {
            nav = html.querySelector(`.${this.pageNavigationClass}`);
            if (nav) {
                this.wrap.querySelector(`.${this.pageNavigationClass}`).innerHTML = nav.innerHTML;
            } else {
                this.wrap.querySelector(`.${this.pageNavigationClass}`).remove();
            }

            elements = html.querySelector(`.${this.elementListClass}`);
            if (elements) {
                this.wrap.querySelector(`.${this.elementListClass}`)
                    .insertAdjacentHTML('beforeend', elements.innerHTML);
            }
        } else if (wrap) {
            this.wrap.innerHTML = wrap.innerHTML;
        }

        if (wrap) {
            Tool.evalScripts(wrap);
        }

        document.dispatchEvent(new CustomEvent('custom_block_ajax_loaded', {detail: {wrap: this.wrapId}}));
    };
}
