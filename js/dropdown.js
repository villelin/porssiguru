myFunction = () => {
  document.getElementById("myDropdown").classList.toggle("show");
};


// When the user clicks anywhere outside of the modal, close it
window.onclick = (event) => {
    if (!event.target.matches('#dropbtn')) {
    const dropdowns = document.getElementsByClassName("dropdown-content");
    let i;
    for (i = 0; i < dropdowns.length; i++) {
      const openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
      if (event.target === modaali) {
        modaali.style.display = "none";
      }
    }
  }
};