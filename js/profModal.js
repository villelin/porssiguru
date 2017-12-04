

myFunction = () => {
  document.getElementById("myDropdown").classList.toggle("show");
};


// PROFIILI MODAALI

// Get the modal
const modaali = document.getElementById('ProfModal');

// Get the button that opens the modal
const btnP = document.getElementById("hloPro");

// Get the <span> element that closes the modal
const span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal
btnP.onclick = () => {
  modaali.style.display = "block";
};

// When the user clicks on <span> (x), close the modal
span.onclick = () => {
  modaali.style.display = "none";
};

// When the user clicks anywhere outside of the modal, close it
window.onclick = (event) => {
  if (event.target === modaali) {
    modaali.style.display = "none";
  }
  if (!event.target.matches('#dropbtn')) {
    const dropdowns = document.getElementsByClassName("dropdown-content");
    let i;
    for (i = 0; i < dropdowns.length; i++) {
      const openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
};