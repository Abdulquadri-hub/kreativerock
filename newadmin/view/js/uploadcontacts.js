var limit = 50;
var limitIncrement = 50;
var results = [];
var firstTimeLoaded = true
var trash = new Set()
const contactHeaders = ['CONTACT_ID', 'EMAIL', 'FIRSTNAME', 'LASTNAME', 'SMS', 'LANDLINE_NUMBER', 'WHATSAPP']

async function uploadcontactsActive() {
    initUploadComponent()
}

function initUploadComponent() {
    const fileInput = document.getElementById('file');
    const uploadTrigger = document.querySelector('[data-hs-file-upload-trigger="file"]');
    const previewContainer = document.querySelector('[data-hs-file-upload-previews]');
    const template = document.querySelector('[data-hs-file-upload-preview]');
    
    const allowedExtensions = ['csv', 'txt'];
    
    uploadTrigger.addEventListener('click', () => {
        fileInput.click();
    });
    
    fileInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
    
        previewContainer.innerHTML = '';
    
        if (file) {
          const fileExtension = file.name.split('.').pop().toLowerCase();
        
          if (!allowedExtensions.includes(fileExtension)) {
            notification('Unsupported file type. Please upload a .csv or .txt file.', 0);
            fileInput.value = '';
            fileInput.removeAttribute('data-ext')
            return;
          }
        
        
          const previewClone = template.content.cloneNode(true);
          const fileNameSpan = previewClone.querySelector('[data-hs-file-upload-file-name]');
          const fileExtSpan = previewClone.querySelector('[data-hs-file-upload-file-ext]');
        
          fileNameSpan.textContent = file.name.split('.').slice(0, -1).join('.');
          fileExtSpan.textContent = fileExtension;
        
          previewContainer.appendChild(previewClone);
          fileInput.setAttribute('data-ext', fileExtension)
        }
    });
}

async function createContactSubmitHandler(button) {
    const payload = getContactsUploadFormData()
    
    const request = await httpRequest('../../admin/controllers/sms/contact', payload, button)
    
    if(!request) {
        notification(request.message ?? 'Sorry! Unable to complete task', 0)
        closeImportProgress()
        toggleContactsPreview()
        return
    }
    
    if(request.errors.length) {
        notification("Import failed! Headers don't match. Please download the sample file.", 0)
        closeImportProgress()
        toggleContactsPreview()
        return
    }
    
    notification('Contacts imported successfully', 1)
    closeImportProgress()
    toggleContactsPreview()
    
}

function getContactsUploadFormData() {
    const contacts = results.data.slice()
    const params = {
        contacts,
        headers: results.meta.fields,
        contactsId: `${ new Date().toLocaleDateString() } | ${ new Date().toLocaleTimeString() } | ${ results.data.length} Contacts`
    }
    
    return JSON.stringify(params)
}

function getParsedPaginatedResults() {
    let result = results.data.slice()
    
    return result.slice(0, limit)
}

function parseImport(event) {
    const fileInput = document.getElementById('file');
    event.target.disabled = true

    Papa.parse(fileInput.files[0], {
        header: true,
        skipEmptyLines: true,
        dynamicTyping: true,
        complete: (result) => {
            results = result
            
            results.data = result.data.slice().map(item => {
              const obj = {};
              for (const prop in item) {
                const lowerProp = prop.toLowerCase().trim();
                obj[lowerProp.replaceAll(' ', '_')] = item[prop];
              }
              return obj;
            });
            
            loadParseData()
            event.target.disabled = false
        },
        error: (error) => {
          event.target.disabled = false
          notifcation('Error parsing file:',  0);
          console.log(error)
        },
     }); 
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
        <th scope="col" class="px-6 py-3 text-start text-sm text-default-500 capitalize">${ formatValue(item.replace(' ', '&nbsp;')) }</th>
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
        document.querySelector('[onclick="closeImportProgress()"]').click()
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

function alertContactClose() {
    document.querySelector('[name="cancel-modal"]').click()
}

function toggleContactsPreview() {
    document.querySelector('[name="modal"]').click()
}

function closeImportProgress() {
    document.getElementById('file').value = null
    document.querySelector('[data-hs-file-upload-previews]').innerHTML = null
    results = []
    limit = 20;
    limitIncrement = 20;
    firstTimeLoaded = true
}