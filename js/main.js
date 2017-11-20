const response_element = document.querySelector("#response");

document.querySelector("form").addEventListener('submit', (evt) => {
  evt.preventDefault();

  const username_element = document.querySelector('input[name="username"]');
  const password_element = document.querySelector('input[name="password"]');

  alert(`User: ${username_element.value} Pass: ${password_element.value}`);

  const data = new FormData();
  data.append('username', username_element.value);
  data.append('password', password_element.value);

  const settings = { method: 'POST', body: data };

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