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
        localStorage.clear();
        window.location.href = "../api/logout.php";
      }, 1500);
    }
  });
}

let subMenu = document.getElementById("subMenu");

function toggleProfile() {
  const chevronIcon = document.querySelector(".chevron-icon");

  subMenu.classList.toggle("open");
  chevronIcon.classList.toggle("rotate");
}

const userId = document.body.dataset.userId;
if (!localStorage.getItem("user_id")) {
  localStorage.setItem("user_id", userId);
}

// ================== CREATE NOTE FORM ========================
const Box_add = document.querySelector(".add-note");
const Box_popup = document.querySelector(".popup-box");
const Title_popup = Box_popup.querySelector("header p");
const Btn_close = Box_popup.querySelector("header i");
const Btn_add = Box_popup.querySelector("button");
// Edit Profile Field
const Btn_editProfile = document.querySelector("#btn_edit_profile");
const Popup_EditProfile = document.querySelector(".popup-box-edit-profile");
const Btn_profile_close = document.querySelector("#close_profile_popup");
// Change Password Field
const Btn_changePassword = document.querySelector("#btn_change_password");
// prettier-ignore
const Popup_changePassword = document.querySelector(".popup-box-change-password");
// prettier-ignore
const Btn_change_password_close = document.querySelector("#close_change_password_popup");

//Share Note buttons
const shareBox = document.getElementById("popup-share-note");
const Btn_Share = document.getElementById("Btn_share");
const Btn_share_close = document.querySelector("#close_share_popup");

const Tag_title = Box_popup.querySelector("input");
const Tag_desc = Box_popup.querySelector("#note_desc");
const Tag_label = Box_popup.querySelector("#label");

// prettier-ignore
const months = [
  "January", "February", "March", "April", "May", "June",
  "July", "August", "September", "October", "November", "December"
];

let notes = JSON.parse(localStorage.getItem("notes") || "[]");
let syncQueue = JSON.parse(localStorage.getItem("syncQueue") || "[]");
const currentUserId = localStorage.getItem("user_id");

if (!currentUserId) {
  Swal.fire("Error", "User ID not found. Please log in again.", "error");
  setTimeout(() => {
    window.location.href = "../index.php";
  }, 1500);
  throw new Error("User ID not found");
}

// prettier-ignore
function showNotes(notes_to_show) {
  document.querySelectorAll(".note").forEach((note) => note.remove());

  notes_to_show.forEach((note) => {

    const pinnedClass = note.is_pinned ? 'pinned' : '';
    const pinIcon = note.is_pinned ? 'bxs-pin' : 'bx-pin';
    const isProtected = note.access === "protect";
    const noteId = note.note_id;
    const note_title = isProtected ? "This note is protected" : note.note_title;
    const note_desc = isProtected ? "Contact to the owner for more infomation." : note.note_desc;

    let li = `<li class="note ${pinnedClass}" data-id="${note.note_id}" data-title="${encodeURIComponent(note.note_title)}" data-desc="${encodeURIComponent(note.note_desc)}" data-label="${note.label_name || ''}">
                <div class="top-content" style="display: flex; align-items: center; gap: 10px;">
                  <div style="display: flex; align-items: center; gap: 8px; background-color: #fff; padding: 4px 8px; border-radius: 4px; border: 1px solid #575757; margin-bottom: 8px">
                    <img src="../assets/uploads/avatar/${note.user_avatar || 'default.webp'}" style="width: 30px;  height: 30px; object-fit: cover; border-radius: 50%;">
                    ${note.username || "Unknown User"}
                  </div>
                </div>
                <div class="details">
                  <p class="note-title">${note_title}</p>
                  <span class="note-desc">${note_desc}</span>
                  <div class="label">
                    <span>${note.label_name || 'No label'}</span>
                  </div>
                    ${isProtected ? `<button class="btn_see" onclick="checkPass(this)" data-id="${noteId}">Show</button>` : ''}
                </div>
                <div class="bottom-content">
                  <span>${note.note_date}</span>
                  <div style="display: flex; align-items: center; gap: 8px;">
                    <i class='bx bx-share' onclick="ShareNote(${note.note_id})" style="cursor: pointer;" title="Share note"></i>
                    <div class="settings">
                      <i onclick="showMenu(this)" class="bx bx-dots-horizontal-rounded"></i>
                      <ul class="menu">
                        <li onclick="togglePinNote(${note.note_id}, this)">
                          <i class="bx ${pinIcon}"></i><span>${note.is_pinned ? 'Unpin' : 'Pin'}</span>
                        </li>
                        <li onclick="updateNote(${note.note_id}, '${encodeURIComponent(note.note_title)}', '${encodeURIComponent(note.note_desc)}', '${note.label_name || ''}')">
                          <i class="bx bx-edit-alt"></i><span>Edit</span>
                        </li>
                        <li onclick="deleteNote(${note.note_id})"><i class="bx bx-trash-alt"></i><span>Delete</span></li>
                      </ul>
                    </div>
                  </div>
                </div>
              </li>`;
    Box_add.insertAdjacentHTML("afterend", li);
  });
}

