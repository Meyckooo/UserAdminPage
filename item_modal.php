<?php
session_start();
require 'config/config.php'; // Apil ang db connection
$base_path = './';

// Fetch items gikan sa tbl_item
$items_query = mysqli_query($conn, "SELECT * FROM tbl_item");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modal Item</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>/assets/css/media.css">
</head>

<body>
    <header>
        <div class="item_header">
            <div class="item_details_left">
                <div class="item_code">
                    <span>5C30A6</span>
                </div>
            </div>
            <div class="item_right_btns">
                <ul>
                    <li><a class="global_btn" href="index.php">Back</a></li>
                    <li><a class="global_btn" href="#" data-modal="modal-add-item">Add Item</a></li>
                    <li><a class="global_btn" href="#" id="btn_post">Post</a></li>
                    <li><a class="global_btn" href="#" data-modal="modal-view-items">View Items</a></li>
                    <li><a class="global_btn" href="#" data-modal="modal-options">Options</a></li>
                </ul>
            </div>
        </div>
    </header>

    <?php include $base_path . 'includes/sidebar.php'; ?>

    <div id="main">
        <div class="main_con main_item_con">
            <div class="item_parent">
                <div class="item_form_container">
                    <h3>Locator Details</h3>
                    <form id="locatorForm">
                        <div class="locator-form-group">
                            <label for="locatorDescription">Locator Description</label>
                            <input type="text" id="locatorDescription" placeholder="Enter description..." required>
                        </div>

                        <div class="locator-form-group">
                            <label for="locatorAdded">Locator Added</label>
                            <input type="date" id="locatorAdded" required>
                        </div>

                        <div class="locator-form-group">
                            <label for="addedBy">Added By</label>
                            <input type="text" id="addedBy" placeholder="Enter name..." required>
                        </div>
                    </form>
                </div>

                <!-- MAIN MAIN OUTSIDE TABLE -->
                <div class="item_table_details">
                    <table class="styled_item_table">
                        <thead>
                            <tr>
                                <th>ITEM CODE</th>
                                <th>BAR CODE</th>
                                <th>ITEM NO</th>
                                <th>UNIT</th>
                                <th>ITEM DESCRIPTION</th>
                                <th>FIRST LOCATOR</th>
                                <th>SECOND LOCATOR</th>
                                <th class="hidden">ITEM STOCK</th>
                                <th class="text-center">ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="main_table_body">
                            <tr class="empty-row">
                                <td colspan="7" style="text-align: center; color: #888;">No items added yet. Click "Add Item" to select.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal 1: Add Item (Lookup) -->
        <div id="modal-add-item" class="modal_overlay">
            <div class="lookup_modal_container">
                <div class="lookup_header">
                    <div class="lookup_title_group">
                        <span class="lookup_title">LOOKUP ITEM</span>
                        <button type="button" class="btn_add_selected" id="btnAddSelected">ADD SELECTED</button>
                    </div>
                    <button class="modal_close">&times;</button>
                </div>

                <div class="lookup_controls">
                    <div class="search_input_wrapper">
                        <!-- GI-GAMITAG ID: lookupSearchInput -->
                        <input type="text" id="lookupSearchInput" class="lookup_search_input" placeholder="Search Item Here">
                    </div>
                    <div class="search_filter_wrapper">
                        <!-- GI-GAMITAG ID: lookupSelectFilter -->
                        <select id="lookupSelectFilter" class="lookup_select">
                            <option value="description">ITEM DESCRIPTION</option>
                            <option value="code">ITEM CODE</option>
                            <option value="barcode">BARCODE</option>
                        </select>
                        <!-- GI-GAMITAG ID: btnLookupSearch -->
                        <button id="btnLookupSearch" class="btn_lookup_search">SEARCH</button>
                        <!-- GI-GAMITAG ID: btnLookupScan -->
                        <button id="btnLookupScan" class="btn_lookup_scan">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2" />
                                <line x1="7" y1="12" x2="17" y2="12" />
                            </svg>
                            SCAN
                        </button>
                    </div>
                </div>

                <div class="lookup_table_wrapper">
                    <table class="styled_lookup_table">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" id="selectAll"> <label for="selectAll">All</label></th>
                                <th>ITEM CODE</th>
                                <th>BARCODE</th>
                                <th>ITEM DESCRIPTION</th>
                                <th>ITEM NO</th>
                                <th>UOM</th>
                                <th>FIRST LOC</th>
                                <th>SECOND LOC</th>
                                <th>STOCK ONHAND</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($items_query) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($items_query)): ?>
                                    <tr>
                                        <td><input type="checkbox" class="row_checkbox"></td>
                                        <td class="item_code"><strong><?= htmlspecialchars($row['item_code']); ?></strong></td>
                                        <td class="item_barcode"><?= htmlspecialchars($row['item_barcode']); ?></td>
                                        <td class="item_desc"><?= htmlspecialchars($row['item_desc']); ?></td>
                                        <td class="item_no"><?= htmlspecialchars($row['item_no']); ?></td>
                                        <td class="item_uom"><?= htmlspecialchars($row['item_uom']); ?></td>
                                        <td class="item_first_loc"><?= htmlspecialchars($row['item_first_loc']); ?></td>
                                        <td class="item_sec_loc"><?= htmlspecialchars($row['item_sec_loc']); ?></td>
                                        <td class="item_stock"><strong><?= number_format((float)$row['item_stock'], 2); ?></strong></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" style="text-align: center;">No items found in database.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div id="modal-view-items" class="modal_overlay">
            <div class="modal_content">
                <div class="modal_header">
                    <h3>View Items</h3>
                    <button class="modal_close">&times;</button>
                </div>
                <div class="modal_body">
                    <p>Overview list or details for items.</p>
                </div>
            </div>
        </div>

        <div id="modal-options" class="modal_overlay">
            <div class="modal_content">
                <div class="modal_header">
                    <h3>Options</h3>
                    <button class="modal_close">&times;</button>
                </div>
                <div class="modal_body">
                    <p>Configuration settings and table display options.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo $base_path; ?>/assets/js/modal_item.js"></script>
    <script src="<?php echo $base_path; ?>assets/js/sidebar_button.js"></script>
    <script src="assets/js/sweetalert.js"></script>

</body>

</html>