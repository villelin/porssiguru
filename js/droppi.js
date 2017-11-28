

/* When the user clicks on the button,
toggle between hiding and showing the dropdown content */
function myFunction() {
  document.getElementById("myDropdown").classList.toggle("show");
}


// Get the modal
const modal = document.getElementById('myModal');
const modalRek = document.getElementById('myModalRek');

// Get the button that opens the modal
const btn = document.getElementById('login');
const rekLinkki = document.getElementById('rek');

// Get the <span> element that closes the modal
const span = document.getElementsByClassName("close")[0];



// Modaali poppaa esiin kun painiketta painetaan
btn.onclick = function() {
  document.getElementById("myDropdown").classList.remove("show");
  modal.style.display = "block";
}
rekLinkki.onclick = function() {
  modal.style.display = "none";
  modalRek.style.display = "block";
}

// Kun klikataan modalin <span> (x), modali sulkeutuu
span.onclick = function() {
  modal.style.display = "none";
  modalRek.style.display = "none"; //EI TOIMI
}

// Kun klikataan muualle kuin modaliin tai droppiin em. osat sulkeutuu EI TOIMIIIIIIIIIIIIIIIIIII**********************
window.onclick = function(event) {
      if (event.target ==  modal  ) {
        modal.style.display = "none";


      }
  if (event.target ==  modalRek ) {

    modalRek.style.display = "none";

  }
      if (!event.target.matches('.dropbtn')) {
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



