let accesscontrolid;

const accessctrl_user = ["PROFILE", "CHANGE PASSWORD", "SELECT USER", "DEACTIVATE USER"];

const access_array = [
    ['accessctrl_user', 'USER', accessctrl_user],
];

async function accesscontrolActive() {
    const form = document.querySelector('#accesscontrolsform');

    if (form.querySelector('#submit')) {
        form.querySelector('#submit').addEventListener('click', accesscontrolFormSubmitHandler);
    }
    if (document.getElementById('accesssave')) {
        document.getElementById('accesssave').addEventListener('click', submitaccesssettings);
    }

    let request = await httpRequest('../controllers/fetchusers', null, null, 'json');
    if (request.status) {
        if (request.data.length) {
            document.querySelector('[list="userslist"]').innerHTML = request.data.map(dat => `<option ="${dat.firstname} ${dat.lastname} || ${dat.email}">${dat.firstname} ${dat.lastname} || ${dat.email}</option>`).join(' ');
        }
    } else {
        return notification('No records retrieved');
    }
    datasource = [];
}

async function submitaccesssettings() {
    if (!validateForm('accesscontrolsform', ['email'])) return;

    function payload() {
        let param = new FormData();
        param.append('email', document.getElementById('email').value.split('||')[1].trim());
        param.append('role', document.getElementById('role').value);

        let accessstring = '';
        Array.from(document.getElementsByClassName('accesscontroller')).forEach(controller => {
            if (controller.checked) {
                accessstring += `${controller.name}||`;
            }
        });

        param.append('permissions', accessstring);
        return param;
    }

    let request = await httpRequest2('../controllers/updatepermissions', payload(), document.querySelector('#accesscontrolsform #accesssave'));
    if (request.status) {
        notification('Record saved successfully!', 1);
        elementWithId('email').value = '';
        elementWithId('accesssave').classList.add('hidden');
        elementWithId('accessctrl_container').innerHTML = '';
        // fetchaccesscontrols();
        return;
    }

    document.querySelector('#accesscontrolsform').reset();
    // fetchaccesscontrols();
    return notification(request.message, 0);
}

function accessboard(element) {
    if (!element.value) {
        elementWithId('accesssave').classList.add('hidden');
        elementWithId('accessctrl_container').innerHTML = '';
    }
}

function accessappendboard(res) {
    access_array.forEach(access => {
        let element = document.createElement('div');
        element.setAttribute('id', access[0]);
        element.classList.add('flex', 'flex-col', 'border-r', 'mr-3', 'pr-3', 'border-b', 'mb-3', 'pb-3', 'W-[200px]');
        elementWithId('accessctrl_container').appendChild(element);

        document.getElementById(access[0]).innerHTML = `
            <p class="page-title">
                <span>${access[1]}</span>
            </p>
            ${access[2].map(data => `
                <label class="bg-[#1d68e305] p-2 pl-1 mb-[1px] relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="${data}" ${res.permissions.split('||').includes(data) ? 'checked' : ''} class="sr-only peer accesscontroller">
                    <div class="scale-[0.8] w-11 h-6 bg-gray-400 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    <span class="ms-2 text-xs font-medium text-blue-900">${data}</span>
                </label>
            `).join('')}
        `;
    });
}

function populateaccesscontrolboard(result) {
    accessappendboard(result);
    elementWithId('role').value = result.role;
    elementWithId('accesssave').classList.remove('hidden');
}

async function fetchaccesscontrols(id) {
    function getparamm() {
        let paramstr = new FormData();
        paramstr.append('id', id);
        return paramstr;
    }

    let request = await httpRequest2('../controllers/fetchaccesscontrols', id ? getparamm() : null, null, 'json');

    if (!id) {
        document.getElementById('tabledata').innerHTML = `No records retrieved`;
    }

    if (request.status) {
        if (!id) {
            if (request.data.length) {
                datasource = request.data;
                resolvePagination(datasource, onaccesscontrolTableDataSignal);
            }
        } else {
            accesscontrolid = request.data[0].id;
            populateData(request.data[0]);
        }
    } else {
        return notification('No records retrieved');
    }
}

async function removeaccesscontrol(id) {
    const confirmed = window.confirm("Are you sure you want to remove this accesscontrol?");
    if (!confirmed) return;

    function getparamm() {
        let paramstr = new FormData();
        paramstr.append('id', id);
        return paramstr;
    }

    let request = await httpRequest2('../controllers/removevisacountries', id ? getparamm() : null, null, 'json');
    fetchaccesscontrols();
    return notification(request.message);
}

async function onaccesscontrolTableDataSignal() {
    let rows = getSignaledDatasource().map((item, index) => `
        <tr>
            <td>${item.index + 1}</td>
            <td>${item.productname}</td>
            <td>${item.productdescription}</td>
            <td class="flex items-center gap-3">
                <button title="Edit row entry" onclick="fetchaccesscontrols('${item.id}')" class="material-symbols-outlined rounded-full bg-primary-g h-8 w-8 text-white drop-shadow-md text-xs" style="font-size: 18px;">edit</button>
                <button title="Delete row entry" onclick="removeaccesscontrol('${item.id}')" class="material-symbols-outlined rounded-full bg-red-600 h-8 w-8 text-white drop-shadow-md text-xs" style="font-size: 18px;">delete</button>
            </td>
        </tr>
    `).join('');
    injectPaginatatedTable(rows);
}

async function accesscontrolFormSubmitHandler() {
    if (!validateForm('accesscontrolsform', ['email'])) return;

    function payload() {
        let params = new FormData();
        params.append('email', document.getElementById('email').value.split('||')[1].trim());
        return params;
    }

    let request = await httpRequest2('../controllers/fetchuserprofile', payload(), document.querySelector('#accesscontrolsform #submit'), 'json');
    if (request.status) {
        populateaccesscontrolboard(request);
        return;
    }

    document.querySelector('#accesscontrolform').reset();
    return notification(request.message, 0);
}
