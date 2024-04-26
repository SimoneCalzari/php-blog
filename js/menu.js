"use strict";
// USER
const user = document.getElementById("user");
// USER MENU
const userMenu = document.getElementById("menu-user");
// CLICK SU USER
user.addEventListener("click", function (e) {
  if (e.target.contains(this)) {
    userMenu.classList.toggle("d-none");
  }
});
// CHIUSARA MENU QUANDO CLICCO FUORI
window.addEventListener("click", function (e) {
  if (e.target !== user) {
    userMenu.classList.add("d-none");
  }
});
