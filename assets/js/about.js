document.addEventListener("DOMContentLoaded", () => {
  // Toggle target elements using [data-collapse]

  document
    .querySelectorAll("[data-collapse]")
    .forEach(function (collapseToggleEl) {
      var collapseId = collapseToggleEl.getAttribute("data-collapse");

      collapseToggleEl.addEventListener("click", function () {
        toggleCollapse(
          collapseId,
          document.getElementById(collapseId).classList.contains("hidden")
        );
      });
    });

  initSwiper();
});

window.toggleCollapse = toggleCollapse;

const initSwiper = () => {
  const swiperEl = document.querySelector("swiper-container");
  const swiperParams = {
    slidesPerView: 3,
    loop: true,
    pagination: false,
    autoplay: {
      delay: 1000,
    },
    on: {
      init() {},
    },
    fadeEffect: {
      crossFade: true,
    },
    speed: 2000,
  };

  Object.assign(swiperEl, swiperParams);
  swiperEl.initialize();
};
