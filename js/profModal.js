
// PROFIILI MODAALI

// Get the modal
const proModaali = document.getElementById('ProfModal');

// Get the button that opens the modal
const btnP = document.getElementById('hloPro');

// Get the <span> element that closes the modal
const span = document.getElementsByClassName('close')[0];

// When the user clicks the button, open the modal
btnP.onclick = () => {
  proModaali.style.display = "block";
};

// When the user clicks on <span> (x), close the modal
span.onclick = () => {
  proModaali.style.display = "none";
};

// When the user clicks anywhere outside of the modal, close it
window.onclick = (event) => {
  if (event.target === modaali) {
    proModaali.style.display = "none";
  }

};