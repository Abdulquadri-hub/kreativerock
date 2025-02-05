let campaigns = [];
async function campaignconversationsActive() {
    await fetchConversationCampaigns()
    
    if(document.getElementById('submit')) {
        document.getElementById('submit').addEventListener('click', replyConversationSubmitHandler)
    }
}

async function replyConversationSubmitHandler(event) {
    const form = document.getElementById('messageform')
    
    if(!form.querySelector('textarea').value.trim().length) {
        return
    }
    
    if(!confirm('You are about to send this reply?')){
        return
    }
    
    const payload = new FormData()
    payload.append('campaignid', document.querySelector('[name="campaigns"]').value)
    payload.append('phonenumber', '+' + document.querySelector('#messageform').dataset.phonenumber)
    payload.append('reply', document.querySelector('#messageform textarea').value.trim())
    
    
    event.target.disabled = true
    let request = await httpRequest('../controllers/sms/sendconversationreply', payload, event.target, 'json')
    
    if(!request.status) {
        notification(request.message ?? 'Reply not sent! try again...', 0)
        event.target.disabled = false
        return
    }
    
    notification('Reply sent successfully!', 1)
    event.target.disabled = false
    appendNewConversationReply()
    
}

function appendNewConversationReply() { 
   const div = document.createElement('div')
   div.className = 'animate__animated animate__slideInUp'

   div.innerHTML = `
        <div class="px-5 py-1 bg-white border-l-2 border-l-green-500 ">
             <h4 class="truncate font-medium text-sm">You</h4>
             <div class="flex justify-between items-center gap-2 overflow-hidden">
                 <div>${ document.querySelector('#messageform textarea').value }</div>
             </div>
        </div>
        <div class="italics text-xs text-right mt-1 opacity-50">${ new Date().toLocaleString() }</div>
    `
    document.querySelector('[name="conversation"]').insertBefore(div, document.querySelector('[name="conversation"]').children[1])
    document.querySelector('#messageform textarea').value = ''
    
}

async function fetchConversationCampaigns() {
    let request = await httpRequest('../controllers/sms/fetchcampaigns')
    request = campaigns = JSON.parse(request)
    
    if(!request.status) {
        notification('No records retrieved')
        const html = `
            <div class="w-full h-max p-20 flex flex-col items-center justify-center mt-20">
                <div class="material-symbols-outlined opacity-60" style="font-size: 60px">folder_open</div>
                <div class="text-xs">No conversations yet!</div>
            </div>
        `
        document.getElementById('content').innerHTML = html
        return
    }
    
    if(!request.data.length) {
        const html = `
            <div class="w-full h-max p-20 flex flex-col items-center justify-center mt-20">
                <div class="material-symbols-outlined opacity-60" style="font-size: 60px">folder_open</div>
                <div class="text-xs">No conversations yet!</div>
            </div>
        `
        document.getElementById('content').innerHTML = html
        return
    }
    
    renderConversations()
}

function renderConversations() {
    let selectedItem = null
    const campaignOptions = campaigns.data.filter( item => !['draft', 'scheduled'].includes(item.status)).map( item => {
        return `
            <option value="${ item.id }"> ${ item.name.length > 20 ? item.name.substr(0, 20) + '...' : item.name } </option>
        `
    }).join('')
    
    
    const template = document.importNode(document.getElementById('template').content, true)
    template.querySelector('[name="campaigns"]').innerHTML = campaignOptions
    
    
    document.getElementById('content').innerHTML = ''
    document.getElementById('content').appendChild(template)
    selectedItem = campaigns.data[0]
    
    
     if(document.querySelector('[name="campaigns"]')) {
        if(checkForProp()) {
            document.querySelector('[name="campaigns"]').value = checkForProp()
            const item = campaigns.data?.find( item => item.id == checkForProp())
            selectedItem = item
        }
        document.querySelector('[name="campaigns"]').addEventListener('change', function(){
            redirectWithProp(this.value, 'campaign/conversations')
        })
    }
    document.querySelector('[name="campaigntype"]').innerHTML = selectedItem.type[0].toUpperCase()
    document.querySelector('[name="campaigntype"]').parentElement.setAttribute('title', `${selectedItem.type} campaign`)
    
    if(window.matchMedia('(max-width: 768px)').matches) {
        document.querySelector('[name="conversation"]').parentElement.nextElementSibling.classList.add('hidden')
    }
    
    loadSelectedConversation()
}

