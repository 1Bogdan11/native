import { dispatchScrollUpdate } from '../helpers/utils';

const tabsInit = () => {
    $.each('.js-tabs', tabs => {
        const line = $.qs(".tabs__line", tabs);
        const $buttons = $.qsa(".js-tabs__btn", tabs)

        const lineMove = (btn) => {
            line.style.width = `${btn.offsetWidth}px`;
            line.style.left = `${btn.offsetLeft}px`
        }

        $buttons.forEach($button => {
            if ($button.classList.contains('is-active')) {
                lineMove($button)
            }
        })

        const switchTab = (index) => {
            const btn = $.qsa(".js-tabs__btn")[index];

            $.qsa(".js-tabs__btn", tabs).forEach((tab) => {
                tab.classList.remove("is-active");
            })

            $.qsa(".js-tabs__content", tabs).forEach((content, contentIndex) => {
                if(contentIndex == index){
                    setTimeout(() => {
                        $.qsa(".js-tabs__content", tabs)[index].style = "height: initial";
                    }, tabs.dataset.animationEnd - tabs.dataset.switchDelay)
                    setTimeout(() => {
                        $.qsa(".js-tabs__content", tabs)[index].classList.add("is-active");
                    }, tabs.dataset.animationEnd);
                    btn.classList.add("is-active");
                }
                else{
                    setTimeout(() => {
                        content.style = "height: 0"

                        dispatchScrollUpdate();
                    }, tabs.dataset.animationEnd)
                    content.classList.remove("is-active");
                    content.classList.remove("is-inview");
                    content.removeAttribute("data-scroll");

                }
            })
            
            if(btn.classList.contains("is-active") && line){
                lineMove(btn);
            }
        }
        $.qsa(".js-tabs__btn", tabs).forEach((btn, index) => {
            btn.addEventListener("click", (e) => {
                e.preventDefault();
                switchTab(index);
            })
        })
    })
}

tabsInit();

document.addEventListener("tabs:init", () => {
    tabsInit();
})
