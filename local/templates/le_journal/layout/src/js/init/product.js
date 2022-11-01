import { breakpoints } from '~/js/helpers/breakpoints';
import ScrollBooster from 'scrollbooster';

const galleryThumbsInit = () => {
    $.qsa(".js-gallery__thumbs .js-toggle-item").forEach((thumb, index) => {
        thumb.addEventListener("click", () => {
            $.qs(".js-gallery__slider").swiper.slideTo(index)
        })
        if(index == 0)
            thumb.classList.add("is-active")
    })
}

const galleryThumbsUpdate = (activeIndex) => {
    $.qsa(".js-gallery__thumbs .js-toggle-item").forEach((thumb, index) => {
        thumb.classList.remove("is-active")
        if(index == activeIndex){
            thumb.classList.add("is-active")
        }
    })
}

const productSizesChange = () => {
    $.each('.js-size-product', el => {
        const listener = (e) => {
            $.each('.js-product-size__outer', el => {
                el.innerHTML = $.qs("label", e.target.closest(".js-size-product")).innerHTML;
            })
        }
        el.removeEventListener("click", listener, false)
        el.addEventListener("click", (e) => listener(e))
    })
}

document.addEventListener("DOMContentLoaded", () => {
    $.each('.js-product', product => {
        const swiper = $.qs(".js-gallery__slider", product).swiper

        if(window.innerWidth < breakpoints.desktop){
            setTimeout(() => {
                product.classList.add("is-loaded")
            }, 500)
        }

        $.each('[data-scroll-disable-delay]', modal => {
            modal.setAttribute("data-scroll-disable-delay", 450);
        })

        $.delegate(`.js-gallery-view`, (e, el) => {
            if(!e.target.classList.contains("js-pin__button") && !e.target.classList.contains("swiper-slide") && !e.target.classList.contains("js-toggle-item")){
                $.qs("body").classList.add("is-fullscreen");
                window.scroll.to("top")

                swiper.allowTouchMove = false;
                swiper.update();

                setTimeout(() => {
                    window.scroll.stop();
                    $.qsa(".js-gallery__slider .swiper-slide img", product).forEach((el) => {
                        el.setAttribute("src", el.dataset.fullscreen);
                    })
                    swiper.update();

                    // Инитим скроллбустер
                    $.qs(".js-gallery__slider", product).classList.add("is-scrollbooster");
                    $.qsa(".js-scrollbooster-wrapper", product).forEach((el) => {
                        const direction = window.innerWidth < breakpoints.desktop ? 'all' : 'vertical'

                        const scrollBooster = new ScrollBooster({
                          viewport: el,
                          content: el.querySelector('.js-scrollbooster-content'),
                          scrollMode: 'transform',
                          direction,
                          bounce: false,
                          emulateScroll: true,
                        });
                        
                        if (window.innerWidth < breakpoints.desktop) {
                            scrollBooster.setPosition({
                                x: (scrollBooster.content.width - window.innerWidth) / 2,
                                y: (scrollBooster.content.height - window.innerHeight) / 2
                            })
                        }
                        
                        el.scrollBooster = scrollBooster;
                    })
                }, 800)
            }
        })
        $.delegate(`.js-gallery-close`, (e, el) => {
            $.qs("body").classList.remove("is-fullscreen");
            $.qs("body").classList.add("is-fullscreen-leave");

            swiper.allowTouchMove = true;
            swiper.update();

            setTimeout(() => {
                $.qs(".js-gallery__slider", product).swiper.update();
                $.qs("body").classList.remove("is-fullscreen-leave");
                $.qs(".js-gallery__slider", product).classList.remove("is-scrollbooster");
                window.scroll.start();
                $.qsa(".js-scrollbooster-wrapper", product).forEach((el) => {
                    el.scrollBooster.scrollTo({ x: 0, y: 0 });
                })
            }, 1000);
        });

        $.qs(".js-gallery__slider", product).swiper.on('slideChange', function (e) {
            galleryThumbsUpdate(e.activeIndex);
        });
    });
    galleryThumbsInit();
    productSizesChange();
})

