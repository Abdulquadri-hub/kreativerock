let numbersValidation;

async function smsCampaignActive() {
    document.getElementById('smsMessage').addEventListener('input', onMessageInput);
    document.getElementById('phoneNumbers').addEventListener('input', onPhoneNumbersInput);
    document.getElementById('submit').addEventListener('click', campaignSubmitHandler);
    document.getElementById('draft').addEventListener('click', campaignSubmitHandler);
    document.getElementById('importButton').addEventListener('click', onImportButtonClick);
    document.getElementById('file').addEventListener('change', onFileInputChange);

    const campaignOptions = document.querySelectorAll('.campaign-option');
    const defaultOption = document.querySelector('[data-campaign="promotional"]');
    
    campaignOptions.forEach(option => {
        option.addEventListener('click', onCampaignOptionClick);
    });

    if (defaultOption) {
        selectCampaign(defaultOption);
    }
    
    const promotionOptions = document.querySelectorAll('.promotion-option');
    const defaultPromotionOption = document.querySelector('[data-promotion="manual"]');

    promotionOptions.forEach(option => {
        option.addEventListener('click', onPromotionOptionClick);
    });
    
    if (defaultOption) {
        selectPromotion(defaultPromotionOption);
    }
    
    createPromptRow()
    
    if(checkForProp()) {
        fetchCampaign()
    }
}

async function fetchCampaign() {
    const payload = new FormData()
    payload.append('campaign_id', checkForProp())
    let request = await httpRequest('../controllers/sms/fetchcampaigns', payload, null, 'json')
    
    if(!request.status) return notification('No record retrieved')
    
    try {
        const record = request.data[0]
        document.querySelector('[name="campaignname"]').value = record.name
        document.querySelector(`[data-campaign=${record.type}]`)?.click()
        
        if(record.response_handling == 'automated') {
            document.querySelector(`[data-promotion=${record.response_handling}]`)?.click()
        }
        
        const contacts = JSON.parse(record.phone_numbers)
        document.getElementById('phoneNumbers').value = contacts.toString()
        
        if(record?.prompts?.length) {
            document.getElementById('prompts').innerHTML = '';
            record.prompts.forEach( item => {
                createPromptRow(item)
            })
        }
        
        document.getElementById('smsMessage').value = record.message  
        document.getElementById('repeatInterval').value = record.repeat_interval
        
        document.getElementById('submit').parentElement.innerHTML = `
            <button onclick="campaignSubmitHandler(event)" data-action="launch" title="Save changes to draft" type="button" class="btn !bg-none bg-primary-g">
                <div class="btnloader" style="display: none;"></div>
                <span>Save Changes</span>
            </button>
        `
        
        triggerEventsOnFields()
        
        
    } catch(e) { console.log(e) }
    
}


function onMessageInput(e) {
    const message = sanitizeMessage(e.target.value);
    highlightOffensiveWords(message);
}

function onPhoneNumbersInput() {
    const phoneInput = this.value.trim();
    const { validNumbers, invalidNumbers, hasSpaces } = validatePhoneNumbers(phoneInput);

    const feedbackElement = document.getElementById('phoneFeedback');
    
    if (!hasSpaces) {
        if (invalidNumbers.length > 0) {
            feedbackElement.classList.remove('hidden');
            feedbackElement.innerHTML = `<span><span class="text-red-500">Invalid Numbers: </span> ${invalidNumbers.length}</span>`;
            return
        } else {
            feedbackElement.innerHTML = ``;
            feedbackElement.classList.add('hidden');
        }

        if (validNumbers.length > 0) {
            feedbackElement.classList.remove('hidden');
            feedbackElement.innerHTML = `<span><span class="text-green-500">Valid Numbers:</span> ${validNumbers.length}</span>`;
        }
    }
}

function onCampaignOptionClick() {
    selectCampaign(this);
}

function onPromotionOptionClick() {
    selectPromotion(this);
}

function onImportButtonClick() {
    const fileInput = document.getElementById('file');
    fileInput.click();
}

function onFileInputChange(event) {
    const file = event.target.files[0];

    if (file && file.type === 'text/csv') {
        parseCSV(file);
    } else {
        notification('Please select a valid CSV file.', 0);
    }
}

function selectCampaign(option) {
    const campaignOptions = document.querySelectorAll('.campaign-option');
    const responseHandlingSection = document.getElementById('responseHandling');
    const responsePromptsSection = document.getElementById('responsePrompts');
    const defaultPromotionOption = document.querySelector('[data-promotion="manual"]');

    campaignOptions.forEach(opt => {
        opt.classList.remove('!border-green-600')
        opt.classList.remove('selected')
        
    });
    
    option.classList.add('!border-green-600');
    option.classList.add('selected')

    if (option.dataset.campaign === 'promotional') {
        responseHandlingSection.classList.remove('hidden');
    }else {
        responseHandlingSection.classList.add('hidden');
        selectPromotion(defaultPromotionOption)
    }
    
    if(option.dataset.campaign === 'keyword') {
        responsePromptsSection.classList.remove('hidden');
    } else {
        responsePromptsSection.classList.add('hidden');
    }
}

