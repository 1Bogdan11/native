const initModalTabs = (modal, tab) => {
    const modalTabs = $.qsa(".js-modal__tabs [data-modal-tab]", modal);
    const modalContent = $.qsa(".js-modal__contents [data-modal-content]", modal);
    const modalLevelLinks = $.qsa("[data-modal-level-open]", modal);
    const modalLevels = $.qsa("[data-modal-level]", modal);
    const modalTabHandler = (e) => {
        modalLevelClose();
        modalTabs.forEach((el) => {
            if(el.dataset.modalTab == e){
                el.classList.add("is-active")
                if(el.dataset.modalStateAdd)
                    modal.classList.add(el.dataset.modalStateAdd);
                if(el.dataset.modalStateRemove)
                    modal.classList.remove(el.dataset.modalStateRemove);
                $.dispatch({
                    el: document,
                    name: 'afterModalTabOpen',
                    detail: { e }
                });
            }
            else{
                el.classList.remove("is-active")
            }
        })

        modalContent.forEach((el) => {
            if(el.dataset.modalContent == e)
                el.classList.add("is-active") 
            else
                el.classList.remove("is-active")
        })
    }

    const modalLevelClose = () => {
        if(modalLevels){
            modalLevels.forEach((el) => {
                el.classList.remove("is-active")
            })
        }
    }

    modalTabs.forEach((el) => {
        el.addEventListener("mouseenter", (e) => {
            if(e.target.nodeName == "A")
                modalTabHandler(el.dataset.modalTab);
        })
        el.addEventListener("click", (e) => {
            if(e.target.nodeName == "BUTTON")
                modalTabHandler(el.dataset.modalTab);
        })
    })

    modalLevelLinks.forEach((el) => {
        el.addEventListener("mouseenter", () => {
            modalLevelClose();
            modalLevels.forEach((level) => {
                if(level.dataset.modalLevel == el.dataset.modalLevelOpen){
                    level.classList.add("is-active")
                }
            })
        })
    })

    if (modal.getAttribute('data-modal')) {
        document.addEventListener("beforeModalClose", (e) => {
            if($.qs(`[data-modal="${e.detail.modalName}"]`).hasAttribute("data-tabs-modal")){
                modalLevelClose();
            }
        })
        modalTabHandler(tab);
    }
}

document.addEventListener("modalTab", (e) => {
    initModalTabs(e.detail.el, e.detail.tab);
})

document.addEventListener("nav:open", (e) => {
    initModalTabs(e.detail.el);
}, {once: true});
