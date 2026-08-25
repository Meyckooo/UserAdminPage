<?php
session_start();
$base_path = './';
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
                    <li><a class="global_btn" href="#" data-modal="modal-post">Post</a></li>
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

                        <button type="button" class="btn-submit">Save Locator</button>
                    </form>
                </div>

                <div class="item_table_details">
                    <table class="styled_item_table">
                        <thead>
                            <tr>
                                <th>ITEM CODE</th>
                                <th>ITEM NO</th>
                                <th>UNIT</th>
                                <th>ITEM DESCRIPTION</th>
                                <th>FIRST LOCATOR</th>
                                <th>SECOND LOCATOR</th>
                                <th class="text-center">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>ITM-001</td>
                                <td>1001</td>
                                <td>PCS</td>
                                <td>High-Performance Industrial Valve Extra Long Description</td>
                                <td>Aisle 3, Rack A</td>
                                <td>Bin 12</td>
                                <td class="text-center">
                                    <button class="btn-delete" onclick="deleteRow(this)" title="Delete Row" aria-label="Delete row">&times;</button>
                                </td>
                            </tr>
                            <tr>
                                <td>ITM-002</td>
                                <td>1002</td>
                                <td>BOX</td>
                                <td>Stainless Steel Mounting Brackets Heavy Duty</td>
                                <td>Aisle 1, Rack C</td>
                                <td>Bin 04</td>
                                <td class="text-center">
                                    <button class="btn-delete" onclick="deleteRow(this)" title="Delete Row" aria-label="Delete row">&times;</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Modal 1: Add Item -->
        <div id="modal-add-item" class="modal_overlay">
            <div class="lookup_modal_container">
                <!-- Top Action Header -->
                <div class="lookup_header">
                    <div class="lookup_title_group">
                        <span class="lookup_title">LOOKUP ITEM</span>
                        <button class="btn_add_selected">ADD SELECTED</button>
                    </div>
                    <button class="modal_close">&times;</button>
                </div>

                <!-- Search Control Bar -->
                <div class="lookup_controls">
                    <div class="search_input_wrapper">
                        <input type="text" class="lookup_search_input" placeholder="Search Item Here">
                    </div>
                    <div class="search_filter_wrapper">
                        <select class="lookup_select">
                            <option value="description">ITEM DESCRIPTION</option>
                            <option value="code">ITEM CODE</option>
                            <option value="barcode">BARCODE</option>
                        </select>
                        <button class="btn_lookup_search">SEARCH</button>
                        <button class="btn_lookup_scan">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2" />
                                <line x1="7" y1="12" x2="17" y2="12" />
                            </svg>
                            SCAN
                        </button>
                    </div>
                </div>

                <!-- Table Details Section -->
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
                                <th>ASA BUTANG</th>
                                <th>SECOND LOC</th>
                                <th>STOCK ONHAND</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="checkbox" class="row_checkbox"></td>
                                <td><strong>1301-02</strong></td>
                                <td>1301-02</td>
                                <td>GI PIPE EAGLE S-40 (LG) 1-1/2#40MM x6M 150/BNDL (</td>
                                <td>8091</td>
                                <td>LENGTH</td>
                                <td>DEFAULT</td>
                                <td></td>
                                <td><strong>0.00</strong></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="row_checkbox"></td>
                                <td><strong>1301-04</strong></td>
                                <td>1301-04</td>
                                <td>GI PIPE EAGLE S-40 (LG) 1-1/4#32MM x6M 180/BNDL (</td>
                                <td>8093</td>
                                <td>LENGTH</td>
                                <td>DEFAULT</td>
                                <td></td>
                                <td><strong>28.00</strong></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="row_checkbox"></td>
                                <td><strong>1301-06</strong></td>
                                <td>1301-06</td>
                                <td>GI PIPE EAGLE S-40 (LG) 1#25MM x6M 250/BNDL (R)</td>
                                <td>8095</td>
                                <td>LENGTH</td>
                                <td>DEFAULT</td>
                                <td></td>
                                <td><strong>55.00</strong></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="row_checkbox"></td>
                                <td><strong>1301-08</strong></td>
                                <td>1301-08</td>
                                <td>GI PIPE EAGLE S-40 (LG) 1/2#15MM x6M 500/BNDL (R)</td>
                                <td>8097</td>
                                <td>LENGTH</td>
                                <td>DEFAULT</td>
                                <td></td>
                                <td><strong>71.00</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal 2: Post -->
        <div id="modal-post" class="modal_overlay">
            <div class="modal_content">
                <div class="modal_header">
                    <h3>Post Action</h3>
                    <button class="modal_close">&times;</button>
                </div>
                <div class="modal_body">
                    <p>Are you sure you want to post these items?</p>
                </div>
            </div>
        </div>

        <!-- Modal 3: View Items -->
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

        <!-- Modal 4: Options -->
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

</body>

</html>