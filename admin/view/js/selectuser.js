async function selectUserActive() {
    datasource = selectusers =  []
    await fetchSelectUsers()
}

async function fetchSelectUsers() {
    let request = await httpRequest('../controllers/fetchusers.php')
    request = JSON.parse(request)
    if(request.status) {
        if(request.data.length) {
            datasource = selectusers = request.data.filter( item => item.role !== 'SUPERADMIN')
            resolvePagination(datasource, onSelectUsersTableDataSignal)
        }
    }
    else return notification('No records retrieved')
}

async function onSelectUsersTableDataSignal() {
    let rows = getSignaledDatasource().map((item, index) => `
    <tr class="${item.role == 'SUPERADMIN' ? 'opacity-0' : ''}" >
        <td>${item.index + 1 }</td>
        <td>${item.firstname}</td>
        <td>${item.lastname}</td>
        <td>${item.othernames ?? ''}</td>
        <td style="text-transform:lowercase">${item.email}</td>
        <td>${item.address}</td> 
        <td>${item.status}</td> 
        <td class="flex items-center gap-3">
            <button onclick="selectUseredit('${item.email}')" title="Edit row entry" class="${item.role == 'SUPERADMIN' ? 'hidden' : ''} material-symbols-outlined rounded-full bg-blue-600 h-8 w-8 text-white drop-shadow-md text-xs" style="font-size: 18px;">edit</button>
            <button onclick="selectUser(event, ${item.id})" title="Edit row entry" class="${item.role == 'SUPERADMIN' ? 'hidden' : ''} material-symbols-outlined rounded-full bg-primary-g h-8 w-8 text-white drop-shadow-md text-xs" style="font-size: 18px;">done</button>
        </td>
    </tr>`
    )
    .join('')
    injectPaginatatedTable(rows)
}

function selectUseredit(id){
    sessionStorage.setItem('edituser', id)
    document.getElementById('profile').click()
}


async function selectUser(event, index) {
    let selectedItem = selectusers.find(item => item.id == index)
    if(selectedItem) {
        if(!confirm('You are about to select this user')) return
        let payload = new FormData()
        payload.append('email', selectedItem.email)
        let request = await httpRequest('../controllers/selectuser.php', payload, event.target)
        if(request.status) {
            document.getElementById('tabledata').innerHTML = ''
            notification('User selected successfully!', 1)
            fetchSelectUsers()
            return
        }
        return notification(request.message, 0)
    }
}
