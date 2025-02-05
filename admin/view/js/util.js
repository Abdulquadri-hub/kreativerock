// constants
let CONFIG = {
    user: null
};


async function httpRequest(url, payload=null, button=null) {

    try {

        let result, res;

        if(button) { 
            button.disabled = true 
        }

        if(button?.querySelector('.btnloader')) { 
            button.querySelector('.btnloader').style.display = 'block' 
        }

        if(payload) {
            result = await fetch(url, {method:'POST', body: payload, headers: new Headers()})
            if(result) {
                res = await result.json()
            }
            else return notification('Unable to perform request.', 0)
        }
        else {
           result = await fetch(url)
           if(result) {
             res = await result.text() 
           }
           else return notification('Unable to perform request.', 0)
           
        }
        return res
    }
    catch(e) { 
        console.log(e)
    }
    finally {
        if(button) { 
            button.disabled = false 
        }
        if(button?.querySelector('.btnloader')) { 
            button.querySelector('.btnloader').style.display = 'none' 
        }
    }
 }

async function httpRequest2(url, payload=null, button=null, type="text") {

    try {

        let result, res;

        if(button) { 
            button.disabled = true 
        }

        if(button?.querySelector('.btnloader')) { 
            button.querySelector('.btnloader').style.display = 'block' 
        }

        if(payload) {
            result = await fetch(url, {method:'POST', body: payload, headers: new Headers()})
            if(result) {
                res = await result.json()
            }
            else return notification('Unable to perform request.', 0)
        }
        else {
           result = await fetch(url)
           if(result) {
             if(type != "json")res = await result.text() 
             if(type == "json")res = await result.json() 
           }
           else return notification('Unable to perform request.', 0)
           
        }
        return res
    }
    catch(e) { 
        console.log(e)
    }
    finally {
        if(button) { 
            button.disabled = false 
        }
        if(button?.querySelector('.btnloader')) { 
            button.querySelector('.btnloader').style.display = 'none' 
        }
    }
 }

function notification(message, type=undefined, timeout=5000) {

    let html;
    if(type === undefined) {
        html = `<span class="animate__animated animate__fadeInDown w-full md:w-[300px] lg:w-[400px] bg-white font-inter text-gray-900 font-medium text-2xs tracking-wide text-center p-3 first-letter:capitalize shadow-md border">${message}</span>`
    }
    else if(type === 0) {
        html = `<span class="animate__animated animate__fadeInDown w-full md:w-[300px] lg:w-[400px] bg-red-100 font-inter text-red-900 font-medium text-2xs tracking-wide text-center p-3 first-letter:capitalize">${message}</span>`
    }
    else if(type === 1) {
        html = `<span class="animate__animated animate__fadeInDown w-full md:w-[300px] lg:w-[400px] bg-green-100 font-inter text-green-900 font-medium text-2xs tracking-wide text-center p-3 first-letter:capitalize">${message}</span>`
    }

    let container = document.createElement('div')
    container.id = 'toast'
    container.innerHTML = html;
    container.classList.add('flex', 'items-center', 'w-full', 'top-0', 'justify-center', 'left-0', 'z-50', 'absolute', 'font-mont', 'px-2', 'py-2', 'lg:p-0')
    document.body.appendChild(container)

    setTimeout(() => document.getElementById('toast')?.remove(), timeout)
}

function controlHasValue(form, selector) {
    return form.querySelector(selector).value.length < 1
}

function mapValidationErrors(errorElements, controls) {

    errorElements.forEach( item => {
        item.previousElementSibling.style.borderColor = '';
        item.remove()
    })

    if(controls.length) {
        controls.map( item => {
            let errorElement = document.createElement('span')
            errorElement.classList.add('control-error','dom-entrance')
            let control = item[0] , mssg = item[1]
            errorElement.textContent = mssg;
            control.parentElement.appendChild(errorElement)            
        })
        return false
    }

    return true
}

const loadScript = function (resource) {
    return new Promise(function (resolve, reject) {
        const script = document.createElement('script');
        script.src = resource.url;
        script.addEventListener('load', function () {
            intializePageJavascript()
            resolve(true);
        });
        document.body.appendChild(script);
    });
};

function getFormData(form) {
    let formdata = new FormData(form)
    return formdata
}

let paginationLimit = 20 
let filteredDataSource = []
let pageCount; 
let currentPage = 1; 
let prevRange; 
let currRange; 
let callback;
let footerCallback;
let datasource = []

