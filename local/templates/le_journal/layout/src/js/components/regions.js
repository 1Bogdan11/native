const regionInit = (id) => {
    $.dispatch({
        el: document,
        name: 'regionChange',
        detail: { id }
    });
}

document.addEventListener("DOMContentLoaded", () => {
    $.each('select.js-contacts-region', regionSelect => {
        regionSelect.addEventListener("change", () => {
            const href = regionSelect.options[regionSelect.selectedIndex].dataset.href
            if (href) {
                location.href = href
            }
        })
        if(regionsMapData){
            regionsMapData.forEach((region) => {
                if(region.active){
                    regionInit(region.id)
                }
            })
        }
    })
})