document.addEventListener("DOMContentLoaded", function () {
  const sidebar = document.getElementById("sidebar");

  // Gracefully exit if sidebar is not present
  if (!sidebar) return;

  const desktopToggle = document.getElementById("desktopSidebarToggle");
  const mobileToggle = document.getElementById("mobileSidebarToggle");
  const mobileClose = document.getElementById("mobileSidebarClose");

  const handleDesktopToggle = () => {
    sidebar.classList.toggle("collapsed");
    localStorage.setItem(
      "sidebarCollapsed",
      sidebar.classList.contains("collapsed")
    );
  };

  // Toggle sidebar on mobile
  if (mobileToggle) {
    mobileToggle.addEventListener("click", function (e) {
      e.preventDefault();
      sidebar.classList.toggle("show");

      if (sidebar.classList.contains("show")) {
        document.body.style.overflow = "hidden";
      } else {
        document.body.style.overflow = "";
      }
    });
  }

  // Attach toggle events
  desktopToggle?.addEventListener("click", function (e) {
    e.preventDefault();
    handleDesktopToggle();
  });

  mobileToggle?.addEventListener("click", function (e) {
    e.preventDefault();
    handleMobileOpen();
  });

  mobileClose?.addEventListener("click", handleMobileClose);

  // Submenu toggle
  document.querySelectorAll(".has-submenu > .nav-link").forEach((toggle) => {
    toggle.addEventListener("click", function (e) {
      if (this.getAttribute("href") === "#") {
        e.preventDefault();
        this.parentElement.classList.toggle("open");
      }
    });
  });

  // Restore desktop state
  if (
    window.innerWidth >= 992 &&
    localStorage.getItem("sidebarCollapsed") === "true"
  ) {
    sidebar.classList.add("collapsed");
  }

  // Auto-close sidebar when clicking outside on mobile
  document.addEventListener("click", function (e) {
    if (
      window.innerWidth < 992 &&
      sidebar.classList.contains("show") &&
      !sidebar.contains(e.target) &&
      !mobileToggle?.contains(e.target)
    ) {
      handleMobileClose();
    }
  });

  // Clean up on resize
  window.addEventListener("resize", function () {
    if (window.innerWidth >= 992) {
      handleMobileClose();
    }
  });
});
