"use strict";

// input file
const inputImg = document.getElementById("image");
// contenitore preview immagine
const previewImg = document.getElementById("img-preview");
//  immagine preview
const preview = document.getElementById("preview");
// icona quando non c'è l immagine
const previewIcon = document.getElementById("icon-preview");
// button che svuota il campo input file
const emptyInput = document.getElementById("empty-input");
// funzione che controlla che sia un img
function isImage(url) {
  return /\.(jpg|jpeg|png|webp|bmp|gif|svg)$/.test(url);
}
// evento al cambio input file per mostrare antemprima img
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
  // caso non sto passando un immagine
  // nascondo la preview e rimetto icona di default
  previewIcon.classList.remove("d-none");
  preview.classList.add("d-none");
});
// click sul button per svuotare campo file e rimuovere eventuale anteprima presente
emptyInput.addEventListener("click", function () {
  // se nell input ho un file che è un immagine svuoto l'anteprima
  if (isImage(inputImg.value)) {
    // nascondo la preview e rimetto icona di default
    preview.classList.add("d-none");
    previewIcon.classList.remove("d-none");
  }
  inputImg.value = "";
});
