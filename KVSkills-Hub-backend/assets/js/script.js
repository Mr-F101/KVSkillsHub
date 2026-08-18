console.log("KVSkills Hub Loaded");

document.addEventListener("DOMContentLoaded", function () {

    // SIDEBAR TOGGLE (mobile)
    const sidebar = document.querySelector(".sidebar");
    const toggleBtn = document.querySelector(".sidebar-toggle");
    const overlaySidebar = document.querySelector(".sidebar-overlay");
    const closeBtn = document.querySelector(".sidebar-close-btn");
    const reopenBtn = document.querySelector(".sidebar-reopen-btn");

    if (sidebar && toggleBtn) {
        toggleBtn.addEventListener("click", function () {
            sidebar.classList.toggle("open");
            if (overlaySidebar) overlaySidebar.classList.toggle("active");
        });
    }

    if (overlaySidebar) {
        overlaySidebar.addEventListener("click", function () {
            sidebar.classList.remove("open");
            overlaySidebar.classList.remove("active");
        });
    }

    // Close button inside the sidebar - works on mobile (off-canvas) and desktop (collapse)
    if (sidebar && closeBtn) {
        closeBtn.addEventListener("click", function () {
            if (window.innerWidth >= 992) {
                sidebar.classList.add("collapsed");
                document.body.classList.add("sidebar-collapsed");
            } else {
                sidebar.classList.remove("open");
                if (overlaySidebar) overlaySidebar.classList.remove("active");
            }
        });
    }

    // Floating button to bring the sidebar back on desktop
    if (sidebar && reopenBtn) {
        reopenBtn.addEventListener("click", function () {
            sidebar.classList.remove("collapsed");
            document.body.classList.remove("sidebar-collapsed");
        });
    }

    const overlay = document.getElementById("loadingOverlay");

    if (!overlay) return; // kalau halaman ni tak ada overlay, skip

    const menuButtons = document.querySelectorAll(".menu-btn");

    menuButtons.forEach(function (btn) {

        btn.addEventListener("click", function (e) {

            const targetUrl = btn.getAttribute("href");
            // kalau href kosong / "#" (halaman belum siap), biar normal, tak perlu loading
            if (!targetUrl || targetUrl === "#") {
                return;
            }

            e.preventDefault(); // halang navigasi terus

            overlay.classList.add("active");

            setTimeout(function () {
                window.location.href = targetUrl;
            }, 900);


        });
    });
});