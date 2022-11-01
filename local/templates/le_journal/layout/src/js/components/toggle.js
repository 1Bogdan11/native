import { dispatchScrollUpdate } from '../helpers/utils';

$.each('.js-toggle-multiply', el => {
    const hiddenElements = $.qsa(".u-hidden", el);
    $.qsa(".js-toggle-btn", el).forEach((btn) => {
        btn.addEventListener("click", () => {
            btn.classList.toggle("is-active");
            hiddenElements.forEach((el) => {
                el.classList.toggle("u-hidden")
            })
        })
    })
})

const toggleInit = () => {
  $.each('.js-toggle', el => {
    if(!el.hasAttribute("data-inited")){
      el.setAttribute("data-inited", true);
      $.qsa(".js-toggle__btn", el).forEach((btn) => {
          btn.addEventListener("click", (e) => {
              if (!e.target.closest(".js-toggle__btn a[data-link]")) {
                e.preventDefault();
              }
              btn.closest(".js-toggle").classList.toggle("is-active")
              dispatchScrollUpdate();
          })
      })
    }
  })

  $.each('.js-toggle-free', el => {
    $.qsa(".js-toggle-item", el).forEach((btn) => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            btn.classList.toggle("is-active")
        })
    })
  })
}


$.each('.js-toggle-height__container', el => {
  $.qsa(".js-toggle-height__button", el).forEach((btn) => {
    btn.addEventListener("click", () => {
      $.qsa(".js-toggled-height", el).forEach((item) => {
        let toggled = item,
          minHeight = btn.getAttribute('data-toggled-height');

        toggleHeight(toggled, minHeight);
      })
    })
  })
})

function toggleHeight(toggled, minHeight) {
  if (toggled.classList.contains("is-active")) {
    toggled.style.height = minHeight;
    toggled.classList.remove('is-active');

    toggled.addEventListener(
      "transitionend",
      function() {
        toggled.classList.remove("is-active");
      },
      { once: true }
    );
  } else {
    toggled.style.height = "auto";
    toggled.classList.add('is-active');
    var height = toggled.clientHeight + "px";
    toggled.style.height = minHeight;
    setTimeout(() => toggled.style.height = height)
  }
}

const toggleListInit = () => {
  $.each('.js-toggle-list', el => {
    $.qsa(".js-toggle-item", el).forEach((btn) => {
      const listener = () => {
        $.qsa(".js-toggle-item", el).forEach((item) => {
          item.classList.remove("is-active");
        })
        btn.classList.add("is-active")
        dispatchScrollUpdate();
        $.dispatch({
          el: el,
          name: 'toggleChanged',
          detail: { btn }
        });
      }
      btn.removeEventListener("click", listener);
      btn.addEventListener("click", listener);
    })
  })
}

toggleInit();
toggleListInit();

document.addEventListener("toggle:update", () => {
  toggleInit();
  toggleListInit();
})
