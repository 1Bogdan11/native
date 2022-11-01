$.each('[data-tab]', tab => {
    tab.addEventListener("click", () => {
        $.each('[data-tab]', tab => {
            tab.classList.remove("is-active");
        })
        $.each('[data-tab-content]', content => {
            if(tab.dataset.tab == content.dataset.tabContent){
                content.classList.add("is-active");
                tab.classList.add("is-active");
            }
            else{
                content.classList.remove("is-active");
            }
        })
    })
});