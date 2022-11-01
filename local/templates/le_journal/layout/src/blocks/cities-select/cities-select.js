import { debounce } from '~/js/helpers/utils.js';

const citiesSelectInit = () => {
    $.each('.js-cities-select', el => {  
        const input = $.qs(".js-cities-select__input", el);
        const hiddenInput = $.qs(".js-cities-select__hidden", el);
        const listContainer = $.qs(".js-cities-select__list", el)
        const emptyContainer = $.qs(".js-cities-select__empty", el);

        const setCity = (id, name) => {
            hiddenInput.value = id;
            hiddenInput.dispatchEvent(new Event('change', { 'bubbles': true }))
            input.value = name;
            el.classList.remove("is-active")
        }

        const clearInput = () => {
            el.classList.remove("is-active");
            hiddenInput.value = "";
            input.value = "";
            $.dispatch({
                el: document,
                name: 'input:reload'
            });
        }

        input.addEventListener("input", debounce(function(e) {
            el.classList.remove("is-loaded");
            listContainer.classList.remove("is-active");
            emptyContainer.classList.remove("is-active");
            if(e.target.value.length > 0){
                el.classList.add("is-active")
                window[el.dataset.function](e.target.value, function (data) {
                    listContainer.innerHTML = '';
                    el.classList.add("is-loaded");
                    if(Object.entries(data).length){
                        Object.entries(data).forEach((el) => {
                            const item = document.createElement("div");
                            item.className = "cities-select__item";
                            item.innerHTML = el[1];
                            item.addEventListener("click", () => setCity(el[0], $.qs("span", item).innerText))
                            listContainer.appendChild(item);
                        })
                        listContainer.classList.add("is-active");
                    }
                    else{
                        emptyContainer.classList.add("is-active");
                    }
                });
            }
            else{
                clearInput();
            }
        }, 500))
        document.addEventListener("click", (e) => {
            if(el.classList.contains("is-active")) {
                if(!e.target.classList.contains("cities-select__dropdown") &&
                !e.target.classList.contains("js-cities-select__input") &&
                !e.target.classList.contains("cities-select__item")){
                    clearInput();
                }
            }
        })
        
    })
}

citiesSelectInit();

document.addEventListener("ordering:changeAfter", (e) => {
    citiesSelectInit();
})

// Пример функции
window.testSearch = function(value, callback) {
    setTimeout(function() {
        let rand =  Math.floor(Math.random() * 4);
        callback([
            {
                "84": "<span>Москва</span>, Россия",
                "88": "<span>Домодедово</span>, Московская область, Россия",
                "91": "<span>Бронницы</span>, Московская область, Россия",
                "104": "<span>Фрязино</span>, Московская область, Россия"
            },
            {
                "107": "<span>Рошаль</span>, Московская область, Россия",
                "120": "<span>Лосино-Петровский</span>, Московская область, Россия",
                "4926": "<span>Зеленоград</span>, Москва, Россия",
                "4928": "<span>Троицк</span>, Москва, Россия"
            },
            {
                "84": "<span>Москва</span>, Россия",
                "88": "<span>Домодедово</span>, Московская область, Россия",
                "91": "<span>Бронницы</span>, Московская область, Россия",
                "104": "<span>Фрязино</span>, Московская область, Россия",
                "107": "<span>Рошаль</span>, Московская область, Россия",
                "120": "<span>Лосино-Петровский</span>, Московская область, Россия",
                "4926": "<span>Зеленоград</span>, Москва, Россия",
                "4928": "<span>Троицк</span>, Москва, Россия",
                "4929": "<span>Щербинка</span>, Москва, Россия",
                "4932": "<span>Московский</span>, Москва, Россия"
            },
            {}
        ][rand]);
    }, 1000);
}