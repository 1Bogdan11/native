$.each('.js-basket-status', status => {
    status.addEventListener("basketStatus:update", (e) => {
        if(e.detail.count > 0){
            status.classList.add("is-active");
            $.qs(".js-basket-status__count", status).innerText = e.detail.count;
        }
        else
            status.classList.remove("is-active");
    })
});