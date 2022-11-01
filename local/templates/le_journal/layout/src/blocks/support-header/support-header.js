$.each('.support__m-menu', el => {
  $.qsa(".support__m-menu-option", el).forEach((btn) => {
    btn.addEventListener("click", (e) => {
      $.qs(".support__m-menu .support__m-menu-option.is-active").classList.remove('is-active')
      btn.classList.add('is-active')
    })
  })
})
