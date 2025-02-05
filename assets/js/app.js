/*********************************/
/*         INDEX                 */
/*================================
 *     01.  Sticky Navbar         *
 *     02.  Navbar active         *
 *     03.  Back to top           *
 *     04.  Accordions            *
 *     05.  Lucide Icons          *
 ================================*/


document.addEventListener("DOMContentLoaded", () => {
  // Toggle target elements using [data-collapse]

  document
    .querySelectorAll("[data-collapse]")
    .forEach(function (collapseToggleEl) {
      var collapseId = collapseToggleEl.getAttribute("data-collapse");

      collapseToggleEl.addEventListener("click", function () {
        console.log(true)
        toggleCollapse(
          collapseId,
          document.getElementById(collapseId).classList.contains("hidden")
        );
      });
    });

});

window.toggleCollapse = toggleCollapse;