function makePaginationMoveButton(type) {
    return `<button type="button" id="${type}-button" disabled>${type}</button>`
}

function initializePaginationMoveButtonsEventListeners() {

    const tableStatusWrap = document.querySelector('.table-status')
    
    tableStatusWrap.querySelector('#prev-button').addEventListener('click', () => {
        setCurrentPage(currentPage - 1); 
    })

    tableStatusWrap.querySelector('#next-button').addEventListener('click', () => {
        setCurrentPage(currentPage + 1); 
    })

    tableStatusWrap.querySelector('#pagination-limit').addEventListener('change', limitChange)

    document.querySelectorAll(".pagination-number").forEach((button) => {
        const pageIndex = Number(button.getAttribute("page-index"));         
        if (pageIndex)  button.addEventListener("click", () => {
            setCurrentPage(pageIndex); 
        });
    });

}

function limitChange() {
    el = document.querySelector('#pagination-limit')
    paginationLimit = +(el.options[el.selectedIndex].value)
    setCurrentPage(1)
}

const handleActivePageNumber = () => {
    document.querySelectorAll(".pagination-number").forEach((button) => {
        button.classList.remove("active");
        const pageIndex = Number(button.getAttribute("page-index"));
        if (pageIndex == currentPage)  button.classList.add("active")
    });
};

const handlePageButtonsStatus = () => {
    const tableStatusWrap = document.querySelector('.table-status')
    if (currentPage === 1)  disableButton(tableStatusWrap.querySelector('#prev-button'));
    else  enableButton(tableStatusWrap.querySelector('#prev-button'))
    if (pageCount === currentPage) disableButton(tableStatusWrap.querySelector('#next-button'))  
    else enableButton(tableStatusWrap.querySelector('#next-button')) 
 };

const disableButton = (button) => {
    button.classList.add("disabled");
    button.setAttribute("disabled", true);
};

const enableButton = (button) => {
    button.classList.remove("disabled");
    button.removeAttribute("disabled");
};

function getSignaledDatasource() {
    return JSON.parse(sessionStorage.getItem('datasource'))
}

function getSignaledPaginationStatus() {
    return sessionStorage.getItem('p-status')
}

function getSignaledPaginationNumbers() {
    return sessionStorage.getItem('p-numbers')
}

function setSignaledFooter(rows) {
    sessionStorage.setItem('table-footer', rows)
}

function getSignaledFooter() {
    return sessionStorage.getItem('table-footer')
}

async function getPaginationNumbers (){
    let str = ''
    pageCount = Math.ceil(datasource.length / paginationLimit);
    for (let i = 1; i <= pageCount; i++) {
      str += `<button class="pagination-number" type="button" aria-label="Page ${i}" page-index="${i}">${i}</button>`;
    }
    return str
};

function addPaginationStatus() {

    template = `
        ${getSignaledPaginationStatus()}
        <span class=" flex justify-between gap-6">
            <span>
                <select id="pagination-limit" class="form-control !bg-white cursor-pointer">
                    <option value="" selected="true">20</option>
                    <option value="20" >20</option>
                    <option value="30">30</option>
                    <option value="35">35</option>
                    <option value="40">40</option>
                    <option value="70">70</option>
                    <option value="100">100</option>
                    <option value="150">150</option>
                    <option value="200">200</option>
                    <option value="250">250</option>
                    <option value="500">500</option>
                    <option value="750">750</option>
                    <option value="1000">1000</option>
                    <option value="1500">1500</option>
                </select>
            </span>
            <span class="flex pagination">
                ${ makePaginationMoveButton('prev') }
                ${ getSignaledPaginationNumbers() }
                ${ makePaginationMoveButton('next') }
            </span>
        </span>
    `
    document.querySelector('.table-status').innerHTML = template
}

function resolvePagination(data, cb, footerCb=null) {
    datasource = data;
    footerCallback = footerCb
    setCurrentPage(1)
    callback = cb
 }
 
function setCurrentPage(pageNum) {
    document.querySelector('#tabledata').innerHTML = `
    <tr>
        <td colspan="100%" class="text-center opacity-70">
            <span class="loader mx-auto"></span>
        </td>
    </tr`
    
    currentPage = pageNum;
    prevRange = (pageNum - 1) * paginationLimit;
    currRange = pageNum * paginationLimit;
    filteredDataSource = [];

    if (datasource.length) {
        for (let i = 0; i < datasource.length; i++) {
            if (i >= prevRange && i < currRange) {
                filteredDataSource.push({ index: i, ...datasource[i] });
            }
        }
        
        sendStorageSignal(filteredDataSource);

        if (currentPage === pageCount) {
            if(!footerCallback) return
            
            const footerRow = footerCallback();
            setSignaledFooter(footerRow);
        }
    }
}

