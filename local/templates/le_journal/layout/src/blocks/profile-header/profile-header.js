$.each('.profile__m-menu', el => {
  $.qsa(".profile__m-menu-option", el).forEach((btn) => {
    btn.addEventListener("click", (e) => {
      $.qs(".profile__m-menu .profile__m-menu-option.is-active").classList.remove('is-active')
      btn.classList.add('is-active')
    })
  })
})
