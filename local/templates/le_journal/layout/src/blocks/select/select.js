import SlimSelect from 'slim-select'
import scroll from '~/js/helpers/stop-scroll';

const selectInit = el => {
    if(el.slim){
        el.slim.destroy();
    }
    new SlimSelect({
        select: el,
        showSearch: false,
        showContent: el.dataset.show,
        hideSelectedOption: true,
        beforeOpen: () => {
            if(el.hasAttribute("data-select-overlay")){
                $.qs("body").classList.add("is-overlay");
                scroll.disable();
            }
            if(el.closest('.basket__list_item')){
                el.closest('.basket__list_item').classList.add('basket__list_item--open')
            }
        },
        beforeClose: () => {
            if(el.hasAttribute("data-select-overlay")){
                $.qs("body").classList.remove("is-overlay")
                scroll.enable();
            }
            if(el.closest('.basket__list_item')){
                el.closest('.basket__list_item').classList.remove('basket__list_item--open')
            }
        },
    })

    el.addEventListener("change", (e) => {
        $.dispatch({
            el: document,
            name: 'select:changed',
            detail: { e }
        });
    })
    
    setTimeout(() => {
        el.classList.add("is-inited")
    }, 400)
}

$.each('select.js-select', el => selectInit(el))

document.addEventListener("beforeModalOpen", () => {
    $.each('select.js-select', el => selectInit(el))
})

document.addEventListener("select:update", e => {
    const wrap = e.detail.wrap
    const selects = $.qsa('select.js-select', wrap || document)
    selects.forEach(el => selectInit(el))
})
