class App {
    constructor() {
        this.html = document.getElementsByTagName("html")[0], this.config = {}, this.defaultConfig = window.config
    }

    initComponents() {
        // Waves.init()
    }

    initSidenav() {
        var e = window.location.href.split(/[?#]/)[0];
        document.querySelectorAll("ul.admin-menu .menu-item a").forEach(n => {
            if (n.href === e) {
                n.classList.add("active");
                let e = n.closest(".menu-item"), t = (e.classList.add("active"), n.parentElement.parentElement.parentElement.parentElement);
                if (t && t.classList.contains("menu-item")) {
                    const l = t.querySelector(".hs-accordion-toggle");
                    if (l) {
                        l.classList.add("open"), t.classList.add("active");
                        const c = l.nextElementSibling;
                        c && c.classList.remove("hidden")
                    }
                }
            }
        }),

            setTimeout(function () {
                var e, l, c, u, o, i, t = document.querySelector("ul.admin-menu .menu-item.active a.active");
                function m() { e = i += 20, t = u, n = o; var e, t, n = (e /= c / 2) < 1 ? n / 2 * e * e + t : -n / 2 * (--e * (e - 2) - 1) + t; l.scrollTop = n, i < c && setTimeout(m, 20) } null != t && (e = document.querySelector("#app-menu .simplebar-content-wrapper"), t = t.offsetTop - 300, e && 100 < t && (c = 600, u = (l = e).scrollTop, o = t - u, i = 0, m()))
            }, 200)
    }

    reverseQuery(e, t) {
        for (; e;) {
            if (e.parentElement && e.parentElement.querySelector(t) === e) return e;
            e = e.parentElement
        }
        return null
    }

    initfullScreenListener() {
        var e = document.querySelector('[data-toggle="fullscreen"]');
        e && e.addEventListener("click", function (e) {
            e.preventDefault(), document.body.classList.toggle("group-fullscreen"), document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement ? document.cancelFullScreen ? document.cancelFullScreen() : document.mozCancelFullScreen ? document.mozCancelFullScreen() : document.webkitCancelFullScreen && document.webkitCancelFullScreen() : document.documentElement.requestFullscreen ? document.documentElement.requestFullscreen() : document.documentElement.mozRequestFullScreen ? document.documentElement.mozRequestFullScreen() : document.documentElement.webkitRequestFullscreen && document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT)
        })
    }
    
    
    async fetchCurrentprofile() {
        let request = await httpRequest('../controllers/fetchuserprofile')
        
        if (request.status) {
            CONFIG.user = request
        } 
    }

    init() {
        this.initComponents(), this.initSidenav(), this.initfullScreenListener()
        window.HSStaticMethods.autoInit();
        this.fetchCurrentprofile();
    }
    

}

(new App).init();