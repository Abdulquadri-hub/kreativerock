async function contactActive(args) { 
    const contactformbtn = document.querySelector('[name="contactform"] button')
    if(contactformbtn) contactformbtn.addEventListener('click', submitMessage)
}

async function submitMessage() {
    if(!validateContactForm()) return 
    
    const payload = new FormData(document.querySelector('[name="contactform"]'))
    let request = await sendContactMessage(payload,  document.querySelector('[name="contactform"] button'))
    request = JSON.parse(request)
    if(!request.status) {
        return notification(request.message ?? 'Sorry! We are not able to send this message', 0)
    }
    
    notification('Thank You! We will respond ASAP', 1)
    document.getElementById('contactform').reset()
}

function validateContactForm() {
  var flag = 1;

  const name = document.querySelector('[name="name"]')
  const companyName = document.querySelector('[name="companyname"]')
  const email = document.querySelector('[name="email"]')
  const service = document.querySelector('[name="service"]')
  const url = document.querySelector('[name="url"]')
  const description = document.querySelector('[name="message"]')
  
  if (name.value.length < 1) {
    name.style.borderColor = 'red'
    flag = 0
  }

  if (email.value.length < 1) {
    email.style.borderColor = 'red'
    flag = 0
  }
  
  if (companyName.value.length < 1) {
    companyName.style.borderColor = 'red'
    flag = 0
  }
  
  if (service.value.length < 1) {
    service.style.borderColor = 'red'
    flag = 0
  }
  

  if (flag == 0) {
    notification('Please Fill contact form correctly', 0)
    return false;
    
  } else {
    name.style.borderColor = ''
    email.style.borderColor = ''
    companyName.style.borderColor = ''
    service.style.borderColor = ''
    return true;
  }
}