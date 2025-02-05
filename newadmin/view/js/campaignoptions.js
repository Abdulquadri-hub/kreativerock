function campaignoptionsActive() {
    const optionsContainer = document.querySelector('[name="options"]')
    
    Array.from(optionsContainer.children).forEach( item => {
        item.addEventListener('click', e => selectCampaignOption(e))
    })
}

function selectCampaignOption(event) {
    const selected = event.currentTarget
    const campaignType = selected.dataset.type

    
    if(!campaignType) {
        notification('Select a valid campaign type')
        return
    }
    
    sessionStorage.setItem('c-params', JSON.stringify({ campaignType }))
    router.navigate('/campaign/setup/name')
}