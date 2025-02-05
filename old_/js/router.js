
const router = new Navigo(`${BASE_HREF}`, { hash: false, strategy: 'ALL' });


const routes = [
    { path: '/', template: parseTemplateUrl('home'), script: parseScriptUrl('home'),startFunction:  'homeActive' },
    { path: '/pricing', template: parseTemplateUrl('pricing') },
    { path: '/about', template: parseTemplateUrl('about') },
    { path: '/contact', template: parseTemplateUrl('contact'), script: parseScriptUrl('contact'),startFunction:  'contactActive' },
    { path: '/services-text', template: parseTemplateUrl('textservice') },
    { path: '/services-campaign', template: parseTemplateUrl('whatsappcampaign') },
    { path: '/services-ads', template: parseTemplateUrl('ads') },
];

function addRoutes() {

    routes.forEach(route => {
        router.on(route.path, ({ data, params, queryString }) => updateView({ ...route, data, params, queryString }));
    });

    router.notFound(function() {
        document.getElementById('app').innerHTML = '<h1>404</h1><p>Page not found!</p>';
    });

    router.resolve();
}

addRoutes()
Object.freeze(routes)
