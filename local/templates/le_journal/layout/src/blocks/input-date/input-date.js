import IMask from "imask";

class InputDate {
  constructor(el) {
    this.$input = el;
    this.isInited = false;
    if (!this.isInited) this.init();
  }

  init() {
    new IMask(this.$input, {
      mask: Date,
      lazy: false,
    })

    this.isInited = true
  }
}

$.each('.js-input-date', input => new InputDate(input))
