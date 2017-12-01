const current_user = document.querySelector("#current_user");
const funds_user = document.querySelector("#funds_user");


const updateUserInfo = (() => {
  // *** KÄYTTÄJÄN TIEDOT ***
alert('kskkssksk');
  const settings = {method: 'POST', cache: 'no-cache', credentials: 'include'};

  fetch('php/user_info.php', settings).then((response) => {
    if (response.status === 200) {
      response.json().then((data) => {
        if (data.user_info != null) {
          const username = data.user_info.username;

          const funds = data.user_info.funds;
          current_user.innerHTML = username ;
          funds_user.innerHTML = parseFloat(funds) + "€";
        }
      });
    } else {
      // virhe
      current_user.innerHTML = "";
    }
  }).catch((error) => {
    // virhe
  });
});


window.onload=updateUserInfo;