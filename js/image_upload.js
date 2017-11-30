const upload_form = document.querySelector("#upload_form");
const profile_image = document.querySelector("#profile_img");
const image_response = document.querySelector("#image_response");

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
      profile_image.src = uri;
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
          let message = "";
          if (data.error == true) {
            message += "VIRHE: ";
          }
          message += data.message;
          image_response.innerHTML = message;
        });
      } else {
        image_response.innerHTML = "Palvelu ei käytössä";
      }
    }).catch((error) => {
      image_response.innerHTML = "FEILAS PAHASTI";
    });
  } else {
    image_response.innerHTML = "Kuvaa ei valittu.";
  }
});


upload_form.addEventListener('submit', uploadEvent);
document.querySelector("#change_image").addEventListener('click', imageChange);