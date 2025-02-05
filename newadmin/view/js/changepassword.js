let changepasswordid
async function changepasswordActive() {
    const form = document.querySelector('#changepasswordform')
    if(form.querySelector('#submit')) form.querySelector('#submit').addEventListener('click', changepasswordFormSubmitHandler)
}

async function changepasswordFormSubmitHandler() {
    if(!validateForm('changepasswordform', [`newupw`, `newupw2`, `oldupw`])) return
    
    if(elementWithId('newupw').value !== elementWithId('newupw2').value) return notification('New passwords dont match', 0)
    
    let payload

    payload = getFormData2(document.querySelector('#changepasswordform'), changepasswordid ? [['id', changepasswordid]] : null)
    let request = await httpRequest2('../controllers/changepassword', payload, document.querySelector('#changepasswordform #submit'))
    if(request.status) {
        notification('Record saved successfully!', 1);
        document.querySelector('#changepasswordform').reset();
        return
    }
    document.querySelector('#changepasswordform').reset();
    return notification(request.message, 0);
}
