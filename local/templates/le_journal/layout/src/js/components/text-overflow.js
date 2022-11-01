window.addEventListener('load', () => {
    $.each('.js-text-overflow', el => {
        const styles = getComputedStyle(el)
        const maxHeight = styles.height.slice(0, -2) 
        const lineHeight = styles.lineHeight.slice(0, -2)
        el.closest('.js-toggle').classList.remove('is-active')
        let minHeight = maxHeight
        if ((maxHeight / lineHeight) > 3) {
            minHeight = lineHeight * 3
            el.closest('.js-toggle').classList.add('is-overflow')
        }
        
        el.style.setProperty(
            '--text-max-height',
            `${maxHeight}px`
        )
        el.style.setProperty(
            '--text-min-height',
            `${minHeight}px`
        )
    })
})