function addToSyncQueue(action, data) {
  syncQueue.push({ action, data, timestamp: new Date().toISOString() });
  localStorage.setItem("syncQueue", JSON.stringify(syncQueue));
}

function processSyncQueue() {
  if (!navigator.onLine) return;

  let queueProcessed = false;

  // Process queue items sequentially to avoid race conditions
  const processNext = (index) => {
    if (index >= syncQueue.length) {
      if (queueProcessed) {
        syncQueue = [];
        localStorage.removeItem("syncQueue");
        console.log("syncQueue cleared from localStorage");
        loadNotesFromServer();
        fetch_label();
      }
      return;
    }

    const item = syncQueue[index];

    const removeFromQueue = () => {
      syncQueue.splice(index, 1);
      localStorage.setItem("syncQueue", JSON.stringify(syncQueue));
      queueProcessed = true;
      processNext(index);
    };

    const handleAjax = (url, data) => {
      $.ajax({
        url: url,
        method: "POST",
        contentType: "application/json",
        dataType: "json",
        data: JSON.stringify(data),
        success: function (response) {
          if (
            response.status === "success" ||
            (item.action === "add_label" &&
              response.message === "Label already exists")
          ) {
            console.log(
              `${item.action} ${
                data.label_id || data.note_id || data.label_name
              } successful${response.message ? " or already exists" : ""}`
            );
            removeFromQueue();
          } else {
            Swal.fire("Sync Error", response.message, "error");
            console.error(`Sync error (${item.action}):`, response.message);
            removeFromQueue();
          }
        },
        error: function (xhr, status, error) {
          console.error(`Sync error (${item.action}):`, {
            status,
            error,
            response: xhr.responseText,
          });
          Swal.fire(
            "Error",
            `Failed to sync ${item.action}, item removed from queue`,
            "warning"
          );
          removeFromQueue();
        },
      });
    };

    if (item.action === "add_label") {
      console.log("Syncing label:", item.data);
      handleAjax("../api/Note/add_label.php", item.data);
    } else if (item.action === "edit_label") {
      handleAjax("../api/Note/edit_label.php", item.data);
    } else if (item.action === "delete_label") {
      handleAjax("../api/Note/delete_label.php", item.data);
    } else if (item.action === "add_note") {
      handleAjax("../api/Note/save_note.php", item.data);
    } else if (item.action === "update_note") {
      handleAjax("../api/Note/update_note.php", item.data);
    } else if (item.action === "pin_note") {
      handleAjax("../api/Note/pin_note.php", item.data);
    } else if (item.action === "delete_note") {
      handleAjax("../api/Note/delete_note.php", item.data);
    }
  };

  processNext(0);
}

