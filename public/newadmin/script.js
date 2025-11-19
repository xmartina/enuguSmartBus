const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("overlay");
const menuButton = document.getElementById("menuButton");
const closeButton = document.getElementById("closeButton");

function openSidebar() {
  sidebar.classList.add("sidebar-active");
  overlay.classList.remove("hidden");
}

function closeSidebar() {
  sidebar.classList.remove("sidebar-active");
  overlay.classList.add("hidden");
}

menuButton.addEventListener("click", openSidebar);
closeButton.addEventListener("click", closeSidebar);
overlay.addEventListener("click", closeSidebar);
