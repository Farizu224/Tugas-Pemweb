document.addEventListener("DOMContentLoaded", function() {
    const sidebar = document.getElementById("sidebar");
    const sidebarToggle = document.getElementById("sidebarToggle");
    const closeSidebar = document.getElementById("closeSidebar");

    // Toggle sidebar visibility
    sidebarToggle.addEventListener("click", () => {
        sidebar.classList.toggle("active");
    });

    // Close sidebar
    closeSidebar.addEventListener("click", () => {
        sidebar.classList.remove("active");
    });
});
