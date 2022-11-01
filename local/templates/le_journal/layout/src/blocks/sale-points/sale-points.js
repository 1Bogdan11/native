const salePoints = $.qsa(".js-sale-points .js-sale-point");

document.addEventListener("regionChange", (e) => {
    if(salePoints){
        salePoints.forEach((el) => {
            if(el.dataset.regionId == e.detail.id){
                el.classList.add("is-active")
            }
            else{
                el.classList.remove("is-active")
            }
        })
    }
})