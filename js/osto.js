const buy_form = document.querySelector("#buy_form");
const buy_response = document.querySelector("#buy_response");


const buySend = ((evt) => {
  evt.preventDefault();

  const stockid_element = document.querySelector('input[name="buy_stock_id"]');
  const amount_element = document.querySelector('input[name="buy_amount"]');

  const data = new FormData();
  data.append('stock_id', stockid_element.value);
  data.append('amount', amount_element.value);
  data.append('type', 'buy');

  const settings = { method: 'POST', body: data, cache: 'no-cache', credentials: 'include' };

  fetch('php/transaction.php', settings).then((response) => {

    if (response.status === 200) {
      response.json().then((data) => {
        let message = "";
        if (data.error == true) {



          message += "VIRHE: ";
        }

        updateUserInfo();
        message += data.message;
        buy_response.innerHTML = message;
      });
    } else {
      buy_response.innerHTML = "Palvelu ei käytössä";
    }
  }).catch((error) => {
    buy_response.innerHTML = "FEILAS PAHASTI";
  });
});




  let html="";
  data.forEach((item) => {
    const stock = item.stock_id;

    let price = parseFloat(item.price);
    price = price.toLocaleString('fi-FI', {style: 'currency', currency: 'EUR'});

    html += `<form method="post" id="buy_form"><tr><td>eka</td><td>${stock}</td><td>${price}</td><td><input type="number" name="buy_amount"></td><td><input type="submit" value="Submit"></td></tr></form>`;
  });




document.querySelector("#buy_form").addEventListener('submit', buySend);
