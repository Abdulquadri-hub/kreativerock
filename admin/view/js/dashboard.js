async function dashboardActive() {
    const userInfo = JSON.parse(sessionStorage.getItem('user'))
    document.querySelector('h1 p span').innerHTML = `${userInfo.firstname}&nbsp;${userInfo.lastname}`
    
    loadDashboardAnalytics()
}


async function loadDashboardAnalytics() {

    let request = await httpRequest(`../controllers/sms/fetchdashboardstats`)
    request = JSON.parse(request)
    
    if(!request.status) {
        return
    }
    
    try {
        const data = request.data
        document.querySelector('[name="smsunits"]').innerHTML = data.unit_balance
        document.querySelector('[name="smsunitsspend"]').innerHTML = data.total_unit_spent
        document.querySelector('[name="onlineusers"]').innerHTML = data.total_online_users
        document.querySelector('[name="registeredusers"]').innerHTML = data.total_registered_users
        document.querySelector('[name="smsaccounts"]').innerHTML = data.total_sms_accounts
        document.querySelector('[name="whatsappaccounts"]').innerHTML = data.total_whatsapp_accounts
    } catch(e) {}
}