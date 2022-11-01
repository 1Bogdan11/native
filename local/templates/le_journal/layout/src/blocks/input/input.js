const checkInputEmpty = (el) => {
    if(el.value.length !== 0){
        el.classList.add("is-filled")
    }
    else{
        el.classList.remove("is-filled")
    }
}

const inputInit = () => {
    const inputEmptyCheck = () => {
        $.each('.js-input-phone', el => {
            const input = $.qs("input[type=tel]", el);
            const changeButton = $.qs(".js-input-phone_change", el);
            if(input.value.match(/[_\?\<]/g)){
                if(input.value.match(/[_\?\<]/g).length < input.dataset.maskLength){
                    el.classList.remove("is-disabled")
                }
                else if(input.value.match(/[_\?\<]/g).length == input.dataset.maskLength){
                    el.classList.add("is-disabled")
                }
            }
            
            if(changeButton){
                changeButton.addEventListener("click", (e) => {
                    e.preventDefault();
                    changeButton.classList.remove("is-active");
                    $.qs(".js-input-phone_accept", el).classList.add("is-active");
                })
            }
        })
    }
    inputEmptyCheck();

    $.each('input', el => {
        el.addEventListener("blur", () => {
            checkInputEmpty(el);
        })
        checkInputEmpty(el);
        
    })

    $.each('input[type=tel]', el => {
        el.addEventListener("keyup", () => {
            inputEmptyCheck();
        })
    })
    
}

inputInit();

document.addEventListener("input:reload", () => {
    inputInit();
})

document.addEventListener("beforeModalOpen", () => {
    inputInit();
})

// Bitrix events
document.addEventListener("custom_phone_auth_ajax_loaded", () => {
    inputInit();
})

document.addEventListener("custom_basket_reload_complete", () => {
    inputInit();
})