async function sendStorageSignal(filteredDataSource) {
    sessionStorage.setItem('datasource', JSON.stringify(filteredDataSource))
    let itemsAvailable = getSignaledDatasource().length;

    let status = `<span class="text-xs text-gray-500">Showing ${ prevRange + 1 }  to ${ itemsAvailable >= currRange * currRange ? (-prevRange + currRange ) + itemsAvailable : itemsAvailable + prevRange} of ${ datasource.length } Records </span> `

    let pageNumbers = await getPaginationNumbers()
    let pageNumberTemplate = `<span id="pagination-numbers"> ${pageNumbers} </span>`

    sessionStorage.setItem('p-status', status)
    sessionStorage.setItem('p-numbers', pageNumberTemplate)
    callback()
}

function injectPaginatatedTable(rows) {
    document.querySelector('#tabledata').innerHTML = rows
    document.querySelector('#tabledata').insertAdjacentHTML('beforeend', getSignaledFooter() ?? '');
    addPaginationStatus()
    handleActivePageNumber();
    handlePageButtonsStatus()
    initializePaginationMoveButtonsEventListeners();
    ['p-status', 'p-numbers', 'datasource', 'table-footer'].forEach(item => sessionStorage.removeItem(item))
}

async function logoff() {
    let request = await httpRequest('../controllers/logoff')
    location.reload();
    // location.href = './login.php'
}

function elementWithId(element){
    if(document.getElementById(element)) return document.getElementById(element)
}

function openModal(data={ template: '', title:'', close:true, cb: null }) {
    
  const modal = document.getElementById('modal');
  modal.classList.remove('hidden');
  modal.classList.add('fade-in');
  
  modal.querySelector('[name="content"]').innerHTML = data.template
  
  if(data.title) {
      modal.querySelector('h2').innerHTML = data.title
  }
  
  if(!data.close) {
      modal.querySelector('button[name="close"]').display="none"
  }

}

function closeModal() {
  const modal = document.getElementById('modal');
  modal.classList.remove('fade-in');
  modal.classList.add('fade-out');

  setTimeout(() => {
    modal.classList.add('hidden');
    modal.classList.remove('fade-out');
    modal.querySelector('h2').innerHTML = ''
    modal.querySelector('button[name="close"]').display="flex"
    modal.querySelector('[name="content"]').innerHTML = ''
  }, 300);
}


function previewWithSrc(id, srcName) {
    elementWithId(id).className = "h-[200px] w-[200px] bg-cover bg-center bg-no-repeat"
    elementWithId(id).style.backgroundImage = `url(../../images/kyc/${srcName})`
}

function getQueryParams(url) {
    let params = {};
    let parser = new URL(url);
    let queryString = parser.search.substring(1);
    let queries = queryString.split("&");

    queries.forEach((query) => {
        let [key, value] = query.split("=");
        params[key] = decodeURIComponent(value);
    });

    return params;
}

function redirect(path, returnData=false) {
    const url = window.location.origin + window.location.pathname + `?r=${ path }`
    if(returnData) return url
    
    location.assign(url)
}

function formatDate(dateString) {
  const date = new Date(dateString);
  
  const months = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
  ];
  
  const day = date.getDate();
  const month = months[date.getMonth()];
  const year = date.getFullYear();
  
  return `${month} ${day}, ${year}`;
}

function redirectWithProp(id, baseUrl) {
  const url = `./index?r=${baseUrl}&prop=${id}`;
  
  window.location.href = url;
}

function checkForProp(property="prop") {
    const urlParams = new URLSearchParams(window.location.search);
    
    const prop = urlParams.get(property);
    
    if (prop) {
        return prop; 
    } else {
        return null;
    }
}

function triggerEventsOnFields() {
  const inputs = document.querySelectorAll('input, select, textarea');

  inputs.forEach(field => {
    if (field.tagName === 'INPUT' || field.tagName === 'TEXTAREA') {
      const inputEvent = new Event('input', { bubbles: true });
      field.dispatchEvent(inputEvent);
    }

    if (field.tagName === 'SELECT' || field.tagName === 'INPUT' || field.tagName === 'TEXTAREA') {
      const changeEvent = new Event('change', { bubbles: true });
      field.dispatchEvent(changeEvent);
    }
  });
}



