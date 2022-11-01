const cardColors = $.qsa('.js-card-color')
if (cardColors) {
    let cardImgContainer, picture, type, saveContent, saveClass;
    
    document.addEventListener('mouseover', e => {
        const target = e.target.closest('.js-card-color')
        if (!target) return;
        
        cardImgContainer = target.closest('.js-card-image')
        picture = $.qs('picture', cardImgContainer)
        
        if (!saveContent) {
            saveContent = picture.innerHTML
        }
        
        if (target.dataset.srcset || target.dataset.src) {
            picture.innerHTML = `
                ${target.dataset.srcset && '<source srcset="' + target.dataset.srcset + '">'}
                ${target.dataset.src && '<img src="' + target.dataset.src + '">'}
            `            
        }
        
        type = target.dataset.type
        if (cardImgContainer.classList.contains('is-item')) {
            if (!saveClass) {
                saveClass = 'is-item'                
            saveClass = 'is-item'
                saveClass = 'is-item'                
            }
            cardImgContainer.classList.remove('is-item')
        }
        if (cardImgContainer.classList.contains('is-full')) {
            if (!saveClass) {
                saveClass = 'is-full'                
            saveClass = 'is-full'
                saveClass = 'is-full'                
            }
            cardImgContainer.classList.remove('is-full')
        }
        cardImgContainer.classList.add(`is-${type}`)
    })
    
    document.addEventListener('mouseout', e => {
        const target = e.target.closest('.js-card-color')
        if (!target) return;
        
        if (!e.relatedTarget.closest('.js-card-color')) {
            picture.innerHTML = saveContent
            cardImgContainer.classList.remove('is-full', 'is-item')
            cardImgContainer.classList.add(saveClass)
            saveContent = null
            saveClass = null
        }
    })
}
