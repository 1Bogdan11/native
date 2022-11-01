const menuBurger = $.qs('[data-menu]');

const changeMobileView = (name) => {
    $.each('[data-mobile-part]', (el) => {
        if(name == el.dataset.mobilePart)
            el.classList.add("is-active")
        else
            el.classList.remove("is-active")
    })
}

$.delegate(`[data-mobile-button]`, (e, el) => {
    changeMobileView(el.dataset.mobileButton);

    if(el.dataset.mobileButton != "menu"){
        menuBurger.dataset.state = "nested"
        menuBurger.classList.add("is-nested")
    }
})