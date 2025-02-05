window.onload = () => {
    document.querySelector('#hero [name="prev"]').addEventListener('click', swiperSlidePrev)
    document.querySelector('#hero [name="next"]').addEventListener('click', swiperSlideNext)
    initSwiper()
}


const initSwiper = () => {
    const swiperEl = document.querySelector('#hero swiper-container');
    const swiperParams = {
        slidesPerView: 1,
        loop: true,
        pagination: false,
        autoplay: {
            delay: 50000,
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

