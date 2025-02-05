window.onload = function() {
    let form = document.getElementById('loginform')
    if(form) {
         if(form.querySelector('button#submit')) form.querySelector('button#submit').addEventListener('click', submitHander, false)
         if(form.querySelector('#password')) form.querySelector('#password').addEventListener('keypress', e => enterEvent(e))
    }
    const passwordInput = document.getElementById('password');
    const visibilityToggle = document.getElementById('pvisible');

    visibilityToggle.addEventListener('click', function() {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            // visibilityToggle.textContent = 'visibility'; // Change eye icon to hide icon
        } else {
            passwordInput.type = 'password';
            // visibilityToggle.textContent = 'visibility_off'; // Change hide icon to eye icon
        }
    });
 }
 
 function enterEvent(event) {
    if(event.key.includes('Enter')) submitHander()
 }

 function redirectToAdmin(result) {
    const { firstname, lastname, othernames } = result.data
    const authInfo = { firstname, lastname, othernames, timestamp: new Date().getTime() }
    
    sessionStorage.setItem('sessiontoken', JSON.stringify(result.data.sessiontoken))
    sessionStorage.setItem('user', JSON.stringify(result.data));
    
    window.location.href = "./index";
 }
 
 async function submitHander() {
     if(!runLoginValidations()) return
 
     let result = await httpRequest('../controllers/loginscript', getLoginFormParams(), document.querySelector('button#submit'))
     if(result.status) {
         notification('Login Successful', 1)
        //  resolveReferrerIntent(result);
        redirectToAdmin(result)
     }
     else { 
         if(result.code == 204) return notification('Incorrect email or password', 0)
         else if(result.code == 300) return notification('Account is unverified', 0)
         else return notification('Unable to sign in. please try again', 0)
     }
 
 }
 
 function getLoginFormParams() {
     let paramstr = new FormData(document.getElementById('loginform'))
     paramstr.append('upw', document.getElementById('loginform').password.value)
     return paramstr
 }
 
 
 function runLoginValidations() {
     let form = document.getElementById('loginform')
     let errorElements = form.querySelectorAll('.control-error')
     let controls = []
 
     if(form.querySelector('#email').value.length < 1)  controls.push([form.querySelector('#email'), 'Email is required'])
     else if(!form.querySelector('#email').value.toLowerCase().match(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)) controls.push([form.querySelector('#email'), 'Email not a valid address'])
 
     if(form.querySelector('#password').value.length < 1)  controls.push([form.querySelector('#password'), 'password is required'])
 
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
 