async function managecampaignActive() {
    datasource = []
    await fetchCampaigns()
}

async function fetchCampaigns() {
    let request = await httpRequest('../controllers/sms/fetchcampaigns')
    request = JSON.parse(request)
    
    if(!request.status) return notification('No records retrieved')
    
    datasource = request.data
    resolvePagination(datasource, onManageCampaignTableDataSignal) 
}

async function onManageCampaignTableDataSignal() {
    let rows = getSignaledDatasource().map((item, index) => `
        <tr>
            <td>${ item.index + 1 }</td>
            <td>${ item.name }</td>
            <td>${ item.type }</td>
            <td>${ formatDate(item.created_at) }</td>
            <td>${ item.scheduled_date ? formatDate(item.scheduled_date) : formatDate(new Date()) }</td>
            <td class="${ item.status == 'completed' ? 'text-green-600' : ''}">${ item.status }</td>
            <td class="flex items-center gap-3">
                <button title="Edit campaign" class=" material-symbols-outlined rounded-full bg-blue-600 h-8 w-8 text-white drop-shadow-md text-xs ${ item.status == 'draft' || item.status == 'scheduled' ? '' : 'hidden' }" style="font-size: 18px;" onclick="redirectWithProp(${item.id}, 'sms/campaign')">Edit</button>
                <button title="Start campaign" class=" material-symbols-outlined rounded-full bg-primary-g h-8 w-8 text-white drop-shadow-md text-xs ${ item.status == 'draft' ? '' : 'hidden' }" style="font-size: 18px;" onclick="campaignAction('launch', ${item.id}, event)">play_arrow</button>
                <button title="View campaign conversations" class=" material-symbols-outlined rounded-full bg-black h-8 w-8 text-white drop-shadow-md text-xs ${ item.status == 'draft' ? 'hidden' : '' }" style="font-size: 18px;" onclick="redirectWithProp(${item.id}, 'campaign/conversations')">chat</button>
                <button title="Delete campaign & its conversations" class=" material-symbols-outlined rounded-full bg-red-600 h-8 w-8 text-white drop-shadow-md text-xs" style="font-size: 18px;" onclick="campaignAction('delete', ${item.id}, event)">delete</button>
            </td>
        </tr>`
    )
    .join('')
    injectPaginatatedTable(rows)
}

async function campaignAction(action, campaignId, event) {
    if (action === 'delete' && !confirm('Are you sure you want to delete this?')) {
        return
    }
    
    const payload = new FormData()
    payload.append('submitaction', action)
    payload.append('campaign_id', campaignId)
    
    let request = await httpRequest(`../controllers/sms/${ action == 'delete' ? 'deletesmscampaign' : 'lauchcampaign' }`, payload, event.currentTarget, 'json')
    
    if(!request.status) {
        notification(request.message ?? 'Sorry! Unable to complete request', 0)
        return
    }
    
    notification(request.message ?? 'Campaign request completed!', 1)
    fetchCampaigns()
}

