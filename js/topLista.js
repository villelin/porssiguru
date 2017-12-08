const toplista = document.querySelector("#toplista");



const data = new FormData();
data.append('num', '15');

const topListaus = ((showProfile) => {
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

            if (showProfile) {
              html += `<tr id="topRivi" onclick="openProfile(${id})">`;
            } else {
              html += `<tr id="topRivi">`;
            }
            html += `<td class="sija">${index+1}</td>`;
            html += `<td class="kuva"><img style="width: 60px" src="${urli}"></td>`;
            html += `<td class="kayttaja">${name}</td>`;
            html += `<td class="nettovarat">${assets}</td>`;
            html += `</tr>`;

            //html += `<tr id="topRivi" onclick="openProfile(${id})"><td class="sija">${index + 1}</td><td class="kuva"><img src="${urli}"></td><td class="kayttaja">${name}</td><td class="nettovarat">${assets} </td></tr>`;
          });
          toplista.innerHTML = `<tr class="topOtsikko" ><td class="otsikkosija">SIJA</td><td colspan="2">KÄYTTÄJÄ</td><td class="otsikkonettovarat">NETTOVARAT</td></tr>` + html;
        }

      });
    } else {
      // virhe
    }
  }).catch((error) => {
    // virhe
  });

  window.setTimeout((() => { topListaus(showProfile); }), 60000);
});
