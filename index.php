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
        <div class="main_con">
            
        </div>
    </div>

    <script src="<?php echo $base_path; ?>/assets/js/modal_item.js"></script>
    <script src="<?php echo $base_path; ?>assets/js/sidebar_button.js"></script>

</body>

</html>