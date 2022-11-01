import Slider from '~/js/components/slider';
let slider;
$.each('.js-about-slider', list => {
  slider = new Slider(list, true);
});

$.delegate('.js-about-prev', () => {
  if (slider) slider.prev();
});

$.delegate('.js-about-next', () => {
  if (slider) slider.next();
});
