$.delegate('[data-scrollto]', (e, btn) => {
  const selector = btn.dataset.scrollto;
  const el = $.qs(selector);

  if (el) {
    const top = el.getBoundingClientRect().top + window.pageYOffset;

    window.scrollTo({
      top,
      behavior: 'smooth'
    });
  }
});
