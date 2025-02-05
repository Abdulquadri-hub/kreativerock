async function selectUserActive() {
    datasource = selectusers =  []
    await fetchSelectUsers()
}

async function fetchSelectUsers() {
    let request = await httpRequest('../controllers/fetchusers.php')
    
    if(!request.status) {
        notification('No records retrieved')
        return
    }
    
    
    if(request.data.length) {
        datasource = selectusers = request.data.filter( item => item.role !== 'SUPERADMIN')
        resolvePagination(datasource, onSelectUsersTableDataSignal)
    }

}

async function onSelectUsersTableDataSignal() {
    let rows = getSignaledDatasource().map((item, index) => `
    <tr class="${item.role == 'SUPERADMIN' ? 'opacity-0' : ''}" >
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
        <td class="flex items-center gap-3">
            <button onclick="selectUseredit('${item.email}')" title="Edit row entry" class="${item.role == 'SUPERADMIN' ? 'hidden' : ''} material-symbols-outlined rounded-full bg-blue-600 h-8 w-8 text-white drop-shadow-md text-xs" style="font-size: 18px;">edit</button>
            <button onclick="selectUser(event, ${item.id})" title="Edit row entry" class="${item.role == 'SUPERADMIN' ? 'hidden' : ''} material-symbols-outlined rounded-full bg-amber-500 h-8 w-8 text-white drop-shadow-md text-xs" style="font-size: 18px;">done</button>
        </td>
    </tr>`
    )
    .join('')
    injectPaginatatedTable(rows)
}

function selectUseredit(id){
    sessionStorage.setItem('edituser', id)
    router.navigate('/user/profile')
}


async function selectUser(event, index) {
    let selectedItem = selectusers.find(item => item.id == index)
    
    if(!selectedItem) {
        return
    }
        
    if(!confirm('You are about to select this user')) return
    
    let payload = new FormData()
    payload.append('email', selectedItem.email)
    
    let request = await httpRequest('../controllers/selectuser.php', payload, event.target)
    
    if(!request.status) {
        notification(request.message, 0)
        return
    }
    
    document.getElementById('tabledata').innerHTML = ''
    
    notification('User selected successfully!', 1)
    fetchSelectUsers()
}
