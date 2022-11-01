const storiesInit = () => {
    $.each('.js-stories', el => {
        $.qsa(".js-stories-prev", el).forEach((button) => {
            button.addEventListener("click", () => {
                el.swiper.slidePrev();
            })
        })
        $.qsa(".js-stories-next", el).forEach((button) => {
            button.addEventListener("click", () => {
                el.swiper.slideNext();
            })
        })
    })
    
    document.addEventListener("afterModalClose", (e) => {
        if(e.detail.modalName == "product-modal"){
            $.each('.js-stories', el => {
                el.classList.remove("is-inited")
            })
        }
    })
}

document.addEventListener("beforeModalOpen", () => {
    storiesInit();
  })