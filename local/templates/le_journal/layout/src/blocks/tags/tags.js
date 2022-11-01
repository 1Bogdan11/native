import { breakpoints } from '~/js/helpers/breakpoints';

const tagsInit = () => {
    if(window.innerWidth <= breakpoints.desktop){
        $.each('.js-tags', el => {
            $.qsa('.js-tag', el).forEach((tag, index) => {
                if(index > 2){
                    tag.classList.add('u-hidden')   
                }
            })
            $.qs('.js-tags-more', el).addEventListener("click", (e) => {
                $.qsa('.js-tag', el).forEach((tag) => {
                    tag.classList.remove('u-hidden')   
                })
                e.target.closest("li").classList.add('u-hidden')   
            })
        })
    }
}

window.addEventListener('resize', () => {
    tagsInit();
})

tagsInit();