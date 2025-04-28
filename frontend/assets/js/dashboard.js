function openSearch() {
  const offcanvas = new bootstrap.Offcanvas(
    document.getElementById("searchOffcanvas")
  );
  offcanvas.show();
}

function closeSearch() {
  const offcanvasElement = document.getElementById("searchOffcanvas");
  const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement);
  if (offcanvasInstance) {
    offcanvasInstance.hide();
  }
}

function logout() {
  Swal.fire({
    title: "Are you sure?",
    text: "You will be logged out!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, log out",
    cancelButtonText: "No, stay here",
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: "Logging out...",
        text: "Redirecting...",
        icon: "info",
        showConfirmButton: false,
        allowOutsideClick: false,
      });

      setTimeout(() => {
        window.location.href = "../api/logout.php";
      }, 1500);
    }
  });
}

// CREATE NOTE FORM
const addBox = document.querySelector(".add-note");
const popupBox = document.querySelector(".popup-box");
const close = popupBox.querySelector("header i");

addBox.addEventListener("click", () => {
  popupBox.classList.add("show");
});

close.addEventListener("click", () => {
  popupBox.classList.remove("show");
});
