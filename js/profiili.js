const  varat= document.querySelector("#varallisuus");
const arvopaperit = document.querySelector("#arvopaperit");
const profiilinimi = document.querySelector("#current_user2");
const since = document.querySelector("#since");
const usertext = document.querySelector("#usertext");
const profiilikuva = document.querySelector("#pKuva");
const ostohistoria = document.querySelector("#buy_history");
const myyntihistoria = document.querySelector("#sell_history");
const kommentit = document.querySelector("#profKommentit p");
const likes_element = document.querySelector("#user_likes");

const rank = document.querySelector("#profLuvutA h2");

const vaihtokuva = document.querySelector("#vaihtokuva");

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
        likes_element.innerHTML = data.likes;

        arvopaperit.innerHTML = parseFloat(data.worth).toLocaleString('fi-FI', { style: 'currency', currency: 'EUR' });

        let imageurl;
        if (data.image.length == 0) {
          imageurl = "http://placecage.com/c/100/100";
        } else {
          imageurl = "uploads/" + data.image;
        }

        profiilikuva.src = imageurl;

        // kuvan vaihdon kuva
        vaihtokuva.src = imageurl;

        let comments = "";
        comments += "<table class='profTable'>";
        data.comments.forEach((item) => {
          comments += "<tr>";
          comments += `<td class="profTd"><strong>${item.username}</strong></td><td class="profTd" >${item.text}</td><td class="profTd">${item.date}</td>`;
          comments += "</tr>";
        });
        comments += "</table>";
        kommentit.innerHTML = comments;

        let buy_history = "";
        buy_history += "<table>";
        buy_history += "<caption><strong>OSTOT</strong></caption>"
        data.buy_history.forEach((item) => {
          buy_history += "<tr>";
          buy_history += `<td>${item.company}</td><td>${item.amount} kpl</td><td>${item.date}</td>`;
          buy_history += "</tr>";
        });
        buy_history += "</table>";
        ostohistoria.innerHTML = buy_history;

        let sell_history = "";
        sell_history += "<table>";
        sell_history += "<caption><strong>MYYNNIT</strong></caption>"
        data.sell_history.forEach((item) => {
          sell_history += "<tr>";
          sell_history += `<td>${item.company}</td><td>${item.amount} kpl</td><td>${item.date}</td>`;
          sell_history += "</tr>";
        });
        sell_history += "</table>";
        myyntihistoria.innerHTML = sell_history;
      });
    } else {
      // virhe
    }
  }).catch((error) => {
    // virhe
  });
});



let image_chosen = null;

const uploadEvent = ((event) => {
  event.preventDefault();

  const file_element = document.querySelector('input[name="file_upload"]');

  if (file_element.files && file_element.files[0]) {
    let width;
    let height;
    let fileSize;
    const reader = new FileReader();
    reader.addEventListener('load', (event) => {
      image_chosen = reader.result;
      const uri = event.target.result;
      vaihtokuva.src = uri;
    });
    reader.addEventListener('error', (event) => {
      console.error(`Ei pystyny lataa - koodi ${event.target.error.code}`);
      image_chosen = null;
    });

    reader.readAsDataURL(file_element.files[0]);
  }
});

const imageChange = ((event) => {
  event.preventDefault();

  if (image_chosen !== null) {
    const data = new FormData();
    data.append('image', image_chosen);

    const settings = { method: 'POST', body: data, cache: 'no-cache', credentials: 'include' };

    fetch('php/image_upload.php', settings).then((response) => {
      if (response.status === 200) {
        response.json().then((data) => {
          // päivitä sivu, että uusi profiilikuva päivittyy
          // Tietokanta on varmaan valmis sekunnin päästä. Ehkä.
          window.setTimeout(() => { window.location.reload(true); }, 1000);
        });
      } else {
        // virhe
      }
    }).catch((error) => {
      // virhe
    });
  } else {
    // virhe
  }
});

updateProfile();

document.querySelector("#upload_form").addEventListener('submit', uploadEvent);
document.querySelector("#change_image").addEventListener('click', imageChange);