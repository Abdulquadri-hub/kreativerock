async function confirmationActive() {
    const currentUrl = window.location.href;
    let { response, service, module } = (getQueryParams(currentUrl) ?? {});
    response = JSON.parse(response)
    
   appendConfirmationDetails(response, { service, module })
}


function appendConfirmationDetails(paymentResponse, paymentInfo) {
    
    const flutterwaveRef = (paymentResponse.status == 'successful' && paymentResponse.tx_ref)
    const details = {}
    
    if(flutterwaveRef) {
        details.reference = paymentResponse.tx_ref;
        details.transactionDate = new Date(paymentResponse.created_at).toLocaleString()
        details.amount = formatCurrency(paymentResponse.amount)
    }
    
    if(!details) return 
    
    if(true || paymentInfo.module == 'sms') {
        details.message = 'Your sms unit payment is complete'
        details.service = paymentInfo.service ?? 'SMS'
        details.paymentHistoryURL = redirect('sms/unit', true)
    }
    
    const invoiceDetails = `
      <h2 class="text-2xl font-semibold">Payment Complete</h2>
      <p class="text-sm text-gray-500 mt-1">${ details.message }</p>
      <div class="text-sm border-t py-5 mt-3 flex flex-col gap-1">
          <p class="flex justify-between">
              <span>Service</span> 
              <span class="capitalize">${ details.service }</span>
          </p>
          <p class="text-sm flex justify-between">
              <span>T. Reference</span> 
              <span>${ details.reference }</span>
           </p>
          <p class="text-sm mt-1 flex justify-between">
              <span>T. Date</span> 
              <span>${ details.transactionDate }</span>
           </p>
          <p class="text-sm mt-1 flex justify-between">
              <span>Total paid</span> 
              <span class="font-bold text-lg">${ details.amount }</span>
           </p>
       </div>
    `
    
    const contentArea = document.querySelector('[name="content"]')
    if(contentArea) {
        contentArea.innerHTML = invoiceDetails
        contentArea.nextElementSibling.firstElementChild.href = details.paymentHistoryURL
    }
}