// getting local storage notes if exist and parsing them
// to js object else passing an empty array to notes
async function loadNotesFromServer() {
  try {
    const res = await fetch("../api/Note/fetch_note.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ user_id: currentUserId }),
    });
    if (!res.ok) throw new Error(`Fetch Failed: ${res.status}`);

    const data = await res.json();
    if (!Array.isArray(data)) {
      throw new Error("Invalid notes data from server");
    }

    notes.length = 0;
    notes.push(...data);
    localStorage.setItem("notes", JSON.stringify(notes));

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

// PINNED NOTE
function togglePinNote(note_id, element) {
  Swal.fire({
    title: "Confirm?",
    text: `Do you want to ${
      element.querySelector("span").innerText === "Pin" ? "pin" : "unpin"
    } this note?`,
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Yes",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      const pinData = { note_id: note_id, user_id: currentUserId };
      if (!navigator.onLine) {
        const noteIndex = notes.findIndex((note) => note.note_id === note_id);
        if (noteIndex !== -1) {
          notes[noteIndex].is_pinned = !notes[noteIndex].is_pinned;
          localStorage.setItem("notes", JSON.stringify(notes));
          addToSyncQueue("pin_note", pinData);
          showNotes(notes);
          Swal.fire(
            "Notification",
            "Note pin status saved locally and will sync when online",
            "info"
          );
        }
      } else {
        $.ajax({
          url: "../api/Note/pin_note.php",
          method: "POST",
          contentType: "application/json",
          dataType: "json",
          data: JSON.stringify(pinData),
          success: function (response) {
            if (response.status === "success") {
              const noteIndex = notes.findIndex(
                (note) => note.note_id === note_id
              );
              if (noteIndex !== -1) {
                notes[noteIndex].is_pinned = response.is_pinned;
                localStorage.setItem("notes", JSON.stringify(notes));
                loadNotesFromServer();
                Swal.fire("Success", response.message, "success");
              }
            } else {
              Swal.fire("Error", response.message, "error");
            }
          },
          error: function (xhr, status, error) {
            console.error("AJAX Error (pin_note):", {
              status: status,
              error: error,
              responseText: xhr.responseText,
              statusCode: xhr.status,
            });
            Swal.fire(
              "Error",
              "Failed to update pin status: " + (xhr.responseText || error),
              "error"
            );
          },
        });
      }
    }
  });
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

Btn_changePassword.addEventListener("click", () => {
  Popup_changePassword.classList.add("show");
});

Btn_close.addEventListener("click", () => {
  // Reset
  isUpdated = false;
  updateId = null;
  Tag_title.value = "";
  Tag_desc.innerHTML = "";
  Tag_label.value = "";
  Btn_add.innerText = "Add Note";
  Title_popup.innerText = "Add a new Note";
  Box_popup.classList.remove("show");
});

Btn_profile_close.addEventListener("click", () => {
  Popup_EditProfile.classList.remove("show");
});

Btn_change_password_close.addEventListener("click", () => {
  Popup_changePassword.classList.remove("show");
  $("#editPasswordForm")[0].reset();
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
      const deleteData = { note_id: note_id, user_id: currentUserId };
      if (!navigator.onLine) {
        const index = notes.findIndex((note) => note.note_id === note_id);
        if (index !== -1) {
          notes.splice(index, 1);
          localStorage.setItem("notes", JSON.stringify(notes));
          addToSyncQueue("delete_note", deleteData);
          showNotes(notes);
          Swal.fire(
            "Notification",
            "Note deleted locally and will sync when online",
            "info"
          );
        }
      } else {
        $.ajax({
          url: "../api/Note/delete_note.php",
          method: "POST",
          contentType: "application/json",
          dataType: "json",
          data: JSON.stringify(deleteData),
          success: function (response) {
            if (response.status === "success") {
              const index = notes.findIndex((note) => note.note_id === note_id);
              if (index !== -1) {
                notes.splice(index, 1);
                localStorage.setItem("notes", JSON.stringify(notes));
                loadNotesFromServer();
                Swal.fire("Success", response.message, "success");
              }
            } else {
              Swal.fire("Error", response.message, "error");
            }
          },
          error: function (xhr, status, error) {
            console.error("AJAX Error (delete_note):", {
              status: status,
              error: error,
              responseText: xhr.responseText,
              statusCode: xhr.status,
            });
            Swal.fire("Error", "Failed to delete note", "error");
          },
        });
      }
    }
  });
}

