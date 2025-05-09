var login = document.getElementById("login");
var register = document.getElementById("register");

function toLogin() {
  // Moblie
  login.classList.add("show");
  register.classList.remove("show");
  // Desktop
  login.style.left = "4px";
  register.style.right = "-520px";
  login.style.opacity = 1;
  register.style.opacity = 0;
}

function toRegister() {
  // Moblie
  login.classList.remove("show");
  register.classList.add("show");
  // Desktop
  login.style.left = "-510px";
  register.style.right = "5px";
  login.style.opacity = 0;
  register.style.opacity = 1;
}

$(document).ready(function () {
  function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
  }

  $("#login").on("submit", function (e) {
    e.preventDefault();

    const email = $("#email").val().trim();
    const password = $("#password").val().trim();
    const remember = $("#login-check").is(":checked") ? 1 : 0;

    if (!email || !password) {
      Swal.fire({
        icon: "warning",
        title: "Missing Info",
        text: "Please fill up the form.",
      });

      return;
    }

    $.ajax({
      url: "api/login_process.php",
      method: "POST",
      dataType: "json",
      data: {
        email: email,
        password: password,
        "remember-me": remember,
      },
      success: function (response) {
        if (response.status === "success") {
          Swal.fire({
            icon: "success",
            title: "Login Successful",
            text: "Redirecting to dashboard...",
            showConfirmButton: false,
            timer: 1500,
          }).then(() => {
            window.location.href = "pages/dashboard.php";
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Login Failed",
            text: response.message,
          });
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", status, error);
        console.error("Response Text:", xhr.responseText);

        Swal.fire({
          icon: "error",
          title: "AJAX Error",
          html: `<b>Status:</b> ${status}<br><b>Error:</b> ${error}`,
        });
      },
    });
  });

  $("#register").on("submit", function (e) {
    e.preventDefault();

    const $submitBtn = $(this).find(".submit");
    $submitBtn.prop("disabled", true).val("Processing...");

    const firstname = $("#firstname").val().trim();
    const lastname = $("#lastname").val().trim();
    const email = $("#new-email").val().trim();
    const username = $("#new-username").val().trim();
    const password = $("#new-password").val().trim();
    const confirm_password = $("#confirm-password").val().trim();
    const remember = $("#register-check").is(":checked") ? 1 : 0;

    if (
      !firstname ||
      !lastname ||
      !email ||
      !username ||
      !password ||
      !confirm_password
    ) {
      Swal.fire({
        icon: "warning",
        title: "Missing Info",
        text: "Please fill up the form.",
      });
      $submitBtn.prop("disabled", false).val("Register");
      return;
    } else if (!validateEmail(email)) {
      Swal.fire({
        icon: "error",
        title: "Invalid Email",
        text: "Please enter a valid email address.",
      });
      $submitBtn.prop("disabled", false).val("Register");
      return;
    } else if (password != confirm_password) {
      Swal.fire({
        icon: "error",
        title: "Password Mismatch",
        text: "The confirmation password does not match the original password. Please check and try again.",
      });
      $submitBtn.prop("disabled", false).val("Register");
      return;
    }

    $.ajax({
      url: "api/register_process.php",
      method: "POST",
      dataType: "json",
      data: {
        firstname: firstname,
        lastname: lastname,
        "new-email": email,
        "new-username": username,
        "new-password": password,
        "remember-me": remember,
      },
      success: function (response) {
        if (response.status === "success") {
          Swal.fire({
            icon: "success",
            title: "Registration Successful",
            text: "Processing...",
            showConfirmButton: false,
            timer: 1500,
          }).then(() => {
            window.location.href = "/pages/active.php?otp=" + response.otp;
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Registration Failed",
            text: response.message,
          });
          $submitBtn.prop("disabled", false).val("Register");
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", status, error);
        console.error("Response Text:", xhr.responseText);

        Swal.fire({
          icon: "error",
          title: "AJAX Error",
          html: `<b>Status:</b> ${status}<br><b>Error:</b> ${error}`,
        });
        $submitBtn.prop("disabled", false).val("Register");
      },
    });
  });
});
