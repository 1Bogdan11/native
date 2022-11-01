import { breakpoints } from '~/js/helpers/breakpoints';
import intersectRect from "intersect-rect"

const galleryPinsInit = () => {
    const galleryMobilePopup = $.qs(".js-gallery__mobile-popup");
    const hidePins = () => {
        $.each('.js-pin', el => {
            el.classList.remove("is-active");
        })
    }

    const checkIntersectRect = (rect) => {
        ["top", "left", "right", "bottom"].forEach((direction) => {
            rect.classList.remove(`is-${direction}`)
        })
        const horizontalIntersect = intersectRect(rect.getBoundingClientRect(), $.qs("[data-intersect=left]").getBoundingClientRect()) ? "left" : "right";
        const verticalIntersect = intersectRect(rect.getBoundingClientRect(), $.qs("[data-intersect=top]").getBoundingClientRect()) ? "top" : "bottom";
        rect.classList.add(`is-${horizontalIntersect}`);
        rect.classList.add(`is-${verticalIntersect}`);
    }
    const outsideHandler = (e) => {
        $.each('.js-pin__button', el => {
            if(!el.contains(e.target)) 
                hidePins();
        })
    }

    const mobileOutsideHandler = (e) => {
        $.each('.js-pin__button', el => {
            if(!e.target.classList.contains("js-pin__button"))
                galleryMobilePopup.classList.remove("is-active");
        })
    }

    $.each('.js-pin', el => {
        const button = $.qs(".js-pin__button", el).cloneNode();
        $.qs(".js-pin__button", el).remove();
        el.appendChild(button);

        const description = $.qs("[data-description]", el);
        const title = $.qs("[data-title]", el);

        const enterHandler = () => {
            hidePins();
            if(window.innerWidth >= breakpoints.desktop)
                el.classList.toggle("is-active")
            else{
                $.qs("[data-title]", galleryMobilePopup).innerText = title ? title.innerText : '';
                $.qs("[data-description]", galleryMobilePopup).innerText = description ? description.innerText: "";
                galleryMobilePopup.classList.add("is-active");
            }
        }

        const leaveHandler = () => {
            hidePins();
        }

        if(window.innerWidth >= breakpoints.desktop){
            button.removeEventListener("mouseenter", enterHandler);
            button.removeEventListener("mouseleave", leaveHandler);
            button.addEventListener("mouseenter", enterHandler);
            button.addEventListener("mouseleave", leaveHandler);
        }
        else{
            button.addEventListener("click", enterHandler);
        }
        checkIntersectRect(el);
    })
    if(window.innerWidth >= breakpoints.desktop)
        document.addEventListener('click', (e) => outsideHandler(e));
    else
        document.addEventListener('click', (e) => mobileOutsideHandler(e));
}
galleryPinsInit();

document.addEventListener("gallery:updated", () => {
    galleryPinsInit()
})