// To update Note
function updateNote(note_id, note_title, note_desc, label_name) {
  updateId = notes.findIndex((note) => note.note_id === note_id);

  if (updateId !== -1) {
    isUpdated = true;
    Tag_title.value = decodeURIComponent(note_title);
    Tag_desc.innerHTML = decodeURIComponent(note_desc);
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

  let Note_title = Tag_title.value.trim();
  let Note_desc = Tag_desc.innerHTML.trim();
  let Note_label = Tag_label.value;

  if (Note_title || Note_desc) {
    let Note_date = new Date();
    const month = months[Note_date.getMonth()];
    const day = Note_date.getDate();
    const year = Note_date.getFullYear();

    let Note_info = {
      note_title: Note_title,
      note_desc: Note_desc,
      label_name: Note_label,
      note_date: `${month} ${day}, ${year}`,
      user_id: currentUserId,
    };

    if (!isUpdated) {
      Note_info.note_id = Date.now();
      notes.push(Note_info);
    } else {
      Note_info.note_id = notes[updateId].note_id;
      notes[updateId] = Note_info;
    }

    if (!navigator.onLine) {
      localStorage.setItem("notes", JSON.stringify(notes));
      addToSyncQueue(isUpdated ? "update_note" : "add_note", Note_info);
      showNotes(notes);
      Btn_close.click();
      Swal.fire(
        "Notification",
        "Note saved locally and will sync when online",
        "info"
      );
    } else {
      $.ajax({
        url: isUpdated
          ? "../api/Note/update_note.php"
          : "../api/Note/save_note.php",
        method: "POST",
        contentType: "application/json",
        dataType: "json",
        data: JSON.stringify(Note_info),
        success: function (response) {
          if (response.status === "success") {
            if (!isUpdated) {
              Note_info.note_id = response.note_id;
              notes[notes.length - 1] = Note_info;
            }
            localStorage.setItem("notes", JSON.stringify(notes));
            loadNotesFromServer();
            Btn_close.click();
            Swal.fire("Success", response.message, "success");
          } else {
            Swal.fire("Error", response.message, "error");
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX Error (save/update_note):", {
            status: status,
            error: error,
            responseText: xhr.responseText,
            statusCode: xhr.status,
          });
          localStorage.setItem("notes", JSON.stringify(notes));
          addToSyncQueue(isUpdated ? "update_note" : "add_note", Note_info);
          showNotes(notes);
          Btn_close.click();
          Swal.fire(
            "Notification",
            "Note saved locally and will sync when online",
            "info"
          );
        },
      });
    }
  }
});

let isRenderingLabels = false;
function fetch_label() {
  if (isRenderingLabels) return; // Prevent concurrent calls
  isRenderingLabels = true;

  const select = Box_popup.querySelector("select");
  const dropdown = document.getElementById("label-menu");

  // Completely clear dropdown, keeping #new_label_item
  dropdown
    .querySelectorAll("li:not(#new_label_item), hr")
    .forEach((el) => el.remove());
  // Clear select
  select.innerHTML =
    '<option value="" disabled hidden selected>-- Select a label --</option>';

  $.ajax({
    url: "../api/Note/fetch_label.php",
    method: "POST",
    contentType: "application/json",
    dataType: "json",
    data: JSON.stringify({ user_id: currentUserId }),
    success: function (data) {
      if (!Array.isArray(data)) {
        console.error("Invalid labels data from server:", data);
        data = [];
      }
      localStorage.setItem("labels", JSON.stringify(data));
      renderLabels(data);
      isRenderingLabels = false;
    },
    error: function (xhr, status, error) {
      console.error("Error fetching labels from server:", {
        status,
        error,
        response: xhr.responseText,
      });
      const cachedLabels = JSON.parse(localStorage.getItem("labels") || "[]");
      renderLabels(cachedLabels);
      isRenderingLabels = false;
    },
  });

  function renderLabels(labels) {
    labels.forEach((label, index) => {
      const isEditable =
        label.user_id == currentUserId &&
        !["Work", "Study", "Business", "Personal"].includes(label.label_name);
      const li = document.createElement("li");
      li.classList.add("label-item");
      li.innerHTML = `
        <a class="dropdown-item label-name" href="#">${label.label_name}</a>
        ${
          isEditable
            ? `
        <div class="actions">
          <button class="edit-btn" onclick="editLabel(${label.label_id}, '${label.label_name}', ${label.user_id})">Edit</button>
          <button class="delete-btn" onclick="deleteLabel(${label.label_id}, ${label.user_id})">Delete</button>
        </div>
        `
            : ""
        }
      `;
      li.querySelector(".label-name").addEventListener("click", function (e) {
        e.preventDefault();
        filterLabel(label.label_name);
      });
      dropdown.appendChild(li);

      if (index < labels.length - 1) {
        const hr = document.createElement("hr");
        hr.classList.add("dropdown-divider");
        dropdown.appendChild(hr);
      }

      const option = document.createElement("option");
      option.value = label.label_name;
      option.textContent = label.label_name;
      select.appendChild(option);
    });
  }
}
// label manage
document.querySelector("#new_label_btn").addEventListener("click", (e) => {
  e.preventDefault();
  const newLabelInput = document.querySelector("#new_label_input");
  newLabelInput.style.display = "flex";
  document.querySelector("#new_label").focus();
});