function selectPromotion(option) {
    const promotionOptions = document.querySelectorAll('.promotion-option');
    const responsePromptsSection = document.getElementById('responsePrompts');

    promotionOptions.forEach(opt => {
        opt.classList.remove('!border-green-600')
        opt.classList.remove('selected')
    });
    
    option.classList.add('!border-green-600');
    option.classList.add('selected')

    if (option.dataset.promotion !== 'manual') {
        responsePromptsSection.classList.remove('hidden');
    } else {
        responsePromptsSection.classList.add('hidden');
    }
}

function createPromptRow(data = null) {
    const newRow = document.createElement('div');
    newRow.classList.add('grid', 'grid-cols-1', 'md:grid-cols-2', 'lg:grid-cols-5', 'gap-2', 'items-end', 'prompt-row');

    newRow.innerHTML = `
        <div class="form-group">
            <label class="control-label">Prompt</label>
            <input value="${ data?.prompt ?? ''}" type="text" class="form-control">
        </div>
        <div class="form-group">
            <label class="control-label">Expected Response type</label>
            <select value="${ data?.expected_response_type ?? ''}" class="form-control">
                <option value=""> -- PICK TYPE -- </option>
                <option value="TEXT">TEXT E.g Hello, Start</option>
                <option value="EMAIL">EMAIL</option>
                <option value="AGE">AGE</option>
                <option value="PHONE">PHONE</option>
                <option value="OTHERS">OTHERS</option>
            </select>
        </div>
        <div class="form-group">
            <label class="control-label">Expected Response</label>
            <input value="${ data?.expected_response ?? ''}" type="text" class="form-control">
        </div>
        <div class="form-group">
            <label class="control-label">Response</label>
            <input value="${ data?.response_message ?? ''}" type="text" class="form-control">
        </div>
        <div class="flex gap-2 lg:justify-end">
            <button title="Add new prompt" type="button" class="add-prompt-btn rounded-full h-8 w-8 flex items-center justify-center text-green-700 border border-green-500">
                <span class="material-symbols-outlined">add</span>
            </button>
            <button title="Remove prompt" type="button" class="remove-prompt-btn rounded-full h-8 w-8 flex items-center justify-center text-red-700 border border-red-500">
                <span class="material-symbols-outlined">delete</span>
            </button>
        </div>
    `;

    const promptsContainer = document.getElementById('prompts');
    promptsContainer.appendChild(newRow);
    attachEventListeners(newRow);
}

function attachEventListeners(row) {
    const promptsContainer = document.getElementById('prompts');
    const addButton = row.querySelector('.add-prompt-btn');
    const removeButton = row.querySelector('.remove-prompt-btn');

    addButton.addEventListener('click', function() {
        createPromptRow();
    });

    removeButton.addEventListener('click', function() {
        if (promptsContainer.children.length > 1) {
            row.remove();
        } else {
            notification("At least one prompt is required.", 0);
        }
    });
}

function parseCSV(file) {
    const reader = new FileReader();

    reader.onload = function(event) {
        const csvContent = event.target.result;

        const rows = csvContent.split('\n');
        const contacts = csvContent.split(',').map(phone => phone.trim()).join(', ');

        const phoneNumbersElement = document.getElementById('phoneNumbers');
        phoneNumbersElement.value = contacts;
        notification('Contacts Imported successfully!', 1)
        
    };

    reader.readAsText(file);
}

function sanitizeMessage(message) {
    return message.replace(/@/g, 'a').replace(/!/g, 'i');
}

const dictionary = ['fool','mumu','abuse','bastard','crap','damn','douche','idiot','jerk','moron','nasty','prick','scum','shit','stupid','suck','trash','ugly','whore','spam',"scam","email","website"];

function validatePhoneNumbers(phoneInput) {
    const invalidNumbers = [];
    const validNumbers = [];
    let hasSpaces = false

    const phoneNumbers = phoneInput.split(/[\n,]+|\s+/).filter(Boolean);
    const phoneRegex = /^\+?\d{10,15}$/; 

    phoneNumbers.forEach((phone) => {
        if (phoneRegex.test(phone)) {
            if (!validNumbers.includes(phone)) {
                validNumbers.push(phone);
            }
        } else {
            invalidNumbers.push(phone); 
        }
        hasSpaces = false
    });
    
    const phoneNumbersItem = phoneInput.split(',')
    phoneNumbersItem.forEach((phone) => {
        if (phone.includes('  ')) {
          const invalidNumbers = [];
          hasSpaces = true
          const feedbackElement = document.getElementById('phoneFeedback');
          feedbackElement.classList.remove('hidden');
          feedbackElement.innerHTML = `<span class="text-red-500">Phone numbers must be separated by comma (,).</span>`;
          return; 
        }
    })
    
    numbersValidation = invalidNumbers
    return { validNumbers, invalidNumbers, hasSpaces};
}

