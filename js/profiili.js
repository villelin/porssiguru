const  varat= document.querySelector("#varallisuus");
const arvopaperit = document.querySelector("#arvopaperit");

const rank = document.querySelector("#profLuvutA h2");

const updateProfile = (() => {
  // *** KÄYTTÄJÄN TIEDOT ***

  const settings = {method: 'POST', cache: 'no-cache', credentials: 'include'};


  fetch('php/get_user_worth.php', settings).then((response) => {
    if (response.status === 200) {
      response.json().then((data) => {
        /*alert(data.worth);*/

        if (data.worth !== null) {
          const  netWorth= parseFloat(data.worth);
        arvopaperit.innerHTML = netWorth.toLocaleString('fi-FI', { style: 'currency', currency: 'EUR' });
        }



      });
    } else {
      // virhe
      

    }
  }).catch((error) => {
    // virhe
  });


  fetch('php/get_user_rank.php', settings).then((response) => {
    if (response.status === 200) {
      response.json().then((data) => {
        /*alert(data.worth);*/
        if (data.rank !== null) {
          rank.innerHTML = data.rank;
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