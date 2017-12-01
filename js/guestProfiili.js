
const like_form = document.querySelector("#like_form");
const testlike_form = document.querySelector("#testlike_form");
const testlike_response = document.querySelector("#testlike_response");

const likes_element = document.querySelector("#user_likes");



// *** KÄYTTÄJÄSTÄ TYKKÄYKSET ***

const likes_data = new FormData();
// TODO: tänne käyttäjä jonka tykkäykset halutaan
//likes_data.append('liked_id', 'USER_ID');

const likes_settings = { method: 'POST', body: likes_data, cache: 'no-cache', credentials: 'include' };

fetch('php/get_user_likes.php', likes_settings).then((response) => {
  if (response.status === 200) {
    response.json().then((data) => {


      if (data.likes != null) {
        likes_element.innerHTML = data.likes;
      }


    });
  } else {
    // virhe
    likes_element.innerHTML = "";
  }
}).catch((error) => {
  // virhe
  likes_element.innerHTML = "";
});



const likeSend = ((evt) => {
  evt.preventDefault();

  const liked_element = document.querySelector('input[name="liked_id"]');

  const data = new FormData();
  data.append('liked_id', liked_element.value);

  const settings = { method: 'POST', body: data, cache: 'no-cache', credentials: 'include' };

  fetch('php/like_user.php', settings).then((response) => {
    if (response.status === 200) {
      response.json().then((data) => {
        let message = "";
        if (data.error == true) {
          message += "VIRHE: ";
        }
        message += data.message;
        like_response.innerHTML = message;
      });
    } else {
      like_response.innerHTML = "Palvelu ei käytössä";
    }
  }).catch((error) => {
    like_response.innerHTML = "FEILAS PAHASTI";
  });
});


document.querySelector("#like_form").addEventListener('submit', likeSend);