import LocomotiveScroll from 'locomotive-scroll';
import { clamp, map } from './scroll-utils';

let loco;

document.addEventListener('DOMContentLoaded', () => {
  const scrollContainer = $.qs('#js-scroll') || $.qs('#js-scroll-native');
  console.log('scrollContainer', scrollContainer);

  if (!scrollContainer) return false;

  // Scroll show/hide
  const scrollbar = {
    show: () => {
      if (loco.scroll.scrollbar)
        loco.scroll.scrollbar.classList.remove('u-hidden');
    },
    hide: () => {
      if (loco.scroll.scrollbar)
        loco.scroll.scrollbar.classList.add('u-hidden');
    }
  };

  // Scroll instance
  loco = new LocomotiveScroll({
    el: scrollContainer,
    // smooth: !$.qs('#js-scroll-native'),
    getDirection: true,
    direction: scrollContainer.dataset.direction,
    inertia: 0.7,
    scrollFromAnywhere: true
  });

  window.loco = loco;

  // Scroll data, start/stop functions
  window.scroll = {
    data: {
      direction: 'down'
    },
    start: () => {
      window.addEventListener('keydown', loco.scroll.checkKey, false);
      scrollbar.show();
      loco.start();
    },
    stop: () => {
      window.removeEventListener('keydown', loco.scroll.checkKey, false);
      scrollbar.hide();
      loco.stop();
    },
    to: scrollTo,
    update: updateScroll
  };

  // Save scroll data
  loco.on('scroll', e => {
    window.scroll.data = e;
    document.dispatchEvent(
      new CustomEvent('customScroll', {
        detail: e
      })
    );
  });
});

// Update scroll on full page load
window.addEventListener('load', () => {
  updateScroll();
});

$.delegate('[data-scrollto]', (e, btn) => {
  const selector = btn.dataset.scrollto;
  const el = $.qs(selector);

  if (el) {
    scrollTo(el);
  }
});

function scrollTo(el) {
  if (loco && loco.scrollTo) {
    loco.scrollTo(el);
  } else {
    const top = el.getBoundingClientRect().top + window.pageYOffset;

    window.scrollTo({
      top,
      behavior: 'smooth'
    });
  }
}

window.scrollToNode = scrollTo;

function updateScroll() {
  if (loco) loco.update();
}

document.addEventListener('scroll:update', () => {
  updateScroll();
});
