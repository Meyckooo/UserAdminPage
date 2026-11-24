document.addEventListener("DOMContentLoaded", () => {
  // ==========================================
  // HELPER: CURRENT LOCAL DATE & TIME
  // ==========================================
  function getCurrentDateTimeLocal() {
    const now = new Date();
    const offset = now.getTimezoneOffset() * 60000;
    return new Date(now.getTime() - offset).toISOString().slice(0, 16);
  }

  const locatorCodeInput = document.getElementById("locatorCodeField");
  const locatorDescInput = document.getElementById("locatorDescription");
  const locatorAddedInput = document.getElementById("locatorAdded");
  const addedByInput = document.getElementById("addedBy");
  const locatorRemarksInput = document.getElementById("locatorRemarks");
  const headerLocatorSpan =
    document.getElementById("headerLocatorCode") ||
    document.querySelector(".item_header .item_code span");

  if (locatorAddedInput && !locatorAddedInput.value) {
    locatorAddedInput.value = getCurrentDateTimeLocal();
  }

  const initialLocator =
    locatorCodeInput?.value.trim() || headerLocatorSpan?.innerText.trim() || "";
  if (headerLocatorSpan && initialLocator) {
    headerLocatorSpan.innerText = initialLocator;
  }
  if (locatorCodeInput && initialLocator) {
    locatorCodeInput.value = initialLocator;
  }

  // ==========================================
  // LOCALSTORAGE PERSISTENCE HELPERS
  // ==========================================

  function saveItemsToStorage() {
    const currentMainBody = document.getElementById("main_table_body");
    if (!currentMainBody) return;

    const rows = currentMainBody.querySelectorAll("tr:not(.empty-row)");
    const items = [];

    rows.forEach((row) => {
      const firstRadio = row.cells[6]?.querySelector('input[type="radio"]');
      const secRadio = row.cells[7]?.querySelector('input[type="radio"]');

      const firstLocVal = firstRadio ? firstRadio.value.trim() : "";
      const secLocVal = secRadio ? secRadio.value.trim() : "";

      const firstLocChecked = firstRadio && firstRadio.checked ? 1 : 0;
      const secLocChecked = secRadio && secRadio.checked ? 1 : 0;

      const itemData = {
        itemLocator: row.cells[0]?.innerText.trim() || "",
        itemCode: row.cells[1]?.innerText.trim() || "",
        barCode: row.cells[2]?.innerText.trim() || "",
        itemNo: row.cells[3]?.innerText.trim() || "",
        uom: row.cells[4]?.innerText.trim() || "",
        desc: row.cells[5]?.innerText.trim() || "",
        firstLocVal: firstLocVal,
        secLocVal: secLocVal,
        firstLocChecked: firstLocChecked,
        secLocChecked: secLocChecked,
        itemStock: row.cells[8]?.innerText.trim() || "",
      };
      items.push(itemData);
    });

    localStorage.setItem("saved_main_table_items", JSON.stringify(items));
  }

  function loadItemsFromStorage() {
    const currentMainBody = document.getElementById("main_table_body");
    if (!currentMainBody) return;

    const storedItems = localStorage.getItem("saved_main_table_items");
    if (!storedItems) return;

    const items = JSON.parse(storedItems);
    if (items.length === 0) return;

    const emptyRow = currentMainBody.querySelector(".empty-row");
    if (emptyRow) emptyRow.remove();

    items.forEach((item) => {
      const newRow = document.createElement("tr");
      newRow.setAttribute("data-item-code", item.itemCode);
      newRow.setAttribute("data-item-locator", item.itemLocator || "");

      // Defaults to first option checked if nothing saved yet
      const isFirstChecked =
        item.firstLocChecked !== undefined ? item.firstLocChecked === 1 : true;
      const isSecChecked =
        item.secLocChecked !== undefined ? item.secLocChecked === 1 : false;

      newRow.innerHTML = `
                <td>${item.itemLocator || ""}</td>
                <td>${item.itemCode}</td>
                <td>${item.barCode}</td>
                <td>${item.itemNo}</td>
                <td>${item.uom}</td>
                <td>${item.desc}</td>
                <td class="text-center">
                    <label class="locator_radio_wrapper">
                        <input type="radio" name="locator_${item.itemCode}" value="${item.firstLocVal}" ${isFirstChecked ? "checked" : ""} onchange="saveItemsToStorage()">
                        <span style="display: none;">${item.firstLocVal}</span>
                    </label>
                </td>
                <td class="text-center">
                    <label class="locator_radio_wrapper">
                        <input type="radio" name="locator_${item.itemCode}" value="${item.secLocVal}" ${isSecChecked ? "checked" : ""} onchange="saveItemsToStorage()">
                        <span style="display: none;">${item.secLocVal}</span>
                    </label>
                </td>
                <td class="hidden">${item.itemStock}</td>
                <td class="text-center">
                    <button type="button" class="btn-delete" onclick="deleteRow(this)" title="Delete Row">&times;</button>
                </td>
            `;
      currentMainBody.appendChild(newRow);
    });
  }

  loadItemsFromStorage();

  // ==========================================
  // ROW CLICK -> DISPLAY IN LOCATOR DETAILS
  // ==========================================
  const mainTableBody = document.getElementById("main_table_body");
  if (mainTableBody) {
    mainTableBody.addEventListener("click", function (e) {
      if (
        e.target.closest(
          'input[type="radio"], .locator_radio_wrapper, .btn-delete',
        )
      ) {
        return;
      }

      const clickedRow = e.target.closest("tr:not(.empty-row)");
      if (!clickedRow) return;

      mainTableBody
        .querySelectorAll("tr")
        .forEach((tr) => tr.classList.remove("selected-row"));
      clickedRow.classList.add("selected-row");

      const rowLocator =
        clickedRow.cells[0]?.innerText.trim() ||
        clickedRow.getAttribute("data-item-locator") ||
        "";
      const itemCode = clickedRow.cells[1]?.innerText.trim() || "";
      const itemDesc = clickedRow.cells[5]?.innerText.trim() || "";

      if (headerLocatorSpan && rowLocator) {
        headerLocatorSpan.innerText = rowLocator;
      }
      if (locatorCodeInput && rowLocator) {
        locatorCodeInput.value = rowLocator;
      }
      if (locatorDescInput) {
        locatorDescInput.value = itemDesc || itemCode;
      }
      if (locatorAddedInput) {
        locatorAddedInput.value = getCurrentDateTimeLocal();
      }
    });
  }

  // ==========================================
  // MODAL OPEN / CLOSE CONTROLLERS
  // ==========================================
  function openModal(modalId) {
    const targetModal = document.getElementById(modalId);
    if (targetModal) {
      targetModal.classList.add("active");
      targetModal.style.display = "flex";

      if (modalId === "modal-view-items") {
        fetchPostedItems();
      }
    }
  }

  function closeModal() {
    const modals = document.querySelectorAll(".modal_overlay");
    modals.forEach((modal) => {
      modal.classList.remove("active");
      modal.style.display = "none";
    });
  }

  document.addEventListener("click", (e) => {
    const triggerBtn = e.target.closest("[data-modal]");
    if (triggerBtn) {
      e.preventDefault();
      const modalId = triggerBtn.getAttribute("data-modal");
      openModal(modalId);
      return;
    }

    if (
      e.target.closest(".modal_close") ||
      e.target.classList.contains("modal_overlay")
    ) {
      closeModal();
    }
  });

  window.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      closeModal();
    }
  });

  // ==========================================
  // FETCH POSTED ITEMS FROM BACKEND (AJAX)
  // ==========================================
  function fetchPostedItems(dateRange = "") {
    const postedTableBody = document.getElementById("posted_table_body");
    if (!postedTableBody) return;

    postedTableBody.innerHTML = `
            <tr>
                <td colspan="11" style="text-align: center;">Loading posted items...</td>
            </tr>
        `;

    const formData = new FormData();
    formData.append("get_posted_items", "1");
    if (dateRange) {
      formData.append("date_range", dateRange);
    }

    fetch("../item_action.php", {
      method: "POST",
      body: formData,
    })
      .then(async (response) => {
        const text = await response.text();
        try {
          return JSON.parse(text);
        } catch (err) {
          console.error("Server returned non-JSON output:", text);
          throw new Error("Server returned invalid JSON response.");
        }
      })
      .then((data) => {
        postedTableBody.innerHTML = "";

        if (data.status === "success" && data.items.length > 0) {
          data.items.forEach((item) => {
            const firstLocStatus =
              parseInt(item.item_first_loc) === 1
                ? '<span style="color: #28a745; font-size: 1.2rem; font-weight: bold;">&#10004;</span>'
                : '<span style="color: #dc3545; font-size: 1.2rem; font-weight: bold;">&#10008;</span>';

            const secLocStatus =
              parseInt(item.item_sec_loc) === 1
                ? '<span style="color: #28a745; font-size: 1.2rem; font-weight: bold;">&#10004;</span>'
                : '<span style="color: #dc3545; font-size: 1.2rem; font-weight: bold;">&#10008;</span>';

            const tr = document.createElement("tr");
            tr.innerHTML = `
                        <td>${item.item_locator}</td>
                        <td>${item.item_code}</td>
                        <td>${item.item_barcode}</td>
                        <td>${item.item_no}</td>
                        <td>${item.item_desc}</td>
                        <td>${item.item_uom}</td>
                        <td class="text-center">${firstLocStatus}</td>
                        <td class="text-center">${secLocStatus}</td>
                        <td>${item.remarks || "-"}</td>
                        <td>${item.added_by || "-"}</td>
                        <td>${item.created_at}</td>
                    `;
            postedTableBody.appendChild(tr);
          });
        } else {
          postedTableBody.innerHTML = `
                    <tr>
                        <td colspan="11" style="text-align: center; color: #888;">No posted items found for the selected criteria.</td>
                    </tr>
                `;
        }
      })
      .catch((error) => {
        console.error("Error fetching posted items:", error);
        postedTableBody.innerHTML = `
                <tr>
                    <td colspan="11" style="text-align: center; color: red;">Failed to fetch data from server.</td>
                </tr>
            `;
      });
  }

  // ==========================================
  // FLATPICKR CALENDAR INITIALIZATION
  // ==========================================
  const dateInput = document.getElementById("posted_date_range");
  const clearBtn = document.getElementById("btn_clear_date");

  if (dateInput && clearBtn) {
    const fp = flatpickr(dateInput, {
      mode: "range",
      dateFormat: "Y-m-d",
      onChange: function (selectedDates, dateStr) {
        if (dateStr) {
          clearBtn.style.display = "flex";
          fetchPostedItems(dateStr);
        } else {
          clearBtn.style.display = "none";
          fetchPostedItems("");
        }
      },
    });

    clearBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      fp.clear();
      clearBtn.style.display = "none";
      fetchPostedItems("");
    });
  }

  fetchPostedItems("");

  // ==========================================
  // STICKY LOCATOR
  // ==========================================
  const tableContainer = document.querySelector(".item_table_details");
  const formContainer = document.querySelector(".item_form_container");

  if (tableContainer && formContainer) {
    const observer = new ResizeObserver(() => {
      const tableHeight = tableContainer.offsetHeight;

      if (tableHeight >= 500) {
        formContainer.style.position = "sticky";
        formContainer.style.top = "20px";
        formContainer.style.maxHeight = "calc(100vh - 40px)";
        formContainer.style.overflowY = "auto";
      } else {
        formContainer.style.position = "static";
        formContainer.style.maxHeight = "none";
        formContainer.style.overflowY = "visible";
      }
    });

    observer.observe(tableContainer);
  }

  // ==========================================
  // SEARCH & FILTER FUNCTION (LOOKUP MODAL)
  // ==========================================
  const selectAllCheckbox = document.getElementById("selectAll");
  const btnAddSelected = document.getElementById("btnAddSelected");

  const searchInput =
    document.getElementById("lookupSearchInput") ||
    document.querySelector(".lookup_search_input");
  const filterSelect =
    document.getElementById("lookupSelectFilter") ||
    document.querySelector(".lookup_select");
  const btnSearch =
    document.getElementById("btnLookupSearch") ||
    document.querySelector(".btn_lookup_search");
  const btnScan =
    document.getElementById("btnLookupScan") ||
    document.querySelector(".btn_lookup_scan");
  const lookupTable = document.querySelector(".styled_lookup_table");

  function toggleAddButton() {
    if (!btnAddSelected) return;

    const checkedBoxes = document.querySelectorAll(
      ".styled_lookup_table tbody tr:not([style*='display: none']) .row_checkbox:checked:not([disabled])",
    );

    if (checkedBoxes.length > 0) {
      btnAddSelected.style.display = "inline-block";
    } else {
      btnAddSelected.style.display = "none";
    }

    if (selectAllCheckbox) {
      const visibleBoxes = document.querySelectorAll(
        ".styled_lookup_table tbody tr:not([style*='display: none']) .row_checkbox:not([disabled])",
      );
      selectAllCheckbox.checked =
        visibleBoxes.length > 0 && checkedBoxes.length === visibleBoxes.length;
    }
  }

  function filterLookupTable() {
    const query = searchInput ? searchInput.value.toLowerCase().trim() : "";
    let filterType = filterSelect
      ? filterSelect.value.toLowerCase().trim()
      : "description";

    if (
      filterSelect &&
      filterSelect.options &&
      filterSelect.selectedIndex >= 0
    ) {
      filterType = filterSelect.options[filterSelect.selectedIndex].text
        .toLowerCase()
        .trim();
    }

    const rows = document.querySelectorAll(".styled_lookup_table tbody tr");

    rows.forEach((row) => {
      if (row.cells.length <= 1) return;

      let targetText = "";

      if (filterType.includes("code")) {
        targetText =
          row.querySelector(".item_code")?.innerText ||
          row.cells[1]?.innerText ||
          "";
      } else if (filterType.includes("barcode")) {
        targetText =
          row.querySelector(".item_barcode")?.innerText ||
          row.cells[2]?.innerText ||
          "";
      } else {
        targetText =
          row.querySelector(".item_desc")?.innerText ||
          row.cells[3]?.innerText ||
          "";
      }

      if (targetText.toLowerCase().includes(query)) {
        row.style.display = "";
      } else {
        row.style.display = "none";
        const cb = row.querySelector(".row_checkbox");
        if (cb && !cb.disabled) cb.checked = false;
      }
    });

    toggleAddButton();
  }

  if (btnSearch) {
    btnSearch.addEventListener("click", function (e) {
      e.preventDefault();
      filterLookupTable();
    });
  }

  if (filterSelect) {
    filterSelect.addEventListener("change", function () {
      filterLookupTable();
    });
  }

  if (searchInput) {
    searchInput.addEventListener("input", filterLookupTable);
    searchInput.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        filterLookupTable();
      }
    });
  }

  if (btnScan) {
    btnScan.addEventListener("click", function (e) {
      e.preventDefault();
      if (searchInput) {
        searchInput.focus();
        searchInput.select();
      }
    });
  }

  if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener("change", function () {
      const visibleCheckboxes = document.querySelectorAll(
        ".styled_lookup_table tbody tr:not([style*='display: none']) .row_checkbox:not([disabled])",
      );
      visibleCheckboxes.forEach((cb) => {
        cb.checked = this.checked;
      });
      toggleAddButton();
    });
  }

  if (lookupTable) {
    lookupTable.addEventListener("change", function (e) {
      if (e.target && e.target.classList.contains("row_checkbox")) {
        toggleAddButton();
      }
    });
  }

  // ==========================================
  // ADD SELECTED ITEMS
  // ==========================================
  if (btnAddSelected) {
    btnAddSelected.addEventListener("click", function () {
      if (!window.USER_PERMISSIONS || !window.USER_PERMISSIONS.canAdd) {
        Swal.fire({
          icon: "error",
          title: "Access Denied",
          text: "You don't have permission to add items in this module.",
          confirmButtonColor: "#dc3545",
        });
        return;
      }

      const checkedBoxes = document.querySelectorAll(
        ".styled_lookup_table .row_checkbox:checked:not([disabled])",
      );

      if (checkedBoxes.length === 0) {
        Swal.fire({
          icon: "warning",
          title: "No Selection",
          text: "Please select at least one item to add.",
          confirmButtonColor: "#3085d6",
        });
        return;
      }

      const currentMainBody = document.getElementById("main_table_body");
      if (!currentMainBody) return;

      const existingCodes = Array.from(currentMainBody.querySelectorAll("tr"))
        .map((tr) => tr.cells[1]?.innerText.trim())
        .filter((code) => code && code !== "");

      let duplicateItems = [];

      const emptyRow = currentMainBody.querySelector(".empty-row");
      if (emptyRow) {
        emptyRow.remove();
      }

      checkedBoxes.forEach((checkbox) => {
        const row = checkbox.closest("tr");

        const itemLocator =
          row.querySelector(".item_locator")?.innerText.trim() ||
          row.cells[1]?.innerText.trim() ||
          "";
        const itemCode =
          row.querySelector(".item_code")?.innerText.trim() ||
          row.cells[2]?.innerText.trim() ||
          "";
        const barCode =
          row.querySelector(".item_barcode")?.innerText.trim() ||
          row.cells[3]?.innerText.trim() ||
          "";
        const itemNo =
          row.querySelector(".item_no")?.innerText.trim() ||
          row.cells[4]?.innerText.trim() ||
          "";
        const uom =
          row.querySelector(".item_uom")?.innerText.trim() ||
          row.cells[5]?.innerText.trim() ||
          "";
        const desc =
          row.querySelector(".item_desc")?.innerText.trim() ||
          row.cells[6]?.innerText.trim() ||
          "";
        const firstLoc =
          row.querySelector(".item_first_loc")?.innerText.trim() ||
          row.cells[7]?.innerText.trim() ||
          "";
        const secLoc =
          row.querySelector(".item_sec_loc")?.innerText.trim() ||
          row.cells[8]?.innerText.trim() ||
          "";
        const itemStock =
          row.querySelector(".item_stock")?.innerText.trim() ||
          row.cells[9]?.innerText.trim() ||
          "";

        if (existingCodes.includes(itemCode)) {
          duplicateItems.push(itemCode);
          checkbox.checked = false;
          return;
        }

        const newRow = document.createElement("tr");
        newRow.setAttribute("data-item-code", itemCode);
        newRow.innerHTML = `
                    <td>${itemLocator}</td>
                    <td>${itemCode}</td>
                    <td>${barCode}</td>
                    <td>${itemNo}</td>
                    <td>${uom}</td>
                    <td>${desc}</td>
                    <td class="text-center">
                        <label class="locator_radio_wrapper">
                            <input type="radio" name="locator_${itemCode}" value="${firstLoc}" checked onchange="saveItemsToStorage()">
                            <span>${firstLoc}</span>
                        </label>
                    </td>
                    <td class="text-center">
                        <label class="locator_radio_wrapper">
                        <input type="radio" name="locator_${itemCode}" value="${secLoc}" onchange="saveItemsToStorage()">
                        <span style="display: none;">${secLoc}</span>
                    </label>
                     </td>
                    <td class="hidden">${itemStock}</td>
                    <td class="text-center">
                        <button type="button" class="btn-delete" onclick="deleteRow(this)" title="Delete Row">&times;</button>
                    </td>
                `;

        currentMainBody.appendChild(newRow);
        checkbox.checked = false;
      });

      saveItemsToStorage();
      closeModal();

      if (duplicateItems.length > 0) {
        const duplicateMsg =
          duplicateItems.length === 1
            ? `Item Code <b>[${duplicateItems[0]}]</b> is already added in the table!`
            : `The following items are already added:<br><b>${duplicateItems.join(", ")}</b>`;

        Swal.fire({
          icon: "warning",
          title: "Duplicate Item Detected",
          html: duplicateMsg,
          confirmButtonColor: "#f39c12",
        });
      }

      if (selectAllCheckbox) selectAllCheckbox.checked = false;
      toggleAddButton();
    });
  }

  toggleAddButton();
  window.saveItemsToStorage = saveItemsToStorage;

  // ==========================================
  // POST ITEMS TO DATABASE ACTION
  // ==========================================
  const btnPost = document.getElementById("btn_post");

  if (btnPost) {
    btnPost.addEventListener("click", function (e) {
      e.preventDefault();

      if (!window.USER_PERMISSIONS || !window.USER_PERMISSIONS.canPost) {
        Swal.fire({
          icon: "error",
          title: "Access Denied",
          text: "You don't have permission to post items.",
          confirmButtonColor: "#dc3545",
        });
        return;
      }

      const locatorCode =
        locatorCodeInput?.value.trim() ||
        headerLocatorSpan?.innerText.trim() ||
        "";
      const locatorDetails = {
        locator_code: locatorCode,
        description: locatorDescInput ? locatorDescInput.value.trim() : "",
        added_at: locatorAddedInput ? locatorAddedInput.value.trim() : "",
        added_by: addedByInput ? addedByInput.value.trim() : "",
        remarks: locatorRemarksInput ? locatorRemarksInput.value.trim() : "",
      };

      saveItemsToStorage(); // I-ensure nga updated ang localStorage
      const storedItems = localStorage.getItem("saved_main_table_items");
      const items = storedItems ? JSON.parse(storedItems) : [];

      if (items.length === 0) {
        Swal.fire({
          icon: "warning",
          title: "No Items Found",
          text: "There are no items in the table to post!",
          confirmButtonColor: "#3085d6",
        });
        return;
      }

      Swal.fire({
        title: "Are you sure?",
        text: `Do you want to post ${items.length} item(s) to the database?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, post now!",
        cancelButtonText: "Cancel",
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire({
            title: "Posting data...",
            text: "Please wait a moment.",
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            },
          });

          const formData = new FormData();
          formData.append("post_items", "1");
          formData.append("locator_item", locatorCode);
          formData.append("locator_details", JSON.stringify(locatorDetails));
          formData.append("items", JSON.stringify(items));

          fetch("../item_action.php", {
            method: "POST",
            body: formData,
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.status === "success") {
                localStorage.removeItem("saved_main_table_items");

                const currentMainBody =
                  document.getElementById("main_table_body");
                if (currentMainBody) {
                  currentMainBody.innerHTML = `
                                    <tr class="empty-row">
                                        <td colspan="10" style="text-align: center; color: #888;">No items added yet. Click "Add Item" to select.</td>
                                    </tr>
                                `;
                }

                if (locatorDescInput) locatorDescInput.value = "";
                if (locatorRemarksInput) locatorRemarksInput.value = "";
                if (locatorAddedInput)
                  locatorAddedInput.value = getCurrentDateTimeLocal();

                Swal.fire({
                  icon: "success",
                  title: "Success!",
                  text: data.message,
                  confirmButtonColor: "#28a745",
                });
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Failed",
                  text:
                    data.message || "An error occurred while posting items.",
                  confirmButtonColor: "#d33",
                });
              }
            })
            .catch((error) => {
              console.error("Error:", error);
              Swal.fire({
                icon: "error",
                title: "Server Error",
                text: "Unable to connect to the server. Please try again.",
                confirmButtonColor: "#d33",
              });
            });
        }
      });
    });
  }
});

// ==========================================
// DELETE ROW FUNCTION
// ==========================================
function deleteRow(btn) {
  const row = btn.closest("tr");
  if (!row) return;

  const parentTbody = row.parentNode;
  row.remove();

  if (parentTbody && parentTbody.querySelectorAll("tr").length === 0) {
    parentTbody.innerHTML = `
            <tr class="empty-row">
                <td colspan="10" style="text-align: center; color: #888;">No items added yet. Click "Add Item" to select.</td>
            </tr>
        `;
  }

  if (typeof window.saveItemsToStorage === "function") {
    window.saveItemsToStorage();
  }
}