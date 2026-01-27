// theme.js
document.addEventListener("DOMContentLoaded", () => {
    const theme = localStorage.getItem("theme");
    if (theme === "dark") {
        document.body.classList.add("dark-mode");
    }

    const toggleBtn = document.getElementById("toggle-theme");
    if (toggleBtn) {
        toggleBtn.addEventListener("click", () => {
            document.body.classList.toggle("dark-mode");
            const newTheme = document.body.classList.contains("dark-mode") ? "dark" : "light";
            localStorage.setItem("theme", newTheme);
        });
    }
});