function highlightOffensiveWords(message) {
    const messageLower = message.toLowerCase();
    
    const offensiveRegex = new RegExp(`\\b(${dictionary.join('|')})\\b`, 'gi');
    
    const matchedWords = messageLower.match(offensiveRegex);

    if (matchedWords) {
        document.getElementById('offensiveWordsCheck').classList.remove('hidden');
        document.getElementById('offensiveWordsCheck').innerText = `Words not allowed: ${matchedWords.join(', ')}`;
        
        notification('Words like  is not accepted!', 0)
        return true; 
    } else {
        document.getElementById('offensiveWordsCheck').classList.add('hidden');
        const message = event.target.value;
        const charCount = message.length;
        const smsPages = Math.ceil(charCount / 160);
        document.getElementById('smsCounter').innerText = `Characters: ${charCount} | SMS Pages: ${smsPages}`;
        
        return false;
    }
}

async function campaignSubmitHandler(event) {
    const btn = event.currentTarget
    if (!validateForm('smscampaignform', ['smsMessage', 'phoneNumbers', 'campaignname'])) {
        return;
    }

    if (numbersValidation?.length) {
        return notification('Phone numbers are invalid', 0);
    }
    
    formatPhoneNumbers()

    const payload = getCampaignFormData()
    payload.append('submitaction', btn.dataset.action);
    btn.disabled = true
    
    let request = await httpRequest2(`../controllers/sms/${ checkForProp() ? 'editcampaign' : 'smscampaign' } `, payload, btn, 'json');

    if (!request.status) {
        return notification(request.message ?? 'Sorry! Unable to complete request', 0);
        btn.disabled = false
    }
        
    notification(request.message ?? 'Campaigned successfully initiated!', 1);
    // document.getElementById('smscampaignform').reset();
    btn.disabled = false
    
    if(!checkForProp()) {
        location.href = './index?r=sms/campaign'
    }
}

function getCampaignFormData() {
    let phonenumbers = new Set(document.getElementById('phoneNumbers').value.split(',').map(item => item.trim()));
    phonenumbers = Array.from(phonenumbers).join(',');

    const campaignTypeElement = document.querySelector('.campaign-option.selected');
    const campaignType = campaignTypeElement?.dataset.campaign ?? '';

    const promotionalCampaignResponseElement = document.querySelector('.promotion-option.selected');
    const promotionalCampaignResponse = promotionalCampaignResponseElement?.dataset.promotion ?? '';

    const promptsElements = Array.from(document.getElementById('prompts').children);
    let prompts = [];
    let hasInvalidPrompt = false;

    promptsElements.forEach(item => {
        const inputs = item.querySelectorAll('input');
        const prompt = inputs[0];
        const expectedResponse = inputs[1];
        const response = inputs[2];
        const expectedResponseType = item.querySelector('select');

        prompts.push({ prompt: prompt?.value, expectedResponse: expectedResponse?.value, response: response?.value, expectedResponseType: expectedResponseType?.value });

        if (promotionalCampaignResponse === 'automated') {
            const elements = [prompt, expectedResponseType, expectedResponse, response].filter(Boolean);
            elements.forEach(el => {
                if (!el.value.trim()) {
                    el.style.borderColor = 'red';
                    hasInvalidPrompt = true;
                } else {
                    el.style.borderColor = '';
                }
            });
        }
    });

    if (hasInvalidPrompt) {
        return;
    }

    const payload = new FormData(document.getElementById('smscampaignform'));
    
    payload.append('campaigntype', campaignType);
    payload.append('responsehandling', promotionalCampaignResponse);
    payload.append('contacts', phonenumbers);
    payload.append('smspages', calculateSmsPages(document.getElementById('smsMessage').value));
    payload.append('promptsrows', prompts.length);
    
    for(let i = 0; i < prompts.length; i++) {
        payload.append(`prompt${i}`, prompts[i].prompt);
        payload.append(`expectedresponse${i}`, prompts[i].expectedResponse);
        payload.append(`response${i}`, prompts[i].response);
        payload.append(`expectedResponsetype${i}`, prompts[i].expectedResponseType);
    }
    
    if(checkForProp()) {
        payload.append('campaign_id', checkForProp())
    }
    
    return payload
}


function calculateSmsPages(text) {
    const maxCharsPerSms = 160; 
    const totalChars = text.length;
    return Math.ceil(totalChars / maxCharsPerSms);
}

function formatPhoneNumbers(textareaId) {
  const textarea = document.getElementById('phoneNumbers');
  const phoneNumbers = textarea.value.split(',');

  const formattedNumbers = phoneNumbers.map(number => {
    number = number.trim();

    if (number.startsWith('+234')) {
      return number;
    }

    if (number.startsWith('0')) {
      return '+234' + number.slice(1);
    }

    return number;
  });

  textarea.value = formattedNumbers.join(', ');
}