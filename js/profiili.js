const  varat= document.querySelector("#varallisuus");
const arvopaperit = document.querySelector("#arvopaperit");
const profiilinimi = document.querySelector("#current_user2");
const since = document.querySelector("#since");
const usertext = document.querySelector("#usertext");
const profiilikuva = document.querySelector("#pKuva");

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
      });
    } else {
      // virhe
    }
  }).catch((error) => {
    // virhe
  });
})

updateProfile();