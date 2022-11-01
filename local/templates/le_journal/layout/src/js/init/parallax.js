const items = $.qsa('.parallax');

if (items.length) {
  window.addEventListener('scroll', () => {
    items.forEach(el => {
      if (el.classList.contains('visible')) {
        if (!el.style.transform && !el.dataset.transformY) {
          el.style.transform = 'translateY(0)';
          el.dataset.transformY = '0';
        }
        if (el.style.transform && !el.dataset.transformY) {
          let transform = window
            .getComputedStyle(el)
            .getPropertyValue('transform')
            .split(', ');
          let translateXNum = parseInt(transform[5]);
          el.dataset.transformY = translateXNum;
        }
        const speed = el.dataset.speed || 0.5;
        const offset = (window.pageYOffset - el.dataset.position) * speed;
        const currentY = parseInt(el.dataset.transformY);
        const position = currentY + offset;

        el.style.transform = `translateY(${position}px)`;
      } else if (el.dataset.transformY) {
        el.dataset.transformY = '';
      }
    });
  });
}
