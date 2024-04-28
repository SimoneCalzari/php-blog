"use strict";

// input password
const psw = document.getElementById("psw");
// occhiolino slash
const eyeSlash = document.querySelector(".fa-eye-slash");
// occhiolino  no slash
const eyeNoSlash = document.querySelector(".fa-eye");

// mostro password
eyeSlash.addEventListener("click", function () {
  this.classList.add("d-none");
  eyeNoSlash.classList.remove("d-none");
  psw.type = "text";
});

// nascondo password
eyeNoSlash.addEventListener("click", function () {
  this.classList.add("d-none");
  eyeSlash.classList.remove("d-none");
  psw.type = "password";
});