document.querySelector("#save_label_btn").addEventListener("click", () => {
  const labelName = document.querySelector("#new_label").value.trim();

  if (!labelName) {
    Swal.fire("Error", "Label name cannot be empty", "error");
    return;
  }

  const labelData = {
    label_name: labelName,
    user_id: currentUserId,
    label_id: Date.now(),
  };

  let labels = JSON.parse(localStorage.getItem("labels") || "[]");
  if (
    labels.some(
      (label) => label.label_name.toLowerCase() === labelName.toLowerCase()
    )
  ) {
    Swal.fire("Error", "Label already exists", "error");
    document.querySelector("#new_label").value = "";
    document.querySelector("#new_label_input").style.display = "none";
    return;
  }

  if (!navigator.onLine) {
    labels.push(labelData);
    localStorage.setItem("labels", JSON.stringify(labels));
    addToSyncQueue("add_label", labelData);
    document.querySelector("#new_label").value = "";
    document.querySelector("#new_label_input").style.display = "none";
    fetch_label();
    Swal.fire(
      "Notification",
      "Label saved locally and will sync when online",
      "info"
    );
  } else {
    $.ajax({
      url: "../api/Note/add_label.php",
      method: "POST",
      contentType: "application/json",
      dataType: "json",
      data: JSON.stringify({ label_name: labelName, user_id: currentUserId }),
      success: function (response) {
        if (response.status === "success") {
          labels.push({
            label_id: response.label.label_id,
            label_name: response.label.label_name,
            user_id: response.label.user_id,
          });
          localStorage.setItem("labels", JSON.stringify(labels));
          document.querySelector("#new_label").value = "";
          document.querySelector("#new_label_input").style.display = "none";
          fetch_label();
          Swal.fire("Success", response.message, "success");
        } else {
          Swal.fire("Error", response.message, "error");
        }
      },
      error: function (xhr, status, error) {
        console.error("Error adding label:", {
          status,
          error,
          response: xhr.responseText,
        });
        labels.push(labelData);
        localStorage.setItem("labels", JSON.stringify(labels));
        addToSyncQueue("add_label", labelData);
        document.querySelector("#new_label").value = "";
        document.querySelector("#new_label_input").style.display = "none";
        fetch_label();
        Swal.fire(
          "Notification",
          "Label saved locally and will sync when online",
          "info"
        );
      },
    });
  }
});

document
  .querySelector("#label-menu")
  .addEventListener("hidden.bs.dropdown", () => {
    const newLabelInput = document.querySelector("#new_label_input");
    newLabelInput.style.display = "none";
    document.querySelector("#new_label").value = "";
  });

