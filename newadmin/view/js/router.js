const router = new Navigo(`${BASE_HREF}`, { hash: false, strategy: 'ALL' });

const routes = [
    { 
        path: '/', 
        template: parseTemplateUrl('home'), 
        script: parseScriptUrl('home'),
        startFunction: 'homeActive',
    },
    { path: '/access',  template: parseTemplateUrl('accesscontrol_'), script: parseScriptUrl('accesscontrol'), startFunction: 'accesscontrolActive'},
    { path: '/password/update',  template: parseTemplateUrl('changepassword_'),  script: parseScriptUrl('changepassword'), startFunction: 'changepasswordActive'},
    { path: '/user/profile',  template: parseTemplateUrl('profile_'),  script: parseScriptUrl('profile'), startFunction: 'profileActive'},
    { path: '/user/select',  template: parseTemplateUrl('selectuser_'),  script: parseScriptUrl('selectuser'), startFunction: 'selectUserActive'},
    { path: '/user/deactivate',  template: parseTemplateUrl('deactivateuser_'),  script: parseScriptUrl('deactivateuser'), startFunction: 'deactivateUserActive'},
    
    { path: '/contact/list', template: parseTemplateUrl('contact'), script: parseScriptUrl('contact'), startFunction: 'contactActive' },
    { path: '/contact/import/options', template: parseTemplateUrl('importcontactoptions') },
    { 
        path: '/contact/import/upload', 
        template: parseTemplateUrl('uploadcontacts'), 
        script: parseScriptUrl('uploadcontacts'),
        startFunction: 'uploadcontactsActive',
        extras: ['assets/libs/papaparse/papaparse.js'] 
    },
    { 
        path: '/contact/import/copy-paste', 
        template: parseTemplateUrl('copypastecontacts'), 
        script: parseScriptUrl('copypastecontacts'),
        startFunction: 'copypastecontactsActive',
        extras: ['assets/libs/papaparse/papaparse.js'] 
    },
    { 
        path: '/contact/manage', 
        template: parseTemplateUrl('managecontacts'), 
        script: parseScriptUrl('managecontacts'),
        startFunction: 'managecontactsActive',
    },
    { 
        path: '/units', 
        template: parseTemplateUrl('units_'), 
        script: parseScriptUrl('units'),
        startFunction: 'unitsActive',
    },
    { 
        path: '/contact/segment', 
        template: parseTemplateUrl('segment'), 
        script: parseScriptUrl('segment'),
        startFunction: 'segmentActive',
    },
    { 
        path: '/payment', 
        template: parseTemplateUrl('payment_'), 
        script: parseScriptUrl('payment'),
        startFunction: 'paymentActive',
    },
    { 
        path: '/payment/confirmation', 
        template: parseTemplateUrl('comfirmation_'), 
        script: parseScriptUrl('confirmation'),
        startFunction: 'confirmationActive',
    },
    { 
        path: '/campaign/option', 
        template: parseTemplateUrl('campaignoptions'),
        script: parseScriptUrl('campaignoptions'),
        startFunction: 'campaignoptionsActive',
    },
    { 
        path: '/campaign/setup/name', 
        template: parseTemplateUrl('campaignsetup-1'),
        script: parseScriptUrl('campaignsetup-1'),
        startFunction: 'campaignSetup1Active',
    },
    { 
        path: '/campaign/setup/edit', 
        template: parseTemplateUrl('campaignsetup-2'),
        script: parseScriptUrl('campaignsetup-2'),
        startFunction: 'campaignSetup2Active',
    },
];

function addRoutes() {

    routes.forEach(route => {
        router.on(route.path, ({ data, params, queryString }) => updateView({ ...route, data, params, queryString }));
    });

    router.notFound(function() {
        document.querySelector('main').innerHTML = '<h1>404</h1><p>Page not found!</p>';
    });

    router.resolve();
}


async function updateView(route) {
    document.querySelector('main').innerHTML = `
        <div class="w-full p-10 flex mt-20">
            <div class="loader m-auto"></div>
        </div>
    `
    
    const { template, script, startFunction, extras } = route;
    const html = await httpRequest(template, null, null, 'text');
    
    if (html) {
        const pageTitle = getRoutePageTitle()

        document.querySelector('main').innerHTML = `
            <div class="flex items-center md:justify-between flex-wrap gap-2 mb-5">
                <h4 class="text-default-900 text-2xl font-semibold capitalize">${ pageTitle ? pageTitle : '' }</h4>
            </div>
            ${ html }
        `;
        
        changePageTheme(route)
        
        if ((script && startFunction) || extras?.length) {
            loadScript(route);
        } else {
            initializeTemplateComponents()
        }
        
    } 
}

function changePageTheme(route) {
    const mainContent = document.querySelector('main')
    
    if (route.path.includes('setup/edit')) {
        mainContent.classList.add('bg-white')
        return
    } 
    
    mainContent.classList.remove('bg-white')
}

function getRoutePageTitle() {
    const pathName = location.pathname.split('/').filter(item => item).filter(item => !['kreativerock', 'newadmin', 'view'].includes(item))
    const routeName = pathName.join('/')
    const clickedAnchor = document.querySelector(`a[href^="/${ routeName }"]`);
    
    return clickedAnchor?.dataset.title ?? false
}
 
function loadScript_(route) { 
    const { script, startFunction, extras } = route;
    const scriptEl = document.createElement('script');
    scriptEl.src = script;
    scriptEl.onload = () => {
        if (typeof window[startFunction] === 'function') {
            window[startFunction](route);
            initializeTemplateComponents()
            
        } else {
            console.error(`Function ${startFunction} not found on window object.`);
        }
    };
    scriptEl.onerror = () => {
        console.error(`Failed to load script: ${script}`);
    };
    document.body.appendChild(scriptEl);
}

function loadScript(route) {
    const { script, startFunction, extras = [] } = route;

    // Helper function to load a single script
    function loadSingleScript(src) {
        return new Promise((resolve, reject) => {
            const scriptEl = document.createElement('script');
            scriptEl.src = src;
            scriptEl.onload = () => resolve(src);
            scriptEl.onerror = () => reject(`Failed to load script: ${src}`);
            document.body.appendChild(scriptEl);
        });
    }

    // Load all scripts in sequence (extras first, then main script)
    const scriptsToLoad = [...extras, script].filter(item => item);

    Promise.all(scriptsToLoad.map(loadSingleScript))
        .then(() => {
            if(!script) return 
            
            if (typeof window[startFunction] === 'function') {
                initializeTemplateComponents();
                window[startFunction](route);
            } else {
                console.error(`Function ${startFunction} not found on window object.`);
            }
        })
        .catch((error) => {
            console.error(error);
        });
}


function initializeTemplateComponents() {
   try{
       window.HSStaticMethods?.autoInit();
       initMask()
   } catch(e) {} 
}


function parseTemplateUrl(templateName) {
   const path = `./${templateName}${DEFAULT_EXTENTION}`
   return path;
}

function parseScriptUrl(scriptName) {
    const path = `./js/${scriptName}.js`
    return path;
 }


addRoutes()
Object.freeze(routes)
