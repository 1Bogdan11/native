const getOrdering = (attribute) => {
    return $.qs(`.ordering`, $.qs(`[data-modal="${attribute}"]`));
};

document.addEventListener("ordering:reloadBefore", () => {
    const ordering = getOrdering("ordering-modal");
    ordering.classList.add("is-reload");
});

document.addEventListener("ordering:changeBefore", () => {
    const ordering = getOrdering("ordering-modal");
    ordering.classList.remove("is-active");
});

document.addEventListener("ordering:reloadAfter", (e) => {
    document.dispatchEvent(new CustomEvent("select:update"));
    const ordering = getOrdering("ordering-modal");
    setTimeout(() => {
        ordering.classList.remove("is-reload");
    }, 100)
});

document.addEventListener("ordering:changeAfter", (e) => {
    document.dispatchEvent(new CustomEvent("select:update"));
    const ordering = getOrdering("ordering-modal");
    ordering.classList.add("is-active");
    $.dispatch({
        el: document,
        name: 'input:reload'
    });
    $.dispatch({
        el: document,
        name: 'uniquie:init'
    });
    $.dispatch({
        el: document,
        name: 'suggestions:init'
    });
});