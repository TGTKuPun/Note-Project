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
const Box_add = document.querySelector(".add-note");
const Box_popup = document.querySelector(".popup-box");
const Title_popup = Box_popup.querySelector("header p");
const Btn_close = Box_popup.querySelector("header i");
const Btn_add = Box_popup.querySelector("button");

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
function showNotes() {
  document.querySelectorAll(".note").forEach((note) => note.remove());
  notes.forEach((note) => {
    let li = `<li class="note">
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
                      <li onclick="updateNote(${note.note_id}, \`${note.note_title}\`, \`${note.note_desc}\`, \`${note.label_name}\`)"><i class="bx bx-edit-alt"></i>Edit</li>
                      <li onclick="deleteNote(${note.note_id})"><i class="bx bx-trash-alt"></i>Delete</li>
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
    const res = await fetch("../api/Note/fetch_note.php");
    if (!res.ok) throw new Error("Fetch Failed");

    const data = await res.json();
    notes.length = 0;
    notes.push(...data);
    console.log(notes);

    localStorage.setItem("notes", JSON.stringify(notes)); // Update local-storage to latest version
    showNotes();

    console.log("Successfully Loaded latest notes from server");
  } catch (error) {
    console.warn("Server fetch failed, falling back to localStorage:", error);
    loadNotesFromLocal();
  }
}

function loadNotesFromLocal() {
  notes = JSON.parse(localStorage.getItem("notes") || "[]");
  showNotes();
  console.log("Loaded notes from localStorage");
}

let isUpdated = false,
  updateId;

Box_add.addEventListener("click", () => {
  Tag_title.focus();
  Box_popup.classList.add("show");

  // Reset
  isUpdated = false;
  updateId = null;
});

Btn_close.addEventListener("click", () => {
  Tag_title.value = "";
  Tag_desc.value = "";
  Tag_label.value = "";
  Btn_add.innerText = "Add Note";
  Title_popup.innerText = "Add a new Note";
  Box_popup.classList.remove("show");
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
function deleteNote(index) {
  Swal.fire({
    title: "Are you sure?",
    text: "This note will be permanently deleted!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, delete it",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      notes.splice(index, 1);
      localStorage.setItem("notes", JSON.stringify(notes));
      showNotes();

      // Sync with server
      $.ajax({
        url: "../api/Note/delete_note.php",
        method: "POST",
        contentType: "application/json",
        dataType: "json",
        data: JSON.stringify({ note_id: index }),
        success: function (response) {
          console.log(response.message);
          showNotes();
        },
        error: function (xhr, status, response) {
          console.error(response.error);
        },
      });
    }
  });
}

// To update Note
function updateNote(index, title, desc, label) {
  Box_add.click();
  isUpdated = true;
  updateId = index;
  Tag_title.value = title;
  Tag_desc.value = desc;
  Tag_label.value = label;
  Btn_add.innerText = "Update Note";
  Title_popup.innerText = "Update a Note";
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
      notes[updateId] = Note_info; // updating specified note
    }

    localStorage.setItem("notes", JSON.stringify(notes));
    showNotes();

    // Sync with the server
    $.ajax({
      url: "../api/Note/save_note.php",
      method: "POST",
      contentType: "application/json", // JSON you send
      dataType: "json", // JSON you receive
      data: JSON.stringify(Note_info),
      success: function (response) {
        console.log(response.message);
        // Immediately reload the notes
      },
      error: function (xhr, status, error) {
        console.error(error);
      },
    });

    // Immediately close the popup-box and reset the textbox
    Btn_close.click();
  }
});
