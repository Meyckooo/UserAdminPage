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
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">
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
                    <li><a class="global_btn" href="#">Add Item</a></li>
                    <li><a class="global_btn" href="#">Post</a></li>
                    <li><a class="global_btn" href="#">View Items</a></li>
                    <li><a class="global_btn" href="#">Options</a></li>
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
    </div>

    <script src="<?php echo $base_path; ?>assets/js/script.js"></script>
    <script src="<?php echo $base_path; ?>assets/js/sidebar_button.js"></script>

</body>

</html>