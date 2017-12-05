
const like_form = document.querySelector("#like_form");
const testlike_form = document.querySelector("#testlike_form");
const testlike_response = document.querySelector("#testlike_response");

const likes_element = document.querySelector("#user_likes");


const openProfile = ((id) => {
  const modaali = document.getElementById('ProfModal');
  modaali.style.display = "block";

  const data = new FormData();
  data.append('user_id', id);

  const settings = {
    method: 'POST',
    body: data,
    cache: 'no-cache',
    credentials: 'include'
  };

  if (modaali != null) {
    fetch('php/get_guest_profile.php', settings).then((response) => {
      if (response.status === 200) {
        response.json().then((data) => {
          const profiilinimi = document.querySelector("#profiilinimi");
          const profiilirek = document.querySelector("#profiilirek");
          const arvopaperit = document.querySelector("#arvopaperit");
          const rank = document.querySelector("#rankki");
          const likes = document.querySelector("#user_likes");
          const kommentit = document.querySelector("#user_comments");
          const usertext = document.querySelector("#omateksti");
          const profiilikuva = document.querySelector("#profiilikuva");
          const likeicon = document.querySelector("#likeicon");

          profiilinimi.innerHTML = data.username;
          profiilirek.innerHTML = "Rekisteröitynyt: " + data.signup;
          rank.innerHTML = data.rank;
          usertext.innerHTML = data.description;
          arvopaperit.innerHTML = parseFloat(data.worth).
              toLocaleString('fi-FI', {style: 'currency', currency: 'EUR'});


          if (data.is_liked) {
            likeicon.style.display = "none";
            likes.innerHTML = `${data.likes}`;
            likes.style.display = "block";
          } else {
            likeicon.style.display = "inline-block";
            likes.innerHTML = "";
            likes.style.display = "none";
          }

          let imageurl;
          if (data.image.length == 0) {
            imageurl = "http://placecage.com/c/100/100";
          } else {
            imageurl = "uploads/" + data.image;
          }

          profiilikuva.src = imageurl;

          let comments = "";
          data.comments.forEach((item) => {
            comments +=`<strong>${item.username}:</strong> ${item.text} -  ${item.date}<br>`;
          });
          kommentit.innerHTML = comments;
        });
      } else {
        // virhe
      }
    }).catch((error) => {
      // virhe
    });
  }
});



// *** KÄYTTÄJÄSTÄ TYKKÄYKSET ***
  /*

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
  */