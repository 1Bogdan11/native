import ScrollBooster from 'scrollbooster';
import { breakpoints } from '~/js/helpers/breakpoints';

// Переменная, регулирующая кликабельность ссылок
let PointerDownBoolean;
if(window.innerWidth > breakpoints.desktop){
  PointerDownBoolean = true;
}
else{
  PointerDownBoolean = false;
}
//

$.each('.js-booster-x', el => {
  new ScrollBooster({
    viewport: el,
    content: el.querySelector('.js-booster__inner'),
    direction: 'horizontal',
    scrollMode: 'transform',
    emulateScroll: false,
    pointerDownPreventDefault: PointerDownBoolean,
  });
})

$.each('.js-booster-desktop-x', el => {
  if(window.innerWidth >= breakpoints.desktop){
    new ScrollBooster({
      viewport: el,
      content: el.querySelector('.js-booster__inner'),
      scrollMode: el.dataset.scrollmode,
      direction: 'horizontal',
      emulateScroll: false,
      pointerDownPreventDefault: PointerDownBoolean,
    });
  }
})


const boosterInitMobileX = () => {
  $.each('.js-booster-mobile-x', el => {
    if(window.innerWidth <= breakpoints.desktop){
      const booster = new ScrollBooster({
        viewport: el,
        content: el.querySelector('.js-booster__inner'),
        scrollMode: 'transform',
        direction: 'horizontal',
        emulateScroll: false,
        pointerDownPreventDefault: PointerDownBoolean,
      });
  
      if(el.dataset.boosterStart){
        booster.scrollTo({ x: el.dataset.boosterStart, y: 0 });
      }
    }
  })
}

$.each('.js-booster-y', el => {
  new ScrollBooster({
    viewport: el,
    content: el.querySelector('.js-booster__inner'),
    scrollMode: 'transform',
    direction: 'vertical',
    emulateScroll: false,
    pointerDownPreventDefault: PointerDownBoolean,
  });
})

$.each('.js-booster-desktop-y', el => {
  if(window.innerWidth >= breakpoints.desktop){
    new ScrollBooster({
      viewport: el,
      content: el.querySelector('.js-booster__inner'),
      scrollMode: 'transform',
      direction: 'vertical',
      emulateScroll: true,
      pointerDownPreventDefault: PointerDownBoolean,
    });
  }
})

boosterInitMobileX();

document.addEventListener("boosterInit:mobileX", () => {
  boosterInitMobileX();
})