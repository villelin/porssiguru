const register_form = document.querySelector("#register_form");
const reg_response = document.querySelector("#reg_response");


const isEmpty = ((field) => {
  if (field.length > 0) {
    return true;
  } else {
    return false;
  }
});

const registerSend = ((evt) => {
  evt.preventDefault();

  const username_element = document.querySelector('input[name="reg_username"]');
  const password_element = document.querySelector('input[name="reg_password"]');
  const password_verify_element = document.querySelector('input[name="reg_password_verify"]');


  const username_regex = new RegExp("^[A-Za-z_][A-Za-z0-9_]{3,14}$");


  let valid = true;

  let valid_message = "";

  if (!isEmpty(username_element.value) && username_regex.exec(username_element.value) == null) {
    // TODO: merkkaa elementti?
    valid_message += `Käyttäjätunnuksessa sallitaan vain aakkoset, numerot ja alaviivat ja se saa olla 4-15 merkkiä pitkä.<br>`;
    valid = false;
  }
  if (!isEmpty(password_element.value)) {
    valid_message += `Salasana on tyhjä.<br>`;
    valid = false;
  }
  if (!isEmpty(password_verify_element.value)) {
    valid_message += `Salasanan varmistus on tyhjä.<br>`;
    valid = false;
  }
  if (password_element.value !== password_verify_element.value) {
    valid_message += `Salasanan varmistus ei täsmää.<br>`;
    valid = false;
  }
  if (valid) {
    const data = new FormData();
    data.append('username', username_element.value);
    data.append('password', password_element.value);

    const settings = {
      method: 'POST',
      body: data,
      cache: 'no-cache',
      credentials: 'include'
    };
/*alert ('asdasdasd');*/
    fetch('php/register.php', settings).then((response) => {
      if (response.status === 200) {
        response.json().then((data) => {
          let message='';
          if (data.error == true) {
            message += `VIRHE: ${data.message}`;
          } else {
            register_form.reset();
            message = data.message;
          }
          valid_message += `${message}<br>`;
        });
      } else {
        valid_message += `Palvelu ei käytössä<br>`;
      }
    }).catch((error) => {
      valid_message += `FEILAS PAHASTI`;
    });
  }
  reg_response.innerHTML = valid_message;
});

document.querySelector("#register_form").addEventListener('submit', registerSend);