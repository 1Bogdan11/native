import noUiSlider from 'nouislider';

const rangeInit = () => {
  $.each('.js-range', el => {

    const slider = $.qs(".js-range-slider", el);
    const from = $.qs(".js-range-from", el);
    const to = $.qs(".js-range-to", el);
    const fromInput = $.qs(".js-range-from-input", el);
    const toInput = $.qs(".js-range-to-input", el);

    const setRangeValues = ({fromValue, toValue}, updateTrigger) => {
      from.innerText = `${fromValue.toLocaleString('ru-RU')} ₽`;
      to.innerText = `${toValue.toLocaleString('ru-RU')} ₽`;
      el.dataset.from = fromValue;
      el.dataset.to = toValue;
      fromInput.value = fromValue;
      toInput.value = toValue;
      if(updateTrigger){
        fromInput.dispatchEvent(new KeyboardEvent('change'));
        toInput.dispatchEvent(new KeyboardEvent('change'));
      }
    }
    
    if(!slider.noUiSlider){
      const numFrom = Number(el.dataset.from);
      const numTo = Number(el.dataset.to);
      const numMin = Number(el.dataset.min);
      const numMax = Number(el.dataset.max);

      const from = numFrom > 0 ? numFrom : numMin;
      const to = numTo > 0 ? numTo : numMax;
      
      noUiSlider.create(slider, {
        start: [from, to],
        connect: true,
        range: {
          'min': numMin,
          'max': numMax
        }
      });
      
      setRangeValues({fromValue: from, toValue: to}, false);
    }

    slider.noUiSlider.on('slide', function (values, handle) {
      const from = Math.round(values[0]);
      const to = Math.round(values[1]);
      setRangeValues({fromValue: from, toValue: to}, true);
    })

    slider.noUiSlider.on('change.one', function () {
      $.dispatch({
        el: el,
        name: 'range:change',
    });
    })

    el.addEventListener("range:clear", () => {  
      const from = Number(el.dataset.min);
      const to = Number(el.dataset.max);
      setRangeValues({fromValue: from, toValue: to}, true)
      slider.noUiSlider.set([Number(el.dataset.min), Number(el.dataset.max)]);
    })

    if(el.dataset.to == el.dataset.min){
      slider.noUiSlider.set([Number(el.dataset.min), Number(el.dataset.max)]);
    }
  })
}

document.addEventListener("beforeModalOpen", () => {
  rangeInit();
})
