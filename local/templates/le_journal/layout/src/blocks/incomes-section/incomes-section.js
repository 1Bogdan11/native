import { dispatchScrollUpdate } from '~/js/helpers/utils';

const changeIncomeTab = (selectIndex) => {
    $.each('.js-incomes-tabs .cards-line', (el, index) => {
        el.classList.remove('is-active');
        el.classList.remove('is-inview');
        if(el.hasAttribute("data-scroll")){
            el.removeAttribute("data-scroll");
            dispatchScrollUpdate();
        }
        if(index == selectIndex){
            el.classList.add('is-active');
        }
    })
}

document.addEventListener("incomesSliderChange", (event) => {
    changeIncomeTab(event.detail.e.realIndex);
})

$.each('.js-incomes-select', (el) => {
    el.addEventListener("change", () => {
        changeIncomeTab(el.selectedIndex);
    })
})