function editLabel(label_id, current_name, user_id) {
  if (user_id != currentUserId) {
    Swal.fire(
      "Error",
      "You do not have permission to edit this label",
      "error"
    );
    return;
  }

  Swal.fire({
    title: "Edit Label",
    input: "text",
    inputValue: current_name,
    showCancelButton: true,
    confirmButtonText: "Save",
    cancelButtonText: "Cancel",
    inputValidator: (value) => {
      if (!value.trim()) {
        return "Label name cannot be empty";
      }
    },
  }).then((result) => {
    if (result.isConfirmed) {
      const newName = result.value.trim();
      const labelData = {
        label_id,
        label_name: newName,
        user_id: currentUserId,
      };

      let labels = JSON.parse(localStorage.getItem("labels") || "[]");
      if (
        labels.some(
          (label) =>
            label.label_name.toLowerCase() === newName.toLowerCase() &&
            label.label_id != label_id
        )
      ) {
        Swal.fire("Error", "Label name already exists", "error");
        return;
      }

      if (!navigator.onLine) {
        const index = labels.findIndex((l) => l.label_id == label_id);
        if (index !== -1 && labels[index].user_id == currentUserId) {
          labels[index].label_name = newName;
          localStorage.setItem("labels", JSON.stringify(labels));
          addToSyncQueue("edit_label", labelData);
          fetch_label();
          loadNotesFromServer();
          Swal.fire(
            "Notification",
            "Label saved locally and will sync when online",
            "info"
          );
        } else {
          Swal.fire(
            "Error",
            "Label not found or you do not have permission to edit",
            "error"
          );
        }
      } else {
        $.ajax({
          url: "../api/Note/edit_label.php",
          method: "POST",
          contentType: "application/json",
          dataType: "json",
          data: JSON.stringify({
            label_id,
            label_name: newName,
            user_id: currentUserId,
          }),
          success: function (response) {
            if (response.status === "success") {
              const index = labels.findIndex((l) => l.label_id == label_id);
              if (index !== -1) {
                labels[index].label_name = newName;
                localStorage.setItem("labels", JSON.stringify(labels));
              }
              fetch_label();
              loadNotesFromServer();
              Swal.fire("Success", response.message, "success");
            } else {
              Swal.fire("Error", response.message, "error");
            }
          },
          error: function (xhr, status, error) {
            console.error("Error editing label:", {
              status,
              error,
              response: xhr.responseText,
            });
            const index = labels.findIndex((l) => l.label_id == label_id);
            if (index !== -1 && labels[index].user_id == currentUserId) {
              labels[index].label_name = newName;
              localStorage.setItem("labels", JSON.stringify(labels));
              addToSyncQueue("edit_label", labelData);
              fetch_label();
              loadNotesFromServer();
              Swal.fire(
                "Notification",
                "Label saved locally and will sync when online",
                "info"
              );
            } else {
              Swal.fire(
                "Error",
                "Label not found or you do not have permission to edit",
                "error"
              );
            }
          },
        });
      }
    }
  });
}

function deleteLabel(label_id, user_id) {
  if (user_id != currentUserId) {
    Swal.fire(
      "Error",
      "You do not have permission to delete this label",
      "error"
    );
    return;
  }

  Swal.fire({
    title: "Are you sure?",
    text: "This label will be permanently deleted!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, delete it",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      if (!navigator.onLine) {
        let labels = JSON.parse(localStorage.getItem("labels") || "[]");
        const index = labels.findIndex((l) => l.label_id == label_id);
        if (index !== -1 && labels[index].user_id == currentUserId) {
          labels.splice(index, 1);
          localStorage.setItem("labels", JSON.stringify(labels));
          addToSyncQueue("delete_label", { label_id, user_id: currentUserId });
          fetch_label();
          loadNotesFromServer();
          Swal.fire(
            "Notification",
            "Label deleted locally and will sync when online",
            "info"
          );
        } else {
          Swal.fire(
            "Error",
            "Label not found or you do not have permission to delete",
            "error"
          );
        }
      } else {
        $.ajax({
          url: "../api/Note/delete_label.php",
          method: "POST",
          contentType: "application/json",
          dataType: "json",
          data: JSON.stringify({ label_id, user_id: currentUserId }),
          success: function (response) {
            if (response.status === "success") {
              let labels = JSON.parse(localStorage.getItem("labels") || "[]");
              const index = labels.findIndex((l) => l.label_id == label_id);
              if (index !== -1) {
                labels.splice(index, 1);
                localStorage.setItem("labels", JSON.stringify(labels));
              }
              fetch_label();
              loadNotesFromServer();
              Swal.fire("Success", response.message, "success");
            } else {
              Swal.fire("Error", response.message, "error");
            }
          },
          error: function (xhr, status, error) {
            console.error("Error deleting label:", {
              status,
              error,
              response: xhr.responseText,
            });
            let labels = JSON.parse(localStorage.getItem("labels") || "[]");
            const index = labels.findIndex((l) => l.label_id == label_id);
            if (index !== -1 && labels[index].user_id == currentUserId) {
              labels.splice(index, 1);
              localStorage.setItem("labels", JSON.stringify(labels));
              addToSyncQueue("delete_label", {
                label_id,
                user_id: currentUserId,
              });
              fetch_label();
              loadNotesFromServer();
              Swal.fire(
                "Notification",
                "Label deleted locally and will sync when online",
                "info"
              );
            } else {
              Swal.fire(
                "Error",
                "Label not found or you do not have permission to delete",
                "error"
              );
            }
          },
        });
      }
    }
  });
}

