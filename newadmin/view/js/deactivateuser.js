async function deactivateUserActive() {
    datasource = deactivateusers =  []
    await fetchDeactivateUsers() 
}

async function fetchDeactivateUsers() {
    let request = await httpRequest('../controllers/fetchusers.php')

    if(!request.status) {
        notification('No records retrieved')
        return 
    }
        
    if(request.data.length) {
        datasource = deactivateusers = request.data
        resolvePagination(datasource, onDeactivateUsersTableDataSignal)
    } 
}

async function onDeactivateUsersTableDataSignal() {
    let rows = getSignaledDatasource().map((item, index) => `
    <tr>
        <td class="py-3 ps-4">
            <div class="flex items-center h-5">
                <input id="table-checkbox-1" type="checkbox" class="form-checkbox rounded">
                <label for="table-checkbox-1" class="sr-only">Checkbox</label>
            </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-default-800">${item.firstname}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-default-800">${item.lastname}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-default-800">${item.othernames ?? ''}</td>
        <td style="text-transform:lowercase" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-default-800">${item.email}</td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-default-800">${item.address}</td> 
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-default-800">${item.status}</td>
        <td class="flex items-end gap-3">
            <button onclick="deactivateUsersItem(event, ${item.id})" title="Deactivate User" class="material-symbols-outlined rounded-full bg-red-600 h-8 w-8 text-white drop-shadow-md text-xs" style="font-size: 18px;">lock</button>
        </td>
    </tr>`
    )
    .join('')
    injectPaginatatedTable(rows)
}

async function deactivateUsersItem(event, index) {
    let deactivateedItem = deactivateusers.find(item => item.id == index)
    
    if(!deactivateedItem) {
        return
    }
    
    if(!confirm('You are about to deactivate this user')) return
    
    let payload = new FormData()
    payload.append('email', deactivateedItem.email)
    
    let request = await httpRequest('../controllers/deactivateuser', payload, event.target)
    
    if(!request.status) {
        notification(request.message, 0)
    }
    
    
    document.getElementById('tabledata').innerHTML = ''
    notification('User deactivated successfully!', 1)
    fetchDeactivateUsers()
}
