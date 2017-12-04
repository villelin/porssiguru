


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
            let image = item.image;
            let urli;
            if (image == null){
              urli = "http://www.placecage.com/c/100/100";
            }else{
              urli = "uploads/" + image;
            }

            html += `<tr><td><img src="${urli}"></td><td>ddd</td><td>dddd</td></tr>`;
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