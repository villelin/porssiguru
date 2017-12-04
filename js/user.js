const current_user = document.querySelector("#current_user");
const funds_user = document.querySelector("#funds_user");
//const current_user2 = document.querySelector("#current_user2");
//const since = document.querySelector("#since");


const updateUserInfo = (() => {
  // *** KÄYTTÄJÄN TIEDOT ***

  const settings = {method: 'POST', cache: 'no-cache', credentials: 'include'};

  fetch('php/user_info.php', settings).then((response) => {
    if (response.status === 200) {
      response.json().then((data) => {
        if (data.user_info != null) {
          const username = data.user_info.username;
          const signup = data.user_info.signup_date;
          const funds = parseFloat(data.user_info.funds);


/*
          if (since !== null){
            since.innerHTML = "Rekisteröitynyt: " + signup ;
          }

          if (current_user2 !== null){
            current_user2.innerHTML = username ;
          }*/
          current_user.innerHTML = username ;
          funds_user.innerHTML = funds.toLocaleString('fi-FI', { style: 'currency', currency: 'EUR' });
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