document.addEventListener("productSizes:change", () => {
    productSizesChange();
})

document.addEventListener("beforeModalOpen", (e) => {
    if(e.detail.modalName == "mobile-menu"){
        $.each('.js-product', el => {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        })
    }
})

//Смена sku
const createPin = (data) => {
    const pin = document.createElement("div");
    pin.className = `product-gallery__pin js-pin`
    const titleRow = data.title ? `<div class="product-gallery__pin_title" data-title>${data.title}</div>` : ''
    const descriptionRow = data.description ? `<div class="product-gallery__pin_description" data-description>${data.description}</div>` : ''
    pin.innerHTML = `
        <div class="product-gallery__pin_button js-pin__button"></div>
        <div class="product-gallery__pin_popup js-pin__popup">
            ${titleRow} ${descriptionRow}
        </div>
    `;
        
    pin.style.left = `${data.location[0]}%`
    pin.style.top = `${data.location[1]}%`
    return pin;
}

const createSlide = (data) => {
    let slide = document.createElement("div");
    const pins = document.createElement("div");
    slide.innerHTML = `
        <div class="product-gallery__slider_item-outer js-scrollbooster-wrapper">
            <div class="product-gallery__slider_item-inner js-scrollbooster-content">
                <picture>
                    <source media="(max-width: 1100px)" srcset="${data.srcset}">
                    <img src="${data.src}" alt="${data.alt}" data-fullscreen="${data.fullscreen}" data-preview="${data.preview}">
                </picture>
            </div>
        </div>
    `;
    pins.className = "product-gallery__pins";
    slide.className = `product-gallery__slider_item swiper-slide`;
    if(data.pins){
        data.pins.forEach((pin) => {
            pins.appendChild(createPin(pin));
        })
        $.qs(".js-scrollbooster-content", slide).appendChild(pins);
    }

    return slide;
}

const createThumb = (data, index) => {
    const first = index == 0 ? "is-active" : "";
    const thumb = document.createElement("div");
    thumb.className = `product-gallery__thumbs_item js-toggle-item ${first}`;
    thumb.innerHTML = `
        <picture>
            <source media="(max-width: 1100px)" srcset="${data.srcset}">
            <img src="${data.src}" alt="${data.alt}" data-fullscreen="${data.fullscreen}" data-preview="${data.preview}">
        </picture>
    `;
    return thumb;
}

const setSKUPhotos = (data, slider) => {
    $.qs(".swiper-wrapper", slider).innerHTML = '';
    data.photos.forEach((photo) => {
        $.qs(".swiper-wrapper", slider).appendChild(createSlide(photo));
    })
    slider.swiper.slideTo(0);
    slider.swiper.update();
}

const setSKUThumbs = (data, slider) => {
    slider.innerHTML = '';
    data.photos.forEach((photo, index) => {
        slider.appendChild(createThumb(photo, index));
    })
    $.dispatch({
        el: document,
        name: 'toggle:update'
    });
    galleryThumbsInit();
}


const updateSKUPhotos = (el) => {
    setSKUPhotos(el, $.qs(".js-gallery__slider"));
    setSKUThumbs(el, $.qs(".js-gallery__thumbs"));
}

document.addEventListener("gallery:set", (e) => {
    $.qs(".js-gallery").classList.add("is-loading");

    setTimeout(() => {
        let defaultCounter = 0;
        galleryData.forEach((el) => {
            if(el.id == e.detail){
                defaultCounter++;
                updateSKUPhotos(el);
            }
        })
        if(defaultCounter < 1){
            galleryData.forEach((el) => {
                if(el.id == "default")
                    updateSKUPhotos(el);
            })
        }
        $.dispatch({
            el: document,
            name: 'gallery:updated',
        });
    }, 600)
    setTimeout(() => {
        $.qs(".js-gallery").classList.remove("is-loading");
    }, 900)
    setTimeout(() => {
        document.dispatchEvent(new CustomEvent('modal:close', {
            detail: {
                name: 'sizes',
            }
        }));
    }, 200)

})
