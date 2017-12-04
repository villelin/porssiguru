const  varat= document.querySelector("#varallisuus");
const arvopaperit = document.querySelector("#arvopaperit");
const profiilinimi = document.querySelector("#current_user2");
const since = document.querySelector("#since");
const usertext = document.querySelector("#usertext");
const profiilikuva = document.querySelector("#pKuva");
const ostohistoria = document.querySelector("#buy_history");
const myyntihistoria = document.querySelector("#sell_history");
const kommentit = document.querySelector("#profKommentit p");

const rank = document.querySelector("#profLuvutA h2");

const updateProfile = (() => {
  // *** KÄYTTÄJÄN TIEDOT ***

  const settings = {method: 'POST', credentials: 'include'};

  fetch('php/get_user_profile.php', settings).then((response) => {
    if (response.status === 200) {
      response.json().then((data) => {
        profiilinimi.innerHTML = data.username;
        since.innerHTML = "Rekisteröitynyt: " + data.signup;
        rank.innerHTML = data.rank;
        usertext.innerHTML = data.description;
        arvopaperit.innerHTML = parseFloat(data.worth).toLocaleString('fi-FI', { style: 'currency', currency: 'EUR' });

        let imageurl;
        if (data.image.length == 0) {
          imageurl = "http://placecage.com/c/100/100";
        } else {
          imageurl = "uploads/" + data.image;
        }

        profiilikuva.src = imageurl;

        let comments = "";
        data.comments.forEach((item) => {
          comments += `${item.commenter_id}, ${item.username}, ${item.text}, ${item.date}<br>`;
        });
        kommentit.innerHTML = comments;

        let buy_history = "";
        data.buy_history.forEach((item) => {
          buy_history += `${item.company}, ${item.amount}, ${item.date}<br>`;
        });
        ostohistoria.innerHTML = buy_history;

        let sell_history = "";
        data.sell_history.forEach((item) => {
          sell_history += `${item.company}, ${item.amount}, ${item.date}<br>`;
        });
        myyntihistoria.innerHTML = sell_history;
      });
    } else {
      // virhe
    }
  }).catch((error) => {
    // virhe
  });
})

updateProfile();