// =============== FILTER LABEL FUNCTION =====================
const all_labels = document.getElementById("all-labels");
let sharedNotes = [];

all_labels.addEventListener("click", function (e) {
  e.preventDefault();

  const myNotes = notes.filter(
    (n) => String(n.user_id) === String(currentUserId)
  );
  const publicSharedNotes = notes.filter((n) => n.access === "public");
  const protectSharedNotes = notes.filter((n) => n.access === "protect");

  const allVisible = [...myNotes, ...publicSharedNotes, ...protectSharedNotes];

  showNotes(allVisible);
});

const my_notes = document.getElementById("my-notes");

my_notes.addEventListener("click", function (e) {
  e.preventDefault();

  const myNotes = notes.filter(
    (n) => String(n.user_id) === String(currentUserId)
  );

  showNotes(myNotes);
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

// ==================== SEARCH BAR FUNCTION ============================
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

$(document).ready(function () {
  if (navigator.onLine) {
    processSyncQueue();
    loadNotesFromServer();
    fetch_label();
  }

  window.addEventListener("offline", () => {
    Swal.fire(
      "Notification",
      "You are offline. Some functions are limited.",
      "warning"
    );
  });

  window.addEventListener("online", () => {
    Swal.fire("Notification", "Reconnected. Syncing data...", "info");
    processSyncQueue();
    loadNotesFromServer();
    fetch_label();
  });

  // =================== UPDATE LAYOUT VIEW PREFERENCE =====================
  $(".dropdown-menu .dropdown-item").on("click", function () {
    const selectedView = $(this).closest("li").data("view");

    $.ajax({
      url: "../api/Note/update_view_preference.php",
      method: "POST",
      contentType: "application/json",
      data: JSON.stringify({ view: selectedView }),
      success: function (response) {
        if (response.success) {
          const wrapper = $(".wrapper");
          localStorage.setItem("view", selectedView);

          if (wrapper.length > 0) {
            wrapper.removeClass("list-view");

            if (selectedView === "list") {
              wrapper.addClass("list-view");
            }
          }

          $(".navbar-nav .nav-item a").removeClass("active");
          $(this).addClass("active");
        } else {
          Swal.fire("Error", "Failed to change layout!", "error");
        }
      },
      error: function (error) {
        console.error("Error occurred:", error);
      },
    });
  });
});

// ====================== SCROLL TO TOP =============================
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

// =================== POPUP EDIT PROFILE FORM ============================
// Change immediately the new avatar to review
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

document.addEventListener("click", function (e) {
  if (e.target.closest(".menu li span")?.innerText === "Edit") {
    const noteEl = e.target.closest("li.note");
    if (!noteEl) return;

    const note_id = parseInt(noteEl.dataset.id);
    const note_title = decodeURIComponent(noteEl.dataset.title);
    const note_desc = decodeURIComponent(noteEl.dataset.desc);
    const note_label = decodeURIComponent(noteEl.dataset.label);

    updateNote(note_id, note_title, note_desc, note_label);
  }
});

// ====================== CHANGE PASSWORD FORM =======================
$("#editPasswordForm").on("submit", function (e) {
  e.preventDefault();

  var currentPassword = $("#current_password").val().trim();
  var newPassword = $("#new_password").val().trim();
  var confirmPassword = $("#confirm_password").val().trim();

  // Delivery data via Ajax
  $.ajax({
    url: "../api/change_password.php",
    method: "POST",
    dataType: "json",
    data: {
      current_password: currentPassword,
      new_password: newPassword,
      confirm_password: confirmPassword,
      //user_id: currentUserId
    },
    success: function (response) {
      if (response.status === "success") {
        $("#editPasswordForm")[0].reset();
        Swal.fire({
          icon: "success",
          title: "Success",
          text: "Your password has been successfully updated to the latest",
        });
        return;
      } else {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: response.message,
        });
        return;
      }
    },
    error: function (xhr, status, error) {
      console.log("XHR:", xhr.responseText);
      console.log("Status:", status);
      console.log("Error:", error);

      alert("Something went wrong! Please try again.");
      return;
    },
  });
});

