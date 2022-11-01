const header = $.qs(".js-header")
let lastScrollTop = 0;

const headerAnimate = (directionDown, y) => {
    if(directionDown && y > 50){
        header.classList.remove("is-active")
    }
    else{
        header.classList.add("is-active")
    }
}

document.addEventListener("scroll", () => {
    const scrollTop = window.pageYOffset;
    const directionDown = scrollTop > lastScrollTop && true;
    headerAnimate(directionDown, scrollTop);
    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
})

const $navMainOpener = $.qs('[data-nav-tab=catalog]')
const $navMain = $.qs('.nav-main')
const $navMainOverlay = $.qs('.nav-main-overlay')
const $header = $.qs('.js-header')

if ($navMainOpener && $navMain) {

    $navMain.addEventListener('close', () => {
        $navMainOverlay.classList.remove('is-shown')
        $navMain.classList.remove('is-shown')
        $header.classList.remove('is-nav-shown')
        // return $.qs('.main-nav-submenu, .main-nav-submenu ul#' + $.qs(this).data('main-submenu')).classList.remove('is-shown')
    })

    $navMain.addEventListener('open', () => {
        $navMainOverlay.classList.add('is-shown')
        $navMain.classList.add('is-shown')
        $header.classList.add('is-nav-shown')

        $.dispatch({
            el: document,
            name: 'nav:open',
            detail: { el: $navMain }
        });
    })

    $navMainOpener.addEventListener('mouseover', (e) => {
        e.preventDefault()
        $navMain.dispatchEvent(new CustomEvent('open'))
    })

    const removeClickListener = () => {
        document.removeEventListener('click', outsideClickListener)
    }

    const outsideClickListener = event => {
        if (!$navMain.contains(event.target)) {
            $navMain.dispatchEvent(new CustomEvent('close'))
            removeClickListener()
        }
    }

    document.addEventListener('mousedown', outsideClickListener)
}
