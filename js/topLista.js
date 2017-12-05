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
          data.forEach((item, index) => {
            const id = item.user_id;
            const name = item.username;

            let  assets= parseFloat(item.assets);
            assets = assets.toLocaleString('fi-FI', { style: 'currency', currency: 'EUR' });
            let image = item.image;
            let urli;
            if (image.length == 0){
              urli = "http://www.placecage.com/c/100/100";
            }else{
              urli = "uploads/" + image;
            }

            html += `<tr onclick="openProfile(${id})"><td><img src="${urli}"></td><td>${index + 1}</td><td>${name}</td><td>${assets} </td></tr>`;
          });
          toplista.innerHTML = html;
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


window.setTimeout(topListaus, 60000);