
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

  fetch('php/assets.php', settings).then((response) => {
    if (response.status !== 200) {
      nappi_response.innerHTML = "Ei toimi";
    } else {
      response.json().then((data) => {
        let list = "";
        data.forEach((item) => {
          list += `User ID: ${item.user_id}, Stock ID: ${item.stock_id}, Company: ${item.company}, Price: ${item.price}, Assets: ${item.assets}<br>`;
        });
        nappi_response.innerHTML = list;
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
  const password_verify_element = document.querySelector('input[name="reg_password_verify"]');
  const email_element = document.querySelector('input[name="reg_email"]');

  const username_regex = new RegExp("^[A-Za-z_][A-Za-z0-9_]{3,14}$");
  const email_regex = new RegExp("^\\S+@\\S+\\.\\S+$");

  if (username_regex.exec(username_element.value) == null) {
    // TODO: merkkaa elementti?
    reg_response.innerHTML = "Käyttäjätunnuksessa sallitaan vain aakkoset, numerot ja alaviivat ja se saa olla 4-15 merkkiä pitkä.";
  }
  else if (email_regex.exec(email_element.value) == null) {
    // TODO: merkkaa elementti?
    reg_response.innerHTML = "Sähköpostiosoite ei ole oikeaa muotoa.";
  }
  else if (password_element.value !== password_verify_element.value) {
    reg_response.innerHTML = "Salasanan varmistus ei täsmää";
  }
  else {
    const data = new FormData();
    data.append('username', username_element.value);
    data.append('password', password_element.value);
    data.append('email', email_element.value);

    const settings = {
      method: 'POST',
      body: data,
      cache: 'no-cache',
      credentials: 'include'
    };

    fetch('php/register.php', settings).then((response) => {
      if (response.status === 200) {
        response.json().then((data) => {
          let message;
          if (data.error == true) {
            message = `VIRHE: ${data.message}`;
          } else {
            register_form.reset();
            message = data.message;
          }
          reg_response.innerHTML = message;
        });
      } else {
        reg_response.innerHTML = "Palvelu ei käytössä";
      }
    }).catch((error) => {
      reg_response.innerHTML = "FEILAS PAHASTI";
    });
  }
});


document.querySelector("#nappi").addEventListener('click', nappiTest);
document.querySelector("#logout").addEventListener('click', nappiLogout);
document.querySelector("#login_form").addEventListener('submit', loginSend);
document.querySelector("#register_form").addEventListener('submit', registerSend);


