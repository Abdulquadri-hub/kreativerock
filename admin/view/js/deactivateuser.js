async function deactivateUserActive() {
    datasource = deactivateusers =  []
    await fetchDeactivateUsers() 
}

async function fetchDeactivateUsers() {
    let request = await httpRequest('../controllers/fetchusers.php')
    request = JSON.parse(request)
    if(request.status) {
        if(request.data.length) {
            datasource = deactivateusers = request.data
            resolvePagination(datasource, onDeactivateUsersTableDataSignal)
        }
    }
    else return notification('No records retrieved')
}

async function onDeactivateUsersTableDataSignal() {
    let rows = getSignaledDatasource().map((item, index) => `
    <tr>
       <td>${item.index + 1 }</td>
       <td>${item.firstname}</td>
        <td>${item.lastname}</td>
        <td>${item.othernames ? item.othernames : ''}</td>
        <td style="text-transform:lowercase">${item.email}</td>
        <td>${item.address}</td>
        <td>${item.status}</td>
        <td class="flex items-center gap-3">
            <button onclick="deactivateUsersItem(event, ${item.id})" title="Deactivate User" class="material-symbols-outlined rounded-full bg-red-600 h-8 w-8 text-white drop-shadow-md text-xs" style="font-size: 18px;">lock</button>
        </td>
    </tr>`
    )
    .join('')
    injectPaginatatedTable(rows)
}

async function deactivateUsersItem(event, index) {
    let deactivateedItem = deactivateusers.find(item => item.id == index)
    if(deactivateedItem) {
        if(!confirm('You are about to deactivate this user')) return
        let payload = new FormData()
        payload.append('email', deactivateedItem.email)
        let request = await httpRequest('../controllers/deactivateuser', payload, event.target)
        if(request.status) {
            document.getElementById('tabledata').innerHTML = ''
            notification('User deactivated successfully!', 1)
            fetchDeactivateUsers()
            return
        }
        return notification(request.message, 0)
    }
}
