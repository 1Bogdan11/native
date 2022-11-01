import SlimSelect from 'slim-select'
import IMask from "imask";

class UniquePhone {
  static inited = []
  static telephoneMasks = [
    {
      "name": "Российская Федерация",
      "dial_code": "+7",
      "text": "RU",
      "value": "+7 ( 000 ) 000 - 00 - 00"
    },
    {
      "name": "Азербайджан",
      "dial_code": "+994",
      "text": "AZ",
      "value": "+994 - 00 - 000 - 00 - 00"
    },
    {
      "name": "Армения",
      "dial_code": "+374",
      "text": "AM",
      "value": "+374 - 00 - 000 - 00"
    },
    {
      "name": "Беларусь",
      "dial_code": "+375",
      "text": "BY",
      "value": "+375 ( 00 ) 000 - 00 - 00"
    },
    {
      "name": "Казахстан",
      "dial_code": "+77",
      "text": "KZ",
      "value": "+7 ( 700 ) 000 - 00 - 00"
    },
    {
      "name": "Кыргызстан",
      "dial_code": "+996",
      "text": "KG",
      "value": "+996 ( 000 ) 000 - 000"
    },
    {
      "name": "Молдова",
      "dial_code": "+373",
      "text": "MD",
      "value": "+373 - 0000 - 0000"
    },
    {
      "name": "Таджикистан",
      "dial_code": "+992",
      "text": "TJK",
      "value": "+992 - 0000 - 0000"
    },
    {
      "name": "Узбекистан",
      "dial_code": "+998",
      "text": "UZB",
      "value": "+998 - 0000 - 0000"
    },
    {
      "name": "Украина",
      "dial_code": "+380",
      "text": "UKR",
      "value": "+380 - 00 - 000 - 0000"
    },
    {
      "name": "Туркменистан",
      "dial_code": "+993",
      "text": "TKM",
      "value": "+993 - 0000 - 0000"
    },
  ]

  constructor(container) {
    let _this = this;

    this.$refs = {
      container,
      input: container.querySelector('.js-unique-phone-input'),
      select: container.querySelector('.js-unique-phone-select'),
    }

    this.$refs.container.classList.add('is-inited')
    const cityCode = this.$refs.container.getAttribute('data-selected-country-code');
    var ss = new SlimSelect({
        select: this.$refs.select.querySelector('select'),
        showSearch: false,
        showContent: "down"
      }),
      code = cityCode ? cityCode : "RU";

    ss.setData(Object.keys(UniquePhone.telephoneMasks).map(key => {
      let country = UniquePhone.telephoneMasks[key];
      return {
        value: `${country.text}`,
        text: `<span class="img-styled"><img src="/assets/img/flags/(${key}).png" alt=""></span>
              <span>${country.name}</span>
              <span>${country.dial_code}</span>`,
        selected: country.text === code ? true : false
      }
    }));

    let mask = UniquePhone.telephoneMasks.filter(country => country.text === code).map(country => country.value)[0];
    _this.$refs.imask = new IMask(_this.$refs.input, {
      mask: mask,
      lazy: false
    });

    _this.$refs.select.addEventListener('change', _this.onChange)
  }

  onChange = () => {
    let select = this.$refs.select.querySelector('select');
    const code = select.options[select.selectedIndex].value;
    let mask = UniquePhone.telephoneMasks.filter(mask => mask.text === code).map(mask => mask.value)[0];
    if (!mask)
      return;

    this.$refs.imask.destroy();

    this.$refs.input.value = '';

    this.$refs.imask = new IMask(this.$refs.input, {
      mask: mask,
      lazy: false
    });
  }
}

const uniquePhonesInit = () => {
  let uniquePhones = document.querySelectorAll('.js-unique-phone:not(.is-inited)');
  uniquePhones.forEach(item => {
    new UniquePhone(item)
  })
}

uniquePhonesInit();

document.addEventListener("uniquie:init", () => {
  uniquePhonesInit();
})

document.addEventListener("afterModalOpen", () => {
  uniquePhonesInit();
})

// Bitrix events
document.addEventListener("custom_phone_auth_ajax_loaded", () => {
  uniquePhonesInit();
})