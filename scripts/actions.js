function menuToggle() {
  var x = document.getElementById("header-menu-list");
  if (x.style.display === "") {
    x.style.display = "flex";
  } else {
    x.style.display = "";
  }
}

function goToDonationPage() {
  location.href='donate.html';
}
