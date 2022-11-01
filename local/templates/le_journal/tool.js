'use strict';

class Tool
{
    static proxy(func, context)
    {
        return function () {
            return func.apply(context, arguments);
        };
    }

    static closest(element, selector)
    {
        if (element.matches(selector)) {
            return element;
        }
        while ((element = element.parentElement)) {
            if (element.matches(selector)) {
                return element;
            }
        }
        return false;
    }

    static inArray(needle, haystack)
    {
        let i;

        if (typeof haystack !== 'object' || needle === 'undefined') {
            return false;
        }

        if (Array.isArray(haystack) === true) {
            for (i = 0; i < haystack.length; i++) {
                if (needle === haystack[i]) {
                    return true;
                }
            }
            return false;
        }

        for (i in haystack) {
            if (haystack.hasOwnProperty(i)) {
                if (needle === haystack[i]) {
                    return true;
                }
            }
        }

        return false;
    }

    static isEmpty(check)
    {
        if (typeof check === 'undefined' || check === false) {
            return true;
        }

        if (typeof check === 'object') {
            if (Array.isArray(check) === true) {
                return (check.length < 1);
            }

            for (let i in check) {
                return false;
            }

            return true;
        }

        return (check === '' || check === ' ' || check === null);
    }

    static makeObjectChain(object, chain)
    {
        let last, i;

        if (typeof chain !== 'object' || Array.isArray(chain) !== true) {
            return false;
        }
        if (typeof object !== 'object') {
            return false;
        }

        last = object;
        for (i = 0; i < chain.length; i++) {
            if (typeof last !== 'object') {
                if (this.isEmpty(last)) {
                    last = {};
                    last[chain[i]] = {};
                } else {
                    return false;
                }
            } else {
                if (!last.hasOwnProperty(chain[i])) {
                    last[chain[i]] = {};
                }
            }
            last = last[chain[i]];
        }

        return true;
    }

    static evalScripts(dom)
    {
        let scripts, i, src, callback, script;

        if (!(dom instanceof Element)) {
            return;
        }

        scripts = dom.querySelectorAll('script');
        if (scripts) {
            for (i = 0; i < scripts.length; i++) {
                eval(scripts[i].innerHTML);
                scripts[i].setAttribute('data-load', 'N');
                src = scripts[i].getAttribute('src');
                callback = scripts[i].getAttribute('data-call');
                if (
                    typeof src === 'string' && src.length > 0
                    && !document.querySelector(`[src="${src}"]:not([data-load="N"])`)
                ) {
                    script = document.createElement('script');
                    script.setAttribute('src', src);
                    script.setAttribute('data-call', callback);
                    if (typeof callback === 'string' && typeof window[callback] === 'function') {
                        script.onload = function () {
                            window[this.getAttribute('data-call')]();
                        };
                    }
                    document.body.appendChild(script);
                } else if (typeof callback === 'string' && typeof window[callback] === 'function') {
                    window[callback]();
                }
            }
        }
    }

    static setParamToLink(link, param, value)
    {
        let search;

        if (typeof link !== 'string') {
            return '';
        }
        if (typeof param !== 'string') {
            return link;
        }

        link = link.replace(/#.+/i, '');
        search = param.replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1");
        link = link.replace(new RegExp('\\?' + search + '=[^&]+&?', 'g'), '?');
        link = link.replace(new RegExp('&' + search + '=[^&]+', 'g'), '');
        link = link.replace(/[&?]$/g, '');

        if (typeof value !== 'undefined') {
            link = link + (link.indexOf('?') > 0 ? '&' : '?') + param + '=' + value;
        }

        return link;
    }
}
