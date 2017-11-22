const response_element = document.querySelector("#response");

const loginSend = ((evt) => {
  evt.preventDefault();

  const username_element = document.querySelector('input[name="log_username"]');
  const password_element = document.querySelector('input[name="log_password"]');

  const data = new FormData();
  data.append('username', username_element.value);
  data.append('password', password_element.value);

  const settings = { method: 'POST', body: data, cache: 'no-cache', credentials: 'include' };

  fetch('php/register.php', settings).then((response) => {
    if (response.status !== 200) {
      response_element.innerHTML = "Ei toimi";
    }
    else {
      response.json().then((data) => {
        response_element.innerHTML = data;
      });
    }
  }).catch((error) => {
    response_element.innerHTML = "FEILAS PAHASTI";
  });
});

const nappi_response = document.querySelector("#nappi_response");
const nappiTest = ((evt) => {
  evt.preventDefault();

  const settings = { method: 'POST', cache: 'no-cache', credentials: 'include' };

  fetch('php/test.php', settings).then((response) => {
    if (response.status !== 200) {
      nappi_response.innerHTML = "Ei toimi";
    } else {
      response.json().then((data) => {
        nappi_response.innerHTML = data.result;
      });
    }
  }).catch((error) => {
    nappi_response.innerHTML = "NYT FEILAS PAHASTI";
  });
});

const nappiLogout = ((evt) => {
  evt.preventDefault();

  const settings = { method: 'POST', cache: 'no-cache', credentials: 'include' };

  fetch('php/logout.php', settings).then((response) => {
    if (response.status !== 200) {
      response_element.innerHTML = "Ei toimi";
    } else {
      response.json().then((data) => {
        response_element.innerHTML = data.result;
      });
    }
  }).catch((error) => {
    response_element.innerHTML = "NYT FEILAS PAHASTI";
  });
});


document.querySelector("#nappi").addEventListener('click', nappiTest);
document.querySelector("#logout").addEventListener('click', nappiLogout);
document.querySelector("form").addEventListener('submit', loginSend);


