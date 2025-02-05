async function updateView(route) {
    const { template, script, startFunction } = route;
    const html = await httpRequest(template);
    if (html) {
        document.getElementById('app').innerHTML = html;
        document.querySelector('footer').style.display = 'block'
        if (script && startFunction) {
            loadScript(route);
        }
    } 
}
 
function loadScript(route) { 
    const { script, startFunction } = route;
    const scriptEl = document.createElement('script');
    scriptEl.src = script;
    scriptEl.onload = () => {
        if (typeof window[startFunction] === 'function') {
            window[startFunction](route);
        } else {
            console.error(`Function ${startFunction} not found on window object.`);
        }
    };
    scriptEl.onerror = () => {
        console.error(`Failed to load script: ${script}`);
    };
    document.body.appendChild(scriptEl);
}

function parseTemplateUrl(templateName) {
   const path = `./templates/${templateName}${DEFAULT_EXTENTION}`
   return path;
}

function parseScriptUrl(scriptName) {
    const path = `./js/${scriptName}.js`
    return path;
 }

function toggleButtonState(button, isLoading) {
    if (button) {
        button.disabled = isLoading;
        const btnLoader = button.querySelector('.btnloader');
        if (btnLoader) {
            btnLoader.style.display = isLoading ? 'block' : 'none';
        }
    }
}


async function handleFetchResponse(result, type) {
    if (!result.ok) {
        throw new Error('Network response was not ok');
    }
    if (type === "json") {
        return await result.json();
    }
    return await result.text();
}


async function httpRequest(url, payload = null, button = null, type = "text") {
    try {
        toggleButtonState(button, true);

        const fetchOptions = {
            method: payload ? 'POST' : 'GET',
            body: payload ? payload : null,
            headers: payload ? new Headers() : undefined
        };

        const result = await fetch(url, fetchOptions);
        return await handleFetchResponse(result, type);
    } catch (error) {
        console.error(error);
        notification('Unable to perform request.', 0);
    } finally {
        toggleButtonState(button, false);
    }
}


function createNotificationHTML(message, type) {
    const baseClasses = 'animate__animated animate__fadeInDown w-full md:w-[300px] lg:w-[400px] font-inter font-medium text-2xs tracking-wide text-center p-3 first-letter:capitalize';
    let typeClasses = 'bg-white text-gray-900 border shadow-md';

    if (type === 0) {
        typeClasses = 'bg-red-100 text-red-900';
    } else if (type === 1) {
        typeClasses = 'bg-green-100 text-green-900';
    }

    return `<span class="${baseClasses} ${typeClasses}">${message}</span>`;
}


function notification(message, type = undefined, timeout = 5000) {
    const html = createNotificationHTML(message, type);
    const container = document.createElement('div');
    
    container.id = 'toast';
    container.innerHTML = html;
    container.classList.add('flex', 'items-center', 'w-full', 'top-0', 'justify-center', 'left-0', 'z-50', 'fixed', 'font-mont', 'px-2', 'py-2', 'lg:p-0');
    
    document.body.appendChild(container);

    setTimeout(() => container.remove(), timeout);
}