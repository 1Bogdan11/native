'use strict';

class PersonalLocationSearch
{
    lang = '';
    site = '';
    url = '';

    constructor(params)
    {
        this.lang = params.lang;
        this.site = params.site;
        this.url = params.url;
    }

    send(phrase, callback)
    {
        let data;

        data = new FormData();
        data.append('lang', this.lang);
        data.append('site', this.site);
        data.append('search', phrase);

        axios({
            url: this.url,
            method: 'post',
            params: {},
            data: data,
            timeout: 0,
            responseType: 'json',
        }).then(Tool.proxy(function (response) {
            this.sendHandler(response.data, callback);
        }, this));
    }

    sendHandler(data, callback)
    {
        let result = {}, i, j, item, path, name;

        if (!!data.result) {
            path = data.data.ETC.PATH_ITEMS;
            for (i = 0; i < data.data.ITEMS.length; i++) {
                item = data.data.ITEMS[i];
                name = '<span>' + item.DISPLAY + '</span>';
                for (j = 0; j < item.PATH.length; j++) {
                    name += ', ' + path[item.PATH[j]].DISPLAY;
                }
                result[item.CODE] = name;
            }
        }

        if (typeof callback === 'function') {
            callback(result);
        }
    }
}
