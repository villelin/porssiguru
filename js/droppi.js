

/* When the user clicks on the button,
toggle between hiding and showing the dropdown content */
myFunction = () => {
  document.getElementById("myDropdown").classList.toggle("show");
};


// Get the modal
const modal = document.getElementById('myModal');
const modalRek = document.getElementById('myModalRek');

// Get the button that opens the modal
const btn = document.getElementById('login');
const btn2 = document.getElementById('logout');

const rekLinkki = document.getElementById('rek');


// Get the <span> element that closes the modal
const span = document.getElementsByClassName("close")[0];


// Modaali poppaa esiin kun painiketta painetaan
btn.onclick = () => {
  document.getElementById("myDropdown").classList.remove("show");
  modal.style.display = "block";
};

btn2.onclick = () => {
  document.getElementById("myDropdown").classList.remove("show");
  modalRek.style.display = "block";
};

rekLinkki.onclick = () => {
  modal.style.display = "none";
  modalRek.style.display = "block";
};

// Kun klikataan modalin <span> (x), modali sulkeutuu
span.onclick = () => {
  modal.style.display = "none";

};



// Kun klikataan muualle kuin modaliin tai droppiin em. osat sulkeutuu
window.onclick = (event) => {
  if (event.target ===  modal  ) {
    modal.style.display = "none";

  }
  if (event.target ===  modalRek ) {

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









