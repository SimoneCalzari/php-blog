"use strict";

// input file
const inputImg = document.getElementById("image");
// contenitore preview immagine
const previewImg = document.getElementById("img-preview");
//  immagine preview
const preview = document.getElementById("preview");
// icona quando non c'è l immagine
const previewIcon = document.getElementById("icon-preview");
// funzione che controlla che sia un img
function isImage(url) {
  return /\.(jpg|jpeg|png|webp|bmp|gif|svg)$/.test(url);
}
inputImg.addEventListener("change", function () {
  // se l input è un immagine la mostro
  if (isImage(inputImg.value)) {
    // nascondo la preview
    previewIcon.classList.add("d-none");
    // creo la path dell immagine
    const path = window.URL.createObjectURL(this.files[0]);
    // assegno la path all img e la mostro
    preview.src = path;
    preview.classList.remove("d-none");
    return;
  }
  //   caso non sto passando un immagine
  previewIcon.classList.remove("d-none");
  preview.classList.add("d-none");
});
