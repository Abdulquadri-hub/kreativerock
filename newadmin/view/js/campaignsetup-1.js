function campaignSetup1Active() {
    const params = JSON.parse(sessionStorage.getItem('c-params'))
    
    document.querySelector('[name="header"]').innerHTML = `
        <h4 class="text-default-900 text-2xl font-semibold">Create a ${ params.campaignType } campaign</h4>
        <p>Keep subscribers engaged by sharing your latest news, promoting your bestselling products, or announcing an upcoming event.</p>
    `
    document.querySelector('#submit').addEventListener('click', function() {
        this.disabled = true
        campaignSubmitHandler()
    })
}

function campaignSubmitHandler() {
    if (!validateForm('campaignform', ['campaignname'])){
        document.querySelector('#submit').disabled = false
        return
    }
    
    const params = JSON.parse(sessionStorage.getItem('c-params'))
    params.campaignname = document.querySelector('[name="campaignname"]').value.trim()
    
    sessionStorage.setItem('c-params', JSON.stringify(params))
    router.navigate('/campaign/setup/edit')
}