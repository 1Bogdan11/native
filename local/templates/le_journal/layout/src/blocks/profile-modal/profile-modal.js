$.each('.js-profile-forgot', el => {
    el.addEventListener("click", (e) => {
        e.preventDefault();
        $.each('.js-profile-modal', modal => {
            modal.classList.add("is-forgot")
        })
    })
})

document.addEventListener("afterModalClose", (e) => {
    if(e.detail.modalName == "profile-modal"){
        $.each('.js-profile-modal', modal => {
            modal.classList.remove("is-forgot")
        })
    }
})