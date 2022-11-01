import { open } from '~/blocks/modal/modal';

$.each('[data-sertificate]', (el) => {
    document.addEventListener("checkbox:changed", (e) => {
        let modalTrigger = "sertificate";
        if(e.detail.name == modalTrigger && e.detail.checked){
            $.each('[data-modal]', (modal) => {
                if(modal.dataset.modal == modalTrigger)
                    open(modal);
            })
            el.classList.add("is-active")
        }
        else{
            el.classList.remove("is-active")
        }
    })
    $.qs(".js-sertificate-close", el).addEventListener("click", () => {
        $.dispatch({
            el: document,
            name: 'checkbox:state',
            detail: { 
                name: el.dataset.sertificate,
                state: false
            }
        });
        el.classList.remove("is-active")
    })
})