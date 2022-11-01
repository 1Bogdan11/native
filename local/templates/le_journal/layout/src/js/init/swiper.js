import { breakpoints } from '~/js/helpers/breakpoints';
import Swiper, { Navigation, Pagination, Thumbs, EffectFade, Autoplay } from 'swiper';
Swiper.use([Navigation, Pagination, Thumbs, EffectFade, Autoplay]);

const mobileSwiperInit = () => {
  $.each('.js-slider-mobile', el => {
    if(window.innerWidth <= breakpoints.desktop){
      if(el.swiper)
        el.swiper.destroy();
      new Swiper (el, {
        slidesPerView: el.dataset.preview,
        spaceBetween: 2,
        pagination: {
          el: $.qs('.swiper-pagination', el),
          type: "progressbar",
        },
        breakpoints: {
          768: {
            slidesPerView: Number(el.dataset.preview) + 1
          },
        }
      })
    }
  })
}

$.each('.js-incomes-slider', el => {
  if(window.innerWidth > breakpoints.desktop){
    new Swiper (el, {
      slidesPerView: 3,
      centeredSlides: true,
      spaceBetween: 30,
      loop: true,
      speed: 800,
      centerInsufficientSlides: true,
      slideToClickedSlide: true,
      on: {
        slideChange: (e) => {
          $.dispatch({
            el: document,
            name: 'incomesSliderChange',
            detail: { e } 
          });
        },
      },
    })
  }
})

document.addEventListener("afterModalTabOpen", (e) => {
  if(e.detail.e == "stories"){
    setTimeout(() => {
      $.each('.js-stories', el => {
        const single = $.qsa(".swiper-slide", el).length <= 1 ? true : false; 
          new Swiper (el, {
            speed: 400,
            centeredSlides: true,
            slidesPerView: "auto",
            spaceBetween: 60,
            on: {
              slideChange: (e) => {
                $.dispatch({
                  el: document,
                  name: 'storiesChange',
                  detail: { e } 
                });
              },
            },
          })
          if(single){
            el.classList.add("is-single")
          }
          el.classList.add("is-inited")
      })}, 600)            
  }
})

$.each('.js-gallery', el => {
  const swiper = new Swiper($.qs('.js-gallery__slider', el), {
    spaceBetween: 10,
    effect: "fade",
    speed: 800,
    autoHeight: false,
    dynamicBullets: true,
    navigation: {
      nextEl: $.qs('.js-gallery-next', el),
      prevEl: $.qs('.js-gallery-prev', el),
    },
    pagination: {
      el: $.qs('.swiper-pagination', el),
      type: "bullets",
      clickable: true
    },
    breakpoints: {
      1100: {
        autoHeight: true,
        noSwiping: true,
        noSwipingClass: "swiper-wrapper",
      },
    },
  });
  swiper.on('slideChangeTransitionEnd', function () {
    $.dispatch({
      el: document,
      name: 'gallery:updated'
    });
    $.dispatch({
      el: document,
      name: 'scroll:update'
    });
  });
  el.swiper = swiper;
});


$.each('[data-swiper="hero-banners"]', el => {
  new Swiper (el, {
    slidesPerView: 'auto',
    loop: true,
    autoplay: {
      delay: 5000,
    },
    pagination:  {
      el: $.qs('[data-pagination]', el),
      bulletClass: 'hero-banners__bullet',
      bulletActiveClass: 'hero-banners__bullet--active',
      type: "bullets",
      clickable: true,
    },
  })
})

document.addEventListener("mobileSwiper:init", () => {
  mobileSwiperInit();
})
mobileSwiperInit();