const nappi_response = document.querySelector("#nappi_response");
const response_element = document.querySelector("#response");

const nappiLogout = ((evt) => {
  evt.preventDefault();

  const settings = { method: 'POST', cache: 'no-cache', credentials: 'include' };

  fetch('php/logout.php', settings).then((response) => {
    if (response.status !== 200) {
      response_element.innerHTML = "Ei toimi";
    } else {
      response.json().then((data) => {

        window.location.replace('index.html');
      });
    }
  }).catch((error) => {
    response_element.innerHTML = "NYT FEILAS PAHASTI";
  });
});


document.querySelector("#logout").addEventListener('click', nappiLogout);