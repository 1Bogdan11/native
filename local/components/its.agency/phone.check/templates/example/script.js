const PhoneAuthComponent = (function () {
    let module, proto;

    module = function (params) {
        this.params = params;

        this.timerEnable = false;
        this.timer = setInterval(this.proxy(this.timerAction,this), 1001);

        this.changeNumber = document.getElementById(this.params.changeNumber);
        this.phoneBlock = document.getElementById(this.params.phoneBlock);
        this.error = document.getElementById(this.params.error);
        this.phone = document.getElementById(this.params.phone);
        this.phoneButton = document.getElementById(this.params.phoneButton);
        this.code = document.getElementById(this.params.code);
        this.codeButton = document.getElementById(this.params.codeButton);
        this.codeBlock = document.getElementById(this.params.codeBlock);
        this.repeat = document.getElementById(this.params.repeat);
        this.message = document.getElementById(this.params.message);
        this.counter = document.getElementById(this.params.counter);

        this.changeNumber.addEventListener('click', this.proxy(this.changeButtonEvent, this));
        this.phoneButton.addEventListener('click', this.proxy(this.phoneButtonEvent, this));
        this.repeat.addEventListener('click', this.proxy(this.phoneButtonEvent, this));
        this.codeButton.addEventListener('click', this.proxy(this.codeButtonEvent, this));
    };

    proto = module.prototype;

    proto.proxy = function (func, context) {
        return function () {
            return func.apply(context, arguments);
        };
    };

    proto.phoneButtonEvent = function (event) {
        this.getCode();
    };

    proto.changeButtonEvent = function (event) {
        this.stateChange();
    };

    proto.codeButtonEvent = function (event) {
        this.confirmCode();
    };

    proto.getCode = function () {
        let data = new FormData();
        data.append('phone', this.phone.value);
        this.send('confirmCode', data);
    };

    proto.confirmCode = function () {
        let data = new FormData();
        data.append('phone', this.phone.value);
        data.append('code', this.code.value);
        this.send('confirmCode', data);
    };

    proto.stateChange = function () {
        this.changeNumber.style.display = 'none';
        this.phoneBlock.style.display = 'block';
        this.phoneButton.style.display = 'block';
        this.codeBlock.style.display = 'none';
    };

    proto.timerAction = function () {
        if (!this.timerEnable) {
            return;
        }

        let current = parseInt(this.counter.innerHTML);

        current--;

        if (current <= 0) {
            this.repeat.style.display = 'block';
            this.message.style.display = 'none';
            this.timerEnable = false;
        } else {
            this.counter.innerHTML = current;
            this.repeat.style.display = 'none';
            this.message.style.display = 'block';
        }
    };

    proto.send = function (action, data, callback) {
        axios({
            url: '/bitrix/services/main/ajax.php',
            method: 'post',
            params: {
                c: 'its.agency:phone.check',
                action: action,
                mode: 'class',
                sessid: this.params.sessId,
            },
            data: data,
            timeout: 0,
            responseType: 'json',
        }).then(this.proxy(function (response) {
            let data = response.data.data;

            this.error.innerHTML = '';
            this.counter.innerHTML = data.timeout;
            this.timerEnable = true;

            if (data.state === 'error') {
                this.error.innerHTML = data.message;
            } else if (data.state === 'confirm') {
                this.changeNumber.style.display = 'none';
                this.phoneBlock.style.display = 'block';
                this.phoneButton.style.display = 'none';
                this.codeBlock.style.display = 'none';
            } else if (data.state === 'code') {
                this.changeNumber.style.display = 'none';
                this.phoneBlock.style.display = 'block';
                this.phoneButton.style.display = 'none';
                this.codeBlock.style.display = 'block';
            }

            console.log(data);
        }, this));
    };

    return module;
})();