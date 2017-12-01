const varat = document.querySelector("#varallisuus");


const updateProfile = (() => {
  // *** KÄYTTÄJÄN TIEDOT ***

  const settings = {method: 'POST', cache: 'no-cache', credentials: 'include'};


  fetch('php/get_user_worth.php', settings).then((response) => {
    if (response.status === 200) {
      response.json().then((data) => {
        alert(data.worth);
        if (data.worth !== null) {
        varat.innerHTML = data.worth;

        }
      });
    } else {
      // virhe
      

    }
  }).catch((error) => {
    // virhe
  });
});

updateProfile();