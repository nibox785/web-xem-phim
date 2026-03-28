document.addEventListener("DOMContentLoaded", function () {
  const hamburger = document.querySelector(".hamburger");
  const menu = document.querySelector(".menu");

  if (hamburger && menu) {
    hamburger.addEventListener("click", function () {
      menu.classList.toggle("active");
    });

    // Close menu when clicking outside
    document.addEventListener("click", function (event) {
      const isClickInsideMenu = menu.contains(event.target);
      const isClickOnHamburger = hamburger.contains(event.target);

      if (
        !isClickInsideMenu &&
        !isClickOnHamburger &&
        menu.classList.contains("active")
      ) {
        menu.classList.remove("active");
      }
    });
  } else {
    console.error("Hamburger or menu element not found");
  }
});
