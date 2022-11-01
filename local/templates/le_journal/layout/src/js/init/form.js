const formInit = () => {
    $.each('.js-form', form => {
        const formSteps = $.qsa("[data-form-step-field]", form);
        form.addEventListener("submit", (e) => {
            e.preventDefault();
        })

        form.addEventListener("form:clear", () => {
            console.log(1)
            $.qsa("input, textarea", form).forEach((field) => {
                field.value = '';
                document.dispatchEvent(new CustomEvent("input:reload"));
            })
        })
        
        if(formSteps){
            formSteps.forEach((el) => {
                el.addEventListener("input", (e) => {
                    const array = Array.from(el.dataset.formStepField.split(','));
                    if(e.target.value.length > 0){
                        $.qsa(`[data-form-step="${Number(array[0]) - 1}"]`).forEach((part) => {
                            if(part != el)
                                part.classList.remove("is-active");
                        })
                        $.qsa(`[data-form-step="${Number(array[0])}"]`).forEach((part) => {         
                            part.classList.add("is-active");
                        })
                    }
                    else{
                        $.qsa(`[data-form-step="${Number(array[0]) - 1}"]`).forEach((part) => {
                            part.classList.add("is-active");
                        })
                        array.forEach((step) => {
                            $.qsa(`[data-form-step="${Number(step)}"]`).forEach((part) => {
                                part.classList.remove("is-active");
                            })
                            $.qsa(`[data-form-step-field="${Number(step)}"]`).forEach((field) => {
                                setTimeout(() => {
                                    $.qs("input", field).value = '';
                                    $.qs("input", field).classList.remove("is-filled")
                                }, 400)
                            })
                        })
                    }
                })
            })
        }
    })
}

formInit();

document.addEventListener("beforeModalOpen", () => {
    formInit();
})
