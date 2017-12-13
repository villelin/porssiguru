const logStatus = document.querySelector("#logStatus");

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
      logStatus.innerHTML = "Palvelu ei käytössä";
    }
    else {
      response.json().then((data) => {
        let message = "";
        if (data.error == true) {
          message += "VIRHE: ";
          message += data.message;
/*          login_form.reset();*/
          logStatus.innerHTML = message;
          //current_user.innerHTML = `Käyttäjä: ${data.username}`;
        } else {
          window.location.replace('top.html');
        }

      });
    }
  }).catch((error) => {
    logStatus.innerHTML = "FEILAS PAHASTI";
  });
});

document.querySelector("#login_form").addEventListener('submit', loginSend);