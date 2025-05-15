  // Submenu toggle for sidebar
  document.querySelectorAll(".has-submenu > .nav-link").forEach((toggle) => {
    toggle.addEventListener("click", function (e) {
      if (this.getAttribute("href") === "#") {
        e.preventDefault();
        this.parentElement.classList.toggle("open");
      }
    });
  });
