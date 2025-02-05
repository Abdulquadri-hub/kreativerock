const baseurl = 'https://comeandsee.com.ng/kreativerock/admin/controllers'

function validateForm(formm, ids=null) {
    let form = document.getElementById(formm)
    let errorElements = form.querySelectorAll('.control-error')
    let controls = [] 
    
    if(ids){
        for(let i=0;i<ids.length;i++){
            // console.log('xxx', ids[i])
            if(controlHasValue(form, `#${ids[i]}`))controls.push([form.querySelector(`#${ids[i]}`), `${form.querySelector(`#${ids[i]}`).previousElementSibling.textContent ? form.querySelector(`#${ids[i]}`).previousElementSibling.textContent : ids[i]} is required`])
        }
    }
    
    return mapValidationErrors(errorElements, controls)   

}

// const callController =(controller, params, funct, silent, e)=>{ 
//     // silent ? null : showSpinner();
// //     if(validate){
// //         if(!validateInputsComponent(validate)){ 
// // // 		hideSpinner();
// // 		return null; 
// // 		}
//     }
//     var request = getAjaxObject();
//     request.open('POST',`../controllers/${controller}`,true);
//     request.onreadystatechange = function(){
//         if(request.readyState == 1){
//         }
//         if(request.readyState == 4 && request.status == 200){
//             // hideSpinner();
//                 // console.log(`controller request.responseText ${name}:`, request.responseText)
//                 let result = JSON.parse(request.responseText); 
//                 //console.log(result);
//                 if(result["code"] == "invalid session data. Proceed to login"){
//                     window.location.href = "login.php";
//                     return;
//                 }
//                 if(result["message"] == "invalid session data. Proceed to login"){
//                     window.location.href = "login.php";
//                     return;
//                 }
//                 if(result["result"] === "ERROR"){
//                     // console.log(`contrlr status ${name}: ERROR`);
//                     notification(`Failed: ${result.message ? result.message : ''}`, 0);
//                     return null;
//                 }else{
//                     // console.log(`controller ${controller}`, result, 'NB: result returned','name', `${name}`);
//                     if(!silent){
//                         if(result.message == 'Successful'){
//                             notification(`${result.message ? result.message : 'Successful'}`, 1)
//                         }else{
//                             notification(`${result.message ? result.message : 'Successful'}`, 2)
//                         }
//                     }
//                     //console.log(funct)
//                     if(funct){
//                         return funct(result);
//                     } 
//                 }
//         }else{
//             // hideSpinner();
//         }
//         try{
//             e.stopPropagation();
//         }catch(ex){}
//     };
//     if(params != null){
//             //   console.log(name, 'PARAMS BELOW');    
//         for (var pair of params.entries()) {
//             //   console.log(pair[0] + ', ' + pair[1]);   
//             // return(name, pair[0]+ ', ' + pair[1]); 
//             }
//     }
    
//     request.setRequestHeader('Connection','close');
//     request.send(params);
// };

function previewImage(img = "url", preview = "imagePreview", size = "200") {
    var input = document.getElementById(`${img}`);
    var preview = document.getElementById(preview);

    // Clear previous preview
    preview.innerHTML = '';

    // Check if any file is selected
    if (input.files && input.files[0]) {
        var fileSize = input.files[0].size; // Get the file size in bytes

        // Check if the size parameter is provided
        if (size !== "") {
            // Convert the size parameter to bytes if it is provided in kilobytes
            const maxSize = parseInt(size) * 1024;

            // Check if the file size exceeds the specified limit
            if (fileSize > maxSize) {
                // Display a notification and return
                notification('File size exceeds the required limit', 0);
                input.value = ''
                return;
            }
        }

        var reader = new FileReader();

        reader.onload = function (e) {
            // Create an image element
            var image = document.createElement('img');
            image.src = e.target.result;
            image.style.width = '200px'; // Set a specific width for the preview (adjust as needed)
            image.style.borderRadius = '10px'; // Set a specific width for the preview (adjust as needed)

            // Append the image to the preview div
            preview.appendChild(image);
        };

        // Read the selected file as a data URL
        reader.readAsDataURL(input.files[0]);
    }
}
    
// TO POPULATE DATA FROM AN ENDPOINT 
function populateData(data, img, imgview="imagePreview") {
    let valuesArray = Object.values(data);
    let keysArray = Object.keys(data);
    let mappedKeys = keysArray.map(function (key) {
        return key
    });
    let mappedValues = valuesArray.map(function (val) {
        return val
    });
    // console.log(mappedKeys, mappedValues)
    for (let i = 0; i < mappedKeys.length; i++) {
        try {
            const inputElement = document.getElementsByName(`${mappedKeys[i]}`)[0];

            if (inputElement) {
                // Check if the input is a file input
                if (mappedKeys[i] == img && mappedValues[i]) {
                    // Create img element and set its src attribute
                    // alert(img, mappedValues[i])
                    const imgElement = document.createElement('img');
                    imgElement.src = `https://nglohitech.com/elfrique/admin/images/${mappedValues[i]}`;
                    imgElement.alt = 'Image Preview';
                    imgElement.style.maxWidth = '100%';
                    imgElement.style.maxHeight = '200px';

                    // Replace or append the img element in the 'imgPreview' div
                    const imgPreviewDiv = document.getElementById(imgview);
                    imgPreviewDiv.innerHTML = ''; // Clear previous content
                    imgPreviewDiv.appendChild(imgElement);
                } else {
                    // Update other input types
                    inputElement.value = mappedValues[i];
                }
            }
        } catch (err) {
            console.log(err);
        }
    }
}



// RESOLVING FORMDATA AND ACCOUNTING FOR ADDITIONAL DATA... NB:additional = [ ['id', 12], ['name', 'oreva'], ['age', 23] ]
const getFormData2 =(form=null, additional=[])=> {
    let formdata = new FormData(form)
    if(additional){
        for(let i=0;i<additional.length;i++){
            formdata.set(`${additional[i][0]}`, additional[i][1])
        }
    }
    formdata.forEach(function (value, key) {
        // console.log(`${key}: ${value}`);
    });
    return formdata
} 

//TO SCROLL TO THE TOP
function scrollToTop(id=null) {
    if(id)document.getElementById(id).scrollIntoView({ behavior: 'smooth' });
    window.scrollTo({
        top: 0,
        behavior: 'smooth' // You can use 'auto' or 'smooth' for scrolling behavior
    });
}

function did(element){
    if(document.getElementById(element))return document.getElementById(element)
}

function dna(element){
    if(document.getElementsByNames(element))return document.getElementsByNames(element)
}

function getIdFromCls(cls){
    let x = []
    for(let i=0;i<document.getElementsByClassName(cls).length;i++){
        x.push(document.getElementsByClassName(cls)[i].id)
    }
    return x
}
 
function specialformatDateTime(inputDateTime) {
  const months = [
    'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
  ];

  const dateObj = new Date(inputDateTime);
  const day = dateObj.getDate();
  const month = months[dateObj.getMonth()];
  const year = dateObj.getFullYear();
  const hours = dateObj.getHours() % 12 || 12; // Convert to 12-hour format
  const minutes = dateObj.getMinutes();
  const ampm = dateObj.getHours() < 12 ? 'am' : 'pm';

  const formattedDateTime = `${day}th ${month}, ${year} ${hours}:${minutes < 10 ? '0' : ''}${minutes}${ampm.toLowerCase()}`;

  return formattedDateTime;
} 

function formatDateT(inputDateTime) {
  const months = [
    'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
  ];

  const dateObj = new Date(inputDateTime);
  const day = dateObj.getDate();
  const month = months[dateObj.getMonth()];
  const year = dateObj.getFullYear();
  const hours = dateObj.getHours() % 12 || 12; // Convert to 12-hour format
  const minutes = dateObj.getMinutes();
  const ampm = dateObj.getHours() < 12 ? 'am' : 'pm';

  const formattedDateTime = `${day}th ${month}, ${year} ${hours}:${minutes < 10 ? '0' : ''}${minutes}${ampm.toLowerCase()}`;

  return formattedDateTime;
}

function generateUID() {
    const timestamp = new Date().getTime(); // Get current timestamp
    const randomPart = Math.floor(Math.random() * 1000); // Add a random number to avoid collisions
    return `id_${timestamp}_${randomPart}`;
}

function genID() {
    const timestamp = new Date().getTime(); // Get current timestamp
    const randomPart = Math.floor(Math.random() * 1000); // Add a random number to avoid collisions
    return `${timestamp}${randomPart}`;
}

const checkdatalist =(element, id)=>{
    if(!element.value)return
    let list = element.list.id
    const datalistOptions = did(`${list}`).options
    const inputValue = element.value;
    
    let isMatch = false;
    for (let i = 0; i < datalistOptions.length; i++) {
        if (inputValue === datalistOptions[i].value) {
            isMatch = true;
            break;
        }
    }

    if (isMatch) {
        // alert('Input is valid.'); 
        console.log('value', getLabelByValue(element.list.id, element.value))
        if(did(id))did(id).value = getLabelByValue(element.list.id, element.value)
        return true
    } else {
        notification(`${inputValue} is not a valid option`, 0);
        let ini = element.style.borderColor
        element.style.borderColor = 'red';
        element.style.color = 'red';
            element.value = '';
        setTimeout(()=>{
            element.style.borderColor = ini;
            element.style.color = 'black';
        },4000)
        if(did(id))did(id).value = ''
        return false
    }
}

function showFileName(id) {
  const input = document.getElementById(id);
  
  // Check if any file is selected
  if (input.files.length > 0) {
    const fileName = input.files[0].name;
    // console.log('Selected file name:', fileName);
    return fileName
  }
}

function getFile(id) {
  const input = document.getElementById(id);
  
  // Check if any file is selected
  if (input.files.length > 0) {
    const selectedFile = input.files[0];
    console.log('Selected file:', selectedFile);
    return selectedFile;
  }
}

function getLabelByValue(id, value) {
    const selectElement = document.getElementById(id);
    
    for (const option of selectElement.options) {
        if (option.value === value) {
            return option.text;
        }
    }

    // If the value is not found
    return null;
}

function disableOptionByValue(id, value, disable=true) {
    const selectElement = document.getElementById(id);
    
    for (const option of selectElement.options) {
        if (option.value === value) {
            if(disable)option.disabled = true;
            if(!disable)option.disabled = false;
            break;  // No need to continue once the option is found and disabled
        }
    }
}

function hideOptionByValue(id, value, hide=true) {
    const selectElement = document.getElementById(id);
    
    for(let i=0;i<document.getElementById(id).children.length;i++){
        if(document.getElementById(id).children[i].value == value){
            if(hide)document.getElementById(id).children[i].classList.add('hidden')
            if(!hide)document.getElementById(id).children[i].classList.remove('hidden')
        }
    }
}

function runCount (id, cls){
    for(let i=0;i<document.getElementsByClassName(cls).length;i++){
        document.getElementsByClassName(cls)[i].innerHTML = i+1
    }
}
 
function formatCurrency(amount) {
  // Use the Intl.NumberFormat object for currency formatting
  const formatter = new Intl.NumberFormat('en-NG', {
    style: 'currency',
    currency: 'NGN',
    minimumFractionDigits: 2,
  });

  // Format the amount using the formatter
  const formattedCurrency = formatter.format(amount);

  return formattedCurrency;
}


function formatNumber(number) {
  // Use regex to add commas to the number
  return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function truncateStr(inputString, no=10) {
  const maxLength = no;

  // Check if the string length is greater than the maximum allowed
  if (inputString.length > maxLength) {
    // Truncate the string to the maximum length and add "..."
    return inputString.slice(0, maxLength) + "...";
  } else {
    // If the string is within the limit, return the original string
    return inputString;
  }
}

function formatTime(inputTime) {
    // Parse the input time
    const [hours, minutes, seconds] = inputTime.split(':').map(Number);

    // Determine if it's AM or PM
    const period = hours >= 12 ? 'pm' : 'am';

    // Convert to 12-hour format
    const formattedHours = hours % 12 === 0 ? 12 : hours % 12;

    // Create the formatted time string
    const formattedTime = `${formattedHours}:${minutes} ${period}`;

    return formattedTime;
}

function formatDate(inputDate) {
  // Parse the input date string
  const date = new Date(inputDate);

  // Define options for formatting
  const options = { year: 'numeric', month: 'long', day: 'numeric' };

  // Format the date
  const formattedDate = date.toLocaleDateString('en-US', options);

  return formattedDate;
}

function givenamebyclass(cls, name){
    if(!name)name = cls;
    if(document.getElementsByClassName(cls).length < 1)return
    for(let i=0; i<document.getElementsByClassName(cls).length; i++){
        document.getElementsByClassName(cls)[i].setAttribute('name', `${name}${i+1}`)
    }
}

function runoptioner(element){
    let name = element.getAttribute('name')
    for(let i=0;i<document.getElementsByClassName('optioner').length;i++){
            document.getElementsByClassName('optioner')[i].classList.remove('!text-blue-600', 'active')
        if(document.getElementsByClassName('optioner')[i].getAttribute('name') == name){
            document.getElementsByClassName('optioner')[i].classList.add('!text-blue-600', 'active')
            document.getElementById(`${name}`).classList.remove('hidden')
        }else{
            document.getElementsByClassName('optioner')[i].classList.remove('!text-blue-600', 'active')
            document.getElementById(`${document.getElementsByClassName('optioner')[i].getAttribute('name')}`).classList.add('hidden')
        }
    }
}

function ordinalSuffix(num) {
    if (num === 1) return num + "st";
    if (num === 2) return num + "nd";
    if (num === 3) return num + "rd";
    if (num >= 4 && num <= 20) return num + "th";

    const lastDigit = num % 10;
    switch (lastDigit) {
        case 1: return num + "st";
        case 2: return num + "nd";
        case 3: return num + "rd";
        default: return num + "th";
    }
}

function runCount (cls='s/n', suffix=false){
    for(let i=0;i<document.getElementsByClassName(cls).length;i++){
        if(!suffix)document.getElementsByClassName(cls)[i].innerHTML = i+1
        if(suffix)document.getElementsByClassName(cls)[i].innerHTML = ordinalSuffix(i+1)
    }
}

function calculateAge(birthDate) {
    const today = new Date();
    const birthDateObj = new Date(birthDate);
    let age = today.getFullYear() - birthDateObj.getFullYear();
    const monthDiff = today.getMonth() - birthDateObj.getMonth();

    // If the birth month is after the current month or 
    // if the birth month is the same as the current month but the birth day is after the current day,
    // subtract 1 from age
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDateObj.getDate())) {
        age--;
    }

    return age;
}

// printContent('LIST OF GENERAL LEDGER ACCOUNT',`<link rel="stylesheet" type="text/css" media="print" href="./css/index.css"><link rel="stylesheet" type="text/css" media="print" href="./css/user.css"><link rel="stylesheet" type="text/css" media="print" href="./css/style.css"><link rel="stylesheet" type="text/css" media="print" href="./css/jstyle.css">`,'viewglfulltableparant')
var printContent = (header, path = `<link rel="stylesheet" type="text/css" media="print" href="./css/index.css"><link rel="stylesheet" type="text/css" media="print" href="./css/user.css"><link rel="stylesheet" type="text/css" media="print" href="./css/style.css"><link rel="stylesheet" type="text/css" media="print" href="./css/css_vanilla.css">`, contentid, select = false) => {
    let content = document.getElementById(`${contentid}`);
    if (path == null) {
        path = `<link rel="stylesheet" type="text/css" media="print" href="./css/index.css"><link rel="stylesheet" type="text/css" media="print" href="./css/user.css"><link rel="stylesheet" type="text/css" media="print" href="./css/style.css"><link rel="stylesheet" type="text/css" media="print" href="./css/css_vanilla.css">`
    }
    if (content) {
        // Clone the content element to avoid modifying the original
        let contentClone = content.cloneNode(true);

        // Detach the cloned content from the document
        contentClone.removeAttribute('id');

        // Convert select elements to radio buttons in a flex-column pattern in the cloned content if select is true
        if (select) {
            contentClone.querySelectorAll('select').forEach(selectElement => {
                const radioContainer = document.createElement('div');
                radioContainer.className = 'flex-column'; // Apply flex-column styling
                selectElement.querySelectorAll('option').forEach(option => {
                    const radioInput = document.createElement('input');
                    radioInput.type = 'radio';
                    radioInput.name = selectElement.name; // Ensure radio buttons have the same name attribute
                    radioInput.value = option.value;
                    radioInput.className = 'mr-4'; // Add 'mr-4' class
                    radioContainer.appendChild(radioInput);

                    const label = document.createElement('label');
                    label.textContent = option.textContent;
                    label.style.marginLeft = '6px'
                    label.style.marginRight = '20px'
                    label.style.marginTop = '20px'
                    label.style.marginBottom = '20px'
                    radioContainer.appendChild(label);
                });
                selectElement.parentNode.replaceChild(radioContainer, selectElement);
            });
        }

        // Remove placeholder attribute from all input elements in the cloned content
        contentClone.querySelectorAll('input').forEach(inputElement => {
            inputElement.removeAttribute('placeholder');
        });
        contentClone.querySelectorAll('textarea').forEach(inputElement => {
            inputElement.removeAttribute('placeholder');
        });

        // Print the modified content
        var winPrint = window.open(`${header}`, '', 'width=1000,height=900');
        winPrint.document.write('<html><head><title></title>');
        winPrint.document.write(`${path}</head><body>`);
        winPrint.document.write(`<h1 style="text-align:center;font-weight:400px;text-transform:uppercase;font-size:14px;">${header}</h1>` + contentClone.innerHTML);
        winPrint.document.write('<script type="text/javascript">addEventListener("load", () => { print(); close(); })</script></body></html>');
        winPrint.document.close();
        winPrint.focus();
    }
}

function exportToPDF(id, vertical=false){
    const element = document.getElementById(id);
    // if(vertical)element.style.transform = 'rotate(90deg)';
    html2pdf()
        .from(element)
        .save();
}

var exportToExcel = (function(table, name) { 
    var uri = 'data:application/vnd.ms-excel;base64,' 
      , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
      , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
      , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
    return function(table, name) {
      if (!table.nodeType) table = document.getElementById(table)
      var ctx = { worksheet: name || 'Worksheet', table: table.innerHTML }
      window.location.href = uri + base64(format(template, ctx))
    }
})()


function copy(content) {
    navigator.clipboard.writeText(content).then(function() {
        notification('copied', 1)
    }).catch(function(error) {
        console.error('Error copying to clipboard:', error);
    });
}


// LIST OF ALL THE COUNTRIES IN THE WORLD
const oreCountries = [
  "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria",
  "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia",
  "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cabo Verde", "Cambodia", "Cameroon",
  "Canada", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Costa Rica", "Croatia", "Cuba", "Cyprus",
  "Czech Republic", "Democratic Republic of the Congo", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt",
  "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini", "Ethiopia", "Fiji", "Finland", "France", "Gabon", "Gambia",
  "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Honduras", "Hungary",
  "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Ivory Coast", "Jamaica", "Japan", "Jordan", "Kazakhstan",
  "Kenya", "Kiribati", "Kosovo", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein",
  "Lithuania", "Luxembourg", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania",
  "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar (Burma)",
  "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "North Macedonia", "Norway",
  "Oman", "Pakistan", "Palau", "Palestine", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal",
  "Qatar", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino",
  "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands",
  "Somalia", "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "Sudan", "Suriname", "Sweden", "Switzerland", "Syria", "Taiwan",
  "Tajikistan", "Tanzania", "Thailand", "Timor-Leste", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu",
  "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City",
  "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"
];

const industries = [
    "Technology",
    "Healthcare",
    "Finance",
    "Manufacturing",
    "Retail",
    "Education",
    "Hospitality",
    "Construction",
    "Energy",
    "Transportation and Logistics",
    "Media and Entertainment",
    "Agriculture",
    "Legal Services",
    "Non-Profit and NGOs",
    "Public Sector"
];

const companyPositions = [
    "Chief Executive Officer (CEO)",
    "Chief Operating Officer (COO)",
    "Chief Financial Officer (CFO)",
    "Chief Technology Officer (CTO)",
    "Chief Marketing Officer (CMO)",
    "Chief Human Resources Officer (CHRO)",
    "Chief Information Officer (CIO)",
    "Chief Product Officer (CPO)",
    "Chief Compliance Officer (CCO)",
    "Chief Security Officer (CSO)",
    "President",
    "Vice President",
    "Director",
    "General Manager",
    "Operations Manager",
    "Product Manager",
    "Project Manager",
    "Account Manager",
    "Sales Manager",
    "Marketing Manager",
    "Human Resources Manager",
    "IT Manager",
    "Finance Manager",
    "Business Development Manager",
    "Customer Service Manager",
    "Administrative Assistant",
    "Executive Assistant",
    "Office Manager",
    "Sales Representative",
    "Business Analyst",
    "Financial Analyst",
    "Software Engineer",
    "Systems Engineer",
    "Network Engineer",
    "Data Scientist",
    "Data Analyst",
    "UX/UI Designer",
    "Graphic Designer",
    "Content Writer",
    "Copywriter",
    "Social Media Manager",
    "Digital Marketing Specialist",
    "SEO Specialist",
    "Public Relations Specialist",
    "Recruiter",
    "Talent Acquisition Specialist",
    "Legal Counsel",
    "Compliance Officer",
    "Quality Assurance (QA) Engineer",
    "Customer Support Specialist",
    "Technical Support Specialist",
    "Operations Coordinator",
    "Supply Chain Manager",
    "Logistics Manager",
    "Procurement Specialist",
    "Research and Development (R&D) Engineer",
    "Manufacturing Engineer",
    "Production Supervisor",
    "Maintenance Technician",
    "Sales Associate",
    "Customer Success Manager",
    "Field Service Technician",
    "Business Consultant",
    "Risk Manager",
    "Environmental Health and Safety (EHS) Specialist",
    "Training and Development Manager",
    "Event Coordinator",
    "Facilities Manager",
    "Security Officer"
];

function loadCountries(){
     if(document.getElementById('countrylist'))document.getElementById('countrylist').innerHTML = oreCountries.map(data=>`<option value="${data}"/>`).join('')
}
