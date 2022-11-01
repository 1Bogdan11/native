import { close } from '~/blocks/modal/modal';

let mobileClose = $.qsa('.mobile-close');

document.addEventListener('swiped-down', function (e) {
  const target = e.target.closest('[data-modal="basket-modal"], [data-modal="active-order"], [data-modal="map-modal"]')
  if (!target) return;

  close(target);
});

$.delegate('.mobile-close', (e, btn) => {
  btn.closest('section.modal').classList.remove('modal--active');
});
