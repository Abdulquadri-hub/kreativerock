async function homeActive(args) {
    document.querySelector('[name="prev"]').addEventListener('click', swiperSlidePrev)
    document.querySelector('[name="next"]').addEventListener('click', swiperSlideNext)
    initSwiper()
    
    lucide.createIcons();
}

const initSwiper = () => {
    const swiperEl = document.querySelector('swiper-container');
    const swiperParams = {
        slidesPerView: 1,
        loop: true,
        pagination: false,
        autoplay: {
            delay: 20000,
        },
        on: {
            init() { },
        },
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
        speed: 2000
    };

    Object.assign(swiperEl, swiperParams);
    swiperEl.initialize()
}

const swiperSlideNext = () => {
    const swiperEl = document.querySelector('swiper-container');
    swiperEl?.swiper.slideNext();
}

const swiperSlidePrev = () => {
    const swiperEl = document.querySelector('swiper-container');
    swiperEl?.swiper.slidePrev();
}

