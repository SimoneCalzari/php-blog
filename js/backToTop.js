"use strict";
// button back to top
const backTop = document.getElementById("back-to-top");
// back to top function
backTop.addEventListener("click", function () {
  document.getElementById("back-to-top-target").scroll({
    top: 0,
    behavior: "smooth",
  });
});
