document.addEventListener('DOMContentLoaded', () => {
    
    // ==========================================
    // LOCALSTORAGE PERSISTENCE HELPERS
    // ==========================================
    
    // Function para i-save ang items gikan sa table padulong sa LocalStorage
    function saveItemsToStorage() {
        const currentMainBody = document.getElementById("main_table_body");
        if (!currentMainBody) return;

        const rows = currentMainBody.querySelectorAll("tr:not(.empty-row)");
        const items = [];

        rows.forEach(row => {
            const itemData = {
                code: row.cells[0]?.innerText.trim() || '',
                itemNo: row.cells[1]?.innerText.trim() || '',
                uom: row.cells[2]?.innerText.trim() || '',
                desc: row.cells[3]?.innerText.trim() || '',
                firstLoc: row.cells[4]?.innerText.trim() || '',
                secLoc: row.cells[5]?.innerText.trim() || ''
            };
            items.push(itemData);
        });

        localStorage.setItem("saved_main_table_items", JSON.stringify(items));
    }

    // Function para i-load ang gipang-save nga items gikan sa LocalStorage inig Refresh
    function loadItemsFromStorage() {
        const currentMainBody = document.getElementById("main_table_body");
        if (!currentMainBody) return;

        const storedItems = localStorage.getItem("saved_main_table_items");
        if (!storedItems) return;

        const items = JSON.parse(storedItems);
        if (items.length === 0) return;

        // Tangtangon ang empty row placeholder
        const emptyRow = currentMainBody.querySelector(".empty-row");
        if (emptyRow) emptyRow.remove();

        items.forEach(item => {
            const newRow = document.createElement("tr");
            newRow.setAttribute("data-item-code", item.code);
            newRow.innerHTML = `
                <td>${item.code}</td>
                <td>${item.itemNo}</td>
                <td>${item.uom}</td>
                <td>${item.desc}</td>
                <td>${item.firstLoc}</td>
                <td>${item.secLoc}</td>
                <td class="text-center">
                    <button type="button" class="btn-delete" onclick="deleteRow(this)" title="Delete Row">&times;</button>
                </td>
            `;
            currentMainBody.appendChild(newRow);
        });
    }

    // Load saved items agad inig load sa page
    loadItemsFromStorage();


    // ==========================================
    // 1. MODAL OPEN / CLOSE CONTROLLERS
    // ==========================================
    const triggerBtns = document.querySelectorAll('[data-modal]');
    const closeBtns = document.querySelectorAll('.modal_close');
    const modals = document.querySelectorAll('.modal_overlay');

    function openModal(modalId) {
        const targetModal = document.getElementById(modalId);
        if (targetModal) {
            targetModal.classList.add('active');
            targetModal.style.display = 'flex';
        }
    }

    function closeModal() {
        modals.forEach(modal => {
            modal.classList.remove('active');
            modal.style.display = 'none';
        });
    }

    triggerBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const modalId = btn.getAttribute('data-modal');
            openModal(modalId);
        });
    });

    closeBtns.forEach(btn => {
        btn.addEventListener('click', closeModal);
    });

    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal_overlay')) {
            closeModal();
        }
    });

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeModal();
        }
    });


    // ==========================================
    // 2. STICKY LOCATOR (SAFE EXECUTION)
    // ==========================================
    const tableContainer = document.querySelector('.item_table_details');
    const formContainer = document.querySelector('.item_form_container');

    if (tableContainer && formContainer) {
        const observer = new ResizeObserver(() => {
            const tableHeight = tableContainer.offsetHeight;

            if (tableHeight >= 500) {
                formContainer.style.position = 'sticky';
                formContainer.style.top = '20px';
                formContainer.style.maxHeight = 'calc(100vh - 40px)';
                formContainer.style.overflowY = 'auto';
            } else {
                formContainer.style.position = 'static';
                formContainer.style.maxHeight = 'none';
                formContainer.style.overflowY = 'visible';
            }
        });

        observer.observe(tableContainer);
    }


    // ==========================================
    // 3. SEARCH & FILTER FUNCTION (LOOKUP MODAL)
    // ==========================================
    const selectAllCheckbox = document.getElementById("selectAll");
    const btnAddSelected = document.getElementById("btnAddSelected");

    const searchInput = document.getElementById("lookupSearchInput") || document.querySelector(".lookup_search_input");
    const filterSelect = document.getElementById("lookupSelectFilter") || document.querySelector(".lookup_select");
    const btnSearch = document.getElementById("btnLookupSearch") || document.querySelector(".btn_lookup_search");
    const btnScan = document.getElementById("btnLookupScan") || document.querySelector(".btn_lookup_scan");

    function filterLookupTable() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : "";
        let filterType = filterSelect ? filterSelect.value.toLowerCase().trim() : "description";
        
        if (!filterSelect && filterSelect?.options && filterSelect?.selectedIndex >= 0) {
            filterType = filterSelect.options[filterSelect.selectedIndex].text.toLowerCase().trim();
        }

        const rows = document.querySelectorAll(".styled_lookup_table tbody tr");

        rows.forEach(row => {
            if (row.cells.length <= 1) return;

            let targetText = "";

            if (filterType.includes("code")) {
                targetText = row.querySelector(".item_code")?.innerText || row.cells[1]?.innerText || "";
            } else if (filterType.includes("barcode")) {
                targetText = row.querySelector(".item_barcode")?.innerText || row.cells[2]?.innerText || "";
            } else {
                targetText = row.querySelector(".item_desc")?.innerText || row.cells[3]?.innerText || "";
            }

            if (targetText.toLowerCase().includes(query)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
                const cb = row.querySelector(".row_checkbox");
                if (cb && !cb.disabled) cb.checked = false;
            }
        });
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

    // ==========================================
    // 4. SELECT / DESELECT ALL CHECKBOXES
    // ==========================================
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", function () {
            const visibleCheckboxes = document.querySelectorAll(".styled_lookup_table tbody tr:not([style*='display: none']) .row_checkbox:not([disabled])");
            visibleCheckboxes.forEach(cb => {
                cb.checked = this.checked;
            });
        });
    }


    // ==========================================
    // 5. ADD SELECTED ITEMS (PERSISTENT SAVING)
    // ==========================================
    if (btnAddSelected) {
        btnAddSelected.addEventListener("click", function () {
            const checkedBoxes = document.querySelectorAll(".styled_lookup_table .row_checkbox:checked:not([disabled])");

            if (checkedBoxes.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Selection',
                    text: 'Please select at least one item to add.',
                    confirmButtonColor: '#3085d6',
                });
                return;
            }

            const currentMainBody = document.getElementById("main_table_body");
            if (!currentMainBody) return;

            const existingCodes = Array.from(currentMainBody.querySelectorAll("tr"))
                .map(tr => tr.cells[0]?.innerText.trim())
                .filter(code => code && code !== "");

            let addedCount = 0;
            let duplicateItems = [];

            const emptyRow = currentMainBody.querySelector(".empty-row");
            if (emptyRow) {
                emptyRow.remove();
            }

            checkedBoxes.forEach(checkbox => {
                const row = checkbox.closest("tr");

                const code = row.querySelector(".item_code")?.innerText.trim() || row.cells[1]?.innerText.trim() || '';
                const itemNo = row.querySelector(".item_no")?.innerText.trim() || row.cells[4]?.innerText.trim() || '';
                const uom = row.querySelector(".item_uom")?.innerText.trim() || row.cells[5]?.innerText.trim() || '';
                const desc = row.querySelector(".item_desc")?.innerText.trim() || row.cells[3]?.innerText.trim() || '';
                const firstLoc = row.querySelector(".item_first_loc")?.innerText.trim() || row.cells[6]?.innerText.trim() || '';
                const secLoc = row.querySelector(".item_sec_loc")?.innerText.trim() || row.cells[7]?.innerText.trim() || '';

                if (existingCodes.includes(code)) {
                    duplicateItems.push(code);
                    checkbox.checked = false;
                    return;
                }

                const newRow = document.createElement("tr");
                newRow.setAttribute("data-item-code", code);
                newRow.innerHTML = `
                    <td>${code}</td>
                    <td>${itemNo}</td>
                    <td>${uom}</td>
                    <td>${desc}</td>
                    <td>${firstLoc}</td>
                    <td>${secLoc}</td>
                    <td class="text-center">
                        <button type="button" class="btn-delete" onclick="deleteRow(this)" title="Delete Row">&times;</button>
                    </td>
                `;

                currentMainBody.appendChild(newRow);
                addedCount++;
                checkbox.checked = false;
            });

            // SAVE ANG MGA BAG-ONG GI-ADD SA LOCALSTORAGE
            saveItemsToStorage();

            closeModal();

            if (duplicateItems.length > 0) {
                const duplicateMsg = duplicateItems.length === 1
                    ? `Item Code <b>[${duplicateItems[0]}]</b> is already added in the table!`
                    : `The following items are already added:<br><b>${duplicateItems.join(', ')}</b>`;

                Swal.fire({
                    icon: 'warning',
                    title: 'Duplicate Item Detected',
                    html: duplicateMsg,
                    confirmButtonColor: '#f39c12',
                });
            }

            if (selectAllCheckbox) selectAllCheckbox.checked = false;
        });
    }

    // Export save function to window level for delete function
    window.saveItemsToStorage = saveItemsToStorage;
});

// ==========================================
// 6. DELETE ROW FUNCTION (WITH STORAGE UPDATE)
// ==========================================
function deleteRow(btn) {
    const row = btn.closest("tr");
    if (!row) return;

    const parentTbody = row.parentNode;
    row.remove();

    if (parentTbody && parentTbody.querySelectorAll("tr").length === 0) {
        parentTbody.innerHTML = `
            <tr class="empty-row">
                <td colspan="7" style="text-align: center; color: #888;">No items added yet. Click "Add Item" to select.</td>
            </tr>
        `;
    }

    // Update ang storage aron matangtang sad ang gi-delete
    if (typeof window.saveItemsToStorage === 'function') {
        window.saveItemsToStorage();
    }
}