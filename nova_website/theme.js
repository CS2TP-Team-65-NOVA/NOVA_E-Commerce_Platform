document.addEventListener("DOMContentLoaded", function () {
    const body = document.body;
    const toggleBtn = document.getElementById("theme-toggle");
   
    const logo = document.getElementById("logo");

    function applyTheme(theme) {
    // remove both first (IMPORTANT)
    body.classList.remove("light-mode", "dark-mode");

    if (theme === "dark") {
        body.classList.add("dark-mode");
        localStorage.setItem("nova-theme", "dark");

        if (logo) logo.src = "nova_logo_white.png";
    } else {
        body.classList.add("light-mode");
        localStorage.setItem("nova-theme", "light");

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