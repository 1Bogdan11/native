function showMessage(data) {
  const modal = document.createElement('div')
  modal.classList.add('message')
  modal.innerHTML = `
    <div class="message__close" data-action="close"></div>
    ${data.title ? `<p class="message__title">${data.title}</p>`: ''}
    ${data.text ? `<p class="message__text">${data.text}</p>`: ''}
    ${data.html || ''}
  `
  document.body.append(modal)

  setTimeout(() => {
      modal.classList.add('is-active')
  }, 100)

  if(data.time){
      setTimeout(() => {
          modal.classList.remove('is-active')
      }, data.time)
  }

  if(data.clickEvent)
    $.qs('button', modal).addEventListener("click", data.clickEvent)

  $.qsa('[data-action="close"]', modal).forEach((close) => {
    close.addEventListener('click', () => {
      modal.classList.remove('is-active')
      setTimeout(() => {
        modal.remove()
      }, 300);
  }, {once: true})
  })
}

window.showMessage = showMessage