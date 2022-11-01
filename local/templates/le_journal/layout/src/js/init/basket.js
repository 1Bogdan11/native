const basketPromocodeClear = () => {
    let basket = $.qs('.js-basket'),
        input = $.qs(".js-form input", basket);
    $.qs(".js-form", basket).classList.remove("is-success");
    input.value = '';
    input.dispatchEvent(new Event('change', {bubbles: true, cancelable: false}));
    document.dispatchEvent(new CustomEvent('input:reload'));
}

const basketInit = () => {
    $.each('.js-basket', basket => {
        const promocodeClose = $.qs(".js-promocode__close", basket);

        document.addEventListener("beforeModalOpen", (e) => {
            if(e.detail.modalName == "ordering-modal"){
                basket.closest(".modal").classList.add("is-ordering")
            }
        })
    
        document.addEventListener("beforeModalClose", (e) => {
            if(e.detail.modalName == "ordering-modal"){
                basket.closest(".modal").classList.remove("is-ordering")
                window.scroll.stop();
            }
        })
        if(promocodeClose)
            promocodeClose.addEventListener("click", basketPromocodeClear)
    })
}

document.addEventListener("beforeModalOpen", basketInit)

document.addEventListener("custom_basket_reload_complete", basketInit)