//Share note
function ShareNote(note_id) {
  document.getElementById("note_id").value = note_id;
  shareBox.classList.add("show");
}

document.getElementById("share_type").addEventListener("change", function () {
  const shareType = document.getElementById("share_type");
  const protect_pass = document.getElementById("protect_pass");
  const note_pass = document.getElementById("note_pass");

  if (shareType.value === "protect") {
    protect_pass.style.display = "block";
    note_pass.setAttribute("required", "required");
  } else {
    document.getElementById("protect_pass").style.display = "none";
    note_pass.removeAttribute("required");
    note_pass.value = "";
  }
});

Btn_share_close.addEventListener("click", () => {
  shareBox.classList.remove("show");
});

document.getElementById("Share").addEventListener("submit", function (e) {
  e.preventDefault();

  note_id = document.getElementById("note_id").value;
  share_type = document.getElementById("share_type").value;
  note_pass = document.getElementById("note_pass").value;

  if (!note_id || !share_type) {
    alert("Vui lòng chọn kiểu chia sẻ.");
    return;
  }

  const Inputdata = {
    note_id: parseInt(note_id),
    share_type: share_type,
  };

  // Nếu share_type là protect và có mật khẩu thì thêm mật khẩu vào request
  if (share_type === "protect" && note_pass.trim() !== "") {
    Inputdata.pass = note_pass;
  }

  fetch("../api/note/share_note.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(Inputdata),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "success") {
        Swal.fire({
          icon: "success",
          title: "Share success",
          text: "Now everyone can see your note",
          confirmButtonText: "OK",
        });

        shareBox.classList.remove("show");
      } else {
        Swal.fire({
          icon: "error",
          title: "Error!",
          text: "Failed to share to everyone",
        });
        shareBox.classList.remove("show");
      }
    })

    .catch((err) => {
      console.error("Lỗi:", err);
      alert("Có lỗi khi kết nối tới máy chủ.");
    });
});

//Verify Password Protected Note
function checkPass(button) {
  const noteId = button.getAttribute("data-id");

  Swal.fire({
    title: "Enter password",
    input: "password",
    inputPlaceholder: "Note password",
    showCancelButton: true,
    confirmButtonText: "Unlock",
  }).then((res) => {
    if (res.isConfirmed) {
      $.ajax({
        url: "../api/note/verify_note_pass.php",
        method: "POST",
        contentType: "application/json",
        dataType: "json",
        data: JSON.stringify({ note_id: noteId, password: res.value }),
        success: function (data) {
          if (data.success) {
            const noteElement = document.querySelector(
              `.note[data-id="${noteId}"]`
            );
            noteElement.querySelector(".note-title").textContent =
              data.note_title;
            noteElement.querySelector(".note-desc").textContent =
              data.note_desc;
            button.remove(); // xóa nút "See"
          } else {
            Swal.fire("Mật khẩu không đúng", "", "error");
          }
        },
        error: function (xhr, status, error) {
          Swal.fire("Lỗi server, thử lại sau", "", "error");
          console.error("AJAX error:", error);
        },
      });
    }
  });
}
