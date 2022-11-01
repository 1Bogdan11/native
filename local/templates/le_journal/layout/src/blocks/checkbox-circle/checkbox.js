$.each('[data-checkbox]', (el) => {
    el.addEventListener("change", () => {
        $.dispatch({
            el: document,
            name: 'checkbox:changed',
            detail: { 
                name: el.dataset.checkbox,
                checked: el.checked    
            }
        });
    })
    
})

document.addEventListener("checkbox:state", (e) => {
    $.each('[data-checkbox]', (el) => {
        if(el.dataset.checkbox == e.detail.name){
            el.checked = e.detail.state;
        }
    })
})