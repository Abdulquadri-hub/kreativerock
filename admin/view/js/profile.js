let profileid;
async function profileActive() {
    
    const countries = oreCountries;
    const options = countries.map( item => `<option value="${ item }"> ${ item } </option>`).join('')
    if(document.getElementById('country')) {
        document.getElementById('country').innerHTML = options
    }
    
    const industriesList = industries;
    const industriesOptions = industries.map( item => `<option value="${ item }"> ${ item } </option>`).join('')
    if(document.getElementById('industry')) {
        document.getElementById('industry').innerHTML = industriesOptions
    }
    
    const positions = companyPositions;
    const positionsOptions = companyPositions.map( item => `<option value="${ item }">`).join('')
    if(document.getElementById('positioninthecompany')) {
        const datalist = document.createElement('datalist')
        datalist.id = 'positions'
        datalist.innerHTML = positionsOptions
        document.body.appendChild(datalist)
    }
    
    if (document.getElementById('user_role').value == 'SUPERADMIN') {
        elementWithId('MERCHANT').classList.remove('hidden')
        elementWithId('SUPERADMIN').classList.remove('hidden')
    } else {
        elementWithId('MERCHANT').classList.add('hidden')
        elementWithId('SUPERADMIN').classList.add('hidden')
    }
    
    
    const form = document.querySelector('#profilesform')
    if (form.querySelector('#submit')) form.querySelector('#submit').addEventListener('click', profileFormSubmitHandler)
    
    if (sessionStorage.getItem('edituser')) {
        let data = sessionStorage.getItem('edituser')
        sessionStorage.removeItem('edituser')
        await fetchprofiles(data)
    } else await fetchprofiles()

}
    
async function profileFormSubmitHandler() {
    if (!validateForm('profilesform', [`email`])) return

    let payload

    payload = getFormData2(
        document.querySelector('#profilesform'), 
        [ 
            ['photofilename', showFileName('imageurl1')], ['userphotoname', getFile('imageurl1')], 
        ]
    )
    
    let request
    if (!profileid) request = await httpRequest2('../controllers/userscript', payload, document.querySelector('#profilesform #submit'))
    if (profileid) request = await httpRequest2('../controllers/updateuser', payload, document.querySelector('#profilesform #submit'))
    
    if (request.status) {
        notification('Record saved successfully!', 1);
        document.querySelector('#profilesform').reset();
        fetchprofiles();
        return
    } else {
        document.querySelector('#profilesform').reset();
        fetchprofiles();
        return notification(request.message, 0);
    }
}


async function fetchprofiles(id = "") {
    profileid = id
    // scrollToTop('scrolldiv')
    function getparamm() {
        let paramstr = new FormData()
        paramstr.append('email', id)
        return paramstr
    }
    let request = await httpRequest2('../controllers/fetchuserprofile', id ? getparamm() : null, null, 'json')
    if (request.status) {
        if (request) {
            populateData(request)
            previewWithSrc('imagePreview',request.imageurl)
            
        }
    } else return notification('No records retrieved')
}

