
const login_form = document.querySelector("#login_form");
const register_form = document.querySelector("#register_form");

const response_element = document.querySelector("#response");
const nappi_response = document.querySelector("#nappi_response");
const reg_response = document.querySelector("#reg_response");

const loginSend = ((evt) => {
  evt.preventDefault();

  const username_element = document.querySelector('input[name="log_username"]');
  const password_element = document.querySelector('input[name="log_password"]');

  const data = new FormData();
  data.append('username', username_element.value);
  data.append('password', password_element.value);

  const settings = { method: 'POST', body: data, cache: 'no-cache', credentials: 'include' };

  fetch('php/login.php', settings).then((response) => {
    if (response.status !== 200) {
      response_element.innerHTML = "Palvelu ei käytössä";
    }
    else {
      response.json().then((data) => {
        let message = "";
        if (data.error == true) {
          message += "VIRHE: ";
        }
        message += data.message;
        login_form.reset();
        response_element.innerHTML = message;
      });
    }
  }).catch((error) => {
    response_element.innerHTML = "FEILAS PAHASTI";
  });
});


const nappiTest = ((evt) => {
  evt.preventDefault();

  const settings = { method: 'POST', cache: 'no-cache', credentials: 'include' };

  fetch('php/test.php', settings).then((response) => {
    if (response.status !== 200) {
      nappi_response.innerHTML = "Ei toimi";
    } else {
      response.json().then((data) => {
        let message = "";
        if (data.error == true) {
          message += "VIRHE: ";
        }
        message += data.message;
        nappi_response.innerHTML = message;
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
        let message = "";
        if (data.error == true) {
          message += "VIRHE: ";
        }
        message += data.message;
        response_element.innerHTML = message;
      });
    }
  }).catch((error) => {
    response_element.innerHTML = "NYT FEILAS PAHASTI";
  });
});


const registerSend = ((evt) => {
  evt.preventDefault();

  const username_element = document.querySelector('input[name="reg_username"]');
  const password_element = document.querySelector('input[name="reg_password"]');
  const email_element = document.querySelector('input[name="reg_email"]');

  const data = new FormData();
  data.append('username', username_element.value);
  data.append('password', password_element.value);
  data.append('email', email_element.value);

  const settings = { method: 'POST', body: data, cache: 'no-cache', credentials: 'include' };

  fetch('php/register.php', settings).then((response) => {
    if (response.status !== 200) {
      reg_response.innerHTML = "Palvelu ei käytössä";
    }
    else {
      response.json().then((data) => {
        let message = "";
        if (data.error == true) {
          message += "VIRHE: ";
        }
        message += data.message;
        register_form.reset();
        reg_response.innerHTML = message;
      });
    }
  }).catch((error) => {
    reg_response.innerHTML = "FEILAS PAHASTI";
  });
});


document.querySelector("#nappi").addEventListener('click', nappiTest);
document.querySelector("#logout").addEventListener('click', nappiLogout);
document.querySelector("#login_form").addEventListener('submit', loginSend);
document.querySelector("#register_form").addEventListener('submit', registerSend);


