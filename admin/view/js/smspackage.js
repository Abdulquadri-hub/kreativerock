let smsPackageId;
async function smsPackageActive() {
    const form = document.querySelector('#smspackageform')
    if(form.querySelector('#submit')) form.querySelector('#submit').addEventListener('click', smsPackageSubmitHandler)
    datasource = []
    await fetchSmsPackages()
}

async function onSmsPackageTableDataSignal() {
    let rows = getSignaledDatasource().map((item, index) => {
        return `
            <tr>
                <td>${ item.index + 1 }</td>
                <td>${ item.packagename }</td>
                <td>${ item.numberofunits } </td>
                <td> ${ formatCurrency(item.costperunit) } </td>
                <td class="flex items-center gap-3">
                    <button title="Edit row entry" onclick="fetchSmsPackages('${item.id}')" class="material-symbols-outlined rounded-full bg-blue-500 h-8 w-8 text-white drop-shadow-md text-xs" style="font-size: 18px;">edit</button>
                    <button title="Delete row entry"s onclick="removeSmsPackage('${item.id}', event)" class="material-symbols-outlined rounded-full bg-red-600 h-8 w-8 text-white drop-shadow-md text-xs" style="font-size: 18px;">delete</button>
                </td>
            </tr>
        `
    }) .join('')
    injectPaginatatedTable(rows)
}

async function fetchSmsPackages(id) {
    
    let payload = new FormData()
    payload.append('id', id)
    
    let request = await httpRequest2('../controllers/sms/fetchpackages', id ? payload : null, null, 'json')
    if(request.data.length) {
        
        if(!id) {
            datasource = request.data
            datasource.length && resolvePagination(datasource, onSmsPackageTableDataSignal)
            return 
        } 
        
        runoptioner(document.getElementsByClassName('updater')[0])
        smsPackageId = request.data[0].id
        populateData(request.data[0])
        
    } else return notification('No records retrieved')
}


async function smsPackageSubmitHandler() {
    const ids = Array.from(document.querySelectorAll('#smspackageform input')).map( item => item.id )
    if(!validateForm('smspackageform', ids)) return
    
    const payload = getSmsPackageFormData()
    const request =  await httpRequest2(`../controllers/sms/${ smsPackageId ? 'editpackage' : 'createpackage' }`, payload, document.querySelector('#submit'))
    if(!request.status) {
        return notification(request.message ?? 'Sorry! We unable to perform task', 0)
    }
    
    notification('Saved successfully!', 1);
    location.reload()
}

async function removeSmsPackage(id, event) {
    if(!confirm('Sure you want to delete this package?')) return 
    
    let payload = new FormData()
    payload.append('id', id)
    
    const request =  await httpRequest2(`../controllers/sms/removepackage`, payload, event.target)
    if(!request.status) {
        return notification(request.message ?? 'Sorry! We unable to perform task', 0)
    }
    
    notification('Item removed successfully!', 1);
    fetchSmsPackages()
}

function getSmsPackageFormData() {
    const payload = getFormData2(document.getElementById('smspackageform'))
    
    if(smsPackageId) {
        payload.append('id', smsPackageId)
    }
    
    return payload
}