async function loadSelectedConversation() {
    
    document.querySelector('[name="conversation-list"]').innerHTML =  `
        <div class="h-full p-20 flex flex-col items-center justify-center">
            <div class="loader m-auto"></div>
        </div>
    `

    let request = await httpRequest(`../controllers/sms/fetchcampaignconversations?campaign_id=${document.querySelector('[name="campaigns"]').value}`)
    request = JSON.parse(request);
    const data = request.data?.filter( item => item.status !== 'failed')
    
    document.querySelector('[name="campaignscounter"]').innerHTML = `${data?.length ? ( data?.length + ' Conversations') : 'No Conversations'}`;
    
    if(!request.status) {
        const html = `
            <div class="h-full p-20 flex flex-col items-center justify-center">
                <div class="material-symbols-outlined opacity-40" style="font-size: 60px">folder_open</div>
                <div class="text-xs text-center mt-5">${ request.message == 'Campaign conversation not found' ? 'Empty conversations list' : 'Unable to load conversations'}</div>
            </div>
        `
        document.querySelector('[name="conversation-list"]').innerHTML = html
        return
    }
    
    const conversations = data?.map( item => {
        return `
            <div onclick="loadConversation(${ item.phone_number }, event)" class="px-5 py-1 cursor-pointer hover:bg-gray-50 transition duration-500 border-r-2">
                 <h4 class="truncate font-medium text-sm invisible">Sandra Basil</h4>
                 <div class="flex justify-between items-center gap-2 overflow-hidden">
                     <div class="font-medium">${ item.phone_number }</div>
                     <span class="italics text-xs">${ new Date(item.created_at).toLocaleDateString() }</span>
                 </div>
             </div>
        `
    }).join('')
    
    document.querySelector('[name="conversation-list"]').innerHTML = data?.length ? conversations : `
        <div class="w-full h-max p-20 flex flex-col items-center justify-center mt-20">
            <div class="material-symbols-outlined opacity-60" style="font-size: 60px">folder_open</div>
            <div class="text-xs">No conversations yet!</div>
        </div>
    `
    
    if(data.length && (!window.matchMedia('(max-width: 768px)').matches)) {
        document.querySelector('[name="conversation-list"]').children[0].click()
    }
    
}


async function loadConversation(phonenumber, event) {
    Array.from(document.querySelector('[name="conversation-list"]').children).forEach( item => item.classList.remove('bg-gray-50', '!border-r-orange-500'))
    event.currentTarget.classList.add('bg-gray-50', '!border-r-orange-500')
    
    document.querySelector('[name="conversation"]').innerHTML =  `
        <div class="h-full p-20 flex flex-col items-center justify-center">
            <div class="loader m-auto"></div>
        </div>
    `
    
    const payload = new FormData()
    payload.append('campaign_id', document.querySelector('[name="campaigns"]').value)
    payload.append('phonenumber', `+${ phonenumber }`)
    let request = await httpRequest(`../controllers/sms/fetchcampaignconversationmessages`, payload, null, 'json')

    
    if(!request.status) {
        const html = `
            <div class="h-full p-20 flex flex-col items-center justify-center">
                <div class="material-symbols-outlined opacity-40" style="font-size: 60px">folder_open</div>
                <div class="text-xs text-center mt-5">Unable to load conversations</div>
            </div>
        `
        document.querySelector('[name="conversation"]').innerHTML = html
        return
    }
    
    const thread = request.data.reverse().map( item => {
        return `
            <div>
                <div class="px-5 py-1 bg-white border-l-2 border-l-${ item.direction == 'incoming' ? 'orange' : 'green' }-500 ">
                     <h4 class="truncate font-medium text-sm">${ item.direction == 'incoming' ? 'Contact user' : 'You' }</h4>
                     <div class="flex justify-between items-center gap-2 overflow-hidden">
                         <div>${ item.content ?? item.message }</div>
                     </div>
                </div>
                <div class="italics text-xs text-right mt-1 opacity-50">${ new Date(item.created_at).toLocaleString() }</div>
            </div>
        `
    }).join('')
    
    if(window.matchMedia('(max-width: 768px)').matches) {
        document.querySelector('[name="conversation"]').parentElement.nextElementSibling.classList.remove('hidden')
        document.querySelector('[name="conversation"]').parentElement.parentElement.classList.remove('-z-10')
        document.querySelector('[name="conversation"]').parentElement.parentElement.classList.add('z-10')
        
    }
    
    document.querySelector('[name="conversation"]').innerHTML = `
        <span class="opacity-60 my-3 text-xs">Converstion started <span classs="italics">${ new Date(request.data[0].created_at).toLocaleString() }</span></span>
        ${ thread }
    ` 
    document.getElementById('messageform').setAttribute('data-phonenumber', phonenumber)
    
}

function hideConversationThread() {
    if(window.matchMedia('(max-width: 768px)').matches) {
        document.querySelector('[name="conversation"]').parentElement.nextElementSibling.classList.add('hidden')
        document.querySelector('[name="conversation"]').parentElement.parentElement.classList.add('-z-10')
        document.querySelector('[name="conversation"]').parentElement.parentElement.classList.remove('z-10')
        
    }
}



