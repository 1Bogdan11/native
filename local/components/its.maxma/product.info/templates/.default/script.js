'use strict';

class ItsMaxmaProductInfo
{
    constructor(wrapId, data, event, callback)
    {
        this.wrapId = wrapId;
        this.data = data;
        this.callback = callback;
        document.addEventListener(event, Tool.proxy(this.changeOffer, this));
    }

    changeOffer(event)
    {
        let id = this.callback(event),
            wrap = document.getElementById(this.wrapId);
        if (this.data[id]) {
            wrap.innerHTML = this.data[id];
            wrap.removeAttribute('style');
        } else {
            wrap.innerHTML = '';
            wrap.style.display = 'none';
        }
    }
}
