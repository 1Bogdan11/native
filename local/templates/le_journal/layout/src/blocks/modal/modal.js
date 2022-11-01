import scroll from '~/js/helpers/stop-scroll';

const onEscape = e => {
  if (e.keyCode === 27) {
    const el = $.qs('.modal--active');

    if (!el) return false;

    const { modal } = el.dataset;
    close(el, modal);
  }
};

export const init = () => {
  // Load
  $.delegate(`[data-modal-url]`, (e, el) => {
    load(el.dataset.modalUrl, el.dataset.modalTab);
  });

  // Open
  $.delegate(`[data-modal-open]`, (e, button) => {
    let modal;
    $.qsa("[data-modal]").forEach((el) => {
      if(button.dataset.modalOpen == el.dataset.modal)
        modal = el;
    })
    open(modal, button.dataset.modalTab);
  });

  // Close
  $.delegate(`[data-modal-close]`, (e, el) => {
    const modal = $.qs(`[data-modal="${el.dataset.modalClose}"]`);
    if (!modal) return false;

    close(modal);

    if(el.dataset.modalStateRemove)
      setTimeout(() => {
        modal.classList.remove(el.dataset.modalStateRemove)
      }, 500)
  });

  // Menu
  $.delegate(`[data-menu]`, (e, el) => {
    const modal = $.qs(`[data-modal="mobile-menu"]`);

    if(modal.classList.contains("modal--active")){
        if(el.dataset.state != "nested"){
          el.classList.remove("is-active");
          close(modal);
        }
        else{
          el.setAttribute('data-state', 'default')
          el.classList.remove("is-nested")
        }
    }
    else{
      el.classList.add("is-active");
      open(modal);
    }
  });
};

export function load(url, modalTab) {
  fetch(url)
  .then((response) => response.text())
  .then((html) => {
    const modal = document.createElement("div");
    modal.innerHTML = html;
    const trueModal = $.qs('section', modal);
    $.qsa('.js-modals section').forEach((el) => {
      if(el.dataset.modal == trueModal.dataset.modal)
        el.remove();
    })
    $.qs('.js-modals').appendChild(trueModal);

    $.dispatch({
      el: document,
      name: 'afterModalLoad',
      detail: {modalName: trueModal.dataset.modal}
    });

    setTimeout(() => {
      open(trueModal, modalTab);
    }, 10)
  })
}

export function open(el, modalTab) {
  const modalName = el.dataset.modal;
  $.qs('body').classList.add(`modal-${modalName}-active`);

  if(modalTab){
    const tab = modalTab;
    $.dispatch({
      el: document,
      name: 'modalTab',
      detail: { tab, el }
    });
  }

  $.dispatch({
    el: document,
    name: 'beforeModalOpen',
    detail: { modalName }
  });

  $.dispatch({
    el: document,
    name: 'toggle:update'
  });

  $.dispatch({
    el: document,
    name: 'tabs:init'
  });
  $.dispatch({
    el: document,
    name: 'boosterInit:mobileX'
  });
  
  $.dispatch({
    el: document,
    name: 'productSizes:change'
  });
  
  if(el.dataset.scrollDisableDelay && window.scroll.data.scroll.y > 0){
    setTimeout(() => {
      scroll.disable();
      el.classList.add('modal--active');
    }, el.dataset.scrollDisableDelay)
  }
  else{
    scroll.disable();
    el.classList.add('modal--active');
  }


  window.addEventListener('keydown', onEscape);

  $.dispatch({
    el: document,
    name: 'afterModalOpen',
    detail: { modalName }
  });
}

export function close(el) {
  const modalName = el.dataset.modal;
  $.qs('body').classList.remove(`modal-${modalName}-active`);

  $.dispatch({
    el: document,
    name: 'beforeModalClose',
    detail: { modalName }
  });

  scroll.enable();
  el.classList.remove('modal--active');
  window.removeEventListener('keydown', onEscape);

  $.dispatch({
    el: document,
    name: 'afterModalClose',
    detail: { modalName }
  });
}

document.addEventListener("afterModalOpen", (e) => {
  if (e.detail.modalName === 'search') {
    document.querySelector(`.modal--${e.detail.modalName} .search-form input`).focus();
  }
})

document.addEventListener("modal:open", (e) => {
  $.qsa("[data-modal]").forEach((el) => {
    if(el.dataset.modal == e.detail.name){
      let tab = e.detail.tab;
      open(el);
      $.dispatch({
        el: document,
        name: 'modalTab',
        detail: { tab, el }
      });
    }
  })
})

document.addEventListener("modal:load", (e) => {
  load(e.detail.url, e.detail.tab);
})

document.addEventListener("modal:close", (e) => {
  $.qsa("[data-modal]").forEach((el) => {
    if(el.dataset.modal == e.detail.name){
      close(el);
    }
  })
})
