const current_user = document.querySelector("#current_user");
const funds_user = document.querySelector("#funds_user");
const current_user2 = document.querySelector("#current_user2");


const toplista = document.querySelector("#toplista");




const data = new FormData();
data.append('num', '15');

const topListaus = (() => {
  // *** KÄYTTÄJÄN TIEDOT ***

  const settings = {method: 'POST', body: data, cache: 'no-cache', credentials: 'include'};

  fetch('php/leaderboard.php', settings).then((response) => {
    if (response.status === 200) {
      response.json().then((data) => {
        if (data != null) {
        let html="";
          data.forEach((item) => {
            const id = item.user_id;
            const name = item.username;
            const assets = item.assets;

            html +=` <tr><td>d</td><td>ddd</td><td>dddd</td></tr>`
          });
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


topListaus();