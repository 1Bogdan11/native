$.each('.js-hint', hint => {
    const breakpoints = [
        { symbols: 10, width: 150 },
        { symbols: 20, width: 200 },
    ]

    const init = () => {
        const contentLength = $.qs(".js-hint__content", hint).innerText.length;
        breakpoints.forEach((breakpoint, index) => {
            if(contentLength >= breakpoints[index].symbols){
                hint.style.width = `${breakpoint.width}px`;
            }
        })
    }
    
    init();

    hint.addEventListener("hint:update", () => {
        init();
    })
});