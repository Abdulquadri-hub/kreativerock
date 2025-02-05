let segmentid
async function segmentActive() {
    datasource = [];
    const form = document.getElementById('segmentform') 
    console.log(form)
    if(form.querySelector('#submit'))form.querySelector('#submit').addEventListener('click', submitSegment);
    alert('Segment is active')
    await fetchsegmentsHistory()
}

async function fetchsegmentsHistory(id) {
    if(!id)segmentid = '';

    // Show loading state using SweetAlert
    const loadingAlert = Swal.fire({
        title: 'Please wait...',
        text: 'Fetching segment data, please wait.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    let form = document.querySelector('#segmentform');
    let formData = new FormData(form);
    // // formData.set('department', '');
    // // formData.set('segment', '');
    // let queryParams = new URLSearchParams(formData).toString();

    if(!id)document.getElementById('tabledata').innerHTML = `<td colspan="100%" class="text-center opacity-70"> Loading... </td>`

    let payload = getFormData2(document.querySelector('#segmentform'), segmentid ? [['id', segmentid]] : []);

    let request = await httpRequest2(baseurl+'/sms/fetchsegments', payload, null, 'json', 'GET');

    swal.close(); // Close the loading alert once the request is complete
    if(!id)document.getElementById('tabledata').innerHTML = `<td colspan="100%" class="text-center opacity-70"> No Records Retrieved </td>`
    if(request.status) {
        if(!id){
            if(request.data.length) {
                datasource = request.data
                resolvePagination(datasource, onsegmentHistoryTableDataSignal, addFFooterTableDataSignal);
            }
        } else {
            // document.getElementsByClassName('updater')[0].click();
            segmentid = request.data[0].id;
            populateData(request.data[0]);
        }
    } else {
        return notification('No records retrieved');
    }
}

// async function (id) {
//     if(request.status) {
//         datasource = request.data
//         datasource.length && resolvePagination(datasource, onsegmentHistoryTableDataSignal, addFFooterTableDataSignal)

//     } else return notification('No records retrieved', 0)
// }

async function onsegmentHistoryTableDataSignal() {
    let rows = getSignaledDatasource().map((item, index) => {
        return `
            <tr>
                <td>${ item.index + 1 }</td>
                <td>${ item.name }</td>
                 <td class="flex items-center gap-3 justify-center whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                    <button title="Edit row entry" class="material-symbols-outlined rounded-full bg-blue-600 h-8 w-8 text-white drop-shadow-md text-xs" style="font-size: 18px;" onclick="fetchSegment('${item.id}')">edit</button>
                    <button title="Delete row entry" class="material-symbols-outlined rounded-full bg-red-600 h-8 w-8 text-white drop-shadow-md text-xs" style="font-size: 18px;">delete</button>
                </td>
            </tr>
        `
    }) .join('')
    injectPaginatatedTable(rows)
}


function addFFooterTableDataSignal() {
    let totalQtyIn = datasource.reduce((acc, item) => acc + (+item.qtyin), 0);
    let totalQtyOut = datasource.reduce((acc, item) => acc + (+item.qtyout), 0);
    let totalAmount = datasource.reduce((acc, item) => acc + (+item.amount), 0);

    let footerRow = `
        <tr colspan="1">
            <td colspan="1" class="!uppercase !text-sm font-bold">Total</td>
            <td class="!font-bold">${totalQtyIn}</td>
            <td class="!font-bold">${totalQtyOut}</td>
            <td class="!font-bold">${formatCurrency(totalAmount)}</td>
            <td colspan="3"></td>
        </tr>
    `;

    return footerRow;
}

function submitSegment() {
    const segment = document.querySelector('#segment').value
    if(!segment) return notification('Segment is required', 0)
    
   const param = new FormData(document.getElementById('segmentform'))
    
    httpRequest2(baseurl+'/sms/segment', param, document.querySelector('#submit'), 'json')
        .then(response => {
            if(response.status) {
                notification('Segment added successfully', 1)
                did('segment').value = '';
            } else {
                notification('Segment could not be added', 0)
            }
        })
        .catch(error => {
            notification('Segment could not be added', 0)
        })
}

