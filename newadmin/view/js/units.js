let smspackages;

async function unitsActive() {
    try {
        await fetchSmsPageData();

        const packagesSelect = document.getElementById('packageid');
        if (packagesSelect) {
            packagesSelect.addEventListener('change', handlePackageChange);
        }

        const qtyInput = document.getElementById('qty');
        if (qtyInput) {
            qtyInput.addEventListener('change', handleQuantityChange);
            qtyInput.addEventListener('input', handleQuantityChange);
        }

        const proceedBtn = document.querySelector('button#submit')
        proceedBtn.addEventListener('click', ProceedToBulkSummary)
        
    } catch (error) {}
} 

function ProceedToBulkSummary() {
    const packagesSelect = document.getElementById('packageid');
    const qtyInput = document.getElementById('qty');
    
    if(!(packagesSelect.value && qtyInput.value)) {
        if(!validateForm('smsform', ['packageid', 'qty'])) return 
    }
    
    const selectedPackage = smspackages.find(item => item.id === packagesSelect.value);
    const template = `
    <div class="border-b pb-3 flex flex-col gap-3 capitalize">
        <div class="flex justify-between items-start">
            <span class="font-medium">Service</span>
            <span>SMS</span>
        </div>
        <div class="flex justify-between items-start">
            <span class="font-medium">Package</span>
            <span>${ selectedPackage.packagename }</span>
        </div>
        <div class="flex justify-between items-start">
            <span class="font-medium">Quanity</span>
            <span>${ qtyInput.value }</span>
        </div>
        <div class="flex justify-between items-start">
            <span class="font-medium">Unit Cost</span>
            <span>${ selectedPackage.costperunit }</span>
        </div>
        <div class="flex justify-between items-start">
            <span class="font-medium">Transaction Date</span>
            <span> ${ new Date().toLocaleString() } </span>
        </div>
    </div>
    <div class="py-3">
         <div class="flex justify-between items-start">
            <span class="font-medium text-lg">Total</span>
            <span class="font-black text-xl">${ formatCurrency( (+qtyInput.value) * selectedPackage.costperunit ) }</span>
        </div>
    </div>
    <div class="pt-4"><button name="x" class="btn w-full bg-primary text-white !p-3">Continue to pay</button></div>
    `
    document.querySelector('#modal .body').innerHTML = template
    document.querySelector('#modal').children[0].click()
    
    
    const url = `/payment?module=${ encodeURIComponent('sms') }&service=${ encodeURIComponent('bulk sms') }&packagename=${ encodeURIComponent(selectedPackage.packagename) }&qty=${ qtyInput.value }`
    
    document.querySelector('[name="x"]').addEventListener('click', () => {
        document.querySelector('#modal').children[0].click()
        setTimeout(() => router.navigate(url), 1500)
    })
}

function handlePackageChange(event) {
    const selectedId = event.target.value;
    const selectedPackage = smspackages.find(item => item.id === selectedId);

    if (selectedPackage) {
        const unitPriceInput = document.getElementById('costperunit');
        const totalInput = document.getElementById('total');
        unitPriceInput.value = formatCurrency(selectedPackage.costperunit);
        totalInput.value = formatCurrency(selectedPackage.costperunit * document.getElementById('qty').value);
    }
}

function handleQuantityChange(event) {
    const selectedId = document.getElementById('packageid').value;
    if (!selectedId) return;

    const selectedPackage = smspackages.find(item => item.id === selectedId);

    if (selectedPackage) {
        
        if (!Number.isInteger(event.target.value)) {
            event.target.value = parseInt(event.target.value, 10) || 1;
        }

        const amount = parseFloat(selectedPackage.costperunit) * parseInt(event.target.value, 10) || 1;
        if (amount < 1) {
            event.target.value = 1;
        }

        const totalInput = document.getElementById('total');
        total.value = formatCurrency(amount);
    }
}

async function fetchSmsPageData() {
    let request = await httpRequest2('../controllers/sms/fetchpackages', null, null, 'json')

    if (request.data.length) {
        smspackages = request.data;
        const options = request.data.map(item => `<option value="${ item.id }"> ${ item.packagename } </option> `)
        document.getElementById('packageid').innerHTML += options
    } else return notification('No packages record retrieved')
}