//const modaali = document.getElementById('ProfModal');

const like_form = document.querySelector("#like_form");
const testlike_form = document.querySelector("#testlike_form");
const testlike_response = document.querySelector("#testlike_response");

const likes_element = document.querySelector("#user_likes");
const modaali = document.getElementById('ProfModal');
const span = document.getElementsByClassName('close')[0];

span.onclick = () => {
  modaali.style.display = "none";
};



const openProfile = ((id) => {

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

          modaali.setAttribute('modal_user_id', id);

          profiilinimi.innerHTML = data.username;
          profiilirek.innerHTML = "Rekisteröitynyt: " + data.signup;
          rank.innerHTML = data.rank;
          usertext.innerHTML = data.description;
          arvopaperit.innerHTML = parseFloat(data.worth).
              toLocaleString('fi-FI', {style: 'currency', currency: 'EUR'});


          if (data.is_liked) {
            likeicon.style.color = "#C49F66";
          } else {
            likeicon.style.color = "black";
          }
          likes.innerHTML = `${data.likes}`;

          let imageurl;
          if (data.image.length == 0) {
            imageurl = "http://placecage.com/c/100/100";
          } else {
            imageurl = "uploads/" + data.image;
          }

          profiilikuva.src = imageurl;

          let comments = "";
          comments += "<table class='profTable'>";
          data.comments.forEach((item) => {
            comments += "<tr>";
            comments +=`<td class="profTd"><strong id="kommenttiNimi">${item.username}:</strong></td><td id="kommenttiText" class="profTd">${item.text}</td><td class="profTd">${item.date}</td>`;
            comments += "</tr>";
          });
          comments += "</table>";
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


const tykkaa = ((evt) => {
  evt.preventDefault();

  const user_id = modaali.getAttribute("modal_user_id");

  const data = new FormData();
  data.append('liked_id', user_id);

  const settings = { method: 'POST', body: data, cache: 'no-cache', credentials: 'include' };

  fetch('php/like_user.php', settings).then((response) => {
    if (response.status === 200) {
      response.json().then((data) => {
        openProfile(user_id);
      });
    } else {
      like_response.innerHTML = "Palvelu ei käytössä";
    }
  }).catch((error) => {
    like_response.innerHTML = "FEILAS PAHASTI";
  });
});



const kommentoi = ((evt) => {
  evt.preventDefault();

  const comment_text = document.querySelector("#comment_text");
  const user_id = modaali.getAttribute("modal_user_id");

  const data = new FormData();
  data.append('commented_id', user_id);
  data.append('comment', comment_text.value);

  const settings = { method: 'POST', body: data, cache: 'no-cache', credentials: 'include' };

  fetch('php/add_comment.php', settings).then((response) => {
    if (response.status === 200) {
      response.json().then((data) => {
        // päivitetään profiili
        openProfile(user_id);
        document.querySelector("#comment_form").reset();
      });
    } else {
      // virhe
    }
  }).catch((error) => {
    // virhe
  });
});

document.querySelector("#comment_form").addEventListener('submit', kommentoi);
document.querySelector("#likeicon").addEventListener('click', tykkaa);