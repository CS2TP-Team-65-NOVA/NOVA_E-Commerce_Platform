
    document.addEventListener("DOMContentLoaded", function () {
    const body = document.body;
    const toggleBtn = document.getElementById("theme-toggle");
    const themeIcon = document.getElementById("theme-icon");
    const logo = document.getElementById("logo");

    function applyTheme(theme) {
        if (theme === "dark") {
            body.classList.add("dark-mode");
            localStorage.setItem("nova-theme", "dark");

            if (themeIcon) themeIcon.textContent = "☀️";
            if (logo) logo.src = "nova_logo_white.png";
        } else {
            body.classList.remove("dark-mode");
            localStorage.setItem("nova-theme", "light");

            if (themeIcon) themeIcon.textContent = "🌙";
            if (logo) logo.src = "nova_logo_black.png";
        }
    }

    const savedTheme = localStorage.getItem("nova-theme") || "light";
    applyTheme(savedTheme);

    if (toggleBtn) {
        toggleBtn.addEventListener("click", function () {
            const newTheme = body.classList.contains("dark-mode") ? "light" : "dark";
            applyTheme(newTheme);
        });
    }
});
