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

let subMenu = document.getElementById("subMenu");

function toggleProfile() {
  subMenu.classList.toggle("open");
}

// CREATE NOTE FORM
const Box_add = document.querySelector(".add-note");
const Box_popup = document.querySelector(".popup-box");
const Title_popup = Box_popup.querySelector("header p");
const Btn_close = Box_popup.querySelector("header i");
const Btn_add = Box_popup.querySelector("button");
const Btn_editProfile = document.querySelector("#btn_edit_profile");
const Popup_EditProfile = document.querySelector(".popup-box-edit-profile");
const Btn_profile_close = document.querySelector("#close_profile_popup");

const Tag_title = Box_popup.querySelector("input");
const Tag_desc = Box_popup.querySelector("textarea");
const Tag_label = Box_popup.querySelector("#label");

// prettier-ignore
const months = [
  "January", "February", "March", "April", "May", "June",
  "July", "August", "September", "October", "November", "December"
];

let notes = JSON.parse(localStorage.getItem("notes") || "[]");

// prettier-ignore
function showNotes(notes_to_show) {
  document.querySelectorAll(".note").forEach((note) => note.remove());
  notes_to_show.forEach((note) => {
    let li = `<li class="note" data-labels="${note.label_name || ''}">
                <div class="details">
                  <p>${note.note_title}</p>
                  <span>${note.note_desc}</span>
                  <div class="label">
                    <span>${note.label_name || 'No label'}</span>
                  </div>
                </div>
                <div class="bottom-content">
                  <span>${note.note_date}</span>
                  <div class="settings">
                    <i onclick="showMenu(this)" class="bx bx-dots-horizontal-rounded"></i>
                    <ul class="menu">
                      <li onclick="updateNote(${note.note_id}, \`${note.note_title}\`, \`${note.note_desc}\`, \`${note.label_name}\`)"><i class="bx bx-edit-alt"></i><span>Edit</span></li>
                      <li onclick="deleteNote(${note.note_id})"><i class="bx bx-trash-alt"></i><span>Delete</span></li>
                    </ul>
                  </div>
                </div>
              </li>`;
    Box_add.insertAdjacentHTML("afterend", li);
  });
}

// getting local storage notes if exist and parsing them
// to js object else passing an empty array to notes
async function loadNotesFromServer() {
  try {
    const res = await fetch("../api/note/fetch_note.php");
    if (!res.ok) throw new Error("Fetch Failed");

    const data = await res.json();
    notes.length = 0;
    notes.push(...data);

    localStorage.setItem("notes", JSON.stringify(notes)); // Update local-storage to latest version

    const savedLabel = localStorage.getItem("selectedLabel");
    const searchKeyword = localStorage.getItem("searchKeyword")?.toLowerCase();

    let filteredNotes = [...notes];

    if (searchKeyword) {
      filteredNotes = filteredNotes.filter((n) =>
        n.note_title.toLowerCase().includes(searchKeyword)
      );
      searchInput.value = searchKeyword;
      showNotes(filteredNotes);
    } else if (savedLabel) {
      filteredNotes = filteredNotes.filter((n) => n.label_name === savedLabel);
      showNotes(filteredNotes);
    } else {
      showNotes(notes);
    }

    console.log("Successfully Loaded latest notes from server");
  } catch (error) {
    console.warn("Server fetch failed, falling back to localStorage:", error);
    loadNotesFromLocal();
  }
}

function loadNotesFromLocal() {
  notes = JSON.parse(localStorage.getItem("notes") || "[]");
  showNotes(notes);
  console.log("Loaded notes from localStorage");
}

let isUpdated = false,
  updateId;

Box_add.addEventListener("click", () => {
  Tag_title.focus();
  Box_popup.classList.add("show");
});

Btn_editProfile.addEventListener("click", () => {
  Popup_EditProfile.classList.add("show");
});

Btn_close.addEventListener("click", () => {
  // Reset
  isUpdated = false;
  updateId = null;
  Tag_title.value = "";
  Tag_desc.value = "";
  Tag_label.value = "";
  Btn_add.innerText = "Add Note";
  Title_popup.innerText = "Add a new Note";
  Box_popup.classList.remove("show");
});

Btn_profile_close.addEventListener("click", () => {
  Popup_EditProfile.classList.remove("show");
});

// To display setting-menu in which is clicked.
let currentMenu = null;

function showMenu(elem) {
  if (currentMenu) {
    currentMenu.classList.remove("show");
  }

  const menuBox = elem.parentElement;
  menuBox.classList.add("show");

  currentMenu = menuBox;
}

document.addEventListener("click", (e) => {
  if (!e.target.closest(".settings")) {
    if (currentMenu) currentMenu.classList.remove("show");
    currentMenu = null;
  }
});

// To delete Note
function deleteNote(note_id) {
  Swal.fire({
    title: "Are you sure?",
    text: "This note will be permanently deleted!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, delete it",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      const index = notes.findIndex((note) => note.note_id === note_id);
      if (index !== -1) {
        notes.splice(index, 1);
        localStorage.setItem("notes", JSON.stringify(notes));
        showNotes(notes);
      }

      // Sync with server
      $.ajax({
        url: "../api/note/delete_note.php",
        method: "POST",
        contentType: "application/json",
        dataType: "json",
        data: JSON.stringify({ note_id: note_id }),
        success: function (response) {
          console.log(response.message);
        },
        error: function (xhr, status, response) {
          console.error(response.error);
        },
      });
    }
  });
}

// To update Note
function updateNote(note_id, note_title, note_desc, label_name) {
  updateId = notes.findIndex((note) => note.note_id === note_id);

  if (updateId !== -1) {
    isUpdated = true;
    Tag_title.value = note_title;
    Tag_desc.value = note_desc;
    Tag_label.value = label_name;
    Btn_add.innerText = "Update Note";
    Title_popup.innerText = "Update a Note";

    Box_add.click();
  } else {
    console.warn("Note with note_id =", note_id, "not found");
  }
}

