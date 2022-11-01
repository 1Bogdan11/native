const showPreloader = (target) => {
  if(target.querySelector(".preloader")){
    target.querySelector(".preloader").remove();
  }

  const preloaderHTML = '<div class="preloader"></div>'
  target.setAttribute("data-loaded", true);
  target.insertAdjacentHTML("beforeend", preloaderHTML)
  setTimeout(() => {
    if(target.dataset.loaded)
      target.classList.add('is-preloading')
  }, 300);
}

const hidePreloader = (target) => {
  target.removeAttribute("data-loaded");
  target.classList.remove('is-preloading')
  if (target) {
    setTimeout(() => {
      if ($.qs('.preloader')) {
        $.qs('.preloader').remove()
      }
    }, 300);
  }
}

window.showPreloader = showPreloader;
window.hidePreloader = hidePreloader;