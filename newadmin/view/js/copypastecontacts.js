var limit = 50;
var limitIncrement = 50;
var results = [];
var firstTimeLoaded = true
var trash = new Set()
const contactHeaders = ['CONTACT_ID', 'EMAIL', 'FIRSTNAME', 'LASTNAME', 'SMS', 'LANDLINE_NUMBER', 'WHATSAPP']

function copypastecontactsActive() {
    const textarea = document.getElementById("contactTextarea");
    const importButton = document.getElementById("importButton");

    textarea.addEventListener("input", toggleImportButton);
    textarea.addEventListener("paste", () => {
        setTimeout(toggleImportButton, 0);
    });
    textarea.addEventListener("change", toggleImportButton);
    
    importButton.addEventListener("click", parseStringCSV);
}

async function createContactSubmitHandler(button) {
    const payload = getContactsUploadFormData()
    
    const request = await httpRequest('../../admin/controllers/sms/contact', payload, button)
    
    if(!request) {
        notification(request.message ?? 'Sorry! Unable to complete task', 0)
        closeImportProcess()
        toggleContactsPreview()
        return
    }
    
    if(request.errors.length) {
        notification("Import failed! Headers don't match. Please download the sample file.", 0)
        closeImportProcess()
        toggleContactsPreview()
        return
    }
    
    notification('Contacts imported successfully', 1)
    closeImportProcess()
    toggleContactsPreview()
    
}

function getContactsUploadFormData() {
    const contacts = results.data.slice()
    const params = {
        contacts,
        headers: results.meta.fields,
        contactsId: `${ new Date().toLocaleDateString() } | ${ new Date().toLocaleTimeString() } | ${ results.data.length} Contacts`,
        type: 'text'
    }
    
    return JSON.stringify(params)
}

function toggleImportButton() {
    const textarea = document.getElementById("contactTextarea");
    const importButton = document.getElementById("importButton");
    
    if (textarea.value.trim().length > 0) {
        importButton.disabled = false;
        importButton.classList.remove("cursor-not-allowed");
        
    } else {
        importButton.disabled = true;
        importButton.classList.add("cursor-not-allowed");
    }
}

function copySyntax() {
    const syntax = `CONTACT_ID,EMAIL,FIRSTNAME,LASTNAME,SMS,LANDLINE_NUMBER,WHATSAPP
123456,emma@example.com,Emma,Dubois,33612345678,33612345678,33612345678
789123,mickael@example.com,Mickael,Parker,15555551234,15555551234,15555551234
456789,ethan@example.com,Jakob,Müller,4930901820,4930901820,4930901820`;

    navigator.clipboard.writeText(syntax)
    .then(() => {
        notification('Expected syntax copied to clipboard!', 1);
    })
    .catch(err => {
        console.error('Failed to copy syntax: ', err);
    });
}

function parseStringCSV() {
    const textarea = document.getElementById("contactTextarea");

    Papa.parse(textarea.value.trim(), {
        header: true,
        skipEmptyLines: true,
        dynamicTyping: true,
        complete: (result) => {
            results = result
            results.data = result.data.slice().map(item => {
              const obj = {};
              for (const prop in item) {
                const lowerProp = prop.toLowerCase();
                obj[lowerProp] = item[prop];
              }
              return obj;
            });
            
            
            loadParseData()
            event.target.disabled = false
        },
        error: (error) => {
          event.target.disabled = false
          notifcation('Error parsing CSV:',  0);
          console.log(error)
        },
     });
}

function toggleContactsPreview() {
    document.querySelector('[name="modal"]').click()
}

function getParsedPaginatedResults() {
    let result = results.data.slice()
    
    return result.slice(0, limit)
}

function loadParseData() {
    const data = getParsedPaginatedResults()
    fileParseSucess(data)
}

function fileParseSucess(data) {
    if(!data.length) {
        firstTimeLoaded = true;
        document.querySelector('[name="appender"]').classList.add('hidden')
        return
    }
    
    function allIn(a, b) {
        return a.every(item => b.includes(item));
    }
    
    const keysOfFirstRow = Object.keys(data[0])
    const expectedHeaders = contactHeaders.slice().map( item => item.toLowerCase())
    
    if(!allIn(keysOfFirstRow, expectedHeaders)) {
        notification("Import failed! Headers don't match. Please download the sample file.", 0)
        return
    } 
    
    // assign ids to rows 
    data.forEach((item, index) => item.rowid = index)
    const headerColumns = results.meta.fields ?? contactHeaders
    
    const tableheadRows =  headerColumns.map( item => `
        <th scope="col" class="px-6 py-3 text-start text-sm text-default-500 capitalize">${ formatValue(item.replace('_', '&nbsp;').replace(' ', '&nbsp;')) }</th>
    `).join('')
    
    
    const tableRows = data.slice().map(item => {
        const rows = Object.keys(item)
            .filter(prop => prop !== 'rowid') 
            .map(prop => `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-default-800">
                    ${ formatValue(item[prop]) }
                </td>
            `).join('');
    
        return `
            <tr>
                <td class="py-3 ps-4">
                    <div class="flex items-center h-5">
                        <input value="${item.rowid}" type="checkbox" class="form-checkbox rounded" onchange="addToDelete(event, ${item.rowid})">
                        <label class="sr-only">Checkbox</label> 
                    </div>
                </td>
                ${rows} 
            </tr>
        `;
    }).join('');
 
    
    const table = document.getElementById('modal')
    table.querySelector('thead').innerHTML = `
        <tr>
            <th scope="col" class="py-3 ps-4">
                <div class="flex items-center h-5">
                    <input id="table-checkbox-all" type="checkbox" class="form-checkbox rounded" onchange="addAllToDelete(event)">
                    <label for="table-checkbox-all" class="sr-only">Checkbox</label>
                </div>
            </th>
            ${ tableheadRows }
        </tr>
    `
    table.querySelector('tbody').innerHTML = tableRows
    table.querySelector('.table-status').innerText = `${ results.data.length } Contacts parsed completely`
    
    
    if(firstTimeLoaded){
        firstTimeLoaded = false;
        toggleContactsPreview()
        
        document.querySelector('[name="appender"]').classList.remove('hidden')
        document.querySelector('[name="appender"]').children[0].addEventListener('click', function() {
            if(Array.from(document.querySelector('tbody').children).length < results.data.length) {
                this.disabled = true;
                this.style.opacity = 0.4
                limit += (limitIncrement); 
                
                loadParseData()
                
                return
            }
        })
    }
    
    document.querySelector('[name="appender"]').children[0].disabled = false;
    document.querySelector('[name="appender"]').children[0].style.opacity = 1
    

    if((Array.from(document.querySelector('tbody').children).length == results.data.length)) {
        document.querySelector('[name="appender"]').classList.add('hidden')
    }
    
}

function addToDelete(event, id) {
    if (event.target.checked) {
        trash.add(+id);
    } else {
        trash.delete(+id);
    }

    const deleteContainer = document.querySelector('[name="delete"]');

    if (trash.size > 0) {
        deleteContainer.innerHTML = `<button type="button" class="btn bg-danger text-white rounded-full" onclick="removeRowsFromContact()"> Delete ${trash.size} records </button>`;
    } else {
        deleteContainer.innerHTML = '';
    }
}

function addAllToDelete(event) {
    const deleteContainer = document.querySelector('[name="delete"]');

    if (event.target.checked) {
        Array.from(document.querySelectorAll('tbody input[type="checkbox"]')).forEach( item => {
            item.checked = true
            trash.add(+item.value)
        })
        
        deleteContainer.innerHTML = `<button type="button" class="btn bg-danger text-white rounded-full" onclick="removeRowsFromContact()"> Delete ${trash.size} records </button>`;
        return
    }
    
    Array.from(document.querySelectorAll('tbody input[type="checkbox"]')).forEach( item => {
        item.checked = false
        trash.delete(+item.value)
    })
    deleteContainer.innerHTML = ``;
}

function removeRowsFromContact() {
    const ids = Array.from(trash)
    
    ids.forEach( item => {
        results = results.filter( record => record.rowid !== item)
        trash.delete(item)
    })
    
    if(!results.length) {
        document.querySelector('[onclick="closeImportProcess()"]').click()
        return
    }
    
    loadParseData()
}

function formatValue(str) {
    const newStr = String(str)
        .replace(/[\[\]']/g, '')
        .split('|')
        .join(',')
        
    return newStr
}

function closeImportProcess() {
    document.getElementById("contactTextarea").value = null
    importButton = document.getElementById("importButton").disabled = true
    results = []
    limit = 20;
    limitIncrement = 20;
    firstTimeLoaded = true
}