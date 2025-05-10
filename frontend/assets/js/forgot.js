var verify = document.getElementById("verify");
var change_password = document.getElementById("change_password");

function toVerify() {
  // Moblie
  verify.classList.add("show");
  change_password.classList.remove("show");
  // Desktop
  verify.style.left = "4px";
  change_password.style.right = "-520px";
  verify.style.opacity = 1;
  change_password.style.opacity = 0;
}

function toReset() {
  // Moblie
  verify.classList.remove("show");
  change_password.classList.add("show");
  // Desktop
  verify.style.left = "-510px";
  change_password.style.right = "5px";
  verify.style.opacity = 0;
  change_password.style.opacity = 1;
}

$("#verify").on("submit", function (e) {
  e.preventDefault();

  const input_email = $("#email").val().trim();

  // Initialize api
  $.ajax({
    url: "../api/check_email.php",
    method: "POST",
    dataType: "json",
    data: { email: input_email },
    success: function (response) {
      if (response.status === "success") {
        // console.log("Success verified, switching form...");
        localStorage.setItem("reset_email", input_email);
        $("#reset-email").val(input_email);
        toReset();
      } else {
        Swal.fire({
          icon: response.status === "error" ? "error" : "info",
          title: "Error",
          text: response.message,
        });
      }
    },
    error: function (xhr, status, response) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Cannot connect to server !!!",
      });
    },
  });
});

$("#change_password").on("submit", function (e) {
  e.preventDefault();

  const email = $("#reset-email").val().trim();
  const new_password = $("#new-password").val().trim();
  const confirm_password = $("#confirm-password").val().trim();

  $.ajax({
    url: "../api/reset_password.php",
    method: "POST",
    dataType: "json",
    data: {
      email: email,
      "new-password": new_password,
      "confirm-password": confirm_password,
    },
    success: function (response) {
      if (response.status == "success") {
        Swal.fire({
          icon: "success",
          title: "Success",
          text: response.message,
          timer: 2000,
          showConfirmButton: false,
        }).then(() => {
          window.location.href = "../index.php";
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: response.message,
        });
      }
    },
    error: function (xhr, status, response) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Cannot connect to server !!!",
      });
    },
  });
});

$(document).ready(function () {
  const savedEmail = localStorage.getItem("reset_email");
  if (savedEmail) {
    $("#reset-email").val(savedEmail);
  }
});
