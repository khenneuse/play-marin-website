function menuToggle() {
  var x = document.getElementById("header-menu-list");
  if (x.style.display === "") {
    x.style.display = "flex";
  } else {
    x.style.display = "";
  }
}

function goToDonationPage() {
  location.href = "https://donate.playmarin.org/944598-holidaydonate";
}

function enableSubmit() {
  let inputs = document.querySelectorAll("input[required]");
  let submitButton = document.querySelector('input[type="submit"]');
  let buttonDisabled = false;
  for (var i = 0; i < inputs.length; i++) {
    let changedInput = inputs[i];
    if (changedInput.value.trim() === "" || changedInput.value === null) {
      buttonDisabled = true;
      break;
    }
  }
  submitButton.disabled = buttonDisabled;
}
