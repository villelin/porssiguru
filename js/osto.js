const ostataulukko = document.querySelector(".ostaTaulukko");
let search_query = "";


const ostaEvent = ((event, stock_id) => {
  event.preventDefault();

  const amount = event.target.querySelector('input[name="buy_amount"]').value;

  const data = new FormData();
  data.append('stock_id', stock_id);
  data.append('amount', amount);
  data.append('type', 'buy');

  const settings = { method: 'POST', body: data, cache: 'no-cache', credentials: 'include' };

  fetch('php/transaction.php', settings).then((response) => {
    if (response.status === 200) {
      response.json().then((data) => {
        // TODO: virheilmoitukset
        ostaLista();
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


const ostaLista = (() => {
  const settings = {method: 'POST', credentials: 'include'};

  fetch('php/get_stock_data.php', settings).then((response) => {
    if (response.status === 200) {
      response.json().then((data) => {
        if (data != null) {
          const funds = parseFloat(data.funds);

          let html="";
          data.stock.forEach((item, index) => {
            const company_search = item.company.toLowerCase();
            const search = search_query.toLowerCase();

            if (search == "" || company_search.includes(search)) {
              const stock_id = item.stock_id;
              const symbol = item.symbol;
              const company = item.company;
              let price = parseFloat(item.price);
              price = price.toLocaleString('fi-FI',
                  {style: 'currency', currency: 'EUR'});
              let variety="";
              if (item.variety > 0.0) {
                variety = `<i class="material-icons">arrow_drop_up</i>+${item.variety}%`;
              } else if (item.variety < 0.0) {
                variety = `<i class="material-icons">arrow_drop_down</i>${item.variety}%`;
              }

              let buy_min = 1;
              let buy_max = Math.floor(funds / parseFloat(item.price));

              let form_disable = "";
              if (buy_max == 0) {
                form_disable = "disabled";
              }

              html += `<tr>`;
              html += `<td id="osake">${company}</td>`;
              html += `<td id="hinta">${price}</td>`;
              html += `<td id="muutos">${variety}</td>`;
              html += `<td class="moTd" >`;
              html += `<form id="buyform" method="POST" onsubmit="ostaEvent(event, ${stock_id})">`;
              html += `<input class="moInput1" type="number" name="buy_amount" min="${buy_min}" max="${buy_max}" ${form_disable}>`;
              html += `<input class="moInput2" type="submit" value="OSTA" ${form_disable}>`;
              html += `</form>`;
              html += `</td>`;
              html += `</tr>`;

              //html += `<tr id="topRivi" onclick="openProfile(${id})"><td class="sija">${index + 1}</td><td class="kuva"><img src="${urli}"></td><td class="kayttaja">${name}</td><td class="nettovarat">${assets} </td></tr>`;
            }
          });
          ostataulukko.innerHTML = "<tr class='tableOtsikot'><th>OSAKE</th><th>HINTA/kpl</th><th class='muutosOtsake'>MUUTOS</th><th class='oikealle'>OSTA kpl</th></tr>" + html;
          //toplista.innerHTML = `<tr class="topOtsikko" ><td class="otsikkosija">SIJA</td><td>KÄYTÄJÄ</td><td></td><td class="otsikkonettovarat">NETTOVARAT</td></tr>` + html;
        }
      });
    } else {
      // virhe
    }
  }).catch((error) => {
    // virhe
  });
});

ostaLista();

const searchEvent = (() => {
  const qq = document.querySelector("#searchbox").value;
  search_query = qq;
  ostaLista();
});


document.querySelector("#searchbox").addEventListener('input', searchEvent);