// SAVING NOTES TO LOCAL STORAGE (AUTO SAVE)
Btn_add.addEventListener("click", (e) => {
  e.preventDefault();

  let Note_title = Tag_title.value;
  let Note_desc = Tag_desc.value;
  let Note_label = Tag_label.value;

  if (Note_title || Note_desc) {
    // getting month, day, year from the current date
    let Note_date = new Date();
    const month = months[Note_date.getMonth()];
    const day = Note_date.getDate();
    const year = Note_date.getFullYear();

    let Note_info = {
      note_title: Note_title,
      note_desc: Note_desc,
      label_name: Note_label,
      note_date: `${month} ${day}, ${year}`,
    };

    if (!isUpdated) {
      notes.push(Note_info); // adding new note to notes
    } else {
      Note_info.note_id = notes[updateId].note_id; // Initialize 'note_id' attr
      notes[updateId] = Note_info; // updating specified note
    }

    console.log(isUpdated);
    // Sync with the server
    $.ajax({
      url: isUpdated
        ? "../api/note/update_note.php"
        : "../api/note/save_note.php",
      method: "POST",
      contentType: "application/json", // JSON you send
      dataType: "json", // JSON you receive
      data: JSON.stringify(Note_info),
      success: function (response) {
        console.log(response.message);

        // if (!isUpdated) {
        //   // Update note_id
        Note_info.note_id = response.note_id;
        notes[notes.length - 1] = Note_info;
        localStorage.setItem("notes", JSON.stringify(notes));
        showNotes(notes);
        // }
      },
      error: function (xhr, status, error) {
        console.error(error);
      },
    });

    // Immediately close the popup-box and reset the textbox
    Btn_close.click();
  }
});

function fetch_label() {
  const select = Box_popup.querySelector("select");
  const dropdown = document.getElementById("label-menu");

  $.ajax({
    url: "../api/note/fetch_label.php",
    method: "POST",
    dataType: "json",
    success: function (data) {
      data.forEach((label, index) => {
        // In navbar
        const li = document.createElement("li");
        const a = document.createElement("a");
        const hr = document.createElement("hr");
        a.classList.add("dropdown-item");
        hr.classList.add("dropdown-divider");
        a.href = "#";
        a.textContent = label.label_name;

        // Filter function
        a.addEventListener("click", function (e) {
          e.preventDefault();
          filterLabel(label.label_name);
        });

        li.appendChild(a);
        dropdown.appendChild(li);

        if (index < data.length - 1) {
          const hr = document.createElement("hr");
          hr.classList.add("dropdown-divider");
          dropdown.appendChild(hr);
        }
        // In create note form
        const option = document.createElement("option");
        option.value = label.label_name;
        option.textContent = label.label_name;
        select.appendChild(option);
      });
    },
    error: function (xhr, status, error) {
      console.error("Error fetching labels from server:", error);
    },
  });
}

// FILTER LABEL FUNCTION
const all_labels = document.getElementById("all-labels");

all_labels.addEventListener("click", function (e) {
  e.preventDefault();
  filterLabel("All");
});

function filterLabel(labelName) {
  if (labelName === "All") {
    // To display the user preference regardless of reloading website
    localStorage.removeItem("selectedLabel");
    showNotes(notes);
  } else {
    localStorage.setItem("selectedLabel", labelName);
    const filtered = notes.filter((n) => n.label_name === labelName);
    showNotes(filtered);
  }
}

// SEARCH BAR FUNCTION
const searchBar = document.getElementById("searchOffcanvas");
const searchInput = searchBar.querySelector("#input-field");

// Use "input" for immediate search.
searchInput.addEventListener("input", function () {
  const keyword = this.value.toLowerCase();
  localStorage.setItem("searchKeyword", keyword);
  const filter = notes.filter((note) =>
    note.note_title.toLowerCase().includes(keyword)
  );
  showNotes(filter);
});

// UPDATE LAYOUT VIEW PREFERENCE
$(document).ready(function () {
  $(".dropdown-menu .dropdown-item").on("click", function () {
    const selectedView = $(this).closest("li").data("view");

    // Gửi yêu cầu AJAX để lưu layout trên server
    $.ajax({
      url: "../api/note/update_view_preference.php",
      method: "POST",
      contentType: "application/json",
      data: JSON.stringify({ view: selectedView }),
      success: function (response) {
        if (response.success) {
          const wrapper = $(".wrapper");
          if (wrapper.length > 0) {
            wrapper.removeClass("list-view");

            if (selectedView === "list") {
              wrapper.addClass("list-view");
            }
          }

          $(".navbar-nav .nav-item a").removeClass("active");
          $(this).addClass("active");
        } else {
          alert("Không thể lưu thay đổi layout!");
        }
      },
      error: function (error) {
        console.error("Có lỗi xảy ra:", error);
      },
    });
  });
});

// SCROLL TO TOP
$(window).scroll(function () {
  if ($(window).scrollTop() > 300) {
    $(".bx.bxs-chevron-up").css({
      opacity: "1",
      "pointer-events": "auto",
    });
  } else {
    $(".bx.bxs-chevron-up").css({
      opacity: "0",
      "pointer-events": "none",
    });
  }
});

$(".bx.bxs-chevron-up").click(function () {
  $("html, body").animate({ scrollTop: 0 }, 200);
});

// POPUP EDIT PROFILE FORM
document.querySelector("#edit_avatar").addEventListener("change", function (e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (event) {
      document.querySelector("#avatar_preview").src = event.target.result;
    };
    reader.readAsDataURL(file);
  }
});
