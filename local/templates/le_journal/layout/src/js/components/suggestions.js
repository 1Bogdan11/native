import data from "~/js/data/suggestions.json";
import { debounce } from '~/js/helpers/utils.js';

class Suggestions {
  constructor(el, params) {
    this.$el = el
    this.$input = $.qs('input', this.$el)
    this.$suggestionsEl = $.qs('.input__suggestions', this.$el)
    this.$suggestionsElInner = $.qs('.input__suggestions-inner', this.$el)
    this.$errorEl = $.qs('.input__suggestions-error', this.$el)
    this.url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address"
    this.token = this.$el.dataset.suggestions
    this.isOpen = false
    this.isInit = false
    this.clickHandler = null
    this.inputHandler = null
    this.data = []
    this.isSelected = null
    this.selectedIndex = null
    this.init()
  }

  init() {
    this.clickHandler = this.clickFunc.bind(this)
    this.inputHandler = debounce(this.inputFunc.bind(this), 500)
    document.addEventListener('click', this.clickHandler)    
    this.$input.addEventListener('input', this.inputHandler)    
    this.isInit = true
  }

  destroy() {
    document.removeEventListener('click', this.clickHandler)
    this.$input.removeEventListener('input', this.inputHandler) 
    this.isInit = false
  }
  
  clickFunc(e) {
    if (e.target === this.$el || this.$el.contains(e.target)) {
      if (e.target === this.$input) {
        if (!this.$el.dataset.city) {
          this.addError('Выберите, пожалуйста, город')
        } else if (e.target.value) {
          this.getData(e.target.value)
        }
      }

      if (e.target.closest('.input__suggestions-item:not(.disabled)')) {
        const target = e.target.closest('.input__suggestions-item:not(.disabled)')        
        this.isSelected = true
        this.selectedIndex = +target.dataset.index
        
        this.$input.value = target.innerHTML
        
        this.hideSuggestions()
      }      
    } else if (this.isOpen === true) {
      this.hideSuggestions()
    }
  }
  
  inputFunc(e) {
    this.isSelected = false
    this.isHouseSelected = false
    this.selectedIndex = null
    if (this.$el.dataset.city) {
      this.getData(e.target.value)      
    }
  }

  getData(query) {
    if (window.axios) {
      window.axios({
        url: this.url,
        method: "post",
        mode: "cors",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "Authorization": "Token " + this.token
        },
        data: JSON.stringify({
          query,          
          locations: [
            {
              "city": this.$el.dataset.city
            }
          ],
        }),
      })
        .then(response => response.data)
        .then(data => {
          this.data = data.suggestions
          this.showSuggestions()
        })
        .catch(error => console.log("error", error));
    } else {
      this.showSuggestions(data)
    }
  }

  showSuggestions() {
    this.renderSuggestions(this.data)
    this.openSuggestions()
    this.removeError()
  }

  hideSuggestions() {
    this.closeSuggestions()
    this.clearSuggestions()
    
    if (!this.$el.dataset.city) {
      this.addError('Выберите, пожалуйста, город')
    } else if (this.isSelected === false) {
      this.addError('Выберите значение из списка')
    } else if (this.isSelected === true && !this.data[this.selectedIndex].data.house) {
      this.addError('Укажите номер дома')
    } else {
      this.$el.dataset.value = this.$input.value
    }
  }

  openSuggestions() {
    this.$el.classList.add('open')
    this.isOpen = true
  }

  closeSuggestions() {
    this.$el.classList.remove('open')
    this.isOpen = false
  }

  renderSuggestions(data) {
    let html
    if (data.length) {
      html = data.slice(0, 5).map((suggestion, i) => {
        return `
          <li class="input__suggestions-item" data-index="${i}">${suggestion.value}</li>
        `
      }).join('')
    } else {
      html = `<li class="input__suggestions-item disabled">Неизвестный адрес</li>`
    }
    this.$suggestionsElInner.innerHTML = html
  }

  clearSuggestions() {
    this.$suggestionsElInner.innerHTML = ''
  }
  
  addError(str) {
    this.$input.classList.add('is-error')
    this.$errorEl.innerHTML = str
  }
  
  removeError() {
    this.$input.classList.remove('is-error')
    this.$errorEl.innerHTML = ''
  }
}

let suggestions

document.addEventListener('suggestions:init', () => {
  if (suggestions) {
    suggestions.destroy()
    suggestions = null    
  }
  if ($.qs('[data-suggestions]')) {
    suggestions = new Suggestions($.qs('[data-suggestions]'))
  }
})
