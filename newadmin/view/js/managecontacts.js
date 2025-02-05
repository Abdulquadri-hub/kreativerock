async function managecontactsActive() {
    initializeTemplateComponents()
    
    document.querySelector('button#submit').addEventListener('click', function() {
        createContact(this)
    })
    
    datasource = []
    await fetchContacts()
}

async function fetchContacts() {
    const request = await fetchContactsGroup()
    console.log(request)

    if(!request.length) {
        notification('No records retrieved', 0)
        return 
    }
    
    contacts = request
    getSource()
}

function getSource(index = 0) {
    datasource = contacts[index].group
    resolvePagination(datasource, onTableDataSignal) 
    
    document.querySelector('[name="totalcontacts"]').innerText = `${ datasource.length } Contacts` 
    document.querySelector('[name="contact-group"]').children[0].innerHTML = contacts.map( (item, index) => `
        <li data-group="${ item.title }">
            <button onclick="selectGroup(event, ${ index })" class="flex items-center font-normal text-default-600 py-2 px-3 transition-all hover:text-default-700 hover:bg-default-400/10 rounded w-full capitalize"> ${ item.title }</button>
        </li>
    `).join('')
    
    document.querySelector('[name="contact-group"]').previousElementSibling.innerHTML = `${ contacts[0].title }<i class="i-tabler-chevron-down ms-1"></i>`
}

function selectGroup(event, index) {
    const btn = event.currentTarget

    getSource(+index)
    document.querySelector('[name="contact-group"]').previousElementSibling.innerHTML = `${ btn.parentElement.dataset.group }<i class="i-tabler-chevron-down ms-1"></i>`
}

async function onTableDataSignal() {
    let rows = getSignaledDatasource().map((item, index) => `
        <tr>
            <td class="py-3 ps-4">
                <div class="flex items-center h-5">
                    <input id="table-checkbox-1" type="checkbox" class="form-checkbox rounded">
                    <label for="table-checkbox-1" class="sr-only">Checkbox</label>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-default-800">${ item.firstname ?? '' } </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-default-800">${ item.lastname ?? '' }</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-default-800">${ item.email ?? '' }</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-default-800">${ item.sms }</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-default-800">${ item.whatsapp }</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-default-800">${ item.landline }</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-default-800">${ new Date(item.created_at).toDateString() }</td>
        </tr>
    `)
    .join('')
    injectPaginatatedTable(rows)
}
