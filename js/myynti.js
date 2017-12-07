const myylista = document.querySelector("#myylista");


const myyEvent = ((event, stock_id) => {
  event.preventDefault();

  const amount = event.target.querySelector('input[name="sell_amount"]').value;

  const data = new FormData();
  data.append('stock_id', stock_id);
  data.append('amount', amount);
  data.append('type', 'sell');

  const settings = { method: 'POST', body: data, cache: 'no-cache', credentials: 'include' };

  fetch('php/transaction.php', settings).then((response) => {
    if (response.status === 200) {
      response.json().then((data) => {
        // TODO: virheilmoitukset
        myyLista();
        // päivitä käyttäjän rahan näyttö
        updateUserInfo();
      });
    } else {
      // virhe
    }
  }).catch((error) => {
    // virhe
  });
});


const myyLista = (() => {
  const settings = {method: 'POST', credentials: 'include'};

  fetch('php/assets.php', settings).then((response) => {
    if (response.status === 200) {
      response.json().then((data) => {
        if (data != null) {
          const funds = data.funds;
          let html="";
          if (data.stock != null) {
            data.stock.forEach((item, index) => {
              const stock_id = item.stock_id;
              const company = item.company;
              let price = parseFloat(item.price);
              price = price.toLocaleString('fi-FI',
                  {style: 'currency', currency: 'EUR'});
              const amount = item.assets;

              const sell_min = 1;
              const sell_max = amount;

              html += `<tr>`;
              html += `<td id="osake">${company}</td>`;
              html += `<td id="hinta">${price}</td>`;
              html += `<td id="maara">${amount}</td>`;
              html += `<td>`;
              html += `<form id="sellform" method="POST" onsubmit="myyEvent(event, ${stock_id})">`;
              html += `<input type="number" name="sell_amount" min="${sell_min}" max="${sell_max}">`;
              html += `<input type="submit" value="Myy">`;
              html += `</form>`;
              html += `</td>`;
              html += `</tr>`;

              //html += `<tr id="topRivi" onclick="openProfile(${id})"><td class="sija">${index + 1}</td><td class="kuva"><img src="${urli}"></td><td class="kayttaja">${name}</td><td class="nettovarat">${assets} </td></tr>`;
            });
            myylista.innerHTML = "<tr><th>Osake</th><th>Hinta/kpl</th><th>Määrä</th><th>Myy kpl</th></tr>" +
                html;
            //toplista.innerHTML = `<tr class="topOtsikko" ><td class="otsikkosija">SIJA</td><td>KÄYTÄJÄ</td><td></td><td class="otsikkonettovarat">NETTOVARAT</td></tr>` + html;
          } else {
            myylista.innerHTML = "Et omista tällä hetkellä mitään.";
          }
        }
      });
    } else {
      // virhe
    }
  }).catch((error) => {
    // virhe
  });
});

myyLista();