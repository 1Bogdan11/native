import declOfNum from '~/js/helpers/declensions'
import { breakpoints } from '~/js/helpers/breakpoints';

const filtersButtonUpdate = () => {
    let globalCounter = 0;
    $.each('[data-filter-active]', el => {
        globalCounter++;
    })

    $.each(".js-filters-btn", el => {
        $.qs(".filter-status__counter", el).innerText = globalCounter;
        if(globalCounter > 0){
            el.classList.add("is-active")
        }
        else{
            el.classList.remove("is-active")
        }
    })
}

const filtersClear = (e) => {
    window.lastAction = 'reset';
    e.preventDefault();
    $.each(".js-filters-btn", el => {
        el.classList.remove("is-active")
    })
    $.qsa('.jsFilterItem').forEach((filter) => {
        if(filter.checked){
            filter.checked = false;
            filter.dispatchEvent(new window.Event('change'))
        }
    });
    $.qsa(".js-range").forEach((range) => {
        const filter = range.closest("[data-filter-active]");
        $.dispatch({
            el: range,
            name: 'range:clear',
        });

        if(filter){
            $.qs(".filter-status", filter).classList.remove("is-active");
            filter.removeAttribute("data-filter-active")
        }

        filtersButtonUpdate();
    });

}

$.each('.js-filter-reset', el => {
    el.addEventListener("click", filtersClear)
})

$.each('[data-filter]', el => {
    const checkboxes = $.qsa("input[type=checkbox]", el);
    const radios = $.qsa("input[type=radio]", el);
    const status = $.qs(".js-filter__status", el);
    const range = $.qs(".js-range", el);
    let counter = []

    const statusUpdate = (counter) => {
        if(counter > 0)
            status.classList.add("is-active")
        else
            status.classList.remove("is-active")

        if(el.dataset.filter === "multiply")
            $.qs("span", status).innerText = counter;

        status.classList.add("is-update")
        setTimeout(() => {
            status.classList.remove("is-update")
        }, 200)
        if(counter > 0)
            el.setAttribute("data-filter-active", true)
        else
            el.removeAttribute("data-filter-active")

        filtersButtonUpdate();
    }

    const fieldsOnLoad = (fields) => {
        fields.forEach((field) => {
            if(field.checked){
                counter++;
            }
        })
        statusUpdate(counter)
    }

    const fieldsChange = (fields) => {
        const type = el.dataset.filter;
        fields.forEach((field) => {
            field.addEventListener("change", () => {
                if(field.checked)
                    counter++;
                else
                    counter = type == "single" ? 0 : counter - 1;
                statusUpdate(counter)
            })
        })
    }

    const fieldsClear = (fields) => {
        fields.forEach((field) => {
            field.checked = false;
            field.dispatchEvent(new window.Event('change'))
        })
    }

    if(range){
        range.addEventListener("range:change", () => {
            statusUpdate(1);
        })
    }

    if(status){
        status.addEventListener("click", () => {
            if(checkboxes)
                fieldsClear(checkboxes)
            if(radios)
                fieldsClear(radios)
            if(range){
                $.dispatch({
                    el: range,
                    name: 'range:clear',
                });
            }
            counter = 0;
            statusUpdate(counter)
        })
    }

    if(checkboxes){
        fieldsChange(checkboxes);
        fieldsOnLoad(checkboxes);
    }

    if(radios){
        fieldsChange(radios);
        fieldsOnLoad(radios);
    }
})

document.addEventListener("filters:clear", () => {
    filtersClear();
})

document.addEventListener("filters:button", (e) => {
    $.each(".js-filters-btn", el => {
        if(e.detail.show)
            el.classList.add("is-active")
        else
            el.classList.remove("is-active")

        if(e.detail.count)
            $.qs(".filter-status__counter", el).innerText = e.detail.count;
    })
})

document.addEventListener("filters:count", (e) => {
    const desktop = window.innerWidth >= breakpoints.desktop;
    $.each(desktop ? '.js-filters-count' : '.js-filters-count__mobile', el => {
        el.classList.add("is-loading")
        if(e.detail.show){
            const desktopText = `${declOfNum(e.detail.count, ["Найден", "Найдено", "Найдено"])} ${e.detail.count} ${declOfNum(e.detail.count, ["товар", "товара", "товаров"])}`;
            const mobileText = e.detail.count;
            setTimeout(() => {
                if(e.detail.count)
                    el.innerText = desktop ? desktopText : mobileText;
            }, 400)
            setTimeout(() => {
                el.classList.remove("is-loading")
            }, 800)
            el.classList.add("is-active")
        }
        else
            el.classList.remove("is-active")
    })
})

document.addEventListener("DOMContentLoaded", () => {
    filtersButtonUpdate();
})
