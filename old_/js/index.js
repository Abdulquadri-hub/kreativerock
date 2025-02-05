window.onload = () => {
    router.updatePageLinks();

    const responseOpenToggler = document.querySelector('[name="toggle-open"]')
    const responseCloseToggler = document.querySelector('[name="toggle-close"]')
    responseOpenToggler.addEventListener('click', openNav)
    responseCloseToggler.addEventListener('click', closeNav)

    headerStyleEventListener()
}

function openNav() {
    const nav = document.querySelector('nav');
    nav.style.display = 'flex'
}

function closeNav() {
    const nav = document.querySelector('nav');
    nav.style.display = 'none'
}


function headerStyleEventListener() {
    window.addEventListener('scroll', handleScroll);
}

function handleScroll() {
    var header = document.querySelector('header');
    var links = [...Array.from(header.querySelectorAll('nav li a')), ...Array.from(header.querySelectorAll('nav li > span'))];
    var authButtons = header.querySelectorAll('nav a.auth');

    if (window.scrollY > 0) {
        addScrolledStyles(header, links, authButtons);
    } else {
        removeScrolledStyles(header, links, authButtons);
    }
}

function addScrolledStyles(header, links, authButtons) {
    header.classList.add('border-b');
    if (!isMobile()) {
        header.classList.add('!bg-white');
        links.forEach(item => item.classList.add('!text-gray-900'));
        authButtons.forEach(item => item.classList.add('!text-gray-900'));
    }
}

function removeScrolledStyles(header, links, authButtons) {
    header.classList.remove('border-b');
    if (!isMobile()) {
        header.classList.remove('!bg-white');
        links.forEach(item => item.classList.remove('!text-gray-900'));
        authButtons.forEach(item => item.classList.remove('!text-gray-900'));
    }
}

function isMobile() {
    return window.innerWidth